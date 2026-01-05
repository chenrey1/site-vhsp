<?php
function sefLink($text) 
{ 
 $text = trim($text);
 $search = array('Ç','ç','Ğ','ğ','ı','İ','Ö','ö','Ş','ş','Ü','ü',' ', '!', '(', ')', '+', '&', '₺');
 $replace = array('c','c','g','g','i','i','o','o','s','s','u','u','-', '', '', '', '', '', '');
 $new_text = str_replace($search,$replace,$text);
 $new_text = strtolower($new_text);
 return $new_text; 
}
function paspas($pas) 
{
 return $newPass = substr(sha1($pas) . md5($pas), 3, -8 );
}
function format_date($date_string) {
    // Verilen tarih dizgesini DateTime nesnesine dönüştür
    $date = new DateTime($date_string);

    // Tarih biçimlendirme
    $formatted_date = $date->format('j F Y H:i:s');

    // Eğer gelen veride saat bilgisi yoksa saati ve saniyeyi çıkar
    if ($date->format('H:i:s') == '00:00:00') {
        $formatted_date = $date->format('j F Y');
    }

    // Türkçe ay isimlerini kullanmak için çeviri
    $turkish_months = array(
        'January'   => 'Ocak',
        'February'  => 'Şubat',
        'March'     => 'Mart',
        'April'     => 'Nisan',
        'May'       => 'Mayıs',
        'June'      => 'Haziran',
        'July'      => 'Temmuz',
        'August'    => 'Ağustos',
        'September' => 'Eylül',
        'October'   => 'Ekim',
        'November'  => 'Kasım',
        'December'  => 'Aralık'
    );

    // Türkçe ay isimlerini kullanarak formatlanmış tarih dizgesini oluştur
    foreach ($turkish_months as $english_month => $turkish_month) {
        $formatted_date = str_replace($english_month, $turkish_month, $formatted_date);
    }

    return $formatted_date;
}
function calculate_remaining_time($date1_string, $date2_string) {
    // İlk tarih dizgesini zaman damgasına dönüştür
    $date1_timestamp = strtotime($date1_string);

    // İkinci tarih dizgesini zaman damgasına dönüştür
    $date2_timestamp = strtotime($date2_string);

    // Zaman damgaları arasındaki farkı hesapla
    $difference_in_seconds = $date2_timestamp - $date1_timestamp;

    // Kalan süreyi hesapla
    $remaining_days = floor($difference_in_seconds / (60 * 60 * 24));
    $remaining_hours = floor(($difference_in_seconds % (60 * 60 * 24)) / (60 * 60));

    // Kalan süreyi dön
    if ($remaining_days < 1) {
        return $remaining_hours . " saat";
    } else {
        return $remaining_days . " gün";
    }
}

function flash($status, $firstMessage, $secondMessage = NULL)
{
    $f = get_instance();
    $mes = '<div class="toast fade show" data-delay="100">
                <div class="toast-header">
                    <strong class="me-auto">' . $status . '</strong>
                    <small>1 Saniye Önce</small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body" style="color: #6c757d;">
                    ' . $firstMessage . '
                </div>
                <div style="height: 2px; background-color: blue; width: 100%; transition: all 3s ease 0s;" id="timer"></div>
            </div>
            <script>
                toastTimer("timer", 4);

                function toastTimer(toastId, time = 4) {
                    setTimeout(function () {
                        if (time <= 0) {
                            document.getElementById(toastId).parentElement.parentElement.hidden = true;
                        } else {
                            var width = parseFloat(document.getElementById(toastId).style.width.replace("%", ""));
                            document.getElementById(toastId).style.width = width - (100 / 4) + "%"; 
                            toastTimer(toastId, time - 1);
                        }
                    }, 1000);
                }
            </script>';
    return $f->session->set_flashdata("message", $mes);
}
function calculateRemaining($expDate){
    $baslangicTarihi = strtotime("now"); 

    $bitisTarihi = strtotime($expDate);

    $fark = $bitisTarihi - $baslangicTarihi;

    $dakika = $fark / 60;
    $saniye_farki = floor($fark - (floor($dakika) * 60));

    $saat = $dakika / 60;
    $dakika_farki = floor($dakika - (floor($saat) * 60));
     
    $gun = $saat / 24;
    $saat_farki = floor($saat - (floor($gun) * 24));
     
    $yil = floor($gun/365);
    $gun_farki = floor($gun - (floor($yil) * 365));
     
    $date = "";
    ($yil != 0) ? $date .= $yil . ' Yıl ' : NULL;
    $date .= $gun_farki . ' Gün ';
    $date .= $saat_farki . ' Saat ';
    $date .= $dakika_farki . ' dakika ';
    $date .= $saniye_farki . ' saniye ';

    return $date;
    //Çıktı: 1 yıl 4 gün 1 saat 40 dakika 13 saniye
}
function addLog($func, $event)
{
  $ci = &get_instance();

  if( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
    } elseif( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else {
        $ip = $_SERVER["REMOTE_ADDR"];
    }

  $data = [
    'function' => $func,
    'user_id' => (!empty($ci->session->userdata('info')) ? $ci->session->userdata('info')['id'] : 0),
    'user_ip' => $ip,
    'event' => $event
  ];

  $ci->db->insert('logs', $data);
}
function getNotification($user_id)
{
    //get notifications
    $ci = &get_instance();
    $ci->db->where('user_id', $user_id);
    $ci->db->where('seen_at', 1);
    $ci->db->where('isActive', 'Active');
    $ci->db->order_by('created_at', 'DESC');
    $ci->db->limit(5);
    $notifications = $ci->db->get('notifications')->result();
    return $notifications;
}
function sendNotificationSite($user_id, $title, $contents, $link, $sender = 'system')
{
    $ci = &get_instance();
    $data = [
        'user_id' => $user_id,
        'title' => $title,
        'contents' => $contents,
        'link' => $link,
        'seen_at' => 1,
        'isActive' => 'Active',
        'created_at' => date('Y-m-d H:i:s'),
        'sender' => $sender,

    ];
    $ci->db->insert('notifications', $data);
}

function calculatePrice($product_id, $qty = 1)
{
  $th = get_instance();
  $product = $th->db->where('id', $product_id)->get('product')->row();
  
  // İndirimli fiyat (eğer ürünün discount değeri varsa)
  ($product->discount > 0) ? $discountedPrice = $product->discount : $discountedPrice = $product->price;
  
  // Bayi indirim ve özel fiyat kontrolü
  $dealerDiscount = 0;
  $specialDealerPrice = null;
  if (!empty($th->session->userdata('info')) && $product->seller_id == 0) {
    $user_id = $th->session->userdata('info')['id'];
    $th->load->model('M_Dealer');
    $dealerInfo = $th->M_Dealer->getUserDealerInfo($user_id);
    if ($dealerInfo) {
      $dpp = $th->M_Dealer->getDealerProductPrice($dealerInfo->dealer_type_id, $product_id);
      if ($dpp) {
        if ($dpp->special_price !== null) {
          $specialDealerPrice = $dpp->special_price;
        } elseif ($dpp->discount_percentage !== null) {
          $dealerDiscount = $dpp->discount_percentage;
        }
      }
      if ($dealerDiscount == 0 && $specialDealerPrice === null) {
        $dealerDiscount = $dealerInfo->discount_percentage;
      }
    }
  }
  // Nihai fiyat hesaplama: önce ürün indirimi, sonra bayi indirimi
  if ($specialDealerPrice !== null) {
    $finalPrice = $specialDealerPrice;
  } elseif ($dealerDiscount > 0) {
    $finalPrice = $discountedPrice - ($discountedPrice * $dealerDiscount / 100);
  } else {
    $finalPrice = $discountedPrice;
  }
  
  // Miktar ile çarp
  $result = abs($finalPrice) * abs($qty);
  
  $data = [
    'price' => $result,
    'isDiscount' => ($product->discount > 0 || $dealerDiscount > 0 || $specialDealerPrice !== null) ? 1 : 0,
    'normalPrice' => $product->price,
    'dealerDiscount' => $dealerDiscount
  ];
  
  return json_encode($data);
}

function alert()
{
  $a = get_instance();
  return $a->session->flashdata("message");
}
function changePhoto($upload_path, $img = "img") {
  $p = get_instance();
  $file = sefLink($_FILES['img']['name']);
  $name_of_file = explode(".", $file);
  $name_of_files = $name_of_file[0] . rand(10, 100000) . "." . $name_of_file[1]; 
  $config['upload_path'] = $upload_path;
  $config['allowed_types'] = 'jpg|png|jpeg|webp';
  $config['file_name'] = $name_of_files;

  $p->load->library('upload', $config);
  $p->upload->do_upload('img');

  $p->load->library('image_lib');
  $configL['image_library'] = 'gd2';
  $configL['source_image'] = './' . $upload_path . "/" . $name_of_files;
  $configL['create_thumb'] = FALSE;
  $configL['maintain_ratio'] = FALSE;
  $configL['quality'] = '70%';
  $p->image_lib->initialize($configL);  
  $p->image_lib->resize();

  return $name_of_files;


}
function randString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function getActiveCategories() {
    $CI =& get_instance();

    // Ana kategorileri al
    $CI->db->where('mother_category_id', 0);
    $CI->db->where('isActive', 1);
    $CI->db->where('isMenu', 1);
    $CI->db->order_by('sort_order', 'ASC');
    $categories = $CI->db->get('category')->result();

    // Her bir ana kategori için alt kategorileri properties olarak ekle
    foreach($categories as $category) {
        $category->has_subcategories = $CI->db->where('isActive', 1)
                ->where('isMenu', 1)
                ->where('mother_category_id', $category->id)
                ->count_all_results('category') > 0;

        $category->subcategories = $CI->db->where('isActive', 1)
            ->where('isMenu', 1)
            ->where('mother_category_id', $category->id)
            ->order_by('sort_order', 'ASC')
            ->get('category')
            ->result();
    }

    return $categories;
}

function sendMail($buyerMail, $messages, $price)
  {
      $p = get_instance();
      $p->load->library('email');
      $properties = $p->db->where('id', 1)->get('properties')->row();
      $smtp = $p->db->where('id', 1)->get('smtp')->row();
      $adminMail = $p->db->where('isAdmin', 1)->get('user')->row();
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
      
            $p->email->initialize($config);
            $p->email->from($smtp->mail, $properties->name);
            $p->email->to($buyerMail);
            $p->email->subject('Yeni Sipariş');
            $p->email->message('<html lang="tr">
              <head>
                  <meta charset="UTF-8">
                  <meta http-equiv="X-UA-Compatible" content="IE=edge">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <title>Siparişiniz Teslim Edildi.</title>
              </head>
              <style>
                 html{font-family:"Segoe UI"}.orius-mail{padding:20px}.orius-mail .box{border:3px solid #d1d1d1;padding:20px}.orius-mail .box small{color:#6c757d;margin-top:50px;display:block}.orius-mail .box .logo{max-height:50px;width:auto}.orius-mail .box .logo-text{margin-top:0;text-transform:uppercase;border-bottom:3px solid #007bff;padding-bottom:5px}.orius-mail .box a{color:#007bff;font-weight:500}.table{width:100%;border:1px solid #ccc}.table td{padding:10px;border:1px solid #ccc}.table td:last-child{color:#2ecc71;font-weight:700}.price{text-align:right}.price span{color:#2ecc71}
              </style>
              <body>
                  
                  <div class="orius-mail">
                      <div class="box">
                          <h1 class="logo-text">'. $properties->name .'</h1>
                          <h2>Siparişiniz Teslim Edildi.</h2>
                          <p>Sepet İçeriği</p>
                          <table class="table">
                              <thead>
                                  <th width="50%">Ürün Adı</th>
                                  <th width="40%">Ürün Bilgisi</th>
                                  <th width="10%">Fiyatı</th>
                              </thead>
                              <tbody>
                                  '.$messages.'
                              </tbody>
                          </table>
                          <h2 class="price">Toplam Fiyat: <span>'.$price.'₺</span></h2>
                          <small>Kullanıcı panelinizde ürünlerinizin güncel durumunu daha detaylı görebilirsiniz.</small>
                      </div>
                  </div>

              </body>
              </html>');
            if ($smtp->mail) {
	            $send = $p->email->send();
            }
  }
function sendConfirmMail($userMail, $url)
  {
      $p = get_instance();
      $p->load->library('email');
      $properties = $p->db->where('id', 1)->get('properties')->row();
      $smtp = $p->db->where('id', 1)->get('smtp')->row();
      $adminMail = $p->db->where('isAdmin', 1)->get('user')->row();
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
      
            $p->email->initialize($config);
            $p->email->from($smtp->mail, $properties->name);
            $p->email->to($userMail);
            $p->email->subject('E-Posta Onayı');
            $p->email->message('<html lang="tr">
              <head>
                  <meta charset="UTF-8">
                  <meta http-equiv="X-UA-Compatible" content="IE=edge">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <title>Hesap Onayınız.</title>
              </head>
              <style>
                 html{font-family:"Segoe UI"}.orius-mail{padding:20px}.orius-mail .box{border:3px solid #d1d1d1;padding:20px}.orius-mail .box small{color:#6c757d;margin-top:50px;display:block}.orius-mail .box .logo{max-height:50px;width:auto}.orius-mail .box .logo-text{margin-top:0;text-transform:uppercase;border-bottom:3px solid #007bff;padding-bottom:5px}.orius-mail .box a{color:#007bff;font-weight:500}.table{width:100%;border:1px solid #ccc}.table td{padding:10px;border:1px solid #ccc}.table td:last-child{color:#2ecc71;font-weight:700}.price{text-align:right}.price span{color:#2ecc71}
              </style>
              <body>
                  
                  <div class="orius-mail">
                      <div class="box">
                          <h1 class="logo-text">'. $properties->name .'</h1>
                          <h4>Aramıza katıldığın için çok mutluyuz. Ancak senin gerçek bir kişi olduğunu doğrulamak isteriz. Aşağıda bulunan bağlantıya tıklayarak üyeliğini doğrulayabilirsin.</h4>
                          <p>'.$url.'</p>
                          </table>
                      </div>
                  </div>

              </body>
              </html>');
            if ($smtp->mail) {
              $send = $p->email->send();
            }
  }
function stockAlert($message)
  {
    $p = get_instance();
      $p->load->library('email');
      $smtp = $p->db->where('id', 1)->get('smtp')->row();
      $admin = $p->db->where('isAdmin', 1)->get('user')->row();
      $properties = $p->db->where('id', 1)->get('properties')->row();
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
      
            $p->email->initialize($config);
            $p->email->from($smtp->mail, $properties->name);
            $p->email->to($admin->email);
            $p->email->subject('Otomatik Bildirim');
            $p->email->message('<html lang="tr">
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
              
                '.$message.'

          </body>
          </html>');
            if ($smtp->mail) {
	            $send = $p->email->send();
            }
  }
function sendGuestPassword($mail, $message)
  {
    $p = get_instance();
      $p->load->library('email');
      $smtp = $p->db->where('id', 1)->get('smtp')->row();
      $admin = $p->db->where('isAdmin', 1)->get('user')->row();
      $properties = $p->db->where('id', 1)->get('properties')->row();
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
      
            $p->email->initialize($config);
            $p->email->from($smtp->mail, $properties->name);
            $p->email->to($mail);
            $p->email->subject('Otomatik Bildirim');
            $p->email->message('<html lang="tr">
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
              
                '.$message.'

          </body>
          </html>');
            if ($smtp->mail) {
              $send = $p->email->send();
            }
  }
function sendNotification($buyerMail, $message)
  {
    $p = get_instance();
      $p->load->library('email');
      $smtp = $p->db->where('id', 1)->get('smtp')->row();
      $admin = $p->db->where('isAdmin', 1)->get('user')->row();
      $properties = $p->db->where('id', 1)->get('properties')->row();
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
      
            $p->email->initialize($config);
            $p->email->from($smtp->mail, $properties->name);
            $p->email->to($buyerMail);
            $p->email->subject($properties->name .' Bildirim');
            $p->email->message('<html lang="tr">
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
              
                '.$message.'

          </body>
          </html>');
            if ($smtp->mail) {
	            $send = $p->email->send();
            }
  }
function sendMailasAdmin($message, $price)
  {
      $p = get_instance();
      $p->load->library('email');
      $smtp = $p->db->where('id', 1)->get('smtp')->row();
      $admin = $p->db->where('isAdmin', 1)->get('user')->row();
      $properties = $p->db->where('id', 1)->get('properties')->row();
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
      
            $p->email->initialize($config);
            $p->email->from($smtp->mail, $properties->name . " Yeni Bir Sipariş");
            $p->email->to($admin->email);
            $p->email->subject('Ürün Bildirimi');
            $p->email->message('<html lang="tr">
              <head>
                  <meta charset="UTF-8">
                  <meta http-equiv="X-UA-Compatible" content="IE=edge">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <title>Sipariş Teslim Edildi.</title>
              </head>
              <style>
                 html{font-family:"Segoe UI"}.orius-mail{padding:20px}.orius-mail .box{border:3px solid #d1d1d1;padding:20px}.orius-mail .box small{color:#6c757d;margin-top:50px;display:block}.orius-mail .box .logo{max-height:50px;width:auto}.orius-mail .box .logo-text{margin-top:0;text-transform:uppercase;border-bottom:3px solid #007bff;padding-bottom:5px}.orius-mail .box a{color:#007bff;font-weight:500}.table{width:100%;border:1px solid #ccc}.table td{padding:10px;border:1px solid #ccc}.table td:last-child{color:#2ecc71;font-weight:700}.price{text-align:right}.price span{color:#2ecc71}
              </style>
              <body>
                  
                  <div class="orius-mail">
                      <div class="box">
                          <h1 class="logo-text">'. $properties->name .'</h1>
                          <h2>Yeni Bir Sipariş Aldınız</h2>
                          <p>Sepet İçeriği</p>
                          <table class="table">
                              <thead>
                                  <th width="50%">Ürün Adı</th>
                                  <th width="40%">Ürün Bilgisi</th>
                                  <th width="10%">Fiyatı</th>
                              </thead>
                              <tbody>
                                  '.$message.'
                              </tbody>
                          </table>
                          <h2 class="price">Toplam Fiyat: <span>'.$price.'₺</span></h2>
                          <small>Kullanıcı panelinizde ürünlerinizin güncel durumunu daha detaylı görebilirsiniz.</small>
                      </div>
                  </div>

              </body>
              </html>');
            if ($smtp->mail) {
	            $send = $p->email->send();
            }
  }
function ConfirmTC($tc, $name, $surname, $birthday)
  {

    $name = mb_strtoupper_tr($name);
    $surname = mb_strtoupper_tr($surname);

    $client = new SoapClient("https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?WSDL");
    try {
        $result = $client->TCKimlikNoDogrula([
            'TCKimlikNo' => $tc,
            'Ad' => $name,
            'Soyad' => $surname,
            'DogumYili' => $birthday
        ]);
        if ($result->TCKimlikNoDogrulaResult) {
            return TRUE;
        } else {
            return FALSE;
        }
    } catch (Exception $e) {
        echo $e->faultstring;
    }
  }
function convertToObject($array) {
    $object = new stdClass();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            $value = convertToObject($value);
        }
        $object->$key = $value;
    }
    return $object;
}
function convertToMounth($orgDate)
  {
    $newDate = date("d F Y", strtotime($orgDate)); 
    $ing = array("January","February","March","May","April","June","July","August","September","October","November","December");
    $tr = array("Ocak","Şubat","Mart","Nisan","Mayıs","Haziran","Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık");
    $newDate = str_replace($ing,$tr,$newDate);
    return $newDate;
  }
function mb_strtoupper_tr($text){
 
    $text = str_replace('ğ', 'Ğ', $text);
    $text = str_replace('ş', 'Ş', $text);
    $text = str_replace('ı', 'I', $text);
    $text = str_replace('ö', 'Ö', $text);
    $text = str_replace('ü', 'Ü', $text);
    $text = str_replace('ç', 'Ç', $text);
    $text = str_replace('i', 'İ', $text);
    return mb_strtoupper($text,'UTF8');
}
function isPerm($auth_id, $permission) {
  $ci = get_instance();
  $role = $ci->db->where('id', $auth_id)->get('roles')->row();
  if (in_array($permission, json_decode($role->roles, true))) {
    return true;
  }else{
    return false;
  }
}
function isPermFunction($permission) {
  $ci = get_instance();
  $user = $ci->db->where('id', $ci->session->userdata('info')['id'])->get('user')->row();
  $role = $ci->db->where('id', $user->role_id)->get('roles')->row();
  if (in_array($permission, json_decode($role->roles, true))) {
    return true;
  }else{
    return false;
  }

}
function getUserIp()
{
  if( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
        $ip = $_SERVER["HTTP_CLIENT_IP"];
      } elseif( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
      } else {
        $ip = $_SERVER["REMOTE_ADDR"];
      }

      return $ip;
}
function getFeature($featureID)
{
    $ci = get_instance();
    $feature = $ci->db->where('id', $featureID)->get('subscription_features')->row();
    $default_commission = $ci->db
        ->where('status', 1)
        ->where('is_default', 1)
        ->get('payment')
        ->row();
    
    // Varsayılan ödeme yöntemi komisyon oranı
    $default_commission_rate = ($default_commission && isset($default_commission->commission_rate)) 
        ? $default_commission->commission_rate 
        : 5.0; // Varsayılan değer
    
    switch ($feature->feature_name) {
        case 'commission_value':
            if ($feature->value == 0) {
                return 'Ödemelerinizde <b>hiç komisyon ücreti yok!</b>';
            } else {
                return 'Ödemelerinizde %' . $default_commission_rate . ' yerine yalnızca <b>%' . $feature->value . '</b> komisyon avantajı!';
            }
            break;
        case 'refund_value':
            return 'Her ürün alımında <b>%' . $feature->value . '</b> oranında bakiyenizi geri kazanın!';
            break;
        case 'max_refund_value':
            if ($feature->value == 0) {
                return 'Her ürün alımında bakiyenizi bir sınır olmadan geri kazanın!';
            } else {
                return 'Her ürün alımında bakiyenizi geri kazanın! (Maksimum <b>' . $feature->value . '</b> TL)';
            }
            break;
    }
}
/**
 * Kullanıcı ve ödeme yöntemine göre komisyon oranını hesaplar
 * En düşük komisyon oranını döndürür (kullanıcı avantajına olan)
 * 
 * @param int|NULL $user_id Kullanıcı ID (null ise aktif kullanıcı)
 * @param int|NULL $payment_id Ödeme yöntemi ID (null ise varsayılan ödeme yöntemi)
 * @return float Komisyon oranı
 */
function getCommission($user_id = NULL, $payment_id = NULL)
{
    $ci = get_instance();
    
    // Kullanıcı ID belirleme
    if ($user_id === NULL && !empty($ci->session->userdata('info')['id'])) {
        $user_id = intval($ci->session->userdata('info')['id']);
    }
    
    // Abonelik komisyonunu hesapla
    $subscriptionCommission = null;
    if ($user_id) {
        $ci->load->model('M_Subscription');
        $subscriptionCommission = $ci->M_Subscription->getCommissionValue($user_id);
    }
    
    // Ödeme yöntemi komisyonunu hesapla
    $paymentCommission = null;
    if ($payment_id !== NULL) {
        // Ödeme yöntemini veritabanından al
        $payment = $ci->db
            ->where('id', intval($payment_id))
            ->where('status', 1)
            ->get('payment')
            ->row();
            
        if ($payment && isset($payment->commission_rate)) {
            $paymentCommission = (float)$payment->commission_rate;
        }
    } else {
    // Varsayılan ödeme yöntemini bul
        $defaultPayment = $ci->db
            ->where('status', 1)
            ->where('is_default', 1)
            ->get('payment')
            ->row();
            
        if ($defaultPayment && isset($defaultPayment->commission_rate)) {
            $paymentCommission = (float)$defaultPayment->commission_rate;
        }
    }
    
    // En düşük komisyon oranını seç (null değerleri kontrol ederek)
    if ($subscriptionCommission !== null && $paymentCommission !== null) {
        // Her iki komisyon da tanımlıysa, düşük olanı seç
        return min($subscriptionCommission, $paymentCommission);
    } else if ($subscriptionCommission !== null) {
        // Sadece abonelik komisyonu tanımlıysa
        return (float)$subscriptionCommission;
    } else if ($paymentCommission !== null) {
        // Sadece ödeme yöntemi komisyonu tanımlıysa
        return $paymentCommission;
    }
    
    // Hiçbir komisyon tanımlı değilse, varsayılan değer
    return 0;
}
function insertUserSavings($user_id, $subscription_id, $shop_id, $reason, $amount, $description, $status = 'successfull', $transaction_date)
{
    $ci = get_instance();

    if (empty($transaction_date)) {
        $transaction_date = date('Y-m-d H:i:s');
    }

    $data = [
        'user_id' => $user_id,
        'subscription_id' => $subscription_id,
        'shop_id' => $shop_id,
        'reason' => $reason,
        'amount' => $amount,
        'total_amount' => $amount,
        'description' => $description,
        'status' => $status,
        'transaction_date' => $transaction_date
    ];
    $ci->db->insert('user_savings', $data);
}
function calculateAverageRating($comments)
{
    if (empty($comments)) {
        return 0;
    }
    $totalStars = 0;
    foreach ($comments as $comment) {
        $totalStars += $comment->star;
    }
    $averageStars = $totalStars / count($comments);
    return $averageStars;
}

if (!function_exists('getSetting')) {
    /**
     * Settings tablosundan ayar değerini getirir
     * 
     * @param string $key Ayar anahtarı
     * @return string|null Ayar değeri
     */
    function getSetting($key)
    {
        $CI =& get_instance();
        $setting = $CI->db->where('key', $key)->get('settings')->row();
        return $setting ? $setting->value : null;
    }
}
?>

