<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dealer extends G_Controller {

    public function __construct() {
        parent::__construct();
        if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
            redirect(base_url(), 'refresh');
            exit();
        }
        // Admin yetkisi kontrolü
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        
        // Bayilik modeli yükleniyor
        $this->load->model('M_Dealer');
    }

    /**
     * Bayilik yönetim paneli ana sayfası
     */
    public function index() {
        // Bekleyen başvuruları kontrol et ve ayarlara göre otomatik işlem yap
        $this->M_Dealer->checkPendingApplications();
        
        // Süreli bayilik değişikliklerini kontrol et
        $this->M_Dealer->checkTimedDealerChanges();
        
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'status' => 'dealerSettings',
            'dealer_types' => $this->M_Dealer->getAllDealerTypesWithUserCount(),
        ];

        $this->adminView('dealer/index', $data);
    }

    /**
     * Bayilik tipi ekleme işlemi
     */
    public function addDealerType() {
        $data = [
            'name' => $this->input->post('name'),
            'min_purchase_amount' => $this->input->post('min_purchase_amount'),
            'discount_percentage' => $this->input->post('discount_percentage'),
            'upgrade_condition' => $this->input->post('upgrade_condition'),
            'description' => $this->input->post('description'),
            'auto_upgrade' => $this->input->post('auto_upgrade') ? 1 : 0,
            'status' => 1
        ];

        $this->M_Dealer->addDealerType($data);
        
        addlog('addDealerType', 'Yeni bayilik tipi eklendi: ' . $data['name']);
        flash('Başarılı', 'Bayilik tipi başarıyla eklendi.');
        redirect(base_url('admin/dealer'), 'refresh');
    }

    /**
     * Bayilik tipi düzenleme sayfası
     */
    public function editDealerType($id) {
        $dealer_type = $this->M_Dealer->getDealerTypeById($id);
        
        if (!$dealer_type) {
            flash('Hata', 'Bayilik tipi bulunamadı.');
            redirect(base_url('admin/dealer'), 'refresh');
            return;
        }
        
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'status' => 'dealerSettings',
            'dealer_type' => $dealer_type
        ];

        $this->adminView('dealer/type-edit', $data);
    }

    /**
     * Bayilik tipi güncelleme işlemi
     */
    public function updateDealerType($id) {
        $dealer_type = $this->M_Dealer->getDealerTypeById($id);
        
        if (!$dealer_type) {
            flash('Hata', 'Bayilik tipi bulunamadı.');
            redirect(base_url('admin/dealer'), 'refresh');
            return;
        }
        
        $data = [
            'name' => $this->input->post('name'),
            'min_purchase_amount' => $this->input->post('min_purchase_amount'),
            'discount_percentage' => $this->input->post('discount_percentage'),
            'upgrade_condition' => $this->input->post('upgrade_condition'),
            'description' => $this->input->post('description'),
            'auto_upgrade' => $this->input->post('auto_upgrade') ? 1 : 0
        ];

        $this->M_Dealer->updateDealerType($id, $data);
        
        addlog('updateDealerType', 'Bayilik tipi güncellendi: ' . $data['name']);
        flash('Başarılı', 'Bayilik tipi başarıyla güncellendi.');
        redirect(base_url('admin/dealer'), 'refresh');
    }

    /**
     * Bayilik tipini devre dışı bırakma işlemi
     */
    public function deleteDealerType($id) {
        $dealer_type = $this->M_Dealer->getDealerTypeById($id);
        
        if (!$dealer_type) {
            flash('Hata', 'Bayilik tipi bulunamadı.');
            redirect(base_url('admin/dealer'), 'refresh');
            return;
        }

        // Yeni bayilik ID'si ve açıklama POST ile geldiyse, kullanıcıları taşı
        $new_dealer_type_id = $this->input->post('new_dealer_type_id');
        $description = $this->input->post('description');
        $convert_to_normal = $this->input->post('convert_to_normal');

        if ($new_dealer_type_id) {
            // Eski bayilikteki kullanıcıları yeni bayiliğe taşı
            $migrated_count = $this->M_Dealer->migrateUsersToNewDealerType(
                $id, 
                $new_dealer_type_id, 
                $description ? $description : 'Bayilik planı silindi: ' . $dealer_type->name
            );
            
            // Bayilik tipini devre dışı bırak
            $this->M_Dealer->deleteDealerType($id);
            
            addlog('deleteDealerType', 'Bayilik tipi silindi ve ' . $migrated_count . ' kullanıcı taşındı: ' . $dealer_type->name);
            flash('Başarılı', 'Bayilik tipi silindi ve ' . $migrated_count . ' kullanıcı yeni bayiliğe taşındı.');
            redirect(base_url('admin/dealer'), 'refresh');
            return;
        } elseif ($convert_to_normal == '1') {
            // Kullanıcıları normal üyeliğe çevir
            $migrated_count = $this->M_Dealer->normalUsersFromDealer(
                $id,
                $description ? $description : 'Bayilik planı silindi ve normal üyeliğe dönüştürüldü: ' . $dealer_type->name
            );
            
            // Bayilik tipini devre dışı bırak
            $this->M_Dealer->deleteDealerType($id);
            
            addlog('deleteDealerType', 'Bayilik tipi silindi ve ' . $migrated_count . ' kullanıcı normal üyeliğe çevrildi: ' . $dealer_type->name);
            flash('Başarılı', 'Bayilik tipi silindi ve ' . $migrated_count . ' kullanıcı normal üyeliğe çevrildi.');
            redirect(base_url('admin/dealer'), 'refresh');
            return;
        }
        
        // Eğer POST değerleri yoksa, taşınacak kullanıcı var mı kontrol et
        $dealer_users = $this->M_Dealer->getUsersByDealerType($id);
        
        if (!empty($dealer_users)) {
            // Eğer bu bayilikte kullanıcılar varsa, modal göstermek için session'a bilgi ekle
            $this->session->set_flashdata('show_migration_modal', true);
            $this->session->set_flashdata('dealer_type_to_delete', $dealer_type);
            $this->session->set_flashdata('dealer_users_count', count($dealer_users));
            
            // Aktif bayilik sayısını kontrol et
            $active_dealer_types = $this->M_Dealer->getActiveDealerTypes();
            $this->session->set_flashdata('is_last_dealer_type', count($active_dealer_types) <= 1);
            
            redirect(base_url('admin/dealer'), 'refresh');
            return;
        }
        
        // Eğer bayilikte kullanıcı yoksa, direkt sil
        $this->M_Dealer->deleteDealerType($id);
        
        addlog('deleteDealerType', 'Bayilik tipi silindi: ' . $dealer_type->name);
        flash('Başarılı', 'Bayilik tipi başarıyla silindi.');
        redirect(base_url('admin/dealer'), 'refresh');
    }

    /**
     * Bayilik kullanıcıları listesi
     */
    public function dealerUsers($dealer_type_id = null) {
        if ($dealer_type_id) {
            $dealer_type = $this->M_Dealer->getDealerTypeById($dealer_type_id);
            
            if (!$dealer_type) {
                flash('Hata', 'Bayilik tipi bulunamadı.');
                redirect(base_url('admin/dealer'), 'refresh');
                return;
            }
            
            $data = [
                'properties' => $this->db->where('id', 1)->get('properties')->row(),
                'status' => 'dealerUsers',
                'dealer_type' => $dealer_type,
                'dealer_users' => $this->M_Dealer->getUsersByDealerType($dealer_type_id)
            ];
        } else {
            $data = [
                'properties' => $this->db->where('id', 1)->get('properties')->row(),
                'status' => 'dealerUsers',
                'dealer_users' => $this->M_Dealer->getAllDealerUsers()
            ];
        }
        
        $data['dealer_types'] = $this->M_Dealer->getActiveDealerTypes();
        $data['users'] = $this->db->where('isActive', 1)->get('user')->result();

        $this->adminView('dealer/users', $data);
    }

    /**
     * Kullanıcıya bayilik atama işlemi
     */
    public function assignDealer() {
        $user_id = $this->input->post('user_id');
        $dealer_type_id = $this->input->post('dealer_type_id');
        $description = $this->input->post('description');
        
        // Süreli bayilik parametreleri
        $enable_timed_dealer = $this->input->post('enable_timed_dealer');
        $dealer_period = $this->input->post('dealer_period');
        $final_dealer_type_id = $this->input->post('final_dealer_type_id');
        
        $user = $this->db->where('id', $user_id)->get('user')->row();
        $dealer_type = $this->M_Dealer->getDealerTypeById($dealer_type_id);
        
        if (!$user || !$dealer_type) {
            flash('Hata', 'Kullanıcı veya bayilik tipi bulunamadı.');
            redirect(base_url('admin/dealer/dealerUsers'), 'refresh');
            return;
        }
        
        // Admin kullanıcı ID'sini al
        $admin_id = $this->session->userdata('info')['id'];
        
        // Bayilik ataması
        $this->M_Dealer->assignDealerToUser($user_id, $dealer_type_id, $description, $admin_id);
        
        // Süreli bayilik değişimi planlama
        if ($enable_timed_dealer == '1' && !empty($dealer_period) && !empty($final_dealer_type_id)) {
            $final_dealer_type = $this->M_Dealer->getDealerTypeById($final_dealer_type_id);
            
            if ($final_dealer_type) {
                // Değişim tarihi hesapla
                $change_date = date('Y-m-d H:i:s', strtotime('+' . $dealer_period . ' days'));
                
                // Timed dealer değişimini kaydet
                $this->M_Dealer->scheduleTimedDealerChange($user_id, $final_dealer_type_id, $change_date);
                
                // Log ekle
                addlog('scheduleTimedDealer', 'Kullanıcıya süreli bayilik planlandı: ' . $user->name . ' ' . $user->surname . 
                       ' - Şu anki: ' . $dealer_type->name . 
                       ' - ' . $dealer_period . ' gün sonra: ' . $final_dealer_type->name);
            }
        }
        
        addlog('assignDealer', 'Kullanıcıya bayilik atandı: ' . $user->name . ' ' . $user->surname . ' - Bayilik: ' . $dealer_type->name);
        flash('Başarılı', 'Kullanıcıya bayilik başarıyla atandı.');
        redirect(base_url('admin/dealer/dealerUsers'), 'refresh');
    }

    /**
     * Kullanıcı bayilik durumunu değiştirme
     */
    public function toggleDealerStatus($user_id) {
        $dealer_info = $this->M_Dealer->getUserDealerInfo($user_id);
        
        if (!$dealer_info) {
            flash('Hata', 'Kullanıcı bayilik bilgisi bulunamadı.');
            redirect(base_url('admin/dealer/dealerUsers'), 'refresh');
            return;
        }
        
        $new_status = $dealer_info->active_status ? 0 : 1;
        $this->M_Dealer->updateUserDealerStatus($user_id, $new_status);
        
        $status_text = $new_status ? 'aktifleştirildi' : 'devre dışı bırakıldı';
        addlog('toggleDealerStatus', 'Kullanıcı bayilik durumu değiştirildi: ' . $dealer_info->user_id . ' - Durum: ' . $status_text);
        flash('Başarılı', 'Kullanıcı bayilik durumu ' . $status_text . '.');
        redirect(base_url('admin/dealer/dealerUsers'), 'refresh');
    }

    /**
     * Ürün fiyatlandırma yönetimi
     */
    public function productPricing($dealer_type_id = null) {
        if (!$dealer_type_id) {
            $dealer_types = $this->M_Dealer->getActiveDealerTypes();
            if (count($dealer_types) > 0) {
                $dealer_type_id = $dealer_types[0]->id;
            } else {
                flash('Uyarı', 'Önce bir bayilik tipi oluşturmalısınız.');
                redirect(base_url('admin/dealer'), 'refresh');
                return;
            }
        }
        
        $dealer_type = $this->M_Dealer->getDealerTypeById($dealer_type_id);
        
        if (!$dealer_type) {
            flash('Hata', 'Bayilik tipi bulunamadı.');
            redirect(base_url('admin/dealer'), 'refresh');
            return;
        }
        
        // Kategorileri yükle
        $categories = $this->db->where('isActive', 1)->order_by('name', 'ASC')->get('category')->result();
        
        // Sadece admin ürünlerini yükle (seller_id = 0)
        $products = $this->db->where('isActive', 1)
                             ->where('seller_id', 0)
                             ->order_by('name', 'ASC')
                             ->get('product')
                             ->result();
        
        // Her ürün için bayilik fiyatlarını yükle
        foreach ($products as $product) {
            $product->dealer_price = $this->M_Dealer->getDealerProductPrice($dealer_type_id, $product->id);
        }
        
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'status' => 'productPricing',
            'dealer_type' => $dealer_type,
            'dealer_types' => $this->M_Dealer->getActiveDealerTypes(),
            'categories' => $categories,
            'products' => $products
        ];

        $this->adminView('dealer/product-pricing', $data);
    }

    /**
     * Ürün için bayilik fiyatı güncelleme
     */
    public function updateProductPrice() {
        $dealer_type_id = $this->input->post('dealer_type_id');
        $product_id = $this->input->post('product_id');
        $price_type = $this->input->post('price_type');
        
        // Gerekli parametreleri kontrol et
        if (empty($dealer_type_id) || empty($product_id) || empty($price_type)) {
            echo json_encode(['success' => false, 'message' => 'Gerekli bilgiler eksik']);
            return;
        }
        
        // Ürünü al
        $product = $this->db->where('id', $product_id)->get('product')->row();
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Ürün bulunamadı']);
            return;
        }
        
        if ($price_type == 'default') {
            // Varsayılan indirim - Özel fiyat belirtilmemiş
            $this->M_Dealer->setDealerProductPrice($dealer_type_id, $product_id, null, null);
        } else if ($price_type == 'discount') {
            $discount_percentage = $this->input->post('discount_percentage');
            if (!is_numeric($discount_percentage) || $discount_percentage < 0 || $discount_percentage > 100) {
                echo json_encode(['success' => false, 'message' => 'Geçerli bir indirim oranı girilmedi']);
                return;
            }
            
            // İndirim yüzdesi belirle
            $this->M_Dealer->setDealerProductPrice($dealer_type_id, $product_id, null, $discount_percentage);
        } else if ($price_type == 'special_price') {
            // Özel fiyat belirle
            $special_price = $this->input->post('special_price');
            if (!is_numeric($special_price) || $special_price < 0) {
                echo json_encode(['success' => false, 'message' => 'Geçerli bir fiyat girilmedi']);
                return;
            }
            $this->M_Dealer->setDealerProductPrice($dealer_type_id, $product_id, $special_price, null);
        } else {
            echo json_encode(['success' => false, 'message' => 'Geçersiz fiyat tipi']);
            return;
        }
        
        addlog('updateProductPrice', 'Ürün için bayilik fiyatı güncellendi: Ürün ID: ' . $product_id . ', Bayilik Tipi ID: ' . $dealer_type_id);
        echo json_encode(['success' => true]);
    }

    /**
     * Toplu ürün fiyat güncelleme
     */
    public function bulkUpdatePrices() {
        $dealer_type_id = $this->input->post('dealer_type_id');
        $price_type = $this->input->post('price_type');
        $category_id = $this->input->post('category_id');
        
        // Bayilik tipi kontrolü
        if (empty($dealer_type_id)) {
            flash('Hata', 'Bayilik tipi seçilmedi.');
            redirect(base_url('admin/dealer/productPricing'), 'refresh');
            return;
        }
        
        // Fiyat tipi kontrolü
        if (empty($price_type) || $price_type != 'discount') {
            flash('Hata', 'Geçerli bir fiyat tipi seçilmedi.');
            redirect(base_url('admin/dealer/productPricing/' . $dealer_type_id), 'refresh');
            return;
        }
        
        $discount_percentage = $this->input->post('discount_percentage');
        if (!is_numeric($discount_percentage) || $discount_percentage < 0 || $discount_percentage > 100) {
            flash('Hata', 'Geçerli bir indirim oranı girilmedi (0-100 arası).');
            redirect(base_url('admin/dealer/productPricing/' . $dealer_type_id), 'refresh');
            return;
        }
        
        // Sadece admin ürünlerini seç (seller_id = 0)
        $this->db->where('isActive', 1)->where('seller_id', 0);
        
        // Ürünleri seç (kategori belirtilmişse sadece o kategorideki ürünler)
        if ($category_id) {
            $this->db->where('category_id', $category_id);
        }
        
        $products = $this->db->get('product')->result();
        
        if (empty($products)) {
            flash('Bilgi', 'Seçilen kriterlere uygun ürün bulunamadı.');
            redirect(base_url('admin/dealer/productPricing/' . $dealer_type_id), 'refresh');
            return;
        }
        
        $count = 0;
        foreach ($products as $product) {
            // İndirim yüzdesi kaydederken special_price null olmalı
            $this->M_Dealer->setDealerProductPrice($dealer_type_id, $product->id, null, $discount_percentage);
            $count++;
        }
        
        addlog('bulkUpdatePrices', 'Toplu ürün fiyatı güncellendi: ' . $count . ' ürün, Bayilik Tipi ID: ' . $dealer_type_id);
        flash('Başarılı', $count . ' ürün için fiyat ayarları güncellendi.');
        redirect(base_url('admin/dealer/productPricing/' . $dealer_type_id), 'refresh');
    }

    /**
     * Otomatik bayilik yükseltme kontrolü
     */
    public function checkUpgrades() {
        $count = $this->M_Dealer->checkDealerUpgrades();
        
        addlog('checkUpgrades', 'Otomatik bayilik yükseltme kontrolü yapıldı. Yükseltilen kullanıcı sayısı: ' . $count);
        flash('Bilgi', 'Bayilik kontrolleri tamamlandı. Yükseltilen kullanıcı sayısı: ' . $count);
        redirect(base_url('admin/dealer/dealerUsers'), 'refresh');
    }

    /**
     * Kullanıcının bayilik geçmişini görüntüler
     */
    public function userHistory($user_id) {
        // Kullanıcı bilgilerini al
        $user = $this->db->where('id', $user_id)->get('user')->row();
        
        if (!$user) {
            flash('Hata', 'Kullanıcı bulunamadı.');
            redirect(base_url('admin/dealer/dealerUsers'), 'refresh');
            return;
        }
        
        // Kullanıcının mevcut bayilik bilgisini al
        $user_dealer = $this->M_Dealer->getUserDealerInfo($user_id);
        
        // Kullanıcının geçmiş bayilik atamalarını al
        $dealer_history = $this->M_Dealer->getUserDealerHistory($user_id);
        
        // Bayilik tipleri
        $dealer_types = $this->M_Dealer->getActiveDealerTypes();
        
        // Kullanıcının süreli bayilik değişim planlamasını kontrol et
        $timed_change = $this->db->where('user_id', $user_id)
                                ->where('is_processed', 0)
                                ->get('dealer_timed_changes')
                                ->row();
        
        // Mevcut ay ve yıl bilgilerini al
        $currentMonth = date('Y-m');
        
        // Kullanıcının ciro bilgilerini al 
        $monthly_earnings = $this->M_Dealer->getUserMonthlyEarnings($user_id);
        $current_month_earnings = $this->M_Dealer->getUserMonthlyEarnings($user_id, $currentMonth);
        $yearly_earnings = $this->M_Dealer->getUserTotalEarningsLastYear($user_id);
        $revenue_targets = $this->M_Dealer->getUserRevenueTargets($user_id);
        
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'status' => 'dealerUsers',
            'user' => $user,
            'dealer_info' => $user_dealer,
            'dealer_history' => $dealer_history,
            'dealer_types' => $dealer_types,
            'timed_change' => $timed_change,
            'monthly_earnings' => $monthly_earnings,
            'current_month_earnings' => $current_month_earnings,
            'yearly_earnings' => $yearly_earnings,
            'revenue_targets' => $revenue_targets
        ];
        
        addlog('viewDealerHistory', 'Kullanıcının bayilik geçmişi görüntülendi: ' . $user->name . ' ' . $user->surname);
        $this->adminView('dealer/user-history', $data);
    }
    
    /**
     * Bayilik başvurularını listeler
     * 
     * @param string $status Başvuru durumu filtresi (all, pending, approved, rejected)
     * @return void
     */
    public function applications($status = NULL)
    {   
        // Başvuruları çek
        if ($status === NULL || $status === 'all') {
            $applications = $this->M_Dealer->getAllDealerApplications();
            $current_status = NULL;
        } else {
            $applications = $this->M_Dealer->getDealerApplicationsByStatus($status);
            $current_status = $status;
        }
        
        // Başvuru sayılarını hesapla
        $counts = [
            'all' => count($this->M_Dealer->getAllDealerApplications()),
            'pending' => count($this->M_Dealer->getDealerApplicationsByStatus('pending')),
            'approved' => count($this->M_Dealer->getDealerApplicationsByStatus('approved')),
            'rejected' => count($this->M_Dealer->getDealerApplicationsByStatus('rejected'))
        ];
        
        // Bayi tiplerini çek
        $dealer_types = $this->M_Dealer->getActiveDealerTypes();
        
        $data = [
            'title' => 'Bayilik Başvuruları',
            'applications' => $applications,
            'current_status' => $current_status,
            'counts' => $counts,
            'status' => 'dealerUsers',
            'dealer_types' => $dealer_types
        ];
        
        $this->adminView('dealer/applications', $data);
    }
    
    /**
     * Bayilik başvurusunu onaylar
     * 
     * @return void
     */
    public function approveApplication()
    {   
        $application_id = $this->input->post('application_id');
        $dealer_type_id = $this->input->post('dealer_type_id');
        
        if (!$application_id || !$dealer_type_id) {
            $this->session->set_flashdata('error', 'Gerekli bilgiler eksik');
            redirect('admin/dealer/applications');
        }
        
        // Başvuru detayını çek
        $application = $this->M_Dealer->getDealerApplicationById($application_id);
        
        if (!$application || $application->status !== 'pending') {
            $this->session->set_flashdata('error', 'Geçersiz başvuru veya başvuru durumu');
            redirect('admin/dealer/applications');
        }
        
        // Başvuru durumunu güncelle
        $this->M_Dealer->updateDealerApplicationStatus($application_id, 'approved', '');
        
        // Kullanıcının mevcut bayilik bilgisini kontrol et
        $user_dealer_info = $this->M_Dealer->getUserDealerInfo($application->user_id);
        
        // Kullanıcı henüz bayi değilse, bayilik ata
        if (!$user_dealer_info) {
            // Admin ID'sini al
            $admin_id = $this->session->userdata('info')['id'];
            
            // assignDealerTypeToUser yerine dealer history kaydını da oluşturan assignDealerToUser kullanılacak
            $this->M_Dealer->assignDealerToUser(
                $application->user_id, 
                $dealer_type_id, 
                'Bayilik başvurusu onayı: ' . $this->M_Dealer->getDealerTypeById($dealer_type_id)->name,
                $admin_id
            );
            
            // Süreli bayilik ataması yapılacak mı?
            $enable_timed_dealer = $this->M_Dealer->getSetting('dealer_enable_timed_dealer', '0');
            $initial_dealer_type_id = $this->M_Dealer->getSetting('dealer_initial_dealer_type_id');
            $final_dealer_type_id = $this->M_Dealer->getSetting('dealer_final_dealer_type_id');
            $dealer_period = $this->M_Dealer->getSetting('dealer_period', '30');
            
            if ($enable_timed_dealer == '1' && !empty($final_dealer_type_id)) {
                // İlk atanan bayilik tipi, süreli bayilik ayarlarındaki ilk tip ile aynıysa
                if ($dealer_type_id == $initial_dealer_type_id) {
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
            $dealer_type = $this->M_Dealer->getDealerTypeById($dealer_type_id);
            $notification_message = 'Bayilik başvurunuz onaylandı. Artık ' . $dealer_type->name . ' bayimizsiniz.';
            
            // Süreli bayilik ise bildir
            if ($enable_timed_dealer == '1' && $dealer_type_id == $initial_dealer_type_id) {
                $notification_message .= ' ' . $dealer_period . ' gün sonra bayilik tipiniz güncellenecektir.';
            }
            
            $notification_message .= ' Bayilik avantajlarından yararlanmak için hesabınıza giriş yapabilirsiniz.';
            sendNotificationSite($application->user_id, 'Bayilik Başvurunuz Onaylandı', $notification_message, base_url('client/my_dealer'), 'admin');
        } else {
            // Kullanıcı zaten bayi ise, sadece bildirim gönder
            $notification_message = 'Bayilik başvurunuz onaylanmıştır.';
            
            // Bildirim gönder
            sendNotificationSite($application->user_id, 'Bayilik Başvurunuz Onaylandı', $notification_message, base_url('client/my_dealer'), 'admin');
        }
        
        $this->session->set_flashdata('success', 'Bayilik başvurusu başarıyla onaylandı');
        redirect('admin/dealer/applications/pending');
    }
    
    /**
     * Bayilik başvurusunu reddeder
     * 
     * @return void
     */
    public function rejectApplication()
    {   
        $application_id = $this->input->post('application_id');
        $admin_notes = $this->input->post('admin_notes');
        
        if (!$application_id) {
            $this->session->set_flashdata('error', 'Gerekli bilgiler eksik');
            redirect('admin/dealer/applications');
        }
        
        // Başvuru detayını çek
        $application = $this->M_Dealer->getDealerApplicationById($application_id);
        
        if (!$application || $application->status !== 'pending') {
            $this->session->set_flashdata('error', 'Geçersiz başvuru veya başvuru durumu');
            redirect('admin/dealer/applications');
        }
        
        // Başvuru durumunu güncelle
        $this->M_Dealer->updateDealerApplicationStatus($application_id, 'rejected', $admin_notes);
        
        // Kullanıcıya bildirim gönder
        $notification_message = 'Bayilik başvurunuz reddedildi. ';
        
        $notification_message .= 'Detaylar için bayilik sayfanızı kontrol ediniz veya bizimle iletişime geçebilirsiniz.';
        sendNotificationSite($application->user_id, 'Bayilik Başvurunuz Reddedildi', $notification_message, base_url('client/my_dealer'), 'admin');
        
        $this->session->set_flashdata('success', 'Bayilik başvurusu başarıyla reddedildi');
        redirect('admin/dealer/applications/pending');
    }
    
    /**
     * Bayilik ayarları sayfasını gösterir
     * 
     * @return void
     */
    public function settings()
    {
        // Bayilik tiplerini çek
        $dealer_types = $this->M_Dealer->getActiveDealerTypes();
        
        // Bayilik ayarlarının varlığını kontrol et ve yoksa oluştur
        $this->ensureDealerSettings();
        
        // Mevcut ayarları çek
        $settings = $this->M_Dealer->getSettingsByPrefix('dealer_');
        
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'status' => 'dealerSettings',
            'dealer_types' => $dealer_types,
            'settings' => $settings
        ];
        
        $this->adminView('dealer/settings', $data);
    }
    
    /**
     * Bayilik ayarlarının varlığını kontrol eder ve yoksa oluşturur
     */
    private function ensureDealerSettings()
    {
        // Bayilik ayarlarının varlığını kontrol et
        $auto_approve = $this->M_Dealer->getSetting('dealer_auto_approve', null);
        
        // Eğer bayilik ayarları yoksa, varsayılan değerlerle oluştur
        if ($auto_approve === null) {
            $default_settings = [
                'dealer_auto_approve' => '0',
                'dealer_default_dealer_type_id' => '',
                'dealer_enable_timed_dealer' => '0',
                'dealer_initial_dealer_type_id' => '',
                'dealer_period' => '30',
                'dealer_final_dealer_type_id' => ''
            ];
            
            $this->M_Dealer->saveSettings($default_settings);
            
            addlog('createDealerSettings', 'Bayilik ayarları oluşturuldu (varsayılan değerlerle)');
        }
    }
    
    /**
     * Bayilik ayarlarını kaydeder
     * 
     * @return void
     */
    public function saveSettings()
    {
        // Form verilerini al
        $settings = [
            'dealer_auto_approve' => $this->input->post('auto_approve') ? '1' : '0',
            'dealer_default_dealer_type_id' => $this->input->post('default_dealer_type_id'),
            'dealer_enable_timed_dealer' => $this->input->post('enable_timed_dealer') ? '1' : '0',
            'dealer_initial_dealer_type_id' => $this->input->post('initial_dealer_type_id'),
            'dealer_period' => $this->input->post('dealer_period'),
            'dealer_final_dealer_type_id' => $this->input->post('final_dealer_type_id')
        ];
        
        // Ayarları kaydet
        $success = $this->M_Dealer->saveSettings($settings);
        
        if ($success) {
            addlog('saveDealerSettings', 'Bayilik ayarları güncellendi');
            flash('Başarılı', 'Bayilik ayarları başarıyla güncellendi.');
        } else {
            flash('Hata', 'Bayilik ayarları güncellenirken bir hata oluştu.');
        }
        
        redirect(base_url('admin/dealer/settings'), 'refresh');
    }

    /**
     * Kullanıcı için planlanmış süreli bayilik değişimini iptal eder
     */
    public function cancelTimedChange($user_id) {
        $user = $this->db->where('id', $user_id)->get('user')->row();
        
        if (!$user) {
            flash('Hata', 'Kullanıcı bulunamadı.');
            redirect(base_url('admin/dealer/dealerUsers'), 'refresh');
            return;
        }
        
        // Planlanan değişimi al
        $timed_change = $this->db->where('user_id', $user_id)
                               ->where('is_processed', 0)
                               ->get('dealer_timed_changes')
                               ->row();
        
        if ($timed_change) {
            // Değişimi iptal et
            $this->db->where('id', $timed_change->id);
            $this->db->update('dealer_timed_changes', ['is_processed' => 2]);
            
            addlog('cancelTimedChange', 'Kullanıcının planlı bayilik değişimi iptal edildi: ' . $user->name . ' ' . $user->surname);
            flash('Başarılı', 'Planlanmış bayilik değişimi iptal edildi.');
        } else {
            flash('Bilgi', 'Kullanıcıya ait planlanmış bayilik değişimi bulunamadı.');
        }
        
        redirect(base_url('admin/dealer/userHistory/' . $user_id), 'refresh');
    }
} 