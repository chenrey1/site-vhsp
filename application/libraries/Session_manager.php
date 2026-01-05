<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Session_manager {
    protected $CI;
    protected $ignored_pages = array('api', 'cron', 'assets', 'favicon.ico', 'admin');
    protected $last_page;
    protected $session_timeout_minutes = 10080; // 1 hafta (7 gün x 24 saat x 60 dakika)
    protected $activity_timeout_minutes = 30; // 30 dakika

    public function __construct() {
        $this->CI =& get_instance();
        $this->last_page = $this->CI->session->userdata('last_page');
        
        // Session süresini ayarla (saniye cinsinden)
        $this->CI->config->set_item('sess_expiration', $this->session_timeout_minutes * 60);
        
        // Session kontrolü
        $this->check_session_timeout();
    }

    /**
     * Session timeout kontrolü
     */
    protected function check_session_timeout() {
        if ($this->CI->session->userdata('info')) {
            $last_activity = $this->CI->session->userdata('last_activity');
            $current_time = time();
            
            if ($last_activity && ($current_time - $last_activity) > ($this->activity_timeout_minutes * 60)) {
                // Session'ı temizle
                $this->CI->session->unset_userdata('info');
                $this->CI->session->unset_userdata('last_page');
                $this->CI->session->unset_userdata('last_activity');
                
                // Veritabanından session kaydını güncelle (silmek yerine)
                $session_id = session_id();
                $this->CI->db->where('id', $session_id);
                $this->CI->db->update('ci_sessions', array(
                    'user_id' => null,
                    'data' => '',
                    'last_activity' => date('Y-m-d H:i:s')
                ));

                // Redirect to home page
                redirect(base_url());
            } else {
                // Son aktivite zamanını güncelle
                $this->CI->session->set_userdata('last_activity', $current_time);
            }
        }
    }

    /**
     * Kullanıcı aktivitesini günceller
     */
    public function update_activity($params = array()) {
        // Router bilgilerini al
        $uri = $this->CI->uri->uri_string();
        $controller = $this->CI->router->fetch_class();
        
        // Hook parametrelerini kontrol et
        if (isset($params['excluded_controllers']) && is_array($params['excluded_controllers'])) {
            if (in_array($controller, $params['excluded_controllers'])) {
                return; // Bu controller için aktivite güncelleme
            }
        }
        
        // URI boşsa base_url'den sonraki kısmı al
        if (empty($uri)) {
            $uri = str_replace(base_url(), '', current_url());
        }
        
        // Hala boşsa güncelleme yapma
        if (empty($uri)) {
            return;
        }

        // Kullanıcı girişi kontrolü
        if (!$this->CI->session->userdata('info')) {
            return;
        }
        
        // Statik dosya kontrolü
        if (pathinfo($uri, PATHINFO_EXTENSION) !== '') {
            return;
        }

        // Takip edilmeyecek sayfaları kontrol et
        if ($this->is_ignored_page($uri)) {
            return;
        }
        
        // Son aktivite güncellemesinden beri en az 5 dakika geçmişse güncelle
        $last_update = $this->CI->session->userdata('last_activity_update');
        $current_time = time();
        
        // Eğer son güncellemeden beri 3 dakikadan az zaman geçtiyse güncelleme yapma
        if ($last_update && ($current_time - $last_update) < (3 * 60)) {
            // Sadece session'daki son sayfa bilgisini güncelle
            $this->CI->session->set_userdata('last_page', $uri);
            $this->last_page = $uri;
            return;
        }
        
        // Son güncelleme zamanını kaydet
        $this->CI->session->set_userdata('last_activity_update', $current_time);

        try {
            $user_id = $this->CI->session->userdata('info')['id'];
            $current_time_formatted = date('Y-m-d H:i:s');
            $session_id = session_id();
            $ip_address = $this->CI->input->ip_address();

            // Eski sessionları temizle
            $this->clean_old_sessions($user_id);

            // Mevcut session'ı güncelle
            $this->CI->db->where('id', $session_id);
            $query = $this->CI->db->get('ci_sessions');

            // Session verilerini hazırla
            $session_data = array(
                'user_id' => $user_id,
                'last_page' => $uri,
                'last_activity' => $current_time_formatted,
                'ip_address' => $ip_address,
                'timestamp' => $current_time
            );

            // Session verilerini ekle
            try {
                $userdata = $this->CI->session->userdata();
                $serialized = '';
                foreach ($userdata as $key => $val) {
                    if ($val !== null) {
                        $serialized .= $key . '|' . serialize($val) . ';';
                    }
                }
                $session_data['data'] = $serialized;
            } catch (Exception $e) {
                log_message('error', 'Session data serialization error: ' . $e->getMessage());
                $session_data['data'] = '';
            }

            // Session güncelleme/oluşturma işlemi
            if ($query->num_rows() > 0) {
                // Mevcut session'ı güncelle
                $this->CI->db->trans_start();
                try {
                    $this->CI->db->where('id', $session_id);
                    $this->CI->db->update('ci_sessions', $session_data);
                } catch (Exception $e) {
                    log_message('error', 'Session update error: ' . $e->getMessage());
                }
                $this->CI->db->trans_complete();
            } else {
                // Yeni session oluştur
                $this->CI->db->trans_start();
                try {
                    // Önce eski session'ı temizle
                    $this->CI->db->where('id', $session_id)->delete('ci_sessions');
                    
                    // Yeni session'ı ekle
                    $session_data['id'] = $session_id;
                    $this->CI->db->insert('ci_sessions', $session_data);
                } catch (Exception $e) {
                    log_message('error', 'Session creation error: ' . $e->getMessage());
                }
                $this->CI->db->trans_complete();
            }

            // Session'a son sayfa bilgisini kaydet
            $this->CI->session->set_userdata('last_page', $uri);
            $this->last_page = $uri;

        } catch (Exception $e) {
            log_message('error', 'Session update error: ' . $e->getMessage());
        }
    }

    /**
     * Takip edilmeyecek sayfa kontrolü
     */
    protected function is_ignored_page($uri) {
        foreach ($this->ignored_pages as $page) {
            if (strpos(strtolower($uri), strtolower($page)) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Eski sessionları temizler
     * Aktif olmayan sessionları siler ama son 30 günlük veriyi tutar
     */
    protected function clean_old_sessions($user_id) {
        try {
            // 30 gün öncesinden eski kayıtları sil
            $month_ago = date('Y-m-d H:i:s', strtotime('-30 days'));
            
            $this->CI->db->trans_start();
            
            // Eski sessionları temizle
            $this->CI->db->where('last_activity <', $month_ago)->delete('ci_sessions');
            
            // Kullanıcının eski sessionlarını temizle
            $this->CI->db->where('user_id', $user_id)
                        ->where('id !=', session_id())
                        ->where('last_activity <', date('Y-m-d H:i:s', strtotime('-' . $this->activity_timeout_minutes . ' minutes')))
                        ->update('ci_sessions', array(
                            'user_id' => null,
                            'data' => ''
                        ));
            
            $this->CI->db->trans_complete();
            
        } catch (Exception $e) {
            log_message('error', 'Clean old sessions error: ' . $e->getMessage());
        }
    }

    /**
     * Online kullanıcıları getirir
     */
    public function get_online_users($minutes = null) {
        // Eğer süre belirtilmemişse activity_timeout'u kullan
        if ($minutes === null) {
            $minutes = $this->activity_timeout_minutes;
        }
        
        $timeout = date('Y-m-d H:i:s', strtotime('-' . $minutes . ' minutes'));
        $daily_timeout = date('Y-m-d H:i:s', strtotime('-24 hours'));

        try {
            // Ana sorgu
            $this->CI->db->select('u.id, u.email, u.name, u.surname, u.role_id, u.balance');
            $this->CI->db->select('s.last_page, s.last_activity, s.ip_address');
            $this->CI->db->select('(SELECT COUNT(*) FROM ci_sessions WHERE user_id = u.id AND last_activity > "'.$daily_timeout.'") as daily_visits', false);
            $this->CI->db->from('ci_sessions s');
            $this->CI->db->join('user u', 'u.id = s.user_id');
            $this->CI->db->where('s.last_activity >', $timeout);
            $this->CI->db->where('s.user_id IS NOT NULL');
            $this->CI->db->where('s.user_id !=', 0);
            
            // Admin sayfalarını kullananları dahil etme
            $this->CI->db->where('s.last_page NOT LIKE', 'admin/%');
            $this->CI->db->where('s.last_page !=', 'admin');
            
            $this->CI->db->group_by('s.user_id');
            $this->CI->db->order_by('s.last_activity', 'DESC');

            $result = $this->CI->db->get()->result();

            // Her kullanıcı için ek bilgileri hesapla
            foreach ($result as $user) {
                // Son aktivite zamanını "... önce" formatına çevir
                $last_activity_time = strtotime($user->last_activity);
                $time_diff = time() - $last_activity_time;
                
                if ($time_diff < 60) {
                    $user->last_activity_text = $time_diff . ' saniye önce';
                } elseif ($time_diff < 3600) {
                    $user->last_activity_text = floor($time_diff/60) . ' dakika önce';
                } elseif ($time_diff < 86400) {
                    $user->last_activity_text = floor($time_diff/3600) . ' saat önce';
                } else {
                    $user->last_activity_text = floor($time_diff/86400) . ' gün önce';
                }

                // Sayfa adını düzenle
                $user->page_name = $user->last_page;
                if (empty($user->page_name)) {
                    $user->page_name = 'Ana Sayfa';
                }

                // IP adresini kontrol et
                if (empty($user->ip_address)) {
                    $user->ip_address = 'Bilinmiyor';
                }

                // Kullanıcı rolünü belirle
                $user->role_name = $user->role_id == 1 ? 'Admin' : 'Kullanıcı';

                // Tam ismi birleştir
                $user->full_name = trim($user->name . ' ' . $user->surname);
                if (empty($user->full_name)) {
                    $user->full_name = $user->email;
                }
            }

            return $result;
        } catch (Exception $e) {
            log_message('error', 'Get online users error: ' . $e->getMessage());
            return array();
        }
    }
} 