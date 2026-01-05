<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Earnings extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function insertEarning($shop_id, $payment_type, $data = NULL)
    {
        if (isset($data)){
            $this->processPaymentOther($data);
        }else{
            $shop = $this->db->where('id', $shop_id)->get('shop')->row();
            if ($shop) {
                if ($payment_type == 'deposit') {
                    $this->processDeposit($shop);
                } else if ($payment_type == 'product_sale' || $payment_type == 'Satın alım') {
                    $invoices = $this->db->where('shop_id', $shop->id)->get('invoice')->result();
                    foreach ($invoices as $invoice) {
                        $this->processProductSale($shop, $invoice);
                    }
                    $pending_products = $this->db->where('shop_id', $shop->id)->get('pending_product')->result();
                    foreach ($pending_products as $pending_product) {
                        $this->processProductSaleForPendingProduct($shop, $pending_product);
                    }
                }else if ($payment_type == 'payment_commission') {
                    $this->processPaymentCommission($shop);
                }
                return true;
            } else {
                return false;
            }
        }
    }

    //private function processPaymentCommission
    private function processPaymentCommission($shop)
    {
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        $payment_commission_rate = $properties->commission;
        $payment_commission_amount = ($shop->price * $payment_commission_rate) / 100;

        $subscription_commission_rate = getCommission($user->id);
        $subscription_commission_amount = ($shop->price * $subscription_commission_rate) / 100;

        $difference = $payment_commission_amount - $subscription_commission_amount;

        if ($difference > 0) {
            $payment_commission_data = array(
                'transaction_date' => date('Y-m-d H:i:s', strtotime($shop->date)),
                'seller_id' => 0,
                'buyer_id' => $shop->user_id,
                'product_id' => 0,
                'invoice_id' => $shop->order_id,
                'shop_id' => $shop->id,
                'amount' => $difference,
                'total' => $shop->price,
                'payment_method' => $shop->type,
                'payment_date' => date('Y-m-d H:i:s', strtotime($shop->date)),
                'transaction_status' => 'successful',
                'payment_commission_amount' => $subscription_commission_amount,
                'payment_commission_rate' => $subscription_commission_rate,
                'commission_amount' => 0,
                'commission_rate' => 0,
                'net_earnings' => $subscription_commission_amount,
                'description' => "Kart ile ödeme komisyon kazancı yansıtıldı. Ödenmesi gereken: " . $payment_commission_amount . " TL, Ödenen: " . $subscription_commission_amount . " TL",
                'seller_type' => 'site',
                'payment_type' => 'Ödeme Komisyonu'
            );
            $this->db->insert('earnings', $payment_commission_data);
        }
    }
    private function processDeposit($shop)
    {
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        $payment_commission_rate = $properties->commission;
        $payment_commission_amount = ($shop->price * getCommission($user->id)) / 100;

        if ($payment_commission_amount > 0) {
            $payment_commission_data = array(
                'transaction_date' => date('Y-m-d H:i:s', strtotime($shop->date)),
                'seller_id' => 0,
                'buyer_id' => $shop->user_id,
                'product_id' => 0,
                'invoice_id' => $shop->order_id,
                'shop_id' => $shop->id,
                'amount' => $payment_commission_amount,
                'total' => $shop->price,
                'payment_method' => $shop->type,
                'payment_date' => date('Y-m-d H:i:s', strtotime($shop->date)),
                'transaction_status' => $shop->status == 0 ? 'successful' : 'pending',
                'payment_commission_amount' => $payment_commission_amount,
                'payment_commission_rate' => getCommission($user->id),
                'commission_amount' => 0,
                'commission_rate' => 0,
                'net_earnings' => $payment_commission_amount,
                'description' => "Ödeme komisyonu alındı. Sipariş ID: " . $shop->order_id,
                'seller_type' => 'site',
                'payment_type' => 'Ödeme komisyonu'
            );
            $this->db->insert('earnings', $payment_commission_data);
        }
    }

    private function processProductSaleForPendingProduct($shop, $pending_product)
    {
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();
        $payment_commission_rate = $shop->type == 'balance' ? 0 : getCommission($user->id); // Bakiye kullanıldıysa ödeme komisyonu yok
        $payment_commission_amount = ($pending_product->price * $payment_commission_rate) / 100;

        if (!$shop->seller_id) {
            $site_earning_data = array(
                'transaction_date' => date('Y-m-d H:i:s', strtotime($shop->date)),
                'seller_id' => 0,
                'buyer_id' => $shop->user_id,
                'product_id' => $pending_product->product_id,
                'invoice_id' => $pending_product->id,
                'shop_id' => $shop->id,
                'pending_product_id' => $pending_product->id,
                'amount' => $pending_product->price - $payment_commission_amount,
                'total' => $pending_product->price,
                'payment_method' => $shop->type,
                'payment_date' => date('Y-m-d H:i:s', strtotime($shop->date)),
                'transaction_status' => 'pending',
                'payment_commission_amount' => $payment_commission_amount,
                'payment_commission_rate' => getCommission($user->id),
                'commission_amount' => 0,
                'commission_rate' => 0,
                'net_earnings' => $pending_product->price - $payment_commission_amount,
                'description' => "Site Kazancı. Stok olmadığı için bekleyen ürünlere aktarıldı. Bekleyen Ürün ID: " . $pending_product->id,
                'seller_type' => 'site',
                'payment_type' => 'Satın alım'
            );
            $this->db->insert('earnings', $site_earning_data);
        }

        if ($payment_commission_amount > 0) {
            $payment_commission_data = array(
                'transaction_date' => date('Y-m-d H:i:s', strtotime($shop->date)),
                'seller_id' => $shop->seller_id ? $shop->seller_id : 0,
                'buyer_id' => $shop->user_id,
                'product_id' => $pending_product->product_id,
                'invoice_id' => $pending_product->id,
                'shop_id' => $shop->id,
                'amount' => $payment_commission_amount,
                'total' => $pending_product->price,
                'payment_method' => $shop->type,
                'payment_date' => date('Y-m-d H:i:s', strtotime($shop->date)),
                'transaction_status' => 'pending',
                'payment_commission_amount' => $payment_commission_amount,
                'payment_commission_rate' => getCommission($user->id),
                'commission_amount' => 0,
                'commission_rate' => 0,
                'net_earnings' => $payment_commission_amount,
                'description' => "Bekleyen ürün için ödeme komisyonu alındı. Sipariş ID: " . $pending_product->id,
                'seller_type' => $shop->seller_id ? 'user' : 'site',
                'payment_type' => 'Ödeme komisyonu'
            );
            $this->db->insert('earnings', $payment_commission_data);
        }

        $seller = $this->db->where('id', $shop->seller_id)->get('user')->row();
        if ($shop->seller_id && $seller->shop_com > 0) {
            $commission_amount = ($pending_product->price * $seller->shop_com) / 100;

            $commission_data = array(
                'transaction_date' => date('Y-m-d H:i:s', strtotime($shop->date)),
                'seller_id' => $shop->seller_id,
                'buyer_id' => $shop->user_id,
                'product_id' => $pending_product->product_id,
                'invoice_id' => $pending_product->id,
                'shop_id' => $shop->id,
                'amount' => $commission_amount,
                'total' => $pending_product->price,
                'payment_method' => $shop->type,
                'payment_date' => date('Y-m-d H:i:s', strtotime($shop->date)),
                'transaction_status' => 'pending',
                'payment_commission_amount' => $payment_commission_amount,
                'payment_commission_rate' => $payment_commission_rate,
                'commission_amount' => $commission_amount,
                'commission_rate' => $seller->shop_com,
                'net_earnings' => $commission_amount,
                'description' => "Transaction commission for pending product ID: " . $pending_product->id,
                'seller_type' => 'site',
                'payment_type' => 'Site Komisyonu'
            );
            $this->db->insert('earnings', $commission_data);
        }
    }

    private function processProductSale($shop, $invoice)
    {
        $properties = $this->db->where('id', 1)->get('properties')->row();
        $payment_commission_rate = $shop->type == 'balance' ? 0 : $properties->commission; // Bakiye kullanıldıysa ödeme komisyonu yok
        $payment_commission_amount = ($invoice->price * $payment_commission_rate) / 100;

        if ($invoice->seller_id > 1) {
            $seller = $this->db->where('id', $invoice->seller_id)->get('user')->row();
            $marketplace_commission_amount = ($invoice->price * $seller->shop_com) / 100;
            $site_earning_data = array(
                'transaction_date' => date('Y-m-d H:i:s', strtotime($invoice->date)),
                'seller_id' => 0,
                'buyer_id' => $shop->user_id,
                'product_id' => $invoice->product_id,
                'invoice_id' => $invoice->id,
                'shop_id' => $shop->id,
                'amount' => $invoice->price - $marketplace_commission_amount,
                'total' => $invoice->price,
                'payment_method' => $shop->type,
                'payment_date' => date('Y-m-d H:i:s', strtotime($invoice->date)),
                'transaction_status' => $invoice->isActive == 0 ? 'successful' : 'pending',
                'payment_commission_amount' => $payment_commission_amount,
                'payment_commission_rate' => $payment_commission_rate,
                'commission_amount' => 0,
                'commission_rate' => 0,
                'net_earnings' => $invoice->price - $marketplace_commission_amount,
                'description' => "Site Kazancı. Fatura ID: " . $invoice->id,
                'seller_type' => 'seller',
                'payment_type' => 'Satın alım'
            );
            $this->db->insert('earnings', $site_earning_data);
        }

        if ($payment_commission_amount > 0) {
            $payment_commission_data = array(
                'transaction_date' => date('Y-m-d H:i:s', strtotime($invoice->date)),
                'seller_id' => $invoice->seller_id ? $invoice->seller_id : 0,
                'buyer_id' => $shop->user_id,
                'product_id' => $invoice->product_id,
                'invoice_id' => $invoice->id,
                'shop_id' => $shop->id,
                'amount' => $payment_commission_amount,
                'total' => $invoice->price,
                'payment_method' => $shop->type,
                'payment_date' => date('Y-m-d H:i:s', strtotime($invoice->date)),
                'transaction_status' => $invoice->isActive == 0 ? 'successful' : 'pending',
                'payment_commission_amount' => $payment_commission_amount,
                'payment_commission_rate' => $payment_commission_rate,
                'commission_amount' => 0,
                'commission_rate' => 0,
                'net_earnings' => $payment_commission_amount,
                'description' => "Ödeme Komisyonu. Sipariş ID: " . $shop->id,
                'seller_type' => $shop->seller_id ? 'user' : 'site',
                'payment_type' => 'Ödeme komisyonu'
            );
            $this->db->insert('earnings', $payment_commission_data);
        }

        $seller = $this->db->where('id', $invoice->seller_id)->get('user')->row();
        if ($invoice->seller_id && $seller->shop_com > 0) {
            $commission_amount = ($invoice->price * $seller->shop_com) / 100;

            $commission_data = array(
                'transaction_date' => date('Y-m-d H:i:s', strtotime($invoice->date)),
                'seller_id' => $invoice->seller_id,
                'buyer_id' => $shop->user_id,
                'product_id' => $invoice->product_id,
                'invoice_id' => $invoice->id,
                'shop_id' => $shop->id,
                'amount' => $commission_amount,
                'total' => $invoice->price,
                'payment_method' => $shop->type,
                'payment_date' => date('Y-m-d H:i:s', strtotime($invoice->date)),
                'transaction_status' => $invoice->isActive == 0 ? 'successful' : 'pending',
                'payment_commission_amount' => $payment_commission_amount,
                'payment_commission_rate' => $payment_commission_rate,
                'commission_amount' => $commission_amount,
                'commission_rate' => $seller->shop_com,
                'net_earnings' => $commission_amount,
                'description' => "Pazaryeri Kazancı. Fatura ID: " . $invoice->id,
                'seller_type' => 'site',
                'payment_type' => 'Site Komisyonu'
            );
            $this->db->insert('earnings', $commission_data);
        }
    }

    private function processPaymentOther($data)
    {
        addLog('processPaymentOther', json_encode($data));
        $payment_commission_data = array(
            'transaction_date' => date('Y-m-d H:i:s'),
            'seller_id' => 0,
            'buyer_id' => $data['user_id'],
            'product_id' => 0,
            'invoice_id' => 0,
            'shop_id' => 0,
            'amount' => $data['amount'],
            'total' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'payment_date' => date('Y-m-d H:i:s'),
            'transaction_status' => 'successful',
            'payment_commission_amount' => 0,
            'payment_commission_rate' => 0,
            'commission_amount' => 0,
            'commission_rate' => 0,
            'net_earnings' => $data['amount'],
            'description' => $data['description'],
            'seller_type' => $data['seller_type'] ? $data['seller_type'] : 'site',
            'payment_type' => 'other'
        );
        $this->db->insert('earnings', $payment_commission_data);
    }

    public function updateEarningPendingTransfer($pending_transfer_id, $data)
    {
        $earning = $this->db->where('pending_product_id', $pending_transfer_id)->get('earnings')->row();
        $this->db->where('id', $earning->id)->update('earnings', $data);
    }
    public function updateEarning($pending_transfer_id, $data)
    {
        $earning = $this->db->where('id', $pending_transfer_id)->get('earnings')->row();
        $this->db->where('id', $earning->id)->update('earnings', $data);
    }

    public function updateEarningByInvoice($invoice_id, $data)
    {
        $earning = $this->db->where('invoice_id', $invoice_id)->get('earnings')->row();
        $this->db->where('id', $earning->id)->update('earnings', $data);
    }

    //getEarningsByInvoiceID
    public function getEarningByInvoice($invoice_id)
    {
        return $this->db->where('invoice_id', $invoice_id)->get('earnings')->row();
    }
    //getEarningsByShopID
    public function getEarningsByShopID($shop_id)
    {
        return $this->db->where('shop_id', $shop_id)->get('earnings')->row();
    }

    //updateEarningByShop
    public function updateEarningByShop($shop_id, $data)
    {
        $earning = $this->db->where('shop_id', $shop_id)->get('earnings')->row();
        $this->db->where('id', $earning->id)->update('earnings', $data);
    }

    public function getDailyEarnings()
    {
        $this->db->select('DATE(transaction_date) as date, SUM(amount) as total, SUM(payment_commission_amount) as commission, SUM(amount - payment_commission_amount) as net_total');
        $this->db->from('earnings');
        $this->db->where('transaction_status', 'successful');
        $this->db->group_by('DATE(transaction_date)');
        $this->db->order_by('DATE(transaction_date)', 'DESC');

        $query = $this->db->get();
        return $query->result_array();

    }
}

