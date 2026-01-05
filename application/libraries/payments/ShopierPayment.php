<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/payments/BasePayment.php';

/**
 * ShopierPayment - Shopier ödeme geçidi entegrasyonu
 */
class ShopierPayment extends BasePayment {
    
    /**
     * Bu ödeme sağlayıcısının callback için özel işaretlerini döndürür
     * 
     * @return array
     */
    public function getCallbackSignatures() {
        return ['platform_order_id'];
    }
    
    /**
     * Ödeme sayfasını oluşturur
     * 
     * @param array $params Ödeme parametreleri
     * @return string Ödeme HTML
     */
    public function createPayment($params) {
        require_once APPPATH . 'libraries/Shopier.php';

        // API Bilgileri
        $api_key = $this->getConfigValue('api_key');
        $secret_key = $this->getConfigValue('secret_key');
        
        // Kullanıcı Bilgileri
        $user = $params['user'];
        
        // Shopier nesnesi oluştur
        $shopier = new Shopier($api_key, $secret_key);
        
        // Kullanıcı bilgilerini ayarla
        $shopier->setBuyer([
            'id' => $user->id,
            'first_name' => $user->name,
            'last_name' => $user->surname,
            'email' => $user->email,
            'phone' => $user->phone,
            'price' => $params['amount']
        ]);
        
        // Fatura bilgilerini ayarla
        $shopier->setOrderBilling([
            'billing_address' => 'N/A - Dijital Ürün',
            'billing_city' => 'Istanbul',
            'billing_country' => 'Turkey',
            'billing_postcode' => '34000',
        ]);
        
        // Kargo bilgilerini ayarla
        $shopier->setOrderShipping([
            'shipping_address' => 'N/A - Dijital Ürün',
            'shipping_city' => 'Istanbul',
            'shipping_country' => 'Turkey',
            'shipping_postcode' => '34000',
        ]);

        // Ödeme formunu oluştur ve göster
        return $shopier->run(
            $params['order_id'],
            $params['amount'],
            base_url('payment/callback')
        );
    }
    
    /**
     * Callback sonuçlarını işle
     * 
     * @param array $post_data POST verileri
     * @return array İşlem sonucu
     */
    public function handleCallback($post_data) {
        require_once APPPATH . 'libraries/Shopier.php';
        
        // API Bilgileri
        $api_key = $this->getConfigValue('api_key');
        $secret_key = $this->getConfigValue('secret_key');
        
        $shopier = new Shopier($api_key, $secret_key);
        
        if ($shopier->verifyShopierSignature($post_data)) {
            return [
                'status' => true,
                'message' => 'Ödeme başarılı',
                'order_id' => $post_data['platform_order_id']
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Ödeme doğrulanamadı',
                'order_id' => $post_data['platform_order_id']
            ];
        }
    }
} 