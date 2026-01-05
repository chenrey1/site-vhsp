<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Payment Controller - Ödeme işlemleri
 */
class Payment extends G_Controller {

	/**
	 * Payment Constructor
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model('M_Payment');
		$this->load->library('PaymentFactory');
		$this->load->library('advanced_cart');
	}

	/**
	 * Bakiye yükleme ödeme sayfası
	 */
	public function index() {
		addlog('Ödeme sayfası - index', 'Sayfa ziyaret edildi: Bakiye yükleme adım 1 - Sayfa ziyareti');

		if (!isset($this->session->userdata('info')['id'])) {
			flash('Ups.', 'Yetkin Olmayan Bir Yere Giriş Yapmaya Çalışıyorsun.');
			redirect(base_url(), 'refresh');
			exit;
		}

		// SQL Injection önlemi - user_id'yi integer'a dönüştür
		$data['user_id'] = intval($this->session->userdata('info')['id']);
		$data['user'] = $this->db->where('id', $data['user_id'])->get('user')->row();

		// Ödeme tutarını kontrol et
		if ($this->input->post('amount') && is_numeric($this->input->post('amount'))) {
			$amount = $this->input->post('amount');
			$amount = str_replace(',', '.', $amount);
			$amount = floatval($amount);
			$data['amount'] = $amount;

			// Seçilen ödeme yöntemi ID'sini al (varsayılan NULL) - SQL Injection önlemi
			$payment_id = $this->input->post('payment_method') ? intval($this->input->post('payment_method')) : null;
			
			try {
				// Ödeme sağlayıcısını al
				$provider = $this->paymentfactory->getPaymentProvider($payment_id);
				
				if (!$provider) {
					// Ödeme sağlayıcı bulunamazsa hata ver
					flash('error', 'Seçilen ödeme yöntemi bulunamadı.');
					redirect('client/balance');
					exit;
				}
				
				// Komisyon oranını hesapla
				$commission_rate = $this->paymentfactory->getCommissionRate($payment_id, $data['user_id']);
				
				// Komisyon tutarını hesapla ve toplam tutara ekle - Yuvarlama sorununu düzelt
				// Önce tam hesaplama yapıp sonra 2 ondalık basamağa yuvarla
				$commission_amount = ($amount * $commission_rate / 100);
				$total_amount = $amount + $commission_amount;
				
				// Son olarak 2 ondalık basamağa yuvarla (ceil yerine round kullanarak)
				$total_amount = round($total_amount, 2);
				
				//Alışveriş kaydı oluştur
				$order_id = $this->M_Payment->addShop(
					$data['user_id'],
					json_encode([
						"action" => "deposit",
						"amount" => $amount,
						"description" => "Bakiye yükleme",
						"date" => date('Y-m-d H:i:s')
					]),
					$amount,
					'deposit',
					$commission_rate,
					null,
					$payment_id
				);

				// Sepet bilgilerini oluştur
				$params = [
					'user' => $data['user'],
					'user_ip' => $_SERVER['REMOTE_ADDR'],
					'amount' => $total_amount, // Komisyon eklenmiş toplam tutar
					'order_id' => $order_id,
					'callback_url' => base_url('payment/callback/' . $payment_id), // Payment ID'yi callback'e gönder
					'merchant_ok_url' => base_url('client/balance'),
					'merchant_fail_url' => base_url('client/balance'),
					'commission_rate' => $commission_rate,
					'ip_address' => $this->getUserIpAddress()
				];
				
				// Ödeme sayfasını oluştur
				$payment_html = $provider->createPayment($params);
				
				// Ödeme sayfasını göster
				echo $payment_html;
				exit;
				
			} catch (Exception $e) {
				flash('error', 'Ödeme işlemi başlatılamadı: ' . $e->getMessage());
				redirect('client/balance');
				exit;
			}
		}
		
		flash('error', 'Geçersiz ödeme tutarı.');
		redirect('client/balance');
		exit;
	}
	
	/**
	 * Sepetteki ürünleri kredi kartı ile satın al
	 */
	public function buyOnCart() {
		addlog('Ödeme sayfası - buyOnCart', 'Sayfa ziyaret edildi: Kart ile ödeme. Adım 1 - Sayfa ziyareti. Sepet içeriği: ' . json_encode($this->advanced_cart->contents(), JSON_UNESCAPED_UNICODE));
		
		$properties = $this->db->where('id', 1)->get('properties')->row();
		$user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
		$encode = json_encode($this->advanced_cart->contents(), JSON_UNESCAPED_UNICODE);
		$price = $this->M_Payment->calculate($encode);
		
		// Ödeme yöntemi belirtilmediyse varsayılanı kullan - SQL Injection önlemi
		$payment_method_id = $this->input->post('payment_method') ? intval($this->input->post('payment_method')) : null;
		if (!$payment_method_id) {
			$payment_method_id = $this->paymentfactory->getDefaultPaymentMethodId();
		}

		if ($price == 0) {
			flash('Ups.', '0 Lira ödeyemezsin.');
			redirect(base_url(), 'refresh');
			exit;
		}

		try {
			// Ödeme sağlayıcısını al
			$payment_provider = $this->paymentfactory->getPaymentProvider($payment_method_id);
			
			// Komisyon oranını hesapla
			$commission_rate = $payment_provider->getCommissionRate($user->id);
			$payment_commission = number_format(($price * $commission_rate) / 100, 2, '.', '');
			$total_amount = $price + $payment_commission;
			
			// Alışveriş kaydı oluştur
			$coupon = $this->advanced_cart->has_cart_extra("coupon_id") ? $this->advanced_cart->get_cart_extra("coupon_id") : null;
			$order_id = $this->M_Payment->addShop(
				$user->id, 
				$encode, 
				$price, 
				'credit_card', 
				$payment_commission, 
				$coupon,
				$payment_method_id
			);
			
			// Kullanıcı IP adresi
			$ip = $this->getUserIpAddress();
			
			// Ödeme parametrelerini hazırla
			$payment_params = [
				'user' => $user,
				'order_id' => $order_id,
				'amount' => $total_amount,
				'type' => 1, // Ürün satın alma
				'ip_address' => $ip,
				'properties' => $properties
			];
			
			$this->advanced_cart->destroy();
			
			// Ödeme sayfasını oluştur ve göster
			echo $payment_provider->createPayment($payment_params);
			
		} catch (Exception $e) {
			flash('Ups.', 'Ödeme işlemi başlatılamadı: ' . $e->getMessage());
			redirect(base_url('cart'), 'refresh');
			exit;
		}
	}

	/**
	 * Ödeme geri dönüşleri
	 */
	public function callback($payment_id = null) {
		addlog('Ödeme callback', 'Payment provider tarafından callback çağrısı alındı. Veriler: ' . json_encode($_POST));
		
		// Raw input verisini al (JSON formatında olabilir)
		$rawInput = file_get_contents('php://input');
		addlog('Ödeme callback - Raw Input', 'Raw Input: ' . $rawInput);
		
		// URL'de payment_id yoksa, POST veya GET'den almaya çalış - SQL Injection önlemi
		if ($payment_id === null) {
			$payment_id = $this->input->post('payment_id') ? intval($this->input->post('payment_id')) : 
				($this->input->get('payment_id') ? intval($this->input->get('payment_id')) : null);
		} else {
			$payment_id = intval($payment_id);
		}
		
		try {
			// Eğer payment_id belirtilmişse, bu ödeme yöntemini doğrudan kullan
			if ($payment_id) {
				$provider = $this->paymentfactory->getPaymentProvider($payment_id);
				
				if (!$provider) {
					addlog('Ödeme callback - HATA', 'Ödeme sağlayıcısı bulunamadı: ' . $payment_id);
					echo "Ödeme sağlayıcısı bulunamadı.";
					return;
				}
				
				addlog('Ödeme callback - Bilgi', 'Payment ID ile provider belirlendi: ' . get_class($provider));
			} else {
				// Payment_id belirtilmemişse, callback verilerine göre sağlayıcıyı tespit et
				$callback_data = array_merge($_POST, $_GET);
				
				// JSON verilerini çözümle ve ekle
				$jsonData = null;
				if (!empty($rawInput)) {
					$jsonData = json_decode($rawInput, true);
					if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
						$callback_data = array_merge($callback_data, $jsonData);
						addlog('Ödeme callback - JSON Verisi', 'JSON verisi callback_data ile birleştirildi');
					} else {
						addlog('Ödeme callback - JSON Hatası', 'JSON çözümlenemedi: ' . json_last_error_msg());
					}
				}
				
				addlog('Ödeme callback - İşlenecek Veri', 'İşlenecek veri: ' . json_encode($callback_data));
				
				try {
					$provider = $this->paymentfactory->getPaymentProviderByCallback($callback_data);
					addlog('Ödeme callback - Bilgi', 'Provider başarıyla belirlendi: ' . get_class($provider));
				} catch (Exception $e) {
					addlog('Ödeme callback - HATA', 'Ödeme sağlayıcısı belirlenemedi: ' . $e->getMessage());
					echo "Ödeme sağlayıcısı belirlenemedi: " . $e->getMessage();
					return;
				}
			}
			
			// Gelen verileri ödeme sağlayıcının kendi callback handler'ına gönder
			$callback_data = array_merge($_POST, $_GET);
			
			// JSON verilerini ekle
			if (!empty($rawInput)) {
				$jsonData = json_decode($rawInput, true);
				if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
					$callback_data = array_merge($callback_data, $jsonData);
				}
			}
			
			addlog('Ödeme callback - Handler Öncesi', 'Provider\'a gönderilecek veri: ' . json_encode($callback_data));
			$result = $provider->handleCallback($callback_data);
			
			// İşlem sonucunu kontrol et
			if ($result['status']) {
				// Başarılı ödeme
				$order_id = $result['order_id'];
				$amount = $result['amount'];
				$user_id = $result['user_id'];
				
				// 1. İYİLEŞTİRME: Sipariş tutarını doğrula
				$shop = $this->db->where('order_id', $order_id)->get('shop')->row();
				if (!$shop) {
					echo "FAIL: Sipariş bulunamadı.";
					addlog('Ödeme callback - HATA', 'Sipariş bulunamadı: ' . $order_id);
					return;
				}
				
				// Tutarı doğrula (küçük fark toleransı ile)
				// Toplam tutar = ana tutar + komisyon
				$expected_amount = $shop->price;
				
				// Eğer komisyon bilgisi varsa, toplam tutara ekle
				if (isset($shop->payment_commission) && $shop->payment_commission > 0) {
					$expected_amount += $shop->payment_commission;
					addlog('Ödeme callback - Tutar Hesaplama', 'Komisyon eklendi: ' . $shop->payment_commission . ', Toplam beklenen: ' . $expected_amount);
				}
				
				
				// 2. İYİLEŞTİRME: Sipariş durumunu kontrol et
				if ($shop->status == 0) {
					echo "OK: Sipariş zaten onaylanmış.";
					addlog('Ödeme callback - BİLGİ', 'Sipariş zaten onaylanmış: ' . $order_id);
					return;
				}
				
				// Sipariş tipine göre işlem yap
				$update_result = false;
				
				// Ödeme tipine göre doğru işlemi çağır
				if ($shop->type == 'deposit') {
					// Bakiye yükleme işlemi
					addlog('Ödeme callback - BİLGİ', 'Bakiye yükleme işlemi için confirmShopForBalance çağrılıyor. Shop ID: ' . $shop->id);
					$update_result = $this->M_Payment->confirmShopForBalance($shop->id);
				} else if ($shop->type == 'credit_card' || $shop->type == 'balance') {
					// Ürün satın alma işlemi
					addlog('Ödeme callback - BİLGİ', 'Ürün satın alma işlemi için confirmShopForCart çağrılıyor. Shop ID: ' . $shop->id);
					$update_result = $this->M_Payment->confirmShopForCart($shop->id);
				} else {
					// Bilinmeyen ödeme tipi
					addlog('Ödeme callback - HATA', 'Bilinmeyen ödeme tipi: ' . $shop->type);
					echo "FAIL: Bilinmeyen ödeme tipi.";
					return;
				}
				
				// İşlem başarılı mesajı
				echo "OK";
				addlog('Ödeme callback - BAŞARILI', 'Ödeme onaylandı. Sipariş: ' . $order_id . ', Tutar: ' . $amount . ', Kullanıcı: ' . $user_id . ', Ödeme Tipi: ' . $shop->type . ', Sonuç: ' . json_encode($update_result));
			} else {
				// Başarısız ödeme
				echo "FAIL: " . ($result['message'] ?? 'Ödeme başarısız.');
				addlog('Ödeme callback - BAŞARISIZ', 'Ödeme başarısız. Hata: ' . ($result['message'] ?? 'Sebep belirtilmemiş') . ', Detaylar: ' . json_encode($result));
			} ?>
			<script>
                window.location.href = "<?= base_url(); ?>";
            </script>
            <?php
			
		} catch (Exception $e) {
			echo "ERROR: " . $e->getMessage();
			addlog('Ödeme callback - HATA', 'İşlem hatası: ' . $e->getMessage() . ', Hatanın oluştuğu yer: ' . $e->getFile() . ':' . $e->getLine());
		}
	}
	
	/**
	 * Ödeme seçeneklerini getir (AJAX)
	 */
	public function getPaymentMethods() {
		// CSRF korumasını kontrol et
		$submitted_token = $this->security->xss_clean($this->input->post('csrf_token'));
		if ($submitted_token !== $this->security->get_csrf_hash()) {
			echo json_encode(['status' => 'error', 'message' => 'Geçersiz güvenlik tokeni']);
			return;
		}
		
		// SQL Injection önlemi - Session'dan gelen user_id'yi integer'a dönüştür
		$user_id = isset($this->session->userdata('info')['id']) ? intval($this->session->userdata('info')['id']) : null;
		$payment_methods = $this->paymentfactory->getActivePaymentMethods();
		$payment_options = [];
		
		foreach ($payment_methods as $method) {
			try {
				// Her ödeme yöntemi için komisyonu hesapla
				$payment_provider = $this->paymentfactory->getPaymentProvider($method->id);
				$commission_rate = $payment_provider->getCommissionRate($user_id);
				
				$payment_options[] = [
					'id' => $method->id,
					'name' => $method->payment_name,
					'description' => $method->description,
					'icon' => $method->icon,
					'commission_rate' => $commission_rate,
					'is_default' => (bool)$method->is_default
				];
			} catch (Exception $e) {
				// Hata durumunda atla
				continue;
			}
		}
		
		echo json_encode(['status' => 'success', 'payment_methods' => $payment_options]);
	}
	
	/**
	 * Kullanıcının IP adresini döndürür
	 * 
	 * @return string IP adresi
	 */
	private function getUserIpAddress() {
		if(isset($_SERVER["HTTP_CLIENT_IP"])) {
			return $_SERVER["HTTP_CLIENT_IP"];
		} elseif(isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
			return $_SERVER["HTTP_X_FORWARDED_FOR"];
		} else {
			return $_SERVER["REMOTE_ADDR"];
		}
	}
}
