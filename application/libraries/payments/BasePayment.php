<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/payments/PaymentInterface.php';

/**
 * BasePayment - Ödeme sağlayıcıları için temel sınıf
 */
abstract class BasePayment implements PaymentInterface {
    protected $CI;
    protected $config;
    
    /**
     * BasePayment Constructor
     * 
     * @param array $config Ödeme sağlayıcı yapılandırma dizisi
     */
    public function __construct($config) {
        $this->CI =& get_instance();
        $this->config = $config;
        
        // Veritabanı ve session kütüphanelerini yükle
        $this->CI->load->database();
        $this->CI->load->library('session');
    }
    
    /**
     * Yapılandırma değerini döndürür
     * 
     * @param string $key İstenilen yapılandırma anahtarı
     * @param mixed $default Değer bulunamazsa dönecek varsayılan değer
     * @return mixed Yapılandırma değeri
     */
    public function getConfigValue($key, $default = null) {
        // Önce JSON config içinde ara
        if (isset($this->config['config']) && is_array($this->config['config']) && isset($this->config['config'][$key])) {
            return $this->config['config'][$key];
        }
        
        return $default;
    }
    
    /**
     * Bu ödeme sağlayıcısının callback için özel işaretlerini döndürür
     * Her alt sınıf bunu kendi ihtiyaçlarına göre ezmeli
     * 
     * @return array Örn: ['merchant_oid', 'platform_order_id', 'ORDER_REF_NUMBER']
     */
    public function getCallbackSignatures() {
        return [];
    }
    
    /**
     * Ödeme yöntemine ait komisyon oranını döndürür
     * 
     * @param int $user_id Kullanıcı ID (isteğe bağlı - özel komisyon oranları için)
     * @return float Komisyon oranı
     */
    public function getCommissionRate($user_id = null) {
        // Varsayılan komisyon oranını veri tabanından çek
        $commission_rate = $this->config['commission_rate'] ?? 0;
        
        // Kullanıcıya özel komisyon oranı kontrolü yapılabilir
        /*if ($user_id) {
            // Örnek: Kullanıcının bayilik durumuna göre indirimli komisyon
            $user = $this->CI->db->where('id', $user_id)->get('user')->row();
            if ($user && isset($user->discount) && $user->discount > 0) {
                // Örnek olarak, kullanıcı indirim oranının %10'u kadar komisyon indirimi
                $commission_rate -= ($user->discount * 0.1);
                
                // Minimum komisyon oranı kontrolü
                if ($commission_rate < 0.5) {
                    $commission_rate = 0.5; // Minimum %0.5 komisyon
                }
            }
        }*/
        
        return $commission_rate;
    }
    
    /**
     * Ödeme yöntemi adını döndürür
     * 
     * @return string Ödeme yöntemi adı
     */
    public function getPaymentName() {
        return isset($this->config['payment_name']) ? $this->config['payment_name'] : 'Ödeme';
    }
} 