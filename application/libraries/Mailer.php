<?php
class Mailer {
    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->library('email');
        $this->CI->load->helper('mail');
    }

    // Mail kuyruğa ekleme
    public function send($to, $template_code, $data = [], $priority = 1) {
        //Düşük öncelikli mail: priority = 1, yüksek öncelik 2
        // Önce şablonun var olup olmadığını kontrol et (sadece kod ile)
        $template = $this->CI->db->where('code', $template_code)
            ->get('mail_templates')
            ->row();

        if (!$template) {
            // Şablon tamamen bulunamadı, template_id=1 kullan
            return $this->CI->db->insert('mail_queue', [
                'template_id' => 1, // Template bulunamadı durumunda 1 kullanıyoruz (unsigned alan hatası için)
                'to_email' => $to,
                'data' => json_encode($data),
                'priority' => $priority,
                'status' => 'failed',
                'error_message' => 'Template not found for: ' . $template_code,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Şablon var ama aktif değil veya mail gönderimi kapalı mı kontrol et
        if ($template->is_active != 1 || $template->send_mail != 1) {
            return $this->CI->db->insert('mail_queue', [
                'template_id' => $template->id, // Şablonun kendi ID'sini kullan
                'to_email' => $to,
                'data' => json_encode($data),
                'priority' => $priority,
                'status' => 'failed',
                'error_message' => 'Mail sending disabled or template not active for: ' . $template_code,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Kuyruğa ekle
        $result = $this->CI->db->insert('mail_queue', [
            'template_id' => $template->id,
            'to_email' => $to,
            'data' => json_encode($data),
            'priority' => $priority,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Eğer kopya gönderimi aktifse ve kopya mail adresi varsa
        if ($template->send_copy == 1 && !empty($template->copy_email) && $to != $template->copy_email) {
            // Kopyayı da kuyruğa ekle
            $this->CI->db->insert('mail_queue', [
                'template_id' => $template->id,
                'to_email' => $template->copy_email,
                'data' => json_encode($data),
                'priority' => $priority,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        return $result;
    }

    // Kuyruktan mail gönderme
    public function process_queue($limit = 5) {
        // SMTP ayarlarını al
        $smtp = $this->CI->db->where('id', 1)->get('smtp')->row();
        $properties = $this->CI->db->where('id', 1)->get('properties')->row();

        // SMTP bilgilerinin kontrolü
        $smtp_missing = false;
        $missing_fields = [];
        
        if (empty($smtp->host)) {
            $smtp_missing = true;
            $missing_fields[] = 'SMTP Host';
        }
        
        if (empty($smtp->port)) {
            $smtp_missing = true;
            $missing_fields[] = 'SMTP Port';
        }
        
        if (empty($smtp->mail)) {
            $smtp_missing = true;
            $missing_fields[] = 'SMTP Mail';
        }
        
        if (empty($smtp->password)) {
            $smtp_missing = true;
            $missing_fields[] = 'SMTP Şifre';
        }
        
        // SMTP bilgileri eksikse, bekleyen mailleri failed olarak işaretle
        if ($smtp_missing) {
            $error_message = 'SMTP bilgileri eksik: ' . implode(', ', $missing_fields);
            
            // Bekleyen tüm mailleri al ve failed olarak işaretle
            $pending_mails = $this->CI->db->where('status', 'pending')
                ->get('mail_queue')
                ->result();
                
            foreach ($pending_mails as $mail) {
                $this->CI->db->where('id', $mail->id)
                    ->update('mail_queue', [
                        'status' => 'failed',
                        'error_message' => $error_message,
                        'retry_count' => 3, // Tekrar deneme yapılmasın
                        'processed_at' => date('Y-m-d H:i:s')
                    ]);
            }
            
            return false;
        }

        // Email ayarları
        $config = [
            'protocol' => 'smtp',
            'smtp_host' => $smtp->host,
            'smtp_port' => $smtp->port,
            'smtp_user' => $smtp->mail,
            'smtp_pass' => $smtp->password,
            'starttls' => true,
            'charset' => 'utf-8',
            'mailtype' => 'html',
            'wordwrap' => true,
            'newline' => "\r\n",
            'crlf' => "\r\n",
            'validate' => true
        ];

        // Bekleyen mailleri al
        $queue_items = $this->CI->db->where('status', 'pending')
            ->where('retry_count <', 3)
            ->order_by('priority', 'DESC')
            ->order_by('created_at', 'ASC')
            ->limit($limit)
            ->get('mail_queue')
            ->result();

        foreach ($queue_items as $item) {
            // İşleme alındı olarak işaretle
            $this->CI->db->where('id', $item->id)
                ->update('mail_queue', ['status' => 'processing']);

            try {
                // Şablonu al
                $template = $this->CI->db->where('id', $item->template_id)
                    ->get('mail_templates')
                    ->row();
                
                if (!$template) {
                    // Şablon bulunamadı veya aktif değil, tekrar deneme
                    $this->CI->db->where('id', $item->id)->update('mail_queue', [
                        'status' => 'failed',
                        'error_message' => 'Template not found or not active',
                        'processed_at' => date('Y-m-d H:i:s')
                    ]);
                    continue;
                }

                // Mail içeriğini hazırla
                $data = json_decode($item->data, true);
                $subject = prepare_mail_content($template->subject, $data);

                // HTML içeriğini düzgün şekilde hazırla
                $message = $template->content;
                $message = str_replace("<br>", "", $message); // Gereksiz <br> etiketlerini temizle
                $message = prepare_mail_content($message, $data);

                // Alıcının user_id'sini bul
                $recipient = $this->CI->db->where('email', $item->to_email)->get('user')->row();
                $recipient_id = $recipient ? $recipient->id : null;

                // Email konfigürasyonunu ayarla
                $this->CI->email->initialize($config);
                $this->CI->email->set_mailtype('html'); // HTML mail tipini belirt
                $this->CI->email->set_newline("\r\n");
                $this->CI->email->from($smtp->mail, $properties->name);
                $this->CI->email->to($item->to_email);
                $this->CI->email->subject($subject);
                $this->CI->email->message($message);

                if ($this->CI->email->send()) {
                    // Başarılı
                    $this->CI->db->where('id', $item->id)->update('mail_queue', [
                        'status' => 'sent',
                        'processed_at' => date('Y-m-d H:i:s')
                    ]);

                    // Mail logs tablosuna başarılı kaydı ekle
                    $this->CI->db->insert('mail_logs', [
                        'template_id' => $item->template_id,
                        'user_id' => $recipient_id,
                        'to_email' => $item->to_email,
                        'subject' => $subject,
                        'content' => $message,
                        'status' => 'success',
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    throw new Exception($this->CI->email->print_debugger());
                }

            } catch (Exception $e) {
                // Hata mesajını kontrol et
                $error_message = $e->getMessage();
                $should_retry = false;
                
                // Zamana bağlı hataları kontrol et (timeout, connection refused gibi)
                $time_based_errors = [
                    'timeout', 'timed out', 'connection refused', 'temporarily unavailable',
                    'try again later', 'rate limit', 'too many connections'
                ];
                
                foreach ($time_based_errors as $time_error) {
                    if (stripos($error_message, $time_error) !== false) {
                        $should_retry = true;
                        break;
                    }
                }
                
                // Hata durumu güncelle
                $this->CI->db->where('id', $item->id)->update('mail_queue', [
                    'status' => 'failed',
                    'error_message' => $error_message,
                    'retry_count' => $should_retry ? ($item->retry_count + 1) : 3, // Zamana bağlı hatalar için tekrar dene, diğerleri için deneme
                    'processed_at' => date('Y-m-d H:i:s')
                ]);

                // Mail logs tablosuna başarısız kaydı ekle
                $this->CI->db->insert('mail_logs', [
                    'template_id' => $item->template_id,
                    'user_id' => $recipient_id,
                    'to_email' => $item->to_email,
                    'subject' => $subject,
                    'content' => $message,
                    'status' => 'failed',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Her mail arasında kısa bekle
            sleep(1);
        }
    }

    // Eski mailleri failed olarak işaretle (1 günden eski)
    public function mark_old_mails_as_failed() {
        $one_day_ago = date('Y-m-d H:i:s', strtotime('-1 day'));
        
        return $this->CI->db->where('created_at <', $one_day_ago)
            ->where('status', 'pending')
            ->update('mail_queue', [
                'status' => 'failed',
                'error_message' => 'Mail is older than 1 day',
                'processed_at' => date('Y-m-d H:i:s')
            ]);
    }

    // Başarısız maili tekrar deneme
    public function retry_failed($id) {
        $mail = $this->CI->db->where('id', $id)->get('mail_queue')->row();
        
        if (!$mail || $mail->status != 'failed') {
            return false;
        }
        
        return $this->CI->db->where('id', $id)->update('mail_queue', [
            'status' => 'pending',
            'error_message' => null,
            'retry_count' => 0,
            'processed_at' => null
        ]);
    }
}