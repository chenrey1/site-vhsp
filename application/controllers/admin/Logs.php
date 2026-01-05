<?php

class Logs extends CI_Controller {

    public function cron_logs() {
        // Kullanıcı yetkisi kontrolü
        if (!isPerm($this->user->role_id, 'seeLogs')) {
            redirect(base_url('admin/dashboard'));
            return;
        }

        // Sadece CronApi ile ilgili logları al
        $data['logs'] = $this->db->like('module', 'CronApi')
                                ->order_by('id', 'desc')
                                ->limit(100)
                                ->get('logs')
                                ->result();
                                
        $data['title'] = 'Cron API Logları';
        $this->adminView('logs/cron_logs', $data);
    }
} 