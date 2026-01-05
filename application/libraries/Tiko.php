<?php
class Tiko {
    private $CI;
    private $base_url = "https://api.earsivportal.net/api/v1/";
    private $auth_token;

    public function __construct($config) {
        $this->CI =& get_instance();
        $this->auth_token = $config["auth_token"];
    }

    public function createInvoice($user, $inv) {
        $this->CI->load->model('M_Subscription');

        $items = [];
        $totalPrice = $inv->price;
        $shop = $this->CI->db->where('id', $inv->shop_id)->get('shop')->row();
        $product = $this->CI->db->where('id', $inv->product_id)->get('product')->row();

        if ($shop->type == 'credit_card') {
            $commissionRate = $this->CI->M_Subscription->getCommissionValue($shop->user_id);
            $commission = ($inv->price * $commissionRate) / 100;
            $this->addItem($items, $product->name, $inv->price);
            $this->addItem($items, "İşlem Ücreti", $commission);
            $totalPrice += $commission;
        } else {
            $this->addItem($items, $product->name, $inv->price);
        }
        $this->logInvoiceCreation($items);

        $invoiceData = $this->prepareInvoiceData($user, $totalPrice, $items);

        return $this->sendInvoiceRequest($inv->id, $invoiceData, 'invoice');
    }

    public function createInvoiceForBalance($user, $shop) {
        if ($shop->type != 'deposit') {
            return false;
        }

        $items = [];
        $this->addItem($items, "İşlem Ücreti", $shop->payment_commission);

        $invoiceData = $this->prepareInvoiceData($user, $shop->payment_commission, $items);

        return $this->sendInvoiceRequest($shop->id, $invoiceData, 'shop');
    }

    public function createInvoiceForSubscription($user, $subscription, $userSubscriptionID) {
        $items = [];
        $this->addItem($items, $subscription->duration . ' Günlük ' . $subscription->name, $subscription->price);

        $invoiceData = $this->prepareInvoiceData($user, $subscription->price, $items);

        return $this->sendInvoiceRequest($userSubscriptionID, $invoiceData, 'user_subscriptions');
    }

    private function addItem(&$items, $description, $price) {
        $priceWithoutVAT = $this->calculatePriceWithoutVAT($price);
        $vatAmount = $price - $priceWithoutVAT;

        $items[] = [
            "id" => "0",
            "aciklama" => $description,
            "miktar" => "1,00",
            "birim" => "C62",
            "birim_fiyat" => (string)$priceWithoutVAT,
            "kdvsiz_toplam" => (string)$priceWithoutVAT,
            "iskonto_orani" => "0,00",
            "iskonto_tutari" => "0,00",
            "ara_toplam" => (string)$priceWithoutVAT,
            "kdv_orani" => "20",
            "kdv_toplam" => (string)$vatAmount,
            "genel_toplam" => (string)$price,
            "sira" => "0",
            "fatura_tarihi" => date("d.m.Y"),
            "fatura_saati" => date("H:i")
        ];
    }

    private function prepareInvoiceData($user, $totalPrice, $items) {
        $totalPrice = $this->roundPrice($totalPrice);
        $priceWithoutVAT = $this->calculatePriceWithoutVAT($totalPrice);

        return [
            'id' => '0',
            'mt_unvan' => '',
            'mt_ad' => $user->name,
            'mt_soyad' => $user->surname,
            'mt_vergi_no' => '',
            'mt_vergi_dairesi' => '',
            'mt_eposta' => $user->email,
            'mt_gsm' => $user->phone,
            'irsaliye_tarihi' => '',
            'irsaliye_no' => '',
            'fatura_tipi' => 'SATIS',
            'fatura_tarihi' => date("d.m.Y"),
            'fatura_saati' => date("H:i"),
            'fatura_notu' => '',
            'fatura_adres' => 'yok',
            'tc_no' => $user->tc,
            'vkn_no' => '',
            'kdvsiz_toplam' => $this->formatPrice($priceWithoutVAT),
            'iskonto_toplam' => '0',
            'ara_toplam' => $this->formatPrice($priceWithoutVAT),
            'tutar_toplam0' => $this->formatPrice($totalPrice),
            'tutar_toplam1' => '0',
            'kdv_toplam1' => '0',
            'tutar_toplam8' => '0',
            'kdv_toplam8' => '0',
            'tutar_toplam18' => '0',
            'kdv_toplam18' => '0',
            'genel_toplam' => $this->formatPrice($totalPrice),
            'satir_table' => json_encode($items, JSON_UNESCAPED_UNICODE)
        ];
    }

    private function sendInvoiceRequest($id, $data, $type = 'invoice') {
        $response = $this->sendRequest("POST", "yeni-fatura-olustur", $data);
        if ($response["code"] == 200) {
            $this->updateInvoiceStatus($type, $id);
            return true;
        } else {
            $this->logError("Invoice creation failed", $response);
            return false;
        }
    }

    private function updateInvoiceStatus($type, $id) {
        $invoiceProvider = $this->CI->db->where(['api_name' => 'billing', 'setting_key' => 'provider'])->get('api_settings')->row()->setting_value;
        $this->CI->db->where('id', $id)->update($type, ['invoice_status' => 'invoiced', 'invoice_provider' => $invoiceProvider]);
    }

    private function roundPrice($price) {
        return round($price, 2);
    }

    private function formatPrice($price) {
        return number_format($price, 2, '.', '');
    }

    private function calculatePriceWithoutVAT($amount) {
        return $this->roundPrice($amount * 100 / 120);
    }

    private function sendRequest($method, $endpoint, $data = []) {
        $headers = [
            'Authorization: ' . $this->auth_token
        ];
        $url = $this->base_url . $endpoint;

        if ($method == "GET") {
            $url .= "?" . http_build_query($data);
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => ($method == "POST") ? http_build_query($data) : [],
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (curl_errno($curl)) {
            $this->logError("CURL Error", curl_error($curl));
        }

        curl_close($curl);

        return ["code" => $httpCode, "response" => $response];
    }

    private function logInvoiceCreation($items) {
        addLog('createInvoice', json_encode($items));
    }

    private function logError($message, $details) {
        // Implement error logging here
        error_log($message . ': ' . json_encode($details));
    }
}
?>