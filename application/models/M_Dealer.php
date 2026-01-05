<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Dealer extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Tüm bayilik tiplerini getirir
     */
    public function getAllDealerTypes() {
        $this->db->order_by('min_purchase_amount', 'ASC');
        return $this->db->get('dealer_types')->result();
    }

    /**
     * Aktif bayilik tiplerini getirir
     */
    public function getActiveDealerTypes() {
        $this->db->where('status', 1);
        $this->db->order_by('min_purchase_amount', 'ASC');
        return $this->db->get('dealer_types')->result();
    }

    /**
     * ID'ye göre bayilik tipini getirir
     */
    public function getDealerTypeById($id) {
        $query = $this->db->get_where('dealer_types', ['id' => $id]);
        return $query->num_rows() > 0 ? $query->row() : null;
    }

    /**
     * Yeni bayilik tipi ekler
     */
    public function addDealerType($data) {
        $this->db->insert('dealer_types', $data);
        return $this->db->insert_id();
    }

    /**
     * Bayilik tipini günceller
     */
    public function updateDealerType($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('dealer_types', $data);
    }

    /**
     * Bayilik tipini siler (veya devre dışı bırakır)
     */
    public function deleteDealerType($id) {
        // Bayilik tipini tamamen silmek yerine durumunu pasif yapıyoruz
        $this->db->where('id', $id);
        return $this->db->update('dealer_types', ['status' => 0]);
    }

    /**
     * Kullanıcıya bayilik atar
     */
    public function assignDealerToUser($user_id, $dealer_type_id, $description, $performed_by = 0) {
        // Kullanıcının mevcut (aktif veya pasif) bayilik kaydını kontrol et
        $existing_dealer = $this->db->where('user_id', $user_id)->get('user_dealers')->row();
        
        // Yeni bayilik bilgisini ekle
        $dealer_data = [
            'user_id' => $user_id,
            'dealer_type_id' => $dealer_type_id,
            'start_date' => date('Y-m-d H:i:s'),
            'active_status' => 1,
            'auto_upgrade' => 1,
            'total_purchase' => 0,
            'next_check_date' => date('Y-m-d H:i:s', strtotime('+30 days'))
        ];
        
        // Eğer mevcut kayd var ise güncelle, yoksa yeni ekle
        if ($existing_dealer) {
            $this->db->where('user_id', $user_id);
            $this->db->update('user_dealers', $dealer_data);
        } else {
            $this->db->insert('user_dealers', $dealer_data);
        }
        
        // İşlem geçmişine kaydet
        $history_data = [
            'user_id' => $user_id,
            'old_dealer_type_id' => $existing_dealer ? $existing_dealer->dealer_type_id : null,
            'new_dealer_type_id' => $dealer_type_id,
            'action' => $existing_dealer ? 'upgrade' : 'assign',
            'description' => $description,
            'performed_by_user_id' => $performed_by
        ];
        $this->db->insert('dealer_history', $history_data);
        
        return true;
    }

    /**
     * Kullanıcının bayilik bilgisini getirir
     */
    public function getUserDealerInfo($user_id) {
        $this->db->select('
            ud.*, 
            dt.name as dealer_name,
            dt.discount_percentage,
            dt.min_purchase_amount
        ');
        $this->db->from('user_dealers ud');
        $this->db->join('dealer_types dt', 'dt.id = ud.dealer_type_id', 'left');
        $this->db->where('ud.user_id', $user_id);
        $this->db->where('ud.active_status', 1);
        $query = $this->db->get();
        
        return $query->num_rows() > 0 ? $query->row() : null;
    }
    
    /**
     * Kullanıcı bayiliğinin durumunu günceller
     */
    public function updateUserDealerStatus($user_id, $status) {
        $this->db->where('user_id', $user_id);
        return $this->db->update('user_dealers', ['active_status' => $status]);
    }

    /**
     * Bayiliği olan tüm kullanıcıları getirir
     */
    public function getAllDealerUsers() {
        $this->db->select('ud.*, u.name, u.surname, u.email, dt.name as dealer_name');
        $this->db->from('user_dealers ud');
        $this->db->join('user u', 'ud.user_id = u.id');
        $this->db->join('dealer_types dt', 'ud.dealer_type_id = dt.id');
        $this->db->where('ud.active_status', 1);
        $this->db->order_by('ud.total_purchase', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Belirli bir bayilik tipine sahip kullanıcıları getirir
     */
    public function getUsersByDealerType($dealer_type_id) {
        $this->db->select('ud.*, u.name, u.surname, u.email, dt.name as dealer_name');
        $this->db->from('user_dealers ud');
        $this->db->join('user u', 'ud.user_id = u.id');
        $this->db->join('dealer_types dt', 'ud.dealer_type_id = dt.id');
        $this->db->where('ud.dealer_type_id', $dealer_type_id);
        $this->db->where('ud.active_status', 1);
        $this->db->order_by('ud.total_purchase', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Kullanıcının toplam alım miktarını günceller
     */
    public function updateUserTotalPurchase($user_id, $amount) {
        $dealer = $this->getUserDealerInfo($user_id);
        if ($dealer) {
            $this->db->set('total_purchase', 'total_purchase + ' . $amount, FALSE);
            $this->db->where('user_id', $user_id);
            $this->db->update('user_dealers');
            return true;
        }
        return false;
    }

    /**
     * Kullanıcının bayilik seviyesini yükseltme şartlarını kontrol eder
     */
    public function checkUpgradeEligibility($user_id) {
        $user_dealer = $this->getUserDealerInfo($user_id);
        if (!$user_dealer || !$user_dealer->auto_upgrade) {
            return false;
        }

        // Mevcut bayilik bilgisini al
        $current_dealer_type = $this->getDealerTypeById($user_dealer->dealer_type_id);
        
        // Kullanıcının toplam alım miktarı
        $user_total_purchase = $user_dealer->total_purchase;
        
        // Aktif tüm bayilik tiplerini min_purchase_amount'a göre sıralayarak al
        $this->db->where('status', 1);
        $this->db->order_by('min_purchase_amount', 'ASC');
        $all_dealer_types = $this->db->get('dealer_types')->result();
        
        // Mevcut bayilikten daha yüksek olan bayilikleri bul
        $eligible_types = [];
        $current_found = false;
        
        foreach ($all_dealer_types as $type) {
            // Mevcut bayilik tipini bulduk, bundan sonrakilere bakacağız
            if ($type->id == $current_dealer_type->id) {
                $current_found = true;
                continue;
            }
            
            // Mevcut bayilikten daha yüksek bayilikler arasından
            // kullanıcının alım miktarını karşılayanları listeye ekle
            if ($current_found && $user_total_purchase >= $type->min_purchase_amount) {
                $eligible_types[] = $type;
            }
        }
        
        // Yükseltmeye uygun bayilik yoksa
        if (empty($eligible_types)) {
            return false;
        }
        
        // En düşük min_purchase_amount değerine sahip uygun bayiliği bul
        // (Zaten min_purchase_amount'a göre sıralandığı için ilk eleman)
        $next_dealer_type = $eligible_types[0];
        
        // Kullanıcıyı yükselt
        $this->assignDealerToUser(
            $user_id, 
            $next_dealer_type->id, 
            'Otomatik seviye yükseltme: ' . $current_dealer_type->name . ' -> ' . $next_dealer_type->name . ' (Toplam alım: ' . $user_total_purchase . ' TL)',
            1 // sistem tarafından yapıldığını belirt (1: admin kullanıcı ID'si)
        );
        
        return true;
    }

    /**
     * Belirli bir ürün ve bayilik tipine göre özel fiyatı getirir
     */
    public function getDealerProductPrice($dealer_type_id, $product_id) {
        return $this->db->where('dealer_type_id', $dealer_type_id)
                        ->where('product_id', $product_id)
                        ->get('dealer_product_prices')
                        ->row();
    }

    /**
     * Ürün için bayilik özel fiyatını ekler veya günceller
     */
    public function setDealerProductPrice($dealer_type_id, $product_id, $special_price = null, $discount_percentage = null) {
        // Eğer special_price verilmişse, bunu discount_percentage'a dönüştür
        if ($special_price !== null && $discount_percentage === null) {
            $product = $this->db->where('id', $product_id)->get('product')->row();
            if ($product) {
                $discount_percentage = 100 - (($special_price / $product->price) * 100);
                $special_price = null; // special_price'ı null yap, artık discount_percentage kullanılacak
            }
        }
        
        $data = [
            'dealer_type_id' => $dealer_type_id,
            'product_id' => $product_id,
            'special_price' => $special_price,
            'discount_percentage' => $discount_percentage
        ];
        
        // Mevcut kaydı kontrol et
        $existing = $this->getDealerProductPrice($dealer_type_id, $product_id);
        
        if ($existing) {
            // Güncelle
            $this->db->where('id', $existing->id);
            return $this->db->update('dealer_product_prices', $data);
        } else {
            // Yeni ekle
            return $this->db->insert('dealer_product_prices', $data);
        }
    }

    /**
     * Tüm özel fiyat (special_price) değerlerini indirim yüzdesine dönüştürür
     * Bu geçiş fonksiyonu, special_price alanının artık kullanılmayacağı durumlarda kullanılabilir
     */
    public function convertAllSpecialPricesToDiscounts() {
        // Özel fiyatı olan tüm kayıtları bul
        $this->db->select('dpp.*, p.price as product_price');
        $this->db->from('dealer_product_prices dpp');
        $this->db->join('product p', 'p.id = dpp.product_id');
        $this->db->where('dpp.special_price IS NOT NULL');
        $special_prices = $this->db->get()->result();
        
        $converted_count = 0;
        
        foreach ($special_prices as $price) {
            // İndirim yüzdesini hesapla
            $discount_percentage = 100 - (($price->special_price / $price->product_price) * 100);
            
            // Kaydı güncelle
            $this->db->where('id', $price->id);
            $this->db->update('dealer_product_prices', [
                'discount_percentage' => $discount_percentage,
                'special_price' => null
            ]);
            
            $converted_count++;
        }
        
        return $converted_count;
    }

    /**
     * Üç günden fazla kontrolü yapılmamış bayilikleri kontrol eder
     */
    public function checkDealerUpgrades() {
        $this->db->where('active_status', 1);
        $this->db->where('auto_upgrade', 1);
        $this->db->where('next_check_date <', date('Y-m-d H:i:s'));
        $dealers = $this->db->get('user_dealers')->result();
        
        $upgraded_count = 0;
        foreach ($dealers as $dealer) {
            if ($this->checkUpgradeEligibility($dealer->user_id)) {
                $upgraded_count++;
            }
            
            // Bir sonraki kontrol tarihini güncelle
            $this->db->where('id', $dealer->id);
            $this->db->update('user_dealers', ['next_check_date' => date('Y-m-d H:i:s', strtotime('+30 days'))]);
        }
        
        return $upgraded_count;
    }

    /**
     * Kullanıcıların bayilik geçmişini getirir
     */
    public function getUserDealerHistory($user_id) {
        $this->db->select('
            dh.*, 
            new_dt.name as new_dealer_name,
            old_dt.name as old_dealer_name,
            admin.name as performed_by_name, 
            admin.surname as performed_by_surname
        ');
        $this->db->from('dealer_history dh');
        $this->db->join('dealer_types new_dt', 'dh.new_dealer_type_id = new_dt.id', 'left');
        $this->db->join('dealer_types old_dt', 'dh.old_dealer_type_id = old_dt.id', 'left');
        $this->db->join('user admin', 'dh.performed_by_user_id = admin.id', 'left');
        $this->db->where('dh.user_id', $user_id);
        $this->db->order_by('dh.created_at', 'DESC');
        
        $results = $this->db->get()->result();
        
        // Eksik özellik hatalarını önlemek için, null değerler için varsayılan değerler atayalım
        foreach ($results as $item) {
            if (!isset($item->old_dealer_name)) {
                $item->old_dealer_name = null;
            }
            if (!isset($item->new_dealer_name)) {
                $item->new_dealer_name = 'Bilinmiyor';
            }
            if (!isset($item->performed_by_name)) {
                $item->performed_by_name = null;
            }
            if (!isset($item->performed_by_surname)) {
                $item->performed_by_surname = null;
            }
        }
        
        return $results;
    }

    /**
     * Tüm bayilik başvurularını getirir
     */
    public function getAllDealerApplications() {
        $this->db->select('da.*, u.name, u.surname, u.email, u.phone');
        $this->db->from('dealer_applications da');
        $this->db->join('user u', 'da.user_id = u.id');
        $this->db->order_by('da.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Belirli durumdaki bayilik başvurularını getirir
     */
    public function getDealerApplicationsByStatus($status) {
        $this->db->select('da.*, u.name, u.surname, u.email, u.phone');
        $this->db->from('dealer_applications da');
        $this->db->join('user u', 'da.user_id = u.id');
        $this->db->where('da.status', $status);
        $this->db->order_by('da.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Belirli bir kullanıcının bayilik başvurularını getirir
     */
    public function getUserDealerApplications($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('dealer_applications')->result();
    }

    /**
     * Bayilik başvurusunun durumunu günceller
     */
    public function updateDealerApplicationStatus($id, $status, $admin_notes = '') {
        // Başvuru bilgilerini al
        $application = $this->getDealerApplicationById($id);
        if (!$application) {
            return false;
        }
        
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('id', $id);
        $result = $this->db->update('dealer_applications', $data);
        
        // Kullanıcıya bildirim gönder
        if ($result && $application->user_id) {
            if ($status == 'approved') {
                $notification_title = 'Bayilik Başvurunuz Onaylandı';
                $notification_message = 'Bayilik başvurunuz onaylandı.';
            } else if ($status == 'rejected') {
                $notification_title = 'Bayilik Başvurunuz Reddedildi';
                $notification_message = 'Bayilik başvurunuz reddedildi.';
            } else {
                // Diğer durumlar için bildirim gönderme
                return $result;
            }
            
            // Admin notu varsa bildirime ekle
            if (!empty($admin_notes)) {
                $notification_message .= ' Not: ' . $admin_notes;
            }
            
            sendNotificationSite(
                $application->user_id, 
                $notification_title, 
                $notification_message, 
                base_url('client/my_dealer'), 
                'admin'
            );
        }
        
        return $result;
    }

    /**
     * Bayilik tipindeki kullanıcı sayısını hesaplar
     */
    public function countUsersByDealerType($dealer_type_id) {
        $this->db->where('dealer_type_id', $dealer_type_id);
        $this->db->where('active_status', 1);
        return $this->db->count_all_results('user_dealers');
    }

    /**
     * Tüm bayilik tiplerini kullanıcı sayılarıyla birlikte getirir
     */
    public function getAllDealerTypesWithUserCount() {
        $dealer_types = $this->getAllDealerTypes();
        
        foreach ($dealer_types as $type) {
            $type->user_count = $this->countUsersByDealerType($type->id);
        }
        
        return $dealer_types;
    }

    /**
     * Kullanıcıları bir bayilikten diğerine taşır
     */
    public function migrateUsersToNewDealerType($old_dealer_type_id, $new_dealer_type_id, $description = '') {
        // Eski bayilikteki aktif kullanıcıları bul
        $this->db->where('dealer_type_id', $old_dealer_type_id);
        $this->db->where('active_status', 1);
        $dealer_users = $this->db->get('user_dealers')->result();
        
        $migrated_count = 0;
        
        // Her kullanıcı için bayilik seviyesini güncelle
        foreach ($dealer_users as $user) {
            $this->db->where('user_id', $user->user_id);
            $this->db->update('user_dealers', ['dealer_type_id' => $new_dealer_type_id]);
            
            // İşlem geçmişini kaydet
            $history_data = [
                'user_id' => $user->user_id,
                'old_dealer_type_id' => $old_dealer_type_id,
                'new_dealer_type_id' => $new_dealer_type_id,
                'action' => 'migrate',
                'description' => $description,
                'performed_by_user_id' => $this->session->userdata('info')['id'] ?? 1, // Admin ID
            ];
            $this->db->insert('dealer_history', $history_data);
            
            $migrated_count++;
        }
        
        return $migrated_count;
    }
    
    /**
     * Bayilik kullanıcılarını normal kullanıcıya çevirir
     */
    public function normalUsersFromDealer($dealer_type_id, $description = '') {
        // Bayilikteki aktif kullanıcıları bul
        $this->db->where('dealer_type_id', $dealer_type_id);
        $this->db->where('active_status', 1);
        $dealer_users = $this->db->get('user_dealers')->result();
        
        $migrated_count = 0;
        
        // En düşük seviyeli (ID'li) bayilik türünü bul - foreign key kısıtlaması için
        $this->db->select_min('id');
        $min_dealer_id = $this->db->get('dealer_types')->row()->id;
        
        // Her kullanıcı için bayilik kaydını pasif yap
        foreach ($dealer_users as $user) {
            // Bayilik kaydını pasif yap (silmek yerine)
            $this->db->where('user_id', $user->user_id);
            $this->db->update('user_dealers', ['active_status' => 0]);
            
            // İşlem geçmişini kaydet
            $history_data = [
                'user_id' => $user->user_id,
                'old_dealer_type_id' => $dealer_type_id,
                'new_dealer_type_id' => $min_dealer_id, // En düşük ID'li bayilik (foreign key için)
                'action' => 'remove_dealer',
                'description' => $description . ' (Bayilik durumu pasif yapıldı)',
                'performed_by_user_id' => $this->session->userdata('info')['id'] ?? 1, // Admin ID
            ];
            $this->db->insert('dealer_history', $history_data);
            
            $migrated_count++;
        }
        
        return $migrated_count;
    }

    /**
     * ID'ye göre bayilik başvurusunu getirir
     */
    public function getDealerApplicationById($id) {
        $this->db->select('da.*, u.name, u.surname, u.email, u.phone');
        $this->db->from('dealer_applications da');
        $this->db->join('user u', 'da.user_id = u.id');
        $this->db->where('da.id', $id);
        $query = $this->db->get();
        
        return $query->num_rows() > 0 ? $query->row() : null;
    }

    /**
     * Bayilik ayarlarını getirir
     * 
     * @return object|null
     */
    public function getDealerSettings() {
        $query = $this->db->get('dealer_settings');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }
    
    /**
     * Bayilik ayarlarını kaydeder
     * 
     * @param array $settings Ayarlar
     * @return bool
     */
    public function saveDealerSettings($settings) {
        // Mevcut ayarları kontrol et
        $query = $this->db->get('dealer_settings');
        
        if ($query->num_rows() > 0) {
            // Ayarlar varsa güncelle
            $this->db->update('dealer_settings', $settings);
        } else {
            // Ayarlar yoksa yeni ekle
            $this->db->insert('dealer_settings', $settings);
        }
        
        return $this->db->affected_rows() > 0;
    }
    
    /**
     * Belirli bir ayarı getirir
     * 
     * @param string $key Ayar anahtarı
     * @param mixed $default Varsayılan değer
     * @return mixed
     */
    public function getSetting($key, $default = null) {
        $query = $this->db->where('key', $key)->get('settings');
        if ($query->num_rows() > 0) {
            return $query->row()->value;
        }
        return $default;
    }

    /**
     * Belirli bir önekle başlayan tüm ayarları getirir
     * 
     * @param string $prefix Önek (örn: 'dealer_')
     * @return object Ayarlar objesi
     */
    public function getSettingsByPrefix($prefix) {
        $query = $this->db->like('key', $prefix, 'after')->get('settings');
        
        $settings = new stdClass();
        
        // Varsayılan değerler tanımla
        if ($prefix == 'dealer_') {
            $settings->auto_approve = '0';
            $settings->default_dealer_type_id = '';
            $settings->enable_timed_dealer = '0';
            $settings->initial_dealer_type_id = '';
            $settings->dealer_period = '30';
            $settings->final_dealer_type_id = '';
        }
        
        // Veritabanında bulunan değerleri ekle
        foreach ($query->result() as $row) {
            // Önek olmadan key adını al
            $key = str_replace($prefix, '', $row->key);
            $settings->$key = $row->value;
        }
        
        return $settings;
    }
    
    /**
     * Ayarı kaydeder
     * 
     * @param string $key Ayar anahtarı
     * @param mixed $value Ayar değeri
     * @return bool
     */
    public function saveSetting($key, $value) {
        $query = $this->db->where('key', $key)->get('settings');
        
        if ($query->num_rows() > 0) {
            // Güncelle
            return $this->db->where('key', $key)
                          ->update('settings', ['value' => $value]);
        } else {
            // Yeni ekle
            return $this->db->insert('settings', [
                'key' => $key,
                'value' => $value
            ]);
        }
    }
    
    /**
     * Birden fazla ayarı kaydeder
     * 
     * @param array $settings Key-value çiftleri
     * @return bool
     */
    public function saveSettings($settings) {
        $success = true;
        
        foreach ($settings as $key => $value) {
            $result = $this->saveSetting($key, $value);
            if (!$result) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Bekleyen bayilik başvurularını kontrol eder ve ayarlara göre otomatik işlem yapar
     * 
     * @return int İşlem yapılan başvuru sayısı
     */
    public function checkPendingApplications() {
        // Ayarları getir
        $auto_approve = $this->getSetting('dealer_auto_approve', '0');
        
        // Otomatik onaylama aktif değilse işlem yapma
        if ($auto_approve != '1') {
            return 0;
        }
        
        // Bekleyen başvuruları getir
        $pending_applications = $this->getDealerApplicationsByStatus('pending');
        
        $processed_count = 0;
        
        foreach ($pending_applications as $application) {
            // Başvuruyu onayla
            $this->updateDealerApplicationStatus($application->id, 'approved', 'Otomatik onaylama');
            
            // Otomatik bayilik ataması yapılacak mı?
            $default_dealer_type_id = $this->getSetting('dealer_default_dealer_type_id');
            
            if (!empty($default_dealer_type_id)) {
                $dealer_type_id = $default_dealer_type_id;
                
                // Süreli bayilik ataması yapılacak mı?
                $enable_timed_dealer = $this->getSetting('dealer_enable_timed_dealer', '0');
                $initial_dealer_type_id = $this->getSetting('dealer_initial_dealer_type_id');
                
                if ($enable_timed_dealer == '1' && !empty($initial_dealer_type_id)) {
                    $dealer_type_id = $initial_dealer_type_id;
                }
                
                // Kullanıcının mevcut bayilik bilgisini kontrol et
                $user_dealer_info = $this->getUserDealerInfo($application->user_id);
                
                // Kullanıcı henüz bayi değilse, bayilik ata
                if (!$user_dealer_info) {
                    // assignDealerToUser metodu ile bayilik ata
                    $this->assignDealerToUser(
                        $application->user_id, 
                        $dealer_type_id, 
                        'Otomatik bayilik ataması',
                        1 // sistem tarafından yapıldığını belirt (1: admin kullanıcı ID'si)
                    );
                    
                    // Süreli bayilik ise, süre sonunda değiştirilecek bayiliği kaydet
                    if ($enable_timed_dealer == '1') {
                        $final_dealer_type_id = $this->getSetting('dealer_final_dealer_type_id');
                        $dealer_period = $this->getSetting('dealer_period', '30');
                        
                        if (!empty($final_dealer_type_id)) {
                            $change_date = date('Y-m-d H:i:s', strtotime('+' . $dealer_period . ' days'));
                            
                            $timed_data = [
                                'user_id' => $application->user_id,
                                'current_dealer_type_id' => $dealer_type_id,
                                'next_dealer_type_id' => $final_dealer_type_id,
                                'change_date' => $change_date,
                                'is_processed' => 0,
                                'created_at' => date('Y-m-d H:i:s')
                            ];
                            
                            $this->db->insert('dealer_timed_changes', $timed_data);
                        }
                    }
                    
                    // Kullanıcıya bildirim gönder
                    $dealer_type = $this->getDealerTypeById($dealer_type_id);
                    $notification_message = 'Bayilik başvurunuz onaylandı! Artık ' . $dealer_type->name . ' bayimizsiniz. ';
                    
                    if ($enable_timed_dealer == '1' && $dealer_type_id == $initial_dealer_type_id) {
                        $dealer_period = $this->getSetting('dealer_period', '30');
                        $notification_message .= $dealer_period . ' gün sonra bayilik tipiniz güncellenecektir. ';
                    }
                    
                    $notification_message .= 'Bayilik avantajlarından yararlanmak için hesabınıza giriş yapabilirsiniz.';
                    sendNotificationSite($application->user_id, 'Bayilik Başvurunuz Onaylandı', $notification_message, base_url('client/my_dealer'), 'admin');
                }
            }
            
            $processed_count++;
        }
        
        return $processed_count;
    }
    
    /**
     * Süreli bayilik değişimi bekleyen kullanıcıları kontrol eder ve gerekirse bayilik tiplerini değiştirir
     * 
     * @return int Bayiliği değiştirilen kullanıcı sayısı
     */
    public function checkTimedDealerChanges() {
        // Değişim tarihi gelmiş kayıtları bul
        $this->db->where('change_date <=', date('Y-m-d H:i:s'));
        $this->db->where('is_processed', 0);
        $changes = $this->db->get('dealer_timed_changes')->result();
        
        $processed_count = 0;
        
        foreach ($changes as $change) {
            // Kullanıcının bayilik bilgisini getir
            $user_dealer = $this->getUserDealerInfo($change->user_id);
            
            // Kullanıcı hala aktif bir bayiyse ve beklenen bayilikte ise
            if ($user_dealer && $user_dealer->dealer_type_id == $change->current_dealer_type_id) {
                // Bayilik seviyesini değiştir
                $this->assignDealerToUser(
                    $change->user_id,
                    $change->next_dealer_type_id,
                    'Süreli bayilik değişimi - Otomatik',
                    1 // sistem tarafından yapıldığını belirt
                );
                
                // Kullanıcıya bildirim gönder
                $dealer_type = $this->getDealerTypeById($change->next_dealer_type_id);
                $notification_message = 'Bayilik tipiniz otomatik olarak güncellendi. Artık ' . $dealer_type->name . ' bayimizsiniz.';
                sendNotificationSite($change->user_id, 'Bayilik Tipiniz Güncellendi', $notification_message, base_url('client/my_dealer'), 'admin');
                
                $processed_count++;
            }
            
            // İşlenmis olarak işaretle
            $this->db->where('id', $change->id);
            $this->db->update('dealer_timed_changes', ['is_processed' => 1]);
        }
        
        return $processed_count;
    }
    
    /**
     * Kullanıcının aylık satın alım kazançlarını getirir
     * 
     * @param int $user_id Kullanıcı ID
     * @param string $year_month Yıl ve ay (YYYY-MM formatında) veya null ise tüm aylar
     * @return array
     */
    public function getUserMonthlyEarnings($user_id, $year_month = null) {
        $this->db->select('
            SUM(amount) as total_amount,
            DATE_FORMAT(transaction_date, "%Y-%m") as month_year,
            COUNT(*) as transaction_count
        ');
        $this->db->from('earnings');
        $this->db->where('buyer_id', $user_id);
        $this->db->where_in('payment_method', ['credit_cart', 'balance']);
        $this->db->where('payment_type', 'Satın alım');
        $this->db->where('transaction_status', 'successful');
        
        if ($year_month) {
            $this->db->where('DATE_FORMAT(transaction_date, "%Y-%m") = ', $year_month);
        }
        
        $this->db->group_by('month_year');
        $this->db->order_by('month_year', 'DESC');
        
        return $this->db->get()->result();
    }
    
    /**
     * Kullanıcının son 12 aydaki toplam alım tutarını getirir
     * 
     * @param int $user_id Kullanıcı ID
     * @return float
     */
    public function getUserTotalEarningsLastYear($user_id) {
        $one_year_ago = date('Y-m-d', strtotime('-1 year'));
        
        $this->db->select_sum('amount', 'total_amount');
        $this->db->from('earnings');
        $this->db->where('buyer_id', $user_id);
        $this->db->where_in('payment_method', ['credit_cart', 'balance']);
        $this->db->where('payment_type', 'Satın alım');
        $this->db->where('transaction_status', 'successful');
        $this->db->where('transaction_date >=', $one_year_ago);
        
        $result = $this->db->get()->row();
        return $result ? $result->total_amount : 0;
    }

    /**
     * Kullanıcının günlük satın alım tutarını getirir
     * 
     * @param int $user_id Kullanıcı ID
     * @param string $date Tarih (YYYY-MM-DD formatında) veya null ise bugün
     * @return float
     */
    public function getUserDailyEarnings($user_id, $date = null) {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        $this->db->select_sum('amount', 'total_amount');
        $this->db->from('earnings');
        $this->db->where('buyer_id', $user_id);
        $this->db->where_in('payment_method', ['credit_cart', 'balance']);
        $this->db->where('payment_type', 'Satın alım');
        $this->db->where('transaction_status', 'successful');
        $this->db->where('DATE(transaction_date)', $date);
        
        $result = $this->db->get()->row();
        return $result ? $result->total_amount : 0;
    }
    
    /**
     * Kullanıcının belirli bir aydaki toplam satış tutarını getirir
     *
     * @param int $user_id Kullanıcı ID
     * @param string $year_month Yıl ve ay (YYYY-MM formatında) veya null ise mevcut ay
     * @return float
     */
    public function getUserTotalMonthlyEarnings($user_id, $year_month = null) {
        if (!$year_month) {
            $year_month = date('Y-m');
        }
        
        $this->db->select_sum('amount', 'total_amount');
        $this->db->from('earnings');
        $this->db->where('buyer_id', $user_id);
        $this->db->where_in('payment_method', ['credit_cart', 'balance']);
        $this->db->where('payment_type', 'Satın alım');
        $this->db->where('transaction_status', 'successful');
        $this->db->where('DATE_FORMAT(transaction_date, "%Y-%m") = ', $year_month);
        
        $result = $this->db->get()->row();
        return $result ? $result->total_amount : 0;
    }
    
    /**
     * Kullanıcının ciro hedefini ve gerçekleşme durumunu getirir
     * 
     * @param int $user_id Kullanıcı ID
     * @return object Hedef ve gerçekleşme bilgilerini içeren nesne
     */
    public function getUserRevenueTargets($user_id) {
        // Kullanıcının bayilik bilgisini al
        $dealer_info = $this->getUserDealerInfo($user_id);
        if (!$dealer_info) {
            return null;
        }
        
        // Bayilik tipinin detaylarını al (min_purchase_amount kullanacağız hedef olarak)
        $dealer_type = $this->getDealerTypeById($dealer_info->dealer_type_id);
        if (!$dealer_type) {
            return null;
        }
        
        // Mevcut ayın toplam gün sayısını hesapla
        $year_month = date('Y-m');
        $total_days_in_month = date('t');
        
        // Hedefleri belirle (bayilik tipindeki min_purchase_amount'ı aylık hedef olarak kullan)
        $monthly_target = $dealer_type->min_purchase_amount;
        $daily_target = $monthly_target / $total_days_in_month;
        
        // Gerçekleşmeleri al
        $monthly_earnings = $this->getUserTotalMonthlyEarnings($user_id, $year_month);
        $daily_earnings = $this->getUserDailyEarnings($user_id);
        
        // Gerçekleşme oranlarını hesapla
        $monthly_completion_rate = $monthly_target > 0 ? ($monthly_earnings / $monthly_target) * 100 : 0;
        $daily_completion_rate = $daily_target > 0 ? ($daily_earnings / $daily_target) * 100 : 0;
        
        // Ayın kaçıncı günü olduğunu hesapla
        $current_day = date('j');
        $expected_monthly_completion = ($current_day / $total_days_in_month) * 100;
        
        // Ay sonu tahmini tutarı hesapla (günlük ortalama x ay gün sayısı)
        $daily_average = $current_day > 0 ? $monthly_earnings / $current_day : 0;
        $monthly_projection = $daily_average * $total_days_in_month;
        
        // Döndürülecek veri yapısını oluştur
        $result = new stdClass();
        $result->monthly_target = $monthly_target;
        $result->daily_target = $daily_target;
        $result->monthly_earnings = $monthly_earnings;
        $result->daily_earnings = $daily_earnings;
        $result->monthly_completion_rate = $monthly_completion_rate;
        $result->daily_completion_rate = $daily_completion_rate;
        $result->expected_monthly_completion = $expected_monthly_completion;
        $result->monthly_projection = $monthly_projection;
        $result->current_day = $current_day;
        $result->total_days_in_month = $total_days_in_month;
        
        return $result;
    }

    /**
     * Kullanıcı için süreli bayilik değişimi planlar
     * 
     * @param int $user_id Kullanıcı ID
     * @param int $final_dealer_type_id Hedef bayilik tipi ID
     * @param string $change_date Değişim tarihi (Y-m-d H:i:s)
     * @return bool İşlem başarılı ise true
     */
    public function scheduleTimedDealerChange($user_id, $final_dealer_type_id, $change_date) {
        // Önce mevcut planlamayı kontrol et/temizle
        $this->db->where('user_id', $user_id);
        $this->db->delete('dealer_timed_changes');
        
        // Kullanıcının mevcut bayilik bilgisini al
        $current_dealer = $this->getUserDealerInfo($user_id);
        $current_dealer_type_id = $current_dealer ? $current_dealer->dealer_type_id : 0;
        
        // Yeni planlama ekle
        $data = array(
            'user_id' => $user_id,
            'current_dealer_type_id' => $current_dealer_type_id,
            'next_dealer_type_id' => $final_dealer_type_id,
            'change_date' => $change_date,
            'is_processed' => 0, // 0: planlandı, 1: işlendi, 2: iptal edildi
            'created_at' => date('Y-m-d H:i:s')
        );
        
        return $this->db->insert('dealer_timed_changes', $data);
    }
} 