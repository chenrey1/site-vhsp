<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Referral System Library
 * 
 * Referans sistemi için tüm işlemleri yöneten library
 * 
 * @package     CodeIgniter
 * @category    Libraries
 * @author      Enes Aydeniz
 * @version     1.0.0
 */
class Referral_System {
    
    protected $CI;
    protected $user_id;
    protected $settings;
    
    public function __construct($config = []) {
        $this->CI =& get_instance();
        
        // Model ve helper'ları yükle
        $this->CI->load->model('M_Settings');
        $this->CI->load->helper(['url', 'text']);
        $this->CI->load->database();
        
        // Kullanıcı ID'sini belirle
        $this->user_id = isset($config['user_id']) ? $config['user_id'] : 
                        (!empty($this->CI->session->userdata('info')['id']) ? 
                         $this->CI->session->userdata('info')['id'] : null);
        
        // Referans ayarlarını yükle
        $this->loadSettings();
    }
    
    /**
     * Referans ayarlarını yükler ve cache'ler
     */
    private function loadSettings() {
        $raw_settings = [
            'system_enabled' => $this->CI->M_Settings->getSettingValue('referral_system_enabled', '0'),
            'register_bonus' => $this->CI->M_Settings->getSettingValue('referral_register_bonus_fixed', '0'),
            'purchase_bonus_rate' => $this->CI->M_Settings->getSettingValue('referral_purchase_bonus_rate', '5.00'),
            'min_purchase_amount' => $this->CI->M_Settings->getSettingValue('referral_min_purchase_amount', '10.00'),
            'bonus_balance_type' => $this->CI->M_Settings->getSettingValue('referral_bonus_balance_type', 'spendable'),
            'require_purchase' => $this->CI->M_Settings->getSettingValue('referral_require_purchase', '0'),
            'max_bonus_per_transaction' => $this->CI->M_Settings->getSettingValue('referral_max_bonus_per_transaction', '50.00'),
            'max_bonus_per_month' => $this->CI->M_Settings->getSettingValue('referral_max_bonus_per_month', '500.00'),
            'max_referrer_changes' => $this->CI->M_Settings->getSettingValue('max_referrer_changes', '3'),
            'referrer_change_cooldown_days' => $this->CI->M_Settings->getSettingValue('referrer_change_cooldown_days', '30'),
            'allow_referrer_change' => $this->CI->M_Settings->getSettingValue('allow_referrer_change', '0'),
            'ref_code_min_length' => $this->CI->M_Settings->getSettingValue('ref_code_min_length', '5'),
            'ref_code_max_length' => $this->CI->M_Settings->getSettingValue('ref_code_max_length', '20'),
            'ref_code_change_max_per_30_days' => $this->CI->M_Settings->getSettingValue('ref_code_change_max_per_30_days', '3'),
            'ref_code_change_cooldown_days' => $this->CI->M_Settings->getSettingValue('ref_code_change_cooldown_days', '7')
        ];
        
        // Validate ve sanitize
        $this->settings = [
            'system_enabled' => in_array($raw_settings['system_enabled'], ['0', '1']) ? $raw_settings['system_enabled'] : '0',
            'register_bonus' => max(0, floatval($raw_settings['register_bonus'])),
            'purchase_bonus_rate' => min(100, max(0, floatval($raw_settings['purchase_bonus_rate']))),
            'min_purchase_amount' => max(0, floatval($raw_settings['min_purchase_amount'])),
            'bonus_balance_type' => in_array($raw_settings['bonus_balance_type'], ['spendable', 'withdrawable']) ? $raw_settings['bonus_balance_type'] : 'spendable',
            'require_purchase' => in_array($raw_settings['require_purchase'], ['0', '1']) ? $raw_settings['require_purchase'] : '0',
            'max_bonus_per_transaction' => max(0, floatval($raw_settings['max_bonus_per_transaction'])),
            'max_bonus_per_month' => max(0, floatval($raw_settings['max_bonus_per_month'])),
            'max_referrer_changes' => min(10, max(0, intval($raw_settings['max_referrer_changes']))),
            'referrer_change_cooldown_days' => min(365, max(0, intval($raw_settings['referrer_change_cooldown_days']))),
            'allow_referrer_change' => in_array($raw_settings['allow_referrer_change'], ['0', '1']) ? $raw_settings['allow_referrer_change'] : '0',
            'ref_code_min_length' => min(20, max(3, intval($raw_settings['ref_code_min_length']))),
            'ref_code_max_length' => min(30, max(5, intval($raw_settings['ref_code_max_length']))),
            'ref_code_change_max_per_30_days' => min(50, max(0, intval($raw_settings['ref_code_change_max_per_30_days']))),
            'ref_code_change_cooldown_days' => min(365, max(0, intval($raw_settings['ref_code_change_cooldown_days'])))
        ];
    }
    
    /**
     * Referans sisteminin aktif olup olmadığını kontrol eder
     * 
     * @return bool
     */
    public function isSystemEnabled() {
        return $this->settings['system_enabled'] == '1';
    }
    
    /**
     * Kullanıcı için referans kodu oluşturur veya mevcut olanı döndürür
     * 
     * @param int $user_id Kullanıcı ID (opsiyonel)
     * @return string|false Referans kodu veya false
     */
    public function getUserReferralCode($user_id = null) {
        if (!$this->isSystemEnabled()) {
            return false;
        }
        
        $user_id = $user_id ?: $this->user_id;
        if (!$user_id || !is_numeric($user_id)) {
            return false;
        }
        
        // Type safety
        $user_id = intval($user_id);
        
        // Mevcut referans kodunu kontrol et
        $user = $this->CI->db->where('id', $user_id)->get('user')->row();
        
        if (!$user) {
            return false; // Kullanıcı bulunamadı
        }
        
        if (!empty($user->ref_code)) {
            return $user->ref_code;
        }
        
        // Transaction kullan
        $this->CI->db->trans_start();
        
        // Yeni referans kodu oluştur
        $ref_code = $this->generateReferralCode($user_id);
        
        // Veritabanına kaydet
        $this->CI->db->where('id', $user_id)
                    ->update('user', [
                        'ref_code' => $ref_code,
                        'ref_code_generated_at' => date('Y-m-d H:i:s')
                    ]);
        
        $this->CI->db->trans_complete();
        
        if ($this->CI->db->trans_status() === FALSE) {
            log_message('error', 'Failed to generate referral code for user: ' . $user_id);
            return false;
        }
        
        return $ref_code;
    }
    
    /**
     * Benzersiz referans kodu oluşturur
     * 
     * @param int $user_id Kullanıcı ID
     * @return string Referans kodu
     */
    private function generateReferralCode($user_id) {
        $min_length = intval($this->settings['ref_code_min_length']);
        $max_length = intval($this->settings['ref_code_max_length']);
        if ($min_length > $max_length) { $tmp = $min_length; $min_length = $max_length; $max_length = $tmp; }
        
        // Kullanıcı adını temizle
        $user = $this->CI->db->where('id', $user_id)->get('user')->row();
        $username = $user ? preg_replace('/[^a-zA-Z0-9]/', '', $user->name) : '';
        
        do {
            $length = rand($min_length, $max_length);
            
            // Kullanıcı adı yeterince uzunsa ve kod uzunluğu 4+ ise
            if (strlen($username) >= $min_length && $length >= 4) {
                // Kullanıcı adı + rastgele sayı
                $username_part_length = max(1, $length - 3); // En az 1 karakter al
                $ref_code = strtoupper(substr($username, 0, $username_part_length) . rand(100, 999));
            } else {
                // Tamamen rastgele
                $ref_code = strtoupper($this->randomString($length));
            }
            
            // Benzersizlik kontrolü
            $exists = $this->CI->db->where('ref_code', $ref_code)->count_all_results('user');
            
        } while ($exists > 0);
        
        return $ref_code;
    }
    
    /**
     * Rastgele string oluşturur
     * 
     * @param int $length Uzunluk
     * @return string
     */
    private function randomString($length = 8) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $result = '';
        
        for ($i = 0; $i < $length; $i++) {
            try {
                $result .= $characters[random_int(0, $charactersLength - 1)];
            } catch (Exception $e) {
                // Fallback to rand() if random_int fails
                $result .= $characters[rand(0, $charactersLength - 1)];
            }
        }
        return $result;
    }
    
    /**
     * Referans kodu ile referans veren kullanıcıyı bulur
     * 
     * @param string $ref_code Referans kodu
     * @return object|false Kullanıcı bilgileri veya false
     */
    public function getUserByReferralCode($ref_code) {
        if (!$this->isSystemEnabled() || empty($ref_code)) {
            return false;
        }
        
        // Dinamik uzunluk doğrulaması (admin ayarlarından)
        $minLen = intval($this->settings['ref_code_min_length']);
        $maxLen = intval($this->settings['ref_code_max_length']);
        if ($minLen > $maxLen) { $tmp = $minLen; $minLen = $maxLen; $maxLen = $tmp; }
        $pattern = '/^[A-Z0-9]{' . $minLen . ',' . $maxLen . '}$/';
        if (!preg_match($pattern, strtoupper($ref_code))) {
            return false;
        }
        
        return $this->CI->db->where('ref_code', $ref_code)
                           ->where('isActive', 1)
                           ->get('user')
                           ->row();
    }
    
    /**
     * İki kullanıcı arasında referans ilişkisi kurar
     * 
     * @param int $referrer_id Referans veren kullanıcı ID
     * @param int $referred_id Referans edilen kullanıcı ID
     * @return array İşlem sonucu
     */
    public function createReferralRelation($referrer_id, $referred_id) {
        if (!$this->isSystemEnabled()) {
            return ['success' => false, 'message' => 'Referans sistemi aktif değil'];
        }
        
        // Aynı kullanıcı kontrolü
        // GÜVENLİK DÜZELTMESİ: Type juggling açığı kapatıldı
        if ((int)$referrer_id === (int)$referred_id) {
            return ['success' => false, 'message' => 'Kendi kendinizi referans edemezsiniz'];
        }

        // Mevcut referans kontrolü
        $existing = $this->CI->db->where('buyer_id', $referred_id)
                                ->where('is_active', 1)
                                ->get('user_references')
                                ->row();
        
        // Eğer mevcut bir referans varsa
        if ($existing) {
            // Referans değiştirmeye izin var mı kontrol et
            if ($this->settings['allow_referrer_change'] == '0') {
                return ['success' => false, 'message' => 'Zaten bir referansınız bulunmaktadır. Referans değiştirme kapalıdır.'];
            }
            
            // İzin varsa, değişiklik limitlerini kontrol et
            $change_result = $this->canChangeReferrer($referred_id);
            if (!$change_result['can_change']) {
                return ['success' => false, 'message' => $change_result['message']];
            }

        }
        
        // Kullanıcı kontrolü
        $referrer = $this->CI->db->where('id', $referrer_id)->where('isActive', 1)->get('user')->row();
        $referred = $this->CI->db->where('id', $referred_id)->where('isActive', 1)->get('user')->row();
        
        if (!$referrer || !$referred) {
            return ['success' => false, 'message' => 'Geçersiz kullanıcı bilgileri'];
        }
        
        // Mevcut referansı pasif yap (varsa)
        if ($existing) {
            $this->CI->db->where('buyer_id', $referred_id)
                        ->update('user_references', [
                            'is_active' => 0,
                            'deactivated_at' => date('Y-m-d H:i:s')
                        ]);
        }
        
        // Yeni referans ilişkisi oluştur
        $relation_data = [
            'referrer_id' => intval($referrer_id), // Type safety
            'buyer_id' => intval($referred_id), // Type safety
            'ref_code_used' => $referrer->ref_code, // Kullanılan referans kodunu kaydet
            'bonus_earned' => 0,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->CI->db->insert('user_references', $relation_data);
        
        return ['success' => true, 'message' => 'Referans ilişkisi başarıyla kuruldu'];
    }
    
    /**
     * Kullanıcının referans değiştirip değiştiremeyeceğini kontrol eder
     * 
     * @param int $user_id Kullanıcı ID
     * @return array Kontrol sonucu
     */
    public function canChangeReferrer($user_id) {
        if ($this->settings['allow_referrer_change'] == '0') {
            return ['can_change' => false, 'message' => 'Referans değişikliği izin verilmiyor'];
        }
        
        // Değişiklik sayısı kontrolü
        $change_count = $this->CI->db->where('buyer_id', $user_id)->count_all_results('user_references');
        
        if ($change_count >= $this->settings['max_referrer_changes']) {
            return ['can_change' => false, 'message' => 'Maksimum referans değişiklik sayısına ulaştınız'];
        }
        
        // Cooldown kontrolü
        $last_change = $this->CI->db->where('buyer_id', $user_id)
                                   ->order_by('created_at', 'DESC')
                                   ->get('user_references')
                                   ->row();
        
        if ($last_change) {
            // GÜVENLİK DÜZELTMESİ: Settings validasyonu eklendi
            $cooldown_days = abs(intval($this->settings['referrer_change_cooldown_days']));
            $cooldown_days = min($cooldown_days, 365); // Maksimum 1 yıl limit
            
            $cooldown_end = strtotime($last_change->created_at . ' +' . $cooldown_days . ' days');
            if (time() < $cooldown_end) {
                $remaining_days = ceil(($cooldown_end - time()) / 86400);
                return ['can_change' => false, 'message' => "Referans değişikliği için {$remaining_days} gün beklemeniz gerekiyor"];
            }
        }
        
        return ['can_change' => true, 'message' => 'Referans değişikliği yapabilirsiniz'];
    }
    
    /**
     * Tek invoice için referans bonusu hesaplar ve verir
     * 
     * @param int $invoice_id Invoice ID
     * @return array İşlem sonucu
     */
    public function processInvoiceBonus($invoice_id) {
        if (!$this->isSystemEnabled()) {
            return ['success' => false, 'message' => 'Referans sistemi aktif değil'];
        }
        
        // Invoice bilgilerini al
        $invoice = $this->CI->db->select('i.*, s.user_id, s.type, p.category_id, c.name as category_name')
                               ->from('invoice i')
                               ->join('shop s', 'i.shop_id = s.id')
                               ->join('product p', 'i.product_id = p.id')
                               ->join('category c', 'p.category_id = c.id')
                               ->where('i.id', $invoice_id)
                               ->get()
                               ->row();
        
        if (!$invoice) {
            return ['success' => false, 'message' => 'Invoice bulunamadı'];
        }
        
        // Invoice teslim edilmiş olmalı
        if ($invoice->isActive != 0) {
            return ['success' => false, 'message' => 'Sadece teslim edilmiş ürünler için bonus verilir'];
        }
        
        // Bakiye yüklemesi referans bonusu vermez
        if ($invoice->type == 'deposit') {
            return ['success' => false, 'message' => 'Bakiye yüklemesi referans bonusu vermez'];
        }
        
        // Kullanıcının referansını bul
        $reference = $this->CI->db->where('buyer_id', $invoice->user_id)
                                 ->where('is_active', 1)
                                 ->get('user_references')
                                 ->row();
        
        if (!$reference) {
            return ['success' => false, 'message' => 'Kullanıcının referansı bulunmuyor'];
        }
        
        // Bu invoice için daha önce bonus verilmiş mi kontrol et
        $existing_bonus = $this->CI->db->where('invoice_id', $invoice_id)
                                      ->where('status', 'paid')
                                      ->get('reference_bonus_history')
                                      ->row();
        
        if ($existing_bonus) {
            return ['success' => false, 'message' => 'Bu ürün için daha önce bonus verilmiş'];
        }
        
        // Minimum tutar kontrolü - Type safety
        $min_amount = floatval($this->settings['min_purchase_amount']);
        if (floatval($invoice->price) < $min_amount) {
            return ['success' => false, 'message' => 'Minimum alışveriş tutarı karşılanmıyor. Min: ' . number_format($min_amount, 2) . ' ₺'];
        }
        
        // Bonus hesapla (kategori bazlı veya genel)
        $bonus_amount = $this->calculateInvoiceBonus($invoice);
        
        if ($bonus_amount <= 0) {
            return ['success' => false, 'message' => 'Hesaplanan bonus tutarı geçersiz'];
        }
        
        // Aylık limit kontrolü
        if (!$this->checkMonthlyLimit($reference->referrer_id, $bonus_amount)) {
            return ['success' => false, 'message' => 'Aylık bonus limiti aşılıyor'];
        }
        
        // Bonus ver
        $bonus_result = $this->giveReferralBonus(
            $reference->referrer_id,
            $reference->buyer_id,
            $bonus_amount,
            'purchase',
            "Ürün bonusu - {$invoice->category_name}",
            null,
            $invoice_id
        );
        
        if ($bonus_result) {
            // Referans ilişkisindeki toplam bonus miktarını güncelle
            // GÜVENLİK DÜZELTMESİ: SQL Injection açığı kapatıldı
            $this->CI->db->query(
                'UPDATE user_references SET bonus_earned = bonus_earned + ? WHERE id = ?',
                [floatval($bonus_amount), intval($reference->id)]
            );
            
            return ['success' => true, 'message' => 'Referans bonusu başarıyla verildi', 'bonus_amount' => $bonus_amount];
        }
        
        return ['success' => false, 'message' => 'Bonus verme işlemi başarısız'];
    }
    
    /**
     * Birden fazla invoice için toplu bonus işlemi
     * 
     * @param array $invoice_ids Invoice ID'leri
     * @return array İşlem sonuçları
     */
    public function processBulkInvoiceBonus($invoice_ids) {
        $results = [];
        $total_bonus = 0;
        $success_count = 0;
        
        foreach ($invoice_ids as $invoice_id) {
            $result = $this->processInvoiceBonus($invoice_id);
            $results[] = $result;
            
            if ($result['success']) {
                $success_count++;
                $total_bonus += $result['bonus_amount'];
            }
        }
        
        return [
            'success' => $success_count > 0,
            'processed_count' => count($invoice_ids),
            'success_count' => $success_count,
            'total_bonus' => $total_bonus,
            'details' => $results
        ];
    }
    
    /**
     * Shop'taki tüm teslim edilmiş invoice'lar için bonus ver
     * 
     * @param int $shop_id Shop ID
     * @return array İşlem sonucu
     */
    public function processShopBonus($shop_id) {
        // Shop'taki teslim edilmiş invoice'ları al
        $invoices = $this->CI->db->select('id')
                              ->where('shop_id', $shop_id)
                              ->where('isActive', 0) // 0 = teslim edildi
                              ->get('invoice')
                              ->result();
        
        if (empty($invoices)) {
            return ['success' => false, 'message' => 'Teslim edilmiş ürün bulunamadı'];
        }
        
        $invoice_ids = array_column($invoices, 'id');
        return $this->processBulkInvoiceBonus($invoice_ids);
    }
    
    /**
     * Tek invoice için bonus hesaplar (kategori bazlı veya genel)
     * 
     * @param object $invoice Invoice bilgileri (category_id ile birlikte)
     * @return float Bonus tutarı
     */
    private function calculateInvoiceBonus($invoice) {
        $bonus_amount = 0;
        
        // Kategori özel bonus ayarını kontrol et
        $category_bonus = $this->CI->db->where('category_id', $invoice->category_id)
                                      ->where('is_active', 1)
                                      ->get('reference_category_commissions')
                                      ->row();
        
        if ($category_bonus) {
            // Kategori özel bonus var
            // Minimum tutar kontrolü
            if ($invoice->price >= $category_bonus->min_amount) {
                $bonus_amount = ($invoice->price * $category_bonus->bonus_percentage) / 100;
                
                // Max bonus kontrolü
                if ($category_bonus->max_bonus > 0 && $bonus_amount > $category_bonus->max_bonus) {
                    $bonus_amount = $category_bonus->max_bonus;
                }
            }
        } else {
            // Genel bonus oranını kullan
            $bonus_amount = ($invoice->price * $this->settings['purchase_bonus_rate']) / 100;
        }
        
        // İşlem başına maksimum bonus kontrolü (genel ayar)
        if ($this->settings['max_bonus_per_transaction'] > 0 && $bonus_amount > $this->settings['max_bonus_per_transaction']) {
            $bonus_amount = $this->settings['max_bonus_per_transaction'];
        }
        
        return round($bonus_amount, 2);
    }
    

    
    /**
     * Referans bonusu verir
     * 
     * @param int $referrer_id Referans veren kullanıcı ID
     * @param int $referred_id Referans edilen kullanıcı ID
     * @param float $amount Bonus tutarı
     * @param string $type Bonus tipi (register, purchase)
     * @param string $description Açıklama
     * @param int $shop_id Shop ID (opsiyonel)
     * @param int $invoice_id Invoice ID (opsiyonel)
     * @return bool İşlem sonucu
     */
    private function giveReferralBonus($referrer_id, $referred_id, $amount, $type, $description, $shop_id = null, $invoice_id = null) {
        if ($amount <= 0) {
            return false;
        }
        
        $this->CI->db->trans_begin();
        
        try {
            // Referrer kullanıcının mevcut bakiyesini al
            $referrer = $this->CI->db->where('id', $referrer_id)->get('user')->row();
            if (!$referrer) {
                throw new Exception('Referrer kullanıcı bulunamadı');
            }
            
        // Bonus geçmişine kaydet
        $history_data = [
            'referrer_id' => $referrer_id,
            'referred_user_id' => $referred_id,
            'bonus_amount' => $amount,
            'bonus_type' => $type,
            'description' => $description,
            'shop_id' => $shop_id,
            'invoice_id' => $invoice_id,
            'status' => 'paid',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $this->CI->db->insert('reference_bonus_history', $history_data);
            
            // Bakiye türünü belirle ve kullanıcı bakiyesini güncelle
            $balance_field = $this->settings['bonus_balance_type'] == 'withdrawable' ? 'balance2' : 'balance';
            $old_balance = $balance_field == 'balance2' ? $referrer->balance2 : $referrer->balance;
            $new_balance = $old_balance + $amount;
            
            $this->CI->db->where('id', $referrer_id)
                        ->set($balance_field, $new_balance)
                        ->update('user');
            
            // Wallet transaction kaydı
            $transaction_data = [
                'user_id' => $referrer_id,
                'transaction_type' => 'referral_bonus',
                'balance_type' => $this->settings['bonus_balance_type'] == 'withdrawable' ? 'withdrawable' : 'spendable',
                'amount' => $amount,
                'description' => $description,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $old_balance,
                'balance_after_transaction' => $new_balance,
                'related_user_id' => $referred_id
            ];
            
            if ($shop_id) {
                $transaction_data['related_id'] = $shop_id;
            }
            
            $this->CI->db->insert('wallet_transactions', $transaction_data);
            
            // Bildirim gönder
            if (function_exists('sendNotificationSite')) {
                sendNotificationSite(
                    $referrer_id,
                    'Referans Bonusu Aldınız!',
                    number_format($amount, 2) . " ₺ referans bonusu hesabınıza eklendi. {$description}",
                    base_url('client/balance')
                );
            }
            
            $this->CI->db->trans_commit();
            return true;
            
        } catch (Exception $e) {
            $this->CI->db->trans_rollback();
            log_message('error', 'Referral bonus error: ' . $e->getMessage());
            return false;
        }
    }
    

    
    /**
     * Aylık bonus limitini kontrol eder
     * 
     * @param int $referrer_id Referans veren kullanıcı ID
     * @param float $new_bonus_amount Yeni bonus tutarı
     * @return bool Limit aşılıp aşılmadığı
     */
    private function checkMonthlyLimit($referrer_id, $new_bonus_amount) {
        if ($this->settings['max_bonus_per_month'] <= 0) {
            return true; // Limit yok
        }
        
        // Bu ayki toplam bonus
        $current_month_start = date('Y-m-01 00:00:00');
        $current_month_end = date('Y-m-t 23:59:59');
        
        $monthly_total = $this->CI->db->select_sum('bonus_amount')
                                     ->where('referrer_id', intval($referrer_id)) // Type safety
                                     ->where('status', 'paid')
                                     ->where('created_at >=', $current_month_start)
                                     ->where('created_at <=', $current_month_end)
                                     ->get('reference_bonus_history')
                                     ->row()
                                     ->bonus_amount;
        
        $monthly_total = floatval($monthly_total ?: 0);
        $max_monthly = floatval($this->settings['max_bonus_per_month']);
        
        return ($monthly_total + floatval($new_bonus_amount)) <= $max_monthly;
    }
    
    /**
     * Kullanıcının referans bilgilerini getirir
     * 
     * @param int $user_id Kullanıcı ID
     * @return array Referans bilgileri
     */
    public function getUserReferralInfo($user_id = null) {
        $user_id = $user_id ?: $this->user_id;
        if (!$user_id) {
            return false;
        }
        
        $info = [
            'user_id' => $user_id,
            'referral_code' => $this->getUserReferralCode($user_id),
            'referrer_info' => null,
            'referred_users' => [],
            'total_earnings' => 0,
            'this_month_earnings' => 0,
            'total_referred_count' => 0,
            'can_change_referrer' => false
        ];
        
        // Referans veren kişi bilgisi
        $referrer_relation = $this->CI->db->select('ur.*, u.name, u.surname, u.ref_code')
                                         ->from('user_references ur')
                                         ->join('user u', 'ur.referrer_id = u.id')
                                         ->where('ur.buyer_id', $user_id)
                                         ->where('ur.is_active', 1)
                                         ->get()
                                         ->row();
        
        if ($referrer_relation) {
            $info['referrer_info'] = [
                'id' => $referrer_relation->referrer_id,
                'name' => $referrer_relation->name . ' ' . $referrer_relation->surname,
                'ref_code' => $referrer_relation->ref_code,
                'relation_date' => $referrer_relation->created_at,
                'total_bonus_earned' => $referrer_relation->bonus_earned
            ];
        }
        
        // Referans verdiği kişiler
        $referred_users = $this->CI->db->select('ur.*, u.name, u.surname')
                                      ->from('user_references ur')
                                      ->join('user u', 'ur.buyer_id = u.id')
                                      ->where('ur.referrer_id', $user_id)
                                      ->where('ur.is_active', 1)
                                      ->order_by('ur.created_at', 'DESC')
                                      ->get()
                                      ->result();
        
        foreach ($referred_users as $referred) {
            $info['referred_users'][] = [
                'id' => $referred->buyer_id,
                'name' => $referred->name . ' ' . $referred->surname,
                'relation_date' => $referred->created_at,
                'bonus_earned' => $referred->bonus_earned
            ];
        }
        
        $info['total_referred_count'] = count($info['referred_users']);
        
        // Toplam kazanç
        $total_earnings = $this->CI->db->select_sum('bonus_amount')
                                      ->where('referrer_id', $user_id)
                                      ->where('status', 'paid')
                                      ->get('reference_bonus_history')
                                      ->row()
                                      ->bonus_amount;
        
        $info['total_earnings'] = $total_earnings ?: 0;
        
        // Bu ayki kazanç
        $current_month_start = date('Y-m-01 00:00:00');
        $current_month_end = date('Y-m-t 23:59:59');
        
        $monthly_earnings = $this->CI->db->select_sum('bonus_amount')
                                        ->where('referrer_id', $user_id)
                                        ->where('status', 'paid')
                                        ->where('created_at >=', $current_month_start)
                                        ->where('created_at <=', $current_month_end)
                                        ->get('reference_bonus_history')
                                        ->row()
                                        ->bonus_amount;
        
        $info['this_month_earnings'] = $monthly_earnings ?: 0;
        
        // Referans değişikliği yapabilir mi?
        $change_result = $this->canChangeReferrer($user_id);
        $info['can_change_referrer'] = $change_result['can_change'];
        $info['change_referrer_message'] = $change_result['message'];
        
        return $info;
    }
    
    /**
     * Kullanıcının referans bonus geçmişini getirir
     * 
     * @param int $user_id Kullanıcı ID
     * @param int $limit Limit
     * @param int $offset Offset
     * @return array Bonus geçmişi
     */
    public function getReferralHistory($user_id = null, $limit = 50, $offset = 0) {
        $user_id = $user_id ?: $this->user_id;
        if (!$user_id) {
            return [];
        }
        
        return $this->CI->db->select('rbh.*, u.name, u.surname')
                           ->from('reference_bonus_history rbh')
                           ->join('user u', 'rbh.referred_user_id = u.id')
                           ->where('rbh.referrer_id', $user_id)
                           ->order_by('rbh.created_at', 'DESC')
                           ->limit($limit, $offset)
                           ->get()
                           ->result();
    }
    
    /**
     * Referans sistemi istatistiklerini getirir
     * 
     * @param int $user_id Kullanıcı ID
     * @return array İstatistikler
     */
    public function getReferralStats($user_id = null) {
        $user_id = $user_id ?: $this->user_id;
        if (!$user_id) {
            return false;
        }
        
        $stats = [
            'total_referred' => 0,
            'active_referred' => 0,
            'total_bonus_earned' => 0,
            'this_month_bonus' => 0,
            'average_bonus_per_user' => 0,
            'best_month' => ['month' => '', 'amount' => 0],
            'monthly_chart_data' => []
        ];
        
        // Toplam referans sayısı
        $stats['total_referred'] = $this->CI->db->where('referrer_id', $user_id)
                                              ->count_all_results('user_references');
        
        // Aktif referans sayısı
        $stats['active_referred'] = $this->CI->db->where('referrer_id', $user_id)
                                               ->where('is_active', 1)
                                               ->count_all_results('user_references');
        
        // Toplam bonus kazancı
        $total_bonus = $this->CI->db->select_sum('bonus_amount')
                                   ->where('referrer_id', $user_id)
                                   ->where('status', 'paid')
                                   ->get('reference_bonus_history')
                                   ->row()
                                   ->bonus_amount;
        
        $stats['total_bonus_earned'] = $total_bonus ?: 0;
        
        // Bu ayki bonus
        $current_month_start = date('Y-m-01 00:00:00');
        $current_month_end = date('Y-m-t 23:59:59');
        
        $monthly_bonus = $this->CI->db->select_sum('bonus_amount')
                                     ->where('referrer_id', $user_id)
                                     ->where('status', 'paid')
                                     ->where('created_at >=', $current_month_start)
                                     ->where('created_at <=', $current_month_end)
                                     ->get('reference_bonus_history')
                                     ->row()
                                     ->bonus_amount;
        
        $stats['this_month_bonus'] = $monthly_bonus ?: 0;
        
        // Kullanıcı başına ortalama bonus
        if ($stats['active_referred'] > 0) {
            $stats['average_bonus_per_user'] = $stats['total_bonus_earned'] / $stats['active_referred'];
        }
        
        // Son 12 ayın verisi (grafik için)
        for ($i = 11; $i >= 0; $i--) {
            $month_start = date('Y-m-01 00:00:00', strtotime("-$i month"));
            $month_end = date('Y-m-t 23:59:59', strtotime("-$i month"));
            
            $month_bonus = $this->CI->db->select_sum('bonus_amount')
                                       ->where('referrer_id', $user_id)
                                       ->where('status', 'paid')
                                       ->where('created_at >=', $month_start)
                                       ->where('created_at <=', $month_end)
                                       ->get('reference_bonus_history')
                                       ->row()
                                       ->bonus_amount;
            
            $month_bonus = $month_bonus ?: 0;
            $month_name = date('M Y', strtotime("-$i month"));
            
            $stats['monthly_chart_data'][] = [
                'month' => $month_name,
                'amount' => $month_bonus
            ];
            
            // En iyi ay
            if ($month_bonus > $stats['best_month']['amount']) {
                $stats['best_month'] = [
                    'month' => $month_name,
                    'amount' => $month_bonus
                ];
            }
        }
        
        return $stats;
    }
    
    /**
     * Ayarları getirir
     * 
     * @return array Referral ayarları
     */
    public function getSettings() {
        return $this->settings;
    }
    
    /**
     * Referans linkini oluşturur
     * 
     * @param int $user_id Kullanıcı ID
     * @param string $page Hedef sayfa (opsiyonel)
     * @return string|false Referans linki
     */
    public function getReferralLink($user_id = null, $page = 'hesap') {
        $ref_code = $this->getUserReferralCode($user_id);
        if (!$ref_code) {
            return false;
        }
        
        $base_url = rtrim(base_url(), '/');
        $page = $page ? '/' . ltrim($page, '/') : '';
        
        return $base_url . $page . '?ref_code=' . $ref_code;
    }

    /**
     * Kayıt bonusu verir
     * 
     * @param int $referrer_id Referans veren kullanıcı ID
     * @param int $referred_id Referans edilen kullanıcı ID
     * @return bool İşlem sonucu
     */
    public function giveRegistrationBonus($referrer_id, $referred_id) {
        if (!$this->isSystemEnabled()) {
            return false;
        }
        
        $register_bonus = floatval($this->settings['register_bonus']);
        if ($register_bonus <= 0) {
            return false;
        }
        
        // Daha önce bu kullanıcı için kayıt bonusu verilmiş mi kontrol et
        $existing_bonus = $this->CI->db->where('referrer_id', $referrer_id)
                                      ->where('referred_user_id', $referred_id)
                                      ->where('bonus_type', 'register')
                                      ->where('status', 'paid')
                                      ->get('reference_bonus_history')
                                      ->row();
        
        if ($existing_bonus) {
            return false; // Zaten verilmiş
        }
        
        // Referred kullanıcının bilgilerini al
        $referred_user = $this->CI->db->where('id', $referred_id)->get('user')->row();
        if (!$referred_user) {
            return false;
        }
        
        $description = "Kayıt bonusu - {$referred_user->name} {$referred_user->surname} kullanıcısının kaydı";
        
        return $this->giveReferralBonus(
            $referrer_id,
            $referred_id,
            $register_bonus,
            'register',
            $description
        );
    }

    /**
     * İlk alışveriş sonrası kayıt bonusu kontrolü
     * 
     * @param int $referred_id Referans edilen kullanıcı ID
     * @return bool İşlem sonucu
     */
    public function processFirstPurchaseRegistrationBonus($referred_id) {
        if (!$this->isSystemEnabled()) {
            return false;
        }
        
        // Referans ayarlarını kontrol et
        $require_purchase = $this->settings['require_purchase'] == '1';
        if (!$require_purchase) {
            return false; // Kayıt bonusu zaten anında verilmiş olmalı
        }
        
        $register_bonus = floatval($this->settings['register_bonus']);
        if ($register_bonus <= 0) {
            return false;
        }
        
        // Bu kullanıcının referans ilişkisini bul
        $reference = $this->CI->db->where('buyer_id', $referred_id)
                                 ->where('is_active', 1)
                                 ->get('user_references')
                                 ->row();
        
        if (!$reference) {
            return false; // Referans ilişkisi yok
        }
        
        // Daha önce kayıt bonusu verilmiş mi kontrol et
        $existing_bonus = $this->CI->db->where('referrer_id', $reference->referrer_id)
                                      ->where('referred_user_id', $referred_id)
                                      ->where('bonus_type', 'register')
                                      ->where('status', 'paid')
                                      ->get('reference_bonus_history')
                                      ->row();
        
        if ($existing_bonus) {
            return false; // Zaten verilmiş
        }
        
        // Bu kullanıcının ilk alışverişi mi kontrol et
        $purchase_count = $this->CI->db->where('user_id', $referred_id)
                                      ->where('status', 0) // Başarılı işlemler
                                      ->where('type !=', 'deposit') // Bakiye yükleme hariç
                                      ->count_all_results('shop');
        
        if ($purchase_count != 1) {
            return false; // İlk alışveriş değil
        }
        
        // Kayıt bonusunu ver
        return $this->giveRegistrationBonus($reference->referrer_id, $referred_id);
    }
} 