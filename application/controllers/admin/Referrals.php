<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Referrals extends G_Controller {

    public function __construct() {
        parent::__construct();
        if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
            redirect(base_url(), 'refresh');
            exit();
        }
        
        // Load necessary models and libraries
        $this->load->model('M_Settings');
        $this->load->model('M_Balance'); 
        $this->load->library('form_validation');
        $this->load->helper(['url', 'form', 'text']);
    }

    /**
     * Index - Redirect to dashboard
     */
    public function index() {
        redirect('admin/referrals/dashboard');
    }

    /**
     * Real-Time Dashboard
     */
    public function dashboard() {
        $data['title'] = 'Referans Sistemi - Canlı İzleme';
        $data['status'] = 'referrals';
        
        // Gerçek zamanlı veriler için AJAX endpoint'leri kullanılacak
        $this->adminView('referrals/dashboard', $data);
    }
    
    /**
     * AJAX - Gerçek zamanlı dashboard verilerini getir
     */
    public function get_realtime_data() {
        $this->output->set_content_type('application/json');
        
        try {
            $time_range = $this->input->get('timeRange') ?: '1h';
            $chart_type = $this->input->get('chartType') ?: 'referrals';
            
            $metrics = $this->_get_realtime_metrics($time_range);
            $chart_data = $this->_get_chart_data($time_range, $chart_type);
            
            echo json_encode([
                'success' => true,
                'metrics' => $metrics,
                'chart_data' => $chart_data,
                'time_range' => $time_range,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Veri yüklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * AJAX - Son aktiviteleri getir
     */
    public function get_recent_activities() {
        $this->output->set_content_type('application/json');
        
        try {
            $limit = min($this->input->get('limit') ?: 10, 50); // Maksimum 50
            $activities = $this->_get_recent_activities($limit);
            
            echo json_encode([
                'success' => true,
                'activities' => $activities
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Aktiviteler yüklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }
    

    /**
     * AJAX - En iyi referans verenleri getir
     */
    public function get_top_referrers() {
        $this->output->set_content_type('application/json');
        
        try {
            $limit = $this->input->get('limit') ?: 10;
            $referrers = $this->_get_top_referrers($limit);
            
            echo json_encode([
                'success' => true,
                'referrers' => $referrers
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'En iyi referans verenler yüklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Referans ayarları sayfası
     */
    public function settings() {

        // Referans ayarlarını çek
        $referral_settings = [
            'referral_system_enabled',
            'referral_register_bonus_fixed',
            'referral_purchase_bonus_rate', 
            'referral_min_purchase_amount',
            'referral_bonus_balance_type',
            'referral_require_purchase',
            'referral_max_bonus_per_transaction',
            'referral_max_bonus_per_month',
            'max_referrer_changes',
            'referrer_change_cooldown_days',
            'allow_referrer_change',
            'ref_code_min_length',
            'ref_code_max_length',
            'ref_code_change_max_per_30_days',
            'ref_code_change_cooldown_days'
        ];

        $data['settings'] = [];
        $data['status'] = 'referrals';
        foreach ($referral_settings as $setting) {
            $setting_obj = $this->M_Settings->getSetting($setting);
            $data['settings'][$setting] = $setting_obj;
        }

        // İstatistikleri hesapla
        $data['stats'] = $this->_get_referral_stats();

        $this->adminView('referrals/settings', $data);
    }

    /**
     * Genel referans ayarlarını güncelle
     */
    public function update_referral_settings() {

        $this->form_validation->set_rules('settings[referral_register_bonus_fixed]', 'Kayıt Bonusu', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('settings[referral_purchase_bonus_rate]', 'Alışveriş Bonus Oranı', 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]');
        $this->form_validation->set_rules('settings[referral_min_purchase_amount]', 'Minimum Alışveriş Tutarı', 'required|numeric|greater_than_equal_to[0]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/referrals/settings');
        }

        $settings = $this->input->post('settings');
        
        // Checkbox'lar için varsayılan değerler
        $settings['referral_system_enabled'] = isset($settings['referral_system_enabled']) ? '1' : '0';
        $settings['referral_require_purchase'] = isset($settings['referral_require_purchase']) ? '1' : '0';

        $success = true;
        foreach ($settings as $key => $value) {
            if (!$this->M_Settings->updateSetting($key, $value)) {
                $success = false;
            }
        }

        if ($success) {
            $this->session->set_flashdata('success', 'Genel ayarlar başarıyla güncellendi.');
        } else {
            $this->session->set_flashdata('error', 'Ayarlar güncellenirken bir hata oluştu.');
        }

        redirect('admin/referrals/settings');
    }

    /**
     * Limit ayarlarını güncelle
     */
    public function update_referral_limits() {

        $this->form_validation->set_rules('settings[referral_max_bonus_per_transaction]', 'İşlem Başına Max Bonus', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('settings[referral_max_bonus_per_month]', 'Aylık Max Bonus', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('settings[max_referrer_changes]', 'Max Referans Değişiklik', 'required|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('settings[referrer_change_cooldown_days]', 'Bekleme Süresi', 'required|integer|greater_than_equal_to[0]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/referrals/settings');
        }

        $settings = $this->input->post('settings');
        
        // Checkbox için varsayılan değer
        $settings['allow_referrer_change'] = isset($settings['allow_referrer_change']) ? '1' : '0';

        $success = true;
        foreach ($settings as $key => $value) {
            if (!$this->M_Settings->updateSetting($key, $value)) {
                $success = false;
            }
        }

        if ($success) {
            $this->session->set_flashdata('success', 'Limit ayarları başarıyla güncellendi.');
        } else {
            $this->session->set_flashdata('error', 'Limit ayarları güncellenirken bir hata oluştu.');
        }

        redirect('admin/referrals/settings');
    }

    /**
     * Referans kodu ayarlarını güncelle
     */
    public function update_referral_codes() {

        // Library tarafında min 3-20, max 5-30 arasında sınırlandırıldığı için admin tarafını da hizalıyoruz
        $this->form_validation->set_rules('settings[ref_code_min_length]', 'Minimum Uzunluk', 'required|integer|greater_than_equal_to[3]|less_than_equal_to[20]');
        $this->form_validation->set_rules('settings[ref_code_max_length]', 'Maksimum Uzunluk', 'required|integer|greater_than_equal_to[5]|less_than_equal_to[30]');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/referrals/settings');
        }

        $settings = $this->input->post('settings');

        // Min ve max uzunluk kontrolü
        if ($settings['ref_code_min_length'] >= $settings['ref_code_max_length']) {
            $this->session->set_flashdata('error', 'Minimum uzunluk, maksimum uzunluktan küçük olmalıdır.');
            redirect('admin/referrals/settings');
        }

        $success = true;
        foreach ($settings as $key => $value) {
            if (!$this->M_Settings->updateSetting($key, $value)) {
                $success = false;
            }
        }

        if ($success) {
            $this->session->set_flashdata('success', 'Kod ayarları başarıyla güncellendi.');
        } else {
            $this->session->set_flashdata('error', 'Kod ayarları güncellenirken bir hata oluştu.');
        }

        redirect('admin/referrals/settings');
    }

    /**
     * Kategori bonusları yönetimi
     */
    public function categories() {
        $data['title'] = 'Kategori Bonus Yönetimi';

        // Kategorileri ve bonus ayarlarını çek
        $this->db->select('c.*, rcc.bonus_percentage, rcc.min_amount, rcc.max_bonus, rcc.is_active as bonus_active');
        $this->db->from('category c');
        $this->db->join('reference_category_commissions rcc', 'c.id = rcc.category_id', 'left');
        $this->db->where('c.isActive', 1);
        $this->db->order_by('c.name', 'ASC');
        $data['categories'] = $this->db->get()->result();
        
        // Son 30 gün için tarih aralığı
        $start_datetime = date('Y-m-d H:i:s', strtotime('-30 days'));
        $end_datetime = date('Y-m-d H:i:s');
        
        // Her kategori için son 30 günün alışveriş hacmini ve ödenen bonusu hesapla
        foreach ($data['categories'] as &$category) {
            // Alışveriş hacmi
            $this->db->select('COALESCE(SUM(i.price), 0) as monthly_volume');
            $this->db->from('invoice i');
            $this->db->join('product p', 'i.product_id = p.id', 'inner');
            $this->db->where('p.category_id', $category->id);
            $this->db->where('i.isActive', 0); // 0 = Teslim edildi (ödendi)
            $this->db->where("STR_TO_DATE(i.date, '%d.%m.%Y %H:%i:%s') >=", $start_datetime);
            $this->db->where("STR_TO_DATE(i.date, '%d.%m.%Y %H:%i:%s') <=", $end_datetime);
            $result = $this->db->get()->row();
            $category->monthly_volume = $result ? $result->monthly_volume : 0;
            
            // Ödenen bonus
            $this->db->select('COALESCE(SUM(rbh.bonus_amount), 0) as total_bonus');
            $this->db->from('reference_bonus_history rbh');
            $this->db->join('invoice i', 'rbh.invoice_id = i.id', 'inner');
            $this->db->join('product p', 'i.product_id = p.id', 'inner');
            $this->db->where('p.category_id', $category->id);
            $this->db->where('rbh.status', 'paid');
            $this->db->where('rbh.bonus_type', 'purchase');
            $this->db->where('rbh.created_at >=', $start_datetime);
            $this->db->where('rbh.created_at <=', $end_datetime);
            $bonus_result = $this->db->get()->row();
            $category->total_bonus = $bonus_result ? $bonus_result->total_bonus : 0;
        }
        
        $data['status'] = 'referrals';

        // Varsayılan referans ayarlarını al
        $data['default_settings'] = [
            'purchase_bonus_rate' => $this->M_Settings->getSettingValue('referral_purchase_bonus_rate', '5.00'),
            'min_purchase_amount' => $this->M_Settings->getSettingValue('referral_min_purchase_amount', '10.00'),
            'max_bonus_per_transaction' => $this->M_Settings->getSettingValue('referral_max_bonus_per_transaction', '50.00')
        ];

        $this->adminView('referrals/categories', $data);
    }

    /**
     * Kategori bonus ayarlarını güncelle (AJAX)
     */
    public function update_category_bonus() {
        // Content-Type header'ı JSON olarak ayarla
        $this->output->set_content_type('application/json');
        
        // AJAX istek kontrolü
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz istek türü!']);
            return;
        }

        $category_id = $this->input->post('category_id');
        $bonus_percentage = $this->input->post('bonus_percentage');
        $min_amount = $this->input->post('min_amount');
        $max_bonus = $this->input->post('max_bonus');
        $is_active = $this->input->post('is_active') ? 1 : 0;

        // Veriler geçerli mi kontrol et
        if (!$category_id || !is_numeric($category_id)) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz kategori ID!']);
            return;
        }

        if (!is_numeric($bonus_percentage) || $bonus_percentage < 0 || $bonus_percentage > 100) {
            echo json_encode(['success' => false, 'message' => 'Bonus yüzdesi 0-100 arasında olmalıdır!']);
            return;
        }

        if (!is_numeric($min_amount) || $min_amount < 0) {
            echo json_encode(['success' => false, 'message' => 'Minimum tutar 0 veya daha büyük olmalıdır!']);
            return;
        }

        if ($max_bonus !== null && $max_bonus !== '' && (!is_numeric($max_bonus) || $max_bonus < 0)) {
            echo json_encode(['success' => false, 'message' => 'Maksimum bonus 0 veya daha büyük olmalıdır!']);
            return;
        }

        // Kategori var mı kontrol et
        $category = $this->db->where('id', $category_id)->where('isActive', 1)->get('category')->row();
        if (!$category) {
            echo json_encode(['success' => false, 'message' => 'Kategori bulunamadı!']);
            return;
        }

        // Mevcut komisyon kaydını kontrol et
        $existing = $this->db->where('category_id', $category_id)->get('reference_category_commissions')->row();

        $data = [
            'category_id' => $category_id,
            'bonus_percentage' => $bonus_percentage,
            'min_amount' => $min_amount,
            'max_bonus' => ($max_bonus !== null && $max_bonus !== '') ? $max_bonus : null,
            'is_active' => $is_active,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            // Güncelle
            $this->db->where('category_id', $category_id);
            $result = $this->db->update('reference_category_commissions', $data);
            
            // Eğer güncelleme yapılmadıysa (aynı veriler), yine de başarılı say
            if (!$result && $this->db->affected_rows() === 0) {
                $result = true; // Aynı veriler ile güncelleme girişimi başarılı sayılır
            }
        } else {
            // Yeni ekle
            $data['created_at'] = date('Y-m-d H:i:s');
            $result = $this->db->insert('reference_category_commissions', $data);
        }


        
        if ($result) {
            echo json_encode([
                'success' => true, 
                'message' => 'Kategori bonus ayarları başarıyla güncellendi.',
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Güncelleme sırasında bir hata oluştu!'
            ]);
        }
    }

    /**
     * Bonus geçmişi
     */
    public function history($page = 1) {
        $data['title'] = 'Referans Bonus Geçmişi';

        // GET parametrelerini al
        $referrer_id = $this->input->get('referrer_id');
        $bonus_type = $this->input->get('bonus_type');
        $status = $this->input->get('status');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        // Sayfa numarasını düzelt
        $page = max(1, (int)$page);
        $per_page = 10;
        $offset = ($page - 1) * $per_page;

        // Query builder'ı başlat
        $this->db->select('rbh.*, ur.name as referrer_name, ur.email as referrer_email, ud.name as referred_name, ud.email as referred_email');
        $this->db->from('reference_bonus_history rbh');
        $this->db->join('user ur', 'rbh.referrer_id = ur.id');
        $this->db->join('user ud', 'rbh.referred_user_id = ud.id');

        // Filtreleri uygula
        if ($referrer_id) {
            $this->db->where('rbh.referrer_id', $referrer_id);
        }
        if ($bonus_type) {
            $this->db->where('rbh.bonus_type', $bonus_type);
        }
        if ($status) {
            $this->db->where('rbh.status', $status);
        }
        if ($start_date) {
            $this->db->where('DATE(rbh.created_at) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(rbh.created_at) <=', $end_date);
        }

        // Toplam kayıt sayısını al (sayfalama için)
        $total_rows = $this->db->count_all_results('', FALSE);

        // Sayfalama için
        $this->load->library('pagination');
        
        // GET parametrelerini URL'ye dahil et
        $get_params = [];
        if ($referrer_id) $get_params['referrer_id'] = $referrer_id;
        if ($bonus_type) $get_params['bonus_type'] = $bonus_type;
        if ($status) $get_params['status'] = $status;
        if ($start_date) $get_params['start_date'] = $start_date;
        if ($end_date) $get_params['end_date'] = $end_date;

        $config['base_url'] = base_url('admin/referrals/history');
        $config['total_rows'] = $total_rows;
        $config['per_page'] = $per_page;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = FALSE;
        $config['query_string_segment'] = 'page';
        
        // GET parametrelerini sayfalama linklerine ekle
        if (!empty($get_params)) {
            $config['suffix'] = '?' . http_build_query($get_params);
            $config['first_url'] = $config['base_url'] . '/1' . $config['suffix'];
        }

        // Bootstrap pagination
        $config['full_tag_open'] = '<nav><ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul></nav>';
        $config['first_link'] = 'İlk';
        $config['last_link'] = 'Son';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = 'Önceki';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = 'Sonraki';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link">';
        $config['cur_tag_close'] = '</a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        // Verileri çek
        $this->db->order_by('rbh.created_at', 'DESC');
        $this->db->limit($per_page, $offset);
        $data['bonus_history'] = $this->db->get()->result();

        $data['pagination'] = $this->pagination->create_links();
        $data['status'] = 'referrals';

        $this->adminView('referrals/history', $data);
    }

    /**
     * Detaylı istatistikler
     */
    public function statistics() {
        $data['title'] = 'Referans İstatistikleri';

        // Filtreleme parametrelerini al
        $period = $this->input->get('period');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        $bonus_type = $this->input->get('bonus_type');

        // Hazır seçenekler için tarih aralığını belirle
        if ($period && $period !== 'custom') {
            $date_range = $this->_get_period_dates($period);
            $start_date = $date_range['start'];
            $end_date = $date_range['end'];
        } else {
            // Özel tarih veya varsayılan
            $start_date = $start_date ?: date('Y-m-d', strtotime('-30 days'));
            $end_date = $end_date ?: date('Y-m-d');
        }

        // Çeşitli istatistikleri hesapla
        $data['stats'] = $this->_get_detailed_stats($start_date, $end_date, $bonus_type);
        
        $data['status'] = 'referrals';

        $this->adminView('referrals/statistics', $data);
    }

    /**
     * Temel referans istatistiklerini hesapla
     */
    private function _get_referral_stats($start_date = null, $end_date = null, $bonus_type = null) {
        $stats = [];

        // Seçilen tarih aralığında kayıt olan kullanıcı sayısı
        if ($start_date && $end_date) {
            $this->db->where("STR_TO_DATE(date, '%d.%m.%Y %H:%i:%s') >=", $start_date . ' 00:00:00');
            $this->db->where("STR_TO_DATE(date, '%d.%m.%Y %H:%i:%s') <=", $end_date . ' 23:59:59');
            $stats['total_users'] = $this->db->count_all_results('user');
        } else {
            $stats['total_users'] = $this->db->count_all('user');
        }

        // Seçilen tarih aralığında referansı olan kullanıcı sayısı
        $this->db->distinct();
        $this->db->select('ur.buyer_id');
        $this->db->from('user_references ur');
        $this->db->join('user u', 'ur.buyer_id = u.id');
        $this->db->where('ur.is_active', 1);
        if ($start_date && $end_date) {
            $this->db->where('DATE(ur.created_at) >=', $start_date);
            $this->db->where('DATE(ur.created_at) <=', $end_date);
        }
        $stats['users_with_referrer'] = $this->db->count_all_results();

        // Seçilen tarih aralığında referans veren kullanıcı sayısı
        $this->db->distinct();
        $this->db->select('ur.referrer_id');
        $this->db->from('user_references ur');
        $this->db->where('ur.is_active', 1);
        if ($start_date && $end_date) {
            $this->db->where('DATE(ur.created_at) >=', $start_date);
            $this->db->where('DATE(ur.created_at) <=', $end_date);
        }
        $stats['users_who_refer'] = $this->db->count_all_results();

        // Seçilen tarih aralığında verilen toplam bonus
        $this->db->select('SUM(bonus_amount) as total_bonus');
        $this->db->from('reference_bonus_history');
        $this->db->where('status', 'paid');
        if ($start_date) {
            $this->db->where('DATE(created_at) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(created_at) <=', $end_date);
        }
        if ($bonus_type) {
            $this->db->where('bonus_type', $bonus_type);
        }
        $result = $this->db->get()->row();
        $stats['total_bonus'] = $result ? $result->total_bonus : 0;

        // Seçilen tarih aralığında bu ay verilen bonus (eğer tarih aralığı bu ayı kapsıyorsa)
        $current_month_start = date('Y-m-01');
        $current_month_end = date('Y-m-t');
        
        if (!$start_date || ($start_date <= $current_month_start && (!$end_date || $end_date >= $current_month_end))) {
            // Tarih aralığı bu ayı kapsıyorsa, bu ayki bonusu hesapla
            $this->db->select('SUM(bonus_amount) as monthly_bonus');
            $this->db->from('reference_bonus_history');
            $this->db->where('MONTH(created_at) =', date('m'));
            $this->db->where('YEAR(created_at) =', date('Y'));
            $this->db->where('status', 'paid');
            if ($bonus_type) {
                $this->db->where('bonus_type', $bonus_type);
            }
            $result = $this->db->get()->row();
            $stats['monthly_bonus'] = $result ? $result->monthly_bonus : 0;
        } else {
            // Tarih aralığı bu ayı kapsamıyorsa, seçilen aralıktaki bonusu hesapla
            $stats['monthly_bonus'] = $stats['total_bonus'];
        }

        return $stats;
    }

    /**
     * Detaylı istatistikleri hesapla
     */
    private function _get_detailed_stats($start_date = null, $end_date = null, $bonus_type = null) {
        $stats = $this->_get_referral_stats($start_date, $end_date, $bonus_type);

        // Aylık bonus trend (son 6 ay) - OPTİMİZE EDİLMİŞ
        $monthly_data = $this->db->select("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(bonus_amount) as amount, COUNT(*) as count")
                                ->from('reference_bonus_history')
                                ->where('status', 'paid')
                                ->where('created_at >=', date('Y-m-01', strtotime('-5 months')))
                                ->group_by("DATE_FORMAT(created_at, '%Y-%m')")
                                ->order_by('month', 'DESC')
                                ->get()
                                ->result();

        $monthly_trends = [];
        foreach ($monthly_data as $row) {
            $monthly_trends[] = [
                'month' => $row->month,
                'amount' => $row->amount,
                'count' => $row->count
            ];
        }
        $stats['monthly_trends'] = $monthly_trends;

        // Günlük trend (filtrelenmiş tarih aralığı) - OPTİMİZE EDİLMİŞ
        $daily_trends = [];
        $start = $start_date ? strtotime($start_date) : strtotime('-30 days');
        $end = $end_date ? strtotime($end_date) : time();

        if ($start && $end) {
            $start_date_str = date('Y-m-d', $start);
            $end_date_str = date('Y-m-d', $end);

            // Tek sorgu ile tüm günlük verileri çek
            $daily_data = $this->db->select("DATE(created_at) as date,
                                           SUM(bonus_amount) as amount,
                                           COUNT(*) as count")
                                  ->from('reference_bonus_history')
                                  ->where('status', 'paid')
                                  ->where('DATE(created_at) >=', $start_date_str)
                                  ->where('DATE(created_at) <=', $end_date_str);

            if ($bonus_type) {
                $this->db->where('bonus_type', $bonus_type);
            }

            $daily_data = $this->db->group_by("DATE(created_at)")
                                  ->order_by("DATE(created_at)", 'ASC')
                                  ->get()
                                  ->result();

            // Tüm tarihler için boş değerler ile array oluştur
            $daily_trends_map = [];
            foreach ($daily_data as $row) {
                $daily_trends_map[$row->date] = [
                    'date' => $row->date,
                    'amount' => $row->amount,
                    'count' => $row->count
                ];
            }

            // Belirtilen tarih aralığında tüm günleri doldur
            for ($date = $start; $date <= $end; $date = strtotime('+1 day', $date)) {
                $date_str = date('Y-m-d', $date);
                $daily_trends[] = $daily_trends_map[$date_str] ?? [
                    'date' => $date_str,
                    'amount' => 0,
                    'count' => 0
                ];
            }
        }
        $stats['daily_trends'] = $daily_trends;

        // Yeni kayıt trendi (filtrelenmiş tarih aralığı) - OPTİMİZE EDİLMİŞ
        $registration_trends = [];
        $start = $start_date ? strtotime($start_date) : strtotime('-30 days');
        $end = $end_date ? strtotime($end_date) : time();

        if ($start && $end) {
            $start_date_str = date('Y-m-d', $start);
            $end_date_str = date('Y-m-d', $end);

            // Tek sorgu ile tüm günlük kayıt sayılarını çek
            $registration_data = $this->db->select("DATE(STR_TO_DATE(date, '%d.%m.%Y %H:%i:%s')) as date,
                                                  COUNT(*) as count")
                                        ->from('user')
                                        ->where("STR_TO_DATE(date, '%d.%m.%Y %H:%i:%s') >=", $start_date_str . ' 00:00:00')
                                        ->where("STR_TO_DATE(date, '%d.%m.%Y %H:%i:%s') <=", $end_date_str . ' 23:59:59')
                                        ->group_by("DATE(STR_TO_DATE(date, '%d.%m.%Y %H:%i:%s'))")
                                        ->order_by("DATE(STR_TO_DATE(date, '%d.%m.%Y %H:%i:%s')) ASC", '', false)
                                        ->get()
                                        ->result();

            // Tüm tarihler için boş değerler ile array oluştur
            $registration_map = [];
            foreach ($registration_data as $row) {
                $registration_map[$row->date] = [
                    'date' => $row->date,
                    'count' => $row->count
                ];
            }

            // Belirtilen tarih aralığında tüm günleri doldur
            for ($date = $start; $date <= $end; $date = strtotime('+1 day', $date)) {
                $date_str = date('Y-m-d', $date);
                $registration_trends[] = $registration_map[$date_str] ?? [
                    'date' => $date_str,
                    'count' => 0
                ];
            }
        }
        $stats['registration_trends'] = $registration_trends;

        // Top referans verenler (tarih filtreli)
        $this->db->select('u.name, u.surname, u.email, u.ref_code, 
                          COUNT(ur.buyer_id) as referral_count,
                          SUM(ur.bonus_earned) as total_earned');
        $this->db->from('user u');
        $this->db->join('user_references ur', 'u.id = ur.referrer_id');
        $this->db->where('ur.is_active', 1);
        if ($start_date) {
            $this->db->where('DATE(ur.created_at) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(ur.created_at) <=', $end_date);
        }
        $this->db->group_by('u.id');
        $this->db->order_by('referral_count', 'DESC');
        $this->db->limit(10);
        $stats['top_referrers'] = $this->db->get()->result();

        // Kullanıcı segmentasyonu
        $stats['user_segments'] = $this->_get_user_segments();

        // Toplam işlem sayısı
        $this->db->select('COUNT(*) as total_transactions');
        $this->db->from('reference_bonus_history');
        $this->db->where('status', 'paid');
        if ($start_date) {
            $this->db->where('DATE(created_at) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(created_at) <=', $end_date);
        }
        if ($bonus_type) {
            $this->db->where('bonus_type', $bonus_type);
        }
        $result = $this->db->get()->row();
        $stats['total_transactions'] = $result ? $result->total_transactions : 0;

        // Toplam satış tutarı (referans bonusu verilen işlemlerden)
        $this->db->select('SUM(i.price) as total_sales');
        $this->db->from('reference_bonus_history rbh');
        $this->db->join('invoice i', 'rbh.invoice_id = i.id', 'left');
        $this->db->where('rbh.status', 'paid');
        $this->db->where('rbh.bonus_type', 'purchase');
        if ($start_date) {
            $this->db->where('DATE(rbh.created_at) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(rbh.created_at) <=', $end_date);
        }
        if ($bonus_type) {
            $this->db->where('rbh.bonus_type', $bonus_type);
        }
        $result = $this->db->get()->row();
        $stats['total_sales'] = $result ? $result->total_sales : 0;

        // Kullanıcı segmentasyonu
        $stats['user_segments'] = $this->_get_user_segments($start_date, $end_date);

        // Bonus türü dağılımı (gerçek veriler)
        $stats['bonus_type_distribution'] = $this->_get_bonus_type_distribution($start_date, $end_date);

        return $stats;
    }

    /**
     * Hazır seçenekler için tarih aralıklarını hesapla
     */
    private function _get_period_dates($period) {
        $today = date('Y-m-d');
        
        switch($period) {
            case 'today':
                return ['start' => $today, 'end' => $today];
                
            case 'week':
                $week_start = date('Y-m-d', strtotime('monday this week'));
                return ['start' => $week_start, 'end' => $today];
                
            case 'month':
                $month_start = date('Y-m-01');
                return ['start' => $month_start, 'end' => $today];
                
            case 'quarter':
                $current_month = date('n');
                $quarter_start_month = floor(($current_month - 1) / 3) * 3 + 1;
                $quarter_start = date('Y-m-01', strtotime(date('Y') . '-' . $quarter_start_month . '-01'));
                return ['start' => $quarter_start, 'end' => $today];
                
            case 'year':
                $year_start = date('Y-01-01');
                return ['start' => $year_start, 'end' => $today];
                
            default:
                return ['start' => date('Y-m-d', strtotime('-30 days')), 'end' => $today];
        }
    }

    /**
     * Kullanıcı segmentasyonu hesapla
     */
    private function _get_user_segments($start_date = null, $end_date = null) {
        $segments = [];

        // Aktif referans verenler (seçilen tarih aralığında referans veren)
        $this->db->select('COUNT(DISTINCT referrer_id) as count');
        $this->db->from('user_references');
        $this->db->where('is_active', 1);
        if ($start_date && $end_date) {
            $this->db->where('created_at >=', $start_date . ' 00:00:00');
            $this->db->where('created_at <=', $end_date . ' 23:59:59');
        } else {
            $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')));
        }
        $result = $this->db->get()->row();
        $segments['active_referrers'] = $result ? $result->count : 0;

        // Pasif referans verenler (seçilen tarih aralığından önce)
        $this->db->select('COUNT(DISTINCT referrer_id) as count');
        $this->db->from('user_references');
        $this->db->where('is_active', 1);
        if ($start_date) {
            $this->db->where('created_at <', $start_date . ' 00:00:00');
        } else {
            $this->db->where('created_at <', date('Y-m-d H:i:s', strtotime('-30 days')));
        }
        $result = $this->db->get()->row();
        $segments['inactive_referrers'] = $result ? $result->count : 0;

        // Yeni kullanıcılar (seçilen tarih aralığında kayıt olan)
        $this->db->select('COUNT(*) as count');
        $this->db->from('user');
        if ($start_date && $end_date) {
            $this->db->where("STR_TO_DATE(date, '%d.%m.%Y %H:%i:%s') >=", $start_date . ' 00:00:00');
            $this->db->where("STR_TO_DATE(date, '%d.%m.%Y %H:%i:%s') <=", $end_date . ' 23:59:59');
        } else {
            $this->db->where("STR_TO_DATE(date, '%d.%m.%Y %H:%i:%s') >=", date('Y-m-d H:i:s', strtotime('-7 days')));
        }
        $result = $this->db->get()->row();
        $segments['new_users'] = $result ? $result->count : 0;

        // Referansı olan kullanıcılar (seçilen tarih aralığında)
        $this->db->select('COUNT(DISTINCT buyer_id) as count');
        $this->db->from('user_references');
        $this->db->where('is_active', 1);
        if ($start_date && $end_date) {
            $this->db->where('created_at >=', $start_date . ' 00:00:00');
            $this->db->where('created_at <=', $end_date . ' 23:59:59');
        } else {
            $this->db->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')));
        }
        $result = $this->db->get()->row();
        $segments['new_referrals'] = $result ? $result->count : 0;

        return $segments;
    }

    /**
     * Bonus türü dağılımını hesapla
     */
    private function _get_bonus_type_distribution($start_date = null, $end_date = null) {
        $distribution = [];

        // Kayıt bonusu
        $this->db->select('SUM(bonus_amount) as amount, COUNT(*) as count');
        $this->db->from('reference_bonus_history');
        $this->db->where('bonus_type', 'register');
        $this->db->where('status', 'paid');
        if ($start_date) {
            $this->db->where('DATE(created_at) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(created_at) <=', $end_date);
        }
        $result = $this->db->get()->row();
        $distribution['registration'] = [
            'amount' => $result ? $result->amount : 0,
            'count' => $result ? $result->count : 0
        ];

        // Alışveriş bonusu
        $this->db->select('SUM(bonus_amount) as amount, COUNT(*) as count');
        $this->db->from('reference_bonus_history');
        $this->db->where('bonus_type', 'purchase');
        $this->db->where('status', 'paid');
        if ($start_date) {
            $this->db->where('DATE(created_at) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(created_at) <=', $end_date);
        }
        $result = $this->db->get()->row();
        $distribution['purchase'] = [
            'amount' => $result ? $result->amount : 0,
            'count' => $result ? $result->count : 0
        ];

        // Diğer bonuslar
        $this->db->select('SUM(bonus_amount) as amount, COUNT(*) as count');
        $this->db->from('reference_bonus_history');
        $this->db->where('bonus_type !=', 'register');
        $this->db->where('bonus_type !=', 'purchase');
        $this->db->where('status', 'paid');
        if ($start_date) {
            $this->db->where('DATE(created_at) >=', $start_date);
        }
        if ($end_date) {
            $this->db->where('DATE(created_at) <=', $end_date);
        }
        $result = $this->db->get()->row();
        $distribution['other'] = [
            'amount' => $result ? $result->amount : 0,
            'count' => $result ? $result->count : 0
        ];

        return $distribution;
    }

    /**
     * Genel performans skoru hesapla
     */
    public function calculate_performance_score($stats) {
        $score = 0;
        
        // Referans kullanım oranı (0-40 puan)
        $referral_rate = $stats['total_users'] > 0 ? ($stats['users_with_referrer'] / $stats['total_users']) * 100 : 0;
        $score += min(40, $referral_rate * 0.4);
        
        // Aktif referans veren oranı (0-30 puan)
        $active_rate = $stats['users_who_refer'] > 0 ? ($stats['user_segments']['active_referrers'] / $stats['users_who_refer']) * 100 : 0;
        $score += min(30, $active_rate * 0.3);
        
        // Ortalama bonus tutarı (0-30 puan)
        $avg_bonus = $stats['users_who_refer'] > 0 ? $stats['total_bonus'] / $stats['users_who_refer'] : 0;
        $score += min(30, $avg_bonus * 0.3);
        
        return number_format($score, 1);
    }

    /**
     * Büyüme skoru hesapla
     */
    public function calculate_growth_score($stats) {
        $score = 0;
        
        // Yeni kullanıcı büyüme oranı (0-50 puan)
        $new_user_rate = $stats['total_users'] > 0 ? ($stats['user_segments']['new_users'] / $stats['total_users']) * 100 : 0;
        $score += min(50, $new_user_rate * 5);
        
        // Yeni referans büyüme oranı (0-50 puan)
        $new_referral_rate = $stats['users_with_referrer'] > 0 ? ($stats['user_segments']['new_referrals'] / $stats['users_with_referrer']) * 100 : 0;
        $score += min(50, $new_referral_rate * 5);
        
        return number_format($score, 1);
    }

    /**
     * AJAX ile istatistikleri getir
     */
    public function get_stats_ajax() {
        // AJAX isteği kontrolü
        if (!$this->input->is_ajax_request()) {
            $this->output->set_status_header(403);
            exit('Direct access not allowed');
        }

        // Filtreleme parametrelerini al
        $start_date = $this->input->get('start_date') ?: date('Y-m-d', strtotime('-30 days'));
        $end_date = $this->input->get('end_date') ?: date('Y-m-d');
        $bonus_type = $this->input->get('bonus_type');

        // İstatistikleri hesapla
        $stats = $this->_get_detailed_stats($start_date, $end_date, $bonus_type);

        // JSON response
        $this->output->set_content_type('application/json');
        $this->output->set_output(json_encode([
            'success' => true,
            'stats' => $stats,
            'timestamp' => date('Y-m-d H:i:s')
        ]));
    }

    /**
     * İstatistikleri export et
     */
    public function export_statistics() {
        if (!$this->input->post('export_type')) {
            redirect('admin/referrals/statistics');
        }

        $export_type = $this->input->post('export_type');
        $stats = $this->_get_detailed_stats();

        if ($export_type === 'excel') {
            $this->_export_excel($stats);
        } elseif ($export_type === 'pdf') {
            $this->_export_pdf($stats);
        }
    }

    /**
     * Excel export
     */
    private function _export_excel($stats) {
        // Excel dosyası oluştur
        $filename = 'referans_istatistikleri_' . date('Y-m-d_H-i-s') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Basit CSV formatında export (Excel uyumlu)
        $output = fopen('php://output', 'w');
        
        // Başlık satırı
        fputcsv($output, ['Referans İstatistikleri Raporu', '', '', '']);
        fputcsv($output, ['Rapor Tarihi:', date('d.m.Y H:i:s'), '', '']);
        fputcsv($output, ['', '', '', '']);

        // Özet veriler
        fputcsv($output, ['ÖZET VERİLER', '', '', '']);
        fputcsv($output, ['Toplam Kullanıcı', $stats['total_users'], '', '']);
        fputcsv($output, ['Referansı Olan Kullanıcı', $stats['users_with_referrer'], '', '']);
        fputcsv($output, ['Referans Veren Kullanıcı', $stats['users_who_refer'], '', '']);
        fputcsv($output, ['Toplam Bonus', $stats['total_bonus'] . ' TL', '', '']);
        fputcsv($output, ['Bu Ay Bonus', $stats['monthly_bonus'] . ' TL', '', '']);
        fputcsv($output, ['', '', '', '']);

        // Günlük trend
        fputcsv($output, ['GÜNLÜK TREND (Son 30 Gün)', '', '', '']);
        fputcsv($output, ['Tarih', 'Bonus Tutarı (TL)', 'İşlem Sayısı', 'Yeni Kayıt']);
        
        foreach ($stats['daily_trends'] as $trend) {
            $registration_count = 0;
            if (isset($stats['registration_trends'])) {
                foreach ($stats['registration_trends'] as $reg_trend) {
                    if ($reg_trend['date'] === $trend['date']) {
                        $registration_count = $reg_trend['count'];
                        break;
                    }
                }
            }
            
            fputcsv($output, [
                date('d.m.Y', strtotime($trend['date'])),
                $trend['amount'],
                $trend['count'],
                $registration_count
            ]);
        }

        fputcsv($output, ['', '', '', '']);

        // Aylık trend
        fputcsv($output, ['AYLIK TREND (Son 6 Ay)', '', '', '']);
        fputcsv($output, ['Ay', 'Bonus Tutarı (TL)', 'İşlem Sayısı']);
        
        foreach ($stats['monthly_trends'] as $trend) {
            fputcsv($output, [
                date('M Y', strtotime($trend['month'] . '-01')),
                $trend['amount'],
                $trend['count']
            ]);
        }

        fputcsv($output, ['', '', '', '']);

        // En iyi referans verenler
        fputcsv($output, ['EN İYİ REFERANS VERENLER', '', '', '']);
        fputcsv($output, ['Sıra', 'Ad Soyad', 'Email', 'Referans Kodu', 'Referans Sayısı', 'Toplam Kazanç (TL)']);
        
        foreach ($stats['top_referrers'] as $index => $referrer) {
            fputcsv($output, [
                $index + 1,
                $referrer->name . ' ' . $referrer->surname,
                $referrer->email,
                $referrer->ref_code,
                $referrer->referral_count,
                $referrer->total_earned
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * PDF export
     */
    private function _export_pdf($stats) {
        // Basit HTML formatında PDF
        $html = '<html><head><title>Referans İstatistikleri</title>';
        $html .= '<style>body{font-family:Arial,sans-serif;margin:20px;}table{border-collapse:collapse;width:100%;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background-color:#f2f2f2;}</style>';
        $html .= '</head><body>';
        
        $html .= '<h1>Referans İstatistikleri Raporu</h1>';
        $html .= '<p><strong>Rapor Tarihi:</strong> ' . date('d.m.Y H:i:s') . '</p>';
        
        $html .= '<h2>Özet Veriler</h2>';
        $html .= '<table><tr><th>Metrik</th><th>Değer</th></tr>';
        $html .= '<tr><td>Toplam Kullanıcı</td><td>' . number_format($stats['total_users']) . '</td></tr>';
        $html .= '<tr><td>Referansı Olan Kullanıcı</td><td>' . number_format($stats['users_with_referrer']) . '</td></tr>';
        $html .= '<tr><td>Referans Veren Kullanıcı</td><td>' . number_format($stats['users_who_refer']) . '</td></tr>';
        $html .= '<tr><td>Toplam Bonus</td><td>' . number_format($stats['total_bonus'], 2) . ' TL</td></tr>';
        $html .= '<tr><td>Bu Ay Bonus</td><td>' . number_format($stats['monthly_bonus'], 2) . ' TL</td></tr>';
        $html .= '</table>';
        
        $html .= '<h2>En İyi Referans Verenler</h2>';
        $html .= '<table><tr><th>Sıra</th><th>Ad Soyad</th><th>Email</th><th>Referans Kodu</th><th>Referans Sayısı</th><th>Toplam Kazanç</th></tr>';
        
        foreach ($stats['top_referrers'] as $index => $referrer) {
            $html .= '<tr>';
            $html .= '<td>' . ($index + 1) . '</td>';
            $html .= '<td>' . $referrer->name . ' ' . $referrer->surname . '</td>';
            $html .= '<td>' . $referrer->email . '</td>';
            $html .= '<td>' . $referrer->ref_code . '</td>';
            $html .= '<td>' . $referrer->referral_count . '</td>';
            $html .= '<td>' . number_format($referrer->total_earned, 2) . ' TL</td>';
            $html .= '</tr>';
        }
        
        $html .= '</table></body></html>';
        
        $filename = 'referans_istatistikleri_' . date('Y-m-d_H-i-s') . '.pdf';
        
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        // Basit HTML to PDF (gerçek uygulamada TCPDF veya benzeri kullanılabilir)
        echo $html;
        exit;
    }
    
    /**
     * Gerçek zamanlı dashboard metrikleri
     */
    private function _get_realtime_metrics($time_range = '1h') {
        $metrics = [];
        
        // Zaman aralığını hesapla
        $time_params = $this->_get_time_range_params($time_range);
        
        // Seçili dönemdeki yeni referanslar
        $metrics['new_referrals_total'] = $this->db
            ->where('created_at >', $time_params['start'])
            ->where('created_at <=', $time_params['end'])
            ->where('is_active', 1)
            ->count_all_results('user_references');
        
        // Önceki dönemdeki referanslar (karşılaştırma için)
        $metrics['new_referrals_previous'] = $this->db
            ->where('created_at >', $time_params['prev_start'])
            ->where('created_at <=', $time_params['prev_end'])
            ->where('is_active', 1)
            ->count_all_results('user_references');
        
        // Seçili dönemdeki bonus ödemeleri
        $bonus_total = $this->db
            ->select_sum('bonus_amount')
            ->where('created_at >', $time_params['start'])
            ->where('created_at <=', $time_params['end'])
            ->where('status', 'paid')
            ->get('reference_bonus_history')
            ->row();
        $metrics['bonus_paid_total'] = $bonus_total ? (float)$bonus_total->bonus_amount : 0;
        
        // Önceki dönemdeki bonus ödemeleri
        $bonus_previous = $this->db
            ->select_sum('bonus_amount')
            ->where('created_at >', $time_params['prev_start'])
            ->where('created_at <=', $time_params['prev_end'])
            ->where('status', 'paid')
            ->get('reference_bonus_history')
            ->row();
        $metrics['bonus_paid_previous'] = $bonus_previous ? (float)$bonus_previous->bonus_amount : 0;
        
        // Satışa Dönüş Oranı Metrikleri
        // Seçili dönemdeki toplam referans sayısı
        $metrics['total_referrals'] = $metrics['new_referrals_total'];
        
        // Seçili dönemde alışveriş yapan referans sayısı
        $purchase_referrals = $this->db
            ->select('COUNT(DISTINCT ur.buyer_id) as count')
            ->from('user_references ur')
            ->join('shop s', 's.user_id = ur.buyer_id')
            ->join('invoice i', 'i.shop_id = s.id')
            ->where('s.date >', $time_params['start'])
            ->where('s.date <=', $time_params['end'])
            ->where('ur.is_active', 1)
            ->get()
            ->row();
        $metrics['purchase_referrals'] = $purchase_referrals ? (int)$purchase_referrals->count : 0;
        
        // Debug: Sorguyu loglayalım
        log_message('debug', 'Purchase referrals query: ' . $this->db->last_query());
        
        // Önceki dönem karşılaştırması için
        $prev_purchase_referrals = $this->db
            ->select('COUNT(DISTINCT ur.buyer_id) as count')
            ->from('user_references ur')
            ->join('shop s', 's.user_id = ur.buyer_id')
            ->join('invoice i', 'i.shop_id = s.id')
            ->where('s.date >', $time_params['prev_start'])
            ->where('s.date <=', $time_params['prev_end'])
            ->where('ur.is_active', 1)
            ->get()
            ->row();
        $prev_purchase_count = $prev_purchase_referrals ? (int)$prev_purchase_referrals->count : 0;
        
        // Trend hesaplamaları kaldırıldı
        
        // Bonus Performansı Metrikleri
        // Seçili dönemdeki toplam ödenen bonus
        $total_bonus_paid = $this->db
            ->select_sum('bonus_amount')
            ->where('created_at >', $time_params['start'])
            ->where('created_at <=', $time_params['end'])
            ->where('status', 'paid')
            ->get('reference_bonus_history')
            ->row();
        $metrics['total_bonus_paid'] = $total_bonus_paid ? (float)$total_bonus_paid->bonus_amount : 0;
        
        // Seçili dönemdeki referanslardan gelen toplam kazanç (referans yapanların harcamaları)
        $total_revenue = $this->db
            ->select('SUM(i.price) as revenue')
            ->from('invoice i')
            ->join('shop s', 's.id = i.shop_id')
            ->join('user_references ur', 's.user_id = ur.buyer_id')
            ->where('s.date >', $time_params['start'])
            ->where('s.date <=', $time_params['end'])
            ->where('ur.is_active', 1)
            ->get()
            ->row();
        $metrics['total_revenue'] = $total_revenue ? (float)$total_revenue->revenue : 0;
        
        // Önceki dönemdeki veriler (karşılaştırma için)
        $prev_period_bonus = $this->db
            ->select_sum('bonus_amount')
            ->where('created_at >', $time_params['prev_start'])
            ->where('created_at <=', $time_params['prev_end'])
            ->where('status', 'paid')
            ->get('reference_bonus_history')
            ->row();
        $prev_period_bonus_amount = $prev_period_bonus ? (float)$prev_period_bonus->bonus_amount : 0;
        
        $prev_period_revenue = $this->db
            ->select('SUM(i.price) as revenue')
            ->from('invoice i')
            ->join('shop s', 's.id = i.shop_id')
            ->join('user_references ur', 's.user_id = ur.buyer_id')
            ->where('s.date >', $time_params['prev_start'])
            ->where('s.date <=', $time_params['prev_end'])
            ->where('ur.is_active', 1)
            ->get()
            ->row();
        $prev_period_revenue_amount = $prev_period_revenue ? (float)$prev_period_revenue->revenue : 0;
        
        // Bonus performans trendi (ROI değişimi)
        $current_roi = $metrics['total_bonus_paid'] > 0 ? 
            (($metrics['total_revenue'] - $metrics['total_bonus_paid']) / $metrics['total_bonus_paid']) * 100 : 0;
        $prev_roi = $prev_period_bonus_amount > 0 ? 
            (($prev_period_revenue_amount - $prev_period_bonus_amount) / $prev_period_bonus_amount) * 100 : 0;
        
        return $metrics;
    }
    
    /**
     * Zaman aralığı parametrelerini hesapla
     */
    private function _get_time_range_params($time_range) {
        $now = new DateTime();
        $params = [];
        
        switch ($time_range) {
            case '1h':
                $params['end'] = $now->format('Y-m-d H:i:s');
                $params['start'] = (clone $now)->modify('-1 hour')->format('Y-m-d H:i:s');
                $params['prev_end'] = (clone $now)->modify('-1 hour')->format('Y-m-d H:i:s');
                $params['prev_start'] = (clone $now)->modify('-2 hours')->format('Y-m-d H:i:s');
                break;
            case '6h':
                $params['end'] = $now->format('Y-m-d H:i:s');
                $params['start'] = (clone $now)->modify('-6 hours')->format('Y-m-d H:i:s');
                $params['prev_end'] = (clone $now)->modify('-6 hours')->format('Y-m-d H:i:s');
                $params['prev_start'] = (clone $now)->modify('-12 hours')->format('Y-m-d H:i:s');
                break;
            case '24h':
                $params['end'] = $now->format('Y-m-d H:i:s');
                $params['start'] = (clone $now)->modify('-1 day')->format('Y-m-d H:i:s');
                $params['prev_end'] = (clone $now)->modify('-1 day')->format('Y-m-d H:i:s');
                $params['prev_start'] = (clone $now)->modify('-2 days')->format('Y-m-d H:i:s');
                break;
            case '7d':
                $params['end'] = $now->format('Y-m-d H:i:s');
                $params['start'] = (clone $now)->modify('-7 days')->format('Y-m-d H:i:s');
                $params['prev_end'] = (clone $now)->modify('-7 days')->format('Y-m-d H:i:s');
                $params['prev_start'] = (clone $now)->modify('-14 days')->format('Y-m-d H:i:s');
                break;
            case '30d':
                $params['end'] = $now->format('Y-m-d H:i:s');
                $params['start'] = (clone $now)->modify('-30 days')->format('Y-m-d H:i:s');
                $params['prev_end'] = (clone $now)->modify('-30 days')->format('Y-m-d H:i:s');
                $params['prev_start'] = (clone $now)->modify('-60 days')->format('Y-m-d H:i:s');
                break;
            default:
                // Varsayılan: Son 1 saat
                $params['end'] = $now->format('Y-m-d H:i:s');
                $params['start'] = (clone $now)->modify('-1 hour')->format('Y-m-d H:i:s');
                $params['prev_end'] = (clone $now)->modify('-1 hour')->format('Y-m-d H:i:s');
                $params['prev_start'] = (clone $now)->modify('-2 hours')->format('Y-m-d H:i:s');
        }
        
        return $params;
    }
    
    /**
     * Son aktiviteleri getir
     */
    private function _get_recent_activities($limit = 10) {
        $activities = [];
        
        // Son referans kayıtları (Son 24 saat)
        $recent_referrals = $this->db
            ->select('ur.created_at, u1.name as referrer_name, u1.ref_code, u2.name as referred_name')
            ->from('user_references ur')
            ->join('user u1', 'ur.referrer_id = u1.id')
            ->join('user u2', 'ur.buyer_id = u2.id')
            ->where('ur.is_active', 1)
            ->where('ur.created_at >', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->order_by('ur.created_at', 'DESC')
            ->limit($limit / 2)
            ->get()
            ->result();
        
        foreach ($recent_referrals as $ref) {
            $activities[] = [
                'type' => 'referral',
                'created_at' => $ref->created_at,
                'user_name' => $ref->referrer_name,
                'description' => "yeni referans kazandı: {$ref->referred_name}",
                'amount' => null
            ];
        }
        
        // Son bonus ödemeleri (Son 24 saat)
        $recent_bonuses = $this->db
            ->select('rbh.created_at, rbh.bonus_amount, rbh.bonus_type, u1.name as referrer_name, u2.name as referred_name')
            ->from('reference_bonus_history rbh')
            ->join('user u1', 'rbh.referrer_id = u1.id')
            ->join('user u2', 'rbh.referred_user_id = u2.id')
            ->where('rbh.status', 'paid')
            ->where('rbh.created_at >', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->order_by('rbh.created_at', 'DESC')
            ->limit($limit / 2)
            ->get()
            ->result();
        
        foreach ($recent_bonuses as $bonus) {
            $bonus_type_text = $bonus->bonus_type == 'register' ? 'kayıt' : 'alışveriş';
            $activities[] = [
                'type' => 'bonus',
                'created_at' => $bonus->created_at,
                'user_name' => $bonus->referrer_name,
                'description' => "{$bonus->referred_name} için {$bonus_type_text} bonusu aldı",
                'amount' => $bonus->bonus_amount
            ];
        }
        
        // Zaman sırasına göre sırala
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($activities, 0, $limit);
    }
    

    /**
     * Chart data hazırla
     */
    private function _get_chart_data($time_range = '1h', $chart_type = 'combined') {
        $labels = [];
        $referrals_data = [];
        $bonuses_data = [];
        $revenue_data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $time = date('H:i', strtotime("-{$i}5 minutes"));
            $start = date('Y-m-d H:i:s', strtotime("-{$i}5 minutes"));
            $end = date('Y-m-d H:i:s', strtotime("-" . ($i-1) . "5 minutes"));
            
            $labels[] = $time;
            
            // Referanslar
            $referral_count = $this->db->where('created_at >=', $start)
                                       ->where('created_at <', $end)
                                       ->where('is_active', 1)
                                       ->count_all_results('user_references');
            $referrals_data[] = $referral_count;
            
            // Bonuslar
            $bonus = $this->db->select_sum('bonus_amount')
                             ->where('created_at >=', $start)
                             ->where('created_at <', $end)
                             ->where('status', 'paid')
                             ->get('reference_bonus_history')
                             ->row();
            $bonuses_data[] = $bonus ? (float)$bonus->bonus_amount : 0;
            
            // Gelir (referanslardan gelen alışverişler)
            $revenue = $this->db->select('SUM(i.price) as total_revenue')
                               ->from('invoice i')
                               ->join('shop s', 's.id = i.shop_id')
                               ->join('user_references ur', 's.user_id = ur.buyer_id')
                               ->where('s.date >=', $start)
                               ->where('s.date <', $end)
                               ->where('ur.is_active', 1)
                               ->get()
                               ->row();
            $revenue_data[] = $revenue ? (float)$revenue->total_revenue : 0;
        }
        
        return [
            'labels' => $labels,
            'referrals' => $referrals_data,
            'bonuses' => $bonuses_data,
            'revenue' => $revenue_data
        ];
    }


    /**
     * AJAX - Haftalık kazananları getir
     */
    public function get_weekly_winners() {
        $this->output->set_content_type('application/json');
        
        try {
            // POST ile gelen week_offset değerini al
            $input = json_decode($this->input->raw_input_stream, true);
            $week_offset = isset($input['week_offset']) ? (int)$input['week_offset'] : 0;
            
            // Hedef haftanın tarih aralığını hesapla
            $date_range = $this->_calculate_week_range($week_offset);
            
            // Haftalık kazananları hesapla
            $winners = $this->_get_weekly_winners_data($date_range, $week_offset);
            
            // İstatistikleri hesapla
            $stats = $this->_get_weekly_stats($date_range);
            
            // Eğer winners boş ise object olarak gönder
            if (empty($winners)) {
                $winners = new stdClass(); // Boş object
            }
            
            echo json_encode([
                'success' => true,
                'winners' => $winners,
                'stats' => $stats,
                'week_range' => $date_range,
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Haftalık veriler yüklenirken hata oluştu: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Hafta aralığını hesapla (Pazartesi - Pazar)
     */
    private function _calculate_week_range($week_offset = 0) {
        $now = new DateTime();
        
        // Bu haftanın pazartesini bul
        $day_of_week = $now->format('N'); // 1=Pazartesi, 7=Pazar
        $days_to_monday = $day_of_week - 1;
        
        $current_monday = clone $now;
        $current_monday->sub(new DateInterval('P' . $days_to_monday . 'D'));
        $current_monday->setTime(0, 0, 0);
        
        // Hedef haftaya git
        $target_monday = clone $current_monday;
        if ($week_offset !== 0) {
            $days = abs($week_offset * 7);
            if ($week_offset > 0) {
                $target_monday->add(new DateInterval('P' . $days . 'D'));
            } else {
                $target_monday->sub(new DateInterval('P' . $days . 'D'));
            }
        }
        
        $target_sunday = clone $target_monday;
        $target_sunday->add(new DateInterval('P6D'));
        $target_sunday->setTime(23, 59, 59);
        
        return [
            'start' => $target_monday->format('Y-m-d H:i:s'),
            'end' => $target_sunday->format('Y-m-d H:i:s'),
            'start_display' => $target_monday->format('Y-m-d'),
            'end_display' => $target_sunday->format('Y-m-d')
        ];
    }

    /**
     * Haftalık kazananları hesapla
     */
    private function _get_weekly_winners_data($date_range, $week_offset = 0) {
        $winners = [];
        
        // 1. Referans Şampiyonu (En çok referans getiren)
        $champion_query = $this->db->select('u.id, u.name, u.surname, COUNT(ur.id) as referral_count')
                                   ->from('user u')
                                   ->join('user_references ur', 'u.id = ur.referrer_id')
                                   ->where('ur.created_at >=', $date_range['start'])
                                   ->where('ur.created_at <=', $date_range['end'])
                                   ->where('ur.is_active', 1)
                                   ->group_by('u.id, u.name, u.surname')
                                   ->having('referral_count >', 0)
                                   ->order_by('referral_count', 'DESC')
                                   ->limit(1)
                                   ->get()
                                   ->row();
        
        if ($champion_query) {
            // Önceki haftadaki performansını da hesapla  
            $prev_week = $this->_calculate_week_range($week_offset - 1);
            $champion_prev = $this->db->select('COUNT(ur.id) as referral_count')
                                      ->from('user_references ur')
                                      ->where('ur.referrer_id', $champion_query->id)
                                      ->where('ur.created_at >=', $prev_week['start'])
                                      ->where('ur.created_at <=', $prev_week['end'])
                                      ->where('ur.is_active', 1)
                                      ->get()
                                      ->row();
            
            $prev_count = $champion_prev ? (int)$champion_prev->referral_count : 0;
            $current_count = (int)$champion_query->referral_count;
            
            // Trend hesaplama - önceki hafta 0 ise %100+ artış
            if ($prev_count == 0 && $current_count > 0) {
                $trend_percent = 100; // Sıfırdan bir şeye çıkması %100+ artış
            } else if ($prev_count > 0) {
                $trend_percent = round(($current_count - $prev_count) / $prev_count * 100, 1);
            } else {
                $trend_percent = 0;
            }
            
            $winners['champion'] = [
                'name' => $champion_query->name,
                'surname' => $champion_query->surname,
                'referral_count' => (int)$champion_query->referral_count,
                'previous_week' => $prev_count,
                'trend_percent' => $trend_percent
            ];
        }
        
        // 2. En Çok Kazanan (En yüksek bonus)
        $earner_query = $this->db->select('u.id, u.name, u.surname, COALESCE(SUM(rbh.bonus_amount), 0) as total_earned')
                                 ->from('user u')
                                 ->join('reference_bonus_history rbh', 'u.id = rbh.referrer_id')
                                 ->where('rbh.created_at >=', $date_range['start'])
                                 ->where('rbh.created_at <=', $date_range['end'])
                                 ->where('rbh.status', 'paid')
                                 ->group_by('u.id, u.name, u.surname')
                                 ->having('total_earned >', 0)
                                 ->order_by('total_earned', 'DESC')
                                 ->limit(1)
                                 ->get()
                                 ->row();
        
        if ($earner_query) {
            // Önceki haftaki kazancını da hesapla
            $prev_week = $this->_calculate_week_range($week_offset - 1);
            $earner_prev = $this->db->select('COALESCE(SUM(rbh.bonus_amount), 0) as total_earned')
                                    ->from('reference_bonus_history rbh')
                                    ->where('rbh.referrer_id', $earner_query->id)
                                    ->where('rbh.created_at >=', $prev_week['start'])
                                    ->where('rbh.created_at <=', $prev_week['end'])
                                    ->where('rbh.status', 'paid')
                                    ->get()
                                    ->row();
            
            $prev_earned = $earner_prev ? (float)$earner_prev->total_earned : 0;
            $current_earned = (float)$earner_query->total_earned;
            
            // Trend hesaplama - önceki hafta 0 ise %100+ artış
            if ($prev_earned == 0 && $current_earned > 0) {
                $trend_percent = 100; // Sıfırdan bir şeye çıkması %100+ artış
            } else if ($prev_earned > 0) {
                $trend_percent = round(($current_earned - $prev_earned) / $prev_earned * 100, 1);
            } else {
                $trend_percent = 0;
            }
            
            $winners['earner'] = [
                'name' => $earner_query->name,
                'surname' => $earner_query->surname,
                'total_earned' => (float)$earner_query->total_earned,
                'previous_week' => $prev_earned,
                'trend_percent' => $trend_percent
            ];
        }
        
        // 3. Yükselen Yıldız (En fazla artış gösteren)
        $rising_query = $this->db->query("
            SELECT u.id, u.name, u.surname,
                   current_week.referral_count as current_count,
                   COALESCE(prev_week.referral_count, 0) as prev_count,
                   CASE 
                       WHEN COALESCE(prev_week.referral_count, 0) > 0 
                       THEN ROUND(((current_week.referral_count - COALESCE(prev_week.referral_count, 0)) / COALESCE(prev_week.referral_count, 1)) * 100, 1)
                       ELSE CASE WHEN current_week.referral_count > 0 THEN 100 ELSE 0 END
                   END as growth_percent
            FROM user u
            INNER JOIN (
                SELECT ur.referrer_id, COUNT(*) as referral_count
                FROM user_references ur
                WHERE ur.created_at >= ? AND ur.created_at <= ? AND ur.is_active = 1
                GROUP BY ur.referrer_id
            ) current_week ON u.id = current_week.referrer_id
            LEFT JOIN (
                SELECT ur.referrer_id, COUNT(*) as referral_count
                FROM user_references ur
                WHERE ur.created_at >= ? AND ur.created_at <= ? AND ur.is_active = 1
                GROUP BY ur.referrer_id
            ) prev_week ON u.id = prev_week.referrer_id
            WHERE current_week.referral_count > 0
            ORDER BY growth_percent DESC
            LIMIT 1
        ", [
            $date_range['start'], $date_range['end'],
            $this->_calculate_week_range($week_offset - 1)['start'], $this->_calculate_week_range($week_offset - 1)['end']
        ])->row();
        
        if ($rising_query) {
            $winners['rising'] = [
                'name' => $rising_query->name,
                'surname' => $rising_query->surname,
                'growth_percent' => (float)$rising_query->growth_percent,
                'new_referrals' => (int)$rising_query->current_count
            ];
        }
        
        // 4. En İstikrarlı (En düzenli referans getiren)
        $consistent_query = $this->db->query("
            SELECT u.id, u.name, u.surname,
                   COUNT(DISTINCT DATE(ur.created_at)) as active_days,
                   COUNT(ur.id) as total_referrals,
                   ROUND(COUNT(ur.id) / COUNT(DISTINCT DATE(ur.created_at)), 1) as daily_average,
                   -- İstikrar skoru: aktif gün sayısı * günlük ortalama
                   (COUNT(DISTINCT DATE(ur.created_at)) * ROUND(COUNT(ur.id) / COUNT(DISTINCT DATE(ur.created_at)), 1)) as consistency_score
            FROM user u
            INNER JOIN user_references ur ON u.id = ur.referrer_id
            WHERE ur.created_at >= ? AND ur.created_at <= ?
              AND ur.is_active = 1
            GROUP BY u.id, u.name, u.surname
            HAVING total_referrals >= 1 AND active_days >= 1
            ORDER BY consistency_score DESC, active_days DESC, daily_average DESC
            LIMIT 1
        ", [$date_range['start'], $date_range['end']])->row();
        
        if ($consistent_query) {
            $winners['consistent'] = [
                'name' => $consistent_query->name,
                'surname' => $consistent_query->surname,
                'active_days' => (int)$consistent_query->active_days,
                'daily_average' => (float)$consistent_query->daily_average
            ];
        }
        
        return $winners;
    }

    /**
     * Haftalık istatistikleri hesapla
     */
    private function _get_weekly_stats($date_range) {
        // Toplam katılımcı sayısı
        $participants = $this->db->select('COUNT(DISTINCT ur.referrer_id) as total')
                                 ->from('user_references ur')
                                 ->where('ur.created_at >=', $date_range['start'])
                                 ->where('ur.created_at <=', $date_range['end'])
                                 ->where('ur.is_active', 1)
                                 ->get()
                                 ->row();
        
        $total_participants = $participants ? (int)$participants->total : 0;
        
        return [
            'total_participants' => $total_participants
        ];
    }

    /**
     * En iyi referans verenleri getir
     */
    private function _get_top_referrers($limit = 10) {
        $query = $this->db->select('u.id, u.name, u.surname, u.ref_code, 
                                   COUNT(ur.id) as referral_count,
                                   COALESCE(SUM(rbh.bonus_amount), 0) as total_earned')
                         ->from('user u')
                         ->join('user_references ur', 'u.id = ur.referrer_id', 'left')
                         ->join('reference_bonus_history rbh', 'u.id = rbh.referrer_id AND rbh.status = "paid"', 'left')
                         ->where('ur.is_active', 1)
                         ->group_by('u.id, u.name, u.surname, u.ref_code')
                         ->having('referral_count >', 0)
                         ->order_by('referral_count', 'DESC')
                         ->limit($limit)
                         ->get();
        
        return $query->result();
    }
}
