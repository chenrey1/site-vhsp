<?php
// models/M_Payment.php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ödeme İşlemleri Modeli
 * 
 * Bu model, tüm ödeme işlemleri, bakiye yükleme, ürün satın alma, 
 * komisyon hesaplama ve fatura oluşturma işlemlerini yönetir.
 */
class M_Payment extends CI_Model {

    /**
     * Sınıf özellikleri
     */
    private $commission_rate;
    private $properties;
    private $api_settings;

    /**
     * Kurucu metod
     */
    public function __construct() {
        parent::__construct();
        $this->properties = $this->db->where('id', 1)->get('properties')->row();
        $this->load->helper('api');
    }

    /**
     * Yeni sipariş oluşturur
     * 
     * @param int $user_id Kullanıcı ID
     * @param string $encode JSON formatında ürün bilgileri
     * @param float $price Toplam fiyat
     * @param string $type Ödeme tipi (deposit, credit_card, balance)
     * @param float $withOutCommission Komisyonsuz fiyat (opsiyonel)
     * @param int|null $coupon Kupon ID (opsiyonel)
     * @param int|null $payment_method_id Ödeme yöntemi ID (opsiyonel)
     * @return string|bool Başarılı ise sipariş ID, başarısız ise false
     */
    public function addShop($user_id, $encode, $price, $type, $withOutCommission = 0, $coupon = null, $payment_method_id = null) {
        // SQL Injection önlemi
        $user_id = intval($user_id);
        $price = floatval($price);
        $withOutCommission = floatval($withOutCommission);
        $type = $this->db->escape_str($type);
        $coupon = $coupon !== null ? intval($coupon) : null;
        $payment_method_id = $payment_method_id !== null ? intval($payment_method_id) : null;
        $this->load->helper('api');
        $randString = $this->generateUniqueOrderId();
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $payment_commission = $this->calculateCommission($type, $price, getCommission($user_id, $payment_method_id));
        $invoice_provider = $this->db->where(['api_name' => 'billing', 'setting_key' => 'provider'])
                                    ->get('api_settings')
                                    ->row()
                                    ->setting_value;

        // Varsayılan ödeme yöntemini kullan eğer belirtilmemişse
        if ($payment_method_id === null) {
            $payment = $this->db->where('is_default', 1)->where('status', 1)->get('payment')->row();
            if (!$payment) {
                $payment = $this->db->where('status', 1)->get('payment')->row();
            }
            $payment_method_id = $payment ? $payment->id : 1;
        }

        $data = [
            'price' => $price,
            'date' => date('Y-m-d H:i:s'),
            'status' => 1,
            'order_id' => $randString,
            'user_id' => $user_id,
            'product' => $encode,
            'ip_address' => getUserIp(),
            'type' => $type,
            'coupon' => $coupon,
            'invoice_provider' => $invoice_provider,
            'payment_commission' => $payment_commission,
            'payment_method_id' => $payment_method_id,
            'balance' => $this->db->where('id', $user_id)->get('user')->row()->balance
        ];

        if ($this->db->insert('shop', $data)) {
            if (isset($coupon) && !empty($coupon)) {
                $this->updateCouponUsage($coupon);
            }
            return $randString;
        } else {
            addlog('M_Payment::addShop', 'Insert failed for order_id: ' . $randString);
            return false;
        }
    }

    /**
     * Sepet içeriğindeki toplam tutarı hesaplar
     * 
     * @param string $encode JSON formatında ürün bilgileri
     * @return float Toplam tutar
     */
    public function calculate($encode) {
        $decode = json_decode($encode, true);
        return array_reduce($decode, function($amount, $d) {
            return $amount + $d['price'] * $d['qty'];
        }, 0);
    }

    /**
     * Bakiye yükleme işlemini onaylar
     * 
     * @param int $shop_id Sipariş ID
     * @return bool İşlem başarılı ise true, değilse false
     */
    public function confirmShopForBalance($shop_id) {
        $this->load->helper('api');
        
        // Sipariş bilgilerini al
        $shop = $this->db->where('id', $shop_id)->get('shop')->row();
        if (!$shop) {
            addlog('M_Payment::confirmShopForBalance', 'Shop not found with id: ' . $shop_id);
            return false;
        }

        // Kullanıcı bilgilerini al
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        if (!$user) {
            addlog('M_Payment::confirmShopForBalance', 'User not found with id: ' . $shop->user_id);
            return false;
        }

        // Transaction başlat
        $this->db->trans_begin();

        try {
            // Siparişi onayla
            $this->db->where('id', $shop_id)->update('shop', ['status' => 0]);

            // Kullanıcı bakiyesini güncelle
            $newBalance = $this->updateUserBalance($user, $shop);

            // Kupon kullanımını güncelle
            if (isset($shop->coupon) && !empty($shop->coupon)) {
                $this->updateCouponUsage($shop->coupon);
            }

            // Fatura oluştur
            createInvoiceForBalance($user, $shop);

            // Siparişi güncelle ve kullanıcının bakiyesini güncelle
            $this->db->where('id', $shop_id)->update('shop', [
                'balance' => $user->balance,
                'new_balance' => $newBalance
            ]);
            $this->db->where('id', $user->id)->update('user', ['balance' => $newBalance]);

            // Transaction'ı kontrol et
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                addlog('M_Payment::confirmShopForBalance', 'Transaction failed for shop id: ' . $shop_id);
                return false;
            }

            // Transaction'ı tamamla
            $this->db->trans_commit();

            // Bakiye yükleme başarılı maili gönder
            $this->sendBalanceSuccessEmail($user, $shop, $newBalance);

            // Bildirimler gönder
            $this->sendNotifications($shop->id);

            // Kazanç kaydı ekle
            $this->addEarningsRecord($shop_id, 'deposit');

            // Kullanıcı tasarruf kaydı ekle (eğer aboneliği varsa)
            $this->handleUserSavings($shop, $user);

            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            addlog('M_Payment::confirmShopForBalance', 'Exception: ' . $e->getMessage() . ' for shop id: ' . $shop_id);
            return false;
        }
    }

    /**
     * Sepet satın alma işlemini onaylar
     * 
     * @param int $shop_id Sipariş ID
     * @return bool İşlem başarılı ise true, değilse false
     */
    public function confirmShopForCart($shop_id) {
        $this->load->helper('api');
        
        // Sipariş bilgilerini al
        $shop = $this->db->where('id', $shop_id)->get('shop')->row();
        if (!$shop) {
            addlog('M_Payment::confirmShopForCart', 'Shop not found with id: ' . $shop_id);
            return false;
        }

        // Kullanıcı bilgilerini al
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        if (!$user) {
            addlog('M_Payment::confirmShopForCart', 'User not found with id: ' . $shop->user_id);
            return false;
        }

        // Sepet içeriğini al
        $cart = json_decode($shop->product, true);
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $api_settings = getAPIsettings();
        $productDetail = [];

        // Transaction başlat
        $this->db->trans_begin();

        try {
            // Kullanıcının bakiyesini güncelle
            $userBalance = $user->balance;
            $newBalance = $this->updateUserBalance($user, $shop);
            
            // Satın alım için bakiye çıkış kaydını oluştur
            if ($shop->type != 'deposit' && $shop->type != 'balance') {
                $this->createPurchaseTransaction($user, $shop, $userBalance, $newBalance);
            }

            // Transaction'ı kontrol et
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                addlog('M_Payment::confirmShopForCart', 'Transaction failed during balance update for shop id: ' . $shop_id);
                return false;
            }

            // Transaction'ı tamamla
            $this->db->trans_commit();

            // Sepetteki her ürünü işle
            foreach ($cart as $c) {
                // Paket kontrolü - eğer name "package_" ile başlıyorsa paket işle
                if (isset($c['name']) && strpos($c['name'], 'package_') === 0) {
                    // Paket ID'sini al
                    $package_id = isset($c['extras']['package_id']) ? intval($c['extras']['package_id']) : intval(str_replace('package_', '', $c['name']));
                    
                    // Paketi getir
                    $package = $this->db->where('id', $package_id)->where('isActive', 1)->get('packages')->row();
                    
                    if ($package) {
                        // Paket içindeki ürünleri getir
                        $package_products = $this->db->select('p.*, pp.quantity, pp.sort_order')
                            ->from('package_products pp')
                            ->join('product p', 'p.id = pp.product_id', 'left')
                            ->where('pp.package_id', $package_id)
                            ->where('p.isActive', 1)
                            ->order_by('pp.sort_order', 'ASC')
                            ->get()
                            ->result();
                        
                        // Paket içindeki her ürünü işle
                        foreach ($package_products as $package_product) {
                            // Paket fiyatını ürünlere orantılı dağıt
                            $total_original_price = 0;
                            foreach ($package_products as $pp) {
                                $total_original_price += $pp->price;
                            }
                            
                            // Ürün fiyatını orantılı hesapla
                            $product_price_ratio = $total_original_price > 0 ? ($package_product->price / $total_original_price) : (1 / count($package_products));
                            $distributed_price = $c['price'] * $product_price_ratio;
                            
                            // Ürün için cart item oluştur
                            $package_cart_item = [
                                'id' => $package_product->id,
                                'product_id' => $package_product->id,
                                'qty' => 1,
                                'price' => $distributed_price,
                                'name' => 'product_' . $package_product->id,
                                'extras' => $c['extras'] ?? []
                            ];
                            
                            // Ürünü işle
                            $qty = 1;
                            while ($qty > 0) {
                                $result = $this->processCartItem($package_cart_item, $user, $shop, $properties, $api_settings, $productDetail);
                                if ($result == "pending") {
                                    $this->finalizeShopCart($shop_id, $user, $shop, $productDetail, 'Satın alım tamamlandı. Ürünlerin en kısa sürede teslim edilecek.');
                                } elseif ($result == "success") {
                                    $product = $this->db->where('id', $package_product->id)->get('product')->row();
                                    $this->finalizeShopCart($shop_id, $user, $shop, $productDetail, 'Satın alım tamamlandı.' . $product->name . ' Adlı ürünün teslim edildi. Değerlendirmeyi unutma!');
                                }
                                $qty--;
                            }
                        }
                    }
                } else {
                    // Normal ürün işleme
                    $product = $this->db->where('id', $c['product_id'])->get('product')->row();
                    $qty = $c['qty'];

                    // Qty kadar ürün satın alınıyor
                    while ($qty > 0) {
                        $result = $this->processCartItem($c, $user, $shop, $properties, $api_settings, $productDetail);
                        if ($result == "pending") {
                            $this->finalizeShopCart($shop_id, $user, $shop, $productDetail, 'Satın alım tamamlandı. Ürünlerin en kısa sürede teslim edilecek.');
                        } elseif ($result == "success") {
                            $product = $this->db->where('id', $c['product_id'])->get('product')->row();
                            $this->finalizeShopCart($shop_id, $user, $shop, $productDetail, 'Satın alım tamamlandı.' . $product->name . ' Adlı ürünün teslim edildi. Değerlendirmeyi unutma!');
                        }
                        $qty--;
                    }
                }
            }

            // Kullanıcıya sipariş oluştu bildirimleri gönder
            $this->sendNotifications($shop_id);

            // Aboneliği kontrol et ve gerekli aksiyonu al
            $this->checkSubscription($user, $shop);

            // Ürün satışı için kazanç kaydı ekle
            $this->addEarningsRecord($shop_id, 'product_sale');

            // Bayilik alım miktarını güncelle
            $this->updateDealerPurchase($user, $shop);

            // Kullanıcı tasarruf kaydını ekle
            $this->addUserSavingsForCommission($user, $shop);

            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            addlog('M_Payment::confirmShopForCart', 'Exception: ' . $e->getMessage() . ' for shop id: ' . $shop_id);
            return false;
        }
    }

    /**
     * Benzersiz sipariş ID'si oluşturur
     * 
     * @return string Benzersiz sipariş ID'si
     */
    private function generateUniqueOrderId() {
        $randString = randString(20);
        while ($this->db->where('order_id', $randString)->get('shop')->row()) {
            $randString = randString(25);
        }
        return $randString;
    }

    /**
     * Komisyon hesaplar
     * 
     * @param string $type Ödeme tipi
     * @param float $withOutCommission Komisyonsuz fiyat
     * @param float $commissionRate Komisyon oranı
     * @return float Komisyon tutarı
     */
    private function calculateCommission($type, $withOutCommission, $commissionRate) {
        if ($type == 'credit_card' || $type == 'deposit') {
            return number_format(($withOutCommission * $commissionRate) / 100, 2, '.', '');
        }
        return 0;
    }

    /**
     * Kupon kullanımını günceller
     * 
     * @param int $coupon_id Kupon ID
     */
    private function updateCouponUsage($coupon_id) {
        $coupon = $this->db->where('id', $coupon_id)->get("coupons")->row();
        if ($coupon) {
            $used_by = json_decode($coupon->used_by ?? "[]", true);
            $used_by[] = $this->session->userdata('info')['id'];
            $this->db->where('id', $coupon_id)->update('coupons', ['used_by' => json_encode($used_by)]);
        }
    }

    /**
     * Kullanıcı bakiyesini günceller
     * 
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @return float Yeni bakiye
     */
    private function updateUserBalance($user, $shop) {
        $newBalance = $user->balance;

        // İşlem tipine göre bakiye güncelleme
        if ($shop->type == 'deposit') {
            // Bakiye yüklemede para eklenir
            $newBalance = $user->balance + $shop->price;
            
            // Bakiye yükleme işlem kaydını oluştur
            $transaction_data = [
                'user_id' => $user->id,
                'transaction_type' => 'transfer_in',
                'amount' => $shop->price,
                'description' => 'Bakiye yükleme',
                'status' => 1, // Onaylı
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance, // İşlem öncesi bakiye
                'balance_after_transaction' => $newBalance // Güncellenmiş bakiye
            ];
            
            $this->db->insert('wallet_transactions', $transaction_data);
            
            // Sadece bakiye yükleme işleminde veritabanını güncelle
            $this->db->where('id', $user->id)->update('user', ['balance' => $newBalance]);
            
            // Bakiye yükleme işleminde referans bonusu VERİLMEZ
            addlog('M_Payment::updateUserBalance', 'Bakiye yükleme işlemi tamamlandı. User ID: '.$user->id.', Eski bakiye: '.$user->balance.', Yeni bakiye: '.$newBalance);
        } else if ($shop->type == 'balance') {
            // Bakiye ile ürün satın alımında bakiye azaltılır
            $newBalance = $user->balance - $shop->price;
            
            // Bakiye ile satın alma işlem kaydını oluştur
            $transaction_data = [
                'user_id' => $user->id,
                'transaction_type' => 'purchase',
                'amount' => -$shop->price,
                'description' => 'Bakiye ile ürün satın alımı - Sipariş No: ' . $shop->id,
                'status' => 1, // Onaylı
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance, // İşlem öncesi bakiye
                'balance_after_transaction' => $newBalance, // Güncellenmiş bakiye
                'related_id' => $shop->id
            ];
            
            $this->db->insert('wallet_transactions', $transaction_data);

            // Bakiyeyi güncelle
            $this->db->where('id', $user->id)->update('user', ['balance' => $newBalance]);
            
            // Güncel bakiyeyi logla
            addlog('M_Payment::updateUserBalance', 'Bakiye ile satın alma işlemi. User ID: '.$user->id.', Eski bakiye: '.$user->balance.', Yeni bakiye: '.$newBalance);
        } else if ($shop->type == 'credit_card') {
            // Kredi kartı ile alımlarda bakiyeden düşüm yapılmamalı
            // İşlem kaydı sadece izleme amaçlı oluşturulmalı
            $transaction_data = [
                'user_id' => $user->id,
                'transaction_type' => 'purchase',
                'amount' => $shop->price, // Bakiye etkilemediği için 0
                'description' => 'Kredi kartı ile ürün satın alımı - Sipariş No: ' . $shop->id,
                'status' => 1, // Onaylı
                'payment_method' => 'credit_card',
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance, // İşlem öncesi bakiye
                'balance_after_transaction' => $user->balance, // Bakiye değişmedi
                'related_id' => $shop->id
            ];
            
            $this->db->insert('wallet_transactions', $transaction_data);
            
            // Bakiye değişmeyecek
            $newBalance = $user->balance;
            
            // Log ekle
            addlog('M_Payment::updateUserBalance', 'Kredi kartı ile satın alma işlemi. User ID: '.$user->id.', Bakiye etkilenmedi: '.$newBalance);
        } else {
            // Diğer tipteki işlemler için bakiye değişimi yok
            $newBalance = $user->balance;
        }
        
        return $newBalance;
    }

    /**
     * İnvoice için referans bonusu işlemi
     * 
     * @param object $user Kullanıcı bilgileri
     * @param object $invoice Fatura bilgileri
     * @param object $shop Shop bilgileri
     * @return bool İşlem başarılı ise true
     */
    public function processInvoiceReferralBonus($user, $invoice, $shop) {
        addlog('M_Payment::processInvoiceReferralBonus', 'Referans bonus işlemi başlatıldı. Kullanıcı: ' . $user->name . ' ' . $user->surname . ' (ID: ' . $user->id . '), Fatura: ' . $invoice->id . ', Fiyat: ' . $invoice->price . ' TL');
        
        // Referans sistemi aktif mi kontrol et
        $ref_settings = $this->db->where('key', 'referral_system_enabled')->get('settings')->row();
        if (!$ref_settings || $ref_settings->value != '1') {
            addlog('M_Payment::processInvoiceReferralBonus', 'Referans sistemi aktif değil. İşlem iptal edildi.');
            return false;
        }
        addlog('M_Payment::processInvoiceReferralBonus', 'Referans sistemi aktif durumda.');

        // Kullanıcının referansı var mı kontrol et
        $user_refs = $this->db->where("buyer_id", $user->id)->get("user_references")->row();
        if (!$user_refs) {
            addlog('M_Payment::processInvoiceReferralBonus', 'Kullanıcının referansı bulunamadı. İşlem iptal edildi.');
            return false;
        }
        addlog('M_Payment::processInvoiceReferralBonus', 'Kullanıcının referansı bulundu. Referans veren: ' . $user_refs->referrer_id);

        // İlk alışveriş kontrolü ve kayıt bonusu ver (referral_require_purchase=1 ise)
        $this->load->library('Referral_System');
        $registration_bonus_result = $this->referral_system->processFirstPurchaseRegistrationBonus($user->id);
        if ($registration_bonus_result) {
            addlog('M_Payment::processInvoiceReferralBonus', 'İlk alışveriş kayıt bonusu verildi. Kullanıcı: ' . $user->id);
        }

        // Ürün ve kategori bilgilerini al
        addlog('M_Payment::processInvoiceReferralBonus', 'Ürün bilgileri alınıyor. Ürün ID: ' . $invoice->product_id);
        $product = $this->db->select('p.*, c.name as category_name')
                           ->from('product p')
                           ->join('category c', 'p.category_id = c.id', 'left')
                           ->where('p.id', $invoice->product_id)
                           ->get()
                           ->row();

        if (!$product) {
            addlog('M_Payment::processInvoiceReferralBonus', 'Ürün bulunamadı. Fatura ID: ' . $invoice->id . ', Ürün ID: ' . $invoice->product_id);
            return false;
        }
        addlog('M_Payment::processInvoiceReferralBonus', 'Ürün bulundu: ' . $product->name . ' (Kategori: ' . $product->category_name . ', Kategori ID: ' . $product->category_id . ')');

        // Kategoriye özel bonus ayarını kontrol et
        addlog('M_Payment::processInvoiceReferralBonus', 'Kategoriye özel bonus ayarları kontrol ediliyor...');
        $category_bonus = $this->db->where('category_id', $product->category_id)
                                  ->where('is_active', 1)
                                  ->get('reference_category_commissions')
                                  ->row();

        $bonus_amount = 0;
        $bonus_source = 'genel'; // 'genel' veya 'kategori'

        if ($category_bonus) {
            addlog('M_Payment::processInvoiceReferralBonus', 'Kategoriye özel bonus ayarı bulundu. Bonus oranı: %' . $category_bonus->bonus_percentage . ', Min tutar: ' . $category_bonus->min_amount . ' TL, Max bonus: ' . ($category_bonus->max_bonus ?: 'Sınırsız'));
            
            // Kategoriye özel bonus var
            if ($invoice->price >= $category_bonus->min_amount) {
                $bonus_amount = ($invoice->price * $category_bonus->bonus_percentage) / 100;
                $bonus_source = 'kategori';
                
                addlog('M_Payment::processInvoiceReferralBonus', 'Kategori bonus hesaplandı: ' . $invoice->price . ' TL x %' . $category_bonus->bonus_percentage . ' = ' . $bonus_amount . ' TL');
                
                // Kategori maksimum bonus kontrolü
                if ($category_bonus->max_bonus > 0 && $bonus_amount > $category_bonus->max_bonus) {
                    addlog('M_Payment::processInvoiceReferralBonus', 'Kategori maksimum bonus limitine takıldı. ' . $bonus_amount . ' TL -> ' . $category_bonus->max_bonus . ' TL');
                    $bonus_amount = $category_bonus->max_bonus;
                }
                
                addlog('M_Payment::processInvoiceReferralBonus', 'Kategoriye özel bonus uygulandı. Ürün: ' . $product->name . ', Kategori: ' . $product->category_name . ', Oran: %' . $category_bonus->bonus_percentage . ', Final bonus: ' . $bonus_amount . ' TL');
            } else {
                addlog('M_Payment::processInvoiceReferralBonus', 'Fatura tutarı kategori minimum tutarının altında. Fatura: ' . $invoice->price . ' TL < Minimum: ' . $category_bonus->min_amount . ' TL');
                return false;
            }
        } else {
            addlog('M_Payment::processInvoiceReferralBonus', 'Kategoriye özel bonus ayarı bulunamadı. Genel bonus ayarları kullanılacak.');
            // Genel bonus ayarlarını al (M_Settings kullanarak varsayılan değerlerle)
            $this->load->model('M_Settings');
            $purchase_bonus_rate_value = $this->M_Settings->getSettingValue('referral_purchase_bonus_rate', '5.00');
            $min_purchase_amount_value = $this->M_Settings->getSettingValue('referral_min_purchase_amount', '0.00');

            addlog('M_Payment::processInvoiceReferralBonus', 'Genel bonus ayarları alınıyor. Bonus oranı: ' . $purchase_bonus_rate_value . '%, Min tutar: ' . $min_purchase_amount_value . ' TL');

            // Minimum alım tutarını kontrol et (genel ayar)
            if (floatval($min_purchase_amount_value) > 0 && $invoice->price < floatval($min_purchase_amount_value)) {
                addlog('M_Payment::processInvoiceReferralBonus', 'Fatura tutarı genel minimum tutarının altında. Fatura: ' . $invoice->price . ' TL < Minimum: ' . $min_purchase_amount_value . ' TL');
                return false;
            }

            // Genel bonus oranını kontrol et
            if (floatval($purchase_bonus_rate_value) <= 0) {
                addlog('M_Payment::processInvoiceReferralBonus', 'Genel bonus oranı sıfır veya ayarlanmamış. Oran: ' . $purchase_bonus_rate_value);
                return false;
            }

            // Genel bonus hesapla
            $bonus_amount = ($invoice->price * floatval($purchase_bonus_rate_value)) / 100;
            addlog('M_Payment::processInvoiceReferralBonus', 'Genel bonus hesaplandı: ' . $invoice->price . ' TL x %' . $purchase_bonus_rate_value . ' = ' . $bonus_amount . ' TL');
        }

        if ($bonus_amount <= 0) {
            addlog('M_Payment::processInvoiceReferralBonus', 'Hesaplanan bonus tutarı sıfır veya negatif: ' . $bonus_amount . ' TL. İşlem iptal edildi.');
            return false;
        }
        
        addlog('M_Payment::processInvoiceReferralBonus', 'Bonus hesaplaması tamamlandı. Kaynak: ' . $bonus_source . ', Tutar: ' . $bonus_amount . ' TL');

        $referrer_id = $user_refs->referrer_id;
        addlog('M_Payment::processInvoiceReferralBonus', 'Referans veren kullanıcı kontrol ediliyor. Referrer ID: ' . $referrer_id);

        // Referans veren kullanıcıyı al
        $referrer = $this->db->where('id', $referrer_id)->get('user')->row();
        if (!$referrer) {
            addlog('M_Payment::processInvoiceReferralBonus', 'Referans veren kullanıcı bulunamadı. Referrer ID: ' . $referrer_id);
            return false;
        }
        addlog('M_Payment::processInvoiceReferralBonus', 'Referans veren kullanıcı bulundu: ' . $referrer->name . ' ' . $referrer->surname . ' (Mevcut bakiye: ' . $referrer->balance . ' TL, Çekilebilir bakiye: ' . ($referrer->balance2 ?? '0') . ' TL)');

        // Genel maksimum bonus kontrollerini al
        addlog('M_Payment::processInvoiceReferralBonus', 'Maksimum bonus limit kontrolleri yapılıyor...');
        $max_bonus_per_transaction = $this->db->where('key', 'referral_max_bonus_per_transaction')->get('settings')->row();
        $max_bonus_per_month = $this->db->where('key', 'referral_max_bonus_per_month')->get('settings')->row();

        addlog('M_Payment::processInvoiceReferralBonus', 'Limit ayarları - İşlem başına: ' . ($max_bonus_per_transaction ? $max_bonus_per_transaction->value . ' TL' : 'Sınırsız') . ', Aylık: ' . ($max_bonus_per_month ? $max_bonus_per_month->value . ' TL' : 'Sınırsız'));

        // İşlem başına maksimum bonus kontrolü (genel ayar)
        if ($max_bonus_per_transaction && $bonus_amount > floatval($max_bonus_per_transaction->value)) {
            $original_bonus = $bonus_amount;
            $bonus_amount = floatval($max_bonus_per_transaction->value);
            addlog('M_Payment::processInvoiceReferralBonus', 'İşlem başına maksimum bonus limitine takıldı. ' . $original_bonus . ' TL -> ' . $bonus_amount . ' TL');
        }

        // Aylık maksimum bonus kontrolü (genel ayar)
        if ($max_bonus_per_month) {
            $monthly_limit = floatval($max_bonus_per_month->value);
            $current_month_start = date('Y-m-01 00:00:00');
            $current_month_end = date('Y-m-t 23:59:59');
            
            addlog('M_Payment::processInvoiceReferralBonus', 'Aylık bonus limiti kontrol ediliyor. Bu ay aralığı: ' . $current_month_start . ' - ' . $current_month_end);
            
            $this_month_bonus = $this->db
                ->where('referrer_id', $referrer_id)
                ->where('bonus_type', 'purchase')
                ->where('status', 'paid')
                ->where('created_at >=', $current_month_start)
                ->where('created_at <=', $current_month_end)
                ->select_sum('bonus_amount')
                ->get('reference_bonus_history')
                ->row()
                ->bonus_amount ?? 0;

            addlog('M_Payment::processInvoiceReferralBonus', 'Bu ay alınan toplam bonus: ' . $this_month_bonus . ' TL, Aylık limit: ' . $monthly_limit . ' TL, Yeni bonus: ' . $bonus_amount . ' TL');

            if (($this_month_bonus + $bonus_amount) > $monthly_limit) {
                $original_bonus = $bonus_amount;
                $bonus_amount = max(0, $monthly_limit - $this_month_bonus);
                if ($bonus_amount <= 0) {
                    addlog('M_Payment::processInvoiceReferralBonus', 'Aylık bonus limitine ulaşıldı. Referrer: ' . $referrer_id . ', Limit: ' . $monthly_limit . ' TL, Bu ay toplam: ' . $this_month_bonus . ' TL. İşlem iptal edildi.');
                    return false;
                }
                addlog('M_Payment::processInvoiceReferralBonus', 'Aylık bonus limitine takıldı. ' . $original_bonus . ' TL -> ' . $bonus_amount . ' TL (Kalan limit: ' . ($monthly_limit - $this_month_bonus) . ' TL)');
            }
        }

        // Referans bonusunun hangi bakiye türüne yükleneceğini kontrol et
        addlog('M_Payment::processInvoiceReferralBonus', 'Bakiye türü ayarı kontrol ediliyor...');
        $balance_type_setting = $this->db->where('key', 'referral_bonus_balance_type')->get('settings')->row();
        $balance_type = $balance_type_setting ? $balance_type_setting->value : 'withdrawable'; // varsayılan: withdrawable
        
        addlog('M_Payment::processInvoiceReferralBonus', 'Bonus bakiye türü belirlendi: ' . $balance_type . ' (' . ($balance_type === 'withdrawable' ? 'Çekilebilir bakiye (balance2)' : 'Harcayabilir bakiye (balance)') . ')');

        // Transaction başlat
        addlog('M_Payment::processInvoiceReferralBonus', 'Veritabanı transaction başlatılıyor...');
        $this->db->trans_begin();

        try {
            // Bakiye türüne göre güncelleme yap
            if ($balance_type === 'withdrawable') {
                // balance2 (çekilebilir bakiye) güncelle
                $current_balance2 = $referrer->balance2 ?? 0;
                $new_balance2 = $current_balance2 + $bonus_amount;
                
                addlog('M_Payment::processInvoiceReferralBonus', 'Çekilebilir bakiye güncellenecek. Önceki: ' . $current_balance2 . ' TL, Bonus: ' . $bonus_amount . ' TL, Sonrası: ' . $new_balance2 . ' TL');
                
                $this->db->where('id', $referrer_id)->update('user', ['balance2' => $new_balance2]);
                
                $balance_before = $current_balance2;
                $balance_after = $new_balance2;
                $wallet_balance_type = 'withdrawable';
                
                addlog('M_Payment::processInvoiceReferralBonus', 'Çekilebilir bakiye başarıyla güncellendi. Referrer: ' . $referrer_id . ', Bonus: ' . $bonus_amount . ' TL, Yeni balance2: ' . $new_balance2 . ' TL');
            } else {
                // balance (harcayabilir bakiye) güncelle - varsayılan
                $new_referrer_balance = $referrer->balance + $bonus_amount;
                
                addlog('M_Payment::processInvoiceReferralBonus', 'Harcayabilir bakiye güncellenecek. Önceki: ' . $referrer->balance . ' TL, Bonus: ' . $bonus_amount . ' TL, Sonrası: ' . $new_referrer_balance . ' TL');
                
                $this->db->where('id', $referrer_id)->update('user', ['balance' => $new_referrer_balance]);
                
                $balance_before = $referrer->balance;
                $balance_after = $new_referrer_balance;
                $wallet_balance_type = 'spendable';
                
                addlog('M_Payment::processInvoiceReferralBonus', 'Harcayabilir bakiye başarıyla güncellendi. Referrer: ' . $referrer_id . ', Bonus: ' . $bonus_amount . ' TL, Yeni balance: ' . $new_referrer_balance . ' TL');
            }

            // Wallet transaction kaydı oluştur
            addlog('M_Payment::processInvoiceReferralBonus', 'Wallet transaction kaydı oluşturuluyor...');
            $transaction_data = [
                'user_id' => $referrer_id,
                'transaction_type' => 'referral_bonus',
                'balance_type' => $wallet_balance_type,
                'amount' => $bonus_amount,
                'description' => 'Alışveriş referans bonusu - ' . $user->name . ' ' . $user->surname . ' (Fatura: ' . $invoice->id . ')',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $balance_before,
                'balance_after_transaction' => $balance_after,
                'related_id' => $invoice->id
            ];
            $this->db->insert('wallet_transactions', $transaction_data);
            addlog('M_Payment::processInvoiceReferralBonus', 'Wallet transaction kaydı oluşturuldu. Tür: ' . $wallet_balance_type . ', Tutar: ' . $bonus_amount . ' TL');

            // Description oluştur (kategoriye özel veya genel)
            addlog('M_Payment::processInvoiceReferralBonus', 'Bonus açıklaması oluşturuluyor. Kaynak: ' . $bonus_source);
            if ($bonus_source == 'kategori') {
                $description = 'Alışveriş bonusu - ' . $product->name . ' (' . $product->category_name . ' kategorisi - %' . $category_bonus->bonus_percentage . ' komisyon)';
            } else {
                $general_rate = $this->db->where('key', 'referral_purchase_bonus_rate')->get('settings')->row();
                $description = 'Alışveriş bonusu - ' . $product->name . ' (Genel oran - %' . ($general_rate ? $general_rate->value : '0') . ' komisyon)';
            }
            addlog('M_Payment::processInvoiceReferralBonus', 'Bonus açıklaması: ' . $description);

            // Reference bonus history kaydı oluştur
            addlog('M_Payment::processInvoiceReferralBonus', 'Referans bonus geçmişi kaydı oluşturuluyor...');
            // Aynı fatura için daha önce bonus ödenmiş mi kontrol et
            $existing_bonus = $this->db->where('invoice_id', $invoice->id)
                                       ->where('status', 'paid')
                                       ->get('reference_bonus_history')
                                       ->row();

            if ($existing_bonus) {
                $this->db->trans_rollback();
                addlog('M_Payment::processInvoiceReferralBonus', 'Bu fatura için daha önce bonus verilmiş. Fatura ID: ' . $invoice->id);
                return false;
            }

            $bonus_history_data = [
                'referrer_id' => $referrer_id,
                'referred_user_id' => $user->id,
                'invoice_id' => $invoice->id,
                'shop_id' => $shop->id,
                'bonus_amount' => $bonus_amount,
                'bonus_type' => 'purchase',
                'description' => $description,
                'status' => 'paid',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('reference_bonus_history', $bonus_history_data);
            addlog('M_Payment::processInvoiceReferralBonus', 'Referans bonus geçmişi kaydı oluşturuldu.');

            // user_references tablosundaki bonus_earned field'ını güncelle (mevcut toplam + yeni bonus)
            $current_bonus = $this->db->where('referrer_id', $referrer_id)->where('buyer_id', $user->id)->get('user_references')->row()->bonus_earned ?? 0;
            $new_total_bonus = $current_bonus + $bonus_amount;
            addlog('M_Payment::processInvoiceReferralBonus', 'Mevcut toplam bonus: ' . $current_bonus . ' TL, Yeni bonus: ' . $bonus_amount . ' TL, Yeni toplam: ' . $new_total_bonus . ' TL');
            
            $this->db->where('referrer_id', $referrer_id)
                    ->where('buyer_id', $user->id)
                    ->set('bonus_earned', $new_total_bonus)
                    ->update('user_references');
            
            addlog('M_Payment::processInvoiceReferralBonus', 'user_references bonus_earned güncellendi. Toplam bonus: ' . $new_total_bonus . ' TL');

            // Transaction'ı kontrol et
            addlog('M_Payment::processInvoiceReferralBonus', 'Transaction durumu kontrol ediliyor...');
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                addlog('M_Payment::processInvoiceReferralBonus', 'Transaction başarısız oldu! Rollback yapıldı. Referrer: ' . $referrer_id . ', Alıcı: ' . $user->id . ', Fatura: ' . $invoice->id . ', Ürün: ' . $product->name . ' (Kaynak: ' . $bonus_source . ')');
                return false;
            }

            // Transaction'ı tamamla
            $this->db->trans_commit();
            addlog('M_Payment::processInvoiceReferralBonus', 'Transaction başarıyla tamamlandı.');

            // Başarılı işlemi logla
            addlog('M_Payment::processInvoiceReferralBonus', 'Referans bonus işlemi başarıyla tamamlandı! Referrer: ' . $referrer_id . ', Alıcı: ' . $user->id . ', Bonus: ' . $bonus_amount . ' TL, Fatura: ' . $invoice->id . ', Kaynak: ' . $bonus_source . ', Bakiye türü: ' . $balance_type);

            // Bildirim mesajı oluştur
            addlog('M_Payment::processInvoiceReferralBonus', 'Bildirim mesajı hazırlanıyor...');
            $balance_text = ($balance_type === 'withdrawable') ? ' çekilebilir bakiyenize' : ' bakiyenize';
            
            if ($bonus_source == 'kategori') {
                $notification_message = $user->name . ' ' . $user->surname . ' adlı kullanıcının ' . $product->name . ' (' . $product->category_name . ' kategorisi) ürün alışverişinden ' . number_format($bonus_amount, 2) . ' TL kategori bonusu' . $balance_text . ' eklendi!';
            } else {
                $notification_message = $user->name . ' ' . $user->surname . ' adlı kullanıcının ' . $product->name . ' ürün alışverişinden ' . number_format($bonus_amount, 2) . ' TL referans bonusu' . $balance_text . ' eklendi!';
            }
            addlog('M_Payment::processInvoiceReferralBonus', 'Bildirim mesajı: ' . $notification_message);

            // Referans veren kullanıcıya bildirim gönder
            addlog('M_Payment::processInvoiceReferralBonus', 'Kullanıcıya bildirim gönderiliyor. Referrer ID: ' . $referrer_id);
            sendNotificationSite(
                $referrer_id, 
                'Referans Bonusu', 
                $notification_message,
                base_url('client/reference')
            );
            addlog('M_Payment::processInvoiceReferralBonus', 'Bildirim başarıyla gönderildi.');

            addlog('M_Payment::processInvoiceReferralBonus', 'Referans bonus işlemi tamamen tamamlandı! İşlem başarılı.');
            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            addlog('M_Payment::processInvoiceReferralBonus', 'HATA! Exception oluştu: ' . $e->getMessage() . ' | Referrer: ' . $referrer_id . ', Alıcı: ' . $user->id . ', Fatura: ' . $invoice->id . ', Ürün: ' . $product->name . ' (Kaynak: ' . $bonus_source . ')');
            return false;
        }
    }

    /**
     * Satın alma işlemi için bakiye çıkış kaydını oluşturur
     * 
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param float $userBalance İşlem öncesi bakiye
     * @param float $newBalance İşlem sonrası bakiye
     */
    private function createPurchaseTransaction($user, $shop, $userBalance, $newBalance) {
        // Kredi kartı ile ödemelerde bakiye kaydı oluşturulmamalı
        if ($shop->type == 'credit_card') {
            return;
        }
        
        $transaction_data = [
            'user_id' => $user->id,
            'transaction_type' => 'purchase',
            'amount' => -$shop->price,
            'description' => 'Ürün satın alımı - Sipariş No: ' . $shop->id,
            'status' => 1, // Onaylı
            'created_at' => date('Y-m-d H:i:s'),
            'balance_before' => $userBalance, // İşlem öncesi bakiye 
            'balance_after_transaction' => $newBalance, // Doğru güncellenmiş bakiye değeri
            'related_id' => $shop->id
        ];
        
        $this->db->insert('wallet_transactions', $transaction_data);
        
        // Satın alma işleminden sonra kullanıcının bakiyesini güncelle
        $this->db->where('id', $user->id)->update('user', ['balance' => $newBalance]);
    }

    /**
     * Sepet öğesini işler
     * 
     * @param array $c Ürün bilgileri
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param object $properties Site özellikleri
     * @param array $api_settings API ayarları
     * @param array &$productDetail Ürün detayları
     * @return string İşlem sonucu (pending, success)
     */
    private function processCartItem($c, $user, $shop, $properties, $api_settings, &$productDetail) {
        $product = $this->db->where('id', $c['product_id'])->get('product')->row();

        if ($product->isStock == 0) {
            $this->createInvoiceForOutOfStockProduct($c, $user, $shop, $properties, $productDetail);
            return "pending";
        } else {
            $stock = $this->db->where('product_id', $c['product_id'])->where('isActive', 1)->get('stock')->row();
            if ($stock) {
                $this->createInvoiceForStockProduct($c, $user, $shop, $properties, $product, $stock, $productDetail);
                return "success";
            } else {
                $this->handleAutoGiveProducts($c, $user, $shop, $properties, $api_settings, $productDetail, $product);
                return "pending";
            }
        }
    }

    /**
     * Stoksuz ürün için fatura oluşturur
     * 
     * @param array $c Ürün bilgileri
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param object $properties Site özellikleri
     * @param array &$productDetail Ürün detayları
     */
    private function createInvoiceForOutOfStockProduct($c, $user, $shop, $properties, &$productDetail) {
        $product = $this->db->where('id', $c['product_id'])->get('product')->row();
        $data = [
            'product_id' => $c['product_id'],
            'extras' => isset($c['extras']) ? $c['extras'] : null,
            'price' => $c['price'],
            'isComment' => 1,
            'isActive' => 1,
            'date' => date('Y-m-d H:i:s'),
            'balance' => $user->balance,
            'new_balance' => $user->balance,
            'shop_id' => $shop->id,
            'seller_id' => $product->seller_id,
            'invoice_provider' => $this->db->where(['api_name' => 'billing', 'setting_key' => 'provider'])->get('api_settings')->row()->setting_value,
            'payment_commission' => ($shop->payment_commission > 0) ? number_format(($c['price'] * getCommission()) / 100, 2, '.', '') : 0
        ];
        $this->db->insert('invoice', $data);
        $inv = $this->db->where('id', $this->db->insert_id())->get('invoice')->row();
        //createInvoiceInAPI($user, $inv);
        $productDetail[] = ['status' => 0, 'product' => $product->name, 'price' => $c['price']];
        
        // Eğer ürün bir pazaryeri ürünü ise (seller_id > 0) satıcı için wallet_transaction kaydı oluştur
        if ($product->seller_id > 0) {
            $this->createSellerWalletTransaction($product->seller_id, $c['price'], $shop->id, $inv->id);
        }
    }

    /**
     * Satıcı için wallet_transaction kaydı oluşturur
     * 
     * @param int $seller_id Satıcı ID
     * @param float $price Ürün fiyatı
     * @param int $shop_id Sipariş ID
     * @param int $invoice_id Fatura ID
     */
    private function createSellerWalletTransaction($seller_id, $price, $shop_id, $invoice_id) {
        $seller = $this->db->where('id', $seller_id)->get('user')->row();
        if (!$seller) {
            addlog('M_Payment::createSellerWalletTransaction', 'Seller not found with id: ' . $seller_id);
            return;
        }
        
        // Satıcının komisyon oranını hesapla
        $percent = ($price / 100) * $seller->shop_com;
        $seller_amount = $price - $percent;
        
        // Satıcı için beklemede olan wallet_transaction kaydını oluştur
        $transaction_data = [
            'user_id' => $seller_id,
            'transaction_type' => 'marketplace',
            'balance_type' => 'withdrawable',
            'amount' => $seller_amount,
            'description' => 'Pazaryeri satışı - Sipariş No: ' . $shop_id . ', Fatura No: ' . $invoice_id,
            'status' => 0, // Beklemede
            'created_at' => date('Y-m-d H:i:s'),
            'balance_before' => $seller->balance2, // Çekilebilir bakiye
            'balance_after_transaction' => $seller->balance2 + $seller_amount, // İşlem sonrası bakiye
            'related_id' => $invoice_id // İlgili fatura ID
        ];
        
        $this->db->insert('wallet_transactions', $transaction_data);
        addlog('M_Payment::createSellerWalletTransaction', 'Seller wallet transaction created for seller: ' . $seller_id . ', amount: ' . $seller_amount . ', invoice: ' . $invoice_id);
    }

    /**
     * Stoklu ürün için fatura oluşturur
     * 
     * @param array $c Ürün bilgileri
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param object $properties Site özellikleri
     * @param object $product Ürün nesnesi
     * @param object $stock Stok nesnesi
     * @param array &$productDetail Ürün detayları
     */
    private function createInvoiceForStockProduct($c, $user, $shop, $properties, $product, $stock, &$productDetail) {
        // Transaction başlat
        $this->db->trans_begin();
        
        try {
            $data = [
                'product' => $stock->product,
                'isActive' => 0,
                'isComment' => 1,
                'price' => $c['price'],
                'date' => date('Y-m-d H:i:s'),
                'balance' => $user->balance,
                'new_balance' => $user->balance,
                'product_id' => $c['product_id'],
                'shop_id' => $shop->id,
                'seller_id' => $product->seller_id,
                'last_refund' => date('Y-m-d H:i:s', strtotime("+1 days")),
                'invoice_provider' => $this->db->where(['api_name' => 'billing', 'setting_key' => 'provider'])->get('api_settings')->row()->setting_value,
                'payment_commission' => ($shop->payment_commission > 0) ? number_format(($c['price'] * getCommission($shop->user_id)) / 100, 2, '.', '') : 0
            ];
            
            $this->db->insert('invoice', $data);
            $invoice_id = $this->db->insert_id();
            
            // Stoğu işaretle
            $this->db->where('id', $stock->id)->update('stock', ['isActive' => 0]);
            
            // Eğer ürün bir pazaryeri ürünü ise (seller_id > 0) satıcı için wallet_transaction kaydı oluştur
            if ($product->seller_id > 0) {
                $this->createSellerWalletTransaction($product->seller_id, $c['price'], $shop->id, $invoice_id);
            }
            
            // Transaction'ı kontrol et
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                addlog('M_Payment::createInvoiceForStockProduct', 'Transaction failed for stock: ' . $stock->id);
                return false;
            }
            
            // Transaction'ı tamamla
            $this->db->trans_commit();
            
            $inv = $this->db->where('id', $invoice_id)->get('invoice')->row();
            createInvoiceInAPI($user, $inv);
            $productDetail[] = ['status' => 1, 'product' => $product->name, 'stock' => $stock->product, 'price' => $c['price']];

            // Başarıyla teslim edilen ürün için referans bonusu ver
            $this->processInvoiceReferralBonus($user, $inv, $shop);

            // Mail için datayı oluştur ve gönder
            $orderData = [
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'order_id' => $shop->order_id,
                'product_name' => $product->name,
                'product_price' => $c['price'],
                'product_code' => $stock->product,
                'date' => date('d.m.Y H:i')
            ];
            
            // Ürünün verildiği mailini gönder
            sendDeliveryNotification($user->email, $orderData);

            $this->alertLowStock($c['product_id'], $properties);
            
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            addlog('M_Payment::createInvoiceForStockProduct', 'Exception: ' . $e->getMessage() . ' for stock: ' . $stock->id);
            return false;
        }
    }

    /**
     * Düşük stok uyarısı gönderir
     * 
     * @param int $product_id Ürün ID
     * @param object $properties Site özellikleri
     */
    private function alertLowStock($product_id, $properties) {
        $stockCount = $this->db->where('product_id', $product_id)->where('isActive', 1)->count_all_results('stock');
        if ($stockCount < 3 && $properties->stock == 1) {
            stockAlert('<div class="orius-mail">
                <div class="box">
                <h1 class="logo-text">'. $properties->name .'</h1>
                <h2>Stok Bilgilendirmesi</h2>
                <p>Ürünü için son '. $stockCount .' Stok Kaldı. Lütfen ekleme yapın.</p>
                </div>
                </div>');
        }
    }

    /**
     * Otomatik ürün teslim etme işlemlerini yönetir
     * 
     * @param array $c Ürün bilgileri
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param object $properties Site özellikleri
     * @param array $api_settings API ayarları
     * @param array &$productDetail Ürün detayları
     * @param object $product Ürün nesnesi
     */
    private function handleAutoGiveProducts($c, $user, $shop, $properties, $api_settings, &$productDetail, $product) {
        $this->load->helper('api');
        if ($properties->autoGive == 1) {
            if ($this->isTurkpinProduct($product, $properties)) {
                $detail = proccessTurkpinOrder($user, $shop, $product, $c['price']);
                $productDetail = array_merge($productDetail, $detail);
            } elseif ($this->isPinabiProduct($product, $api_settings)) {
                $detail = proccessPinabiOrder($user, $shop, $product, $c, count($productDetail));
                $productDetail = array_merge($productDetail, $detail);
            } elseif ($this->isCustomProviderProduct($product)) {
                $provider = $this->db->where('id', $product->product_provider)->get('product_providers')->row();
                $detail = proccessCustomProviderOrder($user, $shop, $product, $c['price']);
                $productDetail = array_merge($productDetail, $detail);
            } else {
                $this->createPendingProduct($c, $user, $shop, $properties, $productDetail, $product);
            }
        } else {
            $this->createPendingProduct($c, $user, $shop, $properties, $productDetail, $product);
        }
    }

    /**
     * Türkpin ürünü olup olmadığını kontrol eder
     * 
     * @param object $product Ürün nesnesi
     * @param object $properties Site özellikleri
     * @return bool Türkpin ürünü ise true
     */
    private function isTurkpinProduct($product, $properties) {
        return $product->game_code != 0 && $product->product_code != 0 &&
            $product->product_provider == "turkpin" &&
            (!empty($properties->turkpin_username) && !empty($properties->turkpin_password));
    }

    /**
     * Pinabi ürünü olup olmadığını kontrol eder
     * 
     * @param object $product Ürün nesnesi
     * @param array $api_settings API ayarları
     * @return bool Pinabi ürünü ise true
     */
    private function isPinabiProduct($product, $api_settings) {
        return $product->game_code != 0 && $product->product_code != 0 &&
            $product->product_provider == "pinabi" &&
            (!empty($api_settings['pinabi']->apiUser) && !empty($api_settings['pinabi']->secretKey) && !empty($api_settings['pinabi']->Authorization));
    }

    /**
     * Özel sağlayıcı ürünü olup olmadığını kontrol eder
     * 
     * @param object $product Ürün nesnesi
     * @return bool Özel sağlayıcı ürünü ise true
     */
    private function isCustomProviderProduct($product) {
        if ($product->game_code != 0 && $product->product_code != 0) {
            $provider = $this->db->where('id', $product->product_provider)->get('product_providers')->row();
            return $provider && $provider->is_active;
        }
        return false;
    }

    /**
     * Bekleyen ürün kaydı oluşturur
     * 
     * @param array $c Ürün bilgileri
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param object $properties Site özellikleri
     * @param array &$productDetail Ürün detayları
     * @param object $product Ürün nesnesi
     */
    private function createPendingProduct($c, $user, $shop, $properties, &$productDetail, $product) {
        $data = [
            'user_id' => $shop->user_id,
            'product_id' => $c['product_id'],
            'date' => date('Y-m-d H:i:s'),
            'balance' => $user->balance,
            'new_balance' => $user->balance - $c['price'],
            'isActive' => 1,
            'shop_id' => $shop->id,
            'price' => $c['price'],
            'invoice_provider' => $this->db->where(['api_name' => 'billing', 'setting_key' => 'provider'])->get('api_settings')->row()->setting_value,
            'payment_commission' => ($shop->payment_commission > 0) ? number_format(($c['price'] * getCommission($shop->user_id)) / 100, 2, '.', '') : 0,
        ];

        $this->db->insert('pending_product', $data);
        $productDetail[] = ['status' => 2, 'product' => $product->name, 'price' => $c['price']];
    }

    /**
     * Sepet işlemini sonlandırır
     * 
     * @param int $shop_id Sipariş ID
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param array $productDetail Ürün detayları
     * @param string $text Bildirim metni
     */
    private function finalizeShopCart($shop_id, $user, $shop, $productDetail, $text) {
        $this->db->where('id', $shop_id)->update('shop', ['status' => 0]);
    }

    /**
     * Bakiye yükleme başarılı e-postası gönderir
     * 
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param float $newBalance Yeni bakiye
     */
    private function sendBalanceSuccessEmail($user, $shop, $newBalance) {
        $this->load->library('mailer');
        $this->mailer->send($user->email, 'balance_success', [
            'name' => $user->name,
            'surname' => $user->surname,
            'amount' => $shop->price,
            'currency' => 'TL',
            'transaction_date' => date('d.m.Y H:i'),
            'transaction_id' => $shop->order_id,
            'old_balance' => $user->balance,
            'new_balance' => $newBalance,
            'current_balance' => $newBalance
        ]);
    }

    /**
     * Abonelik kontrolü yapar ve gerekliyse iade işlemi gerçekleştirir
     * 
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     */
    private function checkSubscription($user, $shop) {
        //include helpers
        $this->load->helper('helpers');
        //hasUserFeature function
        $this->load->model('M_Subscription');
        $hasFeature = $this->M_Subscription->hasUserFeature($user->id, 'refund_value');
        if ($hasFeature) {
            //calculateUserRefund function
            $refundAmount = $this->M_Subscription->calculateUserRefund($user->id, $shop->price);
            
            if ($refundAmount <= 0) {
                return;
            }
            
            // En güncel kullanıcı bakiyesini al
            $currentUser = $this->db->where('id', $user->id)->get('user')->row();
            if (!$currentUser) {
                addlog('M_Payment::checkSubscription', 'User not found with id: ' . $user->id);
                return;
            }
            
            $currentBalance = $currentUser->balance;
            $newBalance = $currentBalance + $refundAmount;
            
            // Transaction başlat
            $this->db->trans_begin();
            
            try {
                // Kullanıcı bakiyesini güncelle
                $this->db->where('id', $user->id)->update('user', ['balance' => round($newBalance, 2)]);
                
                // İade işlemi için bakiye işlem kaydını oluştur
                $transaction_data = [
                    'user_id' => $user->id,
                    'transaction_type' => 'transfer_in',
                    'amount' => $refundAmount,
                    'description' => 'Abonelik avantajı - Satın alım iadesi',
                    'status' => 1, // Onaylı
                    'created_at' => date('Y-m-d H:i:s'),
                    'balance_before' => $currentBalance, // Güncel işlem öncesi bakiye
                    'balance_after_transaction' => $newBalance // Güncellenmiş bakiye
                ];
                
                $this->db->insert('wallet_transactions', $transaction_data);

                // Transaction'ı kontrol et
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    addlog('M_Payment::checkSubscription', 'Transaction failed for user: ' . $user->id . ' and shop: ' . $shop->id);
                    return;
                }

                // Transaction'ı tamamla
                $this->db->trans_commit();
                
                // İade işlemini logla
                addlog('M_Payment::checkSubscription', 'Abonelik iadesi eklendi. User ID: '.$user->id.', Miktar: '.$refundAmount.', Eski bakiye: '.$currentBalance.', Yeni bakiye: '.$newBalance);

                //getSubscriptionByUserId
                $subscription = $this->M_Subscription->getSubscriptionByUserId($user->id);

                if ($subscription){
                    //inserUserSavings helper
                    insertUserSavings($user->id, $subscription->subscription_id, $shop->id, 'Bakiye İadesi', $refundAmount, 'Ürün Bakiye İadesi', 'successful', date('Y-m-d H:i:s'));
                }
            } catch (Exception $e) {
                $this->db->trans_rollback();
                addlog('M_Payment::checkSubscription', 'Exception: ' . $e->getMessage() . ' for user: ' . $user->id . ' and shop: ' . $shop->id);
            }
        }
    }

    /**
     * Bayilik alım miktarını günceller
     * 
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     */
    private function updateDealerPurchase($user, $shop) {
        $this->load->model('M_Dealer');
        $this->M_Dealer->updateUserTotalPurchase($user->id, $shop->price);
        $this->M_Dealer->checkUpgradeEligibility($user->id);
    }

    /**
     * Kullanıcı tasarruf kayıtlarını ekler
     * 
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     */
    private function addUserSavingsForCommission($user, $shop) {
        // Add user savings record
        $this->load->model('M_Subscription');
        $userSaving = $this->M_Subscription->calculateSavingCommission($shop->id);
        $userSubscription = $this->M_Subscription->getSubscriptionByUserId($user->id);
        $properties = $this->db->where('id', 1)->get('properties')->row();
        
        //eğer shop altındaki type balance ise iptal et
        if ($shop->type != 'balance') {
            // Add earnings record for commission
            $this->addEarningsRecord($shop->id, 'payment_commission');
            // Add User Saving
            if ($this->M_Subscription->getSubscriptionByUserId($user->id)){
                if ($shop->type == 'credit_card')
                {
                    $normalPaymentCommission = number_format(($shop->price * $properties->commission) / 100, 2, '.', '');
                    $paymentCommission = number_format(($shop->price * $this->M_Subscription->getCommissionValue($shop->user_id)) / 100, 2, '.', '');
                    insertUserSavings($user->id, $userSubscription->subscription_id, $shop->id, 'Ödeme Komisyon Kazancı', $userSaving, 'Aboneliğiniz sayesinde ' . $normalPaymentCommission . ' TL yerine ' . $paymentCommission . ' TL ödediniz.', 'successful', date('Y-m-d H:i:s'));
                }else{
                    insertUserSavings($user->id, $userSubscription->subscription_id, $shop->id, 'Ödeme Komisyon Kazancı', $userSaving, $shop->type . ' Komisyon Kazancı', 'successful', date('Y-m-d H:i:s'));
                }
            }
        }
    }

    /**
     * Kullanıcı tasarruf kayıtlarını ekler
     * 
     * @param object $shop Sipariş nesnesi
     * @param object $user Kullanıcı nesnesi
     */
    private function handleUserSavings($shop, $user) {
        $this->load->model('M_Subscription');
        if ($this->M_Subscription->getSubscriptionByUserId($user->id)) {
            $userSaving = $this->M_Subscription->calculateSavingCommission($shop->id);
            $userSubscription = $this->M_Subscription->getSubscriptionByUserId($user->id);
            $properties = $this->db->where('id', 1)->get('properties')->row();
            
            $normalPaymentCommission = number_format(($shop->price * $properties->commission) / 100, 2, '.', '');
            $paymentCommission = number_format(($shop->price * $this->M_Subscription->getCommissionValue($shop->user_id)) / 100, 2, '.', '');
            insertUserSavings(
                $user->id, $userSubscription->subscription_id, $shop->id, 'Ödeme Komisyon Kazancı', $userSaving, 'Aboneliğiniz sayesinde ' . $normalPaymentCommission . ' TL yerine ' . $paymentCommission . ' TL ödediniz.', 'successful', date('Y-m-d H:i:s')
            );
        }
    }

    /**
     * Kazanç kaydı ekler
     * 
     * @param int $shop_id Sipariş ID
     * @param string $payment_type Ödeme türü
     */
    private function addEarningsRecord($shop_id, $payment_type) {
        $this->load->model('M_Earnings');
        $this->M_Earnings->insertEarning($shop_id, $payment_type);
    }

    /**
     * Sipariş için bildirimleri gönderir
     * 
     * @param int $shop_id Sipariş ID
     * @return bool İşlem başarılı ise true, değilse false
     */
    private function sendNotifications($shop_id) {
        $this->load->library('mailer');

        $shop = $this->db->where('id', $shop_id)->get('shop')->row();
        if (!$shop) return false;

        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        if (!$user) return false;

        // Sipariş detaylarını al
        $pendingProducts = $this->db->where('shop_id', $shop_id)->get('pending_product')->result();
        $invoices = $this->db->where('shop_id', $shop_id)->get('invoice')->result();

        $productsContent = '';
        foreach($pendingProducts as $product) {
            $productInfo = $this->db->where('id', $product->product_id)->get('product')->row();
            $productsContent .= '<tr>
                <td style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="60">
                                <img src="'.base_url('assets/img/product/') . $productInfo->img.'" 
                                     style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;">
                            </td>
                            <td style="padding-left: 15px;">
                                <div style="color: #1a202c; font-weight: 500;">'.$productInfo->name.'</div>
                            </td>
                            <td width="100" align="center">
                                <span style="color: #64748b;">Adet:</span>
                                <div style="color: #1a202c; font-weight: 500;">1</div>
                            </td>
                            <td width="120" align="right">
                                <span style="color: #1a202c; font-weight: 500;">'.number_format($product->price, 2).' TL</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';
        }

        foreach($invoices as $invoice) {
            $productInfo = $this->db->where('id', $invoice->product_id)->get('product')->row();
            $productsContent .= '<tr style="border-bottom: 1px solid #edf2f7;">
                <td style="padding: 12px 15px; color: #4a5568;">'.$productInfo->name.'</td> 
                <td style="padding: 12px 15px; text-align: center; color: #4a5568;">1</td>
                <td style="padding: 12px 15px; text-align: right; color: #4a5568;">'.number_format($invoice->price, 2).' TL</td>
            </tr>';
        }

        setlocale(LC_TIME, 'tr_TR.UTF-8');
        $date = date('d.m.Y H:i', strtotime($shop->date));

        $orderData = [
            'site_name' => $this->db->where('id', 1)->get('properties')->row()->name,
            'name' => $user->name . ' ' . $user->surname,
            'order_id' => $shop->id,
            'products' => $productsContent,
            'total_amount' => number_format($shop->price, 2),
            'date' => $date
        ];

        $this->mailer->send(
            $user->email,
            'new_order',
            $orderData,
            1
        );

        if($shop->type == 'deposit'){
            $userMessage = "Bakiye yükleme talebiniz tamamlandı. Şimdi harcama zamanı!";
        }else{
            $userMessage = "Siparişiniz başarıyla alındı. Sipariş No: " . $shop->order_id;
        }

        sendNotificationSite($user->id, 'Sistem Bildirimi', $userMessage, base_url('client/product'));
        
        return true;
    }

    /**
     * Ödeme yöntemi adını döndürür
     */
    private function getPaymentMethodName($payment_method_id) {
        // SQL Injection önlemi - payment_method_id'yi integer'a dönüştür
        $payment_method_id = intval($payment_method_id);
        $payment = $this->db->where('id', $payment_method_id)->get('payment')->row();
        return $payment ? $payment->payment_name : 'Bilinmeyen';
    }
}
<?php
// models/M_Payment.php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ödeme İşlemleri Modeli
 * 
 * Bu model, tüm ödeme işlemleri, bakiye yükleme, ürün satın alma, 
 * komisyon hesaplama ve fatura oluşturma işlemlerini yönetir.
 */
class M_Payment extends CI_Model {

    /**
     * Sınıf özellikleri
     */
    private $commission_rate;
    private $properties;
    private $api_settings;

    /**
     * Kurucu metod
     */
    public function __construct() {
        parent::__construct();
        $this->properties = $this->db->where('id', 1)->get('properties')->row();
        $this->load->helper('api');
    }

    /**
     * Yeni sipariş oluşturur
     * 
     * @param int $user_id Kullanıcı ID
     * @param string $encode JSON formatında ürün bilgileri
     * @param float $price Toplam fiyat
     * @param string $type Ödeme tipi (deposit, credit_card, balance)
     * @param float $withOutCommission Komisyonsuz fiyat (opsiyonel)
     * @param int|null $coupon Kupon ID (opsiyonel)
     * @param int|null $payment_method_id Ödeme yöntemi ID (opsiyonel)
     * @return string|bool Başarılı ise sipariş ID, başarısız ise false
     */
    public function addShop($user_id, $encode, $price, $type, $withOutCommission = 0, $coupon = null, $payment_method_id = null) {
        // SQL Injection önlemi
        $user_id = intval($user_id);
        $price = floatval($price);
        $withOutCommission = floatval($withOutCommission);
        $type = $this->db->escape_str($type);
        $coupon = $coupon !== null ? intval($coupon) : null;
        $payment_method_id = $payment_method_id !== null ? intval($payment_method_id) : null;
        $this->load->helper('api');
        $randString = $this->generateUniqueOrderId();
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $payment_commission = $this->calculateCommission($type, $price, getCommission($user_id, $payment_method_id));
        $invoice_provider = $this->db->where(['api_name' => 'billing', 'setting_key' => 'provider'])
                                    ->get('api_settings')
                                    ->row()
                                    ->setting_value;

        // Varsayılan ödeme yöntemini kullan eğer belirtilmemişse
        if ($payment_method_id === null) {
            $payment = $this->db->where('is_default', 1)->where('status', 1)->get('payment')->row();
            if (!$payment) {
                $payment = $this->db->where('status', 1)->get('payment')->row();
            }
            $payment_method_id = $payment ? $payment->id : 1;
        }

        $data = [
            'price' => $price,
            'date' => date('Y-m-d H:i:s'),
            'status' => 1,
            'order_id' => $randString,
            'user_id' => $user_id,
            'product' => $encode,
            'ip_address' => getUserIp(),
            'type' => $type,
            'coupon' => $coupon,
            'invoice_provider' => $invoice_provider,
            'payment_commission' => $payment_commission,
            'payment_method_id' => $payment_method_id,
            'balance' => $this->db->where('id', $user_id)->get('user')->row()->balance
        ];

        if ($this->db->insert('shop', $data)) {
            if (isset($coupon) && !empty($coupon)) {
                $this->updateCouponUsage($coupon);
            }
            return $randString;
        } else {
            addlog('M_Payment::addShop', 'Insert failed for order_id: ' . $randString);
            return false;
        }
    }

    /**
     * Sepet içeriğindeki toplam tutarı hesaplar
     * 
     * @param string $encode JSON formatında ürün bilgileri
     * @return float Toplam tutar
     */
    public function calculate($encode) {
        $decode = json_decode($encode, true);
        return array_reduce($decode, function($amount, $d) {
            return $amount + $d['price'] * $d['qty'];
        }, 0);
    }

    /**
     * Bakiye yükleme işlemini onaylar
     * 
     * @param int $shop_id Sipariş ID
     * @return bool İşlem başarılı ise true, değilse false
     */
    public function confirmShopForBalance($shop_id) {
        $this->load->helper('api');
        
        // Sipariş bilgilerini al
        $shop = $this->db->where('id', $shop_id)->get('shop')->row();
        if (!$shop) {
            addlog('M_Payment::confirmShopForBalance', 'Shop not found with id: ' . $shop_id);
            return false;
        }

        // Kullanıcı bilgilerini al
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        if (!$user) {
            addlog('M_Payment::confirmShopForBalance', 'User not found with id: ' . $shop->user_id);
            return false;
        }

        // Transaction başlat
        $this->db->trans_begin();

        try {
            // Siparişi onayla
            $this->db->where('id', $shop_id)->update('shop', ['status' => 0]);

            // Kullanıcı bakiyesini güncelle
            $newBalance = $this->updateUserBalance($user, $shop);

            // Kupon kullanımını güncelle
            if (isset($shop->coupon) && !empty($shop->coupon)) {
                $this->updateCouponUsage($shop->coupon);
            }

            // Fatura oluştur
            createInvoiceForBalance($user, $shop);

            // Siparişi güncelle ve kullanıcının bakiyesini güncelle
            $this->db->where('id', $shop_id)->update('shop', [
                'balance' => $user->balance,
                'new_balance' => $newBalance
            ]);
            $this->db->where('id', $user->id)->update('user', ['balance' => $newBalance]);

            // Transaction'ı kontrol et
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                addlog('M_Payment::confirmShopForBalance', 'Transaction failed for shop id: ' . $shop_id);
                return false;
            }

            // Transaction'ı tamamla
            $this->db->trans_commit();

            // Bakiye yükleme başarılı maili gönder
            $this->sendBalanceSuccessEmail($user, $shop, $newBalance);

            // Bildirimler gönder
            $this->sendNotifications($shop->id);

            // Kazanç kaydı ekle
            $this->addEarningsRecord($shop_id, 'deposit');

            // Kullanıcı tasarruf kaydı ekle (eğer aboneliği varsa)
            $this->handleUserSavings($shop, $user);

            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            addlog('M_Payment::confirmShopForBalance', 'Exception: ' . $e->getMessage() . ' for shop id: ' . $shop_id);
            return false;
        }
    }

    /**
     * Sepet satın alma işlemini onaylar
     * 
     * @param int $shop_id Sipariş ID
     * @return bool İşlem başarılı ise true, değilse false
     */
    public function confirmShopForCart($shop_id) {
        $this->load->helper('api');
        
        // Sipariş bilgilerini al
        $shop = $this->db->where('id', $shop_id)->get('shop')->row();
        if (!$shop) {
            addlog('M_Payment::confirmShopForCart', 'Shop not found with id: ' . $shop_id);
            return false;
        }

        // Kullanıcı bilgilerini al
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        if (!$user) {
            addlog('M_Payment::confirmShopForCart', 'User not found with id: ' . $shop->user_id);
            return false;
        }

        // Sepet içeriğini al
        $cart = json_decode($shop->product, true);
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $api_settings = getAPIsettings();
        $productDetail = [];

        // Transaction başlat
        $this->db->trans_begin();

        try {
            // Kullanıcının bakiyesini güncelle
            $userBalance = $user->balance;
            $newBalance = $this->updateUserBalance($user, $shop);
            
            // Satın alım için bakiye çıkış kaydını oluştur
            if ($shop->type != 'deposit' && $shop->type != 'balance') {
                $this->createPurchaseTransaction($user, $shop, $userBalance, $newBalance);
            }

            // Transaction'ı kontrol et
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                addlog('M_Payment::confirmShopForCart', 'Transaction failed during balance update for shop id: ' . $shop_id);
                return false;
            }

            // Transaction'ı tamamla
            $this->db->trans_commit();

            // Sepetteki her ürünü işle
            foreach ($cart as $c) {
                // Paket kontrolü - eğer name "package_" ile başlıyorsa paket işle
                if (isset($c['name']) && strpos($c['name'], 'package_') === 0) {
                    // Paket ID'sini al
                    $package_id = isset($c['extras']['package_id']) ? intval($c['extras']['package_id']) : intval(str_replace('package_', '', $c['name']));
                    
                    // Paketi getir
                    $package = $this->db->where('id', $package_id)->where('isActive', 1)->get('packages')->row();
                    
                    if ($package) {
                        // Paket içindeki ürünleri getir
                        $package_products = $this->db->select('p.*, pp.quantity, pp.sort_order')
                            ->from('package_products pp')
                            ->join('product p', 'p.id = pp.product_id', 'left')
                            ->where('pp.package_id', $package_id)
                            ->where('p.isActive', 1)
                            ->order_by('pp.sort_order', 'ASC')
                            ->get()
                            ->result();
                        
                        // Paket içindeki her ürünü işle
                        foreach ($package_products as $package_product) {
                            // Paket fiyatını ürünlere orantılı dağıt
                            $total_original_price = 0;
                            foreach ($package_products as $pp) {
                                $total_original_price += $pp->price;
                            }
                            
                            // Ürün fiyatını orantılı hesapla
                            $product_price_ratio = $total_original_price > 0 ? ($package_product->price / $total_original_price) : (1 / count($package_products));
                            $distributed_price = $c['price'] * $product_price_ratio;
                            
                            // Ürün için cart item oluştur
                            $package_cart_item = [
                                'id' => $package_product->id,
                                'product_id' => $package_product->id,
                                'qty' => 1,
                                'price' => $distributed_price,
                                'name' => 'product_' . $package_product->id,
                                'extras' => $c['extras'] ?? []
                            ];
                            
                            // Ürünü işle
                            $qty = 1;
                            while ($qty > 0) {
                                $result = $this->processCartItem($package_cart_item, $user, $shop, $properties, $api_settings, $productDetail);
                                if ($result == "pending") {
                                    $this->finalizeShopCart($shop_id, $user, $shop, $productDetail, 'Satın alım tamamlandı. Ürünlerin en kısa sürede teslim edilecek.');
                                } elseif ($result == "success") {
                                    $product = $this->db->where('id', $package_product->id)->get('product')->row();
                                    $this->finalizeShopCart($shop_id, $user, $shop, $productDetail, 'Satın alım tamamlandı.' . $product->name . ' Adlı ürünün teslim edildi. Değerlendirmeyi unutma!');
                                }
                                $qty--;
                            }
                        }
                    }
                } else {
                    // Normal ürün işleme
                    $product = $this->db->where('id', $c['product_id'])->get('product')->row();
                    $qty = $c['qty'];

                    // Qty kadar ürün satın alınıyor
                    while ($qty > 0) {
                        $result = $this->processCartItem($c, $user, $shop, $properties, $api_settings, $productDetail);
                        if ($result == "pending") {
                            $this->finalizeShopCart($shop_id, $user, $shop, $productDetail, 'Satın alım tamamlandı. Ürünlerin en kısa sürede teslim edilecek.');
                        } elseif ($result == "success") {
                            $product = $this->db->where('id', $c['product_id'])->get('product')->row();
                            $this->finalizeShopCart($shop_id, $user, $shop, $productDetail, 'Satın alım tamamlandı.' . $product->name . ' Adlı ürünün teslim edildi. Değerlendirmeyi unutma!');
                        }
                        $qty--;
                    }
                }
            }

            // Kullanıcıya sipariş oluştu bildirimleri gönder
            $this->sendNotifications($shop_id);

            // Aboneliği kontrol et ve gerekli aksiyonu al
            $this->checkSubscription($user, $shop);

            // Ürün satışı için kazanç kaydı ekle
            $this->addEarningsRecord($shop_id, 'product_sale');

            // Bayilik alım miktarını güncelle
            $this->updateDealerPurchase($user, $shop);

            // Kullanıcı tasarruf kaydını ekle
            $this->addUserSavingsForCommission($user, $shop);

            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            addlog('M_Payment::confirmShopForCart', 'Exception: ' . $e->getMessage() . ' for shop id: ' . $shop_id);
            return false;
        }
    }

    /**
     * Benzersiz sipariş ID'si oluşturur
     * 
     * @return string Benzersiz sipariş ID'si
     */
    private function generateUniqueOrderId() {
        $randString = randString(20);
        while ($this->db->where('order_id', $randString)->get('shop')->row()) {
            $randString = randString(25);
        }
        return $randString;
    }

    /**
     * Komisyon hesaplar
     * 
     * @param string $type Ödeme tipi
     * @param float $withOutCommission Komisyonsuz fiyat
     * @param float $commissionRate Komisyon oranı
     * @return float Komisyon tutarı
     */
    private function calculateCommission($type, $withOutCommission, $commissionRate) {
        if ($type == 'credit_card' || $type == 'deposit') {
            return number_format(($withOutCommission * $commissionRate) / 100, 2, '.', '');
        }
        return 0;
    }

    /**
     * Kupon kullanımını günceller
     * 
     * @param int $coupon_id Kupon ID
     */
    private function updateCouponUsage($coupon_id) {
        $coupon = $this->db->where('id', $coupon_id)->get("coupons")->row();
        if ($coupon) {
            $used_by = json_decode($coupon->used_by ?? "[]", true);
            $used_by[] = $this->session->userdata('info')['id'];
            $this->db->where('id', $coupon_id)->update('coupons', ['used_by' => json_encode($used_by)]);
        }
    }

    /**
     * Kullanıcı bakiyesini günceller
     * 
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @return float Yeni bakiye
     */
    private function updateUserBalance($user, $shop) {
        $newBalance = $user->balance;

        // İşlem tipine göre bakiye güncelleme
        if ($shop->type == 'deposit') {
            // Bakiye yüklemede para eklenir
            $newBalance = $user->balance + $shop->price;
            
            // Bakiye yükleme işlem kaydını oluştur
            $transaction_data = [
                'user_id' => $user->id,
                'transaction_type' => 'transfer_in',
                'amount' => $shop->price,
                'description' => 'Bakiye yükleme',
                'status' => 1, // Onaylı
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance, // İşlem öncesi bakiye
                'balance_after_transaction' => $newBalance // Güncellenmiş bakiye
            ];
            
            $this->db->insert('wallet_transactions', $transaction_data);
            
            // Sadece bakiye yükleme işleminde veritabanını güncelle
            $this->db->where('id', $user->id)->update('user', ['balance' => $newBalance]);
            
            // Bakiye yükleme işleminde referans bonusu VERİLMEZ
            addlog('M_Payment::updateUserBalance', 'Bakiye yükleme işlemi tamamlandı. User ID: '.$user->id.', Eski bakiye: '.$user->balance.', Yeni bakiye: '.$newBalance);
        } else if ($shop->type == 'balance') {
            // Bakiye ile ürün satın alımında bakiye azaltılır
            $newBalance = $user->balance - $shop->price;
            
            // Bakiye ile satın alma işlem kaydını oluştur
            $transaction_data = [
                'user_id' => $user->id,
                'transaction_type' => 'purchase',
                'amount' => -$shop->price,
                'description' => 'Bakiye ile ürün satın alımı - Sipariş No: ' . $shop->id,
                'status' => 1, // Onaylı
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance, // İşlem öncesi bakiye
                'balance_after_transaction' => $newBalance, // Güncellenmiş bakiye
                'related_id' => $shop->id
            ];
            
            $this->db->insert('wallet_transactions', $transaction_data);

            // Bakiyeyi güncelle
            $this->db->where('id', $user->id)->update('user', ['balance' => $newBalance]);
            
            // Güncel bakiyeyi logla
            addlog('M_Payment::updateUserBalance', 'Bakiye ile satın alma işlemi. User ID: '.$user->id.', Eski bakiye: '.$user->balance.', Yeni bakiye: '.$newBalance);
        } else if ($shop->type == 'credit_card') {
            // Kredi kartı ile alımlarda bakiyeden düşüm yapılmamalı
            // İşlem kaydı sadece izleme amaçlı oluşturulmalı
            $transaction_data = [
                'user_id' => $user->id,
                'transaction_type' => 'purchase',
                'amount' => $shop->price, // Bakiye etkilemediği için 0
                'description' => 'Kredi kartı ile ürün satın alımı - Sipariş No: ' . $shop->id,
                'status' => 1, // Onaylı
                'payment_method' => 'credit_card',
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance, // İşlem öncesi bakiye
                'balance_after_transaction' => $user->balance, // Bakiye değişmedi
                'related_id' => $shop->id
            ];
            
            $this->db->insert('wallet_transactions', $transaction_data);
            
            // Bakiye değişmeyecek
            $newBalance = $user->balance;
            
            // Log ekle
            addlog('M_Payment::updateUserBalance', 'Kredi kartı ile satın alma işlemi. User ID: '.$user->id.', Bakiye etkilenmedi: '.$newBalance);
        } else {
            // Diğer tipteki işlemler için bakiye değişimi yok
            $newBalance = $user->balance;
        }
        
        return $newBalance;
    }

    /**
     * İnvoice için referans bonusu işlemi
     * 
     * @param object $user Kullanıcı bilgileri
     * @param object $invoice Fatura bilgileri
     * @param object $shop Shop bilgileri
     * @return bool İşlem başarılı ise true
     */
    public function processInvoiceReferralBonus($user, $invoice, $shop) {
        addlog('M_Payment::processInvoiceReferralBonus', 'Referans bonus işlemi başlatıldı. Kullanıcı: ' . $user->name . ' ' . $user->surname . ' (ID: ' . $user->id . '), Fatura: ' . $invoice->id . ', Fiyat: ' . $invoice->price . ' TL');
        
        // Referans sistemi aktif mi kontrol et
        $ref_settings = $this->db->where('key', 'referral_system_enabled')->get('settings')->row();
        if (!$ref_settings || $ref_settings->value != '1') {
            addlog('M_Payment::processInvoiceReferralBonus', 'Referans sistemi aktif değil. İşlem iptal edildi.');
            return false;
        }
        addlog('M_Payment::processInvoiceReferralBonus', 'Referans sistemi aktif durumda.');

        // Kullanıcının referansı var mı kontrol et
        $user_refs = $this->db->where("buyer_id", $user->id)->get("user_references")->row();
        if (!$user_refs) {
            addlog('M_Payment::processInvoiceReferralBonus', 'Kullanıcının referansı bulunamadı. İşlem iptal edildi.');
            return false;
        }
        addlog('M_Payment::processInvoiceReferralBonus', 'Kullanıcının referansı bulundu. Referans veren: ' . $user_refs->referrer_id);

        // İlk alışveriş kontrolü ve kayıt bonusu ver (referral_require_purchase=1 ise)
        $this->load->library('Referral_System');
        $registration_bonus_result = $this->referral_system->processFirstPurchaseRegistrationBonus($user->id);
        if ($registration_bonus_result) {
            addlog('M_Payment::processInvoiceReferralBonus', 'İlk alışveriş kayıt bonusu verildi. Kullanıcı: ' . $user->id);
        }

        // Ürün ve kategori bilgilerini al
        addlog('M_Payment::processInvoiceReferralBonus', 'Ürün bilgileri alınıyor. Ürün ID: ' . $invoice->product_id);
        $product = $this->db->select('p.*, c.name as category_name')
                           ->from('product p')
                           ->join('category c', 'p.category_id = c.id', 'left')
                           ->where('p.id', $invoice->product_id)
                           ->get()
                           ->row();

        if (!$product) {
            addlog('M_Payment::processInvoiceReferralBonus', 'Ürün bulunamadı. Fatura ID: ' . $invoice->id . ', Ürün ID: ' . $invoice->product_id);
            return false;
        }
        addlog('M_Payment::processInvoiceReferralBonus', 'Ürün bulundu: ' . $product->name . ' (Kategori: ' . $product->category_name . ', Kategori ID: ' . $product->category_id . ')');

        // Kategoriye özel bonus ayarını kontrol et
        addlog('M_Payment::processInvoiceReferralBonus', 'Kategoriye özel bonus ayarları kontrol ediliyor...');
        $category_bonus = $this->db->where('category_id', $product->category_id)
                                  ->where('is_active', 1)
                                  ->get('reference_category_commissions')
                                  ->row();

        $bonus_amount = 0;
        $bonus_source = 'genel'; // 'genel' veya 'kategori'

        if ($category_bonus) {
            addlog('M_Payment::processInvoiceReferralBonus', 'Kategoriye özel bonus ayarı bulundu. Bonus oranı: %' . $category_bonus->bonus_percentage . ', Min tutar: ' . $category_bonus->min_amount . ' TL, Max bonus: ' . ($category_bonus->max_bonus ?: 'Sınırsız'));
            
            // Kategoriye özel bonus var
            if ($invoice->price >= $category_bonus->min_amount) {
                $bonus_amount = ($invoice->price * $category_bonus->bonus_percentage) / 100;
                $bonus_source = 'kategori';
                
                addlog('M_Payment::processInvoiceReferralBonus', 'Kategori bonus hesaplandı: ' . $invoice->price . ' TL x %' . $category_bonus->bonus_percentage . ' = ' . $bonus_amount . ' TL');
                
                // Kategori maksimum bonus kontrolü
                if ($category_bonus->max_bonus > 0 && $bonus_amount > $category_bonus->max_bonus) {
                    addlog('M_Payment::processInvoiceReferralBonus', 'Kategori maksimum bonus limitine takıldı. ' . $bonus_amount . ' TL -> ' . $category_bonus->max_bonus . ' TL');
                    $bonus_amount = $category_bonus->max_bonus;
                }
                
                addlog('M_Payment::processInvoiceReferralBonus', 'Kategoriye özel bonus uygulandı. Ürün: ' . $product->name . ', Kategori: ' . $product->category_name . ', Oran: %' . $category_bonus->bonus_percentage . ', Final bonus: ' . $bonus_amount . ' TL');
            } else {
                addlog('M_Payment::processInvoiceReferralBonus', 'Fatura tutarı kategori minimum tutarının altında. Fatura: ' . $invoice->price . ' TL < Minimum: ' . $category_bonus->min_amount . ' TL');
                return false;
            }
        } else {
            addlog('M_Payment::processInvoiceReferralBonus', 'Kategoriye özel bonus ayarı bulunamadı. Genel bonus ayarları kullanılacak.');
            // Genel bonus ayarlarını al (M_Settings kullanarak varsayılan değerlerle)
            $this->load->model('M_Settings');
            $purchase_bonus_rate_value = $this->M_Settings->getSettingValue('referral_purchase_bonus_rate', '5.00');
            $min_purchase_amount_value = $this->M_Settings->getSettingValue('referral_min_purchase_amount', '0.00');

            addlog('M_Payment::processInvoiceReferralBonus', 'Genel bonus ayarları alınıyor. Bonus oranı: ' . $purchase_bonus_rate_value . '%, Min tutar: ' . $min_purchase_amount_value . ' TL');

            // Minimum alım tutarını kontrol et (genel ayar)
            if (floatval($min_purchase_amount_value) > 0 && $invoice->price < floatval($min_purchase_amount_value)) {
                addlog('M_Payment::processInvoiceReferralBonus', 'Fatura tutarı genel minimum tutarının altında. Fatura: ' . $invoice->price . ' TL < Minimum: ' . $min_purchase_amount_value . ' TL');
                return false;
            }

            // Genel bonus oranını kontrol et
            if (floatval($purchase_bonus_rate_value) <= 0) {
                addlog('M_Payment::processInvoiceReferralBonus', 'Genel bonus oranı sıfır veya ayarlanmamış. Oran: ' . $purchase_bonus_rate_value);
                return false;
            }

            // Genel bonus hesapla
            $bonus_amount = ($invoice->price * floatval($purchase_bonus_rate_value)) / 100;
            addlog('M_Payment::processInvoiceReferralBonus', 'Genel bonus hesaplandı: ' . $invoice->price . ' TL x %' . $purchase_bonus_rate_value . ' = ' . $bonus_amount . ' TL');
        }

        if ($bonus_amount <= 0) {
            addlog('M_Payment::processInvoiceReferralBonus', 'Hesaplanan bonus tutarı sıfır veya negatif: ' . $bonus_amount . ' TL. İşlem iptal edildi.');
            return false;
        }
        
        addlog('M_Payment::processInvoiceReferralBonus', 'Bonus hesaplaması tamamlandı. Kaynak: ' . $bonus_source . ', Tutar: ' . $bonus_amount . ' TL');

        $referrer_id = $user_refs->referrer_id;
        addlog('M_Payment::processInvoiceReferralBonus', 'Referans veren kullanıcı kontrol ediliyor. Referrer ID: ' . $referrer_id);

        // Referans veren kullanıcıyı al
        $referrer = $this->db->where('id', $referrer_id)->get('user')->row();
        if (!$referrer) {
            addlog('M_Payment::processInvoiceReferralBonus', 'Referans veren kullanıcı bulunamadı. Referrer ID: ' . $referrer_id);
            return false;
        }
        addlog('M_Payment::processInvoiceReferralBonus', 'Referans veren kullanıcı bulundu: ' . $referrer->name . ' ' . $referrer->surname . ' (Mevcut bakiye: ' . $referrer->balance . ' TL, Çekilebilir bakiye: ' . ($referrer->balance2 ?? '0') . ' TL)');

        // Genel maksimum bonus kontrollerini al
        addlog('M_Payment::processInvoiceReferralBonus', 'Maksimum bonus limit kontrolleri yapılıyor...');
        $max_bonus_per_transaction = $this->db->where('key', 'referral_max_bonus_per_transaction')->get('settings')->row();
        $max_bonus_per_month = $this->db->where('key', 'referral_max_bonus_per_month')->get('settings')->row();

        addlog('M_Payment::processInvoiceReferralBonus', 'Limit ayarları - İşlem başına: ' . ($max_bonus_per_transaction ? $max_bonus_per_transaction->value . ' TL' : 'Sınırsız') . ', Aylık: ' . ($max_bonus_per_month ? $max_bonus_per_month->value . ' TL' : 'Sınırsız'));

        // İşlem başına maksimum bonus kontrolü (genel ayar)
        if ($max_bonus_per_transaction && $bonus_amount > floatval($max_bonus_per_transaction->value)) {
            $original_bonus = $bonus_amount;
            $bonus_amount = floatval($max_bonus_per_transaction->value);
            addlog('M_Payment::processInvoiceReferralBonus', 'İşlem başına maksimum bonus limitine takıldı. ' . $original_bonus . ' TL -> ' . $bonus_amount . ' TL');
        }

        // Aylık maksimum bonus kontrolü (genel ayar)
        if ($max_bonus_per_month) {
            $monthly_limit = floatval($max_bonus_per_month->value);
            $current_month_start = date('Y-m-01 00:00:00');
            $current_month_end = date('Y-m-t 23:59:59');
            
            addlog('M_Payment::processInvoiceReferralBonus', 'Aylık bonus limiti kontrol ediliyor. Bu ay aralığı: ' . $current_month_start . ' - ' . $current_month_end);
            
            $this_month_bonus = $this->db
                ->where('referrer_id', $referrer_id)
                ->where('bonus_type', 'purchase')
                ->where('status', 'paid')
                ->where('created_at >=', $current_month_start)
                ->where('created_at <=', $current_month_end)
                ->select_sum('bonus_amount')
                ->get('reference_bonus_history')
                ->row()
                ->bonus_amount ?? 0;

            addlog('M_Payment::processInvoiceReferralBonus', 'Bu ay alınan toplam bonus: ' . $this_month_bonus . ' TL, Aylık limit: ' . $monthly_limit . ' TL, Yeni bonus: ' . $bonus_amount . ' TL');

            if (($this_month_bonus + $bonus_amount) > $monthly_limit) {
                $original_bonus = $bonus_amount;
                $bonus_amount = max(0, $monthly_limit - $this_month_bonus);
                if ($bonus_amount <= 0) {
                    addlog('M_Payment::processInvoiceReferralBonus', 'Aylık bonus limitine ulaşıldı. Referrer: ' . $referrer_id . ', Limit: ' . $monthly_limit . ' TL, Bu ay toplam: ' . $this_month_bonus . ' TL. İşlem iptal edildi.');
                    return false;
                }
                addlog('M_Payment::processInvoiceReferralBonus', 'Aylık bonus limitine takıldı. ' . $original_bonus . ' TL -> ' . $bonus_amount . ' TL (Kalan limit: ' . ($monthly_limit - $this_month_bonus) . ' TL)');
            }
        }

        // Referans bonusunun hangi bakiye türüne yükleneceğini kontrol et
        addlog('M_Payment::processInvoiceReferralBonus', 'Bakiye türü ayarı kontrol ediliyor...');
        $balance_type_setting = $this->db->where('key', 'referral_bonus_balance_type')->get('settings')->row();
        $balance_type = $balance_type_setting ? $balance_type_setting->value : 'withdrawable'; // varsayılan: withdrawable
        
        addlog('M_Payment::processInvoiceReferralBonus', 'Bonus bakiye türü belirlendi: ' . $balance_type . ' (' . ($balance_type === 'withdrawable' ? 'Çekilebilir bakiye (balance2)' : 'Harcayabilir bakiye (balance)') . ')');

        // Transaction başlat
        addlog('M_Payment::processInvoiceReferralBonus', 'Veritabanı transaction başlatılıyor...');
        $this->db->trans_begin();

        try {
            // Bakiye türüne göre güncelleme yap
            if ($balance_type === 'withdrawable') {
                // balance2 (çekilebilir bakiye) güncelle
                $current_balance2 = $referrer->balance2 ?? 0;
                $new_balance2 = $current_balance2 + $bonus_amount;
                
                addlog('M_Payment::processInvoiceReferralBonus', 'Çekilebilir bakiye güncellenecek. Önceki: ' . $current_balance2 . ' TL, Bonus: ' . $bonus_amount . ' TL, Sonrası: ' . $new_balance2 . ' TL');
                
                $this->db->where('id', $referrer_id)->update('user', ['balance2' => $new_balance2]);
                
                $balance_before = $current_balance2;
                $balance_after = $new_balance2;
                $wallet_balance_type = 'withdrawable';
                
                addlog('M_Payment::processInvoiceReferralBonus', 'Çekilebilir bakiye başarıyla güncellendi. Referrer: ' . $referrer_id . ', Bonus: ' . $bonus_amount . ' TL, Yeni balance2: ' . $new_balance2 . ' TL');
            } else {
                // balance (harcayabilir bakiye) güncelle - varsayılan
                $new_referrer_balance = $referrer->balance + $bonus_amount;
                
                addlog('M_Payment::processInvoiceReferralBonus', 'Harcayabilir bakiye güncellenecek. Önceki: ' . $referrer->balance . ' TL, Bonus: ' . $bonus_amount . ' TL, Sonrası: ' . $new_referrer_balance . ' TL');
                
                $this->db->where('id', $referrer_id)->update('user', ['balance' => $new_referrer_balance]);
                
                $balance_before = $referrer->balance;
                $balance_after = $new_referrer_balance;
                $wallet_balance_type = 'spendable';
                
                addlog('M_Payment::processInvoiceReferralBonus', 'Harcayabilir bakiye başarıyla güncellendi. Referrer: ' . $referrer_id . ', Bonus: ' . $bonus_amount . ' TL, Yeni balance: ' . $new_referrer_balance . ' TL');
            }

            // Wallet transaction kaydı oluştur
            addlog('M_Payment::processInvoiceReferralBonus', 'Wallet transaction kaydı oluşturuluyor...');
            $transaction_data = [
                'user_id' => $referrer_id,
                'transaction_type' => 'referral_bonus',
                'balance_type' => $wallet_balance_type,
                'amount' => $bonus_amount,
                'description' => 'Alışveriş referans bonusu - ' . $user->name . ' ' . $user->surname . ' (Fatura: ' . $invoice->id . ')',
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $balance_before,
                'balance_after_transaction' => $balance_after,
                'related_id' => $invoice->id
            ];
            $this->db->insert('wallet_transactions', $transaction_data);
            addlog('M_Payment::processInvoiceReferralBonus', 'Wallet transaction kaydı oluşturuldu. Tür: ' . $wallet_balance_type . ', Tutar: ' . $bonus_amount . ' TL');

            // Description oluştur (kategoriye özel veya genel)
            addlog('M_Payment::processInvoiceReferralBonus', 'Bonus açıklaması oluşturuluyor. Kaynak: ' . $bonus_source);
            if ($bonus_source == 'kategori') {
                $description = 'Alışveriş bonusu - ' . $product->name . ' (' . $product->category_name . ' kategorisi - %' . $category_bonus->bonus_percentage . ' komisyon)';
            } else {
                $general_rate = $this->db->where('key', 'referral_purchase_bonus_rate')->get('settings')->row();
                $description = 'Alışveriş bonusu - ' . $product->name . ' (Genel oran - %' . ($general_rate ? $general_rate->value : '0') . ' komisyon)';
            }
            addlog('M_Payment::processInvoiceReferralBonus', 'Bonus açıklaması: ' . $description);

            // Reference bonus history kaydı oluştur
            addlog('M_Payment::processInvoiceReferralBonus', 'Referans bonus geçmişi kaydı oluşturuluyor...');
            // Aynı fatura için daha önce bonus ödenmiş mi kontrol et
            $existing_bonus = $this->db->where('invoice_id', $invoice->id)
                                       ->where('status', 'paid')
                                       ->get('reference_bonus_history')
                                       ->row();

            if ($existing_bonus) {
                $this->db->trans_rollback();
                addlog('M_Payment::processInvoiceReferralBonus', 'Bu fatura için daha önce bonus verilmiş. Fatura ID: ' . $invoice->id);
                return false;
            }

            $bonus_history_data = [
                'referrer_id' => $referrer_id,
                'referred_user_id' => $user->id,
                'invoice_id' => $invoice->id,
                'shop_id' => $shop->id,
                'bonus_amount' => $bonus_amount,
                'bonus_type' => 'purchase',
                'description' => $description,
                'status' => 'paid',
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('reference_bonus_history', $bonus_history_data);
            addlog('M_Payment::processInvoiceReferralBonus', 'Referans bonus geçmişi kaydı oluşturuldu.');

            // user_references tablosundaki bonus_earned field'ını güncelle (mevcut toplam + yeni bonus)
            $current_bonus = $this->db->where('referrer_id', $referrer_id)->where('buyer_id', $user->id)->get('user_references')->row()->bonus_earned ?? 0;
            $new_total_bonus = $current_bonus + $bonus_amount;
            addlog('M_Payment::processInvoiceReferralBonus', 'Mevcut toplam bonus: ' . $current_bonus . ' TL, Yeni bonus: ' . $bonus_amount . ' TL, Yeni toplam: ' . $new_total_bonus . ' TL');
            
            $this->db->where('referrer_id', $referrer_id)
                    ->where('buyer_id', $user->id)
                    ->set('bonus_earned', $new_total_bonus)
                    ->update('user_references');
            
            addlog('M_Payment::processInvoiceReferralBonus', 'user_references bonus_earned güncellendi. Toplam bonus: ' . $new_total_bonus . ' TL');

            // Transaction'ı kontrol et
            addlog('M_Payment::processInvoiceReferralBonus', 'Transaction durumu kontrol ediliyor...');
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                addlog('M_Payment::processInvoiceReferralBonus', 'Transaction başarısız oldu! Rollback yapıldı. Referrer: ' . $referrer_id . ', Alıcı: ' . $user->id . ', Fatura: ' . $invoice->id . ', Ürün: ' . $product->name . ' (Kaynak: ' . $bonus_source . ')');
                return false;
            }

            // Transaction'ı tamamla
            $this->db->trans_commit();
            addlog('M_Payment::processInvoiceReferralBonus', 'Transaction başarıyla tamamlandı.');

            // Başarılı işlemi logla
            addlog('M_Payment::processInvoiceReferralBonus', 'Referans bonus işlemi başarıyla tamamlandı! Referrer: ' . $referrer_id . ', Alıcı: ' . $user->id . ', Bonus: ' . $bonus_amount . ' TL, Fatura: ' . $invoice->id . ', Kaynak: ' . $bonus_source . ', Bakiye türü: ' . $balance_type);

            // Bildirim mesajı oluştur
            addlog('M_Payment::processInvoiceReferralBonus', 'Bildirim mesajı hazırlanıyor...');
            $balance_text = ($balance_type === 'withdrawable') ? ' çekilebilir bakiyenize' : ' bakiyenize';
            
            if ($bonus_source == 'kategori') {
                $notification_message = $user->name . ' ' . $user->surname . ' adlı kullanıcının ' . $product->name . ' (' . $product->category_name . ' kategorisi) ürün alışverişinden ' . number_format($bonus_amount, 2) . ' TL kategori bonusu' . $balance_text . ' eklendi!';
            } else {
                $notification_message = $user->name . ' ' . $user->surname . ' adlı kullanıcının ' . $product->name . ' ürün alışverişinden ' . number_format($bonus_amount, 2) . ' TL referans bonusu' . $balance_text . ' eklendi!';
            }
            addlog('M_Payment::processInvoiceReferralBonus', 'Bildirim mesajı: ' . $notification_message);

            // Referans veren kullanıcıya bildirim gönder
            addlog('M_Payment::processInvoiceReferralBonus', 'Kullanıcıya bildirim gönderiliyor. Referrer ID: ' . $referrer_id);
            sendNotificationSite(
                $referrer_id, 
                'Referans Bonusu', 
                $notification_message,
                base_url('client/reference')
            );
            addlog('M_Payment::processInvoiceReferralBonus', 'Bildirim başarıyla gönderildi.');

            addlog('M_Payment::processInvoiceReferralBonus', 'Referans bonus işlemi tamamen tamamlandı! İşlem başarılı.');
            return true;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            addlog('M_Payment::processInvoiceReferralBonus', 'HATA! Exception oluştu: ' . $e->getMessage() . ' | Referrer: ' . $referrer_id . ', Alıcı: ' . $user->id . ', Fatura: ' . $invoice->id . ', Ürün: ' . $product->name . ' (Kaynak: ' . $bonus_source . ')');
            return false;
        }
    }

    /**
     * Satın alma işlemi için bakiye çıkış kaydını oluşturur
     * 
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param float $userBalance İşlem öncesi bakiye
     * @param float $newBalance İşlem sonrası bakiye
     */
    private function createPurchaseTransaction($user, $shop, $userBalance, $newBalance) {
        // Kredi kartı ile ödemelerde bakiye kaydı oluşturulmamalı
        if ($shop->type == 'credit_card') {
            return;
        }
        
        $transaction_data = [
            'user_id' => $user->id,
            'transaction_type' => 'purchase',
            'amount' => -$shop->price,
            'description' => 'Ürün satın alımı - Sipariş No: ' . $shop->id,
            'status' => 1, // Onaylı
            'created_at' => date('Y-m-d H:i:s'),
            'balance_before' => $userBalance, // İşlem öncesi bakiye 
            'balance_after_transaction' => $newBalance, // Doğru güncellenmiş bakiye değeri
            'related_id' => $shop->id
        ];
        
        $this->db->insert('wallet_transactions', $transaction_data);
        
        // Satın alma işleminden sonra kullanıcının bakiyesini güncelle
        $this->db->where('id', $user->id)->update('user', ['balance' => $newBalance]);
    }

    /**
     * Sepet öğesini işler
     * 
     * @param array $c Ürün bilgileri
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param object $properties Site özellikleri
     * @param array $api_settings API ayarları
     * @param array &$productDetail Ürün detayları
     * @return string İşlem sonucu (pending, success)
     */
    private function processCartItem($c, $user, $shop, $properties, $api_settings, &$productDetail) {
        $product = $this->db->where('id', $c['product_id'])->get('product')->row();

        if ($product->isStock == 0) {
            $this->createInvoiceForOutOfStockProduct($c, $user, $shop, $properties, $productDetail);
            return "pending";
        } else {
            $stock = $this->db->where('product_id', $c['product_id'])->where('isActive', 1)->get('stock')->row();
            if ($stock) {
                $this->createInvoiceForStockProduct($c, $user, $shop, $properties, $product, $stock, $productDetail);
                return "success";
            } else {
                $this->handleAutoGiveProducts($c, $user, $shop, $properties, $api_settings, $productDetail, $product);
                return "pending";
            }
        }
    }

    /**
     * Stoksuz ürün için fatura oluşturur
     * 
     * @param array $c Ürün bilgileri
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param object $properties Site özellikleri
     * @param array &$productDetail Ürün detayları
     */
    private function createInvoiceForOutOfStockProduct($c, $user, $shop, $properties, &$productDetail) {
        $product = $this->db->where('id', $c['product_id'])->get('product')->row();
        $data = [
            'product_id' => $c['product_id'],
            'extras' => isset($c['extras']) ? $c['extras'] : null,
            'price' => $c['price'],
            'isComment' => 1,
            'isActive' => 1,
            'date' => date('Y-m-d H:i:s'),
            'balance' => $user->balance,
            'new_balance' => $user->balance,
            'shop_id' => $shop->id,
            'seller_id' => $product->seller_id,
            'invoice_provider' => $this->db->where(['api_name' => 'billing', 'setting_key' => 'provider'])->get('api_settings')->row()->setting_value,
            'payment_commission' => ($shop->payment_commission > 0) ? number_format(($c['price'] * getCommission()) / 100, 2, '.', '') : 0
        ];
        $this->db->insert('invoice', $data);
        $inv = $this->db->where('id', $this->db->insert_id())->get('invoice')->row();
        //createInvoiceInAPI($user, $inv);
        $productDetail[] = ['status' => 0, 'product' => $product->name, 'price' => $c['price']];
        
        // Eğer ürün bir pazaryeri ürünü ise (seller_id > 0) satıcı için wallet_transaction kaydı oluştur
        if ($product->seller_id > 0) {
            $this->createSellerWalletTransaction($product->seller_id, $c['price'], $shop->id, $inv->id);
        }
    }

    /**
     * Satıcı için wallet_transaction kaydı oluşturur
     * 
     * @param int $seller_id Satıcı ID
     * @param float $price Ürün fiyatı
     * @param int $shop_id Sipariş ID
     * @param int $invoice_id Fatura ID
     */
    private function createSellerWalletTransaction($seller_id, $price, $shop_id, $invoice_id) {
        $seller = $this->db->where('id', $seller_id)->get('user')->row();
        if (!$seller) {
            addlog('M_Payment::createSellerWalletTransaction', 'Seller not found with id: ' . $seller_id);
            return;
        }
        
        // Satıcının komisyon oranını hesapla
        $percent = ($price / 100) * $seller->shop_com;
        $seller_amount = $price - $percent;
        
        // Satıcı için beklemede olan wallet_transaction kaydını oluştur
        $transaction_data = [
            'user_id' => $seller_id,
            'transaction_type' => 'marketplace',
            'balance_type' => 'withdrawable',
            'amount' => $seller_amount,
            'description' => 'Pazaryeri satışı - Sipariş No: ' . $shop_id . ', Fatura No: ' . $invoice_id,
            'status' => 0, // Beklemede
            'created_at' => date('Y-m-d H:i:s'),
            'balance_before' => $seller->balance2, // Çekilebilir bakiye
            'balance_after_transaction' => $seller->balance2 + $seller_amount, // İşlem sonrası bakiye
            'related_id' => $invoice_id // İlgili fatura ID
        ];
        
        $this->db->insert('wallet_transactions', $transaction_data);
        addlog('M_Payment::createSellerWalletTransaction', 'Seller wallet transaction created for seller: ' . $seller_id . ', amount: ' . $seller_amount . ', invoice: ' . $invoice_id);
    }

    /**
     * Stoklu ürün için fatura oluşturur
     * 
     * @param array $c Ürün bilgileri
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param object $properties Site özellikleri
     * @param object $product Ürün nesnesi
     * @param object $stock Stok nesnesi
     * @param array &$productDetail Ürün detayları
     */
    private function createInvoiceForStockProduct($c, $user, $shop, $properties, $product, $stock, &$productDetail) {
        // Transaction başlat
        $this->db->trans_begin();
        
        try {
            $data = [
                'product' => $stock->product,
                'isActive' => 0,
                'isComment' => 1,
                'price' => $c['price'],
                'date' => date('Y-m-d H:i:s'),
                'balance' => $user->balance,
                'new_balance' => $user->balance,
                'product_id' => $c['product_id'],
                'shop_id' => $shop->id,
                'seller_id' => $product->seller_id,
                'last_refund' => date('Y-m-d H:i:s', strtotime("+1 days")),
                'invoice_provider' => $this->db->where(['api_name' => 'billing', 'setting_key' => 'provider'])->get('api_settings')->row()->setting_value,
                'payment_commission' => ($shop->payment_commission > 0) ? number_format(($c['price'] * getCommission($shop->user_id)) / 100, 2, '.', '') : 0
            ];
            
            $this->db->insert('invoice', $data);
            $invoice_id = $this->db->insert_id();
            
            // Stoğu işaretle
            $this->db->where('id', $stock->id)->update('stock', ['isActive' => 0]);
            
            // Eğer ürün bir pazaryeri ürünü ise (seller_id > 0) satıcı için wallet_transaction kaydı oluştur
            if ($product->seller_id > 0) {
                $this->createSellerWalletTransaction($product->seller_id, $c['price'], $shop->id, $invoice_id);
            }
            
            // Transaction'ı kontrol et
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                addlog('M_Payment::createInvoiceForStockProduct', 'Transaction failed for stock: ' . $stock->id);
                return false;
            }
            
            // Transaction'ı tamamla
            $this->db->trans_commit();
            
            $inv = $this->db->where('id', $invoice_id)->get('invoice')->row();
            createInvoiceInAPI($user, $inv);
            $productDetail[] = ['status' => 1, 'product' => $product->name, 'stock' => $stock->product, 'price' => $c['price']];

            // Başarıyla teslim edilen ürün için referans bonusu ver
            $this->processInvoiceReferralBonus($user, $inv, $shop);

            // Mail için datayı oluştur ve gönder
            $orderData = [
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'order_id' => $shop->order_id,
                'product_name' => $product->name,
                'product_price' => $c['price'],
                'product_code' => $stock->product,
                'date' => date('d.m.Y H:i')
            ];
            
            // Ürünün verildiği mailini gönder
            sendDeliveryNotification($user->email, $orderData);

            $this->alertLowStock($c['product_id'], $properties);
            
            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            addlog('M_Payment::createInvoiceForStockProduct', 'Exception: ' . $e->getMessage() . ' for stock: ' . $stock->id);
            return false;
        }
    }

    /**
     * Düşük stok uyarısı gönderir
     * 
     * @param int $product_id Ürün ID
     * @param object $properties Site özellikleri
     */
    private function alertLowStock($product_id, $properties) {
        $stockCount = $this->db->where('product_id', $product_id)->where('isActive', 1)->count_all_results('stock');
        if ($stockCount < 3 && $properties->stock == 1) {
            stockAlert('<div class="orius-mail">
                <div class="box">
                <h1 class="logo-text">'. $properties->name .'</h1>
                <h2>Stok Bilgilendirmesi</h2>
                <p>Ürünü için son '. $stockCount .' Stok Kaldı. Lütfen ekleme yapın.</p>
                </div>
                </div>');
        }
    }

    /**
     * Otomatik ürün teslim etme işlemlerini yönetir
     * 
     * @param array $c Ürün bilgileri
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param object $properties Site özellikleri
     * @param array $api_settings API ayarları
     * @param array &$productDetail Ürün detayları
     * @param object $product Ürün nesnesi
     */
    private function handleAutoGiveProducts($c, $user, $shop, $properties, $api_settings, &$productDetail, $product) {
        $this->load->helper('api');
        if ($properties->autoGive == 1) {
            if ($this->isTurkpinProduct($product, $properties)) {
                $detail = proccessTurkpinOrder($user, $shop, $product, $c['price']);
                $productDetail = array_merge($productDetail, $detail);
            } elseif ($this->isPinabiProduct($product, $api_settings)) {
                $detail = proccessPinabiOrder($user, $shop, $product, $c, count($productDetail));
                $productDetail = array_merge($productDetail, $detail);
            } elseif ($this->isCustomProviderProduct($product)) {
                $provider = $this->db->where('id', $product->product_provider)->get('product_providers')->row();
                $detail = proccessCustomProviderOrder($user, $shop, $product, $c['price']);
                $productDetail = array_merge($productDetail, $detail);
            } else {
                $this->createPendingProduct($c, $user, $shop, $properties, $productDetail, $product);
            }
        } else {
            $this->createPendingProduct($c, $user, $shop, $properties, $productDetail, $product);
        }
    }

    /**
     * Türkpin ürünü olup olmadığını kontrol eder
     * 
     * @param object $product Ürün nesnesi
     * @param object $properties Site özellikleri
     * @return bool Türkpin ürünü ise true
     */
    private function isTurkpinProduct($product, $properties) {
        return $product->game_code != 0 && $product->product_code != 0 &&
            $product->product_provider == "turkpin" &&
            (!empty($properties->turkpin_username) && !empty($properties->turkpin_password));
    }

    /**
     * Pinabi ürünü olup olmadığını kontrol eder
     * 
     * @param object $product Ürün nesnesi
     * @param array $api_settings API ayarları
     * @return bool Pinabi ürünü ise true
     */
    private function isPinabiProduct($product, $api_settings) {
        return $product->game_code != 0 && $product->product_code != 0 &&
            $product->product_provider == "pinabi" &&
            (!empty($api_settings['pinabi']->apiUser) && !empty($api_settings['pinabi']->secretKey) && !empty($api_settings['pinabi']->Authorization));
    }

    /**
     * Özel sağlayıcı ürünü olup olmadığını kontrol eder
     * 
     * @param object $product Ürün nesnesi
     * @return bool Özel sağlayıcı ürünü ise true
     */
    private function isCustomProviderProduct($product) {
        if ($product->game_code != 0 && $product->product_code != 0) {
            $provider = $this->db->where('id', $product->product_provider)->get('product_providers')->row();
            return $provider && $provider->is_active;
        }
        return false;
    }

    /**
     * Bekleyen ürün kaydı oluşturur
     * 
     * @param array $c Ürün bilgileri
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param object $properties Site özellikleri
     * @param array &$productDetail Ürün detayları
     * @param object $product Ürün nesnesi
     */
    private function createPendingProduct($c, $user, $shop, $properties, &$productDetail, $product) {
        $data = [
            'user_id' => $shop->user_id,
            'product_id' => $c['product_id'],
            'date' => date('Y-m-d H:i:s'),
            'balance' => $user->balance,
            'new_balance' => $user->balance - $c['price'],
            'isActive' => 1,
            'shop_id' => $shop->id,
            'price' => $c['price'],
            'invoice_provider' => $this->db->where(['api_name' => 'billing', 'setting_key' => 'provider'])->get('api_settings')->row()->setting_value,
            'payment_commission' => ($shop->payment_commission > 0) ? number_format(($c['price'] * getCommission($shop->user_id)) / 100, 2, '.', '') : 0,
        ];

        $this->db->insert('pending_product', $data);
        $productDetail[] = ['status' => 2, 'product' => $product->name, 'price' => $c['price']];
    }

    /**
     * Sepet işlemini sonlandırır
     * 
     * @param int $shop_id Sipariş ID
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param array $productDetail Ürün detayları
     * @param string $text Bildirim metni
     */
    private function finalizeShopCart($shop_id, $user, $shop, $productDetail, $text) {
        $this->db->where('id', $shop_id)->update('shop', ['status' => 0]);
    }

    /**
     * Bakiye yükleme başarılı e-postası gönderir
     * 
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     * @param float $newBalance Yeni bakiye
     */
    private function sendBalanceSuccessEmail($user, $shop, $newBalance) {
        $this->load->library('mailer');
        $this->mailer->send($user->email, 'balance_success', [
            'name' => $user->name,
            'surname' => $user->surname,
            'amount' => $shop->price,
            'currency' => 'TL',
            'transaction_date' => date('d.m.Y H:i'),
            'transaction_id' => $shop->order_id,
            'old_balance' => $user->balance,
            'new_balance' => $newBalance,
            'current_balance' => $newBalance
        ]);
    }

    /**
     * Abonelik kontrolü yapar ve gerekliyse iade işlemi gerçekleştirir
     * 
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     */
    private function checkSubscription($user, $shop) {
        //include helpers
        $this->load->helper('helpers');
        //hasUserFeature function
        $this->load->model('M_Subscription');
        $hasFeature = $this->M_Subscription->hasUserFeature($user->id, 'refund_value');
        if ($hasFeature) {
            //calculateUserRefund function
            $refundAmount = $this->M_Subscription->calculateUserRefund($user->id, $shop->price);
            
            if ($refundAmount <= 0) {
                return;
            }
            
            // En güncel kullanıcı bakiyesini al
            $currentUser = $this->db->where('id', $user->id)->get('user')->row();
            if (!$currentUser) {
                addlog('M_Payment::checkSubscription', 'User not found with id: ' . $user->id);
                return;
            }
            
            $currentBalance = $currentUser->balance;
            $newBalance = $currentBalance + $refundAmount;
            
            // Transaction başlat
            $this->db->trans_begin();
            
            try {
                // Kullanıcı bakiyesini güncelle
                $this->db->where('id', $user->id)->update('user', ['balance' => round($newBalance, 2)]);
                
                // İade işlemi için bakiye işlem kaydını oluştur
                $transaction_data = [
                    'user_id' => $user->id,
                    'transaction_type' => 'transfer_in',
                    'amount' => $refundAmount,
                    'description' => 'Abonelik avantajı - Satın alım iadesi',
                    'status' => 1, // Onaylı
                    'created_at' => date('Y-m-d H:i:s'),
                    'balance_before' => $currentBalance, // Güncel işlem öncesi bakiye
                    'balance_after_transaction' => $newBalance // Güncellenmiş bakiye
                ];
                
                $this->db->insert('wallet_transactions', $transaction_data);

                // Transaction'ı kontrol et
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    addlog('M_Payment::checkSubscription', 'Transaction failed for user: ' . $user->id . ' and shop: ' . $shop->id);
                    return;
                }

                // Transaction'ı tamamla
                $this->db->trans_commit();
                
                // İade işlemini logla
                addlog('M_Payment::checkSubscription', 'Abonelik iadesi eklendi. User ID: '.$user->id.', Miktar: '.$refundAmount.', Eski bakiye: '.$currentBalance.', Yeni bakiye: '.$newBalance);

                //getSubscriptionByUserId
                $subscription = $this->M_Subscription->getSubscriptionByUserId($user->id);

                if ($subscription){
                    //inserUserSavings helper
                    insertUserSavings($user->id, $subscription->subscription_id, $shop->id, 'Bakiye İadesi', $refundAmount, 'Ürün Bakiye İadesi', 'successful', date('Y-m-d H:i:s'));
                }
            } catch (Exception $e) {
                $this->db->trans_rollback();
                addlog('M_Payment::checkSubscription', 'Exception: ' . $e->getMessage() . ' for user: ' . $user->id . ' and shop: ' . $shop->id);
            }
        }
    }

    /**
     * Bayilik alım miktarını günceller
     * 
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     */
    private function updateDealerPurchase($user, $shop) {
        $this->load->model('M_Dealer');
        $this->M_Dealer->updateUserTotalPurchase($user->id, $shop->price);
        $this->M_Dealer->checkUpgradeEligibility($user->id);
    }

    /**
     * Kullanıcı tasarruf kayıtlarını ekler
     * 
     * @param object $user Kullanıcı nesnesi
     * @param object $shop Sipariş nesnesi
     */
    private function addUserSavingsForCommission($user, $shop) {
        // Add user savings record
        $this->load->model('M_Subscription');
        $userSaving = $this->M_Subscription->calculateSavingCommission($shop->id);
        $userSubscription = $this->M_Subscription->getSubscriptionByUserId($user->id);
        $properties = $this->db->where('id', 1)->get('properties')->row();
        
        //eğer shop altındaki type balance ise iptal et
        if ($shop->type != 'balance') {
            // Add earnings record for commission
            $this->addEarningsRecord($shop->id, 'payment_commission');
            // Add User Saving
            if ($this->M_Subscription->getSubscriptionByUserId($user->id)){
                if ($shop->type == 'credit_card')
                {
                    $normalPaymentCommission = number_format(($shop->price * $properties->commission) / 100, 2, '.', '');
                    $paymentCommission = number_format(($shop->price * $this->M_Subscription->getCommissionValue($shop->user_id)) / 100, 2, '.', '');
                    insertUserSavings($user->id, $userSubscription->subscription_id, $shop->id, 'Ödeme Komisyon Kazancı', $userSaving, 'Aboneliğiniz sayesinde ' . $normalPaymentCommission . ' TL yerine ' . $paymentCommission . ' TL ödediniz.', 'successful', date('Y-m-d H:i:s'));
                }else{
                    insertUserSavings($user->id, $userSubscription->subscription_id, $shop->id, 'Ödeme Komisyon Kazancı', $userSaving, $shop->type . ' Komisyon Kazancı', 'successful', date('Y-m-d H:i:s'));
                }
            }
        }
    }

    /**
     * Kullanıcı tasarruf kayıtlarını ekler
     * 
     * @param object $shop Sipariş nesnesi
     * @param object $user Kullanıcı nesnesi
     */
    private function handleUserSavings($shop, $user) {
        $this->load->model('M_Subscription');
        if ($this->M_Subscription->getSubscriptionByUserId($user->id)) {
            $userSaving = $this->M_Subscription->calculateSavingCommission($shop->id);
            $userSubscription = $this->M_Subscription->getSubscriptionByUserId($user->id);
            $properties = $this->db->where('id', 1)->get('properties')->row();
            
            $normalPaymentCommission = number_format(($shop->price * $properties->commission) / 100, 2, '.', '');
            $paymentCommission = number_format(($shop->price * $this->M_Subscription->getCommissionValue($shop->user_id)) / 100, 2, '.', '');
            insertUserSavings(
                $user->id, $userSubscription->subscription_id, $shop->id, 'Ödeme Komisyon Kazancı', $userSaving, 'Aboneliğiniz sayesinde ' . $normalPaymentCommission . ' TL yerine ' . $paymentCommission . ' TL ödediniz.', 'successful', date('Y-m-d H:i:s')
            );
        }
    }

    /**
     * Kazanç kaydı ekler
     * 
     * @param int $shop_id Sipariş ID
     * @param string $payment_type Ödeme türü
     */
    private function addEarningsRecord($shop_id, $payment_type) {
        $this->load->model('M_Earnings');
        $this->M_Earnings->insertEarning($shop_id, $payment_type);
    }

    /**
     * Sipariş için bildirimleri gönderir
     * 
     * @param int $shop_id Sipariş ID
     * @return bool İşlem başarılı ise true, değilse false
     */
    private function sendNotifications($shop_id) {
        $this->load->library('mailer');

        $shop = $this->db->where('id', $shop_id)->get('shop')->row();
        if (!$shop) return false;

        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        if (!$user) return false;

        // Sipariş detaylarını al
        $pendingProducts = $this->db->where('shop_id', $shop_id)->get('pending_product')->result();
        $invoices = $this->db->where('shop_id', $shop_id)->get('invoice')->result();

        $productsContent = '';
        foreach($pendingProducts as $product) {
            $productInfo = $this->db->where('id', $product->product_id)->get('product')->row();
            $productsContent .= '<tr>
                <td style="padding: 20px; border-bottom: 1px solid #e5e7eb;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="60">
                                <img src="'.base_url('assets/img/product/') . $productInfo->img.'" 
                                     style="width: 60px; height: 60px; border-radius: 8px; object-fit: cover;">
                            </td>
                            <td style="padding-left: 15px;">
                                <div style="color: #1a202c; font-weight: 500;">'.$productInfo->name.'</div>
                            </td>
                            <td width="100" align="center">
                                <span style="color: #64748b;">Adet:</span>
                                <div style="color: #1a202c; font-weight: 500;">1</div>
                            </td>
                            <td width="120" align="right">
                                <span style="color: #1a202c; font-weight: 500;">'.number_format($product->price, 2).' TL</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>';
        }

        foreach($invoices as $invoice) {
            $productInfo = $this->db->where('id', $invoice->product_id)->get('product')->row();
            $productsContent .= '<tr style="border-bottom: 1px solid #edf2f7;">
                <td style="padding: 12px 15px; color: #4a5568;">'.$productInfo->name.'</td> 
                <td style="padding: 12px 15px; text-align: center; color: #4a5568;">1</td>
                <td style="padding: 12px 15px; text-align: right; color: #4a5568;">'.number_format($invoice->price, 2).' TL</td>
            </tr>';
        }

        setlocale(LC_TIME, 'tr_TR.UTF-8');
        $date = date('d.m.Y H:i', strtotime($shop->date));

        $orderData = [
            'site_name' => $this->db->where('id', 1)->get('properties')->row()->name,
            'name' => $user->name . ' ' . $user->surname,
            'order_id' => $shop->id,
            'products' => $productsContent,
            'total_amount' => number_format($shop->price, 2),
            'date' => $date
        ];

        $this->mailer->send(
            $user->email,
            'new_order',
            $orderData,
            1
        );

        if($shop->type == 'deposit'){
            $userMessage = "Bakiye yükleme talebiniz tamamlandı. Şimdi harcama zamanı!";
        }else{
            $userMessage = "Siparişiniz başarıyla alındı. Sipariş No: " . $shop->order_id;
        }

        sendNotificationSite($user->id, 'Sistem Bildirimi', $userMessage, base_url('client/product'));
        
        return true;
    }

    /**
     * Ödeme yöntemi adını döndürür
     */
    private function getPaymentMethodName($payment_method_id) {
        // SQL Injection önlemi - payment_method_id'yi integer'a dönüştür
        $payment_method_id = intval($payment_method_id);
        $payment = $this->db->where('id', $payment_method_id)->get('payment')->row();
        return $payment ? $payment->payment_name : 'Bilinmeyen';
    }
}
