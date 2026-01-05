<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/payments/BasePayment.php';

/**
 * PaytrHavale - PayTR havale/EFT ödeme geçidi entegrasyonu
 */
class PaytrHavale extends BasePayment {
    
    /**
     * Bu ödeme sağlayıcısının callback için özel işaretlerini döndürür
     * 
     * @return array
     */
    public function getCallbackSignatures() {
        return ['merchant_oid'];
    }
    
    /**
     * Ödeme sayfasını oluşturur
     * 
     * @param array $params Ödeme parametreleri
     * @return string Ödeme iframe HTML
     */
    public function createPayment($params) {
        // API Bilgileri
        $merchant_id = $this->getConfigValue('merchant_id');
        $merchant_key = $this->getConfigValue('merchant_key');
        $merchant_salt = $this->getConfigValue('merchant_salt');
        
        // Kullanıcı Bilgileri
        $user = $params['user'];
        
        // Sipariş Bilgileri
        $merchant_oid = $params['order_id'];
        $payment_amount = $params['amount'] * 100; // 9.99 için 9.99 * 100 = 999 gönderilmeli

        // Müşterinin sepet/sipariş içeriği
        $user_basket = base64_encode(json_encode([
            ["Bakiye Yükleme", $params['amount'], 1]
        ]));
        
        // Diğer Bilgiler
        $user_ip = $params['ip_address'];
        $timeout_limit = "30";
        $debug_on = 0;
        $test_mode = 0;
        $user_name = $user->name . " " . $user->surname;
        $user_address = "Istanbul";
        $user_phone = $user->phone;
        $email = $user->email;
        
        // Callback URL - Payment ID'yi ekliyoruz
        $callback_url = isset($params['callback_url']) ? $params['callback_url'] : base_url('payment/callback/' . $this->config['id']);
        
        // Başarılı ve başarısız URL'leri
        $merchant_ok_url = isset($params['merchant_ok_url']) ? $params['merchant_ok_url'] : base_url('client/balance');
        $merchant_fail_url = isset($params['merchant_fail_url']) ? $params['merchant_fail_url'] : base_url('client/balance');
        
        $currency = "TL";
        
        // Havale modu
        $payment_type = 'eft';
        
        // Token Oluşturma
        $hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $payment_type . $currency . $test_mode;
        $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));
        
        $post_vals = [
            'merchant_id' => $merchant_id,
            'user_ip' => $user_ip,
            'merchant_oid' => $merchant_oid,
            'email' => $email,
            'payment_amount' => $payment_amount,
            'paytr_token' => $paytr_token,
            'user_basket' => $user_basket,
            'debug_on' => $debug_on,
            'user_name' => $user_name,
            'user_address' => $user_address,
            'user_phone' => $user_phone,
            'merchant_ok_url' => $merchant_ok_url,
            'merchant_fail_url' => $merchant_fail_url,
            'timeout_limit' => $timeout_limit,
            'currency' => $currency,
            'test_mode' => $test_mode,
            'payment_type' => $payment_type,
            'notification_url' => $callback_url
        ];
        
        // API İsteği
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        
        $result = @curl_exec($ch);
        
        if (curl_errno($ch)) {
            return "PAYTR IFRAME bağlantı hatası. Hata: " . curl_error($ch);
        }
        
        curl_close($ch);
        $result = json_decode($result, 1);
        
        if ($result && isset($result['status']) && $result['status'] == 'success') {
            $token = $result['token'];
            return '<script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
                    <iframe src="https://www.paytr.com/odeme/guvenli/' . $token . '" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%;"></iframe>
                    <script>iFrameResize({},\'#paytriframe\');</script>';
        } else {
            $reason = isset($result['reason']) ? $result['reason'] : 'PayTR API yanıt vermedi veya hatalı yanıt döndü';
            return "PAYTR IFRAME başarısız. Sebep: " . $reason;
        }
    }
    
    /**
     * Callback sonuçlarını işle
     * 
     * @param array $post_data POST verileri
     * @return array İşlem sonucu
     */
    public function handleCallback($post_data) {
        $merchant_key = $this->getConfigValue('merchant_key');
        $merchant_salt = $this->getConfigValue('merchant_salt');
        
        // Gelen verileri logla
        addlog('PaytrHavale callback', 'Callback verileri: ' . json_encode($post_data));
        
        // Ödeme türünü kontrol et (PayTR havale geri dönüşünde payment_type veya benzer bir alan olmalı)
        // NOT: Eğer PayTR havale için özel bir alan dönmüyorsa bu kontrol çalışmayacaktır
        if (isset($post_data['payment_type']) && $post_data['payment_type'] != 'eft') {
            addlog('PaytrHavale callback', 'Havale işlemi değil, farklı bir ödeme türü: ' . $post_data['payment_type']);
            return [
                'status' => false,
                'message' => 'Bu ödeme türü havale/EFT değil'
            ];
        }
        
        // Hash doğrulama
        $hash = base64_encode(hash_hmac('sha256', $post_data['merchant_oid'] . $merchant_salt . $post_data['status'] . $post_data['total_amount'], $merchant_key, true));
        
        if ($hash != $post_data['hash']) {
            return [
                'status' => false,
                'message' => 'PAYTR bildirimi başarısız: hash doğrulanamadı'
            ];
        }
        
        // Siparişi veritabanından bul
        $shop = $this->CI->db
            ->where('order_id', $post_data['merchant_oid'])
            ->get('shop')
            ->row();
            
        if (!$shop) {
            return [
                'status' => false,
                'message' => 'Sipariş bulunamadı: ' . $post_data['merchant_oid']
            ];
        }
        
        // Ödeme yöntemi ID'sini kontrol et
        if (isset($shop->payment_method_id) && $shop->payment_method_id != $this->config['id']) {
            addlog('PaytrHavale callback', 'Ödeme yöntemi uyuşmazlığı. Beklenen: ' . $this->config['id'] . ', Gelen: ' . $shop->payment_method_id);
            return [
                'status' => false,
                'message' => 'Ödeme yöntemi uyuşmazlığı'
            ];
        }
        
        // İşlem durumunu kontrol et
        if ($post_data['status'] == 'success') {
            // Başarılı ödeme
            return [
                'status' => true,
                'message' => 'Ödeme başarılı',
                'order_id' => $post_data['merchant_oid'],
                'amount' => $shop->price,
                'user_id' => $shop->user_id
            ];
        } else {
            // Başarısız ödeme
            return [
                'status' => false,
                'message' => 'Ödeme başarısız',
                'order_id' => $post_data['merchant_oid']
            ];
        }
    }
} 