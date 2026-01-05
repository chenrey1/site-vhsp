<?php defined('BASEPATH') OR exit('No direct script access allowed');

Class M_Payment extends CI_Model
{
   /* public function addShop($user_id, $encode, $price)
    {

       $randString = randString(20);

        $check = $this->db->where('order_id', $randString)->get('shop')->row();

        if ($check) {
            $randString = randString(25);
        }

        $data = [
            'price' => $price,
            'date' => date('d.m.Y'),
            'status' => 1,
            'order_id' => $randString,
            'user_id' => $user_id,
            'product' => $encode
        ];

        $this->db->insert('shop', $data);
        $last_insert_id = $this->db->insert_id();
        return $randString;
    }*/

    public function addShop($user_id, $encode, $price, $type)
    {

       $randString = randString(20);

        $check = $this->db->where('order_id', $randString)->get('shop')->row();

        if ($check) {
            $randString = randString(25);
        }

        $properties = $this->db->where('id', 1)->get('properties')->row();
        $data = [
            'price' => $price,
            'date' => date('d.m.Y'),
            'status' => 1,
            'order_id' => $randString,
            'user_id' => $user_id,
            'product' => $encode,
            'type' => $type
        ];

        $this->db->insert('shop', $data);
        $last_insert_id = $this->db->insert_id();
        return $randString;
    }

    public function calculate($encode)
    {
        $decode = json_decode($encode, true);
        $amount = 0;
        foreach ($decode as $d) {
           $amount = $amount + $d['price'] * $d['qty'];
        }
        return $amount;
    }

    public function confirmShop($cart, $shop_id)
    {
        foreach ($cart as $c) {
            $i = 0;
            while ($i < $c['qty']) {
            $product = $this->db->where('id', $c['product_id'])->get('product')->row();
            if ($product->isStock == 0) {
                    $data = [
                        'product_id' => $c['product_id'],
                        'extras' => $c['extras'],
                        'price' => $c['price'],
                        'isComment' => 1,
                        'isActive' => 1,
                        'date' => date('d.m.Y'),
                        'shop_id' => $shop_id,
                    ];
                    $this->db->insert('invoice', $data);
            }   

                $stock = $this->db->where('product_id', $c['product_id'])->where('isActive', 1)->get('stock')->row();
                if ($stock) {
                    $data = [
                        'product' => $stock->product,
                        'isActive' => 0,
                        'isComment' => 1,
                        'price' => $c['price'],
                        'date' => date('d.m.Y'),
                        'product_id' => $c['product_id'],
                        'shop_id' => $shop_id
                    ];
                    $this->db->insert('invoice', $data);
                    $this->db->where('id', $stock->id)->update('stock', ['isActive' => 0]);
                }else{
                    $data = [
                    'user_id' => $this->session->userdata('info')['id'],
                    'product_id' => $c['product_id'],
                    'date' => date('d.m.Y'),
                    'isActive' => 1,
                    'shop_id' => $shop_id
                    ];

                    $this->db->insert('pending_product', $data);
                }
                $i++;
            }
        }
        $this->db->where('id', $shop_id)->update('shop', ['status' => 0]);
    }

    public function confirmShopForBalance($shop_id)
    {
        $shop = $this->db->where('id', $shop_id)->get('shop')->row();
        $this->db->where('id', $shop_id)->update('shop', ['status' => 0]);
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        $newBalance = $user->balance + $shop->price;

        $reference_settings = $this->db->where('type', "all_sales")->get('reference_settings')->row();
        $user_refs = $this->db->where("buyer_id", $shop->user_id)->get("user_references")->row();
        if ($user_refs) {
            $referer_id = $user_refs->referrer_id;

            $referer = $this->db->where('id', $referer_id)->get('user')->row();
            $this->db->where('id', $referer_id)->update('user', [
                "balance" => $referer->balance+($shop->price/100*$reference_settings->percent_referrer)
            ]);
        }
        

        $newBalance += ($shop->price/100*$reference_settings->percent_user);

        return $this->db->where('id', $user->id)->update('user', ['balance' => $newBalance]);
    }

    public function confirmShopForCart($shop_id)
    {
        $shop = $this->db->where('id', $shop_id)->get('shop')->row();
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        $cart = json_decode($shop->product, true);
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $productDetail = [];

        $reference_settings = $this->db->where('type', "all_sales")->get('reference_settings')->row();
        $user_refs = $this->db->where("buyer_id", $shop->user_id)->get("user_references")->row();
        if ($user_refs) {
            $referer_id = $user_refs->referrer_id;

            $referer = $this->db->where('id', $referer_id)->get('user')->row();
            $this->db->where('id', $referer_id)->update('user', [
                "balance" => $referer->balance+($shop->price/100*$reference_settings->percent_referrer)
            ]);
        }
        
        foreach ($cart as $c) {
            $i = 0;
            while ($i < $c['qty']) {
                $product = $this->db->where('id', $c['product_id'])->get('product')->row();
                if ($product->isStock == 0) {
                    $data = [
                        'product_id' => $c['product_id'],
                        'extras' => $c['extras'],
                        'price' => $c['price'],
                        'isComment' => 1,
                        'isActive' => 1,
                        'date' => date('d.m.Y'),
                        'shop_id' => $shop->id,
                    ];
                    $this->db->insert('invoice', $data);
                    array_push($productDetail, ['status'=> 0, 'product' => $product->name, 'price' => $c['price']]);

                }else{
                    $stock = $this->db->where('product_id', $c['product_id'])->where('isActive', 1)->get('stock')->row();
                    if ($stock) {
                        $data = [
                            'product' => $stock->product,
                            'isActive' => 0,
                            'isComment' => 1,
                            'price' => $c['price'],
                            'date' => date('d.m.Y'),
                            'product_id' => $c['product_id'],
                            'shop_id' => $shop->id
                        ];
                        $this->db->insert('invoice', $data);
                        array_push($productDetail, ['status'=> 1, 'product' => $product->name, 'stock' => $stock->product, 'price' => $c['price']]);
                        $this->db->where('id', $stock->id)->update('stock', ['isActive' => 0]);

                        $stockCount = $this->db->where('product_id', $c['product_id'])->where('isActive', 1)->count_all_results('stock');
                        if ($stockCount < 3 && $properties->stock == 1) {
                            stockAlert('<div class="orius-mail">
                                <div class="box">
                                <h1 class="logo-text">'. $properties->name .'</h1>
                                <h2>Stok Bilgilendirmesi</h2>
                                <p>'. $product->name .' Ürünü için son '. $stockCount .' Stok Kaldı. Lütfen ekleme yapın.</p>
                                </div>
                                </div>');
                        }
                    }else{

                        if ($properties->autoGive == 1 && $product->game_code != 0 && $product->product_code != 0 && !empty($properties->turkpin_username) && !empty($properties->turkpin_password)) {
                            $xml_data ='DATA=<?xml version="1.0" encoding="utf-8"?>
                            <APIRequest>
                            <params>
                            <cmd>epinSiparisYarat</cmd>
                            <username>'.$properties->turkpin_username.'</username>
                            <password>'.$properties->turkpin_password.'</password>
                            <oyunKodu>'.$product->game_code.'</oyunKodu>
                            <urunKodu>'.$product->product_code.'</urunKodu>
                            <adet>1</adet>
                            <character>'.$user->email.'</character>
                            </params>
                            </APIRequest>
                            ';
                            $url = "http://www.turkpin.com/api.php";

                            $options = array(
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_HEADER => false,
                                CURLOPT_CONNECTTIMEOUT => 30,
                                CURLOPT_TIMEOUT => 30,
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_CUSTOMREQUEST => "POST",
                                CURLOPT_POSTFIELDS => $xml_data
                            );
                            $ch = curl_init($url);
                            curl_setopt_array($ch, $options);
                            $content = curl_exec($ch);
                            $err = curl_errno($ch);
                            $errmsg = curl_error($ch);
                            $header = curl_getinfo($ch);
                            $redirectURL = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                            curl_close($ch);
                            $content = json_decode(json_encode((array)simplexml_load_string($content)),true);

                            if ($content['params']['HATA_NO'] > 0) {
                                $data = [
                                'user_id' => $shop->user_id,
                                'product_id' => $c['product_id'],
                                'date' => date('d.m.Y'),
                                'price' => $c['price'],
                                'isActive' => 1,
                                'shop_id' => $shop->id
                            ];

                            $this->db->insert('pending_product', $data);
                            array_push($productDetail, ['status'=> 2, 'product' => $product->name, 'price' => $c['price']]);

                            if ($content['params']['HATA_NO'] == 014) {
                                stockAlert('<div class="orius-mail">
                                <div class="box">
                                <h1 class="logo-text">'. $properties->name .'</h1>
                                <h2>Bakiye Bilgilendirmesi</h2>
                                <p>Az önce Turkpin sisteminden denenen ürün satın alınması bakiye yetersizliğinden dolayı iptal edildi. Lütfen yükleme yapın ve admin panelden teslimat yapın.</p>
                                </div>
                                </div>');
                            }

                            }else{
                                $code = $content['params']['epin_list']['epin']['code'];
                                $insertData = [
                                    'product' => $code,
                                    'checked' => 1,
                                    'isActive' => 0,
                                    'product_id' => $c['product_id']
                                ];

                                $this->db->insert('stock', $insertData);

                                $data = [
                                    'product' => $code,
                                    'isActive' => 0,
                                    'isComment' => 1,
                                    'price' => $c['price'],
                                    'date' => date('d.m.Y'),
                                    'product_id' => $c['product_id'],
                                    'shop_id' => $shop->id
                                ];
                                $this->db->insert('invoice', $data);
                                array_push($productDetail, ['status'=> 1, 'product' => $product->name, 'stock' => $code, 'price' => $c['price']]);
                            }
                        }else{

                            $data = [
                                'user_id' => $shop->user_id,
                                'product_id' => $c['product_id'],
                                'date' => date('d.m.Y'),
                                'isActive' => 1,
                                'shop_id' => $shop->id,
                                'price' => $c['price']
                            ];

                            $this->db->insert('pending_product', $data);
                            array_push($productDetail, ['status'=> 2, 'product' => $product->name, 'price' => $c['price']]);
                        }
                    }
                }   
                $i++;
            }
        }

        $message = "";
        $messageAdmin = "";
        foreach ($productDetail as $pd) {
            if ($pd["status"] == 0) {
                $message = $message .
                '<tr>
                <td>'.$pd["product"].'</td>
                <td>Bu ürün admin tarafından teslim edilecektir.</td>
                <td>'.$pd["price"].'₺</td>
                </tr>';


                $messageAdmin = $messageAdmin .
                '<tr>
                <td>'.$pd["product"].'</td>
                <td>Bu ürün geri bildirimlidir. Lütfen panelinizi kontrol edin.</td>
                <td>'.$pd["price"].'₺</td>
                </tr>';
            }else if($pd["status"] == 1){
                $message = $message .
                '<tr>
                <td>'.$pd["product"].'</td>
                <td>'.$pd["stock"].'</td>
                <td>'.$pd["price"].'₺</td>
                </tr>';
                $messageAdmin = $messageAdmin .
                '<tr>
                <td>'.$pd["product"].'</td>
                <td>'.$pd["stock"].'</td>
                <td>'.$pd["price"].'₺</td>
                </tr>';
            }else{
                $message = $message .
                '<tr>
                <td>'.$pd["product"].'</td>
                <td>Bu ürün şu anda stoğumuzda bulunmamaktadır. En kısa sürede admin tarafından gönderilecektir.</td>
                <td>'.$pd["price"].'₺</td>
                </tr>';
                $messageAdmin = $messageAdmin .
                '<tr>
                <td>'.$pd["product"].'</td>
                <td>Bu ürünün stoğu bitmiş. Lütfen Kontrol edin.</td>
                <td>'.$pd["price"].'₺</td>
                </tr>';
            }
        }
       
        sendMail($user->email, $message, $shop->price);
        sendMailasAdmin($messageAdmin, $shop->price);
    }
}