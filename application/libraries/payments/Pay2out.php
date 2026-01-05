<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// PaymentInterface'i dahil et
require_once APPPATH . 'libraries/payments/PaymentInterface.php';

/**
 * Pay2out - Pay2out ödeme sağlayıcısı
 */
class Pay2out implements PaymentInterface {
    protected $CI;
    protected $config;
    
    /**
     * Constructor
     * 
     * @param array $config Yapılandırma değerleri
     */
    public function __construct($config = []) {
        $this->CI =& get_instance();
        $this->config = $config;
        $this->CI->load->helper('url');
    }
    
    /**
     * Ödeme sayfasını oluşturur ve döndürür
     * 
     * @param array $params Ödeme parametreleri (kullanıcı bilgileri, tutar vb.)
     * @return string Ödeme sayfası HTML içeriği
     */
    public function createPayment($params) {
        // Gerekli parametreleri doğrula
        if (!isset($params['amount']) || !isset($params['order_id']) || !isset($params['user'])) {
            throw new Exception('Eksik ödeme parametreleri');
        }
        
        // Zorunlu parametreler
        $amount = $params['amount'];
        $order_id = $params['order_id'];
        $user = $params['user'];
        $ip_address = isset($params['ip_address']) ? $params['ip_address'] : '';
        
        // Opsiyonel parametreler
        $callback_url = isset($params['callback_url']) ? $params['callback_url'] : base_url('payment/callback');
        $ok_url = isset($params['merchant_ok_url']) ? $params['merchant_ok_url'] : base_url('client/success');
        $fail_url = isset($params['merchant_fail_url']) ? $params['merchant_fail_url'] : base_url('client/fail');
        
        // Pay2out API bilgileri (config'den al)
        $signature_secret = $this->getConfigValue('api_key', '');
        
        // API istek parametreleri hazırlanıyor
        $postData = [
            "signature_secret" => $signature_secret,
            "amount" => $amount,
            "currency" => "TRY",
            "description" => "Dijital Ürün Ödemesi",
            "has_installment" => false,
            "customer_name" => $user->name . " " . $user->surname,
            "customer_email" => $user->email,
            "customer_phone" => $user->phone,
            "customer_address" => "İstanbul Merkez / 34000",
            "customer_city" => "İstanbul",
            "customer_district" => "Merkez",
            "customer_postal_code" => "34000",
            "reference" => $order_id,
            "order_number" => $order_id,
            "expires_at" => date('Y-m-d H:i:s', strtotime('+1 day')),
            "callback_url" => $callback_url,
            "success_url" => $ok_url,
            "cancel_url" => $fail_url
        ];
        
        $ch = curl_init('https://www.pay2out.com/api/payment/create');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($postData))
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $result = json_decode($response, true);
        
        // Log oluştur
        if (function_exists('addlog')) {
            addlog('Ödeme - Pay2out', 'İstek: ' . json_encode($postData) . ', Yanıt: ' . json_encode($result));
        }
        
        // Başarılı yanıt kontrolü
        if (isset($result['success']) && $result['success'] === true) {
            // Ödeme sayfasına yönlendirme HTML'i
            $redirectUrl = $result['payment_url'];
            
            $html = '<html>
            <head>
                <title>Pay2out Ödeme Sayfasına Yönlendiriliyor</title>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            </head>
            <body>
                <div style="text-align:center; margin-top:50px;">
                    <h3>Pay2out Ödeme Sayfasına Yönlendiriliyor...</h3>
                    <img src="' . base_url('assets/images/loading.gif') . '" alt="Yükleniyor">
                </div>
                <script>
                    window.location.href = "' . $redirectUrl . '";
                </script>
            </body>
            </html>';
            
            return $html;
        } else {
            // Hata durumu
            throw new Exception('Ödeme başlatma hatası: ' . ($result['message'] ?? 'Bilinmeyen hata'));
        }
    }
    
    /**
     * Ödeme geri dönüş işlemlerini yapar
     * 
     * @param array $data Geri dönüş verileri
     * @return array İşlem sonucu ['status' => true/false, 'message' => 'açıklama']
     */
    public function handleCallback($data) {
        // Log oluştur
        if (function_exists('addlog')) {
            addlog('Pay2out Callback - Başlangıç', 'Callback isteği alındı');
        }
        
        // Raw input verisini al (JSON formatında olabilir)
        $rawInput = file_get_contents('php://input');
        
        // Gelen tüm verileri detaylı logla
        if (function_exists('addlog')) {
            addlog('Pay2out Callback - Raw Input', 'Raw veri: ' . $rawInput);
            addlog('Pay2out Callback - POST verileri', 'POST: ' . json_encode($_POST, JSON_UNESCAPED_UNICODE));
            addlog('Pay2out Callback - GET verileri', 'GET: ' . json_encode($_GET, JSON_UNESCAPED_UNICODE));
            
            // Header bilgilerini logla
            $headers = function_exists('getallheaders') ? getallheaders() : [];
            addlog('Pay2out Callback - Headers', 'Headers: ' . json_encode($headers, JSON_UNESCAPED_UNICODE));
        }
        
        // JSON verisini çözümle
        $jsonData = null;
        if (!empty($rawInput)) {
            $jsonData = json_decode($rawInput, true);
            
            // JSON çözümleme hatası kontrolü
            if (json_last_error() !== JSON_ERROR_NONE) {
                if (function_exists('addlog')) {
                    addlog('Pay2out Callback - JSON Hatası', 'JSON çözümlenirken hata oluştu: ' . json_last_error_msg());
                }
            } else if ($jsonData) {
                // Başarılı JSON verisi bulundu
                $data = $jsonData;
                if (function_exists('addlog')) {
                    addlog('Pay2out Callback - Veri Formatı', 'JSON formatında veri alındı: ' . json_encode($jsonData, JSON_UNESCAPED_UNICODE));
                }
            }
        }
        
        // Veri kaynağını birleştir (öncelik: JSON -> POST -> GET)
        if (empty($data) || !is_array($data)) {
            $data = array_merge($_GET, $_POST);
            
            if (function_exists('addlog')) {
                addlog('Pay2out Callback - Veri Kaynağı', 'JSON veri bulunamadı, POST/GET verisi kullanılıyor');
            }
        }
        
        if (function_exists('addlog')) {
            addlog('Pay2out Callback - İşlenecek Veri', 'Veri: ' . json_encode($data, JSON_UNESCAPED_UNICODE));
        }
        
        // Gerekli verilerin kontrolü
        if (!isset($data['status'])) {
            if (function_exists('addlog')) {
                addlog('Pay2out Callback - Hata', 'Eksik veriler: status yok');
            }
            return [
                'status' => false,
                'message' => 'Eksik veya geçersiz callback verileri'
            ];
        }
        
        // Referans numarasını bul
        $reference_id = null;
        
        if (isset($data['reference']) && !empty($data['reference'])) {
            $reference_id = $data['reference'];
            if (function_exists('addlog')) {
                addlog('Pay2out Callback - Sipariş Arama', 'Referans ile aranıyor: ' . $reference_id);
            }
        } elseif (isset($data['order_number']) && !empty($data['order_number'])) {
            $reference_id = $data['order_number'];
            if (function_exists('addlog')) {
                addlog('Pay2out Callback - Sipariş Arama', 'Order number ile aranıyor: ' . $reference_id);
            }
        } elseif (isset($data['payment_id']) && !empty($data['payment_id'])) {
            $reference_id = $data['payment_id'];
            if (function_exists('addlog')) {
                addlog('Pay2out Callback - Sipariş Arama', 'Payment ID ile aranıyor: ' . $reference_id);
            }
        } else {
            if (function_exists('addlog')) {
                addlog('Pay2out Callback - Hata', 'Sipariş referansı bulunamadı');
            }
            return [
                'status' => false,
                'message' => 'Sipariş referansı bulunamadı'
            ];
        }
        
        // Sipariş bilgilerini al
        $shop = $this->CI->db->where('order_id', $reference_id)->get('shop')->row();
        
        if (!$shop) {
            if (function_exists('addlog')) {
                addlog('Pay2out Callback - Hata', 'Sipariş bulunamadı: ' . $reference_id);
            }
            return [
                'status' => false,
                'message' => 'Sipariş bulunamadı'
            ];
        }
        
        if (function_exists('addlog')) {
            addlog('Pay2out Callback - Sipariş Bilgisi', 'ID: ' . $shop->id . ', Referans: ' . $reference_id . ', Durum: ' . $shop->status);
        }
        
        // İmza doğrulama
        $signature_secret = $this->getConfigValue('api_key', '');
        
        // İstek header'larından imzayı al - düzeltilmiş versiyon
        $headers = function_exists('getallheaders') ? getallheaders() : [];
        // Önce doğrudan orijinal header kontrolü yap (büyük/küçük harf duyarlı)
        $signature = isset($headers['X-Signature']) ? $headers['X-Signature'] : null;
        
        // Eğer bulunamadıysa, küçük harfle kontrol et
        if (!$signature) {
            $headers = array_change_key_case($headers, CASE_LOWER);
            $signature = $headers['x-signature'] ?? null;
        }
        
        if (function_exists('addlog')) {
            addlog('Pay2out Callback - İmza Doğrulama', 'Headers: ' . json_encode($headers));
            addlog('Pay2out Callback - İmza Doğrulama', 'API Key: ' . substr($signature_secret, 0, 3) . '***' . substr($signature_secret, -3)); 
            addlog('Pay2out Callback - İmza Doğrulama', 'Raw Input Length: ' . strlen($rawInput));
            addlog('Pay2out Callback - İmza Doğrulama', 'Raw Input İlk 50 Karakter: ' . substr($rawInput, 0, 50) . '...');
        }
        
        if (!$signature) {
            if (function_exists('addlog')) {
                addlog('Pay2out Callback - Hata', 'X-Signature header bulunamadı');
            }
            return [
                'status' => false,
                'message' => 'İmza doğrulanamadı'
            ];
        }
        
        // İmza doğrulama - önceki çalışan versiyondaki gibi, strtolower kullanmadan
        $calculatedSignature = hash_hmac('sha256', $rawInput, $signature_secret);
        
        // Farklı hesaplama yöntemleri için log
        if (function_exists('addlog')) {
            addlog('Pay2out Callback - İmza Doğrulama', 'Gelen İmza: ' . $signature);
            addlog('Pay2out Callback - İmza Doğrulama', 'Hesaplanan İmza (HMAC SHA256): ' . $calculatedSignature);
            addlog('Pay2out Callback - İmza Doğrulama', 'Hesaplanan İmza (strtolower): ' . strtolower($calculatedSignature));
            addlog('Pay2out Callback - İmza Doğrulama', 'Hesaplanan İmza (strtolower+trim): ' . strtolower(hash_hmac('sha256', $rawInput, trim($signature_secret))));
        }
        
        if (!hash_equals($calculatedSignature, $signature)) {
            // Son bir deneme: küçük harfe çevirme
            if (!hash_equals(strtolower($calculatedSignature), strtolower($signature))) {
                if (function_exists('addlog')) {
                    addlog('Pay2out Callback - Hata', 'İmza doğrulaması başarısız');
                }
                return [
                    'status' => false,
                    'message' => 'İmza doğrulaması başarısız'
                ];
            } else {
                if (function_exists('addlog')) {
                    addlog('Pay2out Callback - Bilgi', 'İmza küçük harfe çevrilerek doğrulandı');
                }
            }
        } else {
            if (function_exists('addlog')) {
                addlog('Pay2out Callback - İmza Doğrulama', 'İmza doğrulaması başarılı');
            }
        }
        
        // Ödeme durumuna göre işlem yap
        $paymentStatus = $data['status'];
        
        if (function_exists('addlog')) {
            addlog('Pay2out Callback - Ödeme Durumu', 'Status: ' . $paymentStatus);
        }
        
        // Ödeme başarılı değilse
        if ($paymentStatus !== 'completed' && $paymentStatus !== 'success' && $paymentStatus !== 'approved') {
            if (function_exists('addlog')) {
                addlog('Pay2out Callback - Bilgi', 'Ödeme başarısız veya beklemede. Durum: ' . $paymentStatus);
            }
            return [
                'status' => false,
                'message' => 'Ödeme tamamlanmadı'
            ];
        }
        
        // Tutarı al ve kontrol et
        $amount = isset($data['amount']) ? $data['amount'] : $shop->price;
        
        // Ödeme başarılı, sonuç döndür
        if (function_exists('addlog')) {
            addlog('Pay2out Callback - Tamamlandı', 'İşlem başarıyla tamamlandı');
        }
        
        return [
            'status' => true,
            'order_id' => $reference_id,
            'amount' => $amount,
            'user_id' => $shop->user_id,
            'message' => 'Ödeme başarılı'
        ];
    }

    /**
     * Bu ödeme sağlayıcısının callback için özel işaretlerini döndürür
     * 
     * @return array İşaretler dizisi (callback parametreleri)
     */
    public function getCallbackSignatures() {
        return ['reference', 'order_number', 'status'];
    }
    
    /**
     * Ödeme yöntemi adını döndürür
     * 
     * @return string Ödeme yöntemi adı
     */
    public function getPaymentName() {
        return $this->config['payment_name'] ?? 'Pay2out';
    }

    /**
     * Yapılandırma değerini döndürür
     * 
     * @param string $key İstenilen yapılandırma anahtarı
     * @param mixed $default Değer bulunamazsa dönecek varsayılan değer
     * @return mixed Yapılandırma değeri
     */
    public function getConfigValue($key, $default = null) {
        if (isset($this->config['config'][$key])) {
            return $this->config['config'][$key];
        }
        return $default;
    }

    /**
     * Ödeme yöntemine ait komisyon oranını döndürür
     * 
     * @param int $user_id Kullanıcı ID (isteğe bağlı - özel komisyon oranları için)
     * @return float Komisyon oranı
     */
    public function getCommissionRate($user_id = null) {
        // Varsayılan komisyon oranını kullan
        $commission_rate = $this->config['commission_rate'] ?? 0;
        
        // Eğer kullanıcı ID belirtilmişse, kullanıcıya özel komisyon oranını kontrol et
        if ($user_id !== null) {
            $this->CI->load->library('PaymentFactory');
            return $this->CI->paymentfactory->getCommissionRate($this->config['id'], $user_id);
        }
        
        return $commission_rate;
    }
} 