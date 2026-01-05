<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends G_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('M_Settings');
        
        // CSRF koruması otomatik olarak etkin
        // CodeIgniter 3'te Security sınıfı otomatik olarak yüklenir
        
        // Oturum kontrolü
        if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
            redirect(base_url(), 'refresh');
            exit();
        }
    }
    
    public function index() {
        redirect('admin/settings/balance');
    }
    
    public function balance() {
        $data['title'] = 'Bakiye Ayarları';
        $data['status'] = 'settings';
        $data['settings'] = $this->M_Settings->getAllSettings();
        
        // Views klasörü altındaki settings klasöründeki balance.php dosyasını göster
        $this->adminView('settings/balance', $data);
    }
    
    public function update_setting() {
        // CSRF kontrolü ve AJAX istek kontrolü
        if (!$this->input->is_ajax_request()) {
            show_error('Bu sayfaya doğrudan erişim yok!', 403);
            return;
        }
        
        $setting_key = $this->input->post('setting_key');
        $setting_value = $this->input->post('setting_value');
        
        $result = $this->M_Settings->updateSetting($setting_key, $setting_value);
        
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Ayar başarıyla güncellendi']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Ayar güncellenirken bir hata oluştu']);
        }
    }
    
    public function update_settings() {
        $settings = $this->input->post('settings');
        $success = true;
        
        // Tüm checkbox ayarları için kontrol
        $checkbox_settings = [
            'enable_balance_transfer',
            'enable_balance_exchange',
            'enable_credit_operations'
        ];
        
        // Checkbox'lar işaretlenmediğinde POST verisi gelmeyeceği için bunları 0 olarak işleyelim
        foreach ($checkbox_settings as $key) {
            if (!isset($settings[$key])) {
                $settings[$key] = '0';
            }
        }
        
        foreach ($settings as $key => $value) {
            $result = $this->M_Settings->updateSetting($key, $value);
            if (!$result) {
                $success = false;
            }
        }
        
        if ($success) {
            $this->session->set_flashdata('success', 'Bakiye ayarları başarıyla güncellendi.');
        } else {
            $this->session->set_flashdata('error', 'Bakiye ayarları güncellenirken bir hata oluştu.');
        }
        
        redirect('admin/settings/balance', 'refresh');
    }
    
    /**
     * Ödeme yöntemleri yönetim sayfası
     */
    public function payment() {
        $data['title'] = 'Ödeme Ayarları';
        $data['status'] = 'settings';
        
        // Tüm ödeme yöntemlerini getir
        $data['payment_methods'] = $this->db->get('payment')->result();
        
        // ---- Toplam İşlem ve Karşılaştırma ----
        
        // Bu ayın toplam işlem tutarı
        $currentMonthStart = date('Y-m-01 00:00:00');
        $currentMonthEnd = date('Y-m-t 23:59:59');
        
        $currentMonthTotal = $this->db->select_sum('price')
            ->where('status', 0) 
            ->where('type !=', 'balance')
            ->where('date >=', $currentMonthStart)
            ->where('date <=', $currentMonthEnd)
            ->get('shop')
            ->row()
            ->price;
        $currentMonthTotal = $currentMonthTotal ?: 0;
        
        // Geçen ayın toplam işlem tutarı
        $lastMonthStart = date('Y-m-01 00:00:00', strtotime('-1 month'));
        $lastMonthEnd = date('Y-m-t 23:59:59', strtotime('-1 month'));
        
        $lastMonthTotal = $this->db->select_sum('price')
            ->where('status', 0) 
            ->where('type !=', 'balance')
            ->where('date >=', $lastMonthStart)
            ->where('date <=', $lastMonthEnd)
            ->get('shop')
            ->row()
            ->price;
        $lastMonthTotal = $lastMonthTotal ?: 0;
        
        // Artış/azalış yüzdesi hesaplama
        if ($lastMonthTotal > 0) {
            $monthlyChangeRate = (($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100;
        } else {
            $monthlyChangeRate = $currentMonthTotal > 0 ? 100 : 0;
        }
        $data['monthly_change_rate'] = round($monthlyChangeRate, 1);
        
        // Tüm zamanların toplamı
        $totalPayment = $this->db->select_sum('price')
            ->where('status', 0)
            ->where('type !=', 'balance')
            ->get('shop')
            ->row()
            ->price;
        $data['total_payment'] = $totalPayment ?: 0;
        
        
        // ---- Başarılı İşlem Oranı ve Karşılaştırma ----
        
        // Bu haftanın başarılı işlem oranı
        $currentWeekStart = date('Y-m-d 00:00:00', strtotime('this week monday'));
        $currentWeekEnd = date('Y-m-d 23:59:59', strtotime('this week sunday'));
        
        $currentWeekSuccess = $this->db->where('status', 0)
            ->where('type !=', 'balance')
            ->where('date >=', $currentWeekStart)
            ->where('date <=', $currentWeekEnd)
            ->count_all_results('shop');
        
        $currentWeekTotal = $this->db->where('type !=', 'balance')
            ->where('date >=', $currentWeekStart)
            ->where('date <=', $currentWeekEnd)
            ->count_all_results('shop');
        
        $currentWeekRate = $currentWeekTotal > 0 ? ($currentWeekSuccess / $currentWeekTotal) * 100 : 0;
        
        // Geçen haftanın başarılı işlem oranı
        $lastWeekStart = date('Y-m-d 00:00:00', strtotime('last week monday'));
        $lastWeekEnd = date('Y-m-d 23:59:59', strtotime('last week sunday'));
        
        $lastWeekSuccess = $this->db->where('status', 0)
            ->where('type !=', 'balance')
            ->where('date >=', $lastWeekStart)
            ->where('date <=', $lastWeekEnd)
            ->count_all_results('shop');
        
        $lastWeekTotal = $this->db->where('type !=', 'balance')
            ->where('date >=', $lastWeekStart)
            ->where('date <=', $lastWeekEnd)
            ->count_all_results('shop');
        
        $lastWeekRate = $lastWeekTotal > 0 ? ($lastWeekSuccess / $lastWeekTotal) * 100 : 0;
        
        // Artış/azalış yüzdesi hesaplama
        if ($lastWeekRate > 0) {
            $weeklyRateChange = ($currentWeekRate - $lastWeekRate);
        } else {
            $weeklyRateChange = $currentWeekRate;
        }
        $data['weekly_rate_change'] = round($weeklyRateChange, 1);
        
        // Genel başarılı işlem oranı
        $successfulPayments = $this->db->where('status', 0)
            ->where('type !=', 'balance')
            ->count_all_results('shop');
        
        $totalPaymentAttempts = $this->db->where('type !=', 'balance')
            ->count_all_results('shop');
        
        $data['success_rate'] = $totalPaymentAttempts > 0 ? 
            round(($successfulPayments / $totalPaymentAttempts) * 100, 2) : 0;
            
        
        // ---- Günlük Ortalama İşlem ve Karşılaştırma ----
        
        // Bu haftanın günlük ortalama işlem miktarı
        $currentWeekAvg = $this->db->select_avg('price')
            ->where('status', 0)
            ->where('type !=', 'balance')
            ->where('date >=', $currentWeekStart)
            ->where('date <=', $currentWeekEnd)
            ->get('shop')
            ->row()
            ->price;
        $currentWeekAvg = $currentWeekAvg ?: 0;
        
        // Geçen haftanın günlük ortalama işlem miktarı
        $lastWeekAvg = $this->db->select_avg('price')
            ->where('status', 0)
            ->where('type !=', 'balance')
            ->where('date >=', $lastWeekStart)
            ->where('date <=', $lastWeekEnd)
            ->get('shop')
            ->row()
            ->price;
        $lastWeekAvg = $lastWeekAvg ?: 0;
        
        // Artış/azalış yüzdesi hesaplama
        if ($lastWeekAvg > 0) {
            $weeklyAvgChange = (($currentWeekAvg - $lastWeekAvg) / $lastWeekAvg) * 100;
        } else {
            $weeklyAvgChange = $currentWeekAvg > 0 ? 100 : 0;
        }
        $data['weekly_avg_change'] = round($weeklyAvgChange, 1);
        
        // Son 30 günün ortalaması (ana istatistik)
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        $dailyAverage = $this->db->select_avg('price')
            ->where('status', 0)
            ->where('type !=', 'balance')
            ->where('date >=', $thirtyDaysAgo)
            ->get('shop')
            ->row()
            ->price;
        $data['daily_average'] = $dailyAverage ?: 0;
        
        
        // ---- Ödeme Komisyonu ve Karşılaştırma ----
        
        // Bu ayın toplam komisyon miktarı
        $currentMonthCommission = $this->db->select_sum('payment_commission')
            ->where('status', 0)
            ->where('type !=', 'balance')
            ->where('date >=', $currentMonthStart)
            ->where('date <=', $currentMonthEnd)
            ->get('shop')
            ->row()
            ->payment_commission;
        $currentMonthCommission = $currentMonthCommission ?: 0;
        
        // Geçen ayın toplam komisyon miktarı
        $lastMonthCommission = $this->db->select_sum('payment_commission')
            ->where('status', 0)
            ->where('type !=', 'balance')
            ->where('date >=', $lastMonthStart)
            ->where('date <=', $lastMonthEnd)
            ->get('shop')
            ->row()
            ->payment_commission;
        $lastMonthCommission = $lastMonthCommission ?: 0;
        
        // Artış/azalış yüzdesi hesaplama
        if ($lastMonthCommission > 0) {
            $commissionChangeRate = (($currentMonthCommission - $lastMonthCommission) / $lastMonthCommission) * 100;
        } else {
            $commissionChangeRate = $currentMonthCommission > 0 ? 100 : 0;
        }
        $data['commission_change_rate'] = round($commissionChangeRate, 1);
        
        // Toplam komisyon miktarı (ana istatistik)
        $totalCommission = $this->db->select_sum('payment_commission')
            ->where('status', 0)
            ->where('type !=', 'balance')
            ->get('shop')
            ->row()
            ->payment_commission;
        $data['total_commission'] = $totalCommission ?: 0;
        
        
        // ---- Ödeme yöntemlerine göre kullanım istatistikleri ----
        $paymentUsageStats = [];
        foreach ($data['payment_methods'] as $method) {
            // Son 1 ay içerisindeki bu ödeme yöntemi ile yapılan işlem sayısı
            $usageCount = $this->db->where('payment_method_id', $method->id)
                ->where('status', 0)
                ->where('type !=', 'balance')
                ->where('date >=', $currentMonthStart) // Son ayın başlangıcı
                ->where('date <=', $currentMonthEnd)   // Son ayın sonu
                ->count_all_results('shop');
                
            // Son 1 ay içerisindeki toplam işlem sayısı
            $totalCount = $this->db->where('status', 0)
                ->where('type !=', 'balance')
                ->where('date >=', $currentMonthStart) // Son ayın başlangıcı
                ->where('date <=', $currentMonthEnd)   // Son ayın sonu
                ->count_all_results('shop');
                
            // Kullanım oranını hesapla
            $usageRate = $totalCount > 0 ? round(($usageCount / $totalCount) * 100, 1) : 0;
            
            $paymentUsageStats[$method->id] = $usageRate;
        }
        $data['payment_usage_stats'] = $paymentUsageStats;
        
        // ---- Günlük işlem dağılımı (son 7 gün) ----
        $dailyStats = [];
        $dayNames = ['Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt'];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $nextDay = date('Y-m-d', strtotime("-" . ($i - 1) . " days"));
            
            $count = $this->db->where('status', 0)
                ->where('type !=', 'balance')
                ->where('date >=', $date . ' 00:00:00')
                ->where('date <', $nextDay . ' 00:00:00')
                ->count_all_results('shop');
                
            $dayOfWeek = date('w', strtotime($date));
            $dailyStats[$dayNames[$dayOfWeek]] = $count;
        }
        $data['daily_stats'] = $dailyStats;
        
        $this->adminView('settings/payment', $data);
    }
    
    /**
     * Ödeme yöntemi güncelle
     */
    public function updatePaymentMethod() {
        $payment_id = $this->input->post('payment_id');
        $commission_rate = $this->input->post('commission_rate');
        $display_order = $this->input->post('display_order');
        $description = $this->input->post('description');
        $status = $this->input->post('status') ? 1 : 0;
        $is_default = $this->input->post('is_default') ? 1 : 0;
        
        // Config JSON'ı güncelle (sadece değerleri)
        $config_keys = $this->input->post('config_keys');
        $config_values = $this->input->post('config_values');
        
        // Mevcut yapılandırmayı al
        $current_payment = $this->db->select('config')->where('id', $payment_id)->get('payment')->row();
        $current_config = [];
        
        if ($current_payment && !empty($current_payment->config)) {
            if (is_string($current_payment->config)) {
                $current_config = json_decode($current_payment->config, true) ?? [];
            } else {
                $current_config = json_decode(json_encode($current_payment->config), true) ?? [];
            }
        }
        
        if (!empty($config_keys) && !empty($config_values)) {
            foreach ($config_keys as $i => $key) {
                if (isset($config_values[$i])) {
                    // Anahtarları ve değerleri güncelle/ekle
                    $current_config[$key] = $config_values[$i];
                }
            }
        }
        
        // Eğer varsayılan olarak ayarlanmışsa diğer varsayılanları kaldır
        if ($is_default == 1) {
            $this->db->update('payment', ['is_default' => 0]);
        }
        
        // İkon yükleme işlemi
        if (!empty($_FILES['icon']['name'])) {
            $config['upload_path'] = FCPATH . 'assets/img/payments/';
            $config['allowed_types'] = 'gif|jpg|png|svg';
            $config['max_size'] = 2048;
            $config['encrypt_name'] = true;
            
            $this->load->library('upload', $config);
            
            if (!$this->upload->do_upload('icon')) {
                $this->session->set_flashdata('error', 'İkon yüklenirken hata oluştu: ' . $this->upload->display_errors());
                redirect('admin/settings/payment');
                return;
            }
            
            $upload_data = $this->upload->data();
            $icon = $upload_data['file_name'];
            
            // Eski ikonu silme
            $old_icon = $this->db->select('icon')->where('id', $payment_id)->get('payment')->row()->icon;
            if (!empty($old_icon) && file_exists(FCPATH . 'assets/img/payments/' . $old_icon)) {
                unlink(FCPATH . 'assets/img/payments/' . $old_icon);
            }
            
            $this->db->set('icon', $icon);
        }
        
        // Veritabanını güncelle
        $this->db->set('commission_rate', $commission_rate);
        $this->db->set('display_order', $display_order);
        $this->db->set('description', $description);
        $this->db->set('status', $status);
        $this->db->set('is_default', $is_default);
        $this->db->set('config', json_encode($current_config));
        $this->db->where('id', $payment_id);
        $result = $this->db->update('payment');
        
        if ($result) {
            $this->session->set_flashdata('success', 'Ödeme yöntemi başarıyla güncellendi.');
        } else {
            $this->session->set_flashdata('error', 'Ödeme yöntemi güncellenirken bir hata oluştu.');
        }
        
        redirect('admin/settings/payment');
    }
    
    /**
     * Bir ödeme yöntemini varsayılan yap (AJAX)
     */
    public function setDefaultPaymentMethod() {
        if (!$this->input->is_ajax_request()) {
            show_error('Bu sayfaya doğrudan erişim yok!', 403);
            return;
        }
        
        $payment_id = $this->input->post('payment_id');
        
        // Önce tüm ödeme yöntemlerinin varsayılan değerini kaldır
        $this->db->update('payment', ['is_default' => 0]);
        
        // Seçilen ödeme yöntemini varsayılan yap
        $this->db->where('id', $payment_id)->update('payment', ['is_default' => 1]);
        
        echo json_encode(['status' => 'success']);
    }
    
    /**
     * Ödeme yöntemi durumunu güncelle (AJAX)
     */
    public function updatePaymentStatus() {
        if (!$this->input->is_ajax_request()) {
            show_error('Bu sayfaya doğrudan erişim yok!', 403);
            return;
        }
        
        $payment_id = $this->input->post('payment_id');
        $status = $this->input->post('status');
        
        $result = $this->db->where('id', $payment_id)->update('payment', ['status' => $status]);
        
        if ($result) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error']);
        }
    }
} 