<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Reference extends G_Controller {

    // Tekrarlanan verileri saklayacak sınıf değişkenleri
    protected $properties;
    protected $category;
    protected $pages;
    protected $footerBlog;
    protected $footerPage;
    protected $footerProduct;

    /**
     * İsmi sansürle - Her kelimenin ilk 2 karakterini göster
     */
    private function _censorName($name) {
        if (empty($name)) return '';
        $words = explode(' ', $name);
        $censored = array_map(function($word) {
            if (mb_strlen($word) <= 2) return $word;
            return mb_substr($word, 0, 2) . '***';
        }, $words);
        return implode(' ', $censored);
    }

    /**
     * Email sansürle - @ öncesinin ilk 4 karakterini göster
     */
    private function _censorEmail($email) {
        if (empty($email)) return '';
        $parts = explode('@', $email);
        if (count($parts) != 2) return $email;
        
        $username = $parts[0];
        $domain = $parts[1];
        
        if (mb_strlen($username) <= 4) {
            return $username . '****@' . $domain;
        }
        
        return mb_substr($username, 0, 4) . '****@' . $domain;
    }

    /**
     * Kullanıcı nesnesini sansürle (name, surname, email)
     */
    private function _censorUserData($user) {
        if (empty($user)) return $user;
        
        // Object veya array olabilir
        $isObject = is_object($user);
        
        if ($isObject) {
            if (isset($user->name)) {
                $user->name = $this->_censorName($user->name);
            }
            if (isset($user->surname)) {
                $user->surname = $this->_censorName($user->surname);
            }
            if (isset($user->email)) {
                $user->email = $this->_censorEmail($user->email);
            }
        } else {
            if (isset($user['name'])) {
                $user['name'] = $this->_censorName($user['name']);
            }
            if (isset($user['surname'])) {
                $user['surname'] = $this->_censorName($user['surname']);
            }
            if (isset($user['email'])) {
                $user['email'] = $this->_censorEmail($user['email']);
            }
        }
        
        return $user;
    }

    public function __construct()
    {
        parent::__construct();
        if (!isset($this->session->userdata('info')['id'])) {
            flash('Ups.', 'Yetkin Olmayan Bir Yere Giriş Yapmaya Çalışıyorsun.');
            redirect(base_url(), 'refresh');
            exit;
        }

        $this->properties = $this->db->where('id', 1)->get('properties')->row();
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        if ($this->properties->isConfirmTc == 1 && $user->tc == "11111111111") {
            flash('Eksik Bilgiler.', 'Lütfen üyeliğindeki eksik bilgileri tamamla.');
            redirect(base_url('tc-dogrulama'), 'refresh');
        }

        // Tekrarlanan verileri yükle
        $this->load->library('Referral_System');
        $this->category = getActiveCategories();
        $this->pages = $this->db->get('pages')->result();
        $this->footerBlog = $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result();
        $this->footerPage = $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result();
        $this->footerProduct = $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result();
    }

    public function index($tab = 'dashboard')
    {
        addlog('reference', 'Sayfa ziyaret edildi: Referans - ' . $tab);
        
        $user_id = $this->session->userdata('info')['id'];
        
        // Referral system bilgilerini al
        $referral_info = $this->referral_system->getUserReferralInfo($user_id);
        $referral_stats = $this->referral_system->getReferralStats($user_id);
        $referral_settings = $this->referral_system->getSettings();
        
        // Referans geçmişini al
        $referral_history = $this->referral_system->getReferralHistory($user_id, 10);
        
        // Referans linkini oluştur
        $referral_link = $this->referral_system->getReferralLink($user_id);
        
        // Referral info içindeki kullanıcı bilgilerini sansürle
        if (isset($referral_info['referrer_info']) && !empty($referral_info['referrer_info'])) {
            $referral_info['referrer_info'] = $this->_censorUserData($referral_info['referrer_info']);
        }
        
        if (isset($referral_info['referred_users']) && is_array($referral_info['referred_users'])) {
            foreach ($referral_info['referred_users'] as &$user) {
                $user = $this->_censorUserData($user);
            }
        }
        
        // Referans geçmişini sansürle
        if (is_array($referral_history)) {
            foreach ($referral_history as &$history_item) {
                $history_item = $this->_censorUserData($history_item);
            }
        }
        
        $data = [
            'properties' => $this->properties,
            'category' => $this->category,
            'pages' => $this->pages,
            'footerBlog' => $this->footerBlog,
            'footerPage' => $this->footerPage,
            'footerProduct' => $this->footerProduct,
            'mini' => 1,
            'active_tab' => $tab,
            'referral_info' => $referral_info,
            'referral_stats' => $referral_stats,
            'referral_settings' => $referral_settings,
            'referral_history' => $referral_history,
            'referral_link' => $referral_link,
            // Eski veriler geriye uyumluluk için
            'referrer_user' => $referral_info['referrer_info'] ?? null,
            'references' => $referral_info['referred_users'] ?? [],
            'refcode' => $referral_info['referral_code'] ?? false
        ];

        $this->clientView('reference', $data);
    }

    public function createRefcode()
    {
        addlog('createRefcode', 'Sayfa ziyaret edildi: Referans kodu oluşturma');
        
        if (!$this->referral_system->isSystemEnabled()) {
            flash('Hata', 'Referans sistemi şu anda aktif değil.');
            redirect(base_url('client/reference'), 'refresh');
            return;
        }
        
        $user_id = $this->session->userdata('info')['id'];
        $ref_code = $this->referral_system->getUserReferralCode($user_id);
        
        if ($ref_code) {
            flash('Başarılı', 'Referans kodunuz: ' . $ref_code);
        } else {
            flash('Hata', 'Referans kodu oluşturulamadı. Lütfen tekrar deneyin.');
        }
        
        redirect(base_url('client/reference'), 'refresh');
    }

    /**
     * Referans kodunu ayarlama (yeni kullanıcılar için)
     */
    public function setReferrer()
    {
        addlog('setReferrer', 'Referans kodu girme işlemi');
        
        if (!$this->referral_system->isSystemEnabled()) {
            flash('Hata', 'Referans sistemi şu anda aktif değil.');
            redirect(base_url('client/reference'), 'refresh');
            return;
        }
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('ref_code', 'Referans Kodu', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            flash('Hata', validation_errors());
            redirect(base_url('client/reference'), 'refresh');
            return;
        }
        
        $ref_code = $this->input->post('ref_code');
        $user_id = $this->session->userdata('info')['id'];
        
        // Referans veren kullanıcıyı bul
        $referrer = $this->referral_system->getUserByReferralCode($ref_code);
        
        if (!$referrer) {
            flash('Hata', 'Geçersiz referans kodu.');
            redirect(base_url('client/reference'), 'refresh');
            return;
        }
        
        // Referans ilişkisi kur
        $result = $this->referral_system->createReferralRelation($referrer->id, $user_id);
        
        if ($result['success']) {
            flash('Başarılı', $result['message']);
        } else {
            flash('Hata', $result['message']);
        }
        
        redirect(base_url('client/reference'), 'refresh');
    }

    /**
     * Kendi referans kodunu değiştirme
     */
    public function changeReferralCode()
    {
        $this->output->set_content_type('application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
            return;
        }
        
        if (!$this->referral_system->isSystemEnabled()) {
            echo json_encode(['success' => false, 'message' => 'Referans sistemi şu anda aktif değil.']);
            return;
        }
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('new_ref_code', 'Yeni Referans Kodu', 'required|trim|min_length[3]|max_length[20]|alpha_numeric');
        
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => strip_tags(validation_errors())]);
            return;
        }
        
        $user_id = $this->session->userdata('info')['id'];
        $new_ref_code = strtoupper($this->input->post('new_ref_code'));
        
        // Mevcut referans kodunu al
        $current_ref_code = $this->referral_system->getUserReferralCode($user_id);
        
        if (!$current_ref_code) {
            echo json_encode(['success' => false, 'message' => 'Henüz bir referans kodunuz yok. Önce referans kodu oluşturun.']);
            return;
        }
        
        if ($current_ref_code === $new_ref_code) {
            echo json_encode(['success' => false, 'message' => 'Yeni referans kodu mevcut kodunuzla aynı olamaz.']);
            return;
        }
        
        // Yeni kodun kullanımda olup olmadığını kontrol et
        $existing_user = $this->referral_system->getUserByReferralCode($new_ref_code);
        if ($existing_user) {
            echo json_encode(['success' => false, 'message' => 'Bu referans kodu zaten kullanımda. Lütfen farklı bir kod seçin.']);
            return;
        }
        
        // Değişiklik geçmişini kontrol et (güvenlik için)
            $change_history = $this->_getUserReferralCodeChangeHistory($user_id);
        
        // Son 30 gün içinde ayarlarda belirtilen adetten fazla değişiklik yapılmış mı?
        $recent_changes = 0;
        foreach ($change_history as $change) {
            if (strtotime($change->created_at) > strtotime('-30 days')) {
                $recent_changes++;
            }
        }
        
        $maxPer30 = (int) $this->referral_system->getSettings()['ref_code_change_max_per_30_days'];
        if ($recent_changes >= $maxPer30) {
            echo json_encode(['success' => false, 'message' => 'Son 30 gün içinde en fazla ' . $maxPer30 . ' kez referans kodu değiştirebilirsiniz.']);
            return;
        }
        
        // Son değişiklikten beri cooldown (gün) geçmiş mi? (admin ayarlarından)
        $cooldownDays = (int) $this->referral_system->getSettings()['ref_code_change_cooldown_days'];
        if (!empty($change_history) && strtotime($change_history[0]->created_at) > strtotime('-' . $cooldownDays . ' days')) {
            $next_change_date = date('d.m.Y', strtotime($change_history[0]->created_at . ' +' . $cooldownDays . ' days'));
            echo json_encode(['success' => false, 'message' => "Referans kodunuzu tekrar değiştirebilmek için {$next_change_date} tarihine kadar beklemeniz gerekiyor."]);
            return;
        }
        
        // Değişikliği kaydet
        $result = $this->_updateUserReferralCode($user_id, $current_ref_code, $new_ref_code);
        
        if ($result['success']) {
            addlog('changeReferralCode', "Referans kodu değiştirildi: {$current_ref_code} -> {$new_ref_code}");
            echo json_encode([
                'success' => true, 
                'message' => 'Referans kodunuz başarıyla değiştirildi!',
                'new_code' => $new_ref_code
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => $result['message']]);
        }
    }
    
    /**
     * Kullanıcının referans kodu değişiklik geçmişini getir
     */
    private function _getUserReferralCodeChangeHistory($user_id)
    {
        // Tablo varlığını kontrol et; yoksa boş dizi dön
        try {
            $query = $this->db->query("SHOW TABLES LIKE 'user_referral_code_changes'");
            if ($query->num_rows() === 0) {
                return [];
            }
        } catch (Exception $e) {
            return [];
        }

        return $this->db->where('user_id', $user_id)
                        ->order_by('created_at', 'DESC')
                        ->get('user_referral_code_changes')
                        ->result();
    }
    
    /**
     * Kullanıcının referans kodunu güncelle
     */
    private function _updateUserReferralCode($user_id, $old_code, $new_code)
    {
        $this->db->trans_start();
        
        try {
            // user tablosundaki referans kodunu güncelle
            $this->db->where('id', $user_id)
                     ->update('user', ['ref_code' => $new_code]);
            
            // Değişiklik geçmişini kaydet
            $change_data = [
                'user_id' => $user_id,
                'old_code' => $old_code,
                'new_code' => $new_code,
                'created_at' => date('Y-m-d H:i:s'),
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent()
            ];
            
            // Tablo var ise geçmişe kaydet
            try {
                $query = $this->db->query("SHOW TABLES LIKE 'user_referral_code_changes'");
                if ($query->num_rows() > 0) {
                    $this->db->insert('user_referral_code_changes', $change_data);
                }
            } catch (Exception $e) {
                // yoksay
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                return ['success' => false, 'message' => 'Veritabanı hatası oluştu.'];
            }
            
            return ['success' => true, 'message' => 'Referans kodu başarıyla güncellendi.'];
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            addlog('changeReferralCode', 'Hata: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Bir hata oluştu. Lütfen tekrar deneyin.'];
        }
    }
    
    /**
     * Referans kodu değişiklik geçmişini AJAX ile getir
     */
    public function getReferralCodeChangeHistory()
    {
        $this->output->set_content_type('application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
            return;
        }
        
        $user_id = $this->session->userdata('info')['id'];
        
        try {
            // Tablo kontrol et - alternatif yöntemle
            $table_exists = false;
            try {
                $query = $this->db->query("SHOW TABLES LIKE 'user_referral_code_changes'");
                $table_exists = $query->num_rows() > 0;
            } catch (Exception $e) {
                // Tablo kontrolü başarısız, boş sonuç dön
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'message' => 'Henüz değişiklik geçmişi bulunmuyor.'
                ]);
                return;
            }
            
            if (!$table_exists) {
                echo json_encode([
                    'success' => true,
                    'data' => [],
                    'message' => 'Henüz değişiklik geçmişi bulunmuyor.'
                ]);
                return;
            }
            
            // Kullanıcının referans kodu değişiklik geçmişini getir
            $history = $this->db->select('old_code, new_code, created_at, ip_address')
                               ->where('user_id', $user_id)
                               ->order_by('created_at', 'DESC')
                               ->limit(20) // Son 20 değişiklik
                               ->get('user_referral_code_changes')
                               ->result();
            
            // Tarihleri Türkçe formatta düzenle
            foreach ($history as &$item) {
                $item->formatted_date = $this->_formatDateTurkish(strtotime($item->created_at), 'detailed');
                $item->relative_time = $this->_getRelativeTime($item->created_at);
            }
            
            echo json_encode([
                'success' => true,
                'data' => $history,
                'total_changes' => count($history)
            ]);
            
        } catch (Exception $e) {
            addlog('getReferralCodeChangeHistory', 'Hata: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Geçmiş yüklenirken hata oluştu.'
            ]);
        }
    }
    
    /**
     * Göreceli zaman hesapla (örn: "2 saat önce")
     */
    private function _getRelativeTime($datetime)
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) {
            return 'Az önce';
        } elseif ($time < 3600) {
            $minutes = floor($time / 60);
            return $minutes . ' dakika önce';
        } elseif ($time < 86400) {
            $hours = floor($time / 3600);
            return $hours . ' saat önce';
        } elseif ($time < 2592000) {
            $days = floor($time / 86400);
            return $days . ' gün önce';
        } elseif ($time < 31536000) {
            $months = floor($time / 2592000);
            return $months . ' ay önce';
        } else {
            $years = floor($time / 31536000);
            return $years . ' yıl önce';
        }
    }
    
    /**
     * Referans geçmişini AJAX ile getir
     */
    public function getReferralHistory()
    {
        $this->output->set_content_type('application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
            return;
        }
        
        $user_id = $this->session->userdata('info')['id'];
        $limit = $this->input->get('limit') ?: 20;
        $offset = $this->input->get('offset') ?: 0;
        
        $history = $this->referral_system->getReferralHistory($user_id, $limit, $offset);
        
        // Geçmiş kayıtlarındaki kullanıcı bilgilerini sansürle
        if (is_array($history)) {
            foreach ($history as &$history_item) {
                $history_item = $this->_censorUserData($history_item);
            }
        }
        
        echo json_encode([
            'success' => true,
            'data' => $history
        ]);
    }
    
    /**
     * Detaylı referans istatistiklerini AJAX ile getir
     */
    public function getReferralDetailedStats()
    {
        $this->output->set_content_type('application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
            return;
        }
        
        $user_id = $this->session->userdata('info')['id'];
        $period = $this->input->get('period') ?: 'week'; // week, month, year
        
        $stats = $this->_getDetailedReferralStats($user_id, $period);
        
        echo json_encode([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Referans kullanıcılarını arama ve filtreleme ile getir
     */
    public function searchReferralUsers()
    {
        $this->output->set_content_type('application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
            return;
        }
        
        $user_id = $this->session->userdata('info')['id'];
        $search = $this->input->get('search') ?: '';
        $page = $this->input->get('page') ?: 1;
        $per_page = 10;
        $offset = ($page - 1) * $per_page;
        
        // Referans edilen kullanıcıları ara
        $this->db->select('ur.*, u.name, u.surname, u.email, ur.created_at as relation_date, ur.bonus_earned');
        $this->db->from('user_references ur');
        $this->db->join('user u', 'ur.buyer_id = u.id');
        $this->db->where('ur.referrer_id', $user_id);
        $this->db->where('ur.is_active', 1);
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('u.name', $search);
            $this->db->or_like('u.surname', $search);
            $this->db->or_like('u.email', $search);
            $this->db->group_end();
        }
        
        // Toplam kayıt sayısı
        $total_query = clone $this->db;
        $total = $total_query->count_all_results();
        
        // Sayfalama ile veri getir
        $this->db->order_by('ur.created_at', 'DESC');
        $this->db->limit($per_page, $offset);
        $users = $this->db->get()->result();
        
        // Her kullanıcı için son işlem bilgisini ve alışveriş sayısını al (OPTİMİZE EDİLMİŞ)
        if (!empty($users)) {
            $user_ids = array_column($users, 'buyer_id');

            // Son bonus bilgilerini tek sorgu ile çek
            $last_bonuses = $this->db->select('referred_user_id, bonus_amount, created_at, description')
                                    ->where('referrer_id', $user_id)
                                    ->where_in('referred_user_id', $user_ids)
                                    ->order_by('created_at', 'DESC')
                                    ->get('reference_bonus_history')
                                    ->result();

            // Bonus verilerini kullanıcı ID'ye göre grupla
            $last_bonus_map = [];
            foreach ($last_bonuses as $bonus) {
                if (!isset($last_bonus_map[$bonus->referred_user_id])) {
                    $last_bonus_map[$bonus->referred_user_id] = $bonus;
                }
            }

            // Referans dönemlerini tek sorgu ile çek
            $reference_periods = $this->db->select('buyer_id, created_at, updated_at, is_active')
                                        ->where('referrer_id', $user_id)
                                        ->where_in('buyer_id', $user_ids)
                                        ->order_by('created_at', 'ASC')
                                        ->get('user_references')
                                        ->result();

            // Referans dönemlerini kullanıcı ID'ye göre grupla
            $period_map = [];
            foreach ($reference_periods as $period) {
                $period_map[$period->buyer_id] = $period;
            }

            // Tüm bonus geçmişini tek sorgu ile çek ve say
            $all_bonuses = $this->db->select('referred_user_id, created_at, bonus_type')
                                  ->where('referrer_id', $user_id)
                                  ->where_in('referred_user_id', $user_ids)
                                  ->where('bonus_type', 'purchase')
                                  ->where('status', 'paid')
                                  ->get('reference_bonus_history')
                                  ->result();

            // Bonus sayılarını hesapla
            $bonus_count_map = [];
            $now = date('Y-m-d H:i:s');
            foreach ($all_bonuses as $bonus) {
                $period = $period_map[$bonus->referred_user_id] ?? null;
                if ($period) {
                    $period_start = $period->created_at;
                    $period_end = ($period->is_active == 1) ? $now : $period->updated_at;

                    if ($bonus->created_at >= $period_start && $bonus->created_at <= $period_end) {
                        $bonus_count_map[$bonus->referred_user_id] = ($bonus_count_map[$bonus->referred_user_id] ?? 0) + 1;
                    }
                }
            }

            // Kullanıcılara verileri ekle ve sansürle
            foreach ($users as &$user) {
                $user->last_bonus = $last_bonus_map[$user->buyer_id] ?? null;
                $user->total_purchases = $bonus_count_map[$user->buyer_id] ?? 0;
                
                // Kullanıcı bilgilerini sansürle
                $user = $this->_censorUserData($user);
            }
        }
        
        $total_pages = ceil($total / $per_page);
        
        echo json_encode([
            'success' => true,
            'data' => $users,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $total_pages,
                'total_records' => $total,
                'per_page' => $per_page
            ]
        ]);
    }
    
    /**
     * Referans raporu verilerini getir (günlük, haftalık, aylık)
     */
    public function getReferralReportData()
    {
        $this->output->set_content_type('application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
            return;
        }
        
        $user_id = $this->session->userdata('info')['id'];
        $period = $this->input->get('period') ?: 'daily'; // daily, weekly, monthly
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');
        
        // Periode göre varsayılan tarih aralığı
        if (!$start_date || !$end_date) {
            if ($period == 'weekly') {
                $start_date = date('Y-m-d', strtotime('-4 weeks'));
                $end_date = date('Y-m-d');
            } elseif ($period == 'monthly') {
                $start_date = date('Y-m-d', strtotime('-6 months'));
                $end_date = date('Y-m-d');
            } else {
                $start_date = date('Y-m-d', strtotime('-30 days'));
                $end_date = date('Y-m-d');
            }
        }
        
        $report_data = [];
        
        if ($period == 'daily') {
            // Günlük rapor
            $current_date = strtotime($start_date);
            $end_timestamp = strtotime($end_date);
            
            while ($current_date <= $end_timestamp) {
                $date = date('Y-m-d', $current_date);
                $day_start = $date . ' 00:00:00';
                $day_end = $date . ' 23:59:59';
                
                // Günlük istatistikler
                $daily_stats = $this->_getDailyReferralStats($user_id, $day_start, $day_end);
                
                // Önceki günle karşılaştırma
                $prev_date = date('Y-m-d', $current_date - 86400);
                $prev_start = $prev_date . ' 00:00:00';
                $prev_end = $prev_date . ' 23:59:59';
                $prev_stats = $this->_getDailyReferralStats($user_id, $prev_start, $prev_end);
                
                $report_data[] = [
                    'date' => $date,
                    'formatted_date' => $this->_formatDateTurkish($current_date, 'daily'),
                    'new_referrals' => $daily_stats['new_referrals'],
                    'total_referrals_change' => $daily_stats['total_referrals'] - $prev_stats['total_referrals'],
                    'total_referrals' => $daily_stats['total_referrals'],
                    'daily_bonus' => $daily_stats['daily_bonus'],
                    'total_bonus_change' => $daily_stats['total_bonus'] - $prev_stats['total_bonus'],
                    'total_bonus' => $daily_stats['total_bonus'],
                    'active_referrals' => $daily_stats['active_referrals'],
                    'conversion_rate' => $daily_stats['conversion_rate']
                ];
                
                $current_date += 86400;
            }
            
        } elseif ($period == 'weekly') {
            // Haftalık rapor
            $current_date = strtotime($start_date);
            $end_timestamp = strtotime($end_date);
            
            while ($current_date <= $end_timestamp) {
                $week_start = date('Y-m-d', strtotime('monday this week', $current_date)) . ' 00:00:00';
                $week_end = date('Y-m-d', strtotime('sunday this week', $current_date)) . ' 23:59:59';
                
                $weekly_stats = $this->_getWeeklyReferralStats($user_id, $week_start, $week_end);
                
                $report_data[] = [
                    'date' => date('Y-m-d', strtotime($week_start)),
                    'formatted_date' => $this->_formatDateTurkish(strtotime($week_start), 'weekly') . ' - ' . $this->_formatDateTurkish(strtotime($week_end), 'weekly'),
                    'new_referrals' => $weekly_stats['new_referrals'],
                    'total_referrals' => $weekly_stats['total_referrals'],
                    'weekly_bonus' => $weekly_stats['weekly_bonus'],
                    'total_bonus' => $weekly_stats['total_bonus'],
                    'active_referrals' => $weekly_stats['active_referrals'],
                    'conversion_rate' => $weekly_stats['conversion_rate']
                ];
                
                $current_date += 604800; // 7 gün
            }
            
        } else {
            // Aylık rapor
            $current_date = strtotime(date('Y-m-01', strtotime($start_date)));
            $end_timestamp = strtotime($end_date);
            
            while ($current_date <= $end_timestamp) {
                $month_start = date('Y-m-01', $current_date) . ' 00:00:00';
                $month_end = date('Y-m-t', $current_date) . ' 23:59:59';
                
                $monthly_stats = $this->_getMonthlyReferralStats($user_id, $month_start, $month_end);
                
                $report_data[] = [
                    'date' => date('Y-m-d', $current_date),
                    'formatted_date' => $this->_formatDateTurkish($current_date, 'monthly'),
                    'new_referrals' => $monthly_stats['new_referrals'],
                    'total_referrals' => $monthly_stats['total_referrals'],
                    'monthly_bonus' => $monthly_stats['monthly_bonus'],
                    'total_bonus' => $monthly_stats['total_bonus'],
                    'active_referrals' => $monthly_stats['active_referrals'],
                    'conversion_rate' => $monthly_stats['conversion_rate']
                ];
                
                $current_date = strtotime('+1 month', $current_date);
            }
        }
        
        // Özet istatistiklerini hesapla
        $summary = [
            'total_referrals' => 0,
            'total_bonus' => 0,
            'avg_daily' => 0,
            'best_day' => '-'
        ];
        
        if (!empty($report_data)) {
            // Toplam referans ve bonus hesapla
            $total_new_referrals = 0;
            $total_bonus_sum = 0;
            $best_day_value = 0;
            $best_day_date = '';
            
            foreach ($report_data as $row) {
                $total_new_referrals += $row['new_referrals'];
                
                // Günlük, haftalık veya aylık bonusa göre toplam bonus hesapla
                if ($period == 'daily') {
                    $daily_bonus_value = $row['daily_bonus'];
                } elseif ($period == 'weekly') {
                    $daily_bonus_value = $row['weekly_bonus'];
                } else {
                    $daily_bonus_value = $row['monthly_bonus'];
                }
                
                $total_bonus_sum += $daily_bonus_value;
                
                // En iyi günü bul
                if ($daily_bonus_value > $best_day_value) {
                    $best_day_value = $daily_bonus_value;
                    $best_day_date = $row['formatted_date'];
                }
            }
            
            // Son veriyi al (en güncel)
            $last_row = end($report_data);
            
            $summary['total_referrals'] = $total_new_referrals;
            $summary['total_bonus'] = number_format($total_bonus_sum, 2, '.', '');
            $summary['avg_daily'] = count($report_data) > 0 ? round($total_new_referrals / count($report_data), 1) : 0;
            $summary['best_day'] = $best_day_date ?: '-';
        }
        
        echo json_encode([
            'success' => true,
            'data' => array_reverse($report_data),
            'period' => $period,
            'total_records' => count($report_data),
            'summary' => $summary
        ]);
    }
    
    /**
     * Günlük referans istatistiklerini hesapla
     */
    private function _getDailyReferralStats($user_id, $start_date, $end_date)
    {
        // Yeni referanslar
        $new_referrals = $this->db->where('referrer_id', $user_id)
                                 ->where('is_active', 1)
                                 ->where('created_at >=', $start_date)
                                 ->where('created_at <=', $end_date)
                                 ->count_all_results('user_references');
        
        // Toplam referanslar (bugüne kadar)
        $total_referrals = $this->db->where('referrer_id', $user_id)
                                   ->where('is_active', 1)
                                   ->where('created_at <=', $end_date)
                                   ->count_all_results('user_references');
        
        // Günlük bonus
        $daily_bonus = $this->db->select_sum('bonus_amount')
                               ->where('referrer_id', $user_id)
                               ->where('status', 'paid')
                               ->where('created_at >=', $start_date)
                               ->where('created_at <=', $end_date)
                               ->get('reference_bonus_history')
                               ->row()
                               ->bonus_amount ?: 0;
        
        // Toplam bonus (bugüne kadar)
        $total_bonus = $this->db->select_sum('bonus_amount')
                               ->where('referrer_id', $user_id)
                               ->where('status', 'paid')
                               ->where('created_at <=', $end_date)
                               ->get('reference_bonus_history')
                               ->row()
                               ->bonus_amount ?: 0;
        
        // Aktif referanslar (alışveriş yapanlar)
        $active_referrals = $this->db->distinct()
                                    ->select('referred_user_id')
                                    ->where('referrer_id', $user_id)
                                    ->where('bonus_type', 'purchase')
                                    ->where('created_at <=', $end_date)
                                    ->count_all_results('reference_bonus_history');
        
        // Konversiyon oranı
        $conversion_rate = $total_referrals > 0 ? round(($active_referrals / $total_referrals) * 100, 2) : 0;
        
        return [
            'new_referrals' => $new_referrals,
            'total_referrals' => $total_referrals,
            'daily_bonus' => $daily_bonus,
            'total_bonus' => $total_bonus,
            'active_referrals' => $active_referrals,
            'conversion_rate' => $conversion_rate
        ];
    }
    
    /**
     * Haftalık referans istatistiklerini hesapla
     */
    private function _getWeeklyReferralStats($user_id, $start_date, $end_date)
    {
        // Haftalık yeni referanslar
        $new_referrals = $this->db->where('referrer_id', $user_id)
                                 ->where('is_active', 1)
                                 ->where('created_at >=', $start_date)
                                 ->where('created_at <=', $end_date)
                                 ->count_all_results('user_references');
        
        // Toplam referanslar
        $total_referrals = $this->db->where('referrer_id', $user_id)
                                   ->where('is_active', 1)
                                   ->where('created_at <=', $end_date)
                                   ->count_all_results('user_references');
        
        // Haftalık bonus
        $weekly_bonus = $this->db->select_sum('bonus_amount')
                                ->where('referrer_id', $user_id)
                                ->where('status', 'paid')
                                ->where('created_at >=', $start_date)
                                ->where('created_at <=', $end_date)
                                ->get('reference_bonus_history')
                                ->row()
                                ->bonus_amount ?: 0;
        
        // Toplam bonus
        $total_bonus = $this->db->select_sum('bonus_amount')
                               ->where('referrer_id', $user_id)
                               ->where('status', 'paid')
                               ->where('created_at <=', $end_date)
                               ->get('reference_bonus_history')
                               ->row()
                               ->bonus_amount ?: 0;
        
        // Aktif referanslar
        $active_referrals = $this->db->distinct()
                                    ->select('referred_user_id')
                                    ->where('referrer_id', $user_id)
                                    ->where('bonus_type', 'purchase')
                                    ->where('created_at <=', $end_date)
                                    ->count_all_results('reference_bonus_history');
        
        // Konversiyon oranı
        $conversion_rate = $total_referrals > 0 ? round(($active_referrals / $total_referrals) * 100, 2) : 0;
        
        return [
            'new_referrals' => $new_referrals,
            'total_referrals' => $total_referrals,
            'weekly_bonus' => $weekly_bonus,
            'total_bonus' => $total_bonus,
            'active_referrals' => $active_referrals,
            'conversion_rate' => $conversion_rate
        ];
    }
    
    /**
     * Aylık referans istatistiklerini hesapla
     */
    private function _getMonthlyReferralStats($user_id, $start_date, $end_date)
    {
        // Aylık yeni referanslar
        $new_referrals = $this->db->where('referrer_id', $user_id)
                                 ->where('is_active', 1)
                                 ->where('created_at >=', $start_date)
                                 ->where('created_at <=', $end_date)
                                 ->count_all_results('user_references');
        
        // Toplam referanslar
        $total_referrals = $this->db->where('referrer_id', $user_id)
                                   ->where('is_active', 1)
                                   ->where('created_at <=', $end_date)
                                   ->count_all_results('user_references');
        
        // Aylık bonus
        $monthly_bonus = $this->db->select_sum('bonus_amount')
                                 ->where('referrer_id', $user_id)
                                 ->where('status', 'paid')
                                 ->where('created_at >=', $start_date)
                                 ->where('created_at <=', $end_date)
                                 ->get('reference_bonus_history')
                                 ->row()
                                 ->bonus_amount ?: 0;
        
        // Toplam bonus
        $total_bonus = $this->db->select_sum('bonus_amount')
                               ->where('referrer_id', $user_id)
                               ->where('status', 'paid')
                               ->where('created_at <=', $end_date)
                               ->get('reference_bonus_history')
                               ->row()
                               ->bonus_amount ?: 0;
        
        // Aktif referanslar
        $active_referrals = $this->db->distinct()
                                    ->select('referred_user_id')
                                    ->where('referrer_id', $user_id)
                                    ->where('bonus_type', 'purchase')
                                    ->where('created_at <=', $end_date)
                                    ->count_all_results('reference_bonus_history');
        
        // Konversiyon oranı
        $conversion_rate = $total_referrals > 0 ? round(($active_referrals / $total_referrals) * 100, 2) : 0;
        
        return [
            'new_referrals' => $new_referrals,
            'total_referrals' => $total_referrals,
            'monthly_bonus' => $monthly_bonus,
            'total_bonus' => $total_bonus,
            'active_referrals' => $active_referrals,
            'conversion_rate' => $conversion_rate
        ];
    }
    
    /**
     * Türkçe tarih formatı
     */
    private function _formatDateTurkish($timestamp, $type = 'daily')
    {
        $turkish_months = [
            'January' => 'Ocak',
            'February' => 'Şubat', 
            'March' => 'Mart',
            'April' => 'Nisan',
            'May' => 'Mayıs',
            'June' => 'Haziran',
            'July' => 'Temmuz',
            'August' => 'Ağustos',
            'September' => 'Eylül',
            'October' => 'Ekim',
            'November' => 'Kasım',
            'December' => 'Aralık'
        ];
        
        $turkish_months_short = [
            'Jan' => 'Oca',
            'Feb' => 'Şub',
            'Mar' => 'Mar', 
            'Apr' => 'Nis',
            'May' => 'May',
            'Jun' => 'Haz',
            'Jul' => 'Tem',
            'Aug' => 'Ağu',
            'Sep' => 'Eyl',
            'Oct' => 'Eki',
            'Nov' => 'Kas',
            'Dec' => 'Ara'
        ];
        
        if ($type == 'daily') {
            // 15 Tem 2025 formatı
            $formatted = date('d M Y', $timestamp);
            $month = date('M', $timestamp);
            return str_replace($month, $turkish_months_short[$month], $formatted);
        } elseif ($type == 'weekly') {
            // 15 Tem formatı (hafta için)
            $formatted = date('d M', $timestamp);
            $month = date('M', $timestamp);
            return str_replace($month, $turkish_months_short[$month], $formatted);
        } elseif ($type == 'detailed') {
            // 15 Temmuz 2025, 14:30 formatı (geçmiş için)
            $formatted = date('d F Y, H:i', $timestamp);
            $month = date('F', $timestamp);
            return str_replace($month, $turkish_months[$month], $formatted);
        } else {
            // Ocak 2025 formatı
            $formatted = date('F Y', $timestamp);
            $month = date('F', $timestamp);
            return str_replace($month, $turkish_months[$month], $formatted);
        }
    }
    
    /**
     * Haftalık/aylık trend verilerini getir
     */
    public function getReferralTrends()
    {
        $this->output->set_content_type('application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
            return;
        }
        
        $user_id = $this->session->userdata('info')['id'];
        $period = $this->input->get('period') ?: 'week'; // week, month
        $type = $this->input->get('type') ?: 'bonus'; // bonus, referrals
        
        $trends = [];
        
        // Türkçe gün ve ay isimleri
        $turkish_days = [
            'Monday' => 'Pzt',
            'Tuesday' => 'Sal',
            'Wednesday' => 'Çar',
            'Thursday' => 'Per',
            'Friday' => 'Cum',
            'Saturday' => 'Cmt',
            'Sunday' => 'Paz'
        ];
        
        $turkish_months_short = [
            'Jan' => 'Oca',
            'Feb' => 'Şub',
            'Mar' => 'Mar', 
            'Apr' => 'Nis',
            'May' => 'May',
            'Jun' => 'Haz',
            'Jul' => 'Tem',
            'Aug' => 'Ağu',
            'Sep' => 'Eyl',
            'Oct' => 'Eki',
            'Nov' => 'Kas',
            'Dec' => 'Ara'
        ];
        
        if ($period == 'week') {
            // Son 7 gün
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $start_date = $date . ' 00:00:00';
                $end_date = $date . ' 23:59:59';
                
                if ($type == 'bonus') {
                    $value = $this->db->select_sum('bonus_amount')
                                     ->where('referrer_id', $user_id)
                                     ->where('status', 'paid')
                                     ->where('created_at >=', $start_date)
                                     ->where('created_at <=', $end_date)
                                     ->get('reference_bonus_history')
                                     ->row()
                                     ->bonus_amount;
                } else {
                    $value = $this->db->where('referrer_id', $user_id)
                                     ->where('is_active', 1)
                                     ->where('created_at >=', $start_date)
                                     ->where('created_at <=', $end_date)
                                     ->count_all_results('user_references');
                }
                
                // Türkçe tarih formatı: "15 Oca (Pzt)"
                $day_name = date('l', strtotime($date)); // Monday, Tuesday, etc.
                $day_name_tr = $turkish_days[$day_name];
                $formatted_date = date('d M', strtotime($date));
                $month = date('M', strtotime($date));
                $formatted_date = str_replace($month, $turkish_months_short[$month], $formatted_date);
                
                $trends[] = [
                    'label' => $formatted_date . ' (' . $day_name_tr . ')',
                    'value' => $value ?: 0
                ];
            }
        } else {
            // Son 6 ay
            for ($i = 5; $i >= 0; $i--) {
                $month_start = date('Y-m-01 00:00:00', strtotime("-$i month"));
                $month_end = date('Y-m-t 23:59:59', strtotime("-$i month"));
                
                if ($type == 'bonus') {
                    $value = $this->db->select_sum('bonus_amount')
                                     ->where('referrer_id', $user_id)
                                     ->where('status', 'paid')
                                     ->where('created_at >=', $month_start)
                                     ->where('created_at <=', $month_end)
                                     ->get('reference_bonus_history')
                                     ->row()
                                     ->bonus_amount;
                } else {
                    $value = $this->db->where('referrer_id', $user_id)
                                     ->where('is_active', 1)
                                     ->where('created_at >=', $month_start)
                                     ->where('created_at <=', $month_end)
                                     ->count_all_results('user_references');
                }
                
                // Türkçe tarih formatı: "Oca 2025"
                $formatted_date = date('M Y', strtotime("-$i month"));
                $month = date('M', strtotime("-$i month"));
                $formatted_date = str_replace($month, $turkish_months_short[$month], $formatted_date);
                
                $trends[] = [
                    'label' => $formatted_date,
                    'value' => $value ?: 0
                ];
            }
        }
        
        echo json_encode([
            'success' => true,
            'data' => $trends
        ]);
    }

    /**
     * Detaylı referans istatistiklerini hesapla
     */
    private function _getDetailedReferralStats($user_id, $period = 'week')
    {
        $stats = [];
        
        // Periode göre tarih aralığı belirle
        if ($period == 'week') {
            $start_date = date('Y-m-d H:i:s', strtotime('-7 days'));
            $end_date = date('Y-m-d H:i:s');
        } elseif ($period == 'month') {
            $start_date = date('Y-m-01 00:00:00');
            $end_date = date('Y-m-t 23:59:59');
        } else {
            $start_date = date('Y-01-01 00:00:00');
            $end_date = date('Y-12-31 23:59:59');
        }
        
        // Dönem içi yeni referanslar
        $stats['new_referrals'] = $this->db->where('referrer_id', $user_id)
                                          ->where('is_active', 1)
                                          ->where('created_at >=', $start_date)
                                          ->where('created_at <=', $end_date)
                                          ->count_all_results('user_references');
        
        // Dönem içi bonus kazancı
        $period_bonus = $this->db->select_sum('bonus_amount')
                                ->where('referrer_id', $user_id)
                                ->where('status', 'paid')
                                ->where('created_at >=', $start_date)
                                ->where('created_at <=', $end_date)
                                ->get('reference_bonus_history')
                                ->row()
                                ->bonus_amount;
        
        $stats['period_bonus'] = $period_bonus ?: 0;
        
        // Aktif referans oranı (alışveriş yapan/toplam)
        $total_referrals = $this->db->where('referrer_id', $user_id)
                                   ->where('is_active', 1)
                                   ->count_all_results('user_references');
        
        $active_buyers = $this->db->distinct()
                                 ->select('rbh.referred_user_id')
                                 ->from('reference_bonus_history rbh')
                                 ->where('rbh.referrer_id', $user_id)
                                 ->where('rbh.bonus_type', 'purchase')
                                 ->count_all_results();
        
        $stats['activity_rate'] = $total_referrals > 0 ? round(($active_buyers / $total_referrals) * 100, 1) : 0;
        
        // En iyi performans gösteren referans
        $best_referral = $this->db->select('u.name, u.surname, SUM(rbh.bonus_amount) as total_bonus')
                                 ->from('reference_bonus_history rbh')
                                 ->join('user u', 'rbh.referred_user_id = u.id')
                                 ->where('rbh.referrer_id', $user_id)
                                 ->where('rbh.status', 'paid')
                                 ->group_by('rbh.referred_user_id')
                                 ->order_by('total_bonus', 'DESC')
                                 ->limit(1)
                                 ->get()
                                 ->row();
        
        // En iyi referans bilgisini sansürle
        if ($best_referral) {
            $best_referral = $this->_censorUserData($best_referral);
        }
        
        $stats['best_referral'] = $best_referral;
        
        return $stats;
    }
    
    /**
     * Referans linkini paylaşım için formatla
     */
    public function shareReferral($platform = 'copy')
    {
        addlog('shareReferral', 'Referans linki paylaşımı: ' . $platform);
        
        $user_id = $this->session->userdata('info')['id'];
        $referral_link = $this->referral_system->getReferralLink($user_id);
        
        if (!$referral_link) {
            flash('Hata', 'Referans linki oluşturulamadı.');
            redirect(base_url('client/reference'), 'refresh');
            return;
        }
        
        $message = "Harika fırsatlarla dolu platforma katıl! " . $referral_link;
        
        switch ($platform) {
            case 'whatsapp':
                $url = "https://wa.me/?text=" . urlencode($message);
                break;
            case 'telegram':
                $url = "https://t.me/share/url?url=" . urlencode($referral_link) . "&text=" . urlencode("Harika fırsatlarla dolu platforma katıl!");
                break;
            case 'twitter':
                $url = "https://twitter.com/intent/tweet?text=" . urlencode($message);
                break;
            case 'facebook':
                $url = "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($referral_link);
                break;
            case 'copy':
                $this->output->set_content_type('application/json');
                echo json_encode([
                    'success' => true,
                    'link' => $referral_link,
                    'message' => 'Referans linki kopyalanmaya hazır!'
                ]);
                return;
            default:
                flash('Hata', 'Geçersiz paylaşım platformu.');
                redirect(base_url('client/reference'), 'refresh');
                return;
        }
        
        redirect($url);
    }
    
    /**
     * Detaylı rapor verilerini getir (modal için)
     */
    public function getDetailedReportData()
    {
        $this->output->set_content_type('application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
            return;
        }
        
        $user_id = $this->session->userdata('info')['id'];
        $period = $this->input->get('period') ?: 'daily';
        $date = $this->input->get('date');
        $type = $this->input->get('type') ?: 'detailed';
        
        if (!$date) {
            echo json_encode(['success' => false, 'message' => 'Tarih parametresi gerekli']);
            return;
        }
        
        // Tarih aralığını belirle
        if ($period == 'daily') {
            $start_date = $date . ' 00:00:00';
            $end_date = $date . ' 23:59:59';
        } elseif ($period == 'weekly') {
            // Haftanın başlangıç ve bitiş günlerini hesapla
            $start_date = date('Y-m-d', strtotime('monday this week', strtotime($date))) . ' 00:00:00';
            $end_date = date('Y-m-d', strtotime('sunday this week', strtotime($date))) . ' 23:59:59';
        } elseif ($period == 'monthly') {
            // Ayın ilk ve son günü
            $start_date = date('Y-m-01', strtotime($date)) . ' 00:00:00';
            $end_date = date('Y-m-t', strtotime($date)) . ' 23:59:59';
        } else {
            echo json_encode(['success' => false, 'message' => 'Geçersiz dönem']);
            return;
        }
        
        // Yeni katılanlar
        $new_joined = $this->_getNewJoinedUsers($user_id, $start_date, $end_date);
        
        // Bonus kazandıranlar
        $bonus_earners = $this->_getBonusEarningUsers($user_id, $start_date, $end_date);
        
        // Ayrılanlar (referans ilişkisi sonlananlar)
        $left_users = $this->_getLeftUsers($user_id, $start_date, $end_date);
        
        echo json_encode([
            'success' => true,
            'data' => [
                'new_joined' => $new_joined,
                'bonus_earners' => $bonus_earners,
                'left_users' => $left_users
            ],
            'debug' => [
                'period' => $period,
                'date_range' => $start_date . ' - ' . $end_date,
                'user_id' => $user_id
            ]
        ]);
    }
    
    /**
     * Belirli dönemde yeni katılan kullanıcıları getir
     */
    private function _getNewJoinedUsers($user_id, $start_date, $end_date)
    {
        $this->db->select('u.name, u.surname, u.email, ur.created_at as join_date');
        $this->db->from('user_references ur');
        $this->db->join('user u', 'ur.buyer_id = u.id');
        $this->db->where('ur.referrer_id', $user_id);
        $this->db->where('ur.is_active', 1);
        $this->db->where('ur.created_at >=', $start_date);
        $this->db->where('ur.created_at <=', $end_date);
        $this->db->order_by('ur.created_at', 'DESC');
        
        $users = $this->db->get()->result();
        
        // Kullanıcı bilgilerini sansürle
        foreach ($users as &$user) {
            $user = $this->_censorUserData($user);
        }
        
        return $users;
    }
    
    /**
     * Belirli dönemde bonus kazandıran kullanıcıları getir
     */
    private function _getBonusEarningUsers($user_id, $start_date, $end_date)
    {
        $this->db->select('u.name, u.surname, u.email, rbh.bonus_amount, rbh.bonus_type, rbh.created_at, rbh.description');
        $this->db->from('reference_bonus_history rbh');
        $this->db->join('user u', 'rbh.referred_user_id = u.id');
        $this->db->where('rbh.referrer_id', $user_id);
        $this->db->where('rbh.status', 'paid');
        $this->db->where('rbh.created_at >=', $start_date);
        $this->db->where('rbh.created_at <=', $end_date);
        $this->db->order_by('rbh.bonus_amount', 'DESC');

        $bonus_users = $this->db->get()->result();

        // Settings çağrısını döngü dışına al (OPTİMİZASYON)
        $settings = $this->referral_system->getSettings();
        $bonus_rate = ($settings['purchase_bonus_rate'] ?? 5.00) / 100;

        // Her kullanıcı için alışveriş tutarını hesapla (eğer alışveriş bonusu ise) ve sansürle
        foreach ($bonus_users as &$user) {
            if ($user->bonus_type == 'purchase') {
                if ($bonus_rate > 0) {
                    $user->purchase_amount = $user->bonus_amount / $bonus_rate;
                } else {
                    $user->purchase_amount = 0;
                }
            } else {
                $user->purchase_amount = 0;
            }
            
            // Kullanıcı bilgilerini sansürle
            $user = $this->_censorUserData($user);
        }

        return $bonus_users;
    }
    
    /**
     * Belirli dönemde ayrılan kullanıcıları getir
     */
    private function _getLeftUsers($user_id, $start_date, $end_date)
    {
        // is_active = 0 olan ve güncellenme tarihi bu dönemde olan referanslar
        $this->db->select('u.name, u.surname, u.email, ur.updated_at as leave_date, ur.bonus_earned as total_earned');
        $this->db->from('user_references ur');
        $this->db->join('user u', 'ur.buyer_id = u.id');
        $this->db->where('ur.referrer_id', $user_id);
        $this->db->where('ur.is_active', 0);
        $this->db->where('ur.updated_at >=', $start_date);
        $this->db->where('ur.updated_at <=', $end_date);
        $this->db->order_by('ur.updated_at', 'DESC');
        
        $users = $this->db->get()->result();
        
        // Kullanıcı bilgilerini sansürle
        foreach ($users as &$user) {
            $user = $this->_censorUserData($user);
        }
        
        return $users;
    }

    /**
     * Kategori bazlı komisyon oranlarını getirir
     */
    public function getCategoryCommissions()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        $user_id = $this->session->userdata('info')['id'];
        if (!$user_id) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['success' => false, 'message' => 'Oturum bulunamadı']));
            return;
        }

        try {
            // Genel referans bonus oranını al
            $general_rate = $this->db->where('key', 'referral_purchase_bonus_rate')->get('settings')->row();
            $general_rate = $general_rate ? floatval($general_rate->value) : 'Değer Bulunamadı';

            // Kategorileri ve özel komisyon oranlarını al
            $categories = $this->db->select('c.id, c.name, rcc.bonus_percentage, rcc.min_amount, rcc.max_bonus, rcc.is_active as bonus_active')
                                   ->from('category c')
                                   ->join('reference_category_commissions rcc', 'c.id = rcc.category_id', 'left')
                                   ->where('c.isActive', 1)
                                   ->order_by('c.name', 'ASC')
                                   ->get()
                                   ->result();

            // Genel ayarları al
            $general_min_amount = $this->db->where('key', 'referral_min_purchase_amount')->get('settings')->row();
            $general_min_amount = $general_min_amount ? floatval($general_min_amount->value) : 'Değer Bulunamadı';
            
            $general_max_bonus = $this->db->where('key', 'referral_max_bonus_per_transaction')->get('settings')->row();
            $general_max_bonus = $general_max_bonus ? floatval($general_max_bonus->value) : 'Değer Bulunamadı';

            // Özel oranlı kategorileri filtrele
            $special_categories = [];
            foreach ($categories as $category) {
                // Özel bonus varsa ve aktifse
                if (!empty($category->bonus_percentage) && $category->bonus_active == 1) {
                    $special_categories[] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'commission_rate' => floatval($category->bonus_percentage),
                        'min_amount' => floatval($category->min_amount ?: 0),
                        'max_bonus' => $category->max_bonus ? floatval($category->max_bonus) : null
                    ];
                } else {
                    $special_categories[] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'commission_rate' => null, // Genel oran kullanılacak
                        'min_amount' => null, // Genel minimum kullanılacak
                        'max_bonus' => null // Genel maksimum kullanılacak
                    ];
                }
            }

            $response = [
                'success' => true,
                'general_rate' => $general_rate,
                'general_min_amount' => $general_min_amount,
                'general_max_bonus' => $general_max_bonus,
                'categories' => $special_categories
            ];

        } catch (Exception $e) {
            addlog('Reference::getCategoryCommissions', 'Hata: ' . $e->getMessage());
            $response = [
                'success' => false,
                'message' => 'Kategori komisyonları alınırken bir hata oluştu.'
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }
}

