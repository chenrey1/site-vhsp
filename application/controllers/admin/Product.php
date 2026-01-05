<?php
// controllers/admin/Product.php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends G_Controller {

    public function __construct()
    {
        parent::__construct();
        // AJAX istekleri için admin kontrolünü atla (redirect yapma)
        $method = $this->router->method;
        if ($method == 'update_product_status' || $method == 'add_discount' || $method == 'update_discount_only') {
            // Sadece session kontrolü yap, redirect yapma
            if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
                header('Content-Type: application/json');
                die(json_encode(['status' => 'error', 'message' => 'Yetkisiz erişim']));
            }
            return;
        }
        
        if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
            redirect(base_url(), 'refresh');
            exit();
        }
    }
    
    // _remap ile edit metodunu kesin olarak yakala
    public function _remap($method, $params = array())
    {
        // Eğer edit metodu çağrılıyorsa ve products/product/{id}/product yapısındaysa
        if ($method == 'edit' && isset($params[0]) && $params[0] == 'products' && 
            isset($params[1]) && $params[1] == 'product' && isset($params[2]) && 
            isset($params[3]) && $params[3] == 'product') {
            // Doğrudan edit fonksiyonunu çağır
            return $this->edit($params[0], $params[1], $params[2], $params[3]);
        }
        
        // Eğer edit metodu çağrılıyorsa ve category/category/{id}/category yapısındaysa
        if ($method == 'edit' && isset($params[0]) && $params[0] == 'category' && 
            isset($params[1]) && $params[1] == 'category' && isset($params[2]) && 
            isset($params[3]) && $params[3] == 'category') {
            // Doğrudan edit fonksiyonunu çağır
            return $this->edit($params[0], $params[1], $params[2], $params[3]);
        }
        
        // Eğer edit metodu çağrılıyorsa ve blog/blog/{id}/blog yapısındaysa
        if ($method == 'edit' && isset($params[0]) && $params[0] == 'blog' && 
            isset($params[1]) && $params[1] == 'blog' && isset($params[2]) && 
            isset($params[3]) && $params[3] == 'blog') {
            // Doğrudan edit fonksiyonunu çağır
            return $this->edit($params[0], $params[1], $params[2], $params[3]);
        }
        
        // Diğer metodlar için normal işlem
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }
        
        // Eğer G_Controller'da genel bir metod varsa onu çağır
        if (method_exists(get_parent_class($this), '_remap')) {
            return parent::_remap($method, $params);
        }
        
        show_404();
    }

    public function index()
    {
        $data = [
            'product' => $this->db->order_by('id', 'DESC')->get('product')->result(),
            'status' => 'dashboard'
        ];

        $this->adminView('product', $data);
    }

    public function confirmAllPendingTransfer()
    {
        (isPermFunction('seeHome') != true) ? redirect(base_url('admin')) : NULL;
        $this->load->model('M_Earnings');
        $this->load->helper('api');

        // Tüm bekleyen transferleri al
        $pendingTransfers = $this->db->where('isActive', 1)->get('pending_product')->result();
        $errors = [];

        foreach ($pendingTransfers as $pendingTransfer) {
            $stock = $this->db->where('product_id', $pendingTransfer->product_id)->where('isActive', 1)->get('stock')->row();
            $properties = $this->db->where('id', 1)->get('properties')->row();
            $product = $this->db->where('id', $pendingTransfer->product_id)->get('product')->row();
            $shop = $this->db->where('id', $pendingTransfer->shop_id)->get('shop')->row();
            $user = $this->db->where('id', $shop->user_id)->get('user')->row();

            if ($stock) {
                $data = [
                    'product' => $stock->product,
                    'isActive' => 0,
                    'isComment' => 1,
                    'price' => $pendingTransfer->price,
                    'date' => date('d.m.Y H:i:s'),
                    'balance' => $pendingTransfer->balance,
                    'new_balance' => $pendingTransfer->new_balance,
                    'product_id' => $product->id,
                    'shop_id' => $pendingTransfer->shop_id,
                    'invoice_provider' => $pendingTransfer->invoice_provider,
                    'payment_commission' => $pendingTransfer->payment_commission,
                ];
                $result = $this->db->insert('invoice', $data);

                // Fatura oluştur
                $inv = $this->db->where('id', $this->db->insert_id())->get('invoice')->row();
                createInvoiceInAPI($user, $inv);

                // Verilen stok bilgisini güncelle
                $this->db->where('id', $stock->id)->update('stock', ['isActive' => 0]);
                $this->db->where('id', $pendingTransfer->id)->update('pending_product', ['isActive' => 0]);

                // Ürün onaylandı - referans bonusu ver
                $this->load->model('M_Payment');
                $this->M_Payment->processInvoiceReferralBonus($user, $inv, $shop);

                if ($result) {
                    //Mail için datayı oluştur.
                    $orderData = [
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'email' => $user->email,
                        'order_id' => $shop->id,
                        'product_name' => $product->name,
                        'product_price' => $inv->price,
                        'product_code' => $inv->product,
                        'date' => date('d.m.Y H:i')
                    ];
                    //Ürünün verildiği mailini gönder
                    $this->load->helper('mail');
                    sendDeliveryNotification($user->email, $orderData);

                    $this->M_Earnings->updateEarningPendingTransfer($pendingTransfer->id, ['transaction_status' => 'successful', 'payment_date' => date('Y-m-d H:i:s')]);

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
                    sendNotificationSite($user->id, 'Sistem Bildirimi', 'Daha önce stok olmadığı için teslim edilemeyen ürünün hesabında. Şimdi Görüntüle.', base_url('client/product'));
                } else {
                    $errors[] = 'Transfer ID ' . $product->id . ' onaylanamadı.';
                }
            } else {
                $errors[] =  $product->name . ' için stok mevcut değil.';
            }
        }

        if (empty($errors)) {
            flash('Başarılı', 'Tüm transferler onaylandı.');
        } else {
            flash('Hata', implode('<br>', $errors));
        }
        redirect(base_url('admin/dashboard'), 'refresh');
    }
    public function confirmPendingTransfer($pending_id)
    {
        (isPermFunction('seeHome') != true) ? redirect(base_url('admin')) : NULL;
        $this->load->helper('api');

        $pendingTransfer = $this->db->where('id', $pending_id)->get('pending_product')->row();
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $product = $this->db->where('id', $pendingTransfer->product_id)->get('product')->row();
        $shop = $this->db->where('id', $pendingTransfer->shop_id)->get('shop')->row();
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        $stock = $this->db->where('product_id', $pendingTransfer->product_id)->where('isActive', 1)->get('stock')->row();
        if ($stock) {
            $data = [
                'product' => $stock->product,
                'isActive' => 0,
                'isComment' => 1,
                'price' => $pendingTransfer->price,
                'date' => date('d.m.Y H:i:s'),
                'balance' => $pendingTransfer->balance,
                'new_balance' => $pendingTransfer->new_balance,
                'product_id' => $product->id,
                'shop_id' => $pendingTransfer->shop_id,
                'invoice_provider' => $pendingTransfer->invoice_provider,
                'payment_commission' => $pendingTransfer->payment_commission,
            ];
            $result = $this->db->insert('invoice', $data);
            //Fatura oluştur
            $inv = $this->db->where('id', $this->db->insert_id())->get('invoice')->row();
            createInvoiceInAPI($user, $inv);
            //Verilen stok bilgisini güncelle
            $this->db->where('id', $stock->id)->update('stock', ['isActive' => 0]);
            $this->db->where('id', $pendingTransfer->id)->update('pending_product', ['isActive' => 0]);
            // shop tablosunu güncelle
            $this->db->where('id', $pendingTransfer->shop_id)->update('shop', ['status' => 0]);
            
            // earnings tablosunu da güncelle
            $this->db->where('shop_id', $pendingTransfer->shop_id)->update('earnings', [
                'transaction_status' => 'successful',
                'payment_date' => date('Y-m-d H:i:s'),
                'description' => 'Ürün başarıyla teslim edildi.'
            ]);
            
            // Ürün onaylandı - referans bonusu ver
            $this->load->model('M_Payment');
            $this->M_Payment->processInvoiceReferralBonus($user, $inv, $shop);
            
            if ($result) {
                //Mail için datayı oluştur.
                $orderData = [
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'email' => $user->email,
                    'order_id' => $shop->id,
                    'product_name' => $product->name,
                    'product_price' => $inv->price,
                    'product_code' => $inv->product,
                    'date' => date('d.m.Y H:i')
                ];
                //Ürünün verildiği mailini gönder
                $this->load->helper('mail');
                sendDeliveryNotification($user->email, $orderData);
                //M_Earnings modelini yükle
                $this->load->model('M_Earnings');
                $this->M_Earnings->updateEarningPendingTransfer($pendingTransfer->id, ['transaction_status' => 'successful', 'payment_date' => date('Y-m-d H:i:s')]);

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
                sendNotificationSite($user->id, 'Sistem Bildirimi', 'Daha önce stok olmadığı için teslim edilemeyen ürünün hesabında. Şimdi Görüntüle.', base_url('client/product'));
                flash('Başarılı', 'Transfer Onaylandı.');
            }else{
                flash('Başarısız', 'Bir Sorundan Ötürü Transfer Onaylanamadı.');
            }
            redirect(base_url('admin/productHistory'), 'refresh');
        }else{
            flash('UPS!', 'Bu ürünün stoğu hala mevcut değil.');
            redirect(base_url('admin/productHistory'), 'refresh');
        }
    }
    public function deletePendingTransfer($pending_id)
    {
        (isPermFunction('seeHome') != true) ? redirect(base_url('admin')) : NULL;

        $this->load->model('M_Subscription');

        $pendingTransfer = $this->db->where('id', $pending_id)->get('pending_product')->row();
        $product = $this->db->where('id', $pendingTransfer->product_id)->get('product')->row();
        $shop = $this->db->where('id', $pendingTransfer->shop_id)->get('shop')->row();
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();

        //eğer pendingtransfer->shop_id user_savings altında varsa
        $user_savings = $this->db->where('shop_id', $pendingTransfer->shop_id)->get('user_savings')->row();
        if ($user_savings) {
            $bonusAmount = $this->M_Subscription->cancelProductBonus($pendingTransfer);
            $refundBalance = $pendingTransfer->price - $bonusAmount;
            
            // Kullanıcıya tam ürün bedeli iade edilecek
            $result = $this->db->where('id', $user->id)->update('user', ['balance' => $user->balance + $refundBalance]);
            
            if ($result) {
                // Tam ürün bedeli iadesi için ilk işlem kaydı
                $transaction_data = [
                    'user_id' => $user->id,
                    'transaction_type' => 'purchase',
                    'amount' => $pendingTransfer->price,
                    'description' => 'Ürün iptali bakiye iadesi - ' . $product->name,
                    'status' => 1, // Onaylı
                    'created_at' => date('Y-m-d H:i:s'),
                    'balance_before' => $user->balance, // İşlem öncesi bakiye
                    'balance_after_transaction' => $user->balance + $pendingTransfer->price, // Güncellenmiş bakiye
                    'related_id' => $pendingTransfer->shop_id
                ];
                $this->db->insert('wallet_transactions', $transaction_data);
                
                // Bonus iptal kaydı (negatif değer)
                $bonus_cancel_data = [
                    'user_id' => $user->id,
                    'transaction_type' => 'purchase',
                    'amount' => -$bonusAmount,
                    'description' => 'Abonelik bonusu iptali - ' . $product->name,
                    'status' => 1, // Onaylı
                    'created_at' => date('Y-m-d H:i:s'),
                    'balance_before' => $user->balance + $pendingTransfer->price, // İade sonrası bakiye
                    'balance_after_transaction' => $user->balance + $refundBalance, // Bonus iptali sonrası bakiye
                    'related_id' => $pendingTransfer->shop_id
                ];
                $this->db->insert('wallet_transactions', $bonus_cancel_data);
                
                //orderDatayı hazırla
                $orderData = [
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'email' => $user->email,
                    'order_id' => $shop->id,
                    'product_name' => $product->name,
                    'product_price' => $pendingTransfer->price,
                    'date' => date('d.m.Y H:i')
                ];
                //Ürünün verildiği mailini gönder
                $this->load->helper('mail');
                sendCancelNotification($user->email, $orderData);

                $this->load->model('M_Earnings');
                $this->M_Earnings->updateEarningPendingTransfer($pendingTransfer->id, ['transaction_status' => 'cancelled', 'payment_date' => date('Y-m-d H:i:s'), 'amount' => $pendingTransfer->price, 'total' => $pendingTransfer->price]);
                sendNotificationSite($user->id, 'Sistem Bildirimi', $product->name . ' Adlı ürün için olan siparişin iptal edildi. Bakiyen iade edildi.', base_url('client/product'));
                // İptal edilen ödemeyi user_dealers tablosundaki total_purchase değerinden düş
                $this->load->model('M_Dealer');
                $this->M_Dealer->updateUserTotalPurchase($user->id, -$pendingTransfer->price);
                // pending_product kaydını sil
                $this->db->where('id', $pending_id)->delete('pending_product');
                flash('Başarılı', 'Bakiye iade edildi.');
                redirect(base_url('admin/productHistory'));
            } else {
                flash('Başarılı', 'Bir Sorundan Ötürü İptal Edilemedi.');
                redirect(base_url('admin/productHistory'));
            }
        } else {
            $refundBalance = $pendingTransfer->price;
            
            // shop tablosunu güncelle
            $this->db->where('id', $pendingTransfer->shop_id)->update('shop', ['status' => 2]);
            
            // earnings tablosunu da güncelle
            $this->db->where('shop_id', $pendingTransfer->shop_id)->update('earnings', [
                'transaction_status' => 'cancelled',
                'payment_date' => date('Y-m-d H:i:s'),
                'description' => 'Ürün teslimi iptal edildi.'
            ]);

            $result = $this->db->where('id', $user->id)->update('user', ['balance' => $user->balance + $refundBalance]);
            if ($result) {
                // İade işlemi için bakiye işlem kaydını oluştur
                $transaction_data = [
                    'user_id' => $user->id,
                    'transaction_type' => 'purchase',
                    'amount' => $refundBalance,
                    'description' => 'Ürün iptali bakiye iadesi - ' . $product->name,
                    'status' => 1, // Onaylı
                    'created_at' => date('Y-m-d H:i:s'),
                    'balance_before' => $user->balance, // İşlem öncesi bakiye
                    'balance_after_transaction' => $user->balance + $refundBalance, // Güncellenmiş bakiye
                    'related_id' => $pendingTransfer->shop_id
                ];
                
                $this->db->insert('wallet_transactions', $transaction_data);
                
                //orderDatayı hazırla
                $orderData = [
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'email' => $user->email,
                    'order_id' => $shop->id,
                    'product_name' => $product->name,
                    'product_price' => $pendingTransfer->price,
                    'date' => date('d.m.Y H:i')
                ];
                //Ürünün verildiği mailini gönder
                $this->load->helper('mail');
                sendCancelNotification($user->email, $orderData);

                $this->load->model('M_Earnings');
                $this->M_Earnings->updateEarningPendingTransfer($pendingTransfer->id, ['transaction_status' => 'cancelled', 'payment_date' => date('Y-m-d H:i:s'), 'amount' => $pendingTransfer->price, 'total' => $pendingTransfer->price]);
                sendNotificationSite($user->id, 'Sistem Bildirimi', $product->name . ' Adlı ürün için olan siparişin iptal edildi. Bakiyen iade edildi.', base_url('client/product'));
                // İptal edilen ödemeyi user_dealers tablosundaki total_purchase değerinden düş
                $this->load->model('M_Dealer');
                $this->M_Dealer->updateUserTotalPurchase($user->id, -$pendingTransfer->price);
                // pending_product kaydını sil
                $this->db->where('id', $pending_id)->delete('pending_product');
                flash('Başarılı', 'Bakiye iade edildi.');
                redirect(base_url('admin/productHistory'));
            } else {
                flash('Başarılı', 'Bir Sorundan Ötürü İptal Edilemedi.');
                redirect(base_url('admin/productHistory'));
            }
        }
    }
    public function confirmGiveProduct($invoice_id)
    {
        (isPermFunction('seeHome') != true) ? redirect(base_url('admin')) : NULL;
        $invoice = $this->db->where('id', $invoice_id)->get('invoice')->row();
        $shop = $this->db->where('id', $invoice->shop_id)->get('shop')->row();
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        $product = $this->db->where('id', $invoice->product_id)->get('product')->row();
        $this->db->where('id', $invoice->shop_id)->update('shop', ['status'=>0]);
        $result = $this->db->where('id', $invoice_id)->update('invoice', ['isActive' => 0]);

        // earnings tablosunu da güncelle
        $this->db->where('shop_id', $invoice->shop_id)->update('earnings', [
            'transaction_status' => 'successful',
            'payment_date' => date('Y-m-d H:i:s'),
            'description' => 'Ürün başarıyla teslim edildi.'
        ]);

        // Ürün onaylandı - referans bonusu ver
        $this->load->model('M_Payment');
        $this->M_Payment->processInvoiceReferralBonus($user, $invoice, $shop);

        if ($result) {
            //Mail için datayı oluştur.
            $orderData = [
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'order_id' => $shop->id,
                'product_name' => $product->name,
                'product_price' => $invoice->price,
                'product_code' => $invoice->product,
                'date' => date('d.m.Y H:i')
            ];
            //Ürünün verildiği mailini gönder
            $this->load->helper('mail');
            sendDeliveryNotification($user->email, $orderData);

            $this->load->model('M_Earnings');
            //create array data in earnings table amount, total, transaction_status, payment_date, description
            $data = [
                'transaction_status' => 'successful',
                'payment_date' => date('Y-m-d H:i:s'),
                'description' => 'Ürün gönderimi tamamlandı.'
            ];

            $this->M_Earnings->updateEarningByInvoice($invoice->id, $data);
            $this->load->helper('api');
            createInvoiceInAPI($user, $invoice);
            sendNotificationSite($user->id, 'Sistem Bildirimi', $product->name . ' Ürününü içeren siparişin teslim edildi.', base_url('client/product'));
            
            $this->load->helper('provider');
            process_order_callback($invoice->shop_id);
            
            flash('Başarılı', 'Ürün Gönderimi Onaylandı.');
            redirect(base_url('admin/productHistory'));
        }else{
            sendNotificationSite($user->id, 'Sistem Bildirimi', $product->name . ' Ürününü içeren siparişin iptal edildi. Bakiyen iade edildi.', base_url('client/product'));
            flash('Başarılı', 'Bir Sorundan Ötürü Gönderim Onaylanamadı.');
            redirect(base_url('admin/productHistory'));
        }
    }
    public function cancelGiveProduct($invoice_id)
    {
        (isPermFunction('seeHome') != true) ? redirect(base_url('admin')) : NULL;

        $this->load->model('M_Subscription');
        $this->load->model('M_Earnings');

        $invoice = $this->db->where('id', $invoice_id)->get('invoice')->row();
        $this->db->where('id', $invoice->shop_id)->update('shop', ['status'=>0]);
        $result = $this->db->where('id', $invoice_id)->update('invoice', ['isActive' => 2]);
        $shop = $this->db->where('id', $invoice->shop_id)->get('shop')->row();
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        $product = $this->db->where('id', $invoice->product_id)->get('product')->row();

        // earnings tablosunu da güncelle
        $this->db->where('shop_id', $invoice->shop_id)->update('earnings', [
            'transaction_status' => 'cancelled',
            'payment_date' => date('Y-m-d H:i:s'),
            'description' => 'Ürün teslimi iptal edildi.'
        ]);

        //eğer $invoice->shop_id user_savings altında varsa
        $user_savings = $this->db->where('shop_id', $invoice->shop_id)->get('user_savings')->row();
        if ($user_savings) {
            $refundBalance = $this->M_Subscription->cancelProductBonusNoStock($invoice->id);
            $refundBalance = $invoice->price - $refundBalance;
        }else{
            $refundBalance = $invoice->price;
        }

        if ($result) {
            //orderDatayı hazırla
            $orderData = [
                'name' => $user->name,
                'surname' => $user->surname,
                'email' => $user->email,
                'order_id' => $shop->id,
                'product_name' => $product->name,
                'product_price' => $invoice->price,
                'date' => date('d.m.Y H:i')
            ];
            //Ürünün verildiği mailini gönder
            $this->load->helper('mail');
            sendCancelNotification($user->email, $orderData);

            $earning = $this->M_Earnings->getEarningByInvoice($invoice->id);
            //create array data in earnings table amount, total, transaction_status, payment_date, description
            $data = [
                'amount' => $invoice->price,
                'total' => $earning->total - $invoice->price,
                'transaction_status' => 'cancelled',
                'payment_date' => date('Y-m-d H:i:s'),
                'description' => 'Ürün gönderimi iptal edildi.'
            ];

            $this->load->model('M_Earnings');
            $this->M_Earnings->updateEarningByInvoice($invoice->id, $data);
            $this->db->where('id', $shop->user_id)->update('user', ['balance' => $user->balance + $refundBalance]);
            
            // İade işlemi için bakiye işlem kaydını oluştur
            $transaction_data = [
                'user_id' => $shop->user_id,
                'transaction_type' => 'refund',
                'amount' => $refundBalance,
                'description' => 'Ürün iptali bakiye iadesi - ' . $product->name,
                'status' => 1, // Onaylı
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance, // İşlem öncesi bakiye
                'balance_after_transaction' => $user->balance + $refundBalance, // Güncellenmiş bakiye
                'related_id' => $invoice->shop_id
            ];
            
            $this->db->insert('wallet_transactions', $transaction_data);
            
            flash('Başarılı', 'Bakiye iade edildi.');
            redirect(base_url('admin/productHistory'));
        }else{
            flash('Başarılı', 'Bir Sorundan Ötürü İptal Edilemedi.');
            redirect(base_url('admin/productHistory'));
        }
    }
    public function detail($id)
    {
        if (empty($id)) {
            flash('Ups.', 'Ürünü Bulamadık.');
            redirect(base_url('admin/product'));
        }else{
            (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
            $properties = $this->db->where('id', 1)->get('properties')->row();
            $product = $this->db->where('id', $id)->get('product')->row();
            $product_providers = $this->db->select('id, name')->where('is_active', 1)->get('product_providers')->result();
            
            $data = [
                'product' => $product,
                'categories' => $this->db->where('isActive', 1)->get('category')->result(),
                'status' => 'store',
                'product_providers' => $product_providers
            ];

            $this->adminView('product-detail', $data);
        }
    }
    

    public function products()
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'product' => $this->db->where('isActive !=', 0)->where('isActive !=', 3)->get('product')->result(),
            'status' => 'store'
        ];

        $this->adminView('product', $data);
    }

    public function editProductPrice()
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        
        $percent = $this->input->post('percent');
        $choose = $this->input->post('choose');
        $pricing_type = $this->input->post('pricing_type');
        $category_id = $this->input->post('category_id');

        // Ürünleri getir (discount alanını da dahil et)
        $this->db->select('*');
        $this->db->where('isActive', 1);
        
        if ($pricing_type === 'category' && !empty($category_id)) {
            $this->db->where('category_id', $category_id);
        }
        
        $products = $this->db->get('product')->result();

        if (empty($products)) {
            flash('Uyarı', 'Fiyatlandırma yapılacak ürün bulunamadı.');
            redirect(base_url('admin/products'), 'refresh');
            return;
        }

        $updated_count = 0;
        foreach ($products as $product) {
            // Ana fiyat hesaplama
            $price_amount = ($product->price * $percent) / 100;

            if ($choose == 1) {
                $new_price = round($product->price + $price_amount, 2);
            } else {
                $new_price = round($product->price - $price_amount, 2);
            }

            // Fiyat sıfırdan küçük veya eşit olamaz
            if ($new_price <= 0) {
                $new_price = 1;
            }

            // Güncelleme verilerini hazırla
            $data = ['price' => $new_price];

            // Discount hesaplama (sadece 0'dan büyükse)
            if ($product->discount > 0) {
                $discount_amount = ($product->discount * $percent) / 100;
                
                if ($choose == 1) {
                    $new_discount = round($product->discount + $discount_amount, 2);
                } else {
                    $new_discount = round($product->discount - $discount_amount, 2);
                }

                // Discount sıfırdan küçük olamaz
                if ($new_discount < 0) {
                    $new_discount = 0;
                }

                // KRITIK: İndirim fiyatı ana fiyattan yüksek veya eşit olamaz
                if ($new_discount >= $new_price) {
                    $new_discount = max(0, $new_price - 1);
                }

                $data['discount'] = $new_discount;
            }
            
            if ($this->db->where('id', $product->id)->update('product', $data)) {
                $updated_count++;
            }
        }

        if ($updated_count > 0) {
            $message = $updated_count . ' ürünün fiyatı başarıyla güncellendi.';
            if ($pricing_type === 'category') {
                $category = $this->db->where('id', $category_id)->get('category')->row();
                if ($category) {
                    $message .= ' (' . $category->name . ' kategorisi)';
                }
            }
            flash('Başarılı', $message);
        } else {
            flash('Hata', 'Fiyat güncelleme işlemi başarısız.');
        }
        
        redirect(base_url('admin/products'), 'refresh');
    }

    public function getPricePreview()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        (isPermFunction('seeProduct') != true) ? $this->output->set_status_header(403) : NULL;
        
        $percent = $this->input->post('percent');
        $direction = $this->input->post('direction');
        $pricing_type = $this->input->post('pricing_type');
        $category_id = $this->input->post('category_id');

        // Validasyon
        if (empty($percent) || $percent <= 0) {
            echo json_encode(['success' => false, 'message' => 'Geçerli bir yüzde değeri girin.']);
            return;
        }

        if ($pricing_type === 'category' && empty($category_id)) {
            echo json_encode(['success' => false, 'message' => 'Lütfen bir kategori seçin.']);
            return;
        }

        // Ürünleri getir
        $this->db->select('id, name, price, discount, category_id');
        $this->db->where('isActive', 1);
        
        if ($pricing_type === 'category' && !empty($category_id)) {
            $this->db->where('category_id', $category_id);
        }
        
        $this->db->limit(20); // Önizleme için maksimum 20 ürün
        $products = $this->db->get('product')->result();

        if (empty($products)) {
            echo json_encode(['success' => false, 'message' => 'Önizleme yapılacak ürün bulunamadı.']);
            return;
        }

        $preview_data = [];
        foreach ($products as $product) {
            // Ana fiyat hesaplama
            $price_amount = ($product->price * $percent) / 100;
            
            if ($direction == 1) {
                $new_price = round($product->price + $price_amount, 2);
            } else {
                $new_price = round($product->price - $price_amount, 2);
            }

            // Fiyat sıfırdan küçük veya eşit olamaz
            if ($new_price <= 0) {
                $new_price = 1;
            }

            // Discount hesaplama (sadece 0'dan büyükse)
            $new_discount = $product->discount;
            if ($product->discount > 0) {
                $discount_amount = ($product->discount * $percent) / 100;
                
                if ($direction == 1) {
                    $new_discount = round($product->discount + $discount_amount, 2);
                } else {
                    $new_discount = round($product->discount - $discount_amount, 2);
                }

                // Discount sıfırdan küçük olamaz
                if ($new_discount < 0) {
                    $new_discount = 0;
                }

                // KRITIK: İndirim fiyatı ana fiyattan yüksek veya eşit olamaz
                if ($new_discount >= $new_price) {
                    $new_discount = max(0, $new_price - 1);
                }
            }

            $preview_data[] = [
                'id' => $product->id,
                'name' => (strlen($product->name) > 30) ? substr($product->name, 0, 30) . '...' : $product->name,
                'current_price' => $product->price,
                'new_price' => $new_price,
                'current_discount' => $product->discount,
                'new_discount' => $new_discount,
                'has_discount' => $product->discount > 0,
                'current_price_formatted' => number_format($product->price, 2),
                'new_price_formatted' => number_format($new_price, 2),
                'current_discount_formatted' => number_format($product->discount, 2),
                'new_discount_formatted' => number_format($new_discount, 2)
            ];
        }

        echo json_encode([
            'success' => true,
            'products' => $preview_data,
            'total_count' => count($products)
        ]);
    }

    public function sendTestMail()
    {
        $host = $this->input->post('host');
        $port = $this->input->post('port');
        $mail = $this->input->post('mail');
        $password = $this->input->post('password');

        $this->load->library('email');
        $properties = $this->db->where('id', 1)->get('properties')->row();

        $config = [
            'protocol' => 'smtp',
            'smtp_host' => $host,
            'smtp_port' => $port,
            'smtp_user' => $mail,
            'smtp_pass' => $password,
            'starttls' => true,
            'charset' => 'utf-8',
            'mailtype' => 'html',
            'wordwrap' => true,
            'newline' => "\r\n"
        ];

        $this->email->initialize($config);
        $this->email->from($mail, $properties->name);
        $this->email->to($mail);
        $this->email->subject("Test Maili");
        $this->email->message('Test Maili Gönderildi.');

        // Debug bilgisini önce alalım
        $debug = $this->email->print_debugger(array('headers', 'subject', 'body'));

        $send = $this->email->send(FALSE);

        header('Content-Type: application/json');
        if ($send) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Mail Gönderildi.',
                'debug' => $debug
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Mail Gönderilemedi.',
                'debug' => $debug
            ]);
        }
        exit;
    }

    public function sendMail()
    {
        if (!empty($this->input->post('content'))) {

            $smtp = $this->db->where('id', 1)->get('smtp')->row();
            if (empty($smtp->mail) && empty($smtp->password)) { flash('Ups.', 'SMTP bilgilerin eksik'); redirect(base_url('admin/sendMail'), 'refresh'); }

            $content = $this->input->post('content');

            $this->load->library('email');
            $properties = $this->db->where('id', 1)->get('properties')->row();
            $adminMail = $this->db->where('isAdmin', 1)->get('user')->row();
            $config = [
                'protocol' => 'smtp',
                'smtp_host' => $smtp->host,
                'smtp_port' => $smtp->port,
                'smtp_user' => $smtp->mail,
                'smtp_pass' => $smtp->password,
                'starttls' => true,
                'charset' => 'utf-8',
                'mailtype' => 'html',
                'wordwrap' => true,
                'newline' => "\r\n"
            ];

            $hash = randString(25) . date('d');
            $users = $this->db->where('isMail', 1)->where('isActive', 1)->get('user')->result();
            $userList = "";
            foreach ($users as $user) {
                $this->email->initialize($config);
                $this->email->from($smtp->mail, $properties->name);
                $this->email->to($user->email);
                $this->email->subject($properties->name . " Bildirim");
                $this->email->message('<html lang="tr">
			      <head>
		              <meta charset="UTF-8">
		              <meta http-equiv="X-UA-Compatible" content="IE=edge">
		              <meta name="viewport" content="width=device-width, initial-scale=1.0">
		              <title>Mail</title>
		          </head>
		          <style>
		              html {
		                  font-family: "Segoe UI";
		              }
		              .orius-mail {
		                  padding: 20px;
		              }
		              .orius-mail .box {
		                  border: 3px solid #D1D1D1;
		                  padding: 20px;
		              }
		              .orius-mail .box small {
		                  color: #6c757d;
		                  margin-top: 50px;
		                  display: block;
		              }
		              .orius-mail .box .logo {
		                  max-height: 50px;
		                  width: auto;
		              }
		              .orius-mail .box .logo-text {
		                  margin-top: 0;
		                  text-transform: uppercase;
		                  border-bottom: 3px solid #007bff;
		                  padding-bottom: 5px;
		              }
		              .orius-mail .box a {
		                  color: #007bff;
		                  font-weight: 500;
		              }
		          </style>
		          <body>
		              
		                '.$this->input->post('content').'
		                <small>Mail almak istemiyorsanız kullanıcı panelinizden tercihinizi değiştirebilirsiniz.</small>
		          </body>
		          </html>');
                $send = $this->email->send();
            }

            if ($send) {
                flash('Başarılı', 'Mail Gönderildi.');
                redirect(base_url('admin/users'));
            }else{
                flash('Başarılı', 'Bir Sorundan Ötürü Mail Gönderilemedi.');
                redirect(base_url('admin/users'));
            }

        }else{

            $data = [
                'properties' => $this->db->where('id', 1)->get('properties')->row(),
                'status' => 'users'
            ];

            $this->adminView('send-mail', $data);
        }
    }

    public function addProduct()
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        $properties = $this->db->where('id', 1)->get('properties')->row();

        //product_providers = id, name, type, is_active, base_url, api_details, created_at, updated_at (api_details json ve type hyper için api_key, api_token olacak orius için mail, password)
        $product_providers = $this->db->select('id, name')->where('is_active', 1)->get('product_providers')->result();
        $data = [
			'category' => $this->db->where('isActive', 1)->get('category')->result(),
			'status' => 'store',
            'product_providers' => $product_providers
		];

        $this->adminView('add-product', $data);
    }

    public function disableProduct($id)
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'product_id' => $id
        ];

        $result = $this->db->where('id', $id)->update('product', ['isActive'=>0]);
        $stocks = $this->db->where($data)->get('stock')->result();
        foreach ($stocks as $s) {
            $this->db->where('id', $s->id)->update('stock', ['isActive'=>0]);
        }

        if ($result) {
            flash('Başarılı', 'Ürün Silindi');
            redirect(base_url('admin/products'));
        }else{
            flash('Başarılı', 'Bir Sorundan Ötürü Ürün Silinemedi.');
            redirect(base_url('admin/products'));
        }
    }

    public function stock()
    {
        (isPermFunction('seeStocks') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'products' => $this->db->where('isActive', 1)->get('product')->result(),
            'stocks' => $this->db->where('isActive', 1)->get('stock')->result(),
            'status' => 'store'
        ];
        $this->adminView('add-stock', $data);
    }

    public function addStock()
    {
        (isPermFunction('seeStocks') != true) ? redirect(base_url('admin')) : NULL;
        $product_id = $this->input->post('product_id');
        $product = $this->input->post('product');

        if (empty($product_id))
        {
            flash('Başarısız', 'Ürün Seçimi Yapılmamış.');
            redirect(base_url('admin/product/stock'));
        }

        $productExplode = explode("\n", $product);

        $i = 0;
        foreach ($productExplode as $pe)
        {
            $data = [
                'product_id' => $product_id,
                'product' => $pe,
                'isActive' => '1',
                'checked' => '2'
            ];
            $result = $this->db->insert('stock', $data);
            $i++;
        }

        if ($result) {
            flash('Başarılı', $i . ' Stok Eklendi.');
            redirect(base_url('admin/product/stock'));
        }else{
            flash('Başarılı', 'Bir Sorundan Ötürü Veri Eklenemedi.');
            redirect(base_url('admin/product/stock'));
        }

    }

    public function productHistory()
    {
        (isPermFunction('seeSellHistory') != true) ? redirect(base_url('admin')) : NULL;
        
        // Bekleyen ürünleri getir
        $pending = $this->db->select('
                pending_product.*,
                user.name as user_name,
                user.surname as user_surname,
                product.name as product_name,
                product.img as product_image
            ')
            ->order_by('pending_product.id', 'DESC')
            ->from('pending_product')
            ->join('shop', 'shop.id = pending_product.shop_id')
            ->join('user', 'user.id = shop.user_id')
            ->join('product', 'product.id = pending_product.product_id')
            ->where('pending_product.isActive', 1)
            ->get()
            ->result();

        // Geri dönüşü olan ürünleri getir
        $invoices = $this->db->select('
                invoice.*,
                product.name as product_name,
                product.img as product_image,
                user.name as user_name,
                user.surname as user_surname,
                user.id as user_id,
                shop.id as shop_id
            ')
            ->order_by('invoice.id', 'DESC')
            ->from('invoice')
            ->join('product', 'product.id = invoice.product_id')
            ->join('shop', 'shop.id = invoice.shop_id')
            ->join('user', 'user.id = shop.user_id')
            ->where('invoice.extras IS NOT NULL')
            ->where('invoice.extras !=', '')
            ->where('invoice.isActive', 1)
            ->get()
            ->result();

        $data = [
            'pending' => $pending,
            'invoices' => $invoices,
            'status' => 'sell-history'
        ];

        $this->adminView('sell-history', $data);
    }

    public function invoice($id)
    {
        (isPermFunction('seeSellHistory') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'status' => 'dashboard',
            'shop' => $this->db->where('id', $id)->get('shop')->row(),
        ];

        $this->adminView('invoice', $data);
    }

    public function bankTransfer()
    {
        (isPermFunction('seeTransfer') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'transfers' => $this->db->order_by('id', 'DESC')->where('isActive', 1)->get('bank_transfer')->result(),
            'status' => 'bankTransfer'
        ];

        $this->adminView('transfer', $data);
    }

    public function confirmTransfer($id, $user_id, $price)
    {
        (isPermFunction('seeTransfer') != true) ? redirect(base_url('admin')) : NULL;
        $user = $this->db->where('id', $user_id)->get('user')->row();
        $this->db->where('id', $id)->update('bank_transfer', ['isActive'=>0]);
        $balance = $user->balance + $price;
        $result = $this->db->where('id', $user_id)->update('user', ['balance'=>$balance]);

        if ($result) {
            // Eski wallet transaction kaydını pasif yap
            $this->db->where('related_id', $id)
                     ->update('wallet_transactions', ['status' => 3]); // 3: Yeni kayıtla değiştirildi
            
            // Yeni bir wallet transaction kaydı oluştur (güncel tarihle)
            $transaction_data = [
                'user_id' => $user_id,
                'transaction_type' => 'deposit',
                'amount' => $price,
                'description' => 'Banka havalesi ile bakiye yükleme',
                'status' => 1, // Onaylı
                'related_id' => $id,
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance,
                'balance_after_transaction' => $balance,
                'balance_type' => 'spendable' // Kullanılabilir bakiye
            ];
            $this->db->insert('wallet_transactions', $transaction_data);

            // Earnings tablosuna kayıt ekle
            $earning_data = [
                'transaction_date'      => date('Y-m-d H:i:s'),
                'seller_id'             => 0,
                'buyer_id'              => $user_id,
                'product_id'            => 0,
                'invoice_id'            => 0,
                'shop_id'               => 0,
                'pending_product_id'    => 0,
                'amount'                => $price,
                'total'                 => $price,
                'payment_method'        => 'bank_transfer',
                'payment_date'          => date('Y-m-d H:i:s'),
                'transaction_status'    => 'successful',
                'description'           => 'Banka havalesi ile bakiye yükleme',
                'seller_type'           => 'site',
                'payment_type'          => 'deposit',
            ];
            $this->db->insert('earnings', $earning_data);

            // Shop tablosuna havale onayı ile kayıt oluştur
            $shop_data = [
                'price'             => $price,
                'date'              => date('Y-m-d H:i:s'),
                'status'            => 0, // Onaylandı
                'order_id'          => 'BT-'.$id,
                'user_id'           => $user_id,
                'product'           => 'bank_transfer',
                'type'              => 'deposit',
                'seller_id'         => 0,
                'ip_address'        => $this->input->ip_address(),
                'balance'           => $user->balance,
                'new_balance'       => $balance,
                'coupon'            => '',
                'invoice_status'    => 'in_system',
                'invoice_provider'  => 'disabled',
                'payment_commission'=> 0
            ];
            $this->db->insert('shop', $shop_data);

            sendNotification($user->email, '<div class="orius-mail">
				<div class="box">
				<h1 class="logo-text">'. $properties->name .'</h1>
				<h2>Bakiye Bilgilendirmesi</h2>
				<p>Havale talebin onaylandı. Bakiyen güncellendi.</p>
				</div>
				</div>');
            
            // Site içi bildirim gönder
            sendNotificationSite($user->id, 'Sistem Bildirimi', 'Havale talebin onaylandı ve ' . number_format($price, 2) . ' TL bakiyene eklendi.', base_url('client/balance?tab=bakiye-gecmisi'));
            
            flash('Başarılı', 'Transfer Onaylandı. Bakiye Güncellendi');
            redirect(base_url('admin/product/bankTransfer'));
        }else{
            flash('Başarılı', 'Bir Sorundan Ötürü Bakiye Eklenemedi.');
            redirect(base_url('admin/product/bankTransfer'));
        }
    }

    public function cancelTransfer($id)
    {
        (isPermFunction('seeTransfer') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'isActive' => 0
        ];

        $result = $this->db->where('id', $id)->update('bank_transfer', $data);

        if ($result) {
            // İptal edilen havale talebine ait transaction kaydını güncelle
            $this->db->where('related_id', $id)
                     ->update('wallet_transactions', ['status' => 2]);
                     
            // Havale bilgisini ve kullanıcı bilgisini al
            $transfer = $this->db->where('id', $id)->get('bank_transfer')->row();
            if ($transfer) {
                $user = $this->db->where('id', $transfer->user_id)->get('user')->row();
                if ($user) {
                    // Site içi bildirim gönder
                    sendNotificationSite($user->id, 'Sistem Bildirimi', 'Havale talebin reddedildi. Detaylı bilgi için müşteri hizmetleriyle iletişime geçebilirsin.', base_url('client/balance?tab=bakiye-gecmisi'));
                }
            }
            
            flash('Başarılı', 'Transfer Reddedildi.');
            redirect(base_url('admin/product/bankTransfer'));
        }else{
            flash('Başarılı', 'Bir Sorundan Ötürü İşlem Tamamlanamadı.');
            redirect(base_url('admin/product/bankTransfer'));
        }
    }

    public function category() {
        (isPermFunction('seeCategory') != true) ? redirect(base_url('admin')) : NULL;

        // Ana kategorileri al
        $this->db->where('mother_category_id', 0);
        $this->db->where('isActive', 1);  // Aktif ana kategoriler
        $this->db->order_by('sort_order', 'ASC');
        $main_categories = $this->db->get('category')->result();

        // Her ana kategori için alt kategorileri hazırla
        foreach($main_categories as &$category) {
            $category->product_count = $this->db->where('category_id', $category->id)
                ->where('isActive', 1)
                ->count_all_results('product');
            // Tüm derinliklerde alt kategorileri getir
            $category->sub_categories = $this->fetchChildren($category->id);
        }

        $data = [
            'status' => 'dashboard',
            'category' => $main_categories,
            'categories' => $this->db->where('isActive', 1)->get('category')->result(),
        ];

        $this->adminView('category', $data);
    }

    private function fetchChildren($parentId) {
        $this->db->where('mother_category_id', $parentId);
        $this->db->where('isActive', 1);
        $this->db->order_by('sort_order', 'ASC');
        $children = $this->db->get('category')->result();

        foreach ($children as &$child) {
            $child->product_count = $this->db->where('category_id', $child->id)
                ->where('isActive', 1)
                ->count_all_results('product');
            $child->sub_categories = $this->fetchChildren($child->id);
        }

        return $children;
    }

    public function getCategoryData($id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $category = $this->db->where('id', $id)->get('category')->row();

        if (!$category) {
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'Kategori bulunamadı']);
            return;
        }

        echo json_encode($category);
    }

    private function getOrderedCategories() {
        $this->db->where('mother_category_id', 0);
        // Sadece aktif kategorileri dahil et
        $this->db->where('isActive', 1);
        $this->db->order_by('sort_order', 'ASC');
        $main_categories = $this->db->get('category')->result();

        $ordered_categories = [];

        foreach($main_categories as $main) {
            $ordered_categories[] = $main;
            $this->addSubCategories($main->id, $ordered_categories);
        }

        return $ordered_categories;
    }

    private function addSubCategories($parent_id, &$ordered_categories) {
        $this->db->where('mother_category_id', $parent_id);
        // Sadece aktif kategorileri dahil et
        $this->db->where('isActive', 1);
        $this->db->order_by('sort_order', 'ASC');
        $sub_categories = $this->db->get('category')->result();

        foreach($sub_categories as $sub) {
            $ordered_categories[] = $sub;
            $this->addSubCategories($sub->id, $ordered_categories);
        }
    }

    public function getCategories() {
        // Önce ana kategorileri al
        $this->db->where('mother_category_id', 0);
        // Sadece aktif kategorileri dahil et
        $this->db->where('isActive', 1);
        $this->db->order_by('sort_order', 'ASC');
        $main_categories = $this->db->get('category')->result();

        $ordered_categories = [];

        // Her ana kategori için
        foreach($main_categories as $main) {
            // Ana kategoriyi ekle
            $ordered_categories[] = $main;

            // Alt kategorileri bul ve ekle
            $this->db->where('mother_category_id', $main->id);
            // Sadece aktif kategorileri dahil et
            $this->db->where('isActive', 1);
            $this->db->order_by('sort_order', 'ASC');
            $sub_categories = $this->db->get('category')->result();

            foreach($sub_categories as $sub) {
                $ordered_categories[] = $sub;
            }
        }

        return $ordered_categories;
    }

    public function ajaxMoveCategory() {
        // Ajax isteği kontrolü
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Direct access not allowed']);
            return;
        }

        $category_id = $this->input->post('category_id');
        $direction = $this->input->post('direction');

        if (empty($category_id) || empty($direction)) {
            echo json_encode(['success' => false, 'message' => 'Eksik parametre']);
            return;
        }

        // Kategoriyi bul
        $current_category = $this->db->where('id', $category_id)->get('category')->row();

        if (!$current_category) {
            echo json_encode(['success' => false, 'message' => 'Kategori bulunamadı']);
            return;
        }

        // Aynı seviyedeki kategorileri bul (ana kategori veya aynı ana kategoriye sahip alt kategoriler)
        $this->db->where('mother_category_id', $current_category->mother_category_id);
        // Sadece aktif kategorileri dahil et
        $this->db->where('isActive', 1);
        $this->db->order_by('sort_order', 'ASC');
        $this->db->order_by('id', 'ASC'); // İkincil sıralama
        $siblings = $this->db->get('category')->result();

        // Mevcut kategorinin index'ini bul
        $current_index = false;
        foreach ($siblings as $index => $sibling) {
            if ($sibling->id == $category_id) {
                $current_index = $index;
                break;
            }
        }

        if ($current_index === false) {
            echo json_encode(['success' => false, 'message' => 'Kategori kardeşleri arasında bulunamadı']);
            return;
        }

        if ($direction == 'up' && $current_index > 0) {
            $swap_with = $siblings[$current_index - 1];
        } elseif ($direction == 'down' && $current_index < (count($siblings) - 1)) {
            $swap_with = $siblings[$current_index + 1];
        } else {
            echo json_encode(['success' => false, 'message' => 'Bu yönde hareket edilemez']);
            return;
        }

        // sort_order değerlerini değiştir
        $this->db->trans_start();

        // Önce tüm kardeş kategorilere sort_order değerleri atanmamışsa, atama yap
        $has_null_orders = false;
        foreach ($siblings as $sibling) {
            if ($sibling->sort_order === null || $sibling->sort_order === '' || $sibling->sort_order == 0) {
                $has_null_orders = true;
                break;
            }
        }

        // Eğer sort_order değerleri eksikse, önce hepsine atama yap
        if ($has_null_orders) {
            foreach ($siblings as $idx => $sibling) {
                $this->db->where('id', $sibling->id)
                    ->update('category', ['sort_order' => ($idx + 1) * 10]);
            }
            // Sibling'leri yeniden çek
            $this->db->where('mother_category_id', $current_category->mother_category_id);
            $this->db->where('isActive', 1);
            $this->db->order_by('sort_order', 'ASC');
            $this->db->order_by('id', 'ASC');
            $siblings = $this->db->get('category')->result();
            
            // Index'leri yeniden bul
            $current_index = false;
            foreach ($siblings as $index => $sibling) {
                if ($sibling->id == $category_id) {
                    $current_index = $index;
                    break;
                }
            }
            $current_category = $siblings[$current_index];
            $swap_with = $direction == 'up' ? $siblings[$current_index - 1] : $siblings[$current_index + 1];
        }

        // Swap işlemi - sort_order değerlerini değiştir
        $temp_order = $current_category->sort_order;
        $swap_order = $swap_with->sort_order;

        $this->db->where('id', $current_category->id)
            ->update('category', ['sort_order' => $swap_order]);

        $this->db->where('id', $swap_with->id)
            ->update('category', ['sort_order' => $temp_order]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['success' => false, 'message' => 'Veritabanı işlemi başarısız']);
            return;
        }

        echo json_encode(['success' => true]);
    }

    public function deleteCategory($id)
    {
        (isPermFunction('seeCategory') != true) ? redirect(base_url('admin')) : NULL;
        
        // İlk olarak bu kategoriye bağlı alt kategorileri kontrol et
        $sub_categories = $this->db->where('mother_category_id', $id)->where('isActive', 1)->get('category')->result();
        
        // Alt kategorileri ana kategori olarak güncelle
        if (!empty($sub_categories)) {
            foreach ($sub_categories as $sub) {
                $this->db->where('id', $sub->id)->update('category', ['mother_category_id' => 0]);
            }
        }
        
        $deleteCategory = $this->db->where('id', $id)->update('category', ['isActive'=>0]);
        $product = $this->db->where('category_id', $id)->get('product')->result();

        foreach ($product as $p) {

            $data = [
                'product_id' => $p->id
            ];

            $stocks = $this->db->where($data)->get('stock')->result();
            $this->db->where('id', $p->id)->update('product', ['isActive'=>0]);
            foreach ($stocks as $s) {
                $result = $this->db->where('id', $s->id)->update('stock', ['isActive'=>0]);
            }
        }
        if ($deleteCategory) {
            flash('Başarılı', 'Kategori ve Alt Ürünler Silindi. Alt kategoriler ana kategori olarak güncellendi.');
            redirect(base_url('admin/product/category'));
        }else{
            flash('Başarısız', 'Bir Sorundan Ötürü Kategori Silinemedi.');
            redirect(base_url('admin/product/category'));
        }
    }

    public function comments() {
        (isPermFunction('seeProductComments') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'comments' => $this->db->get('product_comments')->result(),
            'status' => 'dashboard'
        ];

        $this->adminView('comments', $data);
    }

    public function confirmComment($id)
    {
        (isPermFunction('seeProductComments') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'isActive' => 1
        ];

        $comment =  $this->db->where('id', $id)->get('product_comments')->row();
        $result =  $this->db->where('id', $id)->update('product_comments', $data);

        if ($result) {
            $product = $this->db->where('id', $comment->product_id)->get('product')->row();
            sendNotificationSite($comment->user_id, 'Sistem Bildirimi', 'Yorumunuz Onaylandı. Değerlendirmeniz için teşekkür ederiz.', base_url($product->slug));
            flash('Başarılı', 'Yorum Onaylandı.');
            redirect(base_url('admin/product/comments'));
        }else{
            flash('Başarılı', 'Bir Sorundan Ötürü İşlem Tamamlanamadı.');
            redirect(base_url('admin/product/comments'));
        }
    }

    public function deleteComment($id)
    {
        (isPermFunction('seeProductComments') != true) ? redirect(base_url('admin')) : NULL;
        $result = $this->db->where('id', $id)->delete('product_comments');

        if ($result) {
            flash('Başarılı', 'Yorum Silindi.');
            redirect(base_url('admin/product/comments'));
        }else{
            flash('Başarılı', 'Bir Sorundan Ötürü İşlem Tamamlanamadı.');
            redirect(base_url('admin/product/comments'));
        }
    }

    public function pages()
    {
        (isPermFunction('seePages') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'status' => 'pages',
            'pages' => $this->db->get('pages')->result()
        ];

        $this->adminView('pages', $data);
    }

    public function editPage($id)
    {
        (isPermFunction('seePages') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'page' => $this->db->where('id', $id)->get('pages')->row(),
            'status' => 'pages'
        ];

        $this->adminView('page-edit', $data);
    }

    public function listSupports()
    {
        (isPermFunction('seeTickets') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'status' => 'listSupports',
            'tickets' => $this->db->where('ticket.status', 1)->where('ticket.seller_id', 0)->select('ticket.*, user.name, user.surname')->join('user', 'user.id = user_id', 'left')->get('ticket')->result(),
            'readTickets' => $this->db->where('ticket.status', 2)->where('ticket.seller_id', 0)->select('ticket.*, user.name, user.surname')->join('user', 'user.id = user_id', 'left')->get('ticket')->result(),
            'shopTickets' => $this->db->where('ticket.seller_id !=', 0)->select('ticket.*, user.name, user.surname')->join('user', 'user.id = user_id', 'left')->get('ticket')->result(),
            'closedTickets' => $this->db->where('ticket.status', 0)->where('ticket.seller_id', 0)->select('ticket.*, user.name, user.surname')->join('user', 'user.id = user_id', 'left')->get('ticket')->result()
        ];

        $this->adminView('supports', $data);
    }

    public function showSupport($id)
    {
        (isPermFunction('seeTickets') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'status' => 'listSupports',
            'support' => $this->db->where('id', $id)->get('support')->row()
        ];

        $this->adminView('support', $data);
    }

    public function answerTicket($id)
    {
        (isPermFunction('seeTickets') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'answer' => $this->input->post('answer'),
            'date' => date('d.m.Y'),
            'ticket_id' => $id,
            'user_id' => $this->session->userdata('info')['id']
        ];

        $result = $this->db->insert('ticket_answer', $data);
        if ($result) {
            $ticket = $this->db->where('id', $id)->get('ticket')->row();
            $user = $this->db->where('id', $ticket->user_id)->get('user')->row();
            $properties = $this->db->where('id', 1)->get('properties')->row();

            // Send email notification
            $this->load->library('mailer');
            $this->mailer->send(
                $user->email,
                'ticket_reply',
                [
                    'name' => $user->name,
                    'ticket_id' => $ticket->id,
                    'ticket_subject' => $ticket->title,
                    'ticket_status' => 'Cevaplandı',
                    'reply_message' => $this->input->post('answer'),
                    'ticket_message' => $ticket->message,
                    'date' => date('d.m.Y H:i'),
                    'site_name' => $properties->name,
                    'site_url' => base_url()
                ]
            );

            sendNotificationSite($ticket->user_id, 'Sistem Bildirimi', 'Destek talebin cevaplandı. Şimdi Görüntüle.', base_url('client/showTicket/'.$ticket->id));
            $ticket = $this->db->where('id', $id)->get('ticket_answer')->row();
            $this->db->where('id', $id)->update('ticket', ['status'=>2]);
            flash('Başarılı', 'Destek Talebi Cevaplandı');
            redirect(base_url('admin/listSupports'));
        }else{
            flash('Başarılı', 'Bir Sorundan Ötürü Destek Talebi Cevaplanamadı.');
            redirect(base_url('admin/listSupports'));
        }
    }

    public function getTicket()
    {
        $id = $this->input->post('id');
        $ticket = $this->db->where('ticket_id', $id)->get('ticket_answer')->result();
        $data = [
            'id' => $id,
            'ticket' => $ticket
        ];

        return $this->load->view('admin/support', $data);

    }

    public function closeTicket($id)
    {
        (isPermFunction('seeTickets') != true) ? redirect(base_url('admin')) : NULL;
        $result = $this->db->where('id', $id)->update('ticket', ['status' => 0]);

        if ($result) {
            flash('Başarılı', 'Destek Talebi Kapatıldı');
            redirect(base_url('admin/listSupports'), 'refresh');
        }else{
            flash('Başarılı', 'Bir Sorundan Ötürü Talep Kapatılamadı.');
            redirect(base_url('admin/listSupports'), 'refresh');
        }
    }

    public function listLogs()
    {
        (isPermFunction('seeLogs') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'status' => 'listLogs',
        ];

        $this->adminView('list-logs', $data);
    }

    public function blog()
    {
        (isPermFunction('seeBlogs') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'blogs' => $this->db->get('blog')->result(),
            'status' => 'blog'
        ];

        $this->adminView('blog', $data);
    }

    public function addBlog()
    {
        (isPermFunction('seeBlogs') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'status' => 'blog'
        ];

        $this->adminView('add-blog', $data);
    }

	//included download img from api
	public function AutoAddProductFromApi(){

	}
    public function editBlog($id)
    {
        (isPermFunction('seeBlogs') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'blog' => $this->db->where('id', $id)->get('blog')->row(),
            'status' => 'blog'
        ];

        $this->adminView('edit-blog', $data);
    }

    public function users()
    {
        (isPermFunction('seeUsers') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'users' => $this->db->order_by('id', 'DESC')->get('user')->result(),
            'roles' => $this->db->get('roles')->result(),
            'status' => 'users'
        ];

        $this->adminView('users', $data);
    }

    public function editUser($id)
    {
        (isPermFunction('seeUsers') != true) ? redirect(base_url('admin')) : NULL;

        // Kullanıcının mevcut rolünü kontrol et
        $user = $this->db->where('id', $id)->get('user')->row();
        if ($user->role_id == 1 && $this->input->post('role_id') != 1) {
            flash('Hata', 'Admin rolüne sahip kullanıcının yetkisi değiştirilemez.');
            redirect(base_url('admin/product/userShopHistory/'.$id), 'refresh');
            return;
        }
        
        // Bakiye değişikliği kontrolü
        $new_balance = $this->input->post('balance');
        $new_balance2 = $this->input->post('balance2');
        $balance_changed = ($user->balance != $new_balance);
        $balance2_changed = ($user->balance2 != $new_balance2);

        // Kullanıcı bilgilerini güncelle
        $data = [
            'name' => $this->input->post('name'),
            'surname' => $this->input->post('surname'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
            'tc' => $this->input->post('tc'),
            'balance' => $new_balance,
            'balance2' => $new_balance2,
            'discount' => $this->input->post('discount'),
            'role_id' => $this->input->post('role_id'),
            'isAdmin' => ($this->input->post('role_id') == 0) ? 0 : 1,
            'ref_code' => $this->input->post('reference_code'),
            'isActive' => $this->input->post('isActive'),
            'isConfirmMail' => $this->input->post('isConfirmMail'),
            'type' => $this->input->post('type')
        ];

        // Eğer kullanıcı satıcı ise satıcı bilgilerini de güncelle
        if ($this->input->post('type') == 2) {
            $data['shop_name'] = $this->input->post('shop_name');
            $data['shop_com'] = $this->input->post('shop_com');

            // Satıcı resmi yükleme işlemi
            if (!empty($_FILES['shop_image']['name'])) {
                $this->load->helper('helpers');
                $upload = changePhoto('assets/img/shop', $_FILES['shop_image']);
                if ($upload) {
                    // Eski resmi sil
                    if (!empty($user->shop_img) && file_exists('./uploads/shops/' . $user->shop_image)) {
                        unlink('assets/img/shop/' . $user->shop_image);
                    }
                    $data['shop_img'] = $upload;
                } else {
                    flash('Hata', 'Resim yüklenirken bir hata oluştu: ' . $upload);
                    redirect(base_url('admin/product/userShopHistory/'.$id), 'refresh');
                    return;
                }
            }
        }

        $this->db->where('id', $id)->update('user', $data);
        
        // Bakiye değişikliklerini wallet_transactions tablosuna kaydet
        if ($balance_changed) {
            $balance_diff = $new_balance - $user->balance;
            $transaction_type = ($balance_diff > 0) ? 'deposit' : 'withdrawal';
            
            $transaction_data = [
                'user_id' => $id,
                'transaction_type' => 'system_adjustment',
                'amount' => $balance_diff,
                'description' => 'Admin tarafından bakiye ayarlaması yapıldı: ' . number_format($user->balance, 2) . ' TL → ' . number_format($new_balance, 2) . ' TL',
                'status' => 1, // Onaylı
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance, // İşlem öncesi bakiye
                'balance_after_transaction' => $new_balance, // Güncellenmiş bakiye
                'balance_type' => 'spendable' // Kullanılabilir bakiye
            ];
            
            $this->db->insert('wallet_transactions', $transaction_data);
        }
        
        // Çekilebilir bakiye değişikliklerini wallet_transactions tablosuna kaydet
        if ($balance2_changed) {
            $balance2_diff = $new_balance2 - $user->balance2;
            $transaction_type = ($balance2_diff > 0) ? 'deposit' : 'withdrawal';
            
            $transaction_data = [
                'user_id' => $id,
                'transaction_type' => 'system_adjustment',
                'amount' => $balance2_diff,
                'description' => 'Admin tarafından çekilebilir bakiye ayarlaması yapıldı: ' . number_format($user->balance2, 2) . ' TL → ' . number_format($new_balance2, 2) . ' TL',
                'status' => 1, // Onaylı
                'created_at' => date('Y-m-d H:i:s'),
                'balance_before' => $user->balance2, // İşlem öncesi bakiye
                'balance_after_transaction' => $new_balance2, // Güncellenmiş bakiye
                'balance_type' => 'withdrawable' // Çekilebilir bakiye
            ];
            
            $this->db->insert('wallet_transactions', $transaction_data);
        }
        
        flash('Başarılı', 'Düzenleme İşlemi Başarılı');
        redirect(base_url('admin/product/userShopHistory/'.$id), 'refresh');
    }

    public function setActive($status, $id)
    {
        (isPermFunction('seeUsers') != true) ? redirect(base_url('admin')) : NULL;
        $user = $this->db->where('id', $id)->get('user')->row();
        if ($user->type == 2) {
            ($status == 0) ? $this->db->where('seller_id', $user->id)->update('product', ['isActive' => 3]) : $this->db->where('seller_id', $user->id)->where('isActive', 3)->update('product', ['isActive' => 1]);
        }
        $result = $this->db->where('id', $id)->update('user', ['isActive'=>$status]);
        if ($result) {
            flash('Başarılı', 'Düzenleme İşlemi Başarılı');
            redirect(base_url('admin/users'), 'refresh');
        }else{
            flash('Başarılı', 'Bir Sorundan Ötürü Veri Düzenlenemedi.');
            redirect(base_url('admin/users'), 'refresh');
        }
    }

    public function userShopHistory($user_id)
    {
        $data['user'] = $this->db->where('id', $user_id)->get('user')->row();
        $data['status'] = 'users';

        // Kullanıcının rolünü ve tüm rolleri al
        $data['user_role'] = $this->db->where('id', $data['user']->role_id)->get('roles')->row();
        $data['roles'] = $this->db->get('roles')->result();

        // Kullanıcının referans ile gelip gelmediğini kontrol et
        $data['referrer'] = $this->db->select('user_references.*, user.name, user.surname, user.ref_code')
                                   ->from('user_references')
                                   ->join('user', 'user.id = user_references.referrer_id')
                                   ->where('user_references.buyer_id', $user_id)
                                   ->get()
                                   ->row();

        // Bu kullanıcının referans olduğu son 8 kişi
        $data['last_references'] = $this->db->select('user_references.*, user.name, user.surname, user.email')
                                     ->from('user_references')
                                     ->join('user', 'user.id = user_references.buyer_id')
                                     ->where('user_references.referrer_id', $user_id)
                                     ->order_by('user_references.id', 'DESC')
                                     ->limit(8)
                                     ->get()
                                     ->result();

        // Tüm referanslar (Grid için)
        $data['references'] = $this->db->select('user_references.*, user.name, user.surname, user.email')
                                     ->from('user_references')
                                     ->join('user', 'user.id = user_references.buyer_id')
                                     ->where('user_references.referrer_id', $user_id)
                                     ->get()
                                     ->result();

        // Başarılı satışları say (status = 0 ve type != deposit olan shop kayıtları)
        $data['successful_purchases'] = $this->db->where('user_id', $user_id)
            ->where('status', 0)
            ->where('type !=', 'deposit')
            ->from('shop')
            ->count_all_results();

        // Başarısız satışları say (status = 2 veya (status = 1 ve 5 dakikadan eski ve deposit/credit_card) olan shop kayıtları)
        $data['failed_purchases'] = $this->db->where('user_id', $user_id)
            ->group_start()
                ->where('status', 2)
                ->or_group_start()
                    ->where('status', 1)
                    ->where('TIMESTAMPDIFF(MINUTE, date, NOW()) >', 5)
                    ->where_in('type', ['deposit', 'credit_card'])
                ->group_end()
            ->group_end()
            ->where('type !=', 'deposit')
            ->from('shop')
            ->count_all_results();

        // Bakiye yükleme sayısı (başarılı olanlar - status = 0)
        $data['balance_loads'] = $this->db->where('user_id', $user_id)
            ->where('type', 'deposit')
            ->where('status', 0)
            ->from('shop')
            ->count_all_results();

        // Toplam harcanan para (başarılı işlemler)
        $data['total_spent'] = $this->db->select_sum('amount')
            ->where('buyer_id', $user_id)
            ->where('transaction_status', 'successful')
            ->where('payment_method !=', 'deposit')
            ->get('earnings')
            ->row()
            ->amount ?? 0;

        // Bekleyen siparişlerin toplam tutarı (shop tablosundan)
        $pending_shop_total = $this->db->select_sum('price')
            ->where('user_id', $user_id)
            ->where('status', 1)
            ->where('type !=', 'deposit')
            ->where('TIMESTAMPDIFF(MINUTE, date, NOW()) <=', 5)
            ->get('shop')
            ->row()
            ->price ?? 0;

        // Bekleyen siparişlerin toplam tutarı (pending_product tablosundan)
        $pending_product_total = $this->db->select_sum('pending_product.price')
            ->where('shop.user_id', $user_id)
            ->where('pending_product.isActive', 1)
            ->join('shop', 'shop.id = pending_product.shop_id')
            ->get('pending_product')
            ->row()
            ->price ?? 0;

        // Bekleyen siparişlerin toplam tutarı (invoice tablosundan)
        $pending_invoice_total = $this->db->select_sum('invoice.price')
            ->where('shop.user_id', $user_id)
            ->where('invoice.isActive', 1)
            ->join('shop', 'shop.id = invoice.shop_id')
            ->get('invoice')
            ->row()
            ->price ?? 0;

        // Toplam bekleyen sipariş tutarı
        $data['pending_orders_total'] = $pending_shop_total + $pending_product_total + $pending_invoice_total;

        // Bekleyen sipariş sayısı (shop tablosundan)
        $pending_shop_count = $this->db->where('user_id', $user_id)
            ->where('status', 1)
            ->where('type !=', 'deposit')
            ->where('TIMESTAMPDIFF(MINUTE, date, NOW()) <=', 5)
            ->from('shop')
            ->count_all_results();

        // Bekleyen sipariş sayısı (pending_product tablosundan)
        $pending_product_count = $this->db->where('shop.user_id', $user_id)
            ->where('pending_product.isActive', 1)
            ->join('shop', 'shop.id = pending_product.shop_id')
            ->from('pending_product')
            ->count_all_results();

        // Bekleyen sipariş sayısı (invoice tablosundan)
        $pending_invoice_count = $this->db->where('shop.user_id', $user_id)
            ->where('invoice.isActive', 1)
            ->join('shop', 'shop.id = invoice.shop_id')
            ->from('invoice')
            ->count_all_results();

        // Toplam bekleyen sipariş sayısı
        $data['pending_orders_count'] = $pending_shop_count + $pending_product_count + $pending_invoice_count;

        // Toplam yüklenen bakiye (başarılı deposit işlemleri)
        $data['total_deposit'] = $this->db->select_sum('amount')
            ->where('buyer_id', $user_id)
            ->where('transaction_status', 'successful')
            ->where('payment_method', 'deposit')
            ->get('earnings')
            ->row()
            ->amount ?? 0;

        // Toplam işlem sayısı
        $data['total_transactions'] = $this->db->where('user_id', $user_id)
            ->from('shop')
            ->count_all_results();

        // Referans kazancı (reference_bonus_history tablosundan)
        $total_referral_bonus = $this->db->select_sum('bonus_amount')
            ->where('referrer_id', $user_id)
            ->where('status', 'paid')
            ->get('reference_bonus_history')
            ->row();
        $data['total_earnings'] = $total_referral_bonus->bonus_amount ?? 0;

        // Destek talepleri sayısı
        $data['total_tickets'] = $this->db->where('user_id', $user_id)->from('ticket')->count_all_results();

        // Son alışveriş
        $data['last_purchase'] = $this->db->where('user_id', $user_id)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get('shop')
            ->row();

        // İşlem geçmişi
        $data['transactions'] = $this->db->select('shop.*, TIMESTAMPDIFF(MINUTE, shop.date, NOW()) as minutes_passed')
            ->from('shop')
            ->where('shop.user_id', $user_id)
            ->order_by('shop.id', 'DESC')
            ->get()
            ->result();

        // Referanslar
        $data['references'] = $this->db->select('user_references.*, user.name, user.surname, user.email')
            ->from('user_references')
            ->join('user', 'user.id = user_references.buyer_id')
            ->where('user_references.referrer_id', $user_id)
            ->get()
            ->result();

        // Son loglar
        $data['last_logs'] = $this->db->where('user_id', $user_id)
            ->order_by('id', 'DESC')
            ->limit(10)
            ->get('logs')
            ->result();

        // Abonelikler
        $this->load->model('M_Subscription');
        $data['subscriptions'] = $this->db->select('user_subscriptions.*, subscriptions.name as subscription_name')
            ->from('user_subscriptions')
            ->join('subscriptions', 'subscriptions.id = user_subscriptions.subscription_id')
            ->where('user_subscriptions.user_id', $user_id)
            ->where('user_subscriptions.end_date >=', date('Y-m-d H:i:s'))
            ->get()
            ->result();

        // Her abonelik için toplam harcama ve kazanç hesapla
        foreach ($data['subscriptions'] as $subscription) {
            $subscription->total_spent = $this->db->select_sum('price')
                ->where('user_id', $user_id)
                ->where('subscription_id', $subscription->subscription_id)
                ->get('user_subscriptions')
                ->row()
                ->price ?? 0;

            $subscription->total_earned = $this->db->select_sum('amount')
                ->where('user_id', $user_id)
                ->where('subscription_id', $subscription->subscription_id)
                ->get('user_savings')
                ->row()
                ->amount ?? 0;
        }

        // Kullanıcının son IP adresi
        $last_log = $this->db->where('user_id', $user_id)
            ->where('function', 'loginClient')
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get('logs')
            ->row();
        $data['user']->last_ip = $last_log ? $last_log->user_ip : null;

        // Kullanıcının online durumunu kontrol et
        $data['is_online'] = $this->db->where('user_id', $user_id)
            ->where('last_activity >', date('Y-m-d H:i:s', strtotime('-5 minutes')))
            ->count_all_results('ci_sessions') > 0;

        $this->adminView('user_shop_history', $data);
    }

    public function themeSettings()
    {
        (isPermFunction('seeThemeSettings') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'slider' => $this->db->get('slider')->result(),
            'why' => $this->db->get('why')->result(),
            'comments' => $this->db->get('comments')->result(),
            'categories' => $this->db->where('isActive', 1)->get('category')->result(),
            'homeProducts' => $this->db->get('home_products')->result(),
            'homeChoice' => $this->db->get('home_choice')->result(),
            'homeCategory' => $this->db->select('home_category.*, category.name')->join('category', 'category.id = category_id', 'left')->get('home_category')->result(),
            'story' => $this->db->get('story')->result(),
            'status' => 'themeSettings'
        ];

        $this->adminView('theme', $data);
    }

    public function changeTheme($themeName)
    {
        (isPermFunction('seeThemeSettings') != true) ? redirect(base_url('admin')) : NULL;
        $result = $this->db->update('properties', ['theme'=>$themeName]);
        if ($result) {
            flash('Başarılı', 'Tema Değiştirme İşlemi Başarılı');
            redirect(base_url('admin/themeSettings'));
        }else{
            flash('Başarılı', 'Bir Sorundan Ötürü Tema Değiştirilemedi.');
            redirect(base_url('admin/themeSettings'));
        }
    }

    public function addHomeProduct()
    {
        (isPermFunction('seeThemeSettings') != true) ? redirect(base_url('admin')) : NULL;
        $homeProduct = $this->input->post('homeProduct');
        $number = $this->input->post('number');

        $data = [
            'type' => $homeProduct,
            'amount' => $number,
            'category_id' => $homeProduct
        ];

        $result = $this->db->insert('home_products', $data);

        if ($result) {
            flash('Başarılı', 'Ekleme İşlemi Başarılı');
            redirect(base_url('admin/themeSettings'));
        }else{
            flash('Başarısız', 'Bir Sorundan Ötürü Veri Eklenemedi.');
            redirect(base_url('admin/themeSettings'));
        }
    }

    public function publicSettings()
    {
        (isPermFunction('seeSettings') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'smtp' => $this->db->where('id', 1)->get('smtp')->row(),
            'shopier' => $this->db->where('id', 1)->get('payment')->row(),
            'banks' => $this->db->where('isActive', 1)->get('banks')->result(),
            'payments' => $this->db->get('payment')->result(),
            'status' => 'publicSettings'
        ];

        $this->adminView('settings', $data);
    }

    public function deleteBank($id)
    {
        (isPermFunction('seeSettings') != true) ? redirect(base_url('admin')) : NULL;
        $this->db->where('id', $id)->update('banks', ['isActive'=>0]);
        flash('Harika!', 'Banka Bilgisi Silindi');
        redirect(base_url('admin/publicSettings'), 'refresh');
    }

    public function updatePayment()
    {
        (isPermFunction('seeSettings') != true) ? redirect(base_url('admin')) : NULL;
        $payment_id = $this->input->post('payment_id');
        $api_key = $this->input->post('api_key');
        $secret_key = $this->input->post('secret_key');
        $token = $this->input->post('token');

        $lastPayment = $this->db->where('status', 1)->update('payment', ['status'=>0]);
        $nowPayment = $this->db->where('id', $payment_id)->update('payment', ['status'=>1]);
        $data = [
            'api_key' => $api_key,
            'secret_key' => $secret_key,
            'token' => $token
        ];
        $result = $this->db->where('id', $payment_id)->update('payment', $data);
        if ($result) {
            flash('Başarılı', 'Düzenleme İşlemi Başarılı');
            redirect(base_url('admin/publicSettings'), 'refresh');
        }else{
            flash('Başarısız', 'Bir Sorundan Ötürü Veri Düzenlenemedi.');
            redirect(base_url('admin/publicSettings'), 'refresh');
        }
    }

    public function updateApi()
    {
        (isPermFunction('seeSettings') != true) ? redirect(base_url('admin')) : NULL;
        $google_analytics = $this->input->post('google_analytics', FALSE);
        $online_support = $this->input->post('online_support', FALSE);
        $result = $this->db->where('id', 1)->update('properties', ['google_analytics'=>$google_analytics, 'online_support' => $online_support]);
        if ($result) {
            flash('Başarılı', 'Düzenleme İşlemi Başarılı');
            redirect(base_url('admin/publicSettings'), 'refresh');
        }else{
            flash('Başarısız', 'Bir Sorundan Ötürü Veri Düzenlenemedi.');
            redirect(base_url('admin/publicSettings'), 'refresh');
        }
    }

    public function changePassword()
    {
        (isPermFunction('seeSettings') != true) ? redirect(base_url('admin')) : NULL;
        $this->load->helper('helpers');
        $this->load->library('form_validation');

        $this->form_validation->set_rules("password", "Şifre", "required|trim");
        $this->form_validation->set_rules("newPassword", "Yeni Şifre", "required|trim");


        $message = [
            'required' => '<bold>{field}</bold> Alanı boş bırakılamaz.'
        ];

        $this->form_validation->set_message($message);

        if($this->form_validation->run() == FALSE) {
            flash('Ups.', validation_errors());
            redirect(base_url('admin/publicSettings'), 'refresh');
        }else {
            $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
            if ($user->password == paspas($this->input->post('password'))) {
                $this->db->where('id', $this->session->userdata('info')['id'])->update('user', ['password'=>paspas($this->input->post('newPassword'))]);
                flash('Harika', 'Şifre Değiştirme İşlemi Başarılı');
                redirect(base_url('admin/publicSettings'), 'refresh');
            }else{
                flash('Ups.', 'Mevcut Şifren Eşleşmiyor.');
                redirect(base_url('admin/publicSettings'), 'refresh');
            }
        }
    }

    public function changeMail()
    {
        (isPermFunction('seeSettings') != true) ? redirect(base_url('admin')) : NULL;
        $this->load->helper('helpers');
        $this->load->library('form_validation');

        $this->form_validation->set_rules("email", "Mail", "required|trim");
        $this->form_validation->set_rules("newmail", "Yeni Mail", "required|trim|is_unique[user.email]");

        $message = [
            'required' => '<bold>{field}</bold> Alanı boş bırakılamaz.',
            'is_unique' => 'Yeni E-Mail daha önce kullanılmış'
        ];

        $this->form_validation->set_message($message);

        if($this->form_validation->run() == FALSE) {
            flash('Ups.', validation_errors());
            redirect(base_url('admin/publicSettings'), 'refresh');
        }else {
            $data = [
                'email' => $this->input->post('email')
            ];

            $this->db->where('id', $this->session->userdata('info')['id'])->update('user', ['email'=>$this->input->post('newmail')]);
            flash('Harika', 'Mail Değiştirme İşlemi Başarılı');
            redirect(base_url('admin/publicSettings'), 'refresh');
        }
    }

    public function changeFavicon()
    {
        (isPermFunction('seeSettings') != true) ? redirect(base_url('admin')) : NULL;
        if (file_exists('favicon.ico')) {
            unlink('favicon.ico');
        }
        $file = 'favicon.ico';
        $config['upload_path'] = './';
        $config['allowed_types'] = 'jpg|png|jpeg|ico';
        $config['file_name'] = $file;

        $this->load->library('upload', $config);
        $this->upload->do_upload('img');

        $this->load->library('image_lib');
        $configL['image_library'] = 'gd2';
        $configL['source_image'] = './' . $file;
        $configL['create_thumb'] = FALSE;
        $configL['maintain_ratio'] = FALSE;
        $configL['quality'] = '70%';
        $this->image_lib->initialize($configL);
        $result = $this->image_lib->resize();

        if ($result) {
            flash('Harika', 'Favicon Değiştirme İşlemi Başarılı');
            redirect(base_url('admin/publicSettings'), 'refresh');
        }else{
            flash('Ups', 'Bir sorundan ötürü Favicon değiştirilemedi');
            redirect(base_url('admin/publicSettings'), 'refresh');
        }
    }

    public function referenceSettings()
    {
        (isPermFunction('seeReferenceSettings') != true) ? redirect(base_url('admin')) : NULL;
        $ref_sets = $this->db->get('reference_settings')->result();
        $reference_settings= [];
        foreach ($ref_sets as $refs) {
            $reference_settings[$refs->type] = $refs;
        }
        $data = [
            'status' => 'reference',
            'reference_settings' => $reference_settings,
        ];

        $this->adminView('reference-settings', $data);
    }

    public function referenceList()
    {
        (isPermFunction('seeReferences') != true) ? redirect(base_url('admin')) : NULL;
        $users = $this->db->get('user')->result();
        $user_references= [];
        foreach ($users as $key => $user) {
            $user_references[$key] = $user;
            $user_refs = $this->db->where("referrer_id", $user->id)->get("user_references")->result();
            $user_references[$key]->refs = $user_refs ?? [];
            $referrer = $this->db->where("user_references.buyer_id", $user->id)->join('user', 'user.id = user_references.referrer_id', 'left')->get("user_references")->row();
            $user_references[$key]->referrer = $referrer ?? [];

        }
        $data = [
            'status' => 'reference',
            'user_references' => $user_references,
        ];

        $this->adminView('reference-list', $data);
    }

    public function referenceChange($type)
    {
        (isPermFunction('seeReferenceSettings') != true) ? redirect(base_url('admin')) : NULL;
        if ($type == "allsales") $type = "all_sales";
        $percent_referrer = $this->input->post('percent_referrer');
        $percent_user = $this->input->post('percent_user');

        $this->db->where('type', $type)->update('reference_settings', [
            "percent_referrer" => $percent_referrer,
            "percent_user" => $percent_user,
        ]);

        flash('Harika', 'Referans Ayarları Değişikliği Başarılı');
        redirect(base_url('admin/referenceSettings'), 'refresh');
    }

    public function request()
    {
        (isPermFunction('seeRequests') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'status' => 'request',
            'request' => $this->db->where('request.status', 2)->select('request.*, user.name, user.surname, user.email, user.phone, user.bank_name, user.bank_owner, user.bank_iban')->join('user', 'user.id = user_id', 'left')->get('request')->result()
        ];

        $this->adminView('request', $data);
    }

    public function changeRequest($req, $req_id)
    {
        (isPermFunction('seeRequests') != true) ? redirect(base_url('admin')) : NULL;
        $req_sql = $this->db->where('id', $req_id)->get('request')->row();
        $user = $this->db->where('id', $req_sql->user_id)->get('user')->row();
        
        // İlgili wallet_transactions kaydını bul
        $transaction = $this->db->where('related_id', $req_id)
                              ->where('transaction_type', 'withdrawal')
                              ->where('user_id', $req_sql->user_id)
                              ->get('wallet_transactions')->row();
        
        if ($req == 0) { // Çekim talebini reddet
            $this->db->trans_begin();
            
            // Request tablosunu güncelle
            $this->db->where('id', $req_id)->update('request', [
                'status' => $req,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // İlgili işlem kaydını güncelle
            if ($transaction) {
                $this->db->where('id', $transaction->id)->update('wallet_transactions', [
                    'status' => 2, // Reddedildi
                    'description' => $transaction->description . ' (Reddedildi)',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                // İade işlemi için kayıt oluştur
                $refund_data = [
                    'user_id' => $user->id,
                    'transaction_type' => 'refund',
                    'balance_type' => 'withdrawable',
                    'amount' => abs($transaction->amount),
                    'description' => 'Reddedilen çekim talebinin iadesi',
                    'related_id' => $req_id,
                    'status' => 2, // Onaylı
                    'balance_before' => $user->balance2,
                    'balance_after_transaction' => $user->balance2 + abs($transaction->amount),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $this->db->insert('wallet_transactions', $refund_data);
            }
            
            // Kullanıcının bakiyesini güncelle
            $this->db->where('id', $req_sql->user_id)->update("user", [
                "balance2" => $user->balance2 + $req_sql->amount
            ]);
            
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                flash('Hata', 'Çekim talebi reddedilirken bir hata oluştu.');
                return redirect(base_url('admin/request'), 'refresh');
            } else {
                $this->db->trans_commit();
            }
        } else if ($req == 1) { // Çekim talebini onayla
            // Request tablosunu güncelle
            $this->db->where('id', $req_id)->update('request', [
                'status' => $req,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // İlgili işlem kaydını güncelle
            if ($transaction) {
                $this->db->where('id', $transaction->id)->update('wallet_transactions', [
                    'status' => 1, // Onaylandı
                    'description' => $transaction->description . ' (Onaylandı)',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        flash('Harika', 'İşlem Başarılı');
        redirect(base_url("admin/request"), 'refresh');
    }

    public function pendingUserProductList()
    {
        (isPermFunction('seePendingProducts') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'status' => 'pendingUserProductList',
            'pendingProducts' => $this->db->where('isActive', 3)->get('product')->result()
        ];

        $this->adminView('pending-product-list', $data);
    }

    public function changeUserProduct($req, $product_id)
    {
        (isPermFunction('seeObjections') != true) ? redirect(base_url('admin')) : NULL;
        $result = $this->db->where('id', $product_id)->update('product', ['isActive'=>$req]);

        if ($result) {
            flash('Harika', 'İşlem Başarılı');
            redirect(base_url('admin/pendingUserProductList'), 'refresh');
        }
    }

    public function pendingProductObjectionList()
    {
        (isPermFunction('seeObjections') != true) ? redirect(base_url('admin')) : NULL;
        $data = [
            'status' => 'collapseObjection',
            'pendingObjections' => $this->db->where('status', 2)->select("product.img AS product_img, product.name AS product_name, product.isActive AS product_active, invoice.product as stock_value, product.seller_id AS seller_id, product_objections.user_id AS user_id, product_objections.objection AS objection, product_objections.id AS id")->join('invoice', 'invoice.id = product_objections.invoice_id')->join('product', 'product.id = invoice.product_id')->join('user', 'user.id = invoice.seller_id')->get('product_objections')->result()
        ];

        $this->adminView('pending-product-objection-list', $data);
    }

    public function changeProductObjection($req, $objection_id, $type="pending")
    {
        (isPermFunction('seeObjections') != true) ? redirect(base_url('admin')) : NULL;
        $obj_sql = $this->db->where('id', $objection_id)->get('product_objections')->row();
        $invoice = $this->db->where('id', $obj_sql->invoice_id)->get('invoice')->row();
        $user = $this->db->where('id', $obj_sql->user_id)->get('user')->row();
        if ($req==1) {
            $this->db->trans_begin();
        }
        $this->db->where('id', $objection_id)->update('product_objections', ['status'=>$req]);
        if ($req==1) {
            if ($invoice->payed == 1) {
                $seller = $this->db->where('id', $invoice->seller_id)->get('user')->row();
                if ($seller->isAdmin!=1) {
                    $percent = ($invoice->price / 100) * $seller->shop_com;
                    $price = $invoice->price - $percent;
                    $this->db->where('id', $seller->id)->update("user", [
                        "balance2" => $seller->balance2-$price
                    ]);
                }
            }
            $this->db->where('id', $obj_sql->user_id)->update("user", [
                "balance" => $user->balance+$invoice->price
            ]);
            $this->db->where('id', $invoice->id)->update('invoice', [
                "isActive" => 2,
                "price" => 0
            ]);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            flash('Hata', 'Ürün itirazı değiştirilirken bir hata oluştu.');
            return redirect(base_url('admin/pendingProductObjectionList'), 'refresh');
        } else {
            $this->db->trans_commit();
        }
        flash('Harika', 'İşlem Başarılı');
        $page = ($type == "all") ? "admin/productObjectionList" : "admin/pendingProductObjectionList";
        redirect(base_url($page), 'refresh');
    }

    public function productObjectionList()
    {
        (isPermFunction('seeObjections') != true) ? redirect(base_url('admin')) : NULL;
        $this->load->helper("shop");

        $data = [
            'status' => 'collapseObjection',
            'pendingObjections' => $this->db->where('status !=', 2)->select("product.img AS product_img, product.name AS product_name, product.isActive AS product_active, invoice.product as stock_value, product.seller_id AS seller_id, product_objections.user_id AS user_id, product_objections.objection AS objection, product_objections.id AS id, product_objections.status AS status")->join('invoice', 'invoice.id = product_objections.invoice_id')->join('product', 'product.id = invoice.product_id')->join('user', 'user.id = invoice.seller_id')->get('product_objections')->result()
        ];

        $this->adminView('product-objection-list', $data);
    }

    public function userShops()
    {
        (isPermFunction('seeShops') != true) ? redirect(base_url('admin')) : NULL;
        $this->load->helper("shop");

        $data = [
            'status' => 'userShops',
            'users' => $this->db->where("type", 2)->order_by('id', 'DESC')->get('user')->result(),
        ];

        $this->adminView('user-shops', $data);
    }

    public function authList()
    {
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        $role = $this->db->where('id', $user->role_id)->get('roles')->row();
        ($role->role != "Admin") ? redirect(base_url('admin')) : NULL;
        $data = [
            'status' => 'listAuth',
            'roles' => $this->db->where('user.role_id !=', 0)->select('user.id, user.name, user.surname, user.email, roles.role')->join('user', 'user.role_id = roles.id', 'left')->get('roles')->result()
        ];

        $this->adminView('auth-list', $data);
    }

    public function authSettings()
    {
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        $role = $this->db->where('id', $user->role_id)->get('roles')->row();
        ($role->role != "Admin") ? redirect(base_url('admin')) : NULL;
        $data = [
            'status' => 'authSettings',
            'roles' => $this->db->get('roles')->result()
        ];

        $this->adminView('auth-settings', $data);
    }

    public function editPermission()
    {
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        $role = $this->db->where('id', $user->role_id)->get('roles')->row();
        ($role->role != "Admin") ? redirect(base_url('admin')) : NULL;
        if (!empty($this->input->get('auth'))) {

            $data = [
                'status' => 'editPermission',
                'roles' => $this->db->where('id', $this->input->get('auth'))->get('roles')->row()
            ];

        }else{

            $data = [
                'status' => 'editPermission',
            ];

        }

        $this->adminView('edit-perm', $data);
    }

    public function deletePerm($id)
    {
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        $role = $this->db->where('id', $user->role_id)->get('roles')->row();
        ($role->role != "Admin") ? redirect(base_url('admin')) : NULL;
        if ($id != 1) {
            $i = 0;
            $users = $this->db->where('role_id', $id)->get('user')->result();
            foreach ($users as $user) {
                $this->db->where('id', $user->id)->update('user', ['role_id' => 0, 'isAdmin' => 0]);
                $i++;
            }
            $this->db->where('id', $id)->delete('roles');
            flash('Başarılı', 'Yetki silindi ve yetkiye ait ' . $i . ' yetkili üye rolüne aktarıldı.');
            redirect(base_url('admin/authSettings'), 'refresh');
        }else{
            flash('Başarısız', 'Bunun için yetkiniz yok.');
            redirect(base_url('admin/authSettings'), 'refresh');
        }
    }

    public function changePermission($id = 0)
    {
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        $role = $this->db->where('id', $user->role_id)->get('roles')->row();
        ($role->role != "Admin") ? redirect(base_url('admin')) : NULL;
        $auths = [];
        foreach($this->input->post() as $post => $i)
        {
            if ($i == "on") {
                array_push($auths, $post);
            }
        }
        $roles = json_encode($auths);

        if ($id > 0) {
            $this->db->where('id', $id)->update('roles', ['role' => $this->input->post('authName'), 'roles' => $roles]);
        }else{
            $data = [
                'role' => $this->input->post('authName'),
                'roles' => $roles
            ];

            $this->db->insert('roles', $data);
        }

        flash('Başarılı.', 'Yetki ayarları kaydedildi.');
        redirect(base_url("admin/authSettings"), 'refresh');
    }
    public function streamers()
    {
        $data = [
            'status' => 'streamers',
            'users' => $this->db->where("isStreamer !=", 0)->order_by('id', 'DESC')->get('user')->result(),
            'url_without_https' => str_replace('https://', '', str_replace("http://", "", base_url()))
        ];

        $this->adminView('streamer-list', $data);
    }
	public function donations()
	{
		$data = [
			'status' => 'streamerDonations',
			'donates' => $this->db->get('streamer_donations')->result(),
			'url_without_https' => str_replace('https://', '', str_replace("http://", "", base_url()))
		];

		$this->adminView('streamer-donate-list', $data);
	}
    public function pendingStreamerList()
    {
        $data = [
            'status' => 'streamersPending',
            'users' => $this->db->where("isStreamer", 2)->order_by('id', 'DESC')->get('user')->result(),
            'url_without_https' => str_replace('https://', '', str_replace("http://", "", base_url()))
        ];

        $this->adminView('streamer-pending-list', $data);
    }

    public function changeStreamer($req, $streamer_id)
    {
        $result = $this->db->where('id', $streamer_id)->update('user', ['isStreamer'=>$req]);

        if ($result) {
            flash('Harika', 'İşlem Başarılı');
            redirect(base_url('admin/streamers'), 'refresh');
        }
    }


	/* 06.04.2023 Patch */
	public function apiSettings()
	{
		//api_settings (api_name, setting_key, setting_value)
		//get as array key is api_name and values are setting_key and setting_value
		$this->load->helper('api');
		$data = [
			'status' => 'api_settings',
			'properties' => $this->db->where('id', 1)->get('properties')->row(),
			'settings' => getAPIsettings()
		];

		$this->adminView('api-settings', $data);
	}
	public function editAPISettings($api_name) {
		$post = $this->input->post($api_name);
		$this->db->where("api_name", $api_name)->delete("api_settings");
		foreach ($post as $key => $value) {
			if ($value == "on") {
				$value = true;
			} else if ($value == "off") {
				$value = false;
			}
			$this->db->insert("api_settings", [
				"api_name" => $api_name,
				"setting_key" => $key,
				"setting_value" => $value
			]);
		}

		flash('Başarılı.', 'Ayarlar kaydedildi.');
		redirect(base_url("admin/apiSettings"), 'refresh');
	}


	/* Coupon */

	public function coupons() {
		$data = [
			'coupons' => $this->db->order_by('id', 'DESC')->get('coupons')->result(),
			'status' => 'dashboard'
		];

		foreach ($data["coupons"] as $key => $coupon) {
			if ($coupon->status=="active" && strtotime($coupon->end_at)<time()) {
				$coupon->status = "deactive";
				$this->db->where("id", $coupon->id)->update("coupons", [
					"status" => "deactive"
				]);
			}
		}
		$this->adminView('coupons', $data);
	}

	public function createCoupon() {
		$coupon = $this->input->post('coupon');
		$status = $this->input->post('status');
		$categories = $this->input->post('categories') ?? [];
		$products = $this->input->post('products') ?? [];
		$type = $this->input->post('type');
		$amount = $this->input->post('amount');
		$min_amount = $this->input->post('min_amount');
		$start_at = $this->input->post('start_at');
		$end_at = $this->input->post('end_at');
		$users = $this->input->post('users') ?? [];
		$used_by = $this->input->post('used_by') ?? [];
		$only_users = $this->input->post('only_users') ?? 0;

		$data = [
			'coupon' => $coupon,
			'status' => $status,
			'categories' => json_encode($categories),
			'products' => json_encode($products),
			'type' => $type,
			'amount' => $amount,
			'min_amount' => $min_amount,
			'start_at' => $start_at,
			'end_at' => $end_at,
			'users' => ($only_users==1) ? json_encode($users) : "all",
			'used_by' => json_encode($used_by)
		];

		$result = $this->db->insert('coupons', $data);

		if ($result) {
			flash('Başarılı', 'Ekleme İşlemi Başarılı');
			redirect(base_url('admin/coupons'));
		} else {
			flash('Başarısız', 'Bir Sorundan Ötürü Veri Eklenemedi.');
			redirect(base_url('admin/coupons'));
		}
	}

	public function editCoupon($id) {
		$coupon = $this->input->post('coupon');
		$status = $this->input->post('status');
		$categories = $this->input->post('categories') ?? [];
		$products = $this->input->post('products') ?? [];
		$type = $this->input->post('type');
		$amount = $this->input->post('amount');
		$min_amount = $this->input->post('min_amount');
		$start_at = $this->input->post('start_at');
		$end_at = $this->input->post('end_at');
		$users = $this->input->post('users') ?? [];
		$used_by = $this->input->post('used_by') ?? [];

		$data = [
			'coupon' => $coupon,
			'status' => $status,
			'categories' => json_encode($categories),
			'products' => json_encode($products),
			'type' => $type,
			'amount' => $amount,
			'min_amount' => $min_amount,
			'start_at' => $start_at,
			'end_at' => $end_at,
			'users' => json_encode($users),
			'used_by' => json_encode($used_by)
		];

		if ($old = $this->db->where("id", $id)->get("coupons")->row()) {
			$this->db->where('id', $id)->update('coupons', $data);
		} else {
			flash('Başarısız', 'Veri bulunamadığı için düzenlenemedi.');
			return redirect(base_url('admin/coupons'));
		}

		flash('Başarılı.', 'Kupon başarıyla düzenlendi.');
		redirect(base_url("admin/coupons"), 'refresh');
	}

	public function apiProducts() {
		$categories = json_decode(file_get_contents('https://base.advetro.com/api/v1/categories'), false);
		if (empty($categories)) {
			$categories = [];
		} else {
			if ($categories->success) {
				$categories = $categories->categories;
			} else {
				$categories = [];
			}
		}
		$data = [
			'status' => 'dashboard',
			'categories' => $categories
		];

		$this->adminView('api-products', $data);
	}

    /* Providers */
    public function providers()
    {
        $data = [
            'status' => 'providers',
            'providers' => $this->db->get('product_providers')->result()
        ];
        $this->adminView('providers', $data);
    }

    public function addProvider()
    {
        $type = $this->input->post('type');
        $apiDetails = [];
        
        if($type == 'hyper') {
            $apiDetails = [
                'api_key' => $this->input->post('api_key'),
                'api_token' => $this->input->post('api_token')
            ];
        } else if($type == 'orius') {
            $apiDetails = [
                'mail' => $this->input->post('mail'),
                'password' => $this->input->post('password')
            ];
        }

        $data = [
            'name' => $this->input->post('name'),
            'type' => $type,
            'api_details' => json_encode($apiDetails),
            'base_url' => $this->input->post('base_url'),
            'is_active' => 1
        ];

        $this->db->insert('product_providers', $data);
        redirect('admin/providers');
    }

    public function getProvider($id)
    {
        $provider = $this->db->where('id', $id)->get('product_providers')->row();
        echo json_encode($provider);
    }

    public function updateProvider($id)
    {
        $type = $this->input->post('type');
        $apiDetails = [];
        
        if($type == 'hyper') {
            $apiDetails = [
                'api_key' => $this->input->post('api_key'),
                'api_token' => $this->input->post('api_token')
            ];
        } else if($type == 'orius') {
            $apiDetails = [
                'mail' => $this->input->post('mail'),
                'password' => $this->input->post('password')
            ];
        }
    
        $data = [
            'name' => $this->input->post('name'),
            'type' => $type,
            'api_details' => json_encode($apiDetails),
            'base_url' => $this->input->post('base_url'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    
        $this->db->where('id', $id)->update('product_providers', $data);
        redirect('admin/providers');
    }

    public function deleteProvider($id) {
        $this->db->where('id', $id)->delete('product_providers');
        redirect('admin/providers');
    }

    public function getTransactionDetails($id)
    {
        // Ana işlem detaylarını al
        $transaction = $this->db->select('shop.*, invoice.extras, invoice.isActive as invoice_status, shop.payment_commission, user.name as user_name, user.surname as user_surname, TIMESTAMPDIFF(MINUTE, shop.date, NOW()) as minutes_passed')
            ->from('shop')
            ->join('user', 'user.id = shop.user_id', 'left')
            ->join('invoice', 'invoice.shop_id = shop.id', 'left')
            ->where('shop.id', $id)
            ->get()
            ->row();

        if (!$transaction) {
            echo 'İşlem bulunamadı.';
            return;
        }

        // Eğer ürün alımı ise, ürünleri ayrıca al
        if ($transaction->type != 'deposit') {
            $products = $this->db->select('invoice.*, invoice.isActive as invoice_status, product.name as product_name')
                ->from('invoice')
                ->join('product', 'product.id = invoice.product_id', 'left')
                ->where('invoice.shop_id', $id)
                ->get()
                ->result();

            // Bekleyen ürünleri al
            $pending_products = $this->db->select('pending_product.*, product.name as product_name')
                ->from('pending_product')
                ->join('product', 'product.id = pending_product.product_id', 'left')
                ->where('pending_product.shop_id', $id)
                ->where('pending_product.isActive', 1)
                ->get()
                ->result();
        }

        $html = '<div class="transaction-details p-3">';
        
        // Durum Kartı
        $html .= '<div class="status-card mb-4 p-3 rounded">';
        $html .= '<div class="d-flex justify-content-between align-items-center mb-3">';
        $html .= '<h6 class="m-0">İşlem No: ' . $transaction->id . '</h6>';
        
        // İşlem Durumu Badge
        if ($transaction->status == 0) {
            $html .= '<span class="badge badge-soft-success px-3 py-2">Başarılı</span>';
        } elseif ($transaction->status == 2) {
            $html .= '<span class="badge badge-soft-danger px-3 py-2">Başarısız</span>';
        } elseif ($transaction->status == 1) {
            // minutes_passed kontrolü sadece deposit ve credit_card için
            if (in_array($transaction->type, ['deposit', 'credit_card'])) {
                if ($transaction->minutes_passed <= 5) {
                    $html .= '<span class="badge badge-soft-warning px-3 py-2">Beklemede</span>';
                } else {
                    $html .= '<span class="badge badge-soft-danger px-3 py-2">İptal Edildi</span>';
                }
            } else {
                $html .= '<span class="badge badge-soft-warning px-3 py-2">Beklemede</span>';
            }
        }
        $html .= '</div>';

        // İşlem Detayları Grid
        $html .= '<div class="row g-3">';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="detail-item"><i class="far fa-calendar-alt text-muted me-2"></i> <span class="ms-1">' . $transaction->date . '</span></div>';
        $html .= '<div class="detail-item mt-2"><i class="far fa-user text-muted me-2"></i> <span class="ms-1">' . $transaction->user_name . ' ' . $transaction->user_surname . '</span></div>';
        $html .= '<div class="detail-item mt-2"><i class="fas fa-network-wired text-muted me-2"></i> <span class="ms-1">' . $transaction->ip_address . '</span></div>';
        $html .= '</div>';
        
        $html .= '<div class="col-md-6">';
        $html .= '<div class="detail-item"><i class="fas fa-money-bill-wave text-muted me-2"></i> <span class="ms-1"> İşlem Tutarı: ' . number_format($transaction->price, 2) . ' TL</span></div>';
        $html .= '<div class="detail-item mt-2"><i class="fas fa-wallet text-muted me-2"></i> <span class="ms-1">Satın Alım Öncesi Bakiye: ' . number_format($transaction->balance, 2) . ' TL</span></div>';
        $html .= '<div class="detail-item mt-2"><i class="fas fa-wallet text-muted me-2"></i> <span class="ms-1">Satın Alım Sonrası Bakiye: ' . number_format($transaction->new_balance, 2) . ' TL</span></div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Ek Bilgiler ve Ödeme Komisyonu
        $hasExtras = !empty($transaction->extras);
        $hasCommission = isset($transaction->payment_commission) && $transaction->payment_commission > 0;
        $hasCoupon = isset($transaction->coupon) && $transaction->coupon;

        if ($hasExtras || $hasCommission || $hasCoupon) {
            $html .= '<div class="info-card p-3 rounded mb-3">';
            $html .= '<h6 class="border-bottom pb-2 mb-3"><i class="fas fa-info-circle text-primary me-2"></i> Ek Bilgiler</h6>';
            
            if ($hasCoupon) {
                $html .= '<div class="mb-3">';
                $html .= '<small class="text-muted d-block mb-1">Kullanılan Kupon</small>';
                $html .= '<div class="d-flex align-items-center">';
                $html .= '<i class="fas fa-ticket-alt text-success me-2"></i>';
                $html .= '<strong>' . $transaction->coupon . '</strong>';
                $html .= '</div>';
                $html .= '</div>';
            }
            
            if ($hasExtras) {
                $html .= '<div class="mb-3">';
                $html .= '<small class="text-muted d-block mb-1">Ekstra Bilgiler</small>';
                $html .= '<p class="mb-0">' . $transaction->extras . '</p>';
                $html .= '</div>';
            }
            
            if ($hasCommission) {
                $html .= '<div>';
                $html .= '<small class="text-muted d-block mb-1">Ödeme Komisyonu</small>';
                $html .= '<strong>' . number_format($transaction->payment_commission, 2) . ' TL</strong>';
                $html .= '</div>';
            }
            
            $html .= '</div>';
        }

        if ($transaction->type != 'deposit') {
            // Ürün Alım Detayları
            if (!empty($products) || !empty($pending_products)) {
                $html .= '<div class="products-card p-3 rounded">';
                $html .= '<h6 class="border-bottom pb-2 mb-3"><i class="fas fa-box text-primary me-2"></i> Ürün Detayları</h6>';
                $html .= '<div class="table-responsive">';
                $html .= '<table class="table table-sm mb-0">';
                $html .= '<thead class="table-light"><tr>';
                $html .= '<th>Ürün</th>';
                $html .= '<th>Kod</th>';
                $html .= '<th>Tutar</th>';
                $html .= '<th>Durum</th>';
                $html .= '</tr></thead><tbody>';
                
                // Normal ürünleri listele
                foreach ($products as $product) {
                    $html .= '<tr>';
                    $html .= '<td>' . $product->product_name . '</td>';
                    $html .= '<td><code>' . ($product->product ?: '-') . '</code></td>';
                    $html .= '<td>' . number_format($product->price, 2) . ' TL</td>';
                    $html .= '<td>';
                    
                    if ($product->invoice_status == 0) {
                        $html .= '<span class="badge badge-soft-success">Teslim Edildi</span>';
                    } elseif ($product->invoice_status == 1) {
                        $html .= '<span class="badge badge-soft-warning">Beklemede</span>';
                    } elseif ($product->invoice_status == 2) {
                        $html .= '<span class="badge badge-soft-danger">İade Edildi</span>';
                    } else {
                        $html .= '<span class="badge badge-soft-secondary">Belirsiz</span>';
                    }
                    
                    $html .= '</td></tr>';
                }

                // Bekleyen ürünleri listele
                if (!empty($pending_products)) {
                    foreach ($pending_products as $product) {
                        $html .= '<tr>';
                        $html .= '<td>' . $product->product_name . '</td>';
                        $html .= '<td><code>-</code></td>';
                        $html .= '<td>' . number_format($product->price, 2) . ' TL</td>';
                        $html .= '<td><span class="badge badge-soft-info">Teslimat Bekleniyor</span></td>';
                        $html .= '</tr>';
                    }
                }
                
                $html .= '</tbody></table>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        
        $html .= '</div>';

        // CSS Stilleri
        $html .= '<style>
            .transaction-details {
                font-size: 0.9rem;
            }
            .status-card {
                background-color: #fff;
                border: 1px solid rgba(0,0,0,.125);
            }
            .products-card, .info-card {
                background-color: #fff;
                border: 1px solid rgba(0,0,0,.125);
            }
            .detail-item {
                color: #495057;
                display: flex;
                align-items: center;
            }
            .detail-item i {
                min-width: 20px;
            }
            .badge-soft-success {
                color: #0d6832;
                background-color: #ccf3dd;
            }
            .badge-soft-warning {
                color: #997404;
                background-color: #fff3cd;
            }
            .badge-soft-danger {
                color: #842029;
                background-color: #f8d7da;
            }
            .badge-soft-secondary {
                color: #41464b;
                background-color: #e2e3e5;
            }
            .badge-soft-info {
                color: #055160;
                background-color: #cff4fc;
            }
            .table > :not(caption) > * > * {
                padding: 0.75rem;
            }
            .table tbody tr:last-child td {
                border-bottom: none;
            }
            code {
                background: #f8f9fa;
                padding: 0.2rem 0.4rem;
                border-radius: 0.2rem;
                color: #495057;
            }
            .badge {
                padding: 0.5em 0.8em;
            }
        </style>';

        echo $html;
    }

    public function sendVerificationMail($user_id)
    {
        $user = $this->db->where('id', $user_id)->get('user')->row();
        if (!$user) {
            flash('Hata', 'Kullanıcı bulunamadı.');
            redirect(base_url('admin/product/userShopHistory/'.$user_id));
            return;
        }

        // Eğer mail zaten doğrulanmışsa
        if ($user->isConfirmMail == 1) {
            flash('Bilgi', 'Bu kullanıcının e-postası zaten doğrulanmış.');
            redirect(base_url('admin/product/userShopHistory/'.$user_id));
            return;
        }

        // Login'deki gibi doğrulama kodu oluştur
        $randString = randString(25);
        $verify_code = md5($user->name . $randString);
        
        // Doğrulama kodunu güncelle
        $this->db->where('id', $user_id)->update('user', ['mail_code' => $verify_code]);

        // Mail gönderme işlemi
        $this->load->library('mailer');
        $result = $this->mailer->send(
            $user->email,
            'mail_verification',
            [
                'name' => $user->name,
                'verification_link' => base_url('mail-onay/') . $verify_code,
                'date' => date('d.m.Y H:i')
            ]
        );

        if ($result) {
            flash('Başarılı', 'Doğrulama maili gönderildi.');
        } else {
            flash('Hata', 'Doğrulama maili gönderilirken bir hata oluştu.');
        }

        redirect(base_url('admin/product/userShopHistory/'.$user_id));
    }

    // Kullanıcı şifresini sıfırlama
    public function resetUserPassword($user_id) {
        // Form validasyonu
        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_password', 'Yeni Şifre', 'required');
        $this->form_validation->set_rules('confirm_password', 'Şifre Tekrar', 'required|matches[new_password]');

        if ($this->form_validation->run() == FALSE) {
            flash('Hata', validation_errors(), 'error');
            redirect(base_url('admin/product/userShopHistory/'.$user_id));
            return;
        }

        // Kullanıcıyı kontrol et
        $user = $this->db->where('id', $user_id)->get('user')->row();
        if (!$user) {
            flash('Hata', 'Kullanıcı bulunamadı.', 'error');
            redirect(base_url('admin/product/userShopHistory/'.$user_id));
            return;
        }

        // Şifreyi güncelle
        $new_password = $this->input->post('new_password');
        $hashed_password = paspas($new_password);

        $updated = $this->db->where('id', $user_id)
            ->update('user', ['password' => $hashed_password]);

        if ($updated) {
            // Başarılı log kaydı
            $this->db->insert('logs', [
                'user_id' => $user_id,
                'event' => 'Şifre Sıfırlama',
                'function' => 'Admin tarafından şifre sıfırlandı',
                'user_ip' => $_SERVER['REMOTE_ADDR'],
                'date' => date('Y-m-d H:i:s')
            ]);

            flash('Başarılı', 'Kullanıcı şifresi başarıyla sıfırlandı.');
        } else {
            flash('Hata', 'Şifre sıfırlanırken bir hata oluştu.', 'error');
        }

        redirect(base_url('admin/product/userShopHistory/'.$user_id));
    }

    public function addManualStock($pending_id)
    {
        (isPermFunction('seeStocks') != true) ? redirect(base_url('admin')) : NULL;
        
        $stock_code = $this->input->post('stock_code');
        if (empty($stock_code)) {
            flash('Başarısız', 'Stok kodu boş olamaz.');
            redirect(base_url('admin/product/productHistory'));
            return;
        }

        // Bekleyen ürünü al
        $pendingTransfer = $this->db->where('id', $pending_id)->where('isActive', 1)->get('pending_product')->row();
        if (!$pendingTransfer) {
            flash('Başarısız', 'Bekleyen ürün bulunamadı.');
            redirect(base_url('admin/product/productHistory'));
            return;
        }

        // Ürün bilgilerini al
        $product = $this->db->where('id', $pendingTransfer->product_id)->get('product')->row();
        $shop = $this->db->where('id', $pendingTransfer->shop_id)->get('shop')->row();
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();

        // Stok verilerini hazırla
        $data = [
            'product_id' => $pendingTransfer->product_id,
            'product' => $stock_code,
            'isActive' => 0,
            'checked' => 2
        ];

        // Stok ekle
        $this->db->insert('stock', $data);
        $stock_id = $this->db->insert_id();

        if ($stock_id) {
            // Fatura verilerini hazırla
            $invoice_data = [
                'product' => $stock_code,
                'isActive' => 0,
                'isComment' => 1,
                'price' => $pendingTransfer->price,
                'date' => date('d.m.Y H:i:s'),
                'balance' => $pendingTransfer->balance,
                'new_balance' => $pendingTransfer->new_balance,
                'product_id' => $product->id,
                'shop_id' => $pendingTransfer->shop_id,
                'invoice_provider' => $pendingTransfer->invoice_provider,
                'payment_commission' => $pendingTransfer->payment_commission,
            ];

            // Fatura oluştur
            $this->db->insert('invoice', $invoice_data);
            $invoice_id = $this->db->insert_id();
            
            if ($invoice_id) {
                // Fatura API'ye gönder
                $inv = $this->db->where('id', $invoice_id)->get('invoice')->row();
                $this->load->helper('api');
                createInvoiceInAPI($user, $inv);

                // Bekleyen ürünü kapat
                $this->db->where('id', $pending_id)->update('pending_product', ['isActive' => 0]);
                
                // shop tablosunu güncelle
                $this->db->where('id', $pendingTransfer->shop_id)->update('shop', ['status' => 0]);
                
                // earnings tablosunu da güncelle
                $this->db->where('shop_id', $pendingTransfer->shop_id)->update('earnings', [
                    'transaction_status' => 'successful',
                    'payment_date' => date('Y-m-d H:i:s'),
                    'description' => 'Manuel stok gönderimi ile ürün teslim edildi.'
                ]);

                // Ürün onaylandı - referans bonusu ver
                $this->load->model('M_Payment');
                $this->M_Payment->processInvoiceReferralBonus($user, $inv, $shop);

                // Mail için datayı oluştur
                $orderData = [
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'email' => $user->email,
                    'order_id' => $shop->id,
                    'product_name' => $product->name,
                    'product_price' => $inv->price,
                    'product_code' => $inv->product,
                    'date' => date('d.m.Y H:i')
                ];

                // Mail gönder
                $this->load->helper('mail');
                sendDeliveryNotification($user->email, $orderData);

                // Kazanç modelini güncelle
                $this->load->model('M_Earnings');
                $this->M_Earnings->updateEarningPendingTransfer($pending_id, [
                    'transaction_status' => 'successful',
                    'payment_date' => date('Y-m-d H:i:s')
                ]);

                // SMS gönder
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

                // Bildirim gönder
                sendNotificationSite($user->id, 'Sistem Bildirimi', 'Daha önce stok olmadığı için teslim edilemeyen ürünün hesabında. Şimdi Görüntüle.', base_url('client/product'));

                flash('Başarılı', 'Stok başarıyla eklendi ve ürün teslim edildi.');
            } else {
                flash('Başarısız', 'Fatura oluşturulurken bir hata oluştu.');
            }
        } else {
            flash('Başarısız', 'Stok eklenirken bir hata oluştu.');
        }

        redirect(base_url('admin/product/productHistory'));
    }

    /**
     * Kullanıcının tüm bakiye hareketlerini getirir
     *
     * @param int $user_id Kullanıcı ID
     * @return json
     */
    public function userWalletTransactions($user_id)
    {
        // Yetkisiz girişleri engelle
        if (isPermFunction('seeUsers') != true) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['error' => 'Yetkisiz erişim']);
                exit;
            }
            redirect('admin/dashboard');
        }
        
        // Kullanıcı kontrolü
        $user = $this->db->where('id', $user_id)->get('user')->row();
        if (!$user) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['error' => 'Kullanıcı bulunamadı']);
                exit;
            }
            redirect('admin/users');
        }
        
        // Wallet transactions verilerini çek
        $transactions = $this->db->select('wallet_transactions.*')
            ->from('wallet_transactions')
            ->where('wallet_transactions.user_id', $user_id)
            ->where('wallet_transactions.status !=', 3) // Pasif içerikleri filtreliyoruz
            ->order_by('wallet_transactions.id', 'DESC')
            ->get()
            ->result();
            
        // İşlem açıklamalarını zenginleştir
        foreach ($transactions as $key => $transaction) {
            // Tarih formatını düzenle
            $transactions[$key]->created_at = date('d.m.Y H:i:s', strtotime($transaction->created_at));
            
            // Tutar işlemini gerçekleştir (varsa)
            if (isset($transaction->amount)) {
                $transactions[$key]->amount = number_format($transaction->amount, 2, '.', '');
            }
        }
        
        // İstek Ajax ise JSON döndür
        if ($this->input->is_ajax_request()) {
            echo json_encode(['transactions' => $transactions]);
            exit;
        }

        // Normal istek ise kullanıcı detay sayfasına yönlendir
        redirect('admin/product/userShopHistory/' . $user_id);
    }
public function add_discount() {
    // TEST: Eğer ?test=1 gelirse direkt JSON döndür
    if ($this->input->get('test') == '1') {
        while (ob_get_level()) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8', true);
        die(json_encode(['test' => 'ok', 'method' => 'add_discount', 'timestamp' => time()]));
    }
    
    // EN BAŞTA - Tüm output buffer'ları temizle
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // JSON header - ZORUNLU - HTTP status code ile birlikte
    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8', true);
    header('Cache-Control: no-cache, must-revalidate', true);
    header('Expires: Thu, 19 Nov 1981 08:52:00 GMT', true);
    
    $product_id = $this->input->post('product_id');
    $discount = $this->input->post('discount');
    
    // Validasyon
    if (empty($product_id) || !is_numeric($product_id)) {
        die(json_encode([
            'success' => false,
            'message' => 'Geçersiz ürün ID',
            'csrf_hash' => $this->security->get_csrf_hash()
        ]));
    }
    
    // Discount değerini kontrol et (0 veya pozitif sayı olmalı)
    if (!is_numeric($discount) || $discount < 0) {
        die(json_encode([
            'success' => false,
            'message' => 'İndirim değeri geçersiz (0 veya pozitif sayı olmalı)',
            'csrf_hash' => $this->security->get_csrf_hash()
        ]));
    }
    
    // Ürünü kontrol et
    $product = $this->db->where('id', $product_id)->get('product')->row();
    if (!$product) {
        die(json_encode([
            'success' => false,
            'message' => 'Ürün bulunamadı',
            'csrf_hash' => $this->security->get_csrf_hash()
        ]));
    }
    
    // İndirim fiyatı, ana fiyattan yüksek olamaz
    $discount_value = floatval($discount);
    if ($discount_value > 0 && $discount_value >= $product->price) {
        die(json_encode([
            'success' => false,
            'message' => 'İndirimli fiyat, ana fiyattan düşük olmalıdır',
            'csrf_hash' => $this->security->get_csrf_hash()
        ]));
    }
    
    // Veritabanını güncelle - floatval ile güvenli kayıt
    $update_data = ['discount' => floatval($discount_value)];
    $this->db->where('id', $product_id)->update('product', $update_data);
    
    // Güncellenmiş ürünü kontrol et - Yeni sorgu ile cache'i bypass et
    $this->db->reset_query();
    $updated_product = $this->db->where('id', $product_id)->get('product')->row();
    
    // Log ekle (eğer fonksiyon varsa)
    if (function_exists('addlog')) {
        @addlog('add_discount', 'Ürün indirimi güncellendi: Ürün ID: ' . $product_id . ', İndirim: ' . $discount_value);
    }
    
    // update_product_status ile aynı format
    die(json_encode([
        'success' => true,
        'status' => 'success',
        'message' => 'İndirim başarıyla güncellendi',
        'discount' => $updated_product->discount,
        'csrf_hash' => $this->security->get_csrf_hash()
    ]));
}

// YENİ: Sadece indirim için özel endpoint - Hiçbir şey output yapmadan
public function update_discount_only() {
    // EN BAŞTA - Direkt die() ile test - Eğer buraya geliyorsa metod çağrılıyor
    if ($this->input->get('test') == '1') {
        header('Content-Type: application/json', true);
        die(json_encode(['test' => 'ok', 'method' => 'update_discount_only', 'called' => true]));
    }
    
    // EN BAŞTA - Tüm output buffer'ları temizle
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // JSON header - ZORUNLU
    header('Content-Type: application/json; charset=utf-8', true);
    
    $product_id = $this->input->post('product_id');
    $discount = $this->input->post('discount');
    
    if (empty($product_id) || !is_numeric($product_id)) {
        die(json_encode(['success' => false, 'message' => 'Geçersiz ürün ID']));
    }
    
    if (!is_numeric($discount) || $discount < 0) {
        die(json_encode(['success' => false, 'message' => 'Geçersiz indirim değeri']));
    }
    
    $product = $this->db->where('id', $product_id)->get('product')->row();
    if (!$product) {
        die(json_encode(['success' => false, 'message' => 'Ürün bulunamadı']));
    }
    
    $discount_value = floatval($discount);
    if ($discount_value > 0 && $discount_value >= $product->price) {
        die(json_encode(['success' => false, 'message' => 'İndirimli fiyat, ana fiyattan düşük olmalıdır']));
    }
    
    $this->db->where('id', $product_id)->update('product', ['discount' => $discount_value]);
    
    die(json_encode([
        'success' => true,
        'status' => 'success',
        'message' => 'İndirim başarıyla güncellendi',
        'csrf_hash' => $this->security->get_csrf_hash()
    ]));
}

public function update_product_status() {
    // EN BAŞTA - Direkt die() ile test - Eğer buraya geliyorsa metod çağrılıyor demektir
    // Eğer hala HTML dönüyorsa, bu metod hiç çağrılmıyor demektir
    if ($this->input->get('debug') == '1') {
        header('Content-Type: application/json', true);
        die(json_encode(['debug' => 'ok', 'method' => 'update_product_status', 'called' => true]));
    }
    
    // EN BAŞTA - Tüm output buffer'ları temizle
    while (ob_get_level()) {
        ob_end_clean();
    }
    
    // JSON header set et - EN BAŞTA ve ZORUNLU
    header('Content-Type: application/json; charset=utf-8', true);
    header('Cache-Control: no-cache, must-revalidate', true);
    
    // KRİTİK: Eğer buraya geliyorsa ama hala HTML dönüyorsa, başka bir şey output yapıyor demektir
    
    $id = $this->input->post('id');
    $column = $this->input->post('column');
    $value = $this->input->post('value');

    $allowed = ['rank', 'is_bestseller', 'is_deal', 'is_new', 'is_best_seller', 'discount'];

    if (in_array($column, $allowed)) {
        // Discount için özel işlem
        if ($column == 'discount') {
            // Value'yu temizle ve float'a çevir
            $value = trim($value);
            
            // Boş string ise 0 yap
            if ($value === '' || $value === null) {
                $discount_value = 0;
            } else {
                $discount_value = floatval($value);
            }
            
            // Negatif olamaz
            if ($discount_value < 0) {
                die(json_encode([
                    'status' => 'error',
                    'message' => 'İndirim değeri negatif olamaz',
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
            }
            
            // Ürünü kontrol et
            $product = $this->db->where('id', $id)->get('product')->row();
            if (!$product) {
                die(json_encode([
                    'status' => 'error',
                    'message' => 'Ürün bulunamadı',
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
            }
            
            // Discount değeri normal fiyattan küçük olmalı
            if ($discount_value > 0 && $discount_value >= $product->price) {
                die(json_encode([
                    'status' => 'error',
                    'message' => 'İndirimli fiyat, ana fiyattan düşük olmalıdır',
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
            }
            
            // Veritabanını güncelle - DİREKT SQL QUERY (EN GÜVENİLİR YÖNTEM)
            // Prepared statement ile güvenli update
            $sql = "UPDATE `product` SET `discount` = " . floatval($discount_value) . " WHERE `id` = " . intval($id);
            $update_result = $this->db->query($sql);
            
            // Son hatayı kontrol et
            if ($this->db->error()['code'] != 0) {
                die(json_encode([
                    'status' => 'error',
                    'message' => 'Veritabanı hatası: ' . $this->db->error()['message'],
                    'csrf_hash' => $this->security->get_csrf_hash()
                ]));
            }
        } else {
            // Diğer kolonlar için normal update
            $update_data = [$column => $value];
            $update_result = $this->db->where('id', $id)->update('product', $update_data);
        }
        
        // Güncellenmiş değeri kontrol et - Yeni sorgu ile cache'i bypass et
        $this->db->reset_query();
        $updated_product = $this->db->where('id', $id)->get('product')->row();
        
        // Başarılı response - güncellenmiş değeri de gönder
        $new_value = ($column == 'discount') ? (isset($discount_value) ? $discount_value : (isset($updated_product->discount) ? $updated_product->discount : $value)) : $value;
        
        die(json_encode([
            'status' => 'success',
            'column' => $column,
            'new_value' => $new_value,
            'update_result' => $update_result,
            'csrf_hash' => $this->security->get_csrf_hash() 
        ]));
    } else {
        die(json_encode(['status' => 'error']));
    }
}

// Ana ürün güncelleme fonksiyonu - admin/product/edit/products/product/{id}/product
public function edit($table = '', $type = '', $id = '', $type2 = '')
{
    // KRİTİK: Fonksiyonun çağrıldığını kesin olarak test et
    // Eğer bu die() çalışmıyorsa, edit fonksiyonu hiç çağrılmıyor demektir
    if ($this->input->get('test_edit') == '1') {
        die('PRODUCT EDIT FONKSİYONU ÇAĞRILDI - Table: ' . $table . ', Type: ' . $type . ', ID: ' . $id . ', Type2: ' . $type2);
    }
    
    // Log ekle - fonksiyonun çağrıldığını görmek için
    if (function_exists('addlog')) {
        @addlog('product_edit_called', 'Edit fonksiyonu çağrıldı - Table: ' . $table . ', Type: ' . $type . ', ID: ' . $id);
    }
    
    // Kategori düzenleme kontrolü
    if ($table == 'category' && $type == 'category' && !empty($id) && $type2 == 'category') {
        (isPermFunction('seeCategory') != true) ? redirect(base_url('admin')) : NULL;
        
        // Kategoriyi kontrol et
        $category = $this->db->where('id', $id)->get('category')->row();
        if (!$category) {
            flash('Hata', 'Kategori bulunamadı.');
            redirect(base_url('admin/product/category'), 'refresh');
            return;
        }
        
        // POST verilerini al
        $name = $this->input->post('name');
        $slug = $this->input->post('slug');
        $description = $this->input->post('description');
        $isMenu = $this->input->post('isMenu');
        $isMarketPlace = $this->input->post('isMarketPlace');
        $mother_category_id = $this->input->post('mother_category_id');
        
        // Güncelleme verilerini hazırla
        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'isMenu' => intval($isMenu),
            'isMarketPlace' => intval($isMarketPlace),
            'mother_category_id' => intval($mother_category_id)
        ];
        
        // Resim yükleme kontrolü
        if (!empty($_FILES['img']['name'])) {
            $this->load->helper('helpers');
            $upload = changePhoto('assets/img/category', $_FILES['img']);
            if ($upload) {
                // Eski resmi sil
                if (!empty($category->img) && file_exists('./assets/img/category/' . $category->img)) {
                    @unlink('./assets/img/category/' . $category->img);
                }
                $data['img'] = $upload;
            } else {
                flash('Hata', 'Resim yüklenirken bir hata oluştu.');
                redirect(base_url('admin/product/category'), 'refresh');
                return;
            }
        }
        
        // Veritabanını güncelle
        $this->db->where('id', $id);
        $result = $this->db->update('category', $data);
        
        if ($result) {
            flash('Başarılı', 'Kategori başarıyla güncellendi.');
        } else {
            $error = $this->db->error();
            flash('Hata', 'Kategori güncellenirken bir hata oluştu. ' . (isset($error['message']) ? $error['message'] : ''));
        }
        
        redirect(base_url('admin/product/category'), 'refresh');
        return;
    }
    
    // Blog düzenleme kontrolü
    if ($table == 'blog' && $type == 'blog' && !empty($id) && $type2 == 'blog') {
        (isPermFunction('seeBlogs') != true) ? redirect(base_url('admin')) : NULL;
        
        // Blog'u kontrol et
        $blog = $this->db->where('id', $id)->get('blog')->row();
        if (!$blog) {
            flash('Hata', 'Blog bulunamadı.');
            redirect(base_url('admin/product/blog'), 'refresh');
            return;
        }
        
        // POST verilerini al
        $title = $this->input->post('title');
        $slug = $this->input->post('slug');
        $content = $this->input->post('content');
        
        // Güncelleme verilerini hazırla
        $data = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content
        ];
        
        // Resim yükleme kontrolü
        if (!empty($_FILES['img']['name'])) {
            $this->load->helper('helpers');
            $upload = changePhoto('assets/img/blog', $_FILES['img']);
            if ($upload) {
                // Eski resmi sil
                if (!empty($blog->img) && file_exists('./assets/img/blog/' . $blog->img)) {
                    @unlink('./assets/img/blog/' . $blog->img);
                }
                $data['img'] = $upload;
            } else {
                flash('Hata', 'Resim yüklenirken bir hata oluştu.');
                redirect(base_url('admin/product/editBlog/' . $id), 'refresh');
                return;
            }
        }
        
        // Veritabanını güncelle
        $this->db->where('id', $id);
        $result = $this->db->update('blog', $data);
        
        if ($result) {
            flash('Başarılı', 'Blog başarıyla güncellendi.');
        } else {
            $error = $this->db->error();
            flash('Hata', 'Blog güncellenirken bir hata oluştu. ' . (isset($error['message']) ? $error['message'] : ''));
        }
        
        redirect(base_url('admin/product/blog'), 'refresh');
        return;
    }
    
    (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
    
    // Sadece products/product/{id}/product URL yapısını handle et
    if ($table == 'products' && $type == 'product' && !empty($id) && $type2 == 'product') {
        
        // Ürünü kontrol et
        $product = $this->db->where('id', $id)->get('product')->row();
        if (!$product) {
            flash('Hata', 'Ürün bulunamadı.');
            redirect(base_url('admin/products'), 'refresh');
            return;
        }
        
        // POST verilerini al
        $name = $this->input->post('name');
        $slug = $this->input->post('slug');
        $price = $this->input->post('price');
        $discount = $this->input->post('discount');
        $category_id = $this->input->post('category_id');
        $desc = $this->input->post('desc');
        $isActive = $this->input->post('isActive');
        $product_provider = $this->input->post('product_provider');
        $game_code = $this->input->post('game_code');
        $product_code = $this->input->post('product_code');
        
        // Debug: POST verilerini logla (geliştirme aşamasında)
        if (function_exists('addlog')) {
            @addlog('product_edit', 'POST verileri alındı - ID: ' . $id . ', Discount: ' . var_export($discount, true));
        }
        
        // Güncelleme verilerini hazırla
        $data = [
            'name' => $name,
            'slug' => $slug,
            'price' => floatval($price),
            'category_id' => intval($category_id),
            'desc' => $desc,
            'isActive' => intval($isActive)
        ];
        
        // Discount işlemi - HER ZAMAN işle (boş gönderilse bile 0 yap)
        $discount = $this->input->post('discount');
        $discount = trim($discount);
        
        // Boş string, null veya false ise 0 yap
        if ($discount === '' || $discount === null || $discount === false) {
            $discount_value = 0;
        } else {
            $discount_value = floatval($discount);
        }
        
        // Negatif olamaz
        if ($discount_value < 0) {
            flash('Hata', 'İndirim değeri negatif olamaz.');
            redirect(base_url('admin/product/detail/' . $id), 'refresh');
            return;
        }
        
        // Discount değeri normal fiyattan küçük olmalı (0 hariç)
        if ($discount_value > 0 && $discount_value >= floatval($price)) {
            flash('Hata', 'İndirimli fiyat, ana fiyattan düşük olmalıdır.');
            redirect(base_url('admin/product/detail/' . $id), 'refresh');
            return;
        }
        
        // Discount'ı her zaman set et (0 olsa bile)
        $data['discount'] = $discount_value;
        
        // Ürün tedarikçisi
        if (isset($product_provider) && $product_provider != 'null') {
            $data['product_provider'] = $product_provider;
        } else {
            $data['product_provider'] = null;
        }
        
        // Game code ve product code
        if (isset($game_code) && !empty($game_code)) {
            $data['game_code'] = $game_code;
        }
        if (isset($product_code) && !empty($product_code)) {
            $data['product_code'] = $product_code;
        }
        
        // Resim yükleme kontrolü
        if (!empty($_FILES['img']['name'])) {
            $this->load->helper('helpers');
            $upload = changePhoto('assets/img/product', $_FILES['img']);
            if ($upload) {
                // Eski resmi sil
                if (!empty($product->img) && file_exists('./assets/img/product/' . $product->img)) {
                    @unlink('./assets/img/product/' . $product->img);
                }
                $data['img'] = $upload;
            } else {
                flash('Hata', 'Resim yüklenirken bir hata oluştu.');
                redirect(base_url('admin/product/detail/' . $id), 'refresh');
                return;
            }
        }
        
        // Debug: Güncelleme öncesi veriyi logla
        if (function_exists('addlog')) {
            @addlog('product_edit', 'Güncelleme verisi hazırlandı - ID: ' . $id . ', Discount: ' . (isset($data['discount']) ? $data['discount'] : 'YOK') . ', Data: ' . json_encode($data));
        }
        
        // Veritabanını güncelle - discount'ı her zaman dahil et
        $this->db->where('id', $id);
        $result = $this->db->update('product', $data);
        
        // Güncelleme sonrası kontrol et
        $this->db->reset_query();
        $updated_product = $this->db->where('id', $id)->get('product')->row();
        
        // Debug: Güncelleme sonucunu logla
        if (function_exists('addlog')) {
            $db_error = $this->db->error();
            @addlog('product_edit', 'Güncelleme sonucu - ID: ' . $id . ', Result: ' . ($result ? 'true' : 'false') . ', Updated Discount: ' . (isset($updated_product->discount) ? $updated_product->discount : 'YOK') . ', DB Error: ' . json_encode($db_error));
        }
        
        if ($result) {
            flash('Başarılı', 'Ürün başarıyla güncellendi. İndirim: ' . (isset($data['discount']) ? $data['discount'] : '0') . ' TL');
        } else {
            $error = $this->db->error();
            flash('Hata', 'Ürün güncellenirken bir hata oluştu. ' . (isset($error['message']) ? $error['message'] : ''));
        }
        
        redirect(base_url('admin/product/detail/' . $id), 'refresh');
        return;
    }
    
    // Diğer edit işlemleri için parent'a yönlendir (eğer G_Controller'da genel bir edit varsa)
    // Eğer yoksa 404 döndür
    show_404();
}

    /**
     * Bir kategorideki tüm ürünleri başka bir kategoriye kopyalar
     * Kullanım: admin/product/copyProductsToCategory/{kaynak_kategori_id_veya_slug}/{hedef_kategori_id_veya_slug}
     * Örnek: admin/product/copyProductsToCategory/5/10
     * Örnek: admin/product/copyProductsToCategory/valorant-turkiye-sunucusu-istek-skin/yeni-kategori-slug
     * 
     * NOT: Yeni ürünler otomatik olarak yeni ID'ler alır (51, 52, 53... şeklinde)
     */
    public function copyProductsToCategory($source_category = null, $target_category = null)
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        
        // Parametre kontrolü
        if (empty($source_category) || empty($target_category)) {
            flash('Hata', 'Kaynak ve hedef kategori ID\'leri veya slug\'ları belirtilmelidir.');
            redirect(base_url('admin/product'), 'refresh');
            return;
        }
        
        // Kategoriyi bul - ID mi slug mı kontrol et
        if (is_numeric($source_category)) {
            $source_category_obj = $this->db->where('id', $source_category)->get('category')->row();
        } else {
            $source_category_obj = $this->db->where('slug', $source_category)->get('category')->row();
        }
        
        if (is_numeric($target_category)) {
            $target_category_obj = $this->db->where('id', $target_category)->get('category')->row();
        } else {
            $target_category_obj = $this->db->where('slug', $target_category)->get('category')->row();
        }
        
        $source_category_id = $source_category_obj ? $source_category_obj->id : null;
        $target_category_id = $target_category_obj ? $target_category_obj->id : null;
        
        if (!$source_category_obj) {
            flash('Hata', 'Kaynak kategori bulunamadı. (ID veya slug: ' . $source_category . ')');
            redirect(base_url('admin/product'), 'refresh');
            return;
        }
        
        if (!$target_category_obj) {
            flash('Hata', 'Hedef kategori bulunamadı. (ID veya slug: ' . $target_category . ')');
            redirect(base_url('admin/product'), 'refresh');
            return;
        }
        
        if ($source_category_id == $target_category_id) {
            flash('Hata', 'Kaynak ve hedef kategori aynı olamaz.');
            redirect(base_url('admin/product'), 'refresh');
            return;
        }
        
        // Kaynak kategorideki tüm ürünleri al
        $products = $this->db->where('category_id', $source_category_id)->get('product')->result();
        
        if (empty($products)) {
            flash('Bilgi', 'Kaynak kategoride kopyalanacak ürün bulunamadı.');
            redirect(base_url('admin/product'), 'refresh');
            return;
        }
        
        $copied_count = 0;
        $error_count = 0;
        
        // Her ürünü kopyala
        foreach ($products as $product) {
            // Slug'ı benzersiz yapmak için timestamp ekle
            $unique_suffix = '-' . time() . '-' . rand(1000, 9999);
            $new_slug = $product->slug . $unique_suffix;
            
            // Slug'ın çok uzun olmaması için kontrol et (maksimum 255 karakter)
            if (strlen($new_slug) > 250) {
                $new_slug = substr($product->slug, 0, 250 - strlen($unique_suffix)) . $unique_suffix;
            }
            
            // Aynı slug'ın olup olmadığını kontrol et (çok düşük ihtimal ama kontrol edelim)
            $existing = $this->db->where('slug', $new_slug)->get('product')->row();
            if ($existing) {
                $new_slug = $new_slug . '-' . rand(10000, 99999);
            }
            
            // Ürün verilerini hazırla
            $data = [
                'name' => $product->name,
                'slug' => $new_slug,
                'img' => $product->img,
                'background_img' => $product->background_img,
                'desc' => $product->desc,
                'price' => $product->price,
                'isStock' => $product->isStock,
                'text' => $product->text,
                'isActive' => $product->isActive,
                'category_id' => $target_category_id,
                'discount' => $product->discount,
                'game_code' => isset($product->game_code) ? $product->game_code : 0,
                'product_code' => isset($product->product_code) ? $product->product_code : 0,
                'seller_id' => isset($product->seller_id) ? $product->seller_id : 0,
                'difference_percent' => isset($product->difference_percent) ? $product->difference_percent : 0,
                'product_provider' => isset($product->product_provider) ? $product->product_provider : null,
                'rank' => isset($product->rank) ? $product->rank : 0
            ];
            
            // Ürünü ekle
            $result = $this->db->insert('product', $data);
            
            if ($result) {
                $copied_count++;
            } else {
                $error_count++;
            }
        }
        
        // Sonuç mesajı
        if ($error_count == 0) {
            flash('Başarılı', $copied_count . ' ürün başarıyla "' . $target_category_obj->name . '" kategorisine kopyalandı. Yeni ürünler otomatik ID\'ler aldı (51, 52, 53...). Fiyatları manuel olarak güncelleyebilirsiniz.');
        } else {
            flash('Kısmen Başarılı', $copied_count . ' ürün kopyalandı, ' . $error_count . ' ürün kopyalanırken hata oluştu.');
        }
        
        redirect(base_url('admin/product'), 'refresh');
    }

    /**
     * Paketler listesi
     */
    public function packages()
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        
        $data = [
            'packages' => $this->db->order_by('sort_order', 'ASC')->order_by('id', 'DESC')->get('packages')->result(),
            'status' => 'packages'
        ];

        $this->adminView('packages', $data);
    }

    /**
     * Paket ekleme sayfası
     */
    public function addPackage()
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        
        $data = [
            'products' => $this->db->where('isActive', 1)->order_by('name', 'ASC')->get('product')->result(),
            'status' => 'packages'
        ];

        $this->adminView('add-package', $data);
    }

    /**
     * Paket ekleme işlemi
     */
    public function insertPackage()
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        
        $name = $this->input->post('name');
        $slug = $this->input->post('slug');
        $description = $this->input->post('description');
        $price = $this->input->post('price');
        $discount_percent = $this->input->post('discount_percent');
        $isActive = $this->input->post('isActive');
        $products = $this->input->post('products'); // Array of product IDs
        $sort_order = $this->input->post('sort_order') ?: 0;

        // Slug kontrolü ve otomatik üretme
        if (empty($slug)) {
            $this->load->helper('helpers');
            $slug = sefLink($name);
        }

        // Slug'un benzersiz olup olmadığını kontrol et
        $slug_count = $this->db->where('slug', $slug)->count_all_results('packages');
        if ($slug_count > 0) {
            $slug = $slug . '-' . time();
        }

        // Ürün kontrolü
        if (empty($products) || !is_array($products)) {
            flash('Hata', 'En az bir ürün seçmelisiniz.');
            redirect(base_url('admin/product/addPackage'), 'refresh');
            return;
        }

        // Paket verilerini hazırla
        $package_data = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'price' => floatval($price),
            'discount_percent' => floatval($discount_percent),
            'total_products' => count($products),
            'isActive' => intval($isActive),
            'sort_order' => intval($sort_order),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Paketi ekle
        $this->db->insert('packages', $package_data);
        $package_id = $this->db->insert_id();

        if ($package_id) {
            // Paket ürünlerini ekle
            $sort = 0;
            foreach ($products as $product_id) {
                $this->db->insert('package_products', [
                    'package_id' => $package_id,
                    'product_id' => intval($product_id),
                    'quantity' => 1,
                    'sort_order' => $sort++
                ]);
            }

            flash('Başarılı', 'Paket başarıyla eklendi.');
            redirect(base_url('admin/product/packages'), 'refresh');
        } else {
            flash('Hata', 'Paket eklenirken bir hata oluştu.');
            redirect(base_url('admin/product/addPackage'), 'refresh');
        }
    }

    /**
     * Paket düzenleme sayfası
     */
    public function editPackage($id)
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        
        $package = $this->db->where('id', $id)->get('packages')->row();
        
        if (!$package) {
            flash('Hata', 'Paket bulunamadı.');
            redirect(base_url('admin/product/packages'), 'refresh');
            return;
        }

        // Paket içindeki ürünleri getir
        $package_products = $this->db->where('package_id', $id)->order_by('sort_order', 'ASC')->get('package_products')->result();
        $selected_product_ids = [];
        foreach ($package_products as $pp) {
            $selected_product_ids[] = $pp->product_id;
        }

        $data = [
            'package' => $package,
            'package_products' => $package_products,
            'selected_product_ids' => $selected_product_ids,
            'products' => $this->db->where('isActive', 1)->order_by('name', 'ASC')->get('product')->result(),
            'status' => 'packages'
        ];

        $this->adminView('edit-package', $data);
    }

    /**
     * Paket güncelleme işlemi
     */
    public function updatePackage($id)
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        
        $package = $this->db->where('id', $id)->get('packages')->row();
        
        if (!$package) {
            flash('Hata', 'Paket bulunamadı.');
            redirect(base_url('admin/product/packages'), 'refresh');
            return;
        }

        $name = $this->input->post('name');
        $slug = $this->input->post('slug');
        $description = $this->input->post('description');
        $price = $this->input->post('price');
        $discount_percent = $this->input->post('discount_percent');
        $isActive = $this->input->post('isActive');
        $products = $this->input->post('products'); // Array of product IDs
        $sort_order = $this->input->post('sort_order') ?: 0;

        // Slug kontrolü
        if (empty($slug)) {
            $this->load->helper('helpers');
            $slug = sefLink($name);
        }

        // Slug'un benzersiz olup olmadığını kontrol et (kendi slug'ı hariç)
        $slug_check = $this->db->where('slug', $slug)->where('id !=', $id)->count_all_results('packages');
        if ($slug_check > 0) {
            $slug = $slug . '-' . time();
        }

        // Ürün kontrolü
        if (empty($products) || !is_array($products)) {
            flash('Hata', 'En az bir ürün seçmelisiniz.');
            redirect(base_url('admin/product/editPackage/' . $id), 'refresh');
            return;
        }

        // Paket verilerini güncelle
        $package_data = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'price' => floatval($price),
            'discount_percent' => floatval($discount_percent),
            'total_products' => count($products),
            'isActive' => intval($isActive),
            'sort_order' => intval($sort_order),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $id)->update('packages', $package_data);

        // Eski paket ürünlerini sil
        $this->db->where('package_id', $id)->delete('package_products');

        // Yeni paket ürünlerini ekle
        $sort = 0;
        foreach ($products as $product_id) {
            $this->db->insert('package_products', [
                'package_id' => $id,
                'product_id' => intval($product_id),
                'quantity' => 1,
                'sort_order' => $sort++
            ]);
        }

        flash('Başarılı', 'Paket başarıyla güncellendi.');
        redirect(base_url('admin/product/packages'), 'refresh');
    }

    /**
     * Paket silme (soft delete)
     */
    public function deletePackage($id)
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        
        $package = $this->db->where('id', $id)->get('packages')->row();
        
        if (!$package) {
            flash('Hata', 'Paket bulunamadı.');
            redirect(base_url('admin/product/packages'), 'refresh');
            return;
        }

        // Soft delete (isActive = 0)
        $result = $this->db->where('id', $id)->update('packages', ['isActive' => 0]);

        if ($result) {
            flash('Başarılı', 'Paket silindi.');
        } else {
            flash('Hata', 'Paket silinirken bir hata oluştu.');
        }

        redirect(base_url('admin/product/packages'), 'refresh');
    }

    /**
     * Ürün açıklamalarını toplu temizle
     * Açıklamaları al, temizle (Ctrl+A, Ctrl+C, Ctrl+V gibi), tekrar kaydet
     * AÇIKLAMALAR SİLİNMEZ, sadece temizlenip tekrar kaydedilir
     */
    public function cleanProductDescriptions()
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        
        $category_id = $this->input->get('category_id'); // Steam kategorisi için
        $all = $this->input->get('all'); // Tüm ürünler için
        
        // Ürünleri getir
        if (!empty($category_id)) {
            // Belirli bir kategori (örneğin Steam)
            $this->db->where('category_id', $category_id);
        }
        
        $this->db->where('isActive !=', 0);
        $this->db->where('desc IS NOT NULL');
        $this->db->where('desc !=', '');
        $products = $this->db->get('product')->result();
        
        if (empty($products)) {
            flash('Uyarı', 'Temizlenecek ürün bulunamadı.');
            redirect(base_url('admin/product/products'), 'refresh');
            return;
        }
        
        $updated_count = 0;
        $error_count = 0;
        $skipped_count = 0;
        
        foreach ($products as $product) {
            if (empty($product->desc) || trim($product->desc) == '') {
                $skipped_count++;
                continue; // Boş açıklamaları atla
            }
            
            $original_desc = $product->desc;
            
            // Açıklamayı temizle (Ctrl+A, Ctrl+C, Ctrl+V işlemi gibi)
            // Bu işlem açıklamayı alıp, temizleyip, tekrar aynı şekilde kaydeder
            
            // ÖNEMLİ: Sadece görünmez karakterleri temizle, HTML'i dokunma!
            $cleaned_desc = $original_desc;
            
            // 1. Görünmez karakterleri temizle (sadece kontrol karakterleri)
            $cleaned_desc = preg_replace('/[\x00-\x08\x0B-\x0C\x0E-\x1F\x7F-\x9F]/u', '', $cleaned_desc);
            
            // 2. UTF-8 encoding'i kontrol et ve düzelt
            if (!mb_check_encoding($cleaned_desc, 'UTF-8')) {
                $cleaned_desc = mb_convert_encoding($cleaned_desc, 'UTF-8', 'auto');
            }
            
            // 3. Başta ve sonda boşlukları temizle (sadece trim)
            $cleaned_desc = trim($cleaned_desc);
            
            // Boş olduysa atla (güvenlik)
            if (empty($cleaned_desc) || trim($cleaned_desc) == '') {
                $skipped_count++;
                continue;
            }
            
            // Açıklamayı tekrar kaydet (silinmez, sadece temizlenmiş hali kaydedilir)
            $result = $this->db->where('id', $product->id)->update('product', ['desc' => $cleaned_desc]);
            
            if ($result) {
                $updated_count++;
            } else {
                $error_count++;
            }
        }
        
        $message = '';
        if (!empty($category_id)) {
            $category = $this->db->where('id', $category_id)->get('category')->row();
            $message = $category ? $category->name . ' kategorisindeki ' : '';
        } else {
            $message = 'Tüm ';
        }
        
        $message .= $updated_count . ' ürün açıklaması temizlenip tekrar kaydedildi.';
        
        if ($skipped_count > 0) {
            $message .= ' ' . $skipped_count . ' ürün atlandı (boş açıklama).';
        }
        
        if ($error_count > 0) {
            $message .= ' ' . $error_count . ' ürün güncellenirken hata oluştu.';
        }
        
        flash('Başarılı', $message);
        redirect(base_url('admin/product/products'), 'refresh');
    }

    /**
     * Tüm ilanları düzenleyip kaydet (edit sayfasında kaydete basmak gibi)
     * Edit fonksiyonunun yaptığı UPDATE işlemini direkt yapıyor (redirect olmadan)
     */
    public function refreshAllProducts()
    {
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        
        $this->db->where('isActive !=', 0);
        $products = $this->db->get('product')->result();
        
        if (empty($products)) {
            flash('Uyarı', 'Ürün bulunamadı.');
            redirect(base_url('admin/product/products'), 'refresh');
            return;
        }
        
        $updated_count = 0;
        
        foreach ($products as $product) {
            // Edit sayfasında form submit edildiğinde yapılan işlemi simüle et
            // Edit fonksiyonunun yaptığı UPDATE işlemini direkt yap
            
            // Güncelleme verilerini hazırla (edit fonksiyonundaki gibi)
            $data = [
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => floatval($product->price),
                'category_id' => intval($product->category_id),
                'desc' => $product->desc,  // Textarea'dan gelen gibi (POST'tan gelen)
                'isActive' => intval($product->isActive)
            ];
            
            // Discount işlemi (edit fonksiyonundaki gibi)
            $discount = $product->discount;
            $discount = trim($discount);
            
            if ($discount === '' || $discount === null || $discount === false) {
                $discount_value = 0;
            } else {
                $discount_value = floatval($discount);
            }
            
            if ($discount_value < 0) {
                $discount_value = 0;
            }
            
            if ($discount_value > 0 && $discount_value >= floatval($product->price)) {
                $discount_value = 0;
            }
            
            $data['discount'] = $discount_value;
            
            // Ürün tedarikçisi
            if (isset($product->product_provider) && $product->product_provider != 'null') {
                $data['product_provider'] = $product->product_provider;
            } else {
                $data['product_provider'] = null;
            }
            
            // Game code ve product code
            if (isset($product->game_code) && !empty($product->game_code)) {
                $data['game_code'] = $product->game_code;
            }
            if (isset($product->product_code) && !empty($product->product_code)) {
                $data['product_code'] = $product->product_code;
            }
            
            // Veritabanını güncelle (edit fonksiyonundaki gibi)
            $this->db->where('id', $product->id);
            $result = $this->db->update('product', $data);
            
            if ($result !== false) {
                $updated_count++;
            }
        }
        
        flash('Başarılı', $updated_count . ' ürün güncellendi! (Edit sayfasında kaydetme işlemi simüle edildi)');
        redirect(base_url('admin/product/products'), 'refresh');
    }
}
