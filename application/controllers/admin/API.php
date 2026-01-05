<?php
// controllers/admin/API.php
defined('BASEPATH') OR exit('No direct script access allowed');

class API extends G_Controller {

	/**
	 * Admin menü sayfalarını döndüren yardımcı fonksiyon
	 * 
	 * @return array Admin sayfaları listesi
	 */
	private function getAdminPages() {
		return [
			// Ana Sayfa
			["title" => "Ana Sayfa", "link" => "admin/dashboard", "icon" => "fas fa-tachometer-alt", "detail" => "Yönetim Paneli Ana Sayfası"],
			
			// Ürün ve Mağaza Yönetimi
			["title" => "Ürünler", "link" => "admin/products", "icon" => "fas fa-box", "detail" => "Ürün Yönetimi"],
			["title" => "Ürün Ekle", "link" => "admin/product/addProduct", "icon" => "fas fa-plus", "detail" => "Yeni Ürün Ekleme"],
			["title" => "Stok Ekle", "link" => "admin/stock", "icon" => "fas fa-plus-circle", "detail" => "Yeni Stok Ekle, Stok Oluştur"],
			["title" => "Kategoriler", "link" => "admin/category", "icon" => "fas fa-th-large", "detail" => "Kategori Yönetimi"],
			["title" => "Kuponlar", "link" => "admin/coupons", "icon" => "fas fa-tags", "detail" => "Kupon Yönetimi"],
			["title" => "Tedarikçiler", "link" => "admin/providers", "icon" => "fas fa-plug", "detail" => "Tedarikçi Yönetimi"],
			
			// Kullanıcı Yönetimi
			["title" => "Üyeler", "link" => "admin/users", "icon" => "fas fa-users", "detail" => "Kullanıcı Yönetimi"],
			["title" => "Destek Talepleri", "link" => "admin/listSupports", "icon" => "fas fa-ticket-alt", "detail" => "Destek Talepleri"],
			["title" => "Kayıt Geçmişi", "link" => "admin/listLogs", "icon" => "fas fa-history", "detail" => "Sistem Kayıtları"],
			//Yetki Yönetimi
			['title' => "Yetkili Listesi", "link" => "admin/authList", "icon" => "fas fa-user-tag", "detail" => "Yetkili Listesi"],
			['title' => "Yetki Ayarları", "link" => "admin/permissionSettings", "icon" => "fas fa-user-tag", "detail" => "Yetki Ayarları"],

			// Kredi ve Bayi Yönetimi
			["title" => "Kredi Yönetimi", "link" => "admin/credit_management", "icon" => "fas fa-credit-card", "detail" => "Kullanıcı Kredi İşlemleri"],
			["title" => "Kredi Ödemeleri", "link" => "admin/credit_management/payments", "icon" => "fas fa-money-bill-wave", "detail" => "Kredi Ödeme Geçmişi"],
			['title' => "Bayilik Tipleri", "link" => "admin/dealer", "icon" => "fas fa-user-tag", "detail" => "Bayilik Tipleri"],
			['title' => "Bayilik Kullanıcıları", "link" => "admin/dealer/dealerUsers", "icon" => "fas fa-user-tag", "detail" => "Bayilik Kullanıcıları"],
			['title' => "Bayilik Başvuruları", "link" => "admin/dealer/applications", "icon" => "fas fa-user-tag", "detail" => "Bayilik Başvuruları"],
			['title' => "Ürün Fiyatlandırma", "link" => "admin/dealer/productPricing", "icon" => "fas fa-money-bill-wave", "detail" => "Ürün Fiyatlandırma"],
			['title' => "Bayilik Ayarları", "link" => "admin/dealer/settings", "icon" => "fas fa-user-tag", "detail" => "Bayilik Ayarları"],
			
			// Satış ve Finans
			["title" => "Satış Geçmişi", "link" => "admin/productHistory", "icon" => "fas fa-chart-line", "detail" => "Satış İstatistikleri ve Geçmişi"],
			["title" => "Fatura Listesi", "link" => "admin/finance/invoices", "icon" => "fas fa-file-alt", "detail" => "Fatura Yönetimi"],
			["title" => "Havale Bildirimi", "link" => "admin/bankTransfer", "icon" => "fas fa-money-check", "detail" => "Havale Bildirimleri"],
			
			// Abonelik Yönetimi
			['title' => "Abone Listesi", "link" => "admin/subscription/subList", "icon" => "fas fa-user-tag", "detail" => "Abone Listesi"],
			['title' => "Abone Ayarları", "link" => "admin/subscription/subSettings", "icon" => "fas fa-user-tag", "detail" => "Abone Ayarları"],
			
			// Site Ayarları
			["title" => "Bakiye Ayarları", "link" => "admin/settings/balance", "icon" => "fas fa-money-bill-wave", "detail" => "Bakiye Ayarları"],
			["title" => "Tema Ayarları", "link" => "admin/themeSettings", "icon" => "fas fa-sitemap", "detail" => "Site Tema Ayarları"],
			["title" => "Genel Ayarlar", "link" => "admin/publicSettings", "icon" => "fas fa-cog", "detail" => "Site Genel Ayarları"],
			["title" => "API Ayarları", "link" => "admin/apiSettings", "icon" => "fas fa-code", "detail" => "API Entegrasyon Ayarları"],
			//Mail ayarları (mail/templates - mail/logs)
			['title' => "Mail Şablonları", "link" => "admin/mail/templates", "icon" => "fas fa-envelope", "detail" => "Mail Şablonları"],
			['title' => "Mail Logları", "link" => "admin/mail/logs", "icon" => "fas fa-envelope", "detail" => "Mail Logları"],
			// Genel ayarlar (genel/settings)
			['title' => "Genel Ayarlar", "link" => "admin/publicSettings", "icon" => "fas fa-cog", "detail" => "Genel Ayarlar"],
			// İçerik Yönetimi
			["title" => "Blog Yönetimi", "link" => "admin/blog", "icon" => "fas fa-rss", "detail" => "Blog İçerik Yönetimi"],
			["title" => "Sayfa Yönetimi", "link" => "admin/pages", "icon" => "fas fa-file-alt", "detail" => "Statik Sayfa Yönetimi"],
			["title" => "Bildirim Yönetimi", "link" => "admin/Notification/notificationList", "icon" => "fas fa-bell", "detail" => "Bildirim Ayarları"]
		];
	}

	public function __construct()
    {
        parent::__construct();
		$segments = $this->uri->segment_array();
		$segments = array_slice($segments, 1);

		if (count($segments) == 2) {
			if ($segments[0] == "API" && $segments[1] == "uploadTicketImage") {
				return;
			}
            
            // Arama endpointlerinin kontrol edilmeden erişime açılması
            if ($segments[0] == "API" && 
                (
                    $segments[1] == "search" || 
                    $segments[1] == "searchMenu" || 
                    $segments[1] == "searchUsers" || 
                    $segments[1] == "searchProducts" || 
                    $segments[1] == "searchPages"
                )
            ) {
                return;
            }
		}

        if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
            exit(json_encode(["status" => false, "error" => "Unauthorized request."], JSON_UNESCAPED_UNICODE));
        }
    }

    public function getUsers() {
		$countAllResults = $this->db->count_all_results("user");

		$orders = [
			0 => "type",
			1 => "name",
			2 => "surname",
			3 => "email",
			4 => "phone",
			5 => "tc",
			6 => "date",
			7 => "balance",
			8 => "discount"
		];

		$data = $this->db;
		$post = $this->input->post();
		if($post['search']['value']){
			$data = $data->like("(CASE WHEN type = 1 THEN 'Alıcı' ELSE 'Satıcı' END)", $post['search']['value'],'both',false);
			$data = $data->or_like('CONCAT(name," ",surname)', $post['search']['value'],'both',false);
			$data = $data->or_like('email', $post['search']['value']);
			$data = $data->or_like('phone', $post['search']['value']);
			$data = $data->or_like('tc', $post['search']['value']);
			$data = $data->or_like('date', $post['search']['value']);
			$data = $data->or_like('balance', $post['search']['value']);
			$data = $data->or_like('discount', $post['search']['value']);
		}

		if(isset($post['order'][0]['column'])) $data = $data->order_by($orders[$post['order'][0]['column']], $post['order'][0]['dir']);
		if($post['start'] > 0) $data = $data->offset($post['start']);
		if($post['length'] > 0) $data = $data->limit($post['length']);

		$results = $data->get('user')->result();

		foreach ($results as $key => $u) {
			$results[$key]->type = ($u->type == 1) ? 'Alıcı' : '<a href="'.base_url('admin/userShops').'?edit_shop='.$u->id.'">Satıcı</a>'; 
			$results[$key]->extra_row1 = "";
			if ($u->role_id != 1) {
            	if ($u->isActive == 1) { 
               		$results[$key]->extra_row1 .= '<a href="'.base_url('admin/product/setActive/0/').$u->id.'" class="btn btn-outline-danger btn-sm">Yasakla</a>';
            	}else{
                	$results[$key]->extra_row1 .= '<a href="'.base_url('admin/product/setActive/1/') . $u->id.'" class="btn btn-outline-success btn-sm">Yasağı Kaldır</a>';
            	}
           	} 
           	$results[$key]->extra_row2 = "";
           	$results[$key]->extra_row3 = '<a href="'.base_url('admin/product/userShopHistory/').$u->id.'" class="btn btn-outline-info btn-sm">Detay <i class="fa fa-arrow-right"></i></a>';
		}

		$response = [
            "draw" => $post['draw'],
            "iTotalRecords" => $countAllResults,
            "iTotalDisplayRecords" => $post['search']['value'] ? count($results) : $countAllResults,
            "aaData" => $results
        ];

		exit(json_encode($response, JSON_UNESCAPED_UNICODE));
    }

    public function getUser($id) {
    	$user = $this->db->where("id", $id)->get('user')->row();
    	if (isset($user)) {
    		if ($user->role_id != 1) {
		    	$user->type = ($user->type == 1) ? 'Alıcı' : '<a href="'.base_url('admin/userShops').'?edit_shop='.$user->id.'">Satıcı</a>';
		    	$user->name_surname = $user->name." ".$user->surname;
			 	exit(json_encode(["status" => true, "data" => $user], JSON_UNESCAPED_UNICODE));
		 	}
    	}
    	exit(json_encode(["status" => false], JSON_UNESCAPED_UNICODE));
    }

	public function getLogs() {
		// Toplam kayıt sayısını al
		$countAllResults = $this->db->count_all("logs");

		// Parametreleri al
		$post = $this->input->post();
		$start = intval($post['start'] ?? 0);
		$length = intval($post['length'] ?? 25);
		$search = $post['search']['value'] ?? '';
		$order_column = intval($post['order'][0]['column'] ?? 0);
		$order_dir = $post['order'][0]['dir'] ?? 'desc';

		// Sütun adlarını tanımla
		$columns = [
			0 => "id",
			1 => "user_id",
			2 => "user_ip",
			3 => "date",
			4 => "event"
		];

		// Ana sorgu oluşturma
		$this->db->select('logs.id, logs.user_id, logs.user_ip, logs.date, logs.event');
		$this->db->from('logs');

		// Arama filtresi
		if (!empty($search)) {
			$this->db->group_start();
			$this->db->like('logs.id', $search);
			$this->db->or_like('logs.user_id', $search);
			$this->db->or_like('logs.user_ip', $search);
			$this->db->or_like('logs.event', $search);
			$this->db->or_like('logs.date', $search);
			$this->db->group_end();
		}

		// Filtrelenmiş toplam kayıt sayısı - COUNT_ALL_RESULTS kullanarak optimizasyon
		$recordsFiltered = $this->db->count_all_results('', false);

		// Sıralama
		$this->db->order_by($columns[$order_column], $order_dir);
		
		// Sayfalama - doğru sınırlandırma için
		$this->db->limit($length, $start);
		
		// Sorguyu çalıştır
		$logs = $this->db->get()->result();
		
		// Kullanıcı bilgilerini verimli şekilde işle - tek seferde toplu sorgu
		$userIds = array_filter(array_map(function($log) {
			return $log->user_id > 0 ? $log->user_id : null;
		}, $logs));
		
		$users = [];
		if (!empty($userIds)) {
			$userQuery = $this->db->select('id, name, surname')
				->from('user')
				->where_in('id', array_unique($userIds))
				->get();
			
			foreach ($userQuery->result() as $user) {
				$users[$user->id] = $user;
			}
		}
		
		// Log sonuçlarını formatla
		$data = [];
		foreach ($logs as $log) {
			if ($log->user_id == 0) {
				$userName = "Misafir";
			} else if (isset($users[$log->user_id])) {
				$user = $users[$log->user_id];
				$userName = '<a href="'.base_url('admin/userShops').'?edit_user='.$user->id.'">'.$user->name.' '.$user->surname.'</a>';
			} else {
				$userName = "Kullanıcı Bulunamadı";
			}
			
			$data[] = [
				'id' => $log->id,
				'user_id' => $userName,
				'user_ip' => $log->user_ip,
				'date' => $log->date,
				'event' => $log->event
			];
		}
		
		// Yanıt formatı
		$response = [
			"draw" => intval($post['draw']),
			"recordsTotal" => $countAllResults,
			"recordsFiltered" => $recordsFiltered,
			"data" => $data
		];
		
		echo json_encode($response, JSON_UNESCAPED_UNICODE);
	}

    public function getSellHistory() {
		$countAllResults = $this->db->count_all("shop"); 

		$orders = [
			0 => "id",
			1 => 'name',
			2 => "type",
			3 => ["code", "extras"],
			4 => "price",
			5 => "date",
		];

		$data = $this->db->where('shop.status', 0)->select("shop.*, CONCAT(user.name,' ',user.surname) AS name, invoice.product as code, invoice.extras, (NULLIF(invoice.product, '') IS NOT NULL) as has_code",false)->join('user', 'user.id = shop.user_id', 'left')->join('invoice', 'invoice.shop_id = shop.id', 'left');
		$post = $this->input->post();
		if($post['search']['value']){
			$data = $data->like("(CASE WHEN shop.type = 0 THEN 'Bakiye Yüklemesi' ELSE 'Ürün Alımı' END)", $post['search']['value'],'both',false);
			$data = $data->or_like('CONCAT(user.name," ",user.surname)', $post['search']['value'],'both',false);
			$data = $data->or_like('shop.id', $post['search']['value']);
			$data = $data->or_like('invoice.product', $post['search']['value']);
			$data = $data->or_like('invoice.extras', $post['search']['value']);
			$data = $data->or_like('shop.date', $post['search']['value']);
			$data = $data->or_like('shop.price', $post['search']['value']);
		}

		if(isset($post['order'][0]['column'])) {
			if (is_array($orders[$post['order'][0]['column']])) {
				foreach ($orders[$post['order'][0]['column']] as $key => $value) {
					$data = $data->order_by($value, $post['order'][0]['dir']);
				}
			} else {
				$data = $data->order_by($orders[$post['order'][0]['column']], $post['order'][0]['dir']);
			}
		}
		if($post['start'] > 0) $data = $data->offset($post['start']);
		if($post['length'] > 0) $data = $data->limit($post['length']);

		$results = $data->get('shop')->result();

		foreach ($results as $key => $u) {
			$seller = $this->db->where('id', $u->seller_id)->get('user')->row(); 
            if ($u->seller_id == 0 || $seller->isAdmin == 1) {
                $results[$key]->seller = "<span class='text-success'>Yönetici Ürünü</span>";
            }else{
                $results[$key]->seller = $seller->name . " " . $seller->surname;
            }
			/*$results[$key]->seller = ($u->seller_is_admin == 1) ? "<span class='text-success'>Yönetici Ürünü</span>" : '<a href="'.base_url('admin/userShops').'?edit_shop='.$u->seller_id.'">'.$u->seller_name.' '.$u->seller_surname.'</a>'; */

			$results[$key]->type = ($u->type == 0) ? "Bakiye Yüklemesi" : 'Ürün Alımı'; 

			$results[$key]->code_extra = "";
			if ($u->has_code == 1) {
            	$results[$key]->code_extra = $u->code;
           	} else {
           		$ext = json_decode($u->extras ?? "{}", true) ?? [];
           		$results[$key]->code_extra = implode(",", array_map(function ($v, $k) { return sprintf("<b>%s:</b>%s", $k, $v); },$ext,array_keys($ext)));
           	}
           	$results[$key]->price = $u->price."₺";
           	$results[$key]->invoice = '<a href="'.base_url('admin/product/invoice/') . $u->id.'">İncele <i class="fas fa-chevron-right"></i></a>';
		}

		$response = [
            "draw" => $post['draw'],
            "iTotalRecords" => $countAllResults,
            "iTotalDisplayRecords" => $post['search']['value'] ? count($results) : $countAllResults,
            "aaData" => $results
        ];

		exit(json_encode($response, JSON_UNESCAPED_UNICODE));
    }

	public function uploadTicketImage() {
		if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
			exit(json_encode(["message" => "clientError"]));
		}

		if (isset($_FILES["image"]['error']['value']) && $_FILES["image"]['error']['value'] != 0) {
			exit(json_encode(["message" => "uploadError"]));
		}

		$allowed = array("image/jpeg", "image/gif", "image/png", "image/jpg", "image/webp");
		if(!in_array($_FILES['image']['type'], $allowed)) {
			exit(json_encode(["message" => "uploadError"]));
		}

		$nameFile = changePhoto('assets/img/blog/', "image");

		exit(json_encode(["success" => true, "data" => [
			"link" => base_url("assets/img/blog/".$nameFile)
		]]));
	}
	public function getCoupon($id) {
		$user = $this->session->userdata('info');
		$user = $this->db->where("id", $user['id'])->get("user")->row();
		if (!isPerm($user->role_id, 'seeCoupons')) {
			exit(json_encode(["status" => false, "message" => "Yetkiniz yok!"]));
		}
		$datas = new stdClass;
		$datas->users = $this->db->get("user")->result();
		$datas->categories = $this->db->get("category")->result();
		$datas->products = $this->db->where('isActive', 1)->get("product")->result();
		$datas->used_by = [];

		if ($id == "create") {
			$coupon = new stdClass;
			$coupon->coupon = "";
			$coupon->status = "active";
			$coupon->categories = [];
			$coupon->products = [];
			$coupon->type = "amount";
			$coupon->amount = 0;
			$coupon->min_amount = 0;
			$coupon->start_at = "";
			$coupon->end_at = "";
			$coupon->users = [];
			$coupon->used_by = [];
			$coupon->only_users = false;

			exit(json_encode(["status" => true, "data" => $coupon, "datas" => $datas], JSON_UNESCAPED_UNICODE));
		} else {
			$coupon = $this->db->where("id", $id)->get('coupons')->row();
			if (isset($coupon)) {
				if (isset($coupon->users) && $coupon->users == "all") {
					$coupon->only_users = false;
				} else {
					$coupon->only_users = true;
				}
				$coupon->users = json_decode($coupon->users ?? "[]", true);
				$coupon->used_by = json_decode($coupon->used_by ?? "[]", true);
				$coupon->categories = json_decode($coupon->categories ?? "[]", true);
				$coupon->products = json_decode($coupon->products ?? "[]", true);

				$coupon->start_at = date("Y-m-d H:i", strtotime($coupon->start_at));
				$coupon->end_at = date("Y-m-d H:i", strtotime($coupon->end_at));

				if (isset($coupon->users) && !empty($coupon->users)) {
					$this->db->group_start();
					$chunks = array_chunk($coupon->users, 25);
					foreach($chunks as $chunk)
					{
						$this->db->or_where_in('id', $chunk);
					}
					$this->db->group_end();
					$datas->used_by = $this->db->get("user")->result();
				}

				exit(json_encode(["status" => true, "data" => $coupon, "datas" => $datas], JSON_UNESCAPED_UNICODE));
			}
		}
		exit(json_encode(["status" => false], JSON_UNESCAPED_UNICODE));
	}

	public function getCategoriesPinabi() {
		$this->load->helper('api');
		$apiSettings = getAPIsettings();
		$apiSettings = $apiSettings['pinabi'];
		if (empty($apiSettings->apiUser) || empty($apiSettings->secretKey) || empty($apiSettings->Authorization)) {
			$content = [
				'status' => false,
				'message' => 'Pinabi bilgileri eksik!',
				'content' => []
			];
		} else {
			$this->load->library('Pinabi');
			$pinabi_response = $this->pinabi->getProductsByType("ep");
			$pinabi_response = json_decode($pinabi_response, true);
			if ($pinabi_response["status"]["code"] == 200) {
				$content = [
					'status' => true,
					'message' => 'Ürünler başarıyla getirildi!',
					'content' => $pinabi_response["gameList"]
				];
			} else {
				$content = [
					'status' => false,
					'message' => 'Ürünler getirilirken bir hata oluştu! ('.$pinabi_response["status"]["describe"].')',
					'content' => [],
					'response' => $pinabi_response
				];
			}
		}

		exit (json_encode($content));
	}

	public function getProductPinabi() {
		$id = $this->input->post('id');
		$this->load->helper('api');
		$apiSettings = getAPIsettings();
		$apiSettings = $apiSettings['pinabi'];
		if (empty($apiSettings->apiUser) || empty($apiSettings->secretKey) || empty($apiSettings->Authorization)) {
			$content = [
				'status' => false,
				'message' => 'Pinabi bilgileri eksik!',
				'content' => []
			];
		} else {
			$this->load->library('Pinabi');
			$pinabi_response = $this->pinabi->getProductsByType("ep");
			$pinabi_response = json_decode($pinabi_response, true);
			if ($pinabi_response["status"]["code"] == 200) {
				//find $id in $pinabi_response["gameList"]
				$product = array_filter($pinabi_response["gameList"], function ($v) use ($id) {
					return $v['id'] == $id;
				});
				if (count($product) > 0) {
					$content = [
						'status' => true,
						'message' => 'Ürünler başarıyla getirildi!',
						'content' => $product[array_key_first($product)]
					];
				} else {
					$content = [
						'status' => false,
						'message' => 'Ürünler getirilirken bir hata oluştu! (Ürün bulunamadı)',
						'content' => []
					];
				}
			} else {
				$content = [
					'status' => false,
					'message' => 'Ürünler getirilirken bir hata oluştu! ('.$pinabi_response["status"]["describe"].')',
					'content' => []
				];
			}
		}

		exit (json_encode($content));
	}

	public function getCategoriesTurkPin()
	{
		$properties = $this->db->where('id', 1)->get('properties')->row();
		if (!empty($properties->turkpin_username) && !empty($properties->turkpin_password)) {
			$this->load->library('Turkpin');
			$content = $this->turkpin->getGameList();
			if (!empty($content["data"]['params']['HATA_NO']) && $content['params']['HATA_NO'] != "000") {
				$content = [
					'status' => false,
					'message' => 'Ürünler getirilirken bir hata oluştu! ('.$content['params']['HATA_NO'].')',
					'content' => []
				];
			} else {
				$content = [
					'status' => true,
					'message' => 'Ürünler başarıyla getirildi!',
					'content' => $content['data']['params']['oyunListesi']['oyun']
				];
			}
		} else {
			$content = [
				'status' => false,
				'message' => 'Turkpin bilgileri eksik!',
				'content' => []
			];
		}

		exit (json_encode($content));
	}

	public function getProductTurkPin()
	{
		$id = $this->input->post('id');
		$properties = $this->db->where('id', 1)->get('properties')->row();
		$this->load->library('Turkpin');
		$content = $this->turkpin->getEpinProduct($id);

		exit (json_encode($content));
	}

	public function InitProductsFromAPI() {
		$selected_categories = $this->input->post('active_categories');
		$selected_products = $this->input->post('products');
		$product_prices = $this->input->post('product_prices');

		//send request to https://base.advetro.com/api/v1/categories
		$categories = json_decode(file_get_contents('https://base.advetro.com/api/v1/categories'), true);
		//exit();
		if (empty($categories)) {
			exit(json_encode(["success" => false, "message" => "API sistemi ile bağlantı kurulamadı! Sunucu cevap vermiyor."]));
		}
		if ($categories['success']) {
			$categories = $categories['categories'];
			foreach ($categories as $category) {
				if (!in_array($category['id'], $selected_categories)) continue;
				$img = file_get_contents($category['img']);
				$filename = basename($category['img']);
				file_put_contents('assets/img/category/' . $filename, $img);
				$this->db->insert('category', [
					'name' => $category['name'],
					'slug' => $category['slug'],
					'isActive' => $category['isActive'],
					'mother_category_id' => $category['mother_category_id'],
					'isMenu' => $category['isMenu'],
					'description' => $category['description'],
					'img' => $filename,
				]);
				$category_id = $this->db->insert_id();
				foreach ($category['products'] as $product) {
					if (!in_array($product['id'], $selected_products)) continue;

					$img = file_get_contents($product['img']);
					$filename = basename($product['img']);
					file_put_contents('assets/img/product/' . $filename, $img);
					$this->db->insert('product', [
						'name' => $product['name'],
						'slug' => $product['slug'],
						'img' => $filename,
						'background_img' => $product['background_img'],
						'desc' => $product['desc'],
						'price' => $product_prices[$product['id']],
						'isStock' => $product['isStock'],
						'text' => $product['text'],
						'isActive' => $product['isActive'],
						'category_id' => $category_id,
						'discount' => $product['discount'],
						'game_code' => $product['game_code'],
						'product_code' => $product['product_code'],
						'seller_id' => 0,
						'difference_percent' => $product['difference_percent'],
					]);
				}
			}
			exit(json_encode(["success" => true, "message" => "Ürünler başarıyla yüklendi!"]));
		} else {
			exit(json_encode(["success" => false, "message" => "API sistemi ile bağlantı kurulamadı!"]));
		}
	}

    public function getProviderCategories() {
        $this->load->helper('provider');
        $provider_id = $this->input->post('provider_id');

        if ($provider_id === 'turkpin' || $provider_id === 'pinabi') {
            if ($provider_id === 'pinabi') {
                $this->load->helper('api');
                $api_settings = getAPIsettings();
                if (!isset($api_settings['pinabi'])) {
                    echo json_encode([
                        'status' => false,
                        'message' => 'Pinabi bilgileri eksik!'
                    ]);
                    return;
                }
                $pinabi_settings = $api_settings["pinabi"];
                if (empty($pinabi_settings->apiUser) || empty($pinabi_settings->secretKey) || empty($pinabi_settings->Authorization)) {
                    echo json_encode([
                        'status' => false,
                        'message' => 'Pinabi bilgileri eksik!'
                    ]);
                }
            } else {
                $turkpin_settings = $this->db->where('id', 1)->get('properties')->row();
                if (empty($turkpin_settings->turkpin_username) || empty($turkpin_settings->turkpin_password)) {
                    echo json_encode([
                        'status' => false,
                        'message' => 'Turkpin bilgileri eksik!'
                    ]);
                }
            }
            $categories = $provider_id === 'turkpin' ?
                getTurkpinCategories() :
                getPinabiCategories();

            if ($categories === false) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Kategoriler alınırken hata oluştu'
                ]);
                return;
            }

            echo json_encode([
                'status' => true,
                'categories' => normalizeCategories($categories, $provider_id)
            ]);
            return;
        }

        $provider = $this->db->where('id', $provider_id)->get('product_providers')->row();

        if (!$provider) {
            echo json_encode([
                'status' => false,
                'message' => 'Tedarikçi bulunamadı'
            ]);
            return;
        }

        $categories = getProviderCategories($provider);

        if ($categories === false) {
            echo json_encode([
                'status' => false,
                'message' => 'Kategoriler alınırken hata oluştu'
            ]);
            return;
        }

        echo json_encode([
            'status' => true,
            'categories' => $categories
        ]);
    }

	public function getProviderProducts() {
		$this->load->helper('provider');
		$provider_id = $this->input->post('provider_id');
		$category_id = $this->input->post('category_id');
		
		if ($provider_id === 'turkpin' || $provider_id === 'pinabi') {
			$products = $provider_id === 'turkpin' ? 
				getTurkpinProducts($category_id) : 
				getPinabiProducts($category_id);
				
			if ($products === false) {
				echo json_encode([
					'status' => false, 
					'message' => 'Ürünler alınırken hata oluştu'
				]);
				return;
			}
			
			echo json_encode([
				'status' => true, 
				'products' => normalizeProducts($products, $provider_id)
			]);
			return;
		}
		
		$provider = $this->db->where('id', $provider_id)->get('product_providers')->row();
		
		if (!$provider) {
			echo json_encode([
				'status' => false, 
				'message' => 'Tedarikçi bulunamadı'
			]);
			return;	
		}

		$products = getProviderProducts($provider, $category_id);
		
		if ($products === false) {
			echo json_encode([
				'status' => false, 
				'message' => 'Ürünler alınırken hata oluştu'
			]);
			return;
		}
		
		echo json_encode([
			'status' => true, 
			'products' => $products
		]);
	}

    public function getSalesStatus() {
        $timeFrame = $this->input->get('timeFrame', true) ?? 'daily';
        
        switch($timeFrame) {
            case 'monthly':
                $startDate = date('Y-m-d', strtotime('-30 days'));
                break;
            case 'weekly':
                $startDate = date('Y-m-d', strtotime('-7 days'));
                break;
            case 'daily':
            default:
                $startDate = date('Y-m-d');
                break;
        }

        $endDate = date('Y-m-d');

        // Başarılı satışlar ve kazanç
        $this->db->where('DATE(transaction_date) >=', $startDate);
        $this->db->where('DATE(transaction_date) <=', $endDate);
        $successful = $this->db->where('transaction_status', 'successful')->where_in('payment_method', ['balance', 'credit_card'])->count_all_results('earnings');
        
        $this->db->where('DATE(transaction_date) >=', $startDate);
        $this->db->where('DATE(transaction_date) <=', $endDate);
        $successfulEarnings = $this->db->select_sum('amount')->where('transaction_status', 'successful')->where_in('payment_method', ['balance', 'credit_card'])->get('earnings')->row()->amount;
        $successfulEarnings = $successfulEarnings ? $successfulEarnings : 0;

        // Başarısız satışlar ve kazanç (shop tablosundan)
        $this->db->where('DATE(date) >=', $startDate);
        $this->db->where('DATE(date) <=', $endDate);
        $unsuccessful = $this->db->where('status', 1)->count_all_results('shop');
        
        $this->db->where('DATE(date) >=', $startDate);
        $this->db->where('DATE(date) <=', $endDate);
        $unsuccessfulEarnings = $this->db->select_sum('price')->where('status', 1)->get('shop')->row()->price;
        $unsuccessfulEarnings = $unsuccessfulEarnings ? $unsuccessfulEarnings : 0;

        // İptal edilenler ve kazanç
        $this->db->where('DATE(transaction_date) >=', $startDate);
        $this->db->where('DATE(transaction_date) <=', $endDate);
        $cancelled = $this->db->where('transaction_status', 'cancelled')->where_in('payment_method', ['balance', 'credit_card'])->count_all_results('earnings');
        
        $this->db->where('DATE(transaction_date) >=', $startDate);
        $this->db->where('DATE(transaction_date) <=', $endDate);
        $cancelledEarnings = $this->db->select_sum('amount')->where('transaction_status', 'cancelled')->where_in('payment_method', ['balance', 'credit_card'])->get('earnings')->row()->amount;
        $cancelledEarnings = $cancelledEarnings ? $cancelledEarnings : 0;

        // Beklemedeki ürünler ve kazanç
        $this->db->where('DATE(transaction_date) >=', $startDate);
        $this->db->where('DATE(transaction_date) <=', $endDate);
        $pending = $this->db->where('transaction_status', 'pending')->where_in('payment_method', ['balance', 'credit_card'])->count_all_results('earnings');
        
        $this->db->where('DATE(transaction_date) >=', $startDate);
        $this->db->where('DATE(transaction_date) <=', $endDate);
        $pendingEarnings = $this->db->select_sum('amount')->where('transaction_status', 'pending')->where_in('payment_method', ['balance', 'credit_card'])->get('earnings')->row()->amount;
        $pendingEarnings = $pendingEarnings ? $pendingEarnings : 0;

        $data = [
            'successful' => $successful,
            'successfulEarnings' => $successfulEarnings,
            'unsuccessful' => $unsuccessful,
            'unsuccessfulEarnings' => $unsuccessfulEarnings,
            'cancelled' => $cancelled,
            'cancelledEarnings' => $cancelledEarnings,
            'pending' => $pending,
            'pendingEarnings' => $pendingEarnings
        ];

        echo json_encode($data);
    }

    public function get_page_users() {
        // AJAX kontrolü
        if (!$this->input->is_ajax_request()) {
            exit('Direct access not allowed');
        }

        $page = $this->input->post('page');

        if (!$page) {
            echo json_encode(['error' => 'Page parameter is required']);
            return;
        }

        $timeout = date('Y-m-d H:i:s', strtotime('-5 minutes'));

        $users = $this->db->select('u.name, u.email, s.last_activity')
            ->from('ci_sessions s')
            ->join('user u', 'u.id = s.user_id')
            ->where('s.last_page', $page)
            ->where('s.last_activity >', $timeout)
            ->get()
            ->result();

        header('Content-Type: application/json');
        echo json_encode($users);
    }

    public function get_live_summary() {
        // AJAX kontrolü
        if (!$this->input->is_ajax_request()) {
            exit('Direct access not allowed');
        }

        $timeout = date('Y-m-d H:i:s', strtotime('-5 minutes'));

        // Online kullanıcı sayısı
        $online_users = $this->db->where('last_activity >', $timeout)
            ->where('user_id IS NOT NULL')
            ->group_by('user_id')
            ->count_all_results('ci_sessions');

        // En aktif sayfalar
        $active_pages = $this->db->select('last_page, COUNT(*) as user_count')
            ->where('last_activity >', $timeout)
            ->where('user_id IS NOT NULL')
            ->group_by('last_page')
            ->order_by('user_count', 'DESC')
            ->limit(5)
            ->get('ci_sessions')
            ->result();

        header('Content-Type: application/json');
        echo json_encode([
            'online_users' => $online_users,
            'active_pages' => $active_pages
        ]);
    }

    public function getSuccessfulSales() {
        $timeFrame = $this->input->get('timeFrame');
        $endDate = date('Y-m-d');
        
        switch($timeFrame) {
            case 'daily':
                $startDate = date('Y-m-d', strtotime('-1 day'));
                break;
            case 'monthly':
                $startDate = date('Y-m-d', strtotime('-30 days'));
                break;
            case 'weekly':
            default:
                $startDate = date('Y-m-d', strtotime('-7 days'));
                break;
        }

        $query = $this->db->select("DATE(transaction_date) as date, COUNT(*) as count, SUM(amount) as total")
            ->where('transaction_status', 'successful')
            ->where('DATE(transaction_date) >=', $startDate)
            ->where('DATE(transaction_date) <=', $endDate)
            ->where_in('payment_method', ['balance', 'credit_card'])
            ->group_by('DATE(transaction_date)')
            ->order_by('DATE(transaction_date)', 'ASC')
            ->get('earnings');

        $results = [];
        $current = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('+1 day');

        $data = [];
        foreach ($query->result() as $row) {
            $data[$row->date] = [
                'count' => $row->count,
                'total' => $row->total
            ];
        }

        while ($current < $end) {
            $currentDate = $current->format('Y-m-d');
            $results[] = [
                'date' => $currentDate,
                'count' => isset($data[$currentDate]) ? $data[$currentDate]['count'] : 0,
                'total' => isset($data[$currentDate]) ? $data[$currentDate]['total'] : 0
            ];
            $current->modify('+1 day');
        }

        echo json_encode($results);
    }

    public function getFailedSales() {
        $timeFrame = $this->input->get('timeFrame');
        $endDate = date('Y-m-d');
        
        switch($timeFrame) {
            case 'daily':
                $startDate = date('Y-m-d', strtotime('-1 day'));
                break;
            case 'monthly':
                $startDate = date('Y-m-d', strtotime('-30 days'));
                break;
            case 'weekly':
            default:
                $startDate = date('Y-m-d', strtotime('-7 days'));
                break;
        }

        $query = $this->db->select("DATE(date) as date, COUNT(*) as count, SUM(price) as total")
            ->where('status', 1)
            ->where('DATE(date) >=', $startDate)
            ->where('DATE(date) <=', $endDate)
            ->group_by('DATE(date)')
            ->order_by('DATE(date)', 'ASC')
            ->get('shop');

        $results = [];
        $current = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('+1 day');

        $data = [];
        foreach ($query->result() as $row) {
            $data[$row->date] = [
                'count' => $row->count,
                'total' => $row->total
            ];
        }

        while ($current < $end) {
            $currentDate = $current->format('Y-m-d');
            $results[] = [
                'date' => $currentDate,
                'count' => isset($data[$currentDate]) ? $data[$currentDate]['count'] : 0,
                'total' => isset($data[$currentDate]) ? $data[$currentDate]['total'] : 0
            ];
            $current->modify('+1 day');
        }

        echo json_encode($results);
    }

    public function getCancelledSales() {
        $timeFrame = $this->input->get('timeFrame');
        $endDate = date('Y-m-d');
        
        switch($timeFrame) {
            case 'daily':
                $startDate = date('Y-m-d', strtotime('-1 day'));
                break;
            case 'monthly':
                $startDate = date('Y-m-d', strtotime('-30 days'));
                break;
            case 'weekly':
            default:
                $startDate = date('Y-m-d', strtotime('-7 days'));
                break;
        }

        $query = $this->db->select("DATE(transaction_date) as date, COUNT(*) as count, SUM(amount) as total")
            ->where('transaction_status', 'cancelled')
            ->where('DATE(transaction_date) >=', $startDate)
            ->where('DATE(transaction_date) <=', $endDate)
            ->where_in('payment_method', ['balance', 'credit_card'])
            ->group_by('DATE(transaction_date)')
            ->order_by('DATE(transaction_date)', 'ASC')
            ->get('earnings');

        $results = [];
        $current = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('+1 day');

        $data = [];
        foreach ($query->result() as $row) {
            $data[$row->date] = [
                'count' => $row->count,
                'total' => $row->total
            ];
        }

        while ($current < $end) {
            $currentDate = $current->format('Y-m-d');
            $results[] = [
                'date' => $currentDate,
                'count' => isset($data[$currentDate]) ? $data[$currentDate]['count'] : 0,
                'total' => isset($data[$currentDate]) ? $data[$currentDate]['total'] : 0
            ];
            $current->modify('+1 day');
        }

        echo json_encode($results);
    }

    public function getPendingSales() {
        $timeFrame = $this->input->get('timeFrame');
        $endDate = date('Y-m-d');
        
        switch($timeFrame) {
            case 'daily':
                $startDate = date('Y-m-d', strtotime('-1 day'));
                break;
            case 'monthly':
                $startDate = date('Y-m-d', strtotime('-30 days'));
                break;
            case 'weekly':
            default:
                $startDate = date('Y-m-d', strtotime('-7 days'));
                break;
        }

        $query = $this->db->select("DATE(transaction_date) as date, COUNT(*) as count, SUM(amount) as total")
            ->where('transaction_status', 'pending')
            ->where('DATE(transaction_date) >=', $startDate)
            ->where('DATE(transaction_date) <=', $endDate)
            ->where_in('payment_method', ['balance', 'credit_card'])
            ->group_by('DATE(transaction_date)')
            ->order_by('DATE(transaction_date)', 'ASC')
            ->get('earnings');

        $results = [];
        $current = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('+1 day');

        $data = [];
        foreach ($query->result() as $row) {
            $data[$row->date] = [
                'count' => $row->count,
                'total' => $row->total
            ];
        }

        while ($current < $end) {
            $currentDate = $current->format('Y-m-d');
            $results[] = [
                'date' => $currentDate,
                'count' => isset($data[$currentDate]) ? $data[$currentDate]['count'] : 0,
                'total' => isset($data[$currentDate]) ? $data[$currentDate]['total'] : 0
            ];
            $current->modify('+1 day');
        }

        echo json_encode($results);
    }

    public function getSalesData()
    {
        // DataTables'ın standart parametrelerini al
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search_value = $this->input->post('search')['value'] ?? '';
        $order_column = $this->input->post('order')[0]['column'] ?? 0;
        $order_dir = $this->input->post('order')[0]['dir'] ?? 'desc';
        
        // Özel parametrelerimiz
        $filter = $this->input->post('filter') ?: 'all';
        $startDate = $this->input->post('startDate');
        $endDate = $this->input->post('endDate');
        
        // Sütun adlarını tanımla
        $columns = array(
            0 => 'shop.id',
            1 => 'user.name', // Müşteri adı için
            2 => 'shop.type', // İşlem türü
            3 => 'shop.type', // Alım şekli (payment_method)
            4 => 'shop.price', // Tutar
            5 => 'shop.date',  // Tarih
            6 => 'shop.status' // Durum
        );
        
        // Sıralama sütununu al
        $order_column_name = $columns[$order_column];
        
        // Önce toplam kayıt sayısını al (filtresiz)
        $recordsTotal = $this->db->count_all_results('shop');
        
        // Veritabanı sorgusunu oluştur
        $this->db->select('
            shop.id,
            shop.price as amount,
            shop.date,
            shop.status,
            shop.type,
            shop.user_id,
            CASE 
                WHEN shop.type IN ("deposit", "credit_card") THEN "Bakiye Yükleme"
                ELSE "Ürün Alımı"
            END as type_text,
            CASE 
                WHEN shop.type = "credit_card" THEN "Kredi Kartı"
                WHEN shop.type = "deposit" THEN "Kredi Kartı"
                ELSE "Bakiye"
            END as payment_method,
            COALESCE(CONCAT(user.name, " ", user.surname), "Misafir") as customer,
            CASE 
                WHEN shop.status = 0 THEN "Tamamlandı"
                WHEN shop.status = 2 THEN "İptal Edildi"
                WHEN shop.status = 1 AND (
                    (shop.type IN ("deposit", "credit_card") AND TIMESTAMPDIFF(MINUTE, shop.date, NOW()) > 5) OR
                    shop.type NOT IN ("deposit", "credit_card")
                ) THEN "İptal Edildi"
                WHEN shop.status = 1 THEN "Beklemede"
                ELSE "Bilinmeyen Durum"
            END as status_text
        ', false);

        $this->db->from("shop");
        $this->db->join("user", "user.id = shop.user_id", "left");

        // Filtreler
        if ($filter === 'product') {
            $this->db->where('shop.type !=', 'deposit');
            $this->db->where('shop.type !=', 'credit_card');
        } else if ($filter === 'deposit') {
            $this->db->group_start();
            $this->db->where('shop.type', 'deposit');
            $this->db->or_where('shop.type', 'credit_card');
            $this->db->group_end();
        }

        // Tarih filtresi
        if ($startDate && $endDate) {
            $this->db->where('shop.date >=', $startDate . ' 00:00:00');
            $this->db->where('shop.date <=', $endDate . ' 23:59:59');
        }

        // Arama filtresi - daha kapsamlı arama
        if (!empty($search_value)) {
            $this->db->group_start();
            $this->db->like('shop.id', $search_value);
            $this->db->or_like("CONCAT(user.name, ' ', user.surname)", $search_value, 'both', false);
            $this->db->or_like('shop.price', $search_value);
            $this->db->or_like('shop.date', $search_value);
            // Ürün adı üzerinde arama yaparken JOIN gerekebilir
            // $this->db->or_like('product.name', $search_value);
            $this->db->group_end();
        }
        
        // Filtrelenmiş kayıt sayısını almak için kopyalayalım
        $countDb = clone $this->db;
        $recordsFiltered = $countDb->count_all_results();
        
        // Sıralama
        $this->db->order_by($order_column_name, $order_dir);
        
        // Sayfalama
        if ($length > 0) {
            $this->db->limit($length, $start);
        }
        
        // Sorguyu çalıştır
        $query = $this->db->get();
        $data = $query->result();
        
        // Veriyi formatla
        $formatted_data = [];
        foreach ($data as $row) {
            $formatted_data[] = [
                'id' => $row->id,
                'customer' => $row->customer,
                'user_id' => $row->user_id,
                'type_text' => $row->type_text,
                'payment_method' => $row->payment_method,
                'amount' => $row->amount,
                'date' => $row->date,
                'status_text' => $row->status_text
            ];
        }
        
        // DataTables'ın beklediği formatta yanıt döndür
        $response = [
            "draw" => intval($draw),
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $formatted_data
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    public function getTransactionDetails($id) {
        $transaction = $this->db->select('
            shop.*,
            CONCAT(user.name, " ", user.surname) as user_name,
            user.email as user_email,
            user.phone as user_phone,
            product.name as product_name,
            product.img as product_img,
            invoice.product as code,
            invoice.extras,
            (NULLIF(invoice.product, "") IS NOT NULL) as has_code
        ')
        ->from('shop')
        ->join('user', 'user.id = shop.user_id', 'left')
        ->join('product', 'product.id = shop.product_id', 'left')
        ->join('invoice', 'invoice.shop_id = shop.id', 'left')
        ->where('shop.id', $id)
        ->get()
        ->row();

        if (!$transaction) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(404)
                ->set_output(json_encode(['error' => 'Transaction not found']));
        }

        // Format transaction data
        $transaction->status_text = $transaction->status == 0 ? 'Tamamlandı' : 
                                  ($transaction->status == 1 ? 'Beklemede' : 
                                  ($transaction->status == 2 ? 'İptal Edildi' : 'Bilinmeyen'));
        
        $transaction->type_text = $transaction->type == 'deposit' ? 'Bakiye Yükleme' : 'Ürün Alımı';
        $transaction->payment_method = $transaction->type == 'credit_card' ? 'Kredi Kartı' : 'Bakiye';
        
        // Format extras if exists
        if ($transaction->extras) {
            $transaction->extras = json_decode($transaction->extras);
        }

        // Calculate time passed
        $date1 = new DateTime($transaction->date);
        $date2 = new DateTime();
        $interval = $date1->diff($date2);
        
        if ($interval->y > 0) {
            $transaction->time_passed = $interval->y . ' yıl önce';
        } else if ($interval->m > 0) {
            $transaction->time_passed = $interval->m . ' ay önce';
        } else if ($interval->d > 0) {
            $transaction->time_passed = $interval->d . ' gün önce';
        } else if ($interval->h > 0) {
            $transaction->time_passed = $interval->h . ' saat önce';
        } else if ($interval->i > 0) {
            $transaction->time_passed = $interval->i . ' dakika önce';
        } else {
            $transaction->time_passed = 'Az önce';
        }

        // Load view with transaction data
        $this->load->view('admin/modals/transaction_details', ['transaction' => $transaction]);
    }

    public function getOnlineUsersDetails() {
        header('Content-Type: application/json');
        
        // Session manager'dan online kullanıcıları al
        $online_users = $this->session_manager->get_online_users();
        
        echo json_encode($online_users);
    }

    // Arama Endpoint'leri Başlangıç
    
    /**
     * Arama alanı için kullanıcı arama endpoint'i
     * 
     * @return void
     */
    public function searchUsers() {
        $query = $this->input->get('query');
        
        if (empty($query) || strlen($query) < 2) {
            exit(json_encode(["status" => false, "message" => "Arama sorgusu en az 2 karakter olmalıdır."], JSON_UNESCAPED_UNICODE));
        }
        
        $this->db->select('id, name, surname, email, phone, balance')
            ->from('user')
            ->group_start()
                ->like('name', $query)
                ->or_like('surname', $query)
                ->or_like('email', $query)
                ->or_like('phone', $query)
                ->or_like('CONCAT(name, " ", surname)', $query, 'both', false)
            ->group_end()
            ->limit(5);
            
        $results = $this->db->get()->result();
        
        if (empty($results)) {
            exit(json_encode(["status" => false, "message" => "Hiç sonuç bulunamadı."], JSON_UNESCAPED_UNICODE));
        }
        
        // Kullanıcı sonuçlarını formatla
        $formatted_results = [];
        foreach ($results as $user) {
            $formatted_results[] = [
                'id' => $user->id,
                'title' => $user->name . ' ' . $user->surname,
                'detail' => $user->email,
                'icon' => 'fas fa-user',
                'link' => base_url('admin/users') . '?user=' . $user->id,
                'type' => 'user'
            ];
        }
        
        exit(json_encode(["status" => true, "results" => $formatted_results], JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * Arama alanı için ürün arama endpoint'i
     * 
     * @return void
     */
    public function searchProducts() {
        $query = $this->input->get('query');
        
        if (empty($query) || strlen($query) < 2) {
            exit(json_encode(["status" => false, "message" => "Arama sorgusu en az 2 karakter olmalıdır."], JSON_UNESCAPED_UNICODE));
        }
        
        $this->db->select('product.id, product.name, product.price, product.img, category.name as category_name')
            ->from('product')
            ->join('category', 'category.id = product.category_id', 'left')
            ->group_start()
                ->like('product.name', $query)
                ->or_like('product.desc', $query)
                ->or_like('category.name', $query)
            ->group_end()
            ->where('product.isActive', 1)
            ->limit(5);
            
        $results = $this->db->get()->result();
        
        if (empty($results)) {
            exit(json_encode(["status" => false, "message" => "Hiç sonuç bulunamadı."], JSON_UNESCAPED_UNICODE));
        }
        
        // Ürün sonuçlarını formatla
        $formatted_results = [];
        foreach ($results as $product) {
            $formatted_results[] = [
                'id' => $product->id,
                'title' => $product->name,
                'detail' => 'Fiyat: ' . $product->price . '₺ | Kategori: ' . $product->category_name,
                'icon' => 'fas fa-box',
                'link' => base_url('admin/products') . '?product=' . $product->id,
                'type' => 'product',
                'img' => !empty($product->img) ? base_url('assets/img/product/' . $product->img) : null
            ];
        }
        
        exit(json_encode(["status" => true, "results" => $formatted_results], JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * Arama alanı için sayfa/kategori arama endpoint'i
     * 
     * @return void
     */
    public function searchPages() {
        $query = $this->input->get('query');
        
        if (empty($query) || strlen($query) < 2) {
            exit(json_encode(["status" => false, "message" => "Arama sorgusu en az 2 karakter olmalıdır."], JSON_UNESCAPED_UNICODE));
        }
        
        // Sorguyu normalize et
        $normalized_query = $this->normalizeText($query);
        
        // Kategori arama
        $this->db->select('id, name, "category" as type')
            ->from('category')
            ->where('isActive', 1)
            ->limit(10); // Daha fazla sonuç alıp sonra filtreleyeceğiz
        $all_categories = $this->db->get()->result();
        
        // Blog arama 
        $this->db->select('id, title as name, "blog" as type')
            ->from('blog')
            ->where('isActive', 1)
            ->limit(10);
        $all_blogs = $this->db->get()->result();
        
        // Sayfa arama
        $this->db->select('id, title as name, "page" as type')
            ->from('pages')
            ->limit(10);
        $all_pages = $this->db->get()->result();
        
        // Normalize edilmiş stringlerle arama yapmak için PHP tarafında filtreleme yapalım
        $categories = [];
        foreach ($all_categories as $category) {
            $normalized_name = $this->normalizeText($category->name);
            if (strpos($normalized_name, $normalized_query) !== false) {
                $categories[] = $category;
                if (count($categories) >= 3) break; // En fazla 3 sonuç
            }
        }
        
        $blogs = [];
        foreach ($all_blogs as $blog) {
            $normalized_name = $this->normalizeText($blog->name);
            if (strpos($normalized_name, $normalized_query) !== false) {
                $blogs[] = $blog;
                if (count($blogs) >= 3) break;
            }
        }
        
        $pages = [];
        foreach ($all_pages as $page) {
            $normalized_name = $this->normalizeText($page->name);
            if (strpos($normalized_name, $normalized_query) !== false) {
                $pages[] = $page;
                if (count($pages) >= 3) break;
            }
        }
        
        // Tüm sonuçları birleştir
        $results = array_merge($categories, $blogs, $pages);
        
        // Sonuç formatlandırma
        $formatted_results = [];
        $similar_results = [];
        
        if (!empty($results)) {
            // Sayfa sonuçlarını formatla
            foreach ($results as $item) {
                $icon = 'fas fa-file';
                $link = '';
                
                switch ($item->type) {
                    case 'category':
                        $icon = 'fas fa-th-large';
                        $link = base_url('admin/category') . '?edit=' . $item->id;
                        $detail = 'Kategori';
                        break;
                    case 'blog':
                        $icon = 'fas fa-rss';
                        $link = base_url('admin/blog') . '?edit=' . $item->id;
                        $detail = 'Blog';
                        break;
                    case 'page':
                        $icon = 'fas fa-file-alt';
                        $link = base_url('admin/pages') . '?edit=' . $item->id;
                        $detail = 'Sayfa';
                        break;
                }
                
                $formatted_results[] = [
                    'id' => $item->id,
                    'title' => $item->name,
                    'detail' => $detail,
                    'icon' => $icon,
                    'link' => $link,
                    'type' => $item->type
                ];
            }
        } else {
            // Sonuç bulunamadıysa benzer aramaları bul
            
            // Kısmi arama için '%' operatörünü kullan
            $query_parts = explode(' ', $normalized_query);
            $partial_query = '';
            
            if (count($query_parts) > 1) {
                // Çok kelimeli aramada her kelimeyi ayrı ayrı ara
                $partial_query = $query_parts[0];
            } else {
                // Tek kelimeli aramada kelimenin ilk 3 harfini al (en az 3 karakterli olmalı)
                if (strlen($normalized_query) >= 3) {
                    $partial_query = substr($normalized_query, 0, 3);
                } else {
                    $partial_query = $normalized_query;
                }
            }
            
            // Daha geniş bir kategori araması
            $similar_categories = [];
            foreach ($all_categories as $category) {
                $normalized_name = $this->normalizeText($category->name);
                if (strpos($normalized_name, $partial_query) !== false) {
                    $similar_categories[] = $category;
                    if (count($similar_categories) >= 2) break;
                }
            }
            
            // Daha geniş bir blog araması
            $similar_blogs = [];
            foreach ($all_blogs as $blog) {
                $normalized_name = $this->normalizeText($blog->name);
                if (strpos($normalized_name, $partial_query) !== false) {
                    $similar_blogs[] = $blog;
                    if (count($similar_blogs) >= 2) break;
                }
            }
            
            // Daha geniş bir sayfa araması
            $similar_pages = [];
            foreach ($all_pages as $page) {
                $normalized_name = $this->normalizeText($page->name);
                if (strpos($normalized_name, $partial_query) !== false) {
                    $similar_pages[] = $page;
                    if (count($similar_pages) >= 2) break;
                }
            }
            
            // Benzer sonuçları birleştir
            $similar_items = array_merge($similar_categories, $similar_blogs, $similar_pages);
            
            foreach ($similar_items as $item) {
                $icon = 'fas fa-file';
                $link = '';
                
                switch ($item->type) {
                    case 'category':
                        $icon = 'fas fa-th-large';
                        $link = base_url('admin/category') . '?edit=' . $item->id;
                        $detail = 'Kategori';
                        break;
                    case 'blog':
                        $icon = 'fas fa-rss';
                        $link = base_url('admin/blog') . '?edit=' . $item->id;
                        $detail = 'Blog';
                        break;
                    case 'page':
                        $icon = 'fas fa-file-alt';
                        $link = base_url('admin/pages') . '?edit=' . $item->id;
                        $detail = 'Sayfa';
                        break;
                }
                
                $similar_results[] = [
                    'id' => $item->id,
                    'title' => $item->name,
                    'detail' => $detail,
                    'icon' => $icon,
                    'link' => $link,
                    'type' => $item->type,
                    'is_similar' => true
                ];
            }
            
            if (empty($similar_results)) {
                exit(json_encode(["status" => false, "message" => "Hiç sonuç bulunamadı."], JSON_UNESCAPED_UNICODE));
            }
        }
        
        // Sonuçları döndür
        if (!empty($similar_results)) {
            exit(json_encode([
                "status" => true, 
                "results" => $similar_results,
                "message" => "Aradığınız kriterlere uygun sonuç bulunamadı. Bunları mı demek istediniz?"
            ], JSON_UNESCAPED_UNICODE));
        } else {
            exit(json_encode(["status" => true, "results" => $formatted_results], JSON_UNESCAPED_UNICODE));
        }
    }
    
    /**
     * Sidebar menüsündeki ve erişilebilir admin sayfalarını arama endpoint'i
     * 
     * @return void
     */
    public function searchMenu() {
        $query = $this->input->get('query');
        
        if (empty($query) || strlen($query) < 2) {
            exit(json_encode(["status" => false, "message" => "Arama sorgusu en az 2 karakter olmalıdır."], JSON_UNESCAPED_UNICODE));
        }
        
        // Kullanıcı bilgilerini al
        $user = $this->session->userdata('info');
        $user = $this->db->where("id", $user['id'])->get("user")->row();
        
        // Sidebar menü sayfaları ve admin sayfaları
        $admin_pages = $this->getAdminPages();
        
        // Yetki kontrolü
        $filtered_pages = [];
        foreach ($admin_pages as $page) {
            // Her sayfa için ilgili yetki kontrolü
            $include = true;
            
            // Ürünler sayfası
            if ($page['link'] == 'admin/products' || $page['link'] == 'admin/product/addProduct') {
                $include = (isPermFunction('seeProducts') != true) ? false : true;
            }
            
            // Stok sayfaları
            if ($page['link'] == 'admin/stock' || $page['link'] == 'admin/stock/add') {
                $include = (isPermFunction('seeStocks') != true) ? false : true;
            }
            
            // Kategoriler
            if ($page['link'] == 'admin/category') {
                $include = (isPermFunction('seeCategories') != true) ? false : true;
            }
            
            // Kuponlar
            if ($page['link'] == 'admin/coupons') {
                $include = (isPermFunction('seeCoupons') != true) ? false : true;
            }
            
            // Tedarikçiler
            if ($page['link'] == 'admin/providers') {
                $include = (isPermFunction('seeSettings') != true) ? false : true;
            }
            
            // Üyeler
            if ($page['link'] == 'admin/users') {
                $include = (isPermFunction('seeUsers') != true) ? false : true;
            }
            
            // Destek
            if ($page['link'] == 'admin/listSupports') {
                $include = (isPermFunction('seeTickets') != true) ? false : true;
            }
            
            // Loglar
            if ($page['link'] == 'admin/listLogs') {
                $include = (isPermFunction('seeLogs') != true) ? false : true;
            }
            
            // Satış geçmişi
            if ($page['link'] == 'admin/productHistory') {
                $include = (isPermFunction('seeSellHistory') != true) ? false : true;
            }
            
            // Faturalar
            if ($page['link'] == 'admin/finance/invoices') {
                $include = (isPermFunction('seePages') != true) ? false : true;
            }
            
            // Havale bildirimi
            if ($page['link'] == 'admin/bankTransfer') {
                $include = (isPermFunction('seeTransfer') != true) ? false : true;
            }
            
            // Tema
            if ($page['link'] == 'admin/themeSettings') {
                $include = (isPermFunction('seeThemeSettings') != true) ? false : true;
            }
            
            // Genel ve API ayarları
            if ($page['link'] == 'admin/publicSettings' || $page['link'] == 'admin/apiSettings') {
                $include = (isPermFunction('seeSettings') != true) ? false : true;
            }
            
            // Blog
            if ($page['link'] == 'admin/blog') {
                $include = (isPermFunction('seeBlogs') != true) ? false : true;
            }
            
            // Sayfalar
            if ($page['link'] == 'admin/pages') {
                $include = (isPermFunction('seePages') != true) ? false : true;
            }
            
            // Bildirimler
            if ($page['link'] == 'admin/Notification/notificationList') {
                $include = (isPermFunction('seeNotification') != true) ? false : true;
            }
            
            // Eğer sayfaya yetkisi varsa ekle
            if ($include) {
                $filtered_pages[] = $page;
            }
        }
        
        // Sorguya göre arama
        $menu_results = [];
        foreach ($filtered_pages as $page) {
            if (stripos($page['title'], $query) !== false || 
                stripos($page['detail'], $query) !== false) {
                $menu_results[] = [
                    'id' => md5($page['link']),
                    'title' => $page['title'],
                    'detail' => $page['detail'],
                    'icon' => $page['icon'],
                    'link' => base_url($page['link']),
                    'type' => 'admin_page'
                ];
            }
        }
        
        if (empty($menu_results)) {
            exit(json_encode(["status" => false, "message" => "Hiç sonuç bulunamadı."], JSON_UNESCAPED_UNICODE));
        }
        
        exit(json_encode(["status" => true, "results" => $menu_results], JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * Metni Türkçe karakterlerden arındırıp, büyük küçük harf duyarsız hale getirir
     *
     * @param string $text Normalize edilecek metin
     * @return string Normalize edilmiş metin
     */
    private function normalizeText($text) {
        $search = array('Ü','ü','İ','ı','Ğ','ğ','Ş','ş','Ç','ç','Ö','ö');
        $replace = array('U','u','I','i','G','g','S','s','C','c','O','o');
        $text = str_replace($search, $replace, $text);
        return mb_strtolower($text, 'UTF-8');
    }

    /**
     * searchMenu için yardımcı fonksiyon - menü öğelerini arar
     * 
     * @param string $query Arama sorgusu 
     * @return array Arama sonuçları
     */
    private function searchMenuItems($query = '') {
        try {
            if (empty($query)) {
                return [];
            }
            
            // Sorguyu normalize et
            $normalized_query = $this->normalizeText($query);
            
            // Admin sayfaları - Stok araması için özel alanlar tanımlıyoruz
            $admin_pages = $this->getAdminPages();

            // Arama sorgusuna göre filtreleme
            $results = [];
            foreach ($admin_pages as $page) {
                // Sayfa başlığı ve detayını normalize et
                $normalized_title = $this->normalizeText($page['title']);
                $normalized_detail = $this->normalizeText($page['detail']);
                
                // Normalize edilmiş metinlerde arama yap
                if (strpos($normalized_title, $normalized_query) !== false || 
                    strpos($normalized_detail, $normalized_query) !== false) {
                    $results[] = $page;
                }
            }
            
            return $results;
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Arama Endpoint'leri Bitiş

    /**
     * Tüm kaynaklarda arama yapan genel arama endpoint'i
     * 
     * @return void
     */
    public function search() {
        try {
            $query = $this->input->get('query');
            
            if (empty($query) || strlen($query) < 2) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(["status" => false, "message" => "Arama sorgusu en az 2 karakter olmalıdır."], JSON_UNESCAPED_UNICODE);
                return;
            }
            
            // Sorguyu normalize et
            $normalized_query = $this->normalizeText($query);
            
            // Sonuçları grupla
            $results = [
                'users' => [],
                'products' => [],
                'pages' => []
            ];
            
            // Kullanıcı araması
            $this->db->select('id, name, surname, email')
                ->from('user')
                ->limit(10);
            $all_users = $this->db->get()->result();
            
            $users = [];
            foreach ($all_users as $user) {
                $normalized_name = $this->normalizeText($user->name . ' ' . $user->surname);
                $normalized_email = $this->normalizeText($user->email);
                
                if (strpos($normalized_name, $normalized_query) !== false || 
                    strpos($normalized_email, $normalized_query) !== false) {
                    $users[] = $user;
                    if (count($users) >= 3) break;
                }
            }
            
            // Ürün araması
            $this->db->select('product.id, product.name, product.price, product.img, category.name as category_name')
                ->from('product')
                ->join('category', 'category.id = product.category_id', 'left')
                ->where('product.isActive', 1)
                ->limit(10);
            $all_products = $this->db->get()->result();
            
            $products = [];
            foreach ($all_products as $product) {
                $normalized_name = $this->normalizeText($product->name);
                $normalized_category = $this->normalizeText($product->category_name);
                
                if (strpos($normalized_name, $normalized_query) !== false || 
                    strpos($normalized_category, $normalized_query) !== false) {
                    $products[] = $product;
                    if (count($products) >= 3) break;
                }
            }
            
            // Menü araması
            $menu_pages = $this->searchMenuItems($query);
            
            // Kullanıcı sonuçlarını formatla
            foreach ($users as $user) {
                $results['users'][] = [
                    'id' => $user->id,
                    'title' => $user->name . ' ' . $user->surname,
                    'detail' => $user->email,
                    'icon' => 'fas fa-user',
                    'link' => base_url('admin/product/userShopHistory/') . $user->id
                ];
            }
            
            // Ürün sonuçlarını formatla
            foreach ($products as $product) {
                $results['products'][] = [
                    'id' => $product->id,
                    'title' => $product->name,
                    'detail' => 'Fiyat: ' . $product->price . '₺ | Kategori: ' . $product->category_name,
                    'icon' => 'fas fa-box',
                    'link' => base_url('admin/products') . '?product=' . $product->id,
                    'img' => !empty($product->img) ? base_url('assets/img/product/' . $product->img) : null
                ];
            }
            
            // Admin sayfalarını ekle
            foreach ($menu_pages as $page) {
                $results['pages'][] = [
                    'id' => md5($page['link']),
                    'title' => $page['title'],
                    'detail' => $page['detail'],
                    'icon' => $page['icon'],
                    'link' => base_url($page['link'])
                ];
            }
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["status" => true, "results" => $results], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(["status" => false, "message" => "Arama sırasında bir hata oluştu: " . $e->getMessage()], JSON_UNESCAPED_UNICODE);
        }
    }
}
