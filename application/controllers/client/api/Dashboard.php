<?php
require 'application/libraries/Shopier.php';
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends G_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!isset($this->session->userdata('info')['id'])) {
            flash('Ups.', 'Yetkin Olmayan Bir Yere Giriş Yapmaya Çalışıyorsun.');
            redirect(base_url(), 'refresh');
            exit;
        }

        //load library cron_service
        $this->load->library('Cron_Service');

        $properties = $this->db->where('id', 1)->get('properties')->row();
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        if ($properties->isConfirmTc == 1 && $user->tc == "11111111111") {
            flash('Eksik Bilgiler.', 'Lütfen üyeliğindeki eksik bilgileri tamamla.');
            redirect(base_url('tc-dogrulama'), 'refresh');
        }
    }

    public function index()
    {
        addlog('Ana sayfa - index', 'Sayfa ziyaret edildi: Kullanıcı paneli');
        $this->load->helper("shop");
        $paySeller = paySellersPayments();
        $uid = $this->session->userdata('info')['id'];

        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'categories' => $this->db->where('isActive', 1)->get('category')->result(),
            'products' => $this->db->where('shop.user_id', $this->session->userdata('info')['id'])->order_by('id', 'DESC')->select("shop.date, invoice.id, invoice.isActive, invoice.isComment, invoice.price, invoice.product, product.name, product.img, product.isStock, product.seller_id, invoice.last_refund, product.id AS product_id")->join('invoice', 'shop.id = shop_id', 'left')->join('product', 'product.id = product_id')->get('shop')->result(),
            'myProducts' => $this->db->where('seller_id', $this->session->userdata('info')['id'])->where('isActive !=', 0)->get('product')->result(),
            'tickets' => $this->db->where('user_id', $this->session->userdata('info')['id'])->where('seller_id', 0)->order_by('id', 'DESC')->get('ticket')->result(),

            'sellerTickets' => $this->db->where("(user_id = ".$uid." AND (seller_id != ".$uid." and seller_id != 0))")->or_where("(seller_id = ".$uid." AND user_id != ".$uid.")")->order_by('id', 'DESC')->get('ticket')->result(),

            'pages' => $this->db->get('pages')->result(),
            'banks' => $this->db->where('isActive', 1)->get('banks')->result(),
            'stocks' => $this->db->where('product.seller_id', $this->session->userdata('info')['id'])->where('product.isActive', 1)->where('stock.isActive', 1)->select('product.name, stock.*')->join('product', 'product.id = product_id', 'left')->get('stock')->result(),
            'sellProduct' => $this->db->order_by('id', 'DESC')->where('seller_id', $this->session->userdata('info')['id'])->get('invoice')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'referrer_user' => $this->db->where("user_references.buyer_id", $this->session->userdata('info')['id'])->join('user', 'user.id = user_references.referrer_id', 'left')->get("user_references")->row(),
            'references' => $this->db->where("user_references.referrer_id", $this->session->userdata('info')['id'])->join('user', 'user.id = user_references.buyer_id', 'left')->get("user_references")->result(),
            'refcode' => ($this->db->where("id", $this->session->userdata('info')['id'])->get("user")->row())->ref_code ?? false,
            'requests' =>  $this->db->where('user_id', $this->session->userdata('info')['id'])->get('request')->result()
        ];

        $this->clientView('dashboard', $data);
    }

    public function orders()
    {
        addlog('orders', 'Sayfa ziyaret edildi: Siparişlerim');
        $this->load->helper('shop_helper');
        $paySeller = paySellersPayments();
        $uid = $this->session->userdata('info')['id'];
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'categories' => $this->db->where('isActive', 1)->get('category')->result(),
            'pages' => $this->db->get('pages')->result(),
            'sellProduct' => $this->db->order_by('id', 'DESC')->where('seller_id', $this->session->userdata('info')['id'])->get('invoice')->result(),
            'stocks' => $this->db->where('product.seller_id', $this->session->userdata('info')['id'])->where('product.isActive', 1)->where('stock.isActive', 1)->select('product.name, stock.*')->join('product', 'product.id = product_id', 'left')->get('stock')->result(),
            'myProducts' => $this->db->where('seller_id', $this->session->userdata('info')['id'])->where('isActive !=', 0)->get('product')->result(),
            'tickets' => $this->db->where('user_id', $this->session->userdata('info')['id'])->where('seller_id', 0)->order_by('id', 'DESC')->get('ticket')->result(),

            'sellerTickets' => $this->db->where("(user_id = ".$uid." AND (seller_id != ".$uid." and seller_id != 0))")->or_where("(seller_id = ".$uid." AND user_id != ".$uid.")")->order_by('id', 'DESC')->get('ticket')->result(),
            'requests' =>  $this->db->where('user_id', $this->session->userdata('info')['id'])->get('request')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
        ];

        $this->clientView('orders', $data);
    }

    public function balance()
    {
        addlog('balance', 'Sayfa ziyaret edildi: Bakiye');
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'banks' => $this->db->where('isActive', 1)->get('banks')->result(),
            'mini' => 1
        ];

        $this->clientView('balance', $data);
    }

    public function reference()
    {
        addlog('reference', 'Sayfa ziyaret edildi: Referans');
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'mini' => 1,
            'referrer_user' => $this->db->where("user_references.buyer_id", $this->session->userdata('info')['id'])->join('user', 'user.id = user_references.referrer_id', 'left')->get("user_references")->row(),
            'references' => $this->db->where("user_references.referrer_id", $this->session->userdata('info')['id'])->join('user', 'user.id = user_references.buyer_id', 'left')->get("user_references")->result(),
            'refcode' => ($this->db->where("id", $this->session->userdata('info')['id'])->get("user")->row())->ref_code ?? false
        ];

        $this->clientView('reference', $data);
    }

    public function product()
    {
        addlog('product', 'Sayfa ziyaret edildi: Ürünlerim');
        $this->load->helper('shop_helper');
        $this->load->library("pagination");
        $config['uri_segment'] = 3;
        $config['per_page'] = 100;
        $config['total_rows'] = $this->db->where('shop.user_id', $this->session->userdata('info')['id'])->select("shop.date, invoice.id, invoice.isActive, invoice.isComment, invoice.price, invoice.product, product.name, product.img, product.isStock")->join('invoice', 'shop.id = shop_id', 'left')->join('product', 'product.id = product_id')->count_all_results('shop');
        $config['base_url'] = base_url('client/product');
        $config['use_page_numbers'] = TRUE;
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = 'Önceki';
        $config['prev_tag_open'] = '<li class="prev"><div class="page-link">';
        $config['prev_tag_close'] = '</div></li>';
        $config['next_link'] = 'Sonraki';
        $config['next_tag_open'] = '<li><div class="page-link">';
        $config['next_tag_close'] = '</div></li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item"><div class="page-link">';
        $config['num_tag_close'] = '</div></li>';
        $this->pagination->initialize($config);
        $data["links"] = $this->pagination->create_links();
        $sayfa = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;


        if($sayfa > 0)
        {
            $offset = ($sayfa*$config['per_page']) - $config['per_page'];
        }else
        {
            $offset = $sayfa;
        }

        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'buyProducts' => $this->db->where('shop.user_id', $this->session->userdata('info')['id'])->select("shop.date, invoice.id, invoice.isActive, invoice.isComment, invoice.price, invoice.product, product.name, product.img, product.isStock")->join('invoice', 'shop.id = shop_id', 'left')->join('product', 'product.id = product_id')->get('shop')->result(),
            'data' => $this->db->order_by('id', 'DESC')->limit($config['per_page'], $offset)->where('shop.user_id', $this->session->userdata('info')['id'])->select("shop.date, invoice.id, invoice.isActive, invoice.isComment, invoice.price, invoice.product, product.name, product.img, product.isStock, product.seller_id, invoice.last_refund")->join('invoice', 'shop.id = shop_id', 'left')->join('product', 'product.id = product_id')->get('shop')->result(),
            'mini' => 1,
            'links' => $this->pagination->create_links(),
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result()
        ];

        $this->clientView('product', $data);
    }

    public function ticket()
    {
        addlog('ticket', 'Sayfa ziyaret edildi: Destek talepleri');
        $this->load->helper("shop");
        $uid = $this->session->userdata('info')['id'];
        $tickets = $this->db->where("(user_id = ".$uid." OR seller_id = ".$uid.")")->order_by('id', 'DESC')->get('ticket')->result();

        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'tickets' => $tickets,
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'mini' => 1
        ];

        $this->clientView('ticket', $data);
    }

    public function settings()
    {
        addlog('settings', 'Sayfa ziyaret edildi: Ayarlar');
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'mini' => 1
        ];

        $this->clientView('settings', $data);
    }

    public function references()
    {
        addlog('references', 'Sayfa ziyaret edildi: Referanslarım');
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'mini' => 1
        ];

        $this->clientView('references', $data);
    }

    public function changePassword()
    {
        addlog('changePassword', 'Sayfa ziyaret edildi: Şifre değiştirme');
        $this->load->helper('helpers');
        $this->load->library('form_validation');

        $this->form_validation->set_rules("password", "Şifre", "required|trim");
        $this->form_validation->set_rules("newPassword", "Yeni Şifre", "required|trim");


        $message = [
            'required' => '<bold>{field}</bold> Alanı boş bırakılamaz.'
        ];

        $this->form_validation->set_message($message);

        if($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', validation_errors());
            redirect(base_url('client'), 'refresh');
        }else {
            $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
            if ($user->password != paspas($this->input->post('password'))) {
                flash('Hata.', 'Geçmişte kullandığın şifre doğru değil.');
                redirect(base_url('client'), 'refresh');
            }
            $this->db->where('id', $this->session->userdata('info')['id'])->update('user', ['password'=>paspas($this->input->post('newPassword'))]);
            flash('Harika.', 'Şifren başarıyla değiştirildi');
            redirect(base_url('client'), 'refresh');
        }
    }

    public function changeMail()
    {
        addlog('changeMail', 'Sayfa ziyaret edildi: Mail değiştirme');
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
            flash('Hata.', validation_errors());
            redirect(base_url('client'), 'refresh');
        }else {
            $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();

            if ($user->email != $this->input->post('email')) {
                flash('Hata.', 'Mevcut mail adresin doğru değil.');
                redirect(base_url('client'), 'refresh');
            }else{
                flash('Başarılı.', 'Mail adresini güncelledik.');
                $this->db->where('id', $this->session->userdata('info')['id'])->update('user', ['email'=>$this->input->post('newmail')]);
                redirect(base_url('client'), 'refresh');
            }
        }
    }

    public function buyOnBalance()
    {
        $this->load->model('M_Payment');
        $this->load->model('M_Earnings');
        $this->load->helper('api');

        $coupon = $this->advanced_cart->has_cart_extra("coupon_id") ? $this->advanced_cart->get_cart_extra("coupon_id")."" : null;
        $encode = json_encode($this->advanced_cart->contents(), JSON_UNESCAPED_UNICODE);
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        $cart = $this->advanced_cart->contents();
        $this->advanced_cart->destroy();
        $price = $this->M_Payment->calculate($encode);
        $properties = $this->db->where('id', 1)->get('properties')->row();
        addlog('buyOnBalance', 'Sayfa ziyaret edildi: Bakiye ile satın al. Sepet içeriği:' . $encode . 'Mevcut bakiye: '. $user->balance. ' Toplam tutar: '. $price);

        if ($user->balance < $price) {
            flash('UPS!', 'Bakiyen Yetersiz');
            redirect(base_url('client'));
            addlog('buyOnBalance', 'Bakiye yetersiz. Satın alma işlemi iptal edildi.');
            return;
        }

        $order_id = $this->M_Payment->addShop($user->id, $encode, $price, 'balance', 0, $coupon);
        $shop = $this->db->where('order_id', $order_id)->get('shop')->row();

        if (!$order_id) {
            flash('Hata', 'Sipariş oluşturulamadı. Lütfen tekrar dene.');
            redirect(base_url('client'));
            return;
        }

        $this->db->where('id', $user->id)->update('user', ['balance' => $user->balance - $price]);
        $this->M_Payment->confirmShopForCart($shop->id);

        addlog('buyOnBalance', 'Satın alım tamamlandı. Yeni bakiye: ' . ($user->balance - $price));
        flash('Başarılı', 'Satın alımın tamamlandı.');
        redirect(base_url('client'));
    }

    public function addSupport()
    {
        addlog('addSupport', 'Sayfa ziyaret edildi: Destek talebi oluştur');
        $title = $this->input->post('title');
        $message = $this->input->post('message');

        if(!empty($title) && !empty($message))
        {
            $data = [
                'title' => strip_tags($title),
                'message' => strip_tags($message),
                'date' => date('d.m.Y H:i:s'),
                'status' => 1,
                'user_id' => $this->session->userdata('info')['id']
            ];
            if (!empty($_GET['shop'])) {
                $properties = $this->db->where('id', 1)->get('properties')->row();

                if ($properties->shop_active != 1) {
                    flash('Başarısız', 'Bir Sorundan Ötürü Mesaj Gönderilemedi.');
                    return redirect(base_url(), 'refresh');
                }
                $seller = $this->db->where('shop_slug', $_GET['shop'])->get('user')->row();
                $data["seller_id"] = $seller->id;
            }else{
                $data["seller_id"] = 0;
            }

            $result = $this->db->insert('ticket', $data);

            if ($result) {
                flash('Başarılı', 'Destek Talebi Gönderildi.');
                redirect(base_url('client'));
            }else{
                flash('Başarısız', 'Bir Sorundan Ötürü Destek Talebi Gönderilemedi.');
                redirect(base_url('client'));
                exit;
            }
        }else{
            flash('Başarısız', 'Bir Sorundan Ötürü Destek Talebi Gönderilemedi.');
            redirect(base_url('client'));
            exit;
        }
    }

    public function my_subscription() {
        $this->load->model('M_Subscription');
        $this->load->library('pagination');

        addlog('my_subscription', 'Sayfa ziyaret edildi: Aboneliklerim');
        $uid = $this->session->userdata('info')['id'];

        // Sayfalama ayarları
        $config = array();
        $config['base_url'] = base_url('client/my_subscription');
        $config['total_rows'] = $this->M_Subscription->getTotalUserAchievements($uid);
        $config['per_page'] = 10; // Sayfa başına gösterilecek kayıt sayısı
        $config['uri_segment'] = 3; // URL segmenti
        $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = 'İlk';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = 'Son';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'categories' => $this->db->where('isActive', 1)->get('category')->result(),
            'pages' => $this->db->get('pages')->result(),
            'requests' =>  $this->db->where('user_id', $this->session->userdata('info')['id'])->get('request')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'my_subscriptions' => $this->M_Subscription->getUserSubscriptions($uid),
            'available_subscriptions' => $this->db->order_by('id', 'DESC')->where('isActive', 1)->get('subscriptions')->result(),
            'active_subscription' => $this->M_Subscription->getActiveSubscription($uid),
            'subscription_earnings' => $this->M_Subscription->getUserAchievements($uid, $config['per_page'], $page),
            'pagination' => $this->pagination->create_links(),
        ];

        $this->clientView('my_subscription', $data);
    }

    //cancelSubscription function
    public function cancelSubscription($user_subscription_id){
        //load model m_subscription
        $this->load->model('M_Subscription');
        //get user
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        //get userSubscription
        $subscription = $this->M_Subscription->getUserSubscription($user->id, $user_subscription_id);

        if ($subscription){
            //cancel subscription
            $this->M_Subscription->PassiveUserSubscription($user->id, $user_subscription_id);
            addlog('cancelSubscription', 'Abonelik iptal edildi. Abonelik ID: ' . $user_subscription_id . ' Kullanıcı: ' . $user->name . ' ' . $user->surname . '(' . $user->email . ')');
            flash('Başarılı', 'Abonelik iptal edildi.');
            redirect(base_url('client/my_subscription'));
        }

    }
    //buysubscription function
    public function buySubscription($id)
    {
        addLog('buySubscription', 'Satın alım işlemi başlatıldı. Abonelik ID: ' . $id);
        $this->load->model('M_Subscription');
        $this->load->model('M_Payment');
        $this->load->helper('api');

        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $subscription = $this->M_Subscription->getActiveSubscription($user->id);
        $subscriptionPlan = $this->M_Subscription->getSubscriptionById($id);

        if ($subscriptionPlan->price > $user->balance) {
            flash('UPS!', 'Bakiyen Yetersiz');
            redirect(base_url('client/my_subscription'));
            addlog('buySubscription', 'Bakiye yetersiz. Satın alma işlemi iptal edildi.');
            return;
        }
        $this->db->where('id', $user->id)->update('user', ['balance' => $user->balance - $subscriptionPlan->price]);

        // Mevcut abonelik kontrolü
        if ($subscription) {
            $start = new DateTime($subscription->start_date);
            $end = new DateTime($subscription->end_date);

            $interval = $start->diff($end);
            $durationDifference = $subscriptionPlan->duration + $interval->days;
            // Eğer Mevcut abonelik ile alınan abonelik eşitse gününe ekle
            if ($subscription->subscription_id == $subscriptionPlan->id) {
                $this->M_Subscription->updateDurationSubscription(
                    $user->id,
                    $subscription->id,
                    $subscription->subscription_id,
                    date('Y-m-d H:i:s', strtotime("+$durationDifference days")),
                    $subscription->duration + $subscriptionPlan->duration
                );
            } else {
                // Eğer mevcut abonelik ile alınan abonelik farklı ise aboneliği sonlandır.
                $this->M_Subscription->endUserSubscription($user->id);
                $this->M_Subscription->addUserSubscription(
                    $user->id,
                    $subscriptionPlan->id,
                    date('Y-m-d H:i:s'),
                    date('Y-m-d H:i:s', strtotime("+$subscriptionPlan->duration days"))
                );
            }
        } else {
            $this->M_Subscription->addUserSubscription(
                $user->id,
                $subscriptionPlan->id,
                date('Y-m-d H:i:s'),
                date('Y-m-d H:i:s', strtotime("+$subscriptionPlan->duration days"))
            );
        }

        addlog('buySubscription', 'Satın alım tamamlandı. Yeni bakiye: ' . ($user->balance - $subscriptionPlan->price));
        flash('Başarılı', 'Satın alımın tamamlandı.');
        redirect(base_url('client/my_subscription'));
    }


    //subscription detail function
    public function subscriptionDetail($subscriptionId) {
        $userSubscription = $this->db->where('id', $subscriptionId)->get('user_subscriptions')->row();
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        if ($userSubscription->user_id != $user->id) {
            echo json_encode(['error' => 'Bu abonelik size ait değil.']);
        }else{
            $this->db->select('user_subscriptions.*, subscriptions.name, subscriptions.isActive');
            $this->db->from('user_subscriptions');
            $this->db->join('subscriptions', 'user_subscriptions.subscription_id = subscriptions.id');
            $this->db->where('user_subscriptions.id', $subscriptionId);
            $query = $this->db->get();
            $result = $query->row_array();

            // Remaining süreyi hesaplama
            if (isset($result['end_date']) && $result['end_date']) {
                $end_date = date_create($result['end_date']);
                $current_date = date_create(date('Y-m-d H:i:s'));

                if ($end_date && $current_date) {
                    $diff = date_diff($current_date, $end_date);  // Tarih farkını doğru sırayla alıyoruz
                    $remaining_days = $diff->days;

                    if ($current_date > $end_date) {
                        $remaining_days = 0; // Kalan gün 0'dan küçükse 0 olarak ayarlanıyor
                        $remaining_hours = 0;
                        $remaining_minutes = 0;
                    } else {
                        $remaining_hours = $diff->h;
                        $remaining_minutes = $diff->i;
                    }

                    $result['remaining'] = $remaining_days . ' gün ' . $remaining_hours . ' saat ' . $remaining_minutes . ' dakika';
                } else {
                    $result['remaining'] = 'Tarih bilgisi geçersiz';
                }
            } else {
                $result['remaining'] = 'Tarih bilgisi bulunmuyor';
            }


            if (isset($result['start_date'])) {
                $result['start_date'] = format_date($result['start_date']);
            }

            if (isset($result['end_date'])) {
                $result['end_date'] = format_date($result['end_date']);
            }

            if (isset($result['status'])) {
                $result['status'] = $result['status'] == 'active' ? '<span class="text-success">Aktif</span>' : '<span class="text-danger">Pasif</span>';
            }

            if (isset($result['auto_renew'])) {
                $result['auto_renew'] = $result['auto_renew'] == 'active' ? '<span class="text-success">Aktif</span>' : '<span class="text-danger">Pasif</span>';
            }

            $result['user_earnings'] = round($this->db->select_sum('amount')->where('user_id', $user->id)->get('user_savings')->row()->amount, 2);

            echo json_encode($result);
        }
    }

    public function showTicket($id)
    {
        addlog('showTicket', 'Sayfa ziyaret edildi: Destek talebi görüntüle');
        $uid = $this->session->userdata('info')['id'];
        $ticket = $this->db->where("(user_id = ".$uid." OR seller_id = ".$uid.")")->where('id', $id)->get('ticket')->row();
        $properties = $this->db->where('id', 1)->get('properties')->row();
        if($ticket)
        {
            $data = [
                'ticket' => $ticket,
                'ticket_answer' => $this->db->where('ticket_id', $ticket->id)->get('ticket_answer')->result(),
                'ticket_answer_result' => $this->db->where('ticket_id', $ticket->id)->count_all_results('ticket_answer'),
                'properties' => $this->db->where('id', 1)->get('properties')->row(),
                'category' => getActiveCategories(),
                'pages' => $this->db->get('pages')->result(),
                'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
                'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
                'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
                'mini' => 1
            ];

            $this->load->view('theme/' .$properties->theme . '/includes/header', $data);
            ($properties->theme != "torius") ? $this->load->view('theme/' .$properties->theme . '/client/includes/sidebar', $data) : NULL;
            $this->load->view('theme/' .$properties->theme . '/client/show-ticket', $data);
            $this->load->view('theme/' .$properties->theme . '/includes/footer', $data);

        }else{
            flash('Ups', 'Böyle Bir Destek Talebi Bulunamadı.');
            redirect(base_url('client/ticket'));
            exit;
        }
    }

    public function answerTicket($id)
    {
        addlog('answerTicket', 'Sayfa ziyaret edildi: Destek talebine cevap ver');
        $uid = $this->session->userdata('info')['id'];
        $ticket = $this->db->where("(user_id = ".$uid." OR seller_id = ".$uid.")")->where('id', $id)->get('ticket')->row();
        $content = $this->input->post('content');

        if($ticket && !empty($content))
        {
            $data = [
                'answer' => $content,
                'date' => date('d.m.Y H:i:s'),
                'ticket_id' => $id,
                'user_id' => $this->session->userdata('info')['id']
            ];

            $result = $this->db->insert('ticket_answer', $data);
            if ($result) {
                $this->db->where('id', $id)->update('ticket', ['status'=>1]);
                flash('Başarılı', 'Destek Talebi Cevaplandı.');
                redirect(base_url('client'), 'refresh');
            }else{
                flash('Başarısız', 'Bir Sorundan Ötürü Destek Talebi Cevaplanamadı.');
                redirect(base_url('client'), 'refresh');
                exit;
            }
        }else{
            flash('Başarısız', 'Bir Sorundan Ötürü Destek Talebi Cevaplanamadı.');
            redirect(base_url('client'), 'refresh');
            exit;
        }
    }

    public function addTransfer()
    {
        addlog('addTransfer', 'Sayfa ziyaret edildi: Havale bildirimi');
        $name = $this->input->post('name');
        $date = $this->input->post('date');
        $price = $this->input->post('price');
        $bank = $this->input->post('bank');

        $data = [
            'amount' => $price,
            'date' => $date,
            'banks_id' => $bank,
            'user_id' => $this->session->userdata('info')['id']
        ];

        $result = $this->db->insert('bank_transfer', $data);

        if ($result) {
            flash('Başarılı', 'Transfer Formu Gönderildi.');
            redirect(base_url('client'), 'refresh');
        }else{
            flash('Başarısız', 'Bir Sorundan Ötürü Form Gönderilemedi.');
            redirect(base_url('client'), 'refresh');
            exit;
        }
    }

    public function addStars($invoice_id)
    {
        addlog('addStars', 'Sayfa ziyaret edildi: Puanlama');
        if(isset($invoice_id) && $this->db->where('id', $invoice_id)->get('invoice')->row() && $this->input->post('comment') && $this->input->post('stars'))
        {

            $invoice = $this->db->where('id', $invoice_id)->get('invoice')->row();

            if($invoice->isComment == 1)
            {
                $data = [
                    'comment' => $this->input->post('comment'),
                    'star' => ($this->input->post('stars') <= 5) ? $this->input->post('stars') : 5,
                    'date' => date('d.m.Y'),
                    'user_id' => $this->session->userdata('info')['id'],
                    'product_id' => $invoice->product_id
                ];

                $result = $this->db->insert('product_comments', $data);

                if ($result) {
                    $this->db->where('id', $invoice_id)->update('invoice', ['isComment' => 0]);
                    flash('Başarılı', 'Yönetici Onayladığında Yorumun Gözükecek.');
                    redirect(base_url('client'), 'refresh');
                    exit;
                }else{
                    flash('Başarısız', 'Bir Sorundan Ötürü Yorum Yapılamadı.');
                    redirect(base_url('client'), 'refresh');
                    exit;
                }

            }else{
                flash('Ups.','Bu Ürün Daha Önce Puanlanmış.');
                redirect(base_url('client'), 'refresh');
                exit;
            }

        }else{
            flash('Ups.','Gerekli Bilgiler Yok.');
            redirect(base_url('client'), 'refresh');
            exit;
        }
    }

    public function changeSettingsUser()
    {
        addlog('changeSettingsUser', 'Sayfa ziyaret edildi: Kullanıcı ayarları');
        $isMail = $this->input->post('isMail');

        ($isMail == "on") ? $isMail = 1 : $isMail = 0;

        $result = $this->db->where('id', $this->session->userdata('info')['id'])->update('user', ['isMail'=>$isMail]);
        if ($result) {
            flash('Başarılı', 'Ayarların Kaydedildi.');
            redirect(base_url('client'));
        }else{
            flash('Başarısız', 'Bir Sorundan Ötürü Ayarlar Kaydedilemedi.');
            redirect(base_url('client'));
        }
    }

    public function createRefcode()
    {
        addlog('createRefcode', 'Sayfa ziyaret edildi: Referans kodu oluşturma');
        $has_ref = ($this->db->where("id", $this->session->userdata('info')['id'])->get("user")->row())->ref_code ?? false;
        if ($has_ref) {
            flash('Başarısız', 'Zaten bir referans kodunuz var.');
            redirect(base_url('client'));
        } else {
            $this->db->where("id", $this->session->userdata('info')['id'])->update("user", ["ref_code" => randString()]);

            flash('Başarılı', 'Referans kodu oluşturuldu.');
            redirect(base_url('client'));
        }
    }

   public function getOrderDetails($orderId) {
        $invoice = $this->db->where('id', $orderId)->get('invoice')->row();

        if (empty($invoice)) {
            // Belirtilen sipariş ID'sine sahip bir fatura bulunamadı.
            echo "Sipariş bulunamadı.";
            return;
        }

        $shop = $this->db->where('id', $invoice->shop_id)->get('shop')->row();

        if (empty($shop) || $shop->user_id != $this->session->userdata('info')['id']) {
            // Mağaza bulunamadı veya oturum açmış kullanıcıya ait değil.
            $invoice->product = "Ürün Bulunamadı.";
        }

        echo $invoice->product;
    }

    public function success()
    {
        addlog('success', 'Sayfa ziyaret edildi: Ödeme başarılı');
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'why' => $this->db->get('why')->result(),
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'status' => 1
        ];
        $this->view('../../404', $data);
    }

    public function fail()
    {
        addlog('fail', 'Sayfa ziyaret edildi: Ödeme başarısız');
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'why' => $this->db->get('why')->result(),
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'status' => 2
        ];
        $this->view('../../404', $data);
    }

    public function logOut()
    {
        addlog('logOut', 'Sayfa ziyaret edildi: Çıkış yap');
        $this->session->unset_userdata('info');
        $this->session->sess_destroy();
        flash('Harika', 'Başarıyla Çıkış Yaptın.');
        redirect(base_url(), 'refresh');
    }

    public function productObjection($invoice_id) {
        $this->load->library('form_validation');

        $this->form_validation->set_rules("objection_text", "İtiraz Nedeni", "required|trim");

        $message = [
            'required' => '<bold>{field}</bold> Alanı boş bırakılamaz.'
        ];

        $this->form_validation->set_message($message);

        if($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', validation_errors());
            redirect(base_url('client'), 'refresh');
        }else {
            if($invoice = $this->db->where("invoice.id", $invoice_id)->where("shop.user_id", $this->session->userdata('info')['id'])->join('shop', 'shop.id = invoice.shop_id', 'left')->get("invoice")->row()) {
                if (strtotime($invoice->last_refund) > time()) {
                    if (!($this->db->where('invoice_id', $invoice_id)->get('product_objections')->row())) {
                        $data = [
                            'invoice_id' => $invoice_id,
                            'objection' => $this->input->post('objection_text'),
                            'status' => 2,
                            'user_id' => $this->session->userdata('info')['id']
                        ];

                        $this->db->insert('product_objections', $data);
                        flash('Harika.', 'İtirazın için artık yöneticinin incelemesi bekleniyor.');
                        redirect(base_url('client'), 'refresh');
                    } else {
                        flash('Ups.', 'Bu ürün için zaten bir itiraz yapılmış.');
                        redirect(base_url('client'), 'refresh');
                    }
                } else {
                    flash('Ups.', 'Bu ürün için itiraz süresi geçmiş.');
                    redirect(base_url('client'), 'refresh');
                }
            } else {
                flash('Ups.', 'Bir şeyler yanlış gitti.');
                redirect(base_url('client'), 'refresh');
            }
        }
    }

    public function addProduct()
    {
        addlog('addProduct', 'Sayfa ziyaret edildi: Bayi ürün oluşturma');
        $properties = $this->db->where('id', 1)->get('properties')->row();

        if ($properties->shop_active != 1) {
            flash('Hata.', 'Bir sorundan ötürü işlem tamamlanılamadı.');
            return redirect(base_url('client'), 'refresh');
        }
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();

        if ($user->type == 1) {
            flash('Ups.', 'Bunun için yetkin yok');
            redirect(base_url(), 'refresh');
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules("product_name", "Ürün Adı", "required|trim");
        $this->form_validation->set_rules("product_price", "Ürün Fiyatı", "required|trim");
        $this->form_validation->set_rules("product_desc", "Ürün Açıklaması", "required|trim");


        $message = [
            'required' => '<bold>{field}</bold> Alanı boş bırakılamaz.'
        ];

        $this->form_validation->set_message($message);

        if($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', validation_errors());
            redirect(base_url('client'), 'refresh');
        }else {

            $data = [
                'name' => $this->input->post('product_name'),
                'slug' => sefLink($this->input->post('product_name')) . "-" . sefLink($user->shop_name),
                'img' => changePhoto('assets/img/product', 'img'),
                'desc' => '<p>' . $this->input->post('product_desc') . '</p>',
                'price' => $this->input->post('product_price'),
                'isStock' => 1,
                'text' => '["","",""]',
                'isActive' => 3,
                'category_id' => $this->input->post('category_id'),
                'discount' => 0,
                'game_code' => 0,
                'product_code' => 0,
                'seller_id' => $user->id
            ];

            $this->db->insert('product', $data);
            flash('Harika.', 'Ürünün artık yönetici onayını bekliyor.');
            redirect(base_url('client'), 'refresh');

        }

    }

    public function deleteProduct($product_id)
    {
        addlog('deleteProduct', 'Sayfa ziyaret edildi: Bayi ürün silme. Ürün ID: ' . $product_id);
        $properties = $this->db->where('id', 1)->get('properties')->row();

        if ($properties->shop_active != 1) {
            flash('Hata.', 'Bir sorundan ötürü işlem tamamlanılamadı.');
            return redirect(base_url('client'), 'refresh');
        }
        if ($this->db->where('id', $product_id)->where('seller_id', $this->session->userdata('info')['id'])->get('product')->row()) {
            $this->db->where('id', $product_id)->update('product', ['isActive'=>0]);
            flash('Başarılı.', 'Ürün silindi.');
            redirect(base_url('client'), 'refresh');
        }else{
            flash('Ups.', 'Bir sorundan ötürü ürün silinemedi.');
            redirect(base_url('client'), 'refresh');
        }
    }

    public function editProduct($product_id)
    {
        addlog('editProduct', 'Sayfa ziyaret edildi: Bayi ürün düzenleme. Ürün ID: ' . $product_id);
        $properties = $this->db->where('id', 1)->get('properties')->row();

        if ($properties->shop_active != 1) {
            flash('Hata.', 'Bir sorundan ötürü işlem tamamlanılamadı.');
            return redirect(base_url('client'), 'refresh');
        }
        if ($this->db->where('id', $product_id)->where('seller_id', $this->session->userdata('info')['id'])->get('product')->row()) {
            $this->load->library('form_validation');

            $this->form_validation->set_rules("product_price", "Ürün Fiyatı", "required|trim");
            $this->form_validation->set_rules("product_desc", "Ürün Açıklaması", "required|trim");

            $message = [
                'required' => '<bold>{field}</bold> Alanı boş bırakılamaz.'
            ];

            $this->form_validation->set_message($message);

            if($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata('message', validation_errors());
                redirect(base_url('client'), 'refresh');
            } else {

                $data = [
                    'name' => $this->input->post('product_name'),
                    'desc' =>  $this->input->post('product_desc'),
                    'price' => $this->input->post('product_price'),
                    'isActive' => 3,
                    'category_id' => $this->input->post('category_id'),
                ];

                if (!empty($_FILES['img']['name'])) {
                    $data['img'] = changePhoto('assets/img/product', 'img');
                }

                $this->db->where('id', $product_id)->update('product', $data);
                flash('Harika.', 'Ürünün düzenlendi. Artık yönetici onayını bekliyor.');
                redirect(base_url('client'), 'refresh');

            }
        }else{
            flash('Ups.', 'Bir sorundan ötürü ürün düzenlenilemedi.');
            redirect(base_url('client'), 'refresh');
        }
    }

    public function appShop()
    {
        addlog('appShop', 'Sayfa ziyaret edildi: Bayi başvurusu');
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        $properties = $this->db->where('id', 1)->get('properties')->row();

        if ($properties->shop_active != 1) {
            flash('Hata.', 'Bir sorundan ötürü işlem tamamlanılamadı.');
            redirect(base_url('client'), 'refresh');
        }


        $shop_slug = sefLink($this->input->post('shopName'));

        if ($this->db->where('shop_slug', $shop_slug)->get('user')->row()) {
            flash('Hata.', 'Bu mağaza adı daha önce kullanılmış.');
            redirect(base_url('client'), 'refresh');
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules("shopName", "Mağaza Adı", "required|trim|is_unique[user.shop_name]");

        $message = [
            'required' => '<bold>{field}</bold> Alanı boş bırakılamaz.',
            'is_unique' => 'Bu mağaza adı daha önce kullanılmış.'
        ];

        $this->form_validation->set_message($message);

        if($this->form_validation->run() == FALSE) {
            flash('Hata',  validation_errors());
            redirect(base_url('client'), 'refresh');
        }else {

            $img = changePhoto('assets/img/shop', 'img');

            $this->db->where('id', $this->session->userdata('info')['id'])->update('user', [
                'shop_name' => $this->input->post('shopName'),
                'shop_slug' => $shop_slug,
                'shop_img' => $img,
                'shop_com' => $properties->shop_commission,
                'type' => 2
            ]);

            flash('Harika.', 'Her şey hazır. Artık satışa başlayabilirsin.');
            redirect(base_url('client'), 'refresh');

        }

    }

    public function addStock()
    {
        addlog('addStock', 'Sayfa ziyaret edildi: Bayi stok ekleme');
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        $properties = $this->db->where('id', 1)->get('properties')->row();

        if ($properties->shop_active != 1) {
            flash('Hata.', 'Bir sorundan ötürü işlem tamamlanılamadı.');
            return redirect(base_url('client'), 'refresh');
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules("product_id", "Ürün", "required|trim");
        $this->form_validation->set_rules("product_stock", "Stok", "required|trim");

        $message = [
            'required' => '<bold>{field}</bold> Alanı boş bırakılamaz.'
        ];

        $this->form_validation->set_message($message);

        if($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', validation_errors());
            redirect(base_url('client'), 'refresh');
        }else {
            $product = $this->db->where('id', $this->input->post('product_id'))->get('product')->row();
            if ($product && $product->seller_id == $this->session->userdata('info')['id']) {

                $data = [
                    'product' => strip_tags($this->input->post('product_stock')),
                    'checked' => 2,
                    'isActive' => 1,
                    'product_id' => $this->input->post('product_id')
                ];

                $this->db->insert('stock', $data);

                flash('Harika.', 'Stok başarıyla eklendi.');
                return redirect(base_url('client'), 'refresh');

            }else{
                flash('Ups.', 'Bir şeyler yanlış gitti.');
                return redirect(base_url('client'), 'refresh');
            }

        }

    }

    public function deleteStock($stock_id)
    {
        addlog('deleteStock', 'Sayfa ziyaret edildi: Bayi stok silme');
        $properties = $this->db->where('id', 1)->get('properties')->row();

        if ($properties->shop_active != 1) {
            flash('Hata.', 'Bir sorundan ötürü işlem tamamlanılamadı.');
            return redirect(base_url('client'), 'refresh');
        }
        $stock = $this->db->where('id', $stock_id)->get('stock')->row();
        $product = $this->db->where('id', $stock->product_id)->get('product')->row();

        if ($product && $product->seller_id == $this->session->userdata('info')['id']) {
            $result = $this->db->where('id', $stock->id)->update('stock', [
                'isActive' => 0
            ]);

            if ($result) {
                flash('Harika.', 'Stok başarıyla silindi.');
                return redirect(base_url('client'), 'refresh');
            }else{
                flash('Ups.', 'Bir şeyler yanlış gitti.');
                return redirect(base_url('client'), 'refresh');
            }
        }else{
            flash('Ups.', 'Bir şeyler yanlış gitti.');
            return redirect(base_url('client'), 'refresh');
        }
    }


    public function changeBank()
    {
        addlog('changeBank', 'Sayfa ziyaret edildi: Bayi banka ekleme');
        $properties = $this->db->where('id', 1)->get('properties')->row();

        if ($properties->shop_active != 1) {
            flash('Hata.', 'Bir sorundan ötürü işlem tamamlanılamadı.');
            return redirect(base_url('client'), 'refresh');
        }
        $this->load->library('form_validation');

        $this->form_validation->set_rules("bank_name", "Banka Adı", "required|trim");
        $this->form_validation->set_rules("bank_owner", "Hesap Sahibi", "required|trim");
        $this->form_validation->set_rules("bank_iban", "IBAN", "required|trim");

        $message = [
            'required' => '<bold>{field}</bold> Alanı boş bırakılamaz.'
        ];

        $this->form_validation->set_message($message);

        if($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('message', validation_errors());
            return redirect(base_url('client'), 'refresh');
        } else {

            $data = [
                'bank_name' => $this->input->post('bank_name'),
                'bank_owner' => $this->input->post('bank_owner'),
                'bank_iban' => $this->input->post('bank_iban'),
            ];

            $this->db->where('id', $this->session->userdata('info')['id'])->update('user', $data);
            flash('Harika.', 'Banka ayarların düzenlendi.');
            redirect(base_url('client'), 'refresh');

        }
    }

    public function newRequest($type='shop')
    {
        addlog('newRequest', 'Sayfa ziyaret edildi: Bayi para çekme isteği. Miktar:' . $this->input->post('req_amount'));
        $properties = $this->db->where('id', 1)->get('properties')->row();

        if (!in_array($type, ["shop", "streamer"])) {
            flash('Hata.', 'Bir sorundan ötürü işlem tamamlanılamadı.');
            return redirect(base_url('client'), 'refresh');
        }

        if ($type === 'shop' && $properties->shop_active != 1) {
            flash('Hata.', 'Bir sorundan ötürü işlem tamamlanılamadı.');
            return redirect(base_url('client'), 'refresh');
        }

        if ($properties->min_draw > $this->input->post('req_amount')) {
            flash('Hata.', 'Çekim miktarından daha düşük tutar talep edemezsiniz.');
            return redirect(base_url('client'), 'refresh');
        }

        $this->load->library('form_validation');

        $this->form_validation->set_rules("req_amount", "Çekilecek Tutar", "required|trim|numeric");

        $message = [
            'required' => '<bold>{field}</bold> Alanı boş bırakılamaz.',
            'decimal' => '<bold>{field}</bold> alanı geçerli bir sayı değil.'
        ];

        $this->form_validation->set_message($message);

        if($this->form_validation->run() == FALSE) {
            flash('Hata.', implode("<br>", validation_errors()));
            return redirect(base_url('client'), 'refresh');
        } else {
            $req_amount = abs(floatval($this->input->post('req_amount')));

            $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();

            if ($type === 'streamer' && $user->isStreamer != 1) {
                flash('Hata.', 'Bir sorundan ötürü işlem tamamlanılamadı.');
                return redirect(base_url('client'), 'refresh');
            }

            if ($user->balance2<$req_amount) {
                flash('Hata.', 'Bakiyenizden fazlasını çekemezsiniz!');
                redirect(base_url('client'), 'refresh');
            }

            if (empty($user->bank_name) || empty($user->bank_owner) || empty($user->bank_iban)) {
                flash('Hata.', 'Lütfen çekim talebi vermeden önce banka bilgilerinizi girin.');
                redirect(base_url('client'), 'refresh');
            }

            $data = [
                'amount' => $req_amount,
                'status' => 2,
                'user_id' => $this->session->userdata('info')['id'],
            ];

            $this->db->trans_begin();
            $this->db->where('id', $this->session->userdata('info')['id'])->update("user", [
                "balance2" => $user->balance2-$req_amount
            ]);
            $this->db->insert('request', $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                flash('Hata.', 'Çekim talebi oluşturulurken bir hata oluştu.');
                return redirect(base_url('client'), 'refresh');
            } else {
                $this->db->trans_commit();
            }


            flash('Harika.', 'Çekim talebin oluşturuldu.');
            redirect(base_url('client'), 'refresh');

        }
    }

    public function cancelRequest($id)
    {
        addlog('cancelRequest', 'Bayi para çekme isteği iptal edildi. Çekim ID:' . $id);
        $request = $this->db->where('id', $id)->get('request')->row();
        if ($request->user_id != $this->session->userdata('info')['id']) {
            flash('Ups.', 'Bir sorundan ötürü isteğini gerçekleştiremedik.');
            redirect(base_url('client'), 'refresh');
        }

        $data = [
            'status' => 3
        ];

        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        $this->db->trans_begin();
        $this->db->where('id', $id)->update("request", $data);
        $this->db->where('id', $this->session->userdata('info')['id'])->update("user", [
            "balance" => $user->balance+$request->amount
        ]);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            flash('Hata.', 'Çekim talebi iptal edilirken bir hata oluştu.');
            return redirect(base_url('client'), 'refresh');
        } else {
            $this->db->trans_commit();
        }


        flash('Harika.', 'Çekim talebin iptal edildi.');
        redirect(base_url('client'), 'refresh');
    }



    public function streamer()
    {
        $uid = $this->session->userdata('info')['id'];

        $user = $this->db->where('id', $uid)->get('user')->row();
        if (!in_array($user->isStreamer, [1,2,3])) {
            redirect(base_url('client/streamer_app'), 'refresh');
            return;
        }
		$this->load->helper("shop");
        addlog('streamer', 'Sayfa ziyaret edildi: Yayıncı Paneli');
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'url_without_https' => str_replace("http://", "", str_replace("https://", "", base_url())),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'streamerStatus' => ($user->isStreamer==2 ? "pending" : ($user->isStreamer==3 ? "rejected" : "approved")),
        ];

        $this->clientView('streamer', $data);
    }

    public function streamer_donations()
    {
        $uid = $this->session->userdata('info')['id'];

        $user = $this->db->where('id', $uid)->get('user')->row();
        if ($user->isStreamer != 1) {
            redirect(base_url('client/streamer_app'), 'refresh');
            return;
        }
        addlog('streamer', 'Sayfa ziyaret edildi: Yayıncı Paneli/Alınan Bağışlar');
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'url_without_https' => str_replace("http://", "", str_replace("https://", "", base_url())),
            'donations' => $this->db->where('streamer', $uid)->get('streamer_donations')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
        ];

        $this->clientView('streamer_donations', $data);
    }

    public function my_donations()
    {
        $uid = $this->session->userdata('info')['id'];

        $user = $this->db->where('id', $uid)->get('user')->row();
        addlog('streamer', 'Sayfa ziyaret edildi: Yayıncı Paneli/Yapılan Bağışlar');
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'url_without_https' => str_replace("http://", "", str_replace("https://", "", base_url())),
            'donations' => $this->db->select("streamer_donations.*, user.streamer_title, user.streamer_slug")->where('user', $uid)->join("user", "user.id = streamer_donations.user")->get('streamer_donations')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
        ];

        $this->clientView('my_donations', $data);
    }

    public function streamer_app($page=1)
    {
        $this->load->helper('streamer_helper');
        $uid = $this->session->userdata('info')['id'];
        $only_notify_system = false;

        $user = $this->db->where('id', $uid)->get('user')->row();
        if (in_array($user->isStreamer, [1,2,3])) {
            redirect(base_url('client/streamer'), 'refresh');
            return;
        } else {
            if ($user->streamer_stream_url != "" && $user->streamer_title != "" && $user->streamer_slug != "") {
                if ($page != 4 && $page != 5) {
                    $page = 3;
                    $only_notify_system = true;
                }
                $only_notify_system = true;
            }
        }

        if ($page == 4) {
            $post = $this->input->post();

            if (!isset($post["only_notify_system"]) && !$only_notify_system) {
                foreach($post["social"] as $key => $val) {
                    if ($val == "") unset($post["social"][$key]);
                }

                $this->db->where('id', $uid)->update('user', [
                    'streamer_social' => json_encode($post["social"]),
                    'streamer_stream_url' => $post["stream_url"],
                    'streamer_title' => $post["donate_title"],
                    'streamer_slug' => $post["donate_url"],
                    'streamer_min_donate' => $post["min_donate"]
                ]);
            }

            if (!isset($post["notify_system"])) {
                redirect(base_url('client/streamer_app/3'), 'refresh');
                return;
            }

            if ($post["notify_system"] == "streamlabs") {
                $data = streamer_create_authorize(base_url("client/streamer_app/5"));
                if ($data===false) {
                    flash('Ups.', $data["message"]);
                    redirect(base_url('client/streamer'), 'refresh');
                    return;
                }
                exit($data);
            }

            return;
        } else if ($page == 5) {
            if (isset($_GET["code"])) {
                $data = streamer_authorize(base_url("client/streamer_app/5"), $_GET["code"]);
                if ($data["success"]) {
                    $user = $data["user"];
                    $this->db->where('id', $uid)->update('user', [
                        'streamer_info' => json_encode($user),
                        'isStreamer' => 2
                    ]);
                    redirect(base_url('client/streamer'), 'refresh');
                } else {
                    flash('Ups.', $data["message"]);
                    redirect(base_url('client'), 'refresh');
                }
                return;
            }
        }
        addlog('streamer', 'Sayfa ziyaret edildi: Yayıncı Başvuru');
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'url_without_https' => str_replace("http://", "", str_replace("https://", "", base_url())),
            'page' => $page,
            'next_page' => ($page<4) ? $page+1 : 4,
            'only_notify_system' => $only_notify_system,
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
        ];

        $this->clientView('streamer_app', $data);
    }

    public function streamer_donation($streamer)
    {
        addlog('streamer_donation', 'Sayfa ziyaret edildi: Yayıncı bağış isteği. Miktar:' . $this->input->post('amount'));
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $this->load->library('form_validation');
        $this->load->helper('streamer_helper');

        $this->form_validation->set_rules("amount", "Bağış Tutarı", "required|trim|numeric");

        $message = [
            'required' => '<bold>{field}</bold> Alanı boş bırakılamaz.',
            'decimal' => '<bold>{field}</bold> alanı geçerli bir sayı değil.'
        ];

        $this->form_validation->set_message($message);

        if($this->form_validation->run() == FALSE) {
            flash('Hata.', implode("<br>", validation_errors()));
            return redirect(base_url('yayinci/'.$streamer), 'refresh');
        } else {
            $uid = $this->session->userdata('info')['id'];
            $amount = abs(floatval($this->input->post('amount')));
            $donor = $this->input->post('donor');
            $message = $this->input->post('message');
            $hide_in_screen = ($this->input->post('hide_in_screen') == "yes");

            $user = $this->db->where('id', $uid)->get('user')->row();

            if ($user->balance<$amount) {
                flash('Hata.', 'Bakiyenizden fazlasını bağışlayamazsınız!');
                redirect(base_url('yayinci/'.$streamer), 'refresh');
            }

            $streamer = $this->db->where("isStreamer", 1)->where("streamer_slug", $streamer)->order_by('id', 'DESC')->get('user')->row();

            $streamer_info = streamer_refresh_token(
                $streamer->id,
                json_decode($streamer->streamer_info, false),
                strtotime($streamer->streamer_refresh_date)
            );

            $donate_req = streamer_send_donate($streamer_info, $uid, $donor, $message, $user->email, $amount, "TRY", $hide_in_screen);
            if (!$donate_req["success"]) {
                flash('Hata.', 'Bağış gönderilirken bir hata oluştu. '.$donate_req["message"]);
                return redirect(base_url('yayinci/'.$streamer->streamer_slug), 'refresh');
            }

            $data = [
                'streamer' => $streamer->id,
                'user' => $uid,
                'donor' => $donor,
                'message' => $message,
                'amount' => $amount,
                'hide' => $hide_in_screen,
            ];

            $this->db->trans_begin();
            $this->db->where('id', $uid)->update("user", [
                "balance" => $user->balance-$amount
            ]);
            $this->db->where('id', $streamer->id)->update("user", [
                "balance2" => $streamer->balance2+$amount
            ]);

            $this->db->insert('streamer_donations', $data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                flash('Hata.', 'Bağış gönderilirken bir hata oluştu.');
                return redirect(base_url('yayinci/'.$streamer), 'refresh');
            } else {
                $this->db->trans_commit();
            }


            flash('Harika.', 'Bağış başarıyla gönderildi.');
            redirect(base_url('yayinci/'.$streamer->streamer_slug), 'refresh');

        }
    }

    public function api_settings() {
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            
            'user' => $this->db->where('id', $this->session->userdata('info')['id'])
                                 ->get('user')
                                 ->row()
        ];
    
        $this->clientView('theme/future/client/api_settings', $data);
    }
    
    public function updateApiSettings() {
        $allowed_ips = $this->input->post('allowed_ips');
        $ips = array_filter(explode("\n", str_replace("\r", "", $allowed_ips)));
        
        foreach ($ips as $ip) {
            if (!filter_var(trim($ip), FILTER_VALIDATE_IP)) {
                flash('error', 'Geçersiz IP adresi formatı: ' . $ip);
                redirect('client/api_settings');
                return;
            }
        }
    
        $this->db->where('id', $this->session->userdata('info')['id'])
                 ->update('user', ['allowed_ips' => implode(',', $ips)]);
    
        flash('success', 'API ayarları başarıyla güncellendi');
        redirect('client/api_settings');
    }
}

