<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Home extends G_Controller
{
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        addlog('Ana sayfa - index', 'Sayfa ziyaret edildi: Anasayfa');
        $this->load->helper('ai_helper');
        $this->load->helper('shop_helper');
        $this->load->helper('streamer_helper');
        $products = getAIProducts($this->db->get('home_products')->result());
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'slider' => $this->db->order_by('id', 'DESC')->get('slider')->result(),
            'lastProducts' => $this->db->order_by('id', 'DESC')->where('isActive', 1)->limit('5')->get('product')->result(),
            'home_products' => $this->db->get('home_products')->result(),
            'editor_choice' => $this->db->get('home_choice')->result(),
            'home_category' => $this->db->select('home_category.*, category.name')->join('category', 'category.id = category_id', 'left')->get('home_category')->result(),
            'products' => $products,
            'streamers' => $this->db->limit(10)->where("isStreamer", 1)->order_by('id', 'DESC')->get('user')->result(),
            'why' => $this->db->get('why')->result(),
            'pages' => $this->db->get('pages')->result(),
            'stories' => $this->db->get('story')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result()
        ];
        $this->view('index', $data);
    }
    public function marketPlace($page = 1)
    {
        addlog('Ana sayfa - marketPlace', 'Sayfa ziyaret edildi: Oyuncu Pazarı');
        $this->load->helper('ai_helper');
        $this->load->helper('shop_helper');
        $this->load->library("pagination");
        $config['base_url'] = base_url('ilan-pazari');
        $config['total_rows'] = $this->db->where('seller_id >', 0)->where('isActive', 1)->count_all_results('product'); // Ürünler tablosundaki toplam satır sayısı
        $config['per_page'] = 250; // Her sayfada gösterilecek ürün sayısı
        $config['uri_segment'] = 3; // Sayfa numarasının URI'deki segmenti
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
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'slider' => $this->db->get('slider')->result(),
            'lastProducts' => $this->db->order_by('id', 'DESC')->limit('5')->get('product')->result(),
            'products' => $this->db->where('seller_id >', 0)->where('isActive', 1)->limit($config['per_page'], ($page - 1) * $config['per_page'])->get('product')->result(),
            'why' => $this->db->get('why')->result(),
            'pages' => $this->db->get('pages')->result(),
            'stories' => $this->db->get('story')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result()
        ];
        $this->view('market-place', $data);
    }
    public function categories()
    {
        addlog('Tüm Kategoriler - categories', 'Sayfa ziyaret edildi: Tüm Kategoriler');
        $this->load->helper('ai_helper');
        $this->load->helper('shop_helper');
        $this->load->helper('streamer_helper');
        $categories = $this->db->where('isActive', 1)->get('category')->result();
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'slider' => $this->db->get('slider')->result(),
            'lastProducts' => $this->db->order_by('id', 'DESC')->limit('5')->get('product')->result(),
            'home_products' => $this->db->get('home_products')->result(),
            'editor_choice' => $this->db->get('home_choice')->result(),
            'home_category' => $this->db->select('home_category.*, category.name')->join('category', 'category.id = category_id', 'left')->get('home_category')->result(),
            'categories' => $categories,
            'streamers' => $this->db->limit(10)->where("isStreamer", 1)->order_by('id', 'DESC')->get('user')->result(),
            'why' => $this->db->get('why')->result(),
            'pages' => $this->db->get('pages')->result(),
            'stories' => $this->db->get('story')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result()
        ];
        $this->view('categories', $data);
    }
    public function getProduct($slug = NULL)
    {
        $this->load->helper('shop');
        $product = $this->db->where('slug', $slug)->where('isActive', 1)->get('product')->row();
        $properties = $this->db->where('id', 1)->get('properties')->row();
        if (!empty($product)) {
            addlog('getProduct', 'Sayfa ziyaret edildi: Ürünler - ' . $product->name);
            if (!empty($this->session->userdata('info'))) {
                $data = [
                    'user_id' => $this->session->userdata('info')['id'],
                    'category_id' => $product->category_id,
                    'product_id' => $product->id
                ];
                $this->db->insert('category_review', $data);
                $date = date('Y-m-d', strtotime('-15 days'));
                $getDate = $this->db->where('user_id', $this->session->userdata('info')['id'])->where('date <', $date)->get('category_review')->result();
                foreach ($getDate as $gd) {
                    $this->db->where('id', $gd->id)->delete('category_review');
                }
            }
            // Bu ürünü içeren paketleri getir
            $packages_containing_product = $this->db->select('p.*')
                ->from('packages p')
                ->join('package_products pp', 'pp.package_id = p.id', 'left')
                ->where('pp.product_id', $product->id)
                ->where('p.isActive', 1)
                ->group_by('p.id')
                ->order_by('p.sort_order', 'ASC')
                ->order_by('p.id', 'DESC')
                ->get()
                ->result();
            
            // Her paket için ürünleri ve toplam fiyatı hesapla
            foreach ($packages_containing_product as $package) {
                $package->products = $this->db->select('p.*, pp.quantity, pp.sort_order')
                    ->from('package_products pp')
                    ->join('product p', 'p.id = pp.product_id', 'left')
                    ->where('pp.package_id', $package->id)
                    ->where('p.isActive', 1)
                    ->order_by('pp.sort_order', 'ASC')
                    ->get()
                    ->result();
                
                // Toplam fiyat hesapla
                $total_price = 0;
                foreach ($package->products as $pp) {
                    $total_price += $pp->price;
                }
                $package->total_price = $total_price;
            }
            
            $data = [
                'properties' => $this->db->where('id', 1)->get('properties')->row(),
                'category' => getActiveCategories(),
                'product' => $product,
                'stock' => $this->db->where('isActive', 1)->where('product_id', $product->id)->count_all_results('stock'),
                'comments' => $this->db->where('product_comments.product_id', $product->id)->where('product_comments.isActive', 1)->order_by('product_comments.id', 'DESC')->select('product_comments.*, user.name, user.surname')->join('user', 'user.id = user_id', 'left')->get('product_comments')->result(),
                'history' => $this->db->where('product_comments.product_id', $product->id)->where('product_comments.isActive', 1)->order_by('product_comments.id', 'DESC')->select('product_comments.*, user.name, user.surname')->join('user', 'user.id = user_id', 'left')->get('product_comments')->result(),
                'why' => $this->db->get('why')->result(),
                'pages' => $this->db->get('pages')->result(),
                'title' => $product->name . ' - ' . $properties->name,
                'likeProducts' => $this->db->limit(4)->where('category_id', $product->category_id)->where('isActive', 1)->get('product')->result(),
                'packages' => $packages_containing_product, // Paket önerileri
                'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
                'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
                'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result()
            ];
            $this->view('product', $data);
        } else {
            addlog('getProduct', 'Sayfa ziyaret edildi: Ürün bulunamadı - ' . $slug);
            //flash('Ups.', 'Aradık Taradık Ancak Ürünü Bulamadık');
            redirect(base_url());
        }
    }
    public function addToCartItem()
    {
        if (abs($this->input->post('amount')) < 1) {
            addlog('addToCartItem', 'Sepete eklenecek ürün adeti yetersiz: ' . $this->input->post('amount'));
            echo "En düşük 1 adet ürün alınabilir.";
            exit;
        }
        $extras = [];
        if (!empty($_POST['extras']['name1'])) {
            $name1 = $_POST['extras']['name1'];
            $number1 = $_POST['extras']['number1'];
            $extras[$name1] = $number1;
        }
        if (!empty($_POST['extras']['name2'])) {
            $name2 = $_POST['extras']['name2'];
            $number2 = $_POST['extras']['number2'];
            $extras[$name2] = $number2;
        }
        if (!empty($_POST['extras']['name3'])) {
            $name3 = $_POST['extras']['name3'];
            $number3 = $_POST['extras']['number3'];
            $extras[$name3] = $number3;
        }
        $id = $this->input->post('id');
        if ($this->db->where('isActive', 1)->where('id', $id)->get('product')->row()) {
            $product = $this->db->where('isActive', 1)->where('id', $id)->get('product')->row();
            $properties = $this->db->where("id", 1)->get('properties')->row();

            // Kendine ait ürünü satın almayı engelle
            if (!empty($this->session->userdata('info'))) {
                if ($product->seller_id == $this->session->userdata('info')['id']) {
                    $this->load->view('theme/' . $properties->theme . '/cartView');
                    echo '<div class="toast fade show" data-delay="100">
						<div class="toast-header">
							<strong class="mr-auto"><i class="fa fa-globe"></i> ' . "Hata!" . '</strong>
							<small class="text-muted">1 Saniye Önce</small>
							<button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
						</div>
						<div class="toast-body" style="color: #6c757d;">
							' . "Kendine ait bir ürünü satın alamazsın." . '
						</div>
						<div style="height: 2px; background-color: blue; width: 100%; transition: all 3s ease 0s;" id="timer"></div>
					</div>
					<script>
						toastTimer("timer", 4);
						function toastTimer(toastId, time=10) {
							setTimeout(function() {
								if (time==-2) {
									document.getElementById(toastId).parentElement.hidden = true
								} else {
									var width = document.getElementById(toastId).style.width.replace("%", ""); 
									document.getElementById(toastId).style.width = width-(170/time)+"%";
									//console.log("toast", time)
									toastTimer(toastId, time-1);
								}
							}, 1000)
						}
					</script>';
                    return;
                }
            }

            // Bayilik indirimini calculatePrice ile hesapla
            $user_id = !empty($this->session->userdata('info')) ? $this->session->userdata('info')['id'] : 0;
            $qty = abs($this->input->post('amount'));
            $priceData = json_decode(calculatePrice($product->id, $qty), true);
            $price = $priceData['price'] / $qty; // Birim fiyatı almak için toplam fiyatı miktara böl

$data = [
    'id' => $product->id,
    'product_id' => $product->id,
    'qty' => $qty,
    'price' => $price,
    'name' => 'product_' . $product->id,
    'extras' => $extras
];


$this->advanced_cart->insert($data);
$cart_contents = $this->advanced_cart->contents();
addlog('addToCartItem_DEBUG', 'Cart contents: ' . json_encode($cart_contents));
addlog('addToCartItem', 'Sepete ürün eklendi: ' . $product->name);

            addlog('addToCartItem', 'Sepete ürün eklendi: ' . $product->name);
            header('Content-Type: application/json');  // BU SATIRI EKLE
echo json_encode(['status' => 'success']);
exit;
        } else {
            addlog('addToCartItem', 'Sepete eklenecek ürün bulunamadı: ' . $id);
            echo "Ürün Bulunamadı.";
        }
    }
    public function cart()
    {
        addlog('cart', 'Sayfa ziyaret edildi: Sepet');
        $this->load->helper('form');
        $cart = $this->advanced_cart->contents();
        foreach ($cart as $item) {
            $product = $this->db->where('id', $item['product_id'])->get('product')->row();
            $is_admin_product = false;
            if ($product->isStock == 1) {
                if ($product->seller_id == 0) {
                    $is_admin_product = true;
                } else {
                    $seller = $this->db->where('id', $product->seller_id)->get('user')->row();
                    if ($seller->isAdmin == 1) {
                        $is_admin_product = true;
                    }
                }
            } else {
                $is_admin_product = true;
                if ($product->seller_id != 0) {
                    $seller = $this->db->where('id', $product->seller_id)->get('user')->row();
                    if ($seller->isAdmin != 1) {
                        $this->advanced_cart->remove($item["rowid"]);
                        flash("Ups.", "Sepetinizdeki ürün sistemsel bir sorun nedeniyle kaldırıldı.");
                    }
                }
            }
            if (!$is_admin_product) {
                $stockCount = $this->db->where('product_id', $item['product_id'])->where('isActive', 1)->count_all_results('stock');
                if ($stockCount < $item['qty']) {
                    $this->advanced_cart->remove($item["rowid"]);
                    flash("Ups.", "Sepetindeki ürün satıcının stoğunda kalmamış.");
                }
            }
        }
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'pages' => $this->db->get('pages')->result(),
            'category' => getActiveCategories(),
            'why' => $this->db->get('why')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
        ];
        $this->view('cart', $data);
    }
    public function category($slug)
    {
        $result = $this->db->where('slug', $slug)->where('isActive', 1)->get('category')->row();
        $properties = $this->db->where('id', 1)->get('properties')->row();
        if ($result) {
            addlog('category', 'Sayfa ziyaret edildi: Kategori - ' . $result->name);
            $data = [];
            $this->load->library("pagination");
            $config['uri_segment'] = 3;
            $config['per_page'] = 250;
            $config['total_rows'] = $this->db->where('category_id', $result->id)->where('isActive', 1)->count_all_results('product');
            $config['base_url'] = base_url('kategori/') . $slug;
            $config['use_page_numbers'] = TRUE;
            $config['full_tag_open'] = '<ul class="pagination justify-content-end">';
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
            if ($sayfa > 0) {
                $offset = ($sayfa * $config['per_page']) - $config['per_page'];
            } else {
                $offset = $sayfa;
            }
            $data = [
                'properties' => $this->db->where('id', 1)->get('properties')->row(),
                'category' => getActiveCategories(),
                'categories' => $result,
                'subCategories' => $this->db->where('isActive', 1)->where('mother_category_id', $result->id)->get('category')->result(),
                'pages' => $this->db->get('pages')->result(),
                'products' => $this->db->order_by('rank', 'ASC')->order_by('id', 'DESC')->where('category_id', $result->id)->where('isActive', 1)->limit($config['per_page'], $offset)->get('product')->result(),
                'title' => $result->name . ' - ' . $properties->name,
                'why' => $this->db->get('why')->result(),
                'links' => $this->pagination->create_links(),
                'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
                'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
                'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result()
            ];
            $this->view('products', $data);
        } else {
            addlog('category', 'Kategori bulunamadı - ' . $slug);
            flash('Ups.', 'Kategori Bulunamadı');
            redirect(base_url());
        }
    }
    public function blogs()
    {
        addlog('blogs', 'Sayfa ziyaret edildi: Makale Listesi');
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $data = [];
        $this->load->library("pagination");
        $config['uri_segment'] = 2;
        $config['per_page'] = 10;
        $config['total_rows'] = $this->db->count_all_results('blog');
        $config['base_url'] = base_url('makale-listesi');
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
        $sayfa = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        if ($sayfa > 0) {
            $offset = ($sayfa * $config['per_page']) - $config['per_page'];
        } else {
            $offset = $sayfa;
        }
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'pages' => $this->db->get('pages')->result(),
            'category' => getActiveCategories(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'links' => $this->pagination->create_links(),
            'title' => 'BLOG - ' . $properties->name,
            'blogs' => $this->db->order_by('id', 'DESC')->limit($config['per_page'], $offset)->get('blog')->result()
        ];
        $this->view('blogs', $data);
    }
    public function blog($slug)
    {
        if (!empty($slug)) {
            $blog = $this->db->where('slug', $slug)->get('blog')->row();
            if ($blog) {
                addlog('blog', 'Makale ziyaret edildi - ' . $blog->title);
                $properties = $this->db->where('id', 1)->get('properties')->row();
                $data = [
                    'properties' => $this->db->where('id', 1)->get('properties')->row(),
                    'category' => getActiveCategories(),
                    'blog' => $blog,
                    'pages' => $this->db->get('pages')->result(),
                    'blogs' => $this->db->order_by('id', 'DESC')->limit(5)->get('blog')->result(),
                    'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
                    'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
                    'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
                    'title' => $blog->title . ' - ' . $properties->name
                ];
                $this->view('blog', $data);
            } else {
                addlog('blog', 'Makale bulunamadı- ' . $slug);
                flash('Ups.', 'Aradığın yazıyı bulamadık.');
                redirect(base_url(), 'refresh');
            }
        } else {
            addlog('blog', 'Makale bulunamadı- ' . $slug);
            flash('Ups.', 'Aradığın Yazıyı Bulamadık');
            redirect(base_url(), 'refresh');
        }
    }
    public function page($slug)
    {
        if (!empty($slug)) {
            $page = $this->db->where('slug', $slug)->get('pages')->row();
            if ($page) {
                addlog('page', 'Sayfa ziyaret edildi - ' . $page->title);
                $properties = $this->db->where('id', 1)->get('properties')->row();
                $data = [
                    'properties' => $this->db->where('id', 1)->get('properties')->row(),
                    'category' => getActiveCategories(),
                    'pages' => $this->db->get('pages')->result(),
                    'page' => $page,
                    'meta' => $this->db->where('id', $page->id)->get('pages')->row(),
                    'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
                    'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
                    'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
                ];
                $this->view('page', $data);
            } else {
                addlog('page', 'Sayfa bulunamadı - ' . $slug);
                flash('Ups.', 'Aradığın sayfayı bulamadık.');
                redirect(base_url(), 'refresh');
            }
        } else {
            addlog('page', 'Sayfa bulunamadı - ' . $slug);
            flash('Ups.', 'Aradığın Sayfayı Bulamadık');
            redirect(base_url(), 'refresh');
        }
    }
    public function shop($slug)
    {
        $this->load->helper('shop_helper');
        $properties = $this->db->where('id', 1)->get('properties')->row();
        if ($properties->shop_active != 1) {
            return redirect(base_url(), 'refresh');
        }
        $seller = $this->db->where('shop_slug', $slug)->get('user')->row();
        if ($seller && $seller->isActive == 1) {
            addlog('shop', 'Satıcı ziyaret edildi - ' . $seller->shop_name);
            $data = [
                'properties' => $this->db->where('id', 1)->get('properties')->row(),
                'category' => getActiveCategories(),
                'pages' => $this->db->get('pages')->result(),
                'seller' => $seller,
                'why' => $this->db->get('why')->result(),
                'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
                'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
                'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            ];
            $this->view('shop', $data);
        } else {
            addlog('shop', 'Satıcı bulunamadı - ' . $slug);
            flash('Ups.', 'Aradığın satıcı yok veya profili kısıtlanmış.');
            redirect(base_url(), 'refresh');
        }
    }
    public function streamer($streamer)
    {
        addlog('streamer', 'Sayfa ziyaret edildi - Yayıncı');
        $this->load->helper('streamer_helper');
        $properties = $this->db->where('id', 1)->get('properties')->row();
        //$streamer = $this->db->where("isStreamer", 1)->where("JSON_EXTRACT(`streamer_info` -> '$.streamlabs', '$.username') = '$streamer'", null, FALSE)->order_by('id', 'DESC')->get('user')->row();
        $streamer = $this->db->where("isStreamer", 1)->where("streamer_slug", $streamer)->order_by('id', 'DESC')->get('user')->row();
        if (!$streamer)
            return redirect(base_url("yayincilar"), 'refresh');
        $streamer->streamer_info = streamer_refresh_token(
            $streamer->id,
            json_decode($streamer->streamer_info, false),
            strtotime($streamer->streamer_refresh_date)
        );
        $streamer->streamer_social = @json_decode($streamer->streamer_social, false);
        if ($streamer->streamer_social === null)
            $streamer->streamer_social = [];
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'pages' => $this->db->get('pages')->result(),
            'streamer' => $streamer,
            'streamers' => $this->db->limit(12)->where("isStreamer", 1)->where("id !=", $streamer->id)->order_by('id', 'DESC')->get('user')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'title' => 'Yayıncı'
        ];
        $this->view('streamer', $data);
    }
    public function streamers()
    {
        addlog('streamer', 'Sayfa ziyaret edildi - Yayıncı');
        $this->load->helper('streamer_helper');
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'pages' => $this->db->get('pages')->result(),
            'streamers' => $this->db->where("isStreamer", 1)->order_by('id', 'DESC')->get('user')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'title' => 'Yayıncı'
        ];
        $this->view('streamers', $data);
    }
    public function getSearchFormProducts()
    {
        $words = $this->input->post('words');
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $result = $this->db->where('isActive', 1)->limit(6)->like('name', $words, 'both')->get('product')->result();
        $data = [
            'result' => $result
        ];
        $this->load->view('theme/' . $properties->theme . '/searchForm', $data);
    }
    public function removeCart($rowid)
    {
        addlog('remoevCart', 'Sepetten ürün silindi - ' . $rowid);
        $this->advanced_cart->remove($rowid);
        flash('Başarılı.', 'Ürün sepetten silindi.');
        redirect(base_url('sepet'));
    }
    public function reNewPassword()
    {
        if ($this->input->post()) {
            // Mail adresi kontrolü
            $login = $this->db->where('email', $this->input->post('email'))->get('user')->row();

            if (empty($login)) {
                addlog('reNewPassword', 'Şifre sıfırlama isteği gönderildi. Mail - ' . $this->input->post('email'));
                flash('İşlem Başarılı', "Mail adresiniz kayıtlarımızla eşleşirse size şifre sıfırlama bağlantısı göndereceğiz.");
                redirect(base_url('reNewPassword'), 'refresh');
            }

            // Benzersiz hash oluştur
            $hash = randString(25) . date('d');
            while ($this->db->where('paspas', $hash)->get('user')->num_rows() > 0) {
                $hash = rand(1, 999) . $hash;
            }

            // Hash'i kaydet
            $this->db->where('id', $login->id)->update('user', ['paspas' => $hash]);

            // Mail gönder
            $this->load->library('mailer');
            $properties = $this->db->where('id', 1)->get('properties')->row();

            $result = $this->mailer->send(
                $this->input->post('email'),
                'password_reset',
                [
                    'name' => $login->name,
                    'surname' => $login->surname,
                    'email' => $login->email,
                    'reset_link' => base_url("newPassword/" . $hash)
                ]
            );

            if ($result) {
                flash('İşlem Başarılı', "Mail adresiniz kayıtlarımızla eşleşirse size şifre sıfırlama bağlantısı göndereceğiz.");
            } else {
                flash('Ups.', "Sistemde oluşan bir sorundan dolayı işleminizi gerçekleştiremiyoruz. Lütfen yöneticiye bildiriniz.");
            }

            redirect(base_url('reNewPassword'), 'refresh');

        } else {
            $properties = $this->db->where('id', 1)->get('properties')->row();
            $data = [
                'properties' => $properties,
                'pages' => $this->db->get('pages')->result(),
                'category' => getActiveCategories(),
                'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
                'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
                'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
                'title' => 'Şifremi Unuttum - ' . $properties->name
            ];
            $this->view('reNewPassword', $data);
        }
    }

    public function newPassword($hash)
    {
        $controlhash = $this->db->where('paspas', $hash)->get('user')->row();
        $properties = $this->db->where('id', 1)->get('properties')->row();
        if ($controlhash) {
            addlog('newPassword', 'Sayfa ziyaret edildi: Yeni şifre belirleme. HASH - ' . $hash);
            $data = [
                'properties' => $this->db->where('id', 1)->get('properties')->row(),
                'category' => getActiveCategories(),
                'pages' => $this->db->get('pages')->result(),
                'hash' => $hash,
                'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
                'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
                'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
                'title' => 'Yeni Şifre - ' . $properties->name
            ];
            $this->view('new-password', $data);
        } else {
            addlog('newPassword', 'Şifre sıfırlama HASH hatası. HASH - ' . $hash);
            flash('UPS', 'Yanlış veya tarihi geçmiş kod.');
            redirect(base_url());
        }
    }
    public function setNewPassword()
    {
        if ($this->input->post('hash')) {
            $hash = $this->db->where('paspas', $this->input->post('hash'))->get('user')->row();
            if ($hash) {
                $newPassword = $this->input->post('newPassword');
                $reNewPassword = $this->input->post('reNewPassword');
                if ($newPassword == $reNewPassword) {
                    $result = $this->db->where('id', $hash->id)->update('user', ['password' => paspas($newPassword), 'paspas' => NULL]);
                    if ($result) {
                        addlog('newPassword', 'Şifre sıfırlama gerçekleştirildi. Kullanıcı:' . $hash->email);
                        flash('Harika!', 'Yeni şifrenle giriş yapabilirsin.');
                        redirect(base_url());
                    } else {
                        addlog('newPassword', 'Şifre sıfırlama gerçekleştirilemedi (Hash hatası). HASH:' . $hash);
                        flash('UPS', 'Bir sorundan ötürü şifreni yenileyemedik.');
                        redirect(base_url());
                    }
                } else {
                    addlog('newPassword', 'Şifre sıfırlama gerçekleştirilemedi (Şifre eşleşme hatası). HASH - ' . $hash);
                    flash('UPS', 'Şifreler birbiri ile uyuşmuyor.');
                    redirect(base_url('home/newPassword/') . $this->input->post('hash'));
                }
            } else {
                addlog('newPassword', 'Şifre sıfırlama gerçekleştirilemedi (Hash hatası). HASH - ' . $hash);
                flash('UPS', 'Yanlış veya tarihi geçmiş kod.');
                redirect(base_url());
            }
        } else {
            addlog('newPassword', 'Şifre sıfırlama gerçekleştirilemedi (Hash hatası). HASH - ' . $hash);
            flash('UPS', 'Yanlış veya tarihi geçmiş kod.');
            redirect(base_url());
        }
    }
    public function sitemap()
    {
        $this->load->view('Sitemap');
    }
    public function raffles()
    {
        $categories = $this->db->where('isActive', 1)->get('category')->result();
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'slider' => $this->db->get('slider')->result(),
            'lastProducts' => $this->db->order_by('id', 'DESC')->limit('5')->get('product')->result(),
            'home_products' => $this->db->get('home_products')->result(),
            'editor_choice' => $this->db->get('home_choice')->result(),
            'home_category' => $this->db->select('home_category.*, category.name')->join('category', 'category.id = category_id', 'left')->get('home_category')->result(),
            'categories' => $categories,
            'streamers' => $this->db->limit(10)->where("isStreamer", 1)->order_by('id', 'DESC')->get('user')->result(),
            'why' => $this->db->get('why')->result(),
            'pages' => $this->db->get('pages')->result(),
            'stories' => $this->db->get('story')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result()
        ];
        $this->view('raffles', $data);
    }
    public function error()
    {
        addlog('error', 'Sayfa ziyaret edildi: Hata Sayfası');
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'why' => $this->db->get('why')->result(),
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'status' => 0
        ];
        $this->view('../../404', $data);
    }
    public function confirmUserMail($mail_code)
    {
        $user = $this->db->where('mail_code', $mail_code)->get('user')->row();
        if ($user) {
            addlog('confirmUserMail', 'Sayfa ziyaret edildi. Mail doğrulama - ' . $user->email);
            $result = $this->db->where('id', $user->id)->update('user', ['isConfirmMail' => 1, 'mail_code' => '']);
            if ($result) {
                addlog('confirmUserMail', 'Mail doğrulama başarılı. Mail - ' . $user->email);
                flash('Harika!', 'Hesabın onaylandı. Artık giriş yapabilirsin.');
                redirect(base_url('hesap'));
                exit;
            } else {
                addlog('confirmUserMail', 'Mail doğrulama başarısız. Doğrulama kodu: ' . $mail_code);
                flash('UPS', 'Bir sorun oluştu. Lütfen sistem yöneticisi ile iletişime geç.');
                redirect(base_url());
                exit;
            }
        } else {
            $data = [
                'properties' => $this->db->where('id', 1)->get('properties')->row(),
                'category' => getActiveCategories(),
                'why' => $this->db->get('why')->result(),
                'pages' => $this->db->get('pages')->result(),
                'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
                'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
                'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
                'status' => 0
            ];
            $this->view('../../404', $data);
        }
    }
    public function newMailCode()
    {
        addlog('newMailCode', 'Sayfa ziyaret edildi: Mail doğrulama kodu alma sayfası');
        if (!empty($this->session->userdata('newmailcode')) && $this->session->userdata('newmailcode')['newCode'] == 1) {
            $user = $this->db->where('id', $this->session->userdata('newmailcode')['id'])->get('user')->row();
            $sendMailDate = strtotime($user->send_mail_date) + 600;
            if ($sendMailDate < time()) {
                $randString = randString(25);
                $randString = md5($this->input->post('name') . $randString);

                // Mail gönder
                $this->load->library('mailer');
                $properties = $this->db->where('id', 1)->get('properties')->row();

                $result = $this->mailer->send(
                    $user->email,
                    'mail_verification',
                    [
                        'name' => $user->name,
                        'surname' => $user->surname,
                        'verification_link' => base_url('mail-onay/') . $randString
                    ]
                );

                $this->db->where('id', $this->session->userdata('newmailcode')['id'])->update('user', ['mail_code' => $randString, 'send_mail_date' => date('Y-m-d h:i:s')]);
                addlog('newMailCode', 'Mail doğrulama kodu gönderildi. Kod: ' . $randString);
                flash('İşlem Başarılı.', 'Mail adresine yeni bir kod gönderdik.');
                $this->session->unset_userdata('newmailcode');
                redirect(base_url('hesap'), 'refresh');
                exit;
            } else {
                addlog('newMailCode', 'Mail doğrulama kodu gönderilemedi (10 Dakikadan daha önce talep edildi).');
                flash('Ups.', '10 Dakikada sadece 1 kez kod gönderebilirsin.');
                redirect(base_url('hesap'), 'refresh');
                exit;
            }
        }
    }
    public function addTc()
    {
        addlog('addTc', 'Sayfa ziyaret edildi. TC Ekleme.');
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        if ($properties->isConfirmTc == 0 && $user->tc != "11111111111") {
            flash('Ups.', 'Buraya giremezsin.');
            redirect(base_url(), 'refresh');
        }
        $this->load->library('form_validation');
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
        ];
        $this->view('../../tc', $data);
    }
    public function draws()
    {
        $this->load->model('M_Draw');
        $draws = $this->M_Draw->get_all_draws(true); // Tüm çekilişler (aktif ve biten)
        $user_id = $this->session->userdata('info')['id'] ?? null;
        $active_draws = [];
        $finished_draws = [];
        foreach ($draws as &$draw) {
            $draw->participant_count = $this->M_Draw->get_participant_count($draw->id);
            $draw->rewards = $this->M_Draw->get_rewards($draw->id); // Sadece bakiye
            $draw->is_joined = $user_id ? $this->M_Draw->is_user_joined($draw->id, $user_id) : false;
            $draw->masked_participants = $this->M_Draw->get_draw_participant_names($draw->id);
            // Kazananlar
            $draw->winners = [];
            if ($draw->status == 2) {
                $winner_rows = $this->db->where('draw_id', $draw->id)->get('draw_winners')->result();
                foreach ($winner_rows as $winner_row) {
                    $participant = $this->db->where('id', $winner_row->participant_id)->get('draw_participants')->row();
                    $user = $participant ? $this->db->where('id', $participant->user_id)->get('user')->row() : null;
                    $reward = $this->db->where('id', $winner_row->reward_id)->get('draw_rewards')->row();
                    $masked_name = $user ? mb_substr($user->name, 0, 1, 'UTF-8') . str_repeat('*', max(0, mb_strlen($user->name, 'UTF-8') - 1)) : '';
                    $is_me = $user_id && $user && $user->id == $user_id;
                    $draw->winners[] = (object) [
                        'masked_name' => $masked_name,
                        'reward_type' => 'bakiye',
                        'reward_amount' => $reward && $reward->type == 'bakiye' ? $reward->amount : null,
                        'is_me' => $is_me
                    ];
                }
            }
            if ($draw->status == 1)
                $active_draws[] = $draw;
            else
                $finished_draws[] = $draw;
        }
        $data['active_draws'] = $active_draws;
        $data['finished_draws'] = $finished_draws;
        $this->loadCommonData($data);
        $this->load->view('theme/future/draws', $data);
    }
    // Ortak verileri yükleyen fonksiyon (base controller'da olabilir, burada örnek)
    private function loadCommonData(&$data)
    {
        $data['properties'] = $this->db->where('id', 1)->get('properties')->row();
        $data['category'] = function_exists('getActiveCategories') ? getActiveCategories() : [];
        $data['footerPage'] = $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result();
        $data['footerProduct'] = $this->db->limit(18)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result();
        $data['footerBlog'] = $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result();
    }
    // Çekilişe katılım fonksiyonu
    public function joinDraw($draw_id)
    {
        if (!$this->session->userdata('info')) {
            flash('Giriş Yapmalısınız', 'Çekilişe katılmak için giriş yapmalısınız.');
            redirect(base_url('cekilisler'));
        }
        $this->load->model('M_Draw');
        $user_id = $this->session->userdata('info')['id'];
        $result = $this->M_Draw->join_draw($draw_id, $user_id);
        if ($result['success']) {
            flash('Başarılı', $result['message']);
        } else {
            flash('Ups', $result['message']);
        }
        redirect(base_url('cekilisler'));
    }
    // Çekiliş detay fonksiyonu
    public function drawDetail($draw_id)
    {
        $this->load->model('M_Draw');
        $draw = $this->M_Draw->get_draw($draw_id);
        if (!$draw) {
            flash('Ups', 'Çekiliş bulunamadı.');
            redirect(base_url('cekilisler'));
        }
        $draw->participant_count = $this->M_Draw->get_participant_count($draw->id);
        $draw->rewards = $this->M_Draw->get_rewards($draw->id);
        $draw->is_joined = false;
        if ($this->session->userdata('info')) {
            $draw->is_joined = $this->M_Draw->is_user_joined($draw->id, $this->session->userdata('info')['id']);
        }
        $data['draw'] = $draw;
        $this->loadCommonData($data);
        $this->load->view('theme/future/draw_detail', $data);
    }
    public function cekilis_kazanclari()
    {
        if (!isset($_SESSION['info']['id'])) {
            redirect(base_url('login'));
            return;
        }
        $this->load->model('M_Draw');
        $user_id = $_SESSION['info']['id'];
        $won_rewards = $this->M_Draw->get_user_rewards($user_id); // Sadece bakiye
        $joined_draws = $this->M_Draw->get_user_joined_draws($user_id);
        $data = [
            'won_rewards' => $won_rewards,
            'joined_draws' => $joined_draws,
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'category' => getActiveCategories(),
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result(),
            'status' => 'cekilis-kazanclari'
        ];
        $this->load->view('theme/future/draw-user-rewards-public', $data);
    }

    /**
     * Paketler listesi sayfası
     */
    public function packages($sort = 'newest')
    {
        addlog('Paketler - packages', 'Sayfa ziyaret edildi: Paketler Listesi');
        $this->load->helper('ai_helper');
        $this->load->helper('shop_helper');
        $this->load->helper('streamer_helper');
        
        $properties = $this->db->where('id', 1)->get('properties')->row();
        
        // Sıralama seçeneği
        $sort_option = $this->input->get('sort') ?: $sort;
        
        // Paketleri getir (aktif olanlar)
        $this->db->where('isActive', 1);
        
        // Sıralama
        switch ($sort_option) {
            case 'price_low':
                $this->db->order_by('price', 'ASC');
                break;
            case 'price_high':
                $this->db->order_by('price', 'DESC');
                break;
            case 'newest':
            default:
                $this->db->order_by('sort_order', 'ASC');
                $this->db->order_by('id', 'DESC');
                break;
        }
        
        $packages = $this->db->get('packages')->result();
        
        // Her paket için ürünleri getir
        foreach ($packages as $package) {
            $package->products = $this->db->select('p.*')
                ->from('package_products pp')
                ->join('product p', 'p.id = pp.product_id', 'left')
                ->where('pp.package_id', $package->id)
                ->where('p.isActive', 1)
                ->order_by('pp.sort_order', 'ASC')
                ->get()
                ->result();
        }
        
        $data = [
            'properties' => $properties,
            'category' => getActiveCategories(),
            'packages' => $packages,
            'sort_option' => $sort_option,
            'why' => $this->db->get('why')->result(),
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result()
        ];
        
        $this->view('packages', $data);
    }

    /**
     * Paket detay sayfası
     */
    public function package($slug)
    {
        addlog('Paket Detay - package', 'Sayfa ziyaret edildi: Paket - ' . $slug);
        $this->load->helper('ai_helper');
        $this->load->helper('shop_helper');
        
        $properties = $this->db->where('id', 1)->get('properties')->row();
        
        // Paketi getir
        $package = $this->db->where('slug', $slug)->where('isActive', 1)->get('packages')->row();
        
        if (!$package) {
            addlog('Paket Detay - package', 'Paket bulunamadı - ' . $slug);
            flash('Ups.', 'Paket Bulunamadı');
            redirect(base_url('paketler'));
            return;
        }
        
        // Paket içindeki ürünleri getir
        $package_products = $this->db->select('p.*, pp.quantity, pp.sort_order')
            ->from('package_products pp')
            ->join('product p', 'p.id = pp.product_id', 'left')
            ->where('pp.package_id', $package->id)
            ->where('p.isActive', 1)
            ->order_by('pp.sort_order', 'ASC')
            ->get()
            ->result();
        
        // Toplam fiyat hesapla (indirimsiz)
        $total_price = 0;
        foreach ($package_products as $pp) {
            $total_price += $pp->price;
        }
        
        $package->products = $package_products;
        $package->total_price = $total_price;
        
        $data = [
            'properties' => $properties,
            'category' => getActiveCategories(),
            'package' => $package,
            'why' => $this->db->get('why')->result(),
            'pages' => $this->db->get('pages')->result(),
            'footerBlog' => $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result(),
            'footerPage' => $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result(),
            'footerProduct' => $this->db->limit(6)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result()
        ];
        
        $this->view('package-detail', $data);
    }

    /**
     * Paketi sepete ekleme
     */
    public function addPackageToCart()
    {
        $package_id = $this->input->post('package_id');
        
        if (empty($package_id) || !is_numeric($package_id)) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz paket ID']);
            exit;
        }
        
        // Paketi getir
        $package = $this->db->where('id', $package_id)->where('isActive', 1)->get('packages')->row();
        
        if (!$package) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Paket bulunamadı']);
            exit;
        }
        
        // Paketi sepete ekle (tek bir item olarak)
        $data = [
            'id' => $package->id,
            'product_id' => $package->id, // Paket ID'si
            'qty' => 1,
            'price' => floatval($package->price),
            'name' => 'package_' . $package->id, // Paket olduğunu belirtmek için
            'extras' => ['type' => 'package', 'package_id' => $package->id]
        ];
        
        $this->advanced_cart->insert($data);
        addlog('addPackageToCart', 'Paket sepete eklendi: ' . $package->name);
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
        exit;
    }
}
