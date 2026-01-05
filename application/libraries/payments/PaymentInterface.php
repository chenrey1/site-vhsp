<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PaymentInterface - Tüm ödeme sağlayıcılarının uygulaması gereken arayüz
 */
interface PaymentInterface {
    /**
     * Ödeme sayfasını oluşturur ve döndürür
     * 
     * @param array $params Ödeme parametreleri (kullanıcı bilgileri, tutar vb.)
     * @return string Ödeme sayfası HTML içeriği
     */
    public function createPayment($params);
    
    /**
     * Ödeme geri dönüş işlemlerini yapar
     * 
     * @param array $data Geri dönüş verileri
     * @return array İşlem sonucu ['status' => true/false, 'message' => 'açıklama']
     */
    public function handleCallback($data);

    /**
     * Bu ödeme sağlayıcısının callback için özel işaretlerini döndürür
     * 
     * @return array İşaretler dizisi (callback parametreleri)
     */
    public function getCallbackSignatures();
    
    /**
     * Ödeme yöntemi adını döndürür
     * 
     * @return string Ödeme yöntemi adı
     */
    public function getPaymentName();

    /**
     * Yapılandırma değerini döndürür
     * 
     * @param string $key İstenilen yapılandırma anahtarı
     * @param mixed $default Değer bulunamazsa dönecek varsayılan değer
     * @return mixed Yapılandırma değeri
     */
    public function getConfigValue($key, $default = null);

    /**
     * Ödeme yöntemine ait komisyon oranını döndürür
     * 
     * @param int $user_id Kullanıcı ID (isteğe bağlı - özel komisyon oranları için)
     * @return float Komisyon oranı
     */
    public function getCommissionRate($user_id = null);
} 