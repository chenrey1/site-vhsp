<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CronApi extends G_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('Cron_Service');
    }
    
    public function index() {
        $this->run();
    }
    
    public function run($api_key = null) {
        // API key kontrolü
        if (empty($api_key)) {
            addLog('CronApi', 'API key eksik', 'error');
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'API key is required'
                ]));
            return;
        }

        // API key doğrulama
        if (!$this->cron_service->validate_api_key($api_key)) {
            addLog('CronApi', 'Geçersiz API key: ' . $api_key, 'error');
            $this->output
                ->set_status_header(403)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Invalid API key'
                ]));
            return;
        }

        // Cron görevlerini çalıştır
        try {
            addLog('CronApi', 'Cron görevleri tetiklendi', 'info');
            $this->cron_service->run_from_external_api();
            
            // Başarılı yanıt
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => 'Cron jobs executed successfully',
                    'timestamp' => date('Y-m-d H:i:s')
                ]));
        } catch (Exception $e) {
            addLog('CronApi', 'Cron görevleri çalıştırılırken hata: ' . $e->getMessage(), 'error');
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Error executing cron jobs: ' . $e->getMessage()
                ]));
        }
    }
} 