<?php
// controllers/API.php
class API extends G_Controller {

	public function __construct() {

		parent::__construct();

	}



	public function pinabiDelivery() {

		$json_data = file_get_contents('php://input');

		$data = json_decode($json_data, true);

		if ($data === null) {

			$this->output

				->set_content_type('application/json')

				->set_output(json_encode(["success" => false, "message" => "Invalid request"]));

			return;

		} else {

			$transaction_id = $data['clientTransactionId'];

			//get from pending_product

			$pending_product = $this->db->where(['pending_id' => $transaction_id, 'api_pending' => true])->get("pending_product")->row();

			if (empty($pending_product)) {

				$this->output

					->set_content_type('application/json')

					->set_output(json_encode(["success" => false, "message" => "Invalid request"]));

				return;

			}

			$product = $this->db->where(['id' => $pending_product->product_id])->get("product")->row();

			if (empty($product)) {

				addLog("pinabiDelivery", "product is empty (Shop: " . $pending_product->shop_id . ")");

				$this->output

					->set_content_type('application/json')

					->set_output(json_encode(["success" => false, "message" => "Invalid request"]));

				return;

			}

			if ($product->product_provider != "pinabi") {

				addLog("pinabiDelivery", "product_provider is not pinabi (Shop: " . $pending_product->shop_id . ")");

				$this->output

					->set_content_type('application/json')

					->set_output(json_encode(["success" => false, "message" => "Invalid request"]));

				return;

			}



			$this->load->library("Pinabi");

			$pinabi = $this->pinabi->getOrder($transaction_id, $product->product_code);

			$pinabi = json_decode($pinabi, true);

			if (in_array($pinabi["status"]["code"], [200, 301]) && in_array($data["status"]["code"], [200, 301])) {

				$code = implode(' , ', $data['lstEpin']);

				$stockData = [

					'product' => $code,

					'checked' => 1,

					'isActive' => 0,

					'product_id' => $pending_product->product_id

				];



				$invData = [

					'product' => $code,

					'isActive' => 0,

					'isComment' => 1,

					'price' => $pending_product->price,

					'date' => date('d.m.Y'),

					'balance' => $pending_product->balance,

					'new_balance' => $pending_product->new_balance,

					'product_id' => $pending_product->product_id,

					'shop_id' => $pending_product->shop_id

				];

				$this->db->trans_begin();

				$this->db->insert('stock', $stockData);

				$this->db->where('id', $pending_product->id)->update('pending_product', ['isActive' => 0]);

				$this->db->insert('invoice', $invData);

				if ($this->db->trans_status() === FALSE) {

					$this->db->trans_rollback();

					addLog("pinabiDelivery", "Pinabi teslimatı sırasında bir hata oluştu. Dönüt SQL'e kaydedilemedi.");

					$this->output

						->set_content_type('application/json')

						->set_output(json_encode(["success" => false, "message" => "Invalid request"]));

				} else {

					$this->db->trans_commit();

					$user = $this->db->where('id', $pending_product->user_id)->get('user')->row();

					$properties = $this->db->where('id', 1)->get('properties')->row();

					addLog("pinabiDelivery", "Pinabi teslimatı başarılı bir şekilde gerçekleşti.");



					sendNotification($user->email, '<div class="orius-mail">

                        <div class="box">

                        <h1 class="logo-text">'. $properties->name .'</h1>

                        <h2>Ürünün Hesabında</h2>

                        <p>Daha önce stok olmadığı için verilemeyen ürünün şu an hesabında. Ürün bilgilerin aşağıdaki gibidir;</p>

                        <small>'. $code .'</small>

                        </div>

                        </div>');



					$this->load->helper('api');

					$api_settings = getAPIsettings();

					if ($api_settings["sms"]->neworder_enabled) {

						sendSMSMessage($user->phone,

							str_replace(

								["{name}", "{surname}"],

								[$user->name, $user->surname],

								$api_settings["sms"]->neworder_message

							)

						);

					}

					$this->output

						->set_content_type('application/json')

						->set_output(json_encode(["success" => true, "message" => "Success"]));

					return;

				}

			} else {

				$this->db->where('id', $pending_product->id)->update('pending_product', ['api_pending' => false]);

				addLog("pinabiDelivery", "Pinabi teslimatı sırasında bir hata oluştu. API teslimatı kapatıldı. (Ürün: " . $pending_product->product_id . ", Shop: " . $pending_product->shop_id . ") Pinabi Dönütü: " . json_encode($pinabi) . " API Dönütü: " . json_encode($data));

				$this->output

					->set_content_type('application/json')

					->set_output(json_encode(["success" => false, "message" => "Invalid request"]));

				return;

			}

		}

	}



	public function useCoupon() {

		if (!isset($this->session->userdata('info')['id'])) {

			exit(json_encode(["status" => "fail", "message" => "Kupon kullanmak için giriş yapmalısınız!"]));

		}

		$uid = $this->session->userdata('info')['id'];

		$coupon_code = $this->input->post("coupon");

		$coupon = $this->db->where([

			"status" => "active",

			"coupon" => $coupon_code

		])->get("coupons")->row();

		if ($coupon) {

			if (isset($coupon->users)  && $coupon->users == "all") {

				$coupon->only_users = false;

			} else {

				$coupon->only_users = true;

				$coupon->users = json_decode($coupon->users ?? "[]", true);

			}

			$coupon->used_by = json_decode($coupon->used_by ?? "[]", true);

			$coupon->categories = json_decode($coupon->categories ?? "[]", true);

			$coupon->products = json_decode($coupon->products ?? "[]", true);

			$coupon->type = ($coupon->type=="rate") ? "percentage" : "amount";



			$coupon->start_at = strtotime($coupon->start_at);

			$coupon->end_at = strtotime($coupon->end_at);



			if ($coupon->start_at>time() || $coupon->end_at<time()) {

				exit(json_encode(["status" => "fail", "message" => "Böyle bir kupon bulunamadı!"]));

			}

			if (($coupon->only_users && !in_array($uid, $coupon->users))) {

				exit(json_encode(["status" => "fail", "message" => "Böyle bir kupon bulunamadı!"]));

			}



			$cart_total = $this->advanced_cart->total();

			if ($coupon->min_amount>$cart_total) {

				exit(json_encode(["status" => "fail", "message" => "Bu kuponu kullanmak için sepetinizde en az ".$coupon->min_amount."TL değerinde ürün bulunmalı!"]));

			}



			$applied_products = [];

			$category_products = [];

			if (!empty($coupon->categories)) {

				foreach ($this->advanced_cart->contents() as $item){

					$product = $this->db->where("id", $item["product_id"])->get("product")->row();

					if (!in_array($product->category_id, $coupon->categories)) {

						$category_products[]  = $item["product_id"];

						continue;

					}

					$applied_products[] = $item["product_id"];

				}

			}

			if (!empty($coupon->products)) {

				foreach ($this->advanced_cart->contents() as $item){

					if (in_array($item["product_id"], $applied_products)) continue;

					if (in_array($item["product_id"], $category_products)) {

						unset($category_products[array_search($item["product_id"], $category_products)]);

					}

					if (!in_array($item["product_id"], $coupon->products)) {

						exit(json_encode(["status" => "fail", "message" => "Bu kupon sepetinizdeki bazı ürünlerde geçerli değil!"]));

					}

				}

			}



			if (!empty($category_products)) {

				exit(json_encode(["status" => "fail", "message" => "Bu kupon sepetinizdeki bazı kategorilerde geçerli değil!"]));

			}



			if ((($coupon->only_users && in_array($uid, $coupon->users)) || !$coupon->only_users) && !in_array($uid, $coupon->used_by)) {

				$this->advanced_cart->discount($coupon->type, $coupon->amount, $coupon->min_amount);

				$this->advanced_cart->cart_extra("coupon_code", $coupon_code);

				$this->advanced_cart->cart_extra("coupon_id", $coupon->id);

				flash('Başarılı!', 'Kupon başarıyla uygulandı.');

				exit(json_encode(["status" => "success"]));

			} else {

				if ((($coupon->only_users && in_array($uid, $coupon->users)) || !$coupon->only_users) && in_array($uid, $coupon->used_by)) {

					exit(json_encode(["status" => "fail", "message" => "Bu kuponu daha önce kullanmışsınız!"]));

				}

				exit(json_encode(["status" => "fail", "message" => "Böyle bir kupon bulunamadı!"]));

			}

		} else {

			exit(json_encode(["status" => "fail", "message" => "Böyle bir kupon bulunamadı!"]));

		}

		//$this->advanced_cart->discount()

	}



	public function cancelCoupon() {

		$this->advanced_cart->remove_cart_extra("coupon_code");

		$this->advanced_cart->remove_cart_extra("coupon_id");

		$this->advanced_cart->reset_cart_discount();

		flash('Başarılı!', 'Kupon başarıyla kaldırıldı.');

		exit(json_encode(["status" => "success"]));

	}



	public function getCartAmount()

	{

		echo count($this->advanced_cart->contents());

	}



    public function setSeen()

    {

    if ($this->input->server('REQUEST_METHOD') === 'POST' && $this->input->is_ajax_request()) {

            $notification_id = $this->input->post('notification_id');

            $notification = $this->db->where('id', $notification_id)->get('notifications')->row();

            $notification_management = $this->db->where('id', $notification->notification_id)->get('notification_management')->row();



            if ($notification->seen_at == 1) {

                $this->db->set('views', $notification_management->views + 1)->where('id', $notification_management->id)->update('notification_management');

                $this->db->where('id', $notification_id)->update('notifications', ['seen_at' => 0, 'seen_date' => date('Y-m-d H:i:s')]);

            }



            echo json_encode(array('success' => true));

        } else {

            // Eğer doğrudan bu sayfaya erişim varsa hata döndür

            show_error('Direct script access not allowed');

        }

    }



    public function setAllSeen()

    {

        if ($this->input->server('REQUEST_METHOD') === 'POST' && $this->input->is_ajax_request()) {

            $user_id = $this->session->userdata('info')['id'];

            $notifications = $this->db->where(['user_id' => $user_id, 'isActive' => 'Active'])->get('notifications')->result();



            foreach ($notifications as $notification) {

                if ($notification->seen_at == 1) {

                    $notification_management = $this->db->where('id', $notification->notification_id)->get('notification_management')->row();

                    $this->db->where('id', $notification_management->id)->update('notification_management', ['views' => $notification_management->views + 1]);

                    $this->db->where('id', $notification->id)->update('notifications', ['seen_at' => 0, 'seen_date' => date('Y-m-d H:i:s')]);

                }

            }



            flash('Başarılı', 'Tüm bildirimleriniz okundu olarak işaretlendi.');

            echo json_encode(array('success' => true));

        } else {

            // Eğer doğrudan bu sayfaya erişim varsa hata döndür

            show_error('Direct script access not allowed');

        }

    }



    public function getEarningsData() {

        $timeFrame = $this->input->get('timeFrame');

        $this->db->select('DATE(transaction_date) as date, SUM(amount) as total');

        $this->db->from('earnings');

        $this->db->where('transaction_status', 'successful');

        $this->db->where_in('payment_method', ['balance', 'credit_card']);



        if ($timeFrame == 'daily') {

            $this->db->group_by('DATE(transaction_date)');

        } elseif ($timeFrame == 'weekly') {

            $this->db->group_by('YEARWEEK(transaction_date)');

        } elseif ($timeFrame == 'monthly') {

            $this->db->group_by('MONTH(transaction_date)');

        }



        $query = $this->db->get();

        $data = $query->result_array();



        echo json_encode($data);

    }





    public function getSalesStatus() {

        $today = date('Y-m-d');



        // Başarılı satışlar ve kazanç

        $this->db->where('DATE(transaction_date)', $today);

        $successful = $this->db->where('transaction_status', 'successful')->where_in('payment_method', ['balance', 'credit_card'])->count_all_results('earnings');

        $this->db->where('DATE(transaction_date)', $today);

        $successfulEarnings = $this->db->select_sum('amount')->where('transaction_status', 'successful')->where_in('payment_method', ['balance', 'credit_card'])->get('earnings')->row()->amount;

        $successfulEarnings = $successfulEarnings ? $successfulEarnings : 0;



        // Başarısız satışlar ve kazanç (shop tablosundan)

        $this->db->where('DATE(date)', $today);

        $unsuccessful = $this->db->where('status', 1)->count_all_results('shop');

        $this->db->where('DATE(date)', $today);

        $unsuccessfulEarnings = $this->db->select_sum('price')->where('status', 1)->get('shop')->row()->price;

        $unsuccessfulEarnings = $unsuccessfulEarnings ? $unsuccessfulEarnings : 0;



        // İptal edilenler ve kazanç

        $this->db->where('DATE(transaction_date)', $today);

        $cancelled = $this->db->where('transaction_status', 'cancelled')->where_in('payment_method', ['balance', 'credit_card'])->count_all_results('earnings');

        $this->db->where('DATE(transaction_date)', $today);

        $cancelledEarnings = $this->db->select_sum('amount')->where('transaction_status', 'cancelled')->where_in('payment_method', ['balance', 'credit_card'])->get('earnings')->row()->amount;

        $cancelledEarnings = $cancelledEarnings ? $cancelledEarnings : 0;



        // Beklemedeki ürünler ve kazanç

        $this->db->where('DATE(transaction_date)', $today);

        $pending = $this->db->where('transaction_status', 'pending')->where_in('payment_method', ['balance', 'credit_card'])->count_all_results('earnings');

        $this->db->where('DATE(transaction_date)', $today);

        $pendingEarnings = $this->db->select_sum('amount')->where('transaction_status', 'pending')->where_in('payment_method', ['balance', 'credit_card'])->get('earnings')->row()->amount;

        $pendingEarnings = $pendingEarnings ? $pendingEarnings : 0;



        // Yüklenen bakiye ve kazanç

        $this->db->where('DATE(date)', $today);

        $deposit = $this->db->where('status', 0)->where('type', 'deposit')->count_all_results('shop');

        $this->db->where('DATE(date)', $today);

        $depositEarnings = $this->db->select_sum('price')->where('status', 0)->where('type', 'deposit')->get('shop')->row()->price;

        $depositEarnings = $depositEarnings ? $depositEarnings : 0;



        $data = [

            'successful' => $successful,

            'successfulEarnings' => $successfulEarnings,

            'unsuccessful' => $unsuccessful,

            'unsuccessfulEarnings' => $unsuccessfulEarnings,

            'cancelled' => $cancelled,

            'cancelledEarnings' => $cancelledEarnings,

            'pending' => $pending,

            'pendingEarnings' => $pendingEarnings,

            'deposit' => $deposit,

            'depositEarnings' => $depositEarnings

        ];



        echo json_encode($data);

    }

    
        public function provider_callback()
    {
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);
        if ($data === null) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
            return;
        } else {
            $delivery_id = explode("XoriX", $data['delivery_id'])[0];
            $status = $data['status'];
            $code = $data['code'];
            $processed_at = $data['processed_at'];

            if ($status != 'success') {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
                return;
            }

            $pending_product = $this->db->where(['pending_id' => $delivery_id, 'api_pending' => true])->get('pending_product')->row();
            if (empty($pending_product)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
                return;
            }

            $product = $this->db->where(['id' => $pending_product->product_id])->get('product')->row();
            if (empty($product)) {
                addLog("provider_callback", "product is empty (Shop: " . $pending_product->shop_id . ")");
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
                return;
            }

            //get provider
            $provider = $this->db->where('id', $product->product_provider)->get('product_providers')->row();
            if (!$provider) {
                addLog("provider_callback", "provider is empty (Shop: " . $pending_product->shop_id . ")");
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
                return;
            }

            $stockData = [
                'product' => $code,
                'checked' => 1,
                'isActive' => 0,
                'product_id' => $pending_product->product_id
            ];

            $invData = [
                'product' => $code,
                'isActive' => 0,
                'isComment' => 1,
                'price' => $pending_product->price,
                'date' => date('d.m.Y'),
                'balance' => $pending_product->balance,
                'new_balance' => $pending_product->new_balance,
                'product_id' => $pending_product->product_id,
                'shop_id' => $pending_product->shop_id
            ];

            $this->db->trans_begin();
            $this->db->insert('stock', $stockData);
            $this->db->where('id', $pending_product->id)->update('pending_product', ['isActive' => 0]);
            $this->db->insert('invoice', $invData);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                addLog("provider_callback", "Provider {$product->product_provider} teslimatı sırasında bir hata oluştu. Dönüt SQL'e kaydedilemedi.");
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
            } else {
                $this->db->trans_commit();
                $user = $this->db->where('id', $pending_product->user_id)->get('user')->row();
                $properties = $this->db->where('id', 1)->get('properties')->row();
                addLog("provider_callback", "Provider {$product->product_provider} teslimatı başarılı bir şekilde gerçekleşti.");

                sendNotification($user->email, '<div class="orius-mail">

                        <div class="box">

                        <h1 class="logo-text">'. $properties->name .'</h1>

                        <h2>Ürünün Hesabında</h2>

                        <p>Daha önce stok olmadığı için verilemeyen ürünün şu an hesabında. Ürün bilgilerin aşağıdaki gibidir;</p>

                        <small>'. $code .'</small>

                        </div>

                        </div>');



                $this->load->helper('api');

                $api_settings = getAPIsettings();

                if ($api_settings["sms"]->neworder_enabled) {

                    sendSMSMessage($user->phone,

                        str_replace(

                            ["{name}", "{surname}"],

                            [$user->name, $user->surname],

                            $api_settings["sms"]->neworder_message

                        )

                    );

                }

                $this->output

                    ->set_content_type('application/json')

                    ->set_output(json_encode(["success" => true, "message" => "Success"]));

                return;
            }
        }
    }


    public function provider_callback_hyper()
    {
        $authorizationHeader = $this->input->get_request_header('Authorization', TRUE);
        $token = str_replace('Bearer ', '', $authorizationHeader);

        $jsonContent = file_get_contents('php://input');
        $jsonData = json_decode($jsonContent);
        addlog("provider_callback_hyper", json_encode($jsonData));

        if (!$jsonData) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
            return;
        }

        $guid = $jsonData->guid;
        $deliverID = explode("XoriX", $jsonData->DeliverID)[0];
        $status = $jsonData->Status;
        $deliveryData = $jsonData->DeliveryData;
        $deliveryData = implode(',', $deliveryData);

        $pending_product = $this->db->where(['pending_id' => $jsonData->DeliverID, 'api_pending' => true])->get('pending_product')->row();
        if (empty($pending_product)) {
            addLog("provider_callback_hyper", "Pending empty");
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
            return;
        }

        $product = $this->db->where(['id' => $pending_product->product_id])->get('product')->row();
        if (empty($product)) {
            addLog("provider_callback_hyper", "product is empty (Shop: " . $pending_product->shop_id . ")");
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
            return;
        }

        $provider = $this->db->where('id', $product->product_provider)->where('is_active', 1)->get('product_providers')->row();
        if (!$provider) {
            addLog("provider_callback_hyper", "Provider empty");
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
            return;
        }

        if ($provider->type !== "hyper") {
            addLog("provider_callback_hyper", "Not hyper");
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
            return;
        }
        $api_details = json_decode($provider->api_details);

        $hash = $guid . 1;
        $hmac = hash_hmac('sha256', $hash, $api_details->api_key, true);

        if (strtoupper(base64_encode($hmac)) != strtoupper($token)) {
            addLog("provider_callback_hyper", "Hmac wrong");
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
            return;
        }

        if ($status != 1) {
            addLog("provider_callback_hyper", "status not true");
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
            return;
        }

        $stockData = [
            'product' => $deliveryData,
            'checked' => 1,
            'isActive' => 0,
            'product_id' => $pending_product->product_id
        ];

        $invData = [
            'product' => $deliveryData,
            'isActive' => 0,
            'isComment' => 1,
            'price' => $pending_product->price,
            'date' => date('d.m.Y'),
            'balance' => $pending_product->balance,
            'new_balance' => $pending_product->new_balance,
            'product_id' => $pending_product->product_id,
            'shop_id' => $pending_product->shop_id
        ];

        $this->db->trans_begin();
        $this->db->insert('stock', $stockData);
        $this->db->where('id', $pending_product->id)->update('pending_product', ['isActive' => 0]);
        $this->db->insert('invoice', $invData);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            addLog("provider_callback", "Provider {$product->product_provider} teslimatı sırasında bir hata oluştu. Dönüt SQL'e kaydedilemedi.");
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(["success" => false, "message" => "Invalid request"]));
        } else {
            $this->db->trans_commit();
            $user = $this->db->where('id', $pending_product->user_id)->get('user')->row();
            $properties = $this->db->where('id', 1)->get('properties')->row();
            addLog("provider_callback", "Provider {$product->product_provider} teslimatı başarılı bir şekilde gerçekleşti.");

            sendNotification($user->email, '<div class="orius-mail">

                        <div class="box">

                        <h1 class="logo-text">' . $properties->name . '</h1>

                        <h2>Ürünün Hesabında</h2>

                        <p>Daha önce stok olmadığı için verilemeyen ürünün şu an hesabında. Ürün bilgilerin aşağıdaki gibidir;</p>

                        <small>' . $deliveryData . '</small>

                        </div>

                        </div>');


            $this->load->helper('api');

            $api_settings = getAPIsettings();

            if ($api_settings["sms"]->neworder_enabled) {

                sendSMSMessage($user->phone,

                    str_replace(

                        ["{name}", "{surname}"],

                        [$user->name, $user->surname],

                        $api_settings["sms"]->neworder_message

                    )

                );

            }

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(["success" => true, "message" => "Success"]));

            return;
        }
    }
}

