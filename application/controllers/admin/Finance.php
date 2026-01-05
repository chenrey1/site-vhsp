<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance extends G_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
            redirect(base_url(), 'refresh');
            exit();
        }
    }

    public function invoices()
    {
        $data = [
            'invoices' => $this->db->order_by('id', 'DESC')->get('invoice')->result(),
            'balanceShops' => $this->db->where('type', 'deposit')->where('status', 0)->order_by('id', 'DESC')->get('shop')->result(),
            'status' => 'invoiceList'
        ];

        $this->adminView('invoice-list', $data);
    }

    public function createInvoicebyINV($invoiceID){
        $this->load->helper('api');
        $invoice = $this->db->where('id', $invoiceID)->get('invoice')->row();
        $shop = $this->db->where('id', $invoice->shop_id)->get('shop')->row();
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();

        $result = createInvoiceInAPI($user, $invoice);

        if($result){
            flash('Başarılı', 'Fatura Servisine Gönderildi.');
            redirect(base_url('admin/finance/invoices'));
        }else{
            flash('Başarısız', 'Bir Sorundan Ötürü Fatura Gönderilemedi.');
            redirect(base_url('admin/finance/invoices'));
        }
    }

    public function createInvoicebyShop($shopID){
        $this->load->helper('api');
        $shop = $this->db->where('id', $shopID)->get('shop')->row();
        $user = $this->db->where('id', $shop->user_id)->get('user')->row();

        $result = createInvoiceForBalance($user, $shop);

        if($result){
            flash('Başarılı', 'Fatura Servisine Gönderildi.');
            redirect(base_url('admin/finance/invoices'));
        }else{
            flash('Başarısız', 'Bir Sorundan Ötürü Fatura Gönderilemedi.');
            redirect(base_url('admin/finance/invoices'));
        }
    }

}
