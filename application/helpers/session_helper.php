<?php
    function update_session_activity() {
        $CI =& get_instance();
    
        // Kullanıcı girişi kontrolü
        if (!$CI->session->userdata('info')) {
            return;
        }
    
        // Takip edilmeyecek sayfalar
        $ignored_pages = array('ajax', 'api', 'cron', 'assets', 'favicon.ico');
        
        // Router bilgilerini al
        $directory = trim($CI->router->directory, '/');
        $class = $CI->router->class;
        $method = $CI->router->method;
        $uri = $CI->uri->uri_string();
        $uri_segments = explode('/', $uri);
    
        // Statik dosya kontrolü
        if (pathinfo($uri, PATHINFO_EXTENSION) !== '') {
            return;
        }
        
        // Sayfa adını belirle
        $page_name = '';
    
        // Önce method'a göre sayfa adını belirle
        if ($method == 'category' || strpos($uri, 'kategori/') === 0) {
            $page_name = 'Kategori';
        }
        else if ($method == 'getProduct' || strpos($uri, 'urun/') === 0) {
            $page_name = 'Ürün Detayı';
        }
        else if ($method == 'index' && empty($uri)) {
            $page_name = 'Ana Sayfa';
        }
        // Admin kontrolü
        else if (strpos($uri, 'admin/') === 0) {
            $page_name = 'Admin Panel';
        }
        // Client kontrolü
        else if (strpos($uri, 'client/') === 0) {
            $client_segments = array_slice($uri_segments, 1);
            $client_class = isset($client_segments[0]) ? $client_segments[0] : '';
            
            switch($client_class) {
                case 'dashboard':
                    $page_name = 'Panel';
                    break;
                case 'product':
                    if ($method == 'view') {
                        $page_name = 'Ürün Detayı';
                    } else if ($method == 'category') {
                        $page_name = 'Kategori';
                    } else {
                        $page_name = 'Ürünler';
                    }
                    break;
                case 'cart':
                    $page_name = 'Sepet';
                    break;
                case 'payment':
                    $page_name = 'Ödeme';
                    break;
                case 'profile':
                    $page_name = 'Profil';
                    break;
                case 'ticket':
                    $page_name = ($method == 'view') ? 'Destek Detayı' : 'Destek';
                    break;
                default:
                    $page_name = 'Panel';
            }
        }
        // API kontrolü
        else if (strtolower($class) == 'api' || strpos(strtolower($uri), 'api/') === 0) {
            return; // API isteklerini takip etme
        }
        // Diğer sayfalar için URI'ye göre kontrol
        else {
            // Eğer özel bir sayfa değilse ve URI boşsa ana sayfa
            if (empty($uri)) {
                $page_name = 'Ana Sayfa';
            } else {
                // URI'ye göre sayfa adını belirle
                $last_segment = end($uri_segments);
                if (!empty($last_segment)) {
                    $page_name = ucfirst($last_segment);
                } else {
                    $page_name = 'Ana Sayfa';
                }
            }
        }
    
        // Takip edilmeyecek sayfaları kontrol et
        foreach ($ignored_pages as $page) {
            if (strpos(strtolower($uri), strtolower($page)) !== false) {
                return;
            }
        }
    
        // Mevcut sayfayı kontrol et - aynı sayfada gereksiz güncelleme yapma
        $current_page = $CI->session->userdata('last_page');
        if ($current_page === $page_name) {
            return;
        }
    
        try {
            $user_id = $CI->session->userdata('info')['id'];
            $current_time = date('Y-m-d H:i:s');
    
            // Önce eski session kayıtlarını temizle (30 dakikadan eski)
            $timeout = date('Y-m-d H:i:s', strtotime('-30 minutes'));
            $CI->db->where('last_activity <', $timeout);
            $CI->db->where('user_id', $user_id);
            $CI->db->delete('ci_sessions');
    
            // Kullanıcının aktif sessionunu güncelle
            $CI->db->where('user_id', $user_id);
            $CI->db->update('ci_sessions', array(
                'last_page' => $page_name,
                'last_activity' => $current_time
            ));
    
            // Session'a son sayfa bilgisini kaydet
            $CI->session->set_userdata('last_page', $page_name);
    
        } catch (Exception $e) {
            log_message('error', 'Session update error: ' . $e->getMessage());
        }
    }

    function get_online_users($minutes = 5) {
        $CI =& get_instance();
        $timeout = date('Y-m-d H:i:s', strtotime('-' . $minutes . ' minutes'));

        try {
            $CI->db->select('u.*, s.last_page, s.last_activity');
            $CI->db->from('ci_sessions s');
            $CI->db->join('user u', 'u.id = s.user_id');
            $CI->db->where('s.last_activity >', $timeout);
            $CI->db->where('s.user_id IS NOT NULL');
            $CI->db->where('s.user_id !=', 0);
            $CI->db->group_by('s.user_id');
            $CI->db->order_by('s.last_activity', 'DESC');

            return $CI->db->get()->result();
        } catch (Exception $e) {
            log_message('error', 'Get online users error: ' . $e->getMessage());
            return array();
        }
    }