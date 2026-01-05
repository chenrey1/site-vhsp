<?php

function getTurkpinCategories() {
    $CI =& get_instance();
    $properties = $CI->db->where('id', 1)->get('properties')->row();
    
    if (empty($properties->turkpin_username) || empty($properties->turkpin_password)) {
        return false;
    }
    
    $CI->load->library('Turkpin');
    $response = $CI->turkpin->getGameList();
    
    if (!empty($response["data"]['params']['HATA_NO']) && $response['params']['HATA_NO'] != "000") {
        return false;
    }
    
    return $response['data']['params']['oyunListesi']['oyun'];
}

function getPinabiCategories() {
    $CI =& get_instance();
    $CI->load->library('Pinabi');
    $response = $CI->pinabi->getProductsByType("ep");
    $response = json_decode($response, true);
    
    if (!$response['status'] || $response['status']['code'] != 200) {
        return false;
    }
    
    return $response['gameList'];
}

function getHyperCategories($api_url, $api_token) {
    $url = $api_url . "/Categories";

    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Bearer $api_token"
        ),
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return false;
    }

    return json_decode($response, true);
}

function getOriusCategories($api_url, $api_email, $api_password) {
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $api_url . "/categories",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Basic " . base64_encode($api_email . ":" . $api_password)
        ),
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return false;
    }
    $response = json_decode($response, true);
    if (!$response) {
        return false;
    }

    return $response["categories"];
}

function getTurkpinProducts($category_id) {
    $CI =& get_instance();
    $CI->load->library('Turkpin');
    $response = $CI->turkpin->getEpinProduct($category_id);
    
    if (!empty($response["data"]['params']['HATA_NO']) && $response['params']['HATA_NO'] != "000") {
        return false;
    }
    
    return $response['data']['params']['epinUrunListesi']['urun'];
}

function getPinabiProducts($category_id) {
    $CI =& get_instance();
    $CI->load->library('Pinabi');
    $response = $CI->pinabi->getProductsByType("ep");
    $response = json_decode($response, true);
    
    if (!$response['status'] || $response['status']['code'] != 200) {
        return false;
    }

    $products = array_filter($response["gameList"], function ($v) use ($id) {
        return $v['id'] == $id;
    });
    
    return $products;
}

function getHyperProducts($api_url, $api_token, $categoryID=null, $page=null, $pageSize=null) {
    $url = $api_url . "/Products/List";
    if ($categoryID) {
        $url .= "?productCategoryID=" . $categoryID;
    }
    if ($page) {
        $url .= "&page=" . $page;
    }
    if ($pageSize) {
        $url .= "&pageSize=" . $pageSize;
    }

    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Bearer $api_token"
        ),
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return false;
    }

    return json_decode($response, true);
}

function getOriusProducts($api_url, $api_email, $api_password, $category_id = null, $search = null, $page = 1, $pageSize = 10) {
    $url = $api_url . "/products";

    $data = [];
    if ($page) {
        $data["page"] = $page;
    }
    if ($pageSize) {
        $data["pageSize"] = $pageSize;
    }
    
    if ($category_id) {
        $data["category_id"] = $category_id;
    }
    
    if ($search) {
        $data["search"] = $search;
    }

    $url .= "?" . http_build_query($data);

    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Basic " . base64_encode($api_email . ":" . $api_password)
        ),
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return false;
    }

    $response = json_decode($response, true);
    if (!$response) {
        return false;
    }

    return $response["products"];
}

function createHyperOrder($api_url, $api_token, $productId, $deliverId, $fields) {
    $formattedFields = array_map(function($field_id, $value) {
        return [
            "productRequireID" => (int) $field_id,
            "value" => $value
        ];
    }, array_keys($fields), $fields);
    
    $data = [
        "basketData" => [[
            "deliverID" => $deliverId,
            "customerStoreProductID" => $productId,
            "quantity" => 1,
            "requireData" => $formattedFields
        ]],
        "notifyURL" => base_url("provider-callback/hyper")
    ];

    $url = $api_url . "/Order/create";

    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
            "accept: application/json",
            "authorization: Bearer $api_token"
        ),
        CURLOPT_POSTFIELDS => json_encode($data),
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return false;
    }

    $response = json_decode($response, true);
    $response["request"] = $data;


    addlog("createHyperOrder", json_encode($response));

    return $response;
}

function getHyperOrders($api_url, $api_token, $page=null, $pageSize=null) {
    $url = $api_url . "/Order/customer/list";

    $data = [];
    if ($page) {
        $data["page"] = $page;
    }
    if ($pageSize) {
        $data["pageSize"] = $pageSize;
    }

    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
            "accept: application/json",
            "authorization: Bearer $api_token"
        ),
        CURLOPT_POSTFIELDS => json_encode($data),
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return false;
    }

    return json_decode($response, true);
}

function createOriusOrder($api_url, $api_email, $api_password, $product_id, $deliver_id, $quantity = 1) {
    $data = [
        "product_id" => $product_id,
        "delivery_id" => $deliver_id,
        "quantity" => $quantity,
        "callback_url" => base_url("provider-callback")
    ];

    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $api_url . "/purchase",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
            "content-type: application/json",
            "accept: application/json",
            "authorization: Basic " . base64_encode($api_email . ":" . $api_password)
        ),
        CURLOPT_POSTFIELDS => json_encode($data),
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return false;
    }

    $response = json_decode($response, true);
    $response["request"] = $data;

    return $response;
}

function getOriusBalance($api_url, $api_email, $api_password) {
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL => $api_url . "/balance",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "authorization: Basic " . base64_encode($api_email . ":" . $api_password)
        ),
    ));

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return false;
    }

    return json_decode($response, true);
}

function normalizeCategories($categories, $provider_type) {
    switch($provider_type) {
        case 'hyper':
            return normalizeHyperCategories($categories);
        case 'orius':
            return normalizeOriusCategories($categories);
        case 'turkpin':
            return normalizeTurkpinCategories($categories);
        case 'pinabi':
            return normalizePinabiCategories($categories);
        default:
            return [];
    }
}

function normalizeHyperCategories($categories) {
    $normalized = [];
    foreach($categories['data'] as $category) {
        $normalized[] = [
            'id' => $category['productCategoryID'],
            'name' => $category['categoryName'],
            'parent_id' => $category['parentID'] ?? 0,
            'status' => true
        ];
    }
    return $normalized;
}

function normalizeOriusCategories($categories) {
    $normalized = [];
    foreach($categories as $category) {
        $normalized[] = [
            'id' => $category['id'],
            'name' => $category['name'],
            'parent_id' => $category['parent_id'] ?? 0,
            'status' => $category['status'] ?? true
        ];
    }
    return $normalized;
}

function normalizeTurkpinCategories($categories) {
    $normalized = [];
    foreach($categories as $category) {
        $normalized[] = [
            'id' => $category['id'],
            'name' => $category['name'],
            'parent_id' => 0,
            'status' => true
        ];
    }
    return $normalized;
}

function normalizePinabiCategories($categories) {
    $normalized = [];
    foreach($categories['content'] as $category) {
        $normalized[] = [
            'id' => $category['id'],
            'name' => $category['name'],
            'parent_id' => 0,
            'status' => true
        ];
    }
    return $normalized;
}

function normalizeProducts($products, $provider_type) {
    switch($provider_type) {
        case 'hyper':
            return normalizeHyperProducts($products);
        case 'orius':
            return normalizeOriusProducts($products);
        case 'turkpin':
            return normalizeTurkpinProducts($products);
        case 'pinabi':
            return normalizePinabiProducts($products);
        default:
            return [];
    }
}

function normalizeHyperProducts($products) {
    $normalized = [];
    foreach($products['data'] as $product) {
        $normalized[] = [
            'id' => $product['customerStoreProductID'],
            'name' => $product['productName'],
            'description' => $product['productData']['productDescription'] ?? '',
            'price' => $product['salePrice'],
            'stock' => $product['totalStock'] ?? null,
            'category_id' => $product['productCategoryID'],
            'status' => $product['status'] ?? true,
            'provider_code' => $product['productCode'] ?? '',
            'image_url' => $product['productMainImage'] ?? '',
            'meta' => [
                'min_quantity' => $product['minQuantity'] ?? 1,
                'max_quantity' => $product['maxQuantity'] ?? null,
                'delivery_type' => $product['deliveryType'] ?? 'instant'
            ],
            'required_fields' => array_map(function($field) {
                return [
                    "id" => $field["productRequireID"],
                    "name" => $field["title"]
                ];
            }, $product["productRequire"])
        ];
    }
    return $normalized;
}

function normalizeOriusProducts($products) {
    $normalized = [];
    foreach($products as $product) {
        $normalized[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'desc' => $product['desc'] ?? null,
            'category_id' => $product['category_id']
        ];
    }
    return $normalized;
}

function normalizeTurkpinProducts($products) {
    $normalized = [];
    foreach($products as $product) {
        $normalized[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'description' => '',
            'price' => $product['price'],
            'stock' => null, // Turkpin stok bilgisi vermiyor
            'category_id' => $product['category_id'] ?? null,
            'status' => true,
            'provider_code' => $product['id'],
            'image_url' => '',
            'meta' => [
                'min_quantity' => 1,
                'max_quantity' => null,
                'delivery_type' => 'instant'
            ]
        ];
    }
    return $normalized;
}

function normalizePinabiProducts($products) {
    $normalized = [];
    foreach($products as $product) {
        $normalized[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'description' => '',
            'price' => $product['price'],
            'stock' => $product['stock'],
            'category_id' => $product['category_id'] ?? null,
            'status' => true,
            'provider_code' => $product['id'],
            'image_url' => '',
            'meta' => [
                'min_quantity' => 1,
                'max_quantity' => null,
                'delivery_type' => 'instant'
            ]
        ];
    }
    return $normalized;
}

function normalizeHyperOrder($order) {
    return [
        'status' => $order['success'],
        'delivery_status' => "pending",
        'code' => ""
    ];
}

function normalizeOriusOrder($order)
{
    return [
        'status' => $order['status'],
        'delivery_status' => $order['delivery_status'],
        'code' => $order["code"],
    ];
}

function normalizeOrder($order, $provider_type) {
    switch($provider_type) {
        case 'hyper':
            return normalizeHyperOrder($order);
        case 'orius':
            return normalizeOriusOrder($order);
        default:
            return [];
    }
}

function getProviderCategories($provider) {
    $api_details = json_decode($provider->api_details);
    $categories = false;
    
    switch($provider->type) {
        case 'hyper':
            $categories = getHyperCategories($provider->base_url, $api_details->api_token);
            break;
        case 'orius':
            $categories = getOriusCategories($provider->base_url, $api_details->api_email, $api_details->api_password);
            break;
        case 'turkpin':
            $categories = getTurkpinCategories();
            break;
        case 'pinabi':
            $categories = getPinabiCategories();
            break;
    }
    
    return $categories ? normalizeCategories($categories, $provider->type) : false;
}

function getProviderProducts($provider, $category_id = null) {
    $api_details = json_decode($provider->api_details);
    $products = false;
    
    switch($provider->type) {
        case 'hyper':
            $products = getHyperProducts($provider->base_url, $api_details->api_token, $category_id);
            break;
        case 'orius':
            $products = getOriusProducts($provider->base_url, $api_details->api_email, $api_details->api_password, $category_id);
            break;
        case 'turkpin':
            $products = getTurkpinProducts($category_id);
            break;
        case 'pinabi':
            $products = getPinabiProducts($category_id);
            break;
    }
    
    return $products ? normalizeProducts($products, $provider->type) : false;
}

function createProviderOrder($provider_id, $product_id, $deliver_id, $fields) {
    $ci =& get_instance();
    $provider = $ci->db->where('id', $provider_id)->get('product_providers')->row();
    if (!$provider) {
        return false;
    }

    $api_details = json_decode($provider->api_details);
    $order = false;

    switch($provider->type) {
        case 'hyper':
            $order = createHyperOrder($provider->base_url, $api_details->api_token, $product_id, $deliver_id, $fields);
            break;
        case 'orius':
            $order = createOriusOrder($provider->base_url, $api_details->api_email, $api_details->api_password, $product_id, $deliver_id);
            break;
    }

    return $order ? normalizeOrder($order, $provider->type) : false;
}
function process_order_callback($order_id) {
    $ci =& get_instance();
    
    $callback = $ci->db->where('order_id', $order_id)->get('api_orders')->row();
    if (!$callback) {
        return false;
    }

    $pending_product = $ci->db->where('shop_id', $order_id)
        ->get('pending_product')->row();
    $invoice = $ci->db->where('shop_id', $order_id)
                     ->get('invoice')->row();

    $status = null;
    $code = null;

    if ($invoice && $invoice->isActive == 0) {
        $status = 'completed';
        $code = $invoice->product;
    } else if ($pending_product) {
        $status = 'pending';
    } else {
        $status = 'cancelled';
    }

    addlog('callback_process', 'Order #' . $order_id . ' callback processing. Status: ' . $status);

    if ($callback->status == 'sent') {
        return true;
    }

    if ($status != 'completed' && $status != 'cancelled') {
        return false;
    }

    $ch = curl_init($callback->callback_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'delivery_id' => $callback->delivery_id,
        'status' => $status,
        'code' => $code,
        'processed_at' => date('Y-m-d H:i:s')
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $ci->db->where('order_id', $order_id)->update('api_orders', [
        'last_try' => date('Y-m-d H:i:s'),
        'status' => ($http_code >= 200 && $http_code < 300) ? 'sent' : 'failed',
        'response_code' => $http_code
    ]);

    addlog('callback_process', 'Order #' . $order_id . ' callback sent. HTTP Response: ' . $http_code);

    return ($http_code >= 200 && $http_code < 300);
}

function retry_failed_callbacks() {
    $ci =& get_instance();

    $callbacks = $ci->db->where('status', 'failed')
                        ->where('last_try <', date('Y-m-d H:i:s', strtotime('-5 minutes')))
                        ->order_by('last_try', 'ASC')
                        ->get('api_orders', 5)->result();

    foreach($callbacks as $callback) {
        process_order_callback($callback->order_id);
    }
}