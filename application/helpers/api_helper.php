<?php
// helpers/api_helper.php
	function getAPIsettings() {
		$ci = &get_instance();
		$apis = $ci->db->get('api_settings')->result();
		$api_settings = [];
		foreach ($apis as $api) {
			if (!isset($api_settings[$api->api_name])) {
				$api_settings[$api->api_name] = new stdClass();
			}
			$api_settings[$api->api_name]->{$api->setting_key} = $api->setting_value;
		}
		return $api_settings;
	}

	function createInvoiceInAPI($user, $inv) {
		$ci = &get_instance();
		$api_settings = getAPIsettings();
		if ($api_settings["billing"]->provider !== "disabled") {
			$provider = $api_settings["billing"]->provider;
			if (file_exists(APPPATH . 'libraries/' . ucfirst($provider) . '.php')) {
				$ci->load->library($provider, (array)$api_settings["billing"], "billing");
				return $ci->billing->createInvoice($user, $inv);
			} else {
				addlog('createInvoiceInAPI', "Fatura oluşturulurken bir hata oluştu. {$provider} fatura sağlayıcısı bulunamadı!");
				return false;
			}
		} else {
			return true;
		}
	}

	function createInvoiceForBalance($user, $shop) {
		$ci = &get_instance();
		$api_settings = getAPIsettings();
		if ($api_settings["billing"]->provider !== "disabled") {
			$provider = $api_settings["billing"]->provider;
			if (file_exists(APPPATH . 'libraries/' . ucfirst($provider) . '.php')) {
				$ci->load->library($provider, (array)$api_settings["billing"], "billing");
				return $ci->billing->createInvoiceforBalance($user, $shop);
			} else {
				addlog('createInvoiceForBalance', "Fatura oluşturulurken bir hata oluştu. {$provider} fatura sağlayıcısı bulunamadı!");
				return false;
			}
		} else {
			return true;
		}
	}

	function createInvoiceforSubscription($user, $subscription, $userSubscriptionID) {
		$ci = &get_instance();
		$api_settings = getAPIsettings();
		if ($api_settings["billing"]->provider !== "disabled") {
			$provider = $api_settings["billing"]->provider;
			if (file_exists(APPPATH . 'libraries/' . ucfirst($provider) . '.php')) {
				$ci->load->library($provider, (array)$api_settings["billing"], "billing");
				return $ci->billing->createInvoiceforSubscription($user, $subscription, $userSubscriptionID);
			} else {
				addlog('createInvoiceforSubscription', "Fatura oluşturulurken bir hata oluştu. {$provider} fatura sağlayıcısı bulunamadı!");
				return false;
			}
		} else {
			return true;
		}
	}
	function sendSMSMessage($phone, $message) {
		$ci = &get_instance();
		$api_settings = getAPIsettings();
		if ($api_settings["sms"]->provider !== "disabled") {
			$provider = $api_settings["sms"]->provider;
			try {
				$ci->load->library("SMSProvider", NULL, "sms");
				$ci->sms->set_provider($provider);
				$ci->sms->set_options($api_settings["sms"]);
				$ci->sms->add_message(new SMS_Message($phone, $message));
				return $ci->sms->send_sms();
			} catch (Exception $e) {
				addlog('sendSMSMessage', "SMS gönderilirken bir hata oluştu. {$provider} SMS sağlayıcısı bulunamadı!");
				return false;
			}
		} else {
			return true;
		}
	}

	function sendSMSMessageToAll($message) {
		$ci = &get_instance();
		$api_settings = getAPIsettings();
		if ($api_settings["sms"]->provider !== "disabled") {
			$user_phones = $ci->db->select('phone')->get('user')->result();
			$user_phones = array_column($user_phones, 'phone');
			$provider = $api_settings["sms"]->provider;
			try {
				$ci->load->library("SMSProvider", NULL, "sms");
				$ci->sms->set_provider($provider);
				$ci->sms->set_options($api_settings["sms"]);
				foreach ($user_phones as $phone) {
					$ci->sms->add_message(new SMS_Message($phone, $message));
				}
				return $ci->sms->send_sms();
			} catch (Exception $e) {
				addlog('sendSMSMessage', "SMS gönderilirken bir hata oluştu. {$provider} SMS sağlayıcısı bulunamadı!");
				return false;
			}
		} else {
			return true;
		}
	}

	function getEpinProduct($gameCode)
	{
		$ci = &get_instance();
		$ci->load->library("Turkpin");
		return  $ci->turkpin->getEpinProduct($gameCode);
	}

	function proccessTurkpinOrder($user, $shop, $product, $price, $is_balance=false)
	{
		$ci = &get_instance();
		$properties = $ci->db->where('id', 1)->get('properties')->row();
		$productDetail = [];

		$ci->load->library("Turkpin");
		$turkpin = $ci->turkpin->createEPIN($product->game_code, $product->product_code, $user->email);

		if ($turkpin["status"]) {
			if(isset($turkpin["error_code"]) && $turkpin["error_code"] > 0) {
				$data = [
					'user_id' => $shop->user_id,
					'product_id' => $product->id, 
					'date' => date('d.m.Y H:i:s'),
					'balance' => $user->balance,
        			'new_balance' =>  $user->balance - ($is_balance ? $price : 0),
					'price' => $price,
					'isActive' => 1,
					'shop_id' => $shop->id
				];

				$ci->db->insert('pending_product', $data);
				array_push($productDetail, ['status'=> 2, 'product' => $product->name, 'price' => $price]);
				addlog('proccessTurkpinOrder', 'Ürün alımı başarılı (Turkpin bakiye yetersizliğinden dolayı ürün beklemeye alındı). Ürün: '. $product->name . ' Fiyat: ' . $price);

				if ($turkpin["error_code"] == 014) {
					stockAlert('<div class="orius-mail">
						<div class="box">
						<h1 class="logo-text">'. $properties->name .'</h1>
						<h2>Bakiye Bilgilendirmesi</h2>
						<p>Az önce Turkpin sisteminden denenen ürün satın alınması bakiye yetersizliğinden dolayı iptal edildi. Lütfen yükleme yapın ve admin panelden teslimat yapın.</p>
						</div>
						</div>');
				}
			} else {
				$code = $turkpin['epin'];
				$insertData = [
					'product' => $code,
					'checked' => 1,
					'isActive' => 0,
					'product_id' => $product->id
				];

				$ci->db->insert('stock', $insertData);

				$data = [
					'product' => $code,
					'isActive' => 0,
					'isComment' => 1,
					'price' => $price,
					'date' => date('d.m.Y H:i:s'),
					'balance' => $user->balance,
        			'new_balance' => $user->balance - ($is_balance ? $price : 0),
					'product_id' => $product->id,
					'shop_id' => $shop->id,
					'seller_id' => $product->seller_id
				];
				$ci->db->insert('invoice', $data);
				array_push($productDetail, ['status'=> 1, 'product' => $product->name, 'stock' => $code, 'price' => $price]);
				addlog('proccessTurkpinOrder', 'Ürün alımı başarılı. Ürün: '. $product->name . ' Fiyat: ' . $price . ' Verilen ürün: ' . $code);
			}
		} else {
			$data = [
				'user_id' => $shop->user_id,  
				'product_id' => $product->id,
				'date' => date('d.m.Y H:i:s'),
				'balance' => $user->balance,
        		'new_balance' => $user->balance - ($is_balance ? $price : 0),
				'isActive' => 1,
				'shop_id' => $shop->id,
				'price' => $price
			];

			$ci->db->insert('pending_product', $data);
			array_push($productDetail, ['status'=> 2, 'product' => $product->name, 'price' => $price]);
			addlog('proccessTurkpinOrder', 'Ürün alımı başarılı (Stok olmadığı için ürün beklemeye alındı). Ürün: '. $product->name . ' Fiyat: ' . $price);
		}
		return $productDetail;
	}

	function proccessPinabiOrder($user, $shop, $product, $cart, $i, $is_balance=false) {
		$price = $cart['price'];
		$ci = &get_instance();
		$properties = $ci->db->where('id', 1)->get('properties')->row();
		$productDetail = [];

		$ci->load->library("Pinabi");
		$pinabi = $ci->pinabi->createOrder([
			"transactionId" => $shop->id."XpjpX".$i,
			"quantity" => 1,
			"productId" => $product->product_code,
		]);
		$pinabi = json_decode($pinabi, true);

		if ($pinabi["status"]["code"] == 200 || $pinabi["status"]["code"] == 301) {
			if ($pinabi["status"]["code"] == 301) {
				$data = [
					'user_id' => $shop->user_id,
					'product_id' => $product->id,
					'date' => date('d.m.Y H:i:s'),
					'balance' => $user->balance,
					'new_balance' => $user->balance - ($is_balance ? $price : 0),
					'price' => $price,
					'isActive' => 1,
					'shop_id' => $shop->id,
					'api_pending' => true,
					'pending_id' => $shop->id."XpjpX".$i
				];

				$ci->db->insert('pending_product', $data);
				array_push($productDetail, ['status'=> 2, 'product' => $product->name, 'price' => $price]);
				addLog('proccessPinabiOrder', 'Ürün alımı başarılı (Stok olmadığı için ürün beklemeye alındı). Ürün: '. $product->name . ' Fiyat: ' . $price);
			} else {
				$code = implode(' , ', $pinabi['epinCodeList']);
				$insertData = [
					'product' => $code,
					'checked' => 1,
					'isActive' => 0,
					'product_id' => $product->id
				];

				$ci->db->insert('stock', $insertData);

				$data = [
					'product' => $code,
					'isActive' => 0,
					'isComment' => 1,
					'price' => $price,
					'date' => date('d.m.Y H:i:s'),
					'balance' => $user->balance,
					'new_balance' => $user->balance - ($is_balance ? $price : 0),
					'product_id' => $product->id,
					'shop_id' => $shop->id,
					'seller_id' => $product->seller_id
				];
				$ci->db->insert('invoice', $data);
				array_push($productDetail, ['status'=> 1, 'product' => $product->name, 'stock' => $code, 'price' => $price]);
				addlog('proccessPinabiOrder', 'Ürün alımı başarılı. Ürün: '. $product->name . ' Fiyat: ' . $price . ' Verilen ürün: ' . $code);
			}
		} else {
			$data = [
				'user_id' => $shop->user_id,
				'product_id' => $product->id,
				'date' => date('d.m.Y H:i:s'),
				'balance' => $user->balance,
				'new_balance' => $user->balance - ($is_balance ? $price : 0),
				'price' => $price,
				'isActive' => 1,
				'shop_id' => $shop->id
			];

			$ci->db->insert('pending_product', $data);
			array_push($productDetail, ['status'=> 2, 'product' => $product->name, 'price' => $price]);
			$message = ($pinabi["status"]["code"] == "450") ? "Pinabi stoğu tükendiği için ürün beklemeye alındı." :
				($pinabi["status"]["code"] == "455" ? "Pinabi bakiye yetersizliğinden dolayı ürün beklemeye alındı." : "Pinabi'de bir hata oluştu. Kod: " . $pinabi["status"]["code"]);
			addlog('proccessPinabiOrder', 'Ürün alımı başarılı ('.$message.'). Ürün: '. $product->name . ' Fiyat: ' . $price);

			if ($pinabi["status"]["code"] == 455) {
				stockAlert('<div class="orius-mail">
								<div class="box">
								<h1 class="logo-text">'. $properties->name .'</h1>
								<h2>Bakiye Bilgilendirmesi</h2>
								<p>Az önce Pinabi sisteminden denenen ürün satın alınması bakiye yetersizliğinden dolayı iptal edildi. Lütfen yükleme yapın ve admin panelden teslimat yapın.</p>
								</div>
								</div>');
			}
		}
		return $productDetail;
	}


	function proccessCustomProviderOrder($user, $shop, $product, $price, $is_balance=false) {
		$ci = &get_instance();
		$ci->load->helper('provider');
		$properties = $ci->db->where('id', 1)->get('properties')->row();
		$productDetail = [];

		$provider = $ci->db->where('id', $product->product_provider)->get('product_providers')->row();
		$delivery_id = $shop->id . "XoriX" . rand(1000, 9999);
		$invoice = $ci->db->where('shop_id', $shop->id)->get('invoice')->row();
		$fields = [];
		if (!empty($invoice["extras"])) {
			$field_values = explode(",", $invoice["extras"]);
			$field_ids = explode(",", $product->field_ids);

			foreach ($field_ids as $index => $field_id) {
				$fields[$field_id] = $field_values[$index] ?? "";
			}
		}
		$get_order = createProviderOrder($provider->id, $product->product_code, $delivery_id, $fields);

		if ($get_order["status"]) {
			if ($get_order["delivery_status"] === "pending") {
				$data = [
					'user_id' => $shop->user_id,
					'product_id' => $product->id,
					'date' => date('d.m.Y H:i:s'),
					'balance' => $user->balance,
					'new_balance' => $user->balance - ($is_balance ? $price : 0),
					'price' => $price,
					'isActive' => 1,
					'shop_id' => $shop->id,
					'api_pending' => true,
					'pending_id' => $delivery_id
				];

				$ci->db->insert('pending_product', $data);
				array_push($productDetail, ['status'=> 2, 'product' => $product->name, 'price' => $price]);
				addLog('proccessCustomProviderOrder', 'Ürün alımı başarılı (Stok olmadığı için ürün beklemeye alındı). Ürün: '. $product->name . ' Fiyat: ' . $price);
			} else if ($get_order["delivery_status"] === "completed") {
				$code = $get_order['code'];
				$insertData = [
					'product' => $code,
					'checked' => 1,
					'isActive' => 0,
					'product_id' => $product->id
				];

				$ci->db->insert('stock', $insertData);

				$data = [
					'product' => $code,
					'isActive' => 0,
					'isComment' => 1,
					'price' => $price,
					'date' => date('d.m.Y H:i:s'),
					'balance' => $user->balance,
					'new_balance' => $user->balance - ($is_balance ? $price : 0),
					'product_id' => $product->id,
					'shop_id' => $shop->id,
					'seller_id' => $product->seller_id
				];
				$ci->db->insert('invoice', $data);
				array_push($productDetail, ['status'=> 1, 'product' => $product->name, 'stock' => $code, 'price' => $price]);
				addlog('proccessCustomProviderOrder', 'Ürün alımı başarılı. Ürün: '. $product->name . ' Fiyat: ' . $price . ' Verilen ürün: ' . $code);
			}
		} else {
			$data = [
				'user_id' => $shop->user_id,
				'product_id' => $product->id,
				'date' => date('d.m.Y H:i:s'),
				'balance' => $user->balance,
				'new_balance' => $user->balance - ($is_balance ? $price : 0),
				'price' => $price,
				'isActive' => 1,
				'shop_id' => $shop->id
			];

			$ci->db->insert('pending_product', $data);
			array_push($productDetail, ['status'=> 2, 'product' => $product->name, 'price' => $price]);
			$message = $get_order["message"];
			addlog('proccessCustomProviderOrder', 'Ürün alımı başarılı ('.$message.'). Ürün: '. $product->name . ' Fiyat: ' . $price);
		}
		return $productDetail;
	}

?>
