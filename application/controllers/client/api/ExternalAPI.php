<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExternalAPI extends G_Controller {

    private $user;

    public function __construct() {
        parent::__construct();
        $properties = $this->db->get('properties')->row();
        if ($properties->api_is_active == 0) {
            $this->response(['status' => false, 'message' => 'API is disabled'], 403);
            return;
        }
        $this->load->model('M_Payment');
        $this->load->helper('api');
        $this->authenticate();
    }

    private function authenticate() {
        $this->load->library('encryption');

        if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            $this->unauthorized();
            return;
        }

        $email = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];
        $ip = $this->input->ip_address();

        $this->user = $this->db->where('email', $email)->get('user')->row();

        if (!$this->user || $this->user->password !== paspas($password)) {
            $this->unauthorized();
            return;
        }

        $allowed_ips = explode(',', $this->user->allowed_ips);
        if (!in_array($ip, $allowed_ips)) {
            $this->response(['status' => false, 'message' => 'IP not allowed ('. $ip .')'], 403);
            return;
        }
    }

    private function unauthorized() {
        header('WWW-Authenticate: Basic realm="Orius API"');
        $this->response(['status' => false, 'message' => 'Unauthorized'], 401);
    }

    private function response($data, $status = 200) {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header($status)
            ->set_output(json_encode($data))
            ->_display();
        exit();
    }

    public function balance() {
        $this->response(['status' => true, 'balance' => $this->user->balance]);
    }

    public function products_by_id($id)
    {
        $this->db->select('id, name, img, background_img, desc, price, text, category_id');
        $product = $this->db->where('id', $id)->where('isActive', 1)->get('product')->row();
        if (!$product) {
            $this->response(['status' => false, 'message' => 'Product not found'], 404);
            return;
        }

        $product->required_fields = json_decode($product->text, true) ?? [];
        $this->response(['status' => true, 'product' => $product]);
    }

    public function products() {
        $search = $this->input->get('search');
        $category_id = $this->input->get('category_id');
        $page = max(1, intval($this->input->get('page') ?: 1));
        $pageSize = min(100, max(1, intval($this->input->get('pageSize') ?: 10)));

        $this->db->select('p.id, p.name, p.img, p.background_img, p.desc, p.price, p.text, p.category_id, c.name as category_name')
            ->from('product p')
            ->join('category c', 'c.id = p.category_id', 'left')
            ->where('p.isActive', 1);

        if ($search) {
            $this->db->group_start()
                ->like('p.name', $search)
                ->or_like('p.desc', $search)
                ->group_end();
        }

        if ($category_id) {
            $this->db->where('p.category_id', $category_id);
        }

        $total_products = $this->db->count_all_results('', false);

        $offset = ($page - 1) * $pageSize;
        $this->db->limit($pageSize, $offset);
        $products = $this->db->get()->result();

        foreach($products as $product) {
            $product->required_fields = json_decode($product->text, true) ?? [];
            unset($product->text);
        }

        $this->response([
            'status' => true,
            'total' => $total_products,
            'page' => $page,
            'pageSize' => $pageSize,
            'products' => $products,
        ]);
    }

    public function categories_by_id($id) {
        $this->db->select('id, name, img, description');
        $category = $this->db->where('id', $id)->where('isActive', 1)->get('category')->row();
        if (!$category) {
            $this->response(['status' => false, 'message' => 'Category not found'], 404);
            return;
        }

        $this->response(['status' => true, 'category' => $category]);
    }

    public function categories() {
        $searchTerm = $this->input->get('name');
        $categoryTree = $this->input->get('categoryTree') === 'true';

        $this->db->select('c.id, c.name, c.img, c.description, c.mother_category_id AS parent_id, COUNT(p.id) as product_count')
            ->from('category c')
            ->join('product p', 'p.category_id = c.id', 'left')
            ->where('c.isActive', 1)
            ->group_by('c.id');

        if ($searchTerm) {
            $this->db->like('c.name', $searchTerm);
        }

        $categories = $this->db->get()->result();

        if ($categoryTree) {
            $response = $this->buildCategoryTree($categories);
        } else {
            $response = $categories;
        }

        $this->response([
            'status' => true,
            'total' => count($categories),
            'categories' => $response
        ]);
    }

    public function buildCategoryTree($categories, $parentId = 0, $searchTerm = null) {
        $branch = array();

        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                $children = $this->buildCategoryTree($categories, $category->id, $searchTerm);
                if ($children) {
                    $category->children = $children;
                }

                if ($searchTerm && strpos(strtolower($category->name), strtolower($searchTerm)) === false) {
                    continue;
                }

                $branch[] = $category;
            }
        }

        return $branch;
    }

    public function purchase() {
        $product_id = $this->input->post('product_id');
        $quantity = $this->input->post('quantity');
        $delivery_id = $this->input->post('delivery_id');
        $callback_url = $this->input->post('callback_url');

        if (!$product_id || !$quantity || !$delivery_id) {
            $this->response(['status' => false, 'message' => 'Missing product_id or quantity or delivery_id'], 400);
            return;
        }

        $product = $this->db->where('id', $product_id)->where('isActive', 1)->get('product')->row();
        if (!$product) {
            $this->response(['status' => false, 'message' => 'Product not found'], 404);
            return;
        }

        $total_price = $product->price * $quantity;
        if ($this->user->balance < $total_price) {
            $this->response(['status' => false, 'message' => 'Insufficient balance'], 400);
            return;
        }

        $required_fields = json_decode($product->text, true) ?? [];
        if (!empty($required_fields)) {
            $form_data = $this->input->post('required_fields');
            if (empty($form_data)) {
                $this->response([
                    'status' => false, 
                    'message' => 'Required fields are missing',
                    'required_fields' => $required_fields
                ], 400);
                return;
            }

            foreach ($required_fields as $field) {
                if (empty($form_data[$field])) {
                    $this->response([
                        'status' => false, 
                        'message' => $field . ' is required',
                        'required_fields' => $required_fields
                    ], 400);
                    return;
                }
            }
        }

        $encode = json_encode([
            [
                'id' => $product->id, 
                'qty' => $quantity, 
                'price' => $product->price,
                'extras' => $this->input->post('required_fields') ?? []
            ]
        ]);

        $order_id = $this->M_Payment->addShop($this->user->id, $encode, $total_price, 'balance');

        if ($order_id) {
            $api_order_data = [
                'order_id' => $order_id,
                'delivery_id' => $delivery_id,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];
            if ($callback_url) {
                $api_order_data['callback_url'] = $callback_url;
            }
            $this->db->insert('api_orders', $api_order_data);

            $this->M_Payment->confirmShopForCart($order_id);
            
            $pending_product = $this->db->where('shop_id', $order_id)->get('pending_product')->row();
            
            if ($pending_product) {
                $this->response([
                    'status' => true, 
                    'message' => 'Purchase pending', 
                    'order_id' => $order_id,
                    'delivery_status' => 'pending'
                ]);
            } else {
                $invoice = $this->db->where('shop_id', $order_id)->get('invoice')->row();
                $this->response([
                    'status' => true, 
                    'message' => 'Purchase successful', 
                    'order_id' => $order_id,
                    'delivery_status' => 'completed',
                    'code' => $invoice->product
                ]);
            }
        } else {
            $this->response(['status' => false, 'message' => 'Purchase failed'], 500);
        }
    }

    public function check_order($delivery_id) {
        $order_id = $this->db->where('delivery_id', $delivery_id)->get('api_orders')->row('order_id');
        if (!$order_id) {
            $this->response(['status' => false, 'message' => 'Order not found'], 404);
            return;
        }

        $shop = $this->db->where('order_id', $order_id)->get('shop')->row();
        
        if (!$shop) {
            $this->response(['status' => false, 'message' => 'Order not found'], 404);
            return;
        }

        $pending_product = $this->db->where('shop_id', $shop->id)->get('pending_product')->row();
        $invoice = $this->db->where('shop_id', $shop->id)->get('invoice')->row();

        if ($pending_product) {
            $this->response([
                'status' => true,
                'order_id' => $order_id,
                'delivery_status' => 'pending'
            ]);
        } else if ($invoice) {
            $this->response([
                'status' => true,
                'order_id' => $order_id,
                'delivery_status' => 'completed',
                'delivery_details' => $invoice->product
            ]);
        } else {
            $this->response([
                'status' => false,
                'message' => 'Order status unknown'
            ], 500);
        }
    }
}