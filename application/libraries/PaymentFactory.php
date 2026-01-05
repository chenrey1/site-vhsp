<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PaymentFactory - Ödeme sistemleri için fabrika sınıfı
 */
class PaymentFactory {
    protected $CI;

    /**
     * Constructor
     */
    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    /**
     * Ödeme sağlayıcı sınıfını döndürür
     * 
     * @param int $payment_id Ödeme sağlayıcı ID
     * @return mixed Ödeme sağlayıcı sınıfı örneği
     */
    public function getPaymentProvider($payment_id = null) {
        // SQL Injection önlemi
        if ($payment_id !== null) {
            $payment_id = intval($payment_id);
        }
        // Belirli bir ödeme yöntemi istenmemişse varsayılan olanı al
        if ($payment_id === null) {
            $payment = $this->CI->db
                ->where('status', 1)
                ->where('is_default', 1)
                ->get('payment')
                ->row();
            
            // Hiç varsayılan yoksa, ilk aktif olanı al
            if (!$payment) {
                $payment = $this->CI->db
                    ->where('status', 1)
                    ->order_by('display_order', 'ASC')
                    ->get('payment')
                    ->row();
            }
            
            $payment_id = $payment ? $payment->id : 1; // Hiç aktif yoksa 1 varsayalım
        }
        
        // Ödeme yöntemini veritabanından al
        $payment = $this->CI->db
            ->where('id', $payment_id)
            ->get('payment')
            ->row();
            
        if (!$payment) {
            return null;
        }
        
        // Sınıf adını kontrol et
        $className = $payment->class_name ?? null;
        
        if ($className === null || empty($className)) {
            return null;
        }
        
        // İlgili kütüphaneyi yükle
        $libPath = APPPATH . 'libraries/payments/' . $className . '.php';
        if (!file_exists($libPath)) {
            return null;
        }
        
        require_once $libPath;
        
        // JSON config değerini doğru bir şekilde işle
        $config_json = $payment->config ?? null;
        $config_array = [];
         
        if ($config_json) {
            if (is_string($config_json)) {
                $config_array = json_decode($config_json, true) ?? [];
            } else {
                // MySQL JSON tipi otomatik olarak PHPde nesneye dönüşmüş olabilir
                $config_array = json_decode(json_encode($config_json), true) ?? [];
            }
        }

        // Temel config değerlerini oluştur
        $config = [
            'id' => $payment->id,
            'payment_name' => $payment->payment_name,
            'commission_rate' => $payment->commission_rate,
            'config' => $config_array
        ];
        
        // Ödeme sağlayıcı sınıfını başlat
        return new $className($config);
    }

    /**
     * Callback verileri üzerinden ödeme sağlayıcısını belirler
     * 
     * @param array $post_data POST verileri
     * @return mixed Ödeme sağlayıcı sınıfı örneği
     */
    public function getPaymentProviderByCallback($post_data) {
        // Önce raw input verilerini kontrol et (JSON formatında olabilir)
        $rawInput = file_get_contents('php://input');
        $jsonData = null;
        
        if (!empty($rawInput)) {
            $jsonData = json_decode($rawInput, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                // Eğer JSON verisi başarıyla çözümlendiyse, post_data'ya ekle
                $post_data = array_merge($post_data, $jsonData);
                
                if (function_exists('addlog')) {
                    addlog('PaymentFactory - JSON Verisi', 'Raw JSON verisi çözümlendi: ' . json_encode($jsonData));
                }
            } else if (function_exists('addlog')) {
                addlog('PaymentFactory - JSON Hatası', 'Raw input çözümlenemedi: ' . json_last_error_msg());
            }
        }
        
        // Önce sipariş ID'si içeren bir post veri varsa, doğrudan bu siparişin ödeme yöntemini bul
        if (isset($post_data['merchant_oid'])) {
            // shop tablosundan sipariş ID'sine göre ödeme yöntemi ID'sini sorgula
            $shop = $this->CI->db
                ->where('order_id', $post_data['merchant_oid'])
                ->get('shop')
                ->row();
                
            if ($shop && isset($shop->payment_method_id) && $shop->payment_method_id > 0) {
                // Siparişte kayıtlı olan ödeme yöntemini kullan
                $provider = $this->getPaymentProvider($shop->payment_method_id);
                
                if ($provider) {
                    return $provider;
                }
            }
        }
        
        // Pay2out için özel kontrol - referans, order_number veya payment_id varsa
        if (isset($post_data['reference']) || isset($post_data['order_number']) || isset($post_data['payment_id'])) {
            $reference_id = isset($post_data['reference']) ? $post_data['reference'] : 
                           (isset($post_data['order_number']) ? $post_data['order_number'] : 
                           (isset($post_data['payment_id']) ? $post_data['payment_id'] : null));
            
            if ($reference_id) {
                // Shop tablosundan order_id'ye göre payment_method_id'yi bul
                $shop = $this->CI->db
                    ->where('order_id', $reference_id)
                    ->get('shop')
                    ->row();
                    
                if ($shop && isset($shop->payment_method_id) && $shop->payment_method_id > 0) {
                    $provider = $this->getPaymentProvider($shop->payment_method_id);
                    
                    if ($provider && function_exists('addlog')) {
                        addlog('PaymentFactory - Provider Bulundu', 'Shop verisi ile provider bulundu: ' . get_class($provider));
                    }
                    
                    if ($provider) {
                        return $provider;
                    }
                }
            }
        }
        
        // Eğer yukarıdaki yöntemle belirlenemezse, eski yöntemi kullan
        
        // 1. Önce PayTR Havale için PayTR'nin ödeme türünü kontrol et
        if (isset($post_data['payment_type']) && $post_data['payment_type'] == 'eft') {
            // Havale/EFT ödemesi
            $paytr_havale = $this->CI->db
                ->where('class_name', 'PaytrHavale')
                ->where('status', 1)
                ->get('payment')
                ->row();
                
            if ($paytr_havale) {
                return $this->getPaymentProvider($paytr_havale->id);
            }
        }
        
        // 2. Sonra aktif tüm ödeme sağlayıcılarını kontrol et
        $payment_methods = $this->CI->db
            ->where('status', 1)
            ->get('payment')
            ->result();
            
        foreach ($payment_methods as $payment) {
            try {
                if (empty($payment->class_name)) {
                    continue;
                }
                
                // Ödeme sağlayıcısının örneğini oluştur
                $provider = $this->getPaymentProvider($payment->id);
                
                if (!$provider) {
                    continue;
                }
                
                // Callback işaretlerini al
                $signatures = $provider->getCallbackSignatures();
                
                // Eğer bu işaretlerden herhangi biri post_data içinde varsa, bu ödeme sağlayıcısı doğru olanıdır
                foreach ($signatures as $key) {
                    if (isset($post_data[$key])) {
                        if (function_exists('addlog')) {
                            addlog('PaymentFactory - Provider Bulundu', 'Signature ' . $key . ' ile provider bulundu: ' . get_class($provider));
                        }
                        return $provider;
                    }
                }
            } catch (Exception $e) {
                // Hata durumunda bir sonraki sağlayıcıya geç
                if (function_exists('addlog')) {
                    addlog('PaymentFactory - Hata', 'Provider kontrol edilirken hata: ' . $e->getMessage());
                }
                continue;
            }
        }
        
        if (function_exists('addlog')) {
            addlog('PaymentFactory - Bulunamadı', 'Callback için ödeme sağlayıcısı belirlenemedi. Veriler: ' . json_encode($post_data));
        }
        
        throw new Exception('Callback için ödeme sağlayıcısı belirlenemedi');
    }

    /**
     * Tüm aktif ödeme yöntemlerini döndürür
     * 
     * @return array Aktif ödeme yöntemleri
     */
    public function getActivePaymentMethods() {
        return $this->CI->db
            ->where('status', 1)
            ->order_by('display_order', 'ASC') // Sonra görüntüleme sırasına göre
            ->get('payment')
            ->result();
    }

    /**
     * Varsayılan ödeme yönteminin ID'sini döndürür
     * 
     * @return int Varsayılan ödeme yöntemi ID'si
     */
    public function getDefaultPaymentMethodId() {
        $payment = $this->CI->db
            ->where('status', 1)
            ->where('is_default', 1)
            ->get('payment')
            ->row();
            
        if (!$payment) {
            // Varsayılan yoksa ilk aktif olanı kullan
            $payment = $this->CI->db
                ->where('status', 1)
                ->order_by('display_order', 'ASC')
                ->get('payment')
                ->row();
        }
        
        return $payment ? $payment->id : 1; // Hiç aktif yoksa 1 varsayalım
    }

    /**
     * Belirli bir ödeme yöntemi ve kullanıcı için komisyon oranını hesaplar
     * 
     * @param int $payment_id Ödeme yöntemi ID'si
     * @param int $user_id Kullanıcı ID'si
     * @return float Komisyon oranı
     */
    public function getCommissionRate($payment_id, $user_id) {
        // Artık tüm komisyon hesaplama işi getCommission helper'ına devredildi
        // Tutarlılık için direkt helper fonksiyonunu kullanıyoruz
        return getCommission($user_id, $payment_id);
    }
} 