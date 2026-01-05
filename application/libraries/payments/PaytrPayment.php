<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/payments/BasePayment.php';

/**
 * PaytrPayment - PayTR ödeme geçidi entegrasyonu
 */
class PaytrPayment extends BasePayment {
    
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
            ["Dijital Ürün", $params['amount'], 1]
        ]));
        
        // Diğer Bilgiler
        $user_ip = $params['ip_address'];
        $timeout_limit = "30";
        $debug_on = 0;
        $test_mode = 0;
        $no_installment = 1; 
        $max_installment = 0;
        $user_name = $user->name . " " . $user->surname;
        $user_address = "Istanbul";
        $user_phone = $user->phone;
        $email = $user->email;
        $merchant_ok_url = base_url('client/success');
        $merchant_fail_url = base_url('client/fail');
        $currency = "TL";
        
        // Token Oluşturma
        $hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
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
            'no_installment' => $no_installment,
            'max_installment' => $max_installment,
            'user_name' => $user_name,
            'user_address' => $user_address,
            'user_phone' => $user_phone,
            'merchant_ok_url' => $merchant_ok_url,
            'merchant_fail_url' => $merchant_fail_url,
            'timeout_limit' => $timeout_limit,
            'currency' => $currency,
            'test_mode' => $test_mode
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
            return "PAYTR IFRAME connection error. err:" . curl_error($ch);
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
            return "PAYTR IFRAME failed. reason:" . $reason;
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
        addlog('PaytrPayment callback', 'Callback verileri: ' . json_encode($post_data));
        
        // Ödeme türünü kontrol et (kredi kartı ödemesi için)
        // Havale ödemesi değilse devam et
        if (isset($post_data['payment_type']) && $post_data['payment_type'] == 'eft') {
            addlog('PaytrPayment callback', 'Bu bir havale ödemesi, PaytrPayment tarafından işlenmemeli');
            return [
                'status' => false,
                'message' => 'Bu bir havale ödemesi, bu ödeme sağlayıcı tarafından işlenmemeli'
            ];
        }
        
        $hash = base64_encode(hash_hmac('sha256', $post_data['merchant_oid'] . $merchant_salt . $post_data['status'] . $post_data['total_amount'], $merchant_key, true));
        
        if ($hash != $post_data['hash']) {
            return [
                'status' => false,
                'message' => 'PAYTR notification failed: bad hash'
            ];
        }
        
        // Siparişi veritabanından bul
        $shop = $this->CI->db
            ->where('order_id', $post_data['merchant_oid'])
            ->get('shop')
            ->row();
            
        if ($shop && isset($shop->payment_method_id) && $shop->payment_method_id != $this->config['id']) {
            addlog('PaytrPayment callback', 'Ödeme yöntemi uyuşmazlığı. Beklenen: ' . $this->config['id'] . ', Gelen: ' . $shop->payment_method_id);
            return [
                'status' => false,
                'message' => 'Ödeme yöntemi uyuşmazlığı'
            ];
        }
        
        if ($post_data['status'] == 'success') {
            return [
                'status' => true,
                'message' => 'Ödeme başarılı',
                'order_id' => $post_data['merchant_oid'],
                'amount' => isset($shop->price) ? $shop->price : ($post_data['total_amount'] / 100),
                'user_id' => isset($shop->user_id) ? $shop->user_id : null
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Ödeme başarısız',
                'order_id' => $post_data['merchant_oid']
            ];
        }
    }
} 