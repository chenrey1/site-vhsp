<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Çekiliş işlemleri için log yardımcı dosyası
 * Bu dosya, çekiliş işlemlerinin loglanmasına ve hata ayıklamaya yardımcı olur.
 */

/**
 * Çekiliş log
 * 
 * Belirtilen mesajı ve veriyi log dosyasına yazar.
 * 
 * @param string $action İşlem adı
 * @param string $message Mesaj
 * @param array $data Ek veri (opsiyonel)
 * @return bool İşlem başarılı mı?
 */
if (!function_exists('draw_log')) {
    function draw_log($action, $message, $data = []) {
        // CodeIgniter instance al
        $CI =& get_instance();
        
        // Log dosyası için dizin oluştur
        $log_path = APPPATH . 'logs/draws/';
        if (!is_dir($log_path)) {
            mkdir($log_path, 0755, true);
        }
        
        // Log dosyası adı
        $log_file = $log_path . 'draws_' . date('Y-m-d') . '.log';
        
        // Log mesajı oluştur
        $log_time = date('Y-m-d H:i:s');
        $log_ip = $CI->input->ip_address();
        $log_user = isset($CI->session) && $CI->session->userdata('info') ? $CI->session->userdata('info')['id'] : 'sistem';
        
        $log_message = "[{$log_time}] [{$log_ip}] [{$log_user}] [{$action}] {$message}";
        
        // Ek veri varsa JSON olarak ekle
        if (!empty($data)) {
            $log_message .= " " . json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        
        $log_message .= PHP_EOL;
        
        // Dosyaya yaz
        return file_put_contents($log_file, $log_message, FILE_APPEND | LOCK_EX) !== false;
    }
}

/**
 * Çekiliş hatası
 * 
 * Belirtilen hatayı log dosyasına yazar ve isterse ekrana gösterir.
 * 
 * @param string $action İşlem adı
 * @param string $message Hata mesajı
 * @param array $data Ek veri (opsiyonel)
 * @param bool $show_error Hata ekrana gösterilsin mi?
 * @return void
 */
if (!function_exists('draw_error')) {
    function draw_error($action, $message, $data = [], $show_error = false) {
        // Log mesajını oluştur
        $log_message = "HATA: {$message}";
        
        // Log yaz
        draw_log($action, $log_message, $data);
        
        // CodeIgniter hata mesajı göster
        if ($show_error) {
            $CI =& get_instance();
            $CI->output->set_status_header(500);
            show_error($message, 500, 'Çekiliş Hatası');
        }
    }
}

/**
 * Çekiliş başarı
 * 
 * Belirtilen başarı mesajını log dosyasına yazar.
 * 
 * @param string $action İşlem adı
 * @param string $message Başarı mesajı
 * @param array $data Ek veri (opsiyonel)
 * @return void
 */
if (!function_exists('draw_success')) {
    function draw_success($action, $message, $data = []) {
        // Log mesajını oluştur
        $log_message = "BAŞARILI: {$message}";
        
        // Log yaz
        draw_log($action, $log_message, $data);
    }
}

/**
 * Çekiliş bilgi
 * 
 * Belirtilen bilgi mesajını log dosyasına yazar.
 * 
 * @param string $action İşlem adı
 * @param string $message Bilgi mesajı
 * @param array $data Ek veri (opsiyonel)
 * @return void
 */
if (!function_exists('draw_info')) {
    function draw_info($action, $message, $data = []) {
        // Log mesajını oluştur
        $log_message = "BİLGİ: {$message}";
        
        // Log yaz
        draw_log($action, $log_message, $data);
    }
} 
