<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends G_Controller {

	public function __construct()
    {
        parent::__construct();
        if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
        	redirect(base_url(), 'refresh');
        	exit();
        }
	}

    public function index()
    {
        $today = date('Y-m-d');
        $today2 = date('d.m.Y');
        $data = [];

        // Site açıldığından bu yana kazanç ve satışlar
        $data['invoiceAllTimeAmount'] = round($this->db->select_sum('amount')->where('transaction_status', 'successful')->where_in('payment_method', ['balance', 'credit_card'])->get('earnings')->row()->amount);
        $data['invoiceAllTimeSell'] = $this->db->where('transaction_status', 'successful')->where_in('payment_method', ['balance', 'credit_card'])->count_all_results('earnings');

        // Site açıldığından bu yana bakiye yüklemeleri
        $data['balanceAllTimeAmount'] = round($this->db->select_sum('price')->where('type', 'deposit')->where('status', 0)->get('shop')->row()->price);
        $data['balanceAllTimeSell'] = $this->db->where('payment_method', 'deposit')->where('transaction_status', 'successful')->count_all_results('earnings');

        // Destek talepleri ve kullanıcı kayıtları (örneğin başka tablolardan)
        $data['allTickets'] = $this->db->count_all('ticket');
        $data['allUsers'] = $this->db->count_all('user');

        // Bugünkü kazanç ve satışlar
        $today = date('Y-m-d');
        $data['todayInvoicesAmount'] = round($this->db->select_sum('amount')->where('transaction_status', 'successful')->where_in('payment_method', ['balance', 'credit_card'])->where('DATE(transaction_date)', $today)->get('earnings')->row()->amount);
        $data['invoiceTodaySell'] = $this->db->where('transaction_status', 'successful')->where_in('payment_method', ['balance', 'credit_card'])->where('DATE(transaction_date)', $today)->count_all_results('earnings');

        // Bugünkü bakiye yüklemeleri
        $data['todayBal'] = round($this->db->select_sum('price')->where('type', 'deposit')->where('status', 0)->where('DATE(date)', $today)->get('shop')->row()->price);
        $data['todayBalSell'] = $this->db->where('type', 'deposit')->where('status', 0)->where('DATE(date)', $today)->count_all_results('shop');

        // Bugünkü destek talepleri ve kullanıcı kayıtları (örneğin başka tablolardan)
        $data['dayTickets'] = $this->db->like('date', $today2, 'after')->count_all_results('ticket');
        $data['dayUsers'] = $this->db->like('date', $today2, 'after')->count_all_results('user');

        // Diğer veri eklemeleri
        $data['pending'] = $this->db->order_by('id', 'desc')->where('isActive', 1)->get('pending_product')->result();
        $data['users'] = $this->db->order_by('id', 'desc')->get('user')->result();
        $data['invoices'] = $this->db->select('invoice.*, product.name')->where('invoice.isActive', 1)->where('invoice.seller_id', 0)->join('product', 'product.id = product_id', 'left')->get('invoice')->result();
        $data['status'] = 1;
        $data['update_info'] = $this->load->get_update_info();

        // Son cron çalışma zamanını kontrol et
        $last_cron_run = $this->db->where('`function`', 'Cron_Service')
                                 ->order_by('id', 'DESC')
                                 ->limit(1)
                                 ->get('logs')
                                 ->row();

        // Eğer log kaydı yoksa, $last_cron_run null olacak
        $data['last_cron_run'] = $last_cron_run;
        
        // Son 5 cron çalışmasını al
        $data['cron_history'] = $this->db->like('function', 'Cron_Service')
                                        ->order_by('id', 'desc')
                                        ->limit(5)
                                        ->get('logs')
                                        ->result();

        $this->adminView('dashboard', $data);
    }

	public function update() {
		$return = $this->load->update_script();
		echo $return;
	}

    public function overView()
    {
        $data = [];
        $data['status'] = 1;
        $data['update_info'] = $this->load->get_update_info();

        $this->adminView('overview', $data);
    }

    public function getOnlineUsers()
    {
        header('Content-Type: application/json');
        
        // Son 5 dakika içinde aktif olan kullanıcıları al
        $timeout = time() - (5 * 60);
        
        // Aktif kullanıcı sayısını al (sadece giriş yapmış kullanıcılar)
        $currentCount = $this->db->where('timestamp >', $timeout)
                                ->where('user_id IS NOT NULL')
                                ->where('user_id !=', 0)
                                ->where('last_page !=', '')
                                ->where('last_page !=', base_url())
                                ->group_by('user_id')
                                ->count_all_results('ci_sessions');

        // Dün aynı saatteki kullanıcı sayısını al
        $yesterdayTime = strtotime('-1 day');
        $yesterdayCount = $this->db->where('timestamp >', $yesterdayTime - 300)
                                  ->where('timestamp <', $yesterdayTime + 300)
                                  ->where('user_id IS NOT NULL')
                                  ->where('user_id !=', 0)
                                  ->where('last_page !=', '')
                                  ->where('last_page !=', base_url())
                                  ->group_by('user_id')
                                  ->count_all_results('ci_sessions');

        // Geçen hafta aynı saatteki kullanıcı sayısını al
        $lastWeekTime = strtotime('-1 week');
        $lastWeekCount = $this->db->where('timestamp >', $lastWeekTime - 300)
                                 ->where('timestamp <', $lastWeekTime + 300)
                                 ->where('user_id IS NOT NULL')
                                 ->where('user_id !=', 0)
                                 ->where('last_page !=', '')
                                 ->where('last_page !=', base_url())
                                 ->group_by('user_id')
                                 ->count_all_results('ci_sessions');

        // En aktif sayfaları al (sadece giriş yapmış kullanıcılar)
        $activePages = $this->db->select('last_page, COUNT(DISTINCT user_id) as visit_count')
                               ->where('timestamp >', $timeout)
                               ->where('last_page IS NOT NULL')
                               ->where('last_page !=', '')
                               ->where('last_page !=', base_url())
                               ->where('user_id IS NOT NULL')
                               ->where('user_id !=', 0)
                               ->group_by('last_page')
                               ->order_by('visit_count', 'DESC')
                               ->limit(5)
                               ->get('ci_sessions')
                               ->result();

        $response = [
            'count' => $currentCount,
            'comparisonData' => [
                'currentCount' => $currentCount,
                'yesterdayCount' => $yesterdayCount,
                'lastWeekCount' => $lastWeekCount
            ],
            'activePages' => $activePages
        ];

        echo json_encode($response);
    }

    /**
     * Manuel olarak cron görevlerini çalıştırır
     */
    public function run_cron() {
        try {
            // Cron Service kütüphanesini yükle ve çalıştır
            $this->load->library('Cron_Service');
            $result = $this->cron_service->run_from_external_api();
            
            // Log kaydı oluştur
            addLog('Dashboard', 'Cron görevleri manuel olarak çalıştırıldı', 'success');
            
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => true, 'message' => 'Cron görevleri başarıyla çalıştırıldı.']));
        } catch (Exception $e) {
            // Hata durumunda log kaydı oluştur
            addLog('Dashboard', 'Cron görevleri çalıştırılırken hata: ' . $e->getMessage(), 'error');
            
            $this->output->set_content_type('application/json')
                         ->set_output(json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]));
        }
    }

}
