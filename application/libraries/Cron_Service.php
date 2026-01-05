<?php

class Cron_Service {
    protected $CI;
    private $cache_dir;
    private $jobs = [];

    // Sabit zaman aralıkları (saniye cinsinden)
    const INTERVAL_1MIN  = 60;
    const INTERVAL_10MIN = 600;
    const INTERVAL_30MIN = 1800;
    const INTERVAL_1HOUR = 3600;
    const INTERVAL_1DAY  = 86400;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('M_Subscription');

        // Alt klasör kullanmadan doğrudan cache klasörünü kullan
        $this->cache_dir = APPPATH . 'cache/';

        // Cache dizininin var olduğundan emin ol
        if (!file_exists($this->cache_dir)) {
            @mkdir($this->cache_dir, 0755, true);
            addLog('Cron_Service', 'Cache dizini oluşturuldu: ' . $this->cache_dir);
        }

        // Sadece görevleri kaydet, otomatik çalıştırma
        $this->register_jobs();
    }

    // Dışarıdan API ile çağrılacak fonksiyon
    public function run_from_external_api() {
        // Görevleri kaydet ve çalıştır
        $this->register_jobs();
        
        // Başlangıç zamanını kaydet
        $start_time = microtime(true);
        
        // Görevleri çalıştır
        $this->run_cron_jobs();
        
        // Bitiş zamanını ve toplam süreyi hesapla
        $end_time = microtime(true);
        $execution_time = round($end_time - $start_time, 2);
        
        // Log kaydı oluştur - "tamamlandı" kelimesini ekleyelim
        addLog('Cron_Service', 'Cron görevleri başarıyla tamamlandı. Süre: ' . $execution_time . ' saniye', 'success');
        
        return true;
    }

    // API Key doğrulama fonksiyonu
    public function validate_api_key($provided_key) {
        $expected_key = $this->generate_api_key();
        return hash_equals($expected_key, $provided_key);
    }

    // Sadece alan adının sha256 ile şifrelenmiş hali olacak şekilde API Key oluşturma
    public function generate_api_key() {
        $site_name = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'default_site';
        return hash('sha256', $site_name);
    }

    private function register_jobs() {
        // Tüm işleri sınıf metodları olarak tanımlıyoruz
        $this->jobs = [
            // Her dakika çalışacak işler
            'retry_callbacks' => [
                'interval' => self::INTERVAL_1MIN,
                'method' => 'process_retry_callbacks'
            ],

            'process_mail_queue' => [
                'interval' => self::INTERVAL_1MIN,
                'method' => 'process_mail_queue'
            ],

            // Her 30 dakikada çalışacak işler
            'subscription_renew' => [
                'interval' => self::INTERVAL_30MIN,
                'method' => 'process_subscription_renewals'
            ],

            // Günlük işler
            'subscription_end' => [
                'interval' => self::INTERVAL_1DAY,
                'method' => 'process_subscription_endings'
            ]

        ];
    }

    private function run_cron_jobs() {
        $executed_jobs = 0;
        $skipped_jobs = 0;
        
        foreach ($this->jobs as $job_name => $job) {
            // "cron_" öneki ile dosya oluştur
            $cache_file = $this->cache_dir . 'cron_' . $job_name . '.txt';

            if ($this->should_run_job($cache_file, $job['interval'])) {
                try {
                    $start_time = microtime(true);
                    $start_memory = memory_get_usage();

                    // Metodu çağır
                    $this->{$job['method']}();

                    $execution_time = microtime(true) - $start_time;
                    $memory_used = memory_get_usage() - $start_memory;

                    $this->log_performance($job_name, $execution_time, $memory_used);
                    $this->update_last_run_time($cache_file);
                    
                    $executed_jobs++;
                    
                    // Her görev için ayrı log kaydı
                    addLog('Cron_Service', "'{$job_name}' görevi başarıyla tamamlandı. Süre: " . round($execution_time, 2) . " saniye", 'success');

                } catch (Exception $e) {
                    addLog('Cron_Service', "'{$job_name}' işi çalıştırılırken hata oluştu: " . $e->getMessage(), 'error');
                }
            } else {
                $skipped_jobs++;
            }
        }
        
        // Tüm görevler tamamlandıktan sonra genel bir log kaydı oluştur
        addLog('Cron_Service', 'Tüm cron görevleri tamamlandı. Çalıştırılan: ' . $executed_jobs . ', Atlanan: ' . $skipped_jobs . ' - ' . date('Y-m-d H:i:s'), 'success');
    }

    // Abonelik yenileme işlemi
    private function process_subscription_renewals() {
        $this->CI->M_Subscription->checkAndRenewSubscriptions();
        addLog('Cron_Service', 'Abonelik yenileme işlemleri tamamlandı');
    }

    // Abonelik sonlandırma işlemi
    private function process_subscription_endings() {
        $this->CI->M_Subscription->checkAndEndSubscriptions();
        addLog('Cron_Service', 'Abonelik sonlandırma işlemleri tamamlandı');
    }

    // Callback yeniden deneme işlemi
    private function process_retry_callbacks() {
        $this->CI->load->helper('provider');
        retry_failed_callbacks();
        addLog('Cron_Service', 'Başarısız callback işlemleri yeniden denendi');
    }

    // Kuyruktaki maili gönderme işlemi
    private function process_mail_queue() {
        $this->CI->load->library('mailer');
        $this->CI->mailer->process_queue(5); // Her seferde 5 mail işle
        addLog('Cron_Service', 'Mail kuyruğu işlemi tamamlandı');
    }

    private function should_run_job($cache_file, $interval) {
        if (!file_exists($cache_file)) {
            return true;
        }

        $last_run = (int)file_get_contents($cache_file);
        return (time() - $last_run) > $interval;
    }

    private function update_last_run_time($cache_file) {
        try {
            // Önce dizinin yazılabilir olduğunu kontrol et
            $cache_dir = dirname($cache_file);
            if (!is_writable($cache_dir)) {
                // Dizin yazılabilir değilse, izinleri değiştirmeyi dene
                @chmod($cache_dir, 0755);
                addLog('Cron_Service', 'Cache dizini izinleri 0755 olarak değiştirilmeye çalışıldı: ' . $cache_dir);
                
                if (!is_writable($cache_dir)) {
                    addLog('Cron_Service', 'Cache dizini yazılabilir değil ve izinler değiştirilemedi: ' . $cache_dir);
                    return false;
                }
            }
            
            // Dosya varsa yazılabilirliğini kontrol et
            if (file_exists($cache_file) && !is_writable($cache_file)) {
                // Dosya yazılabilir değilse, izinleri değiştirmeyi dene
                @chmod($cache_file, 0666);
                addLog('Cron_Service', 'Cache dosyası izinleri 0666 olarak değiştirilmeye çalışıldı: ' . $cache_file);
                
                if (!is_writable($cache_file)) {
                    addLog('Cron_Service', 'Cache dosyası yazılabilir değil ve izinler değiştirilemedi: ' . $cache_file);
                    return false;
                }
            }
            
            // Şu anki zaman damgasını (timestamp) hazırla
            $current_time = time();
            
            // Dosyaya yazma işlemi
            $result = @file_put_contents($cache_file, $current_time);
            
            if ($result === false) {
                addLog('Cron_Service', 'Son çalışma zamanı dosyası yazılamadı: ' . $cache_file);
                return false;
            }
            
            // Başarıyla yazıldığını kontrol et
            if (file_exists($cache_file)) {
                $content = @file_get_contents($cache_file);
                if ($content == $current_time) {
                    addLog('Cron_Service', 'Son çalışma zamanı başarıyla kaydedildi: ' . date('Y-m-d H:i:s', $current_time));
                    return true;
                }
            }
            
            addLog('Cron_Service', 'Son çalışma zamanı doğrulanamadı: ' . $cache_file);
            return false;
        } catch (Exception $e) {
            addLog('Cron_Service', 'Cache dosyası yazma hatası: ' . $e->getMessage());
            return false;
        }
    }

    private function log_performance($job_name, $execution_time, $memory_used) {
        $log_message = sprintf(
            "İş: %s, Çalışma Süresi: %.4f saniye, Kullanılan Bellek: %.2f MB",
            $job_name,
            $execution_time,
            $memory_used / 1024 / 1024
        );
        addLog('Cron_Service', $log_message);
    }
}