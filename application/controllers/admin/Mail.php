<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mail extends G_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
            redirect(base_url(), 'refresh');
            exit();
        }
        //(isPermFunction('seeNotification') != true) ? redirect(base_url('admin')) : NULL;
        
        // Form validation kütüphanesini yükle
        $this->load->library('form_validation');
    }

    // Mail şablonları listesi
    public function templates() {
        $data['templates'] = $this->db->select('*')
            ->from('mail_templates')
            ->where('is_active', 1)
            ->order_by('created_at', 'DESC')
            ->get()
            ->result();

        $data['status'] = 'mailTemplates';
        $data['title'] = 'Mail Şablonları';
        $data['breadcrumb'] = 'Mail Şablonları';

        $this->adminView('mail/templates', $data);
    }

    //İçerik temizleme fonksiyonu
    private function cleanHtmlContent($content) {
        // Gereksiz boşlukları temizle
        $content = preg_replace('/\s+/', ' ', $content);

        // CKEditor'ün eklediği gereksiz <p> etiketlerini temizle
        $content = str_replace(['<p>', '</p>'], '', $content);

        // Çift boşlukları tek boşluğa çevir
        $content = preg_replace('/\s+/', ' ', $content);

        // Başındaki ve sonundaki boşlukları temizle
        $content = trim($content);

        return $content;
    }

    // Yeni şablon ekleme
    public function add_template() {
        // Form doğrulama kütüphanesini yükle
        $this->load->library('form_validation');

        // Form doğrulama kuralları belirle
        $this->form_validation->set_rules('name', 'Şablon Adı', 'required|trim');
        $this->form_validation->set_rules('code', 'Şablon Kodu', 'required|in_list[welcome_mail,mail_verification,password_reset,default,guest_registration,new_order,order_delivery,cancel_delivery,balance_success,subscription_start,ticket_reply]');
        $this->form_validation->set_rules('subject', 'Mail Konusu', 'required|trim');
        $this->form_validation->set_rules('content', 'İçerik', 'required');

        // Form gönderildiği durumda doğrulamayı çalıştır
        if ($this->form_validation->run() == FALSE) {
            // Doğrulama hataları varsa, flash mesajı oluştur
            flash('Başarısız', validation_errors(), 'error');
            redirect('mail/templates');
            return;
        }

        $content = $this->cleanHtmlContent($this->input->post('content', FALSE));
        $content = str_replace("<br>", "", $content); // Gereksiz <br> etiketlerini temizle
        $content = trim($content); // Başındaki ve sonundaki boşlukları temizle
        
        // Aynı koda sahip şablonları deaktif et
        $template_code = $this->input->post('code', TRUE);
        $this->db->where('code', $template_code)
            ->where('is_active', 1)
            ->update('mail_templates', ['is_active' => 0]);

        // Doğrulama başarılıysa, verileri temizleyip kaydet
        $data = [
            'name' => $this->input->post('name', TRUE),
            'code' => $template_code,
            'subject' => $this->input->post('subject', TRUE),
            'content' => html_entity_decode($content), // HTML entity'leri decode et
            'is_active' => 1, // Şablon aktif
            'send_mail' => $this->input->post('send_mail') ? 1 : 0, // Mail gönderimi
            'send_copy' => $this->input->post('send_copy') ? 1 : 0, // Kopya gönderimi
            'copy_email' => $this->input->post('copy_email'), // Kopya mail adresi
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->db->insert('mail_templates', $data)) {
            flash('Başarılı', 'Şablon başarıyla eklendi.');
        } else {
            flash('Başarısız', 'Şablon eklenirken bir hata oluştu.');
        }

        redirect(base_url('admin/mail/templates'));
    }
    public function get_template($id) {
        $template = $this->db->get_where('mail_templates', ['id' => $id])->row();

        if ($template) {
            // Veriyi JSON olarak döndür
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($template));
        } else {
            $response = ['error' => 'Şablon bulunamadı.'];
            $this->output
                ->set_status_header(404)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }
    }

    public function edit_template($id) {
        // Gerekli kütüphaneleri ve modeli yükleyin
        $this->load->library('form_validation');

        // Form doğrulama kuralları
        $this->form_validation->set_rules('name', 'Şablon Adı', 'required|trim');
        $this->form_validation->set_rules('code', 'Şablon Kodu', 'required|in_list[welcome_mail,mail_verification,password_reset,default,guest_registration,new_order,order_delivery,cancel_delivery,balance_success,subscription_start,ticket_reply]');
        $this->form_validation->set_rules('subject', 'Mail Konusu', 'required|trim');
        $this->form_validation->set_rules('content', 'İçerik', 'required');

        if ($this->form_validation->run() == FALSE) {
            // Doğrulama hataları varsa
            flash('Hata', validation_errors(), 'error');
            redirect(base_url('admin/mail/templates'));
            return;
        }

        $content = $this->cleanHtmlContent($this->input->post('content', FALSE));
        $content = str_replace("<br>", "", $content); // Gereksiz <br> etiketlerini temizle
        $content = trim($content); // Başındaki ve sonundaki boşlukları temizle
        
        // Mevcut şablonun kodunu al
        $current_template = $this->db->get_where('mail_templates', ['id' => $id])->row();
        $new_code = $this->input->post('code', TRUE);
        $is_active = $this->input->post('is_active') ? 1 : 0;
        
        // Eğer kod değiştiyse ve yeni şablon aktifse, aynı kodlu diğer şablonları deaktif et
        if ($current_template && $current_template->code != $new_code && $is_active == 1) {
            $this->db->where('code', $new_code)
                ->where('id !=', $id)
                ->where('is_active', 1)
                ->update('mail_templates', ['is_active' => 0]);
        }
        // Eğer kod değişmediyse ve şablon aktifse, aynı kodlu diğer şablonları deaktif et
        else if ($current_template && $current_template->code == $new_code && $is_active == 1) {
            $this->db->where('code', $new_code)
                ->where('id !=', $id)
                ->where('is_active', 1)
                ->update('mail_templates', ['is_active' => 0]);
        }

        $data = [
            'name'       => $this->input->post('name', TRUE),
            'code'       => $new_code,
            'subject'    => $this->input->post('subject', TRUE),
            'content'    => html_entity_decode($content),
            'is_active'  => 1, // Şablon aktif
            'send_mail'  => $this->input->post('send_mail') ? 1 : 0, // Mail gönderimi
            'send_copy'  => $this->input->post('send_copy') ? 1 : 0, // Kopya gönderimi
            'copy_email' => $this->input->post('copy_email'), // Kopya mail adresi
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Model yardımıyla güncelleme işlemi
        $updated = $this->db->where('id', $id)->update('mail_templates', $data);

        if($updated) {
            flash('Başarılı', 'Şablon başarıyla güncellendi.');
        } else {
            flash('Hata', 'Şablon güncellenirken bir hata oluştu.', 'error');
        }

        redirect(base_url('admin/mail/templates'));
    }


    public function upload_image()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $response = [
                'error' => true,
                'message' => 'Geçersiz istek yöntemi'
            ];
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        // Files'ın ilk öğesini al
        if (isset($_FILES) && !empty($_FILES)) {
            $fileKey = array_key_first($_FILES);
            $file = [
                'name' => $_FILES[$fileKey]['name'][0],
                'type' => $_FILES[$fileKey]['type'][0],
                'tmp_name' => $_FILES[$fileKey]['tmp_name'][0],
                'error' => $_FILES[$fileKey]['error'][0],
                'size' => $_FILES[$fileKey]['size'][0]
            ];
        } else {
            $response = [
                'error' => true,
                'message' => 'Dosya verisi bulunamadı'
            ];
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $response = [
                'error' => true,
                'message' => 'Dosya yüklenirken hata oluştu. Hata kodu: ' . $file['error']
            ];
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        // Uzantı kontrolü
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExtension, $allowedfileExtensions)) {
            $response = [
                'error' => true,
                'message' => 'Sadece JPG, JPEG, PNG, GIF dosyalarına izin verilir.'
            ];
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        $uploadDir = FCPATH . 'assets/uploads/mail/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Yeni dosya adı oluştur
        $newFileName = md5(time() . $file['name']) . '.' . $fileExtension;
        $dest_path = $uploadDir . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $dest_path)) {
            $response = [
                'error' => true,
                'message' => 'Dosya taşınırken bir hata oluştu.'
            ];
            return $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        }

        // Başarılı yanıt
        $uploadedUrl = base_url('assets/uploads/mail/' . $newFileName);
        $response = [
            'success' => true,
            'error' => false,
            'files' => [$uploadedUrl],
            'path' => $newFileName,
            'baseurl' => base_url('assets/uploads/mail/'),
            'message' => 'Dosya başarıyla yüklendi'
        ];

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    // Şablon silme
    public function delete_template($id) {
        if ($this->db->where('id', $id)->delete('mail_templates')) {
            flash('Başarılı', 'Şablon başarıyla silindi.');
        } else {
            flash('Başarısız', 'Şablon silinirken bir hata oluştu.');
        }

        redirect('mail/templates');
    }

    // Test mail gönderme
    public function test_template($id) {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        // Mail şablonunu al
        $template = $this->db->where('id', $id)->get('mail_templates')->row();
        if (!$template) {
            echo json_encode(['status' => 'error', 'message' => 'Şablon bulunamadı.']);
            return;
        }
        
        // SMTP ayarlarını al
        $smtp = $this->db->where('id', 1)->get('smtp')->row();
        $properties = $this->db->where('id', 1)->get('properties')->row();

        // Test mail adresi al
        $test_email = $this->input->post('test_email');
        if (empty($test_email)) {
            echo json_encode(['status' => 'error', 'message' => 'Mail adresi gereklidir.']);
            return;
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
            'encoding' => '8bit',
            'mailtype' => 'html',
            'wordwrap' => true,
            'newline' => "\r\n",
            'crlf' => "\r\n",
            'validate' => true
        ];

        // Test verilerini hazırla
        $test_data = [
            'name' => 'Test',
            'surname' => 'Kullanıcı',
            'email' => $test_email,
            'company_name' => $properties->name,
            'company_logo' => base_url('assets/img/') . $properties->img,
            'company_url' => base_url(),
            'amount' => '500.00',
            'currency' => 'TL',
            'transaction_date' => date('d.m.Y H:i'),
            'transaction_id' => 'TRX'.time(),
            'old_balance' => '100.00',
            'new_balance' => '600.00',
            'payment_method' => 'Kredi Kartı',
            'current_balance' => '600.00',
            'support_email' => 'destek@example.com'
        ];

        // Mail içeriğini hazırla
        $subject = prepare_mail_content($template->subject, $test_data);
        $message = prepare_mail_content($template->content, $test_data);

        // Email gönder
        $this->load->library('email');
        $this->email->initialize($config);
        $this->email->from($smtp->mail, $properties->name);
        $this->email->to($test_email);
        $this->email->subject($subject);
        $this->email->message($message);

        if ($this->email->send()) {
            // Mail logs tablosuna kaydet
            $this->db->insert('mail_logs', [
                'template_id' => $template->id,
                'user_id' => $this->session->userdata('info')['id'],
                'to_email' => $test_email,
                'subject' => $subject,
                'content' => $message,
                'status' => 'success',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Test maili başarıyla gönderildi.']);
        } else {
            // Mail logs tablosuna başarısız kaydı ekle
            $this->db->insert('mail_logs', [
                'template_id' => $template->id,
                'user_id' => $this->session->userdata('info')['id'],
                'to_email' => $test_email,
                'subject' => $subject,
                'content' => $message,
                'status' => 'failed',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            echo json_encode(['status' => 'error', 'message' => 'Test maili gönderilirken bir hata oluştu.']);
        }
    }

    // Şablon adını getir
    public function get_template_name($id) {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $template = $this->db->select('name')->where('id', $id)->get('mail_templates')->row();
        if (!$template) {
            echo json_encode(['status' => 'error', 'message' => 'Şablon bulunamadı.']);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'name' => $template->name
        ]);
    }

    // Mail logları listesi
    public function logs() {
        $data['logs'] = $this->db->select('mail_logs.*, mail_templates.name as template_name, user.name as user_name, user.surname as user_surname')
            ->from('mail_logs')
            ->join('mail_templates', 'mail_templates.id = mail_logs.template_id', 'left')
            ->join('user', 'user.id = mail_logs.user_id', 'left')
            ->order_by('mail_logs.id', 'DESC')
            ->get()
            ->result();

        $data['status'] = 'mailLogs';
        $data['title'] = 'Mail Logları';
        $data['breadcrumb'] = 'Mail Logları';

        $this->adminView('mail/logs', $data);
    }

    // Mail detay görüntüleme
    public function view_mail($id) {
        $data['mail'] = $this->db->select('mail_logs.*, mail_templates.name as template_name, user.name as user_name, user.surname as user_surname')
            ->from('mail_logs')
            ->join('mail_templates', 'mail_templates.id = mail_logs.template_id', 'left')
            ->join('user', 'user.id = mail_logs.user_id', 'left')
            ->where('mail_logs.id', $id)
            ->get()
            ->row();

        if (!$data['mail']) {
            show_404();
        }

        $this->load->view('admin/mail/view_mail', $data);
    }

    // Başarısız maili tekrar gönderme
    public function retry_mail($id) {
        $mail = $this->db->where('id', $id)->get('mail_queue')->row();

        if ($mail && $mail->status == 'failed') {
            $this->load->library('mailer');
            if ($this->mailer->retry_failed($id)) {
                flash('Başarılı', 'Mail tekrar kuyruğa alındı.');
            } else {
                flash('Başarısız', 'Mail tekrar kuyruğa alınırken bir hata oluştu.');
            }
        }

        redirect('mail/logs');
    }

    // Mail kaydı silme
    public function delete_log($id) {
        if ($this->db->where('id', $id)->delete('mail_logs')) {
            flash('Başarılı', 'Mail kaydı silindi.');
        } else {
            flash('Başarısız', 'Mail kaydı silinirken bir hata oluştu.');
        }

        redirect('admin/mail/logs');
    }

    public function get_log_details($id) {
        $log = $this->db->select('mail_logs.*, mail_templates.name as template_name, user.name as user_name, user.surname as user_surname')
            ->from('mail_logs')
            ->join('mail_templates', 'mail_templates.id = mail_logs.template_id', 'left')
            ->join('user', 'user.id = mail_logs.user_id', 'left')
            ->where('mail_logs.id', $id)
            ->get()
            ->row();

        if ($log) {
            $response = [
                'success' => true,
                'data' => $log
            ];
        } else {
            $response = [
                'success' => false,
                'message' => 'Mail log kaydı bulunamadı.'
            ];
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    public function send_bulk_mail() {
        // Form doğrulama
        $this->form_validation->set_rules('subject', 'Mail Başlığı', 'required|trim');
        $this->form_validation->set_rules('content', 'İçerik', 'required');
        $this->form_validation->set_rules('recipient_type', 'Alıcı Tipi', 'required');
        $this->form_validation->set_rules('priority', 'Öncelik', 'required|in_list[1,2,3]');

        if ($this->form_validation->run() == FALSE) {
            flash('Hata', validation_errors(), 'error');
            redirect(base_url('admin/mail/templates'));
            return;
        }

        // Mail içeriğini al
        $content = $this->input->post('content', FALSE);
        $subject = $this->input->post('subject', TRUE);
        $recipient_type = $this->input->post('recipient_type');
        $priority = $this->input->post('priority', TRUE);

        // Geçici şablon oluştur
        $template_data = [
            'name' => 'Toplu Mail - ' . date('d.m.Y H:i:s'),
            'code' => 'bulk_mail_' . time(),
            'subject' => $subject,
            'content' => $content,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Debug için içeriği kontrol et
        log_message('debug', 'Mail content before insert: ' . strlen($content));

        $this->db->insert('mail_templates', $template_data);
        $template_id = $this->db->insert_id();

        // Debug için kaydedilen içeriği kontrol et
        $saved_template = $this->db->get_where('mail_templates', ['id' => $template_id])->row();
        log_message('debug', 'Mail content after insert: ' . strlen($saved_template->content));

        // Alıcıları seç
        $this->db->select('user.*')->from('user');
        $this->db->where('user.isMail', 1)->where('user.isActive', 1);

        switch($recipient_type) {
            case 'all_subscribers':
                $this->db->join('user_subscriptions', 'user_subscriptions.user_id = user.id');
                $this->db->where('user_subscriptions.end_date >=', date('Y-m-d H:i:s'));
                break;

            case 'specific_subscribers':
                $subscription_ids = $this->input->post('subscription_ids');
                if(empty($subscription_ids)) {
                    flash('Hata', 'Lütfen en az bir abonelik seçin.', 'error');
                    redirect(base_url('admin/mail/templates'));
                    return;
                }
                $this->db->join('user_subscriptions', 'user_subscriptions.user_id = user.id');
                $this->db->where_in('user_subscriptions.subscription_id', $subscription_ids);
                $this->db->where('user_subscriptions.end_date >=', date('Y-m-d H:i:s'));
                break;

            case 'referral':
                $referral_code = $this->input->post('referral_code');
                if(empty($referral_code)) {
                    flash('Hata', 'Referans kodu gereklidir.', 'error');
                    redirect(base_url('admin/mail/templates'));
                    return;
                }
                $this->db->join('user_references', 'user_references.buyer_id = user.id');
                $this->db->join('user as referrer', 'referrer.id = user_references.referrer_id');
                $this->db->where('referrer.ref_code', $referral_code);
                break;

            case 'inactive':
                $inactive_days = $this->input->post('inactive_days');
                if(empty($inactive_days)) {
                    flash('Hata', 'Gün sayısı gereklidir.', 'error');
                    redirect(base_url('admin/mail/templates'));
                    return;
                }
                $this->db->join('logs', 'logs.user_id = user.id', 'left');
                $this->db->where('logs.date <', date('Y-m-d H:i:s', strtotime("-$inactive_days days")));
                break;

            case 'new':
                $new_user_days = $this->input->post('new_user_days');
                if(empty($new_user_days)) {
                    flash('Hata', 'Gün sayısı gereklidir.', 'error');
                    redirect(base_url('admin/mail/templates'));
                    return;
                }
                $this->db->where('user.date >=', date('Y-m-d H:i:s', strtotime("-$new_user_days days")));
                break;

            case 'no_purchase':
                $period = $this->input->post('no_purchase_period');
                if($period === 'recent') {
                    $no_purchase_days = $this->input->post('no_purchase_days');
                    if(empty($no_purchase_days)) {
                        flash('Hata', 'Gün sayısı gereklidir.', 'error');
                        redirect(base_url('admin/mail/templates'));
                        return;
                    }
                    $this->db->join('shop', 'shop.user_id = user.id', 'left');
                    $this->db->where('(shop.date < "' . date('Y-m-d H:i:s', strtotime("-$no_purchase_days days")) . '" OR shop.id IS NULL)');
                } else {
                    $this->db->join('shop', 'shop.user_id = user.id', 'left');
                    $this->db->where('shop.id IS NULL');
                }
                break;
        }

        $users = $this->db->group_by('user.id')->get()->result();

        if(empty($users)) {
            // Şablonu deaktif et
            $this->db->where('id', $template_id)->update('mail_templates', ['is_active' => 0]);
            flash('Uyarı', 'Seçilen kriterlere uygun kullanıcı bulunamadı.', 'warning');
            redirect(base_url('admin/mail/templates'));
            return;
        }

        $success_count = 0;
        $error_count = 0;

        // Mailer kütüphanesini yükle
        $this->load->library('mailer');

        // Her kullanıcı için mail kuyruğuna ekle
        foreach ($users as $user) {
            // Mail kuyruğuna ekle
            $mail_queue_data = [
                'template_id' => $template_id,
                'to_email' => $user->email,
                'data' => json_encode([
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'email' => $user->email,
                    'date' => date('d.m.Y H:i')
                ]),
                'status' => 'pending',
                'error_message' => NULL,
                'retry_count' => 0,
                'priority' => $priority
            ];

            if ($this->db->insert('mail_queue', $mail_queue_data)) {
                $success_count++;
            } else {
                $error_count++;
            }
        }

        // Şablonu deaktif et
        $this->db->where('id', $template_id)->update('mail_templates', ['is_active' => 0]);

        if ($success_count > 0) {
            flash('Başarılı', $success_count . ' mail kuyruğa eklendi.' . ($error_count > 0 ? ' ' . $error_count . ' mail eklenemedi.' : ''));
        } else {
            flash('Hata', 'Hiçbir mail kuyruğa eklenemedi.', 'error');
        }

        redirect(base_url('admin/mail/templates'));
    }

    public function bulk_mail() {
        $data['status'] = 'mailBulk';
        $data['title'] = 'Toplu Mail Gönderimi';
        $data['breadcrumb'] = 'Toplu Mail Gönderimi';

        // Aktif abonelikleri getir
        $data['subscriptions'] = $this->db->where('isActive', 1)->get('subscriptions')->result();

        // İstatistikler
        $stats = [
            'total_users' => $this->db->where('isActive', 1)->where('isMail', 1)->count_all_results('user'),
            'total_subscribers' => $this->db->where('end_date >=', date('Y-m-d H:i:s'))->count_all_results('user_subscriptions'),
            'total_referrals' => $this->db->count_all_results('user_references'),
            'new_users_7days' => $this->db->where('date >=', date('Y-m-d H:i:s', strtotime("-7 days")))->count_all_results('user'),
            'new_users_30days' => $this->db->where('date >=', date('Y-m-d H:i:s', strtotime("-30 days")))->count_all_results('user'),
            'inactive_users_7days' => $this->db->query("
                SELECT COUNT(DISTINCT user.id) as count
                FROM user 
                LEFT JOIN logs ON logs.user_id = user.id
                WHERE user.isActive = 1 
                AND user.isMail = 1
                AND (logs.date < ? OR logs.id IS NULL)", 
                [date('Y-m-d H:i:s', strtotime("-7 days"))]
            )->row()->count,
            'inactive_users_30days' => $this->db->query("
                SELECT COUNT(DISTINCT user.id) as count
                FROM user 
                LEFT JOIN logs ON logs.user_id = user.id
                WHERE user.isActive = 1 
                AND user.isMail = 1
                AND (logs.date < ? OR logs.id IS NULL)", 
                [date('Y-m-d H:i:s', strtotime("-30 days"))]
            )->row()->count,
            'no_purchase_all' => $this->db->query("
                SELECT COUNT(DISTINCT user.id) as count
                FROM user 
                LEFT JOIN shop ON shop.user_id = user.id
                WHERE user.isActive = 1 
                AND user.isMail = 1
                AND shop.id IS NULL"
            )->row()->count,
            'no_purchase_30days' => $this->db->query("
                SELECT COUNT(DISTINCT user.id) as count
                FROM user 
                LEFT JOIN shop ON shop.user_id = user.id
                WHERE user.isActive = 1 
                AND user.isMail = 1
                AND (shop.date < ? OR shop.id IS NULL)", 
                [date('Y-m-d H:i:s', strtotime("-30 days"))]
            )->row()->count
        ];

        $data['stats'] = $stats;
        $this->adminView('mail/bulk_mail', $data);
    }

    public function get_recipient_count() {
        $recipient_type = $this->input->post('recipient_type');
        $params = $this->input->post();

        $this->db->select('user.id')->from('user');
        $this->db->where('user.isMail', 1)->where('user.isActive', 1);

        switch($recipient_type) {
            case 'all_subscribers':
                $this->db->join('user_subscriptions', 'user_subscriptions.user_id = user.id');
                $this->db->where('user_subscriptions.end_date >=', date('Y-m-d H:i:s'));
                break;

            case 'specific_subscribers':
                if(empty($params['subscription_ids'])) {
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'Lütfen en az bir abonelik seçin.'
                    ]);
                    return;
                }
                $this->db->join('user_subscriptions', 'user_subscriptions.user_id = user.id');
                $this->db->where_in('user_subscriptions.subscription_id', $params['subscription_ids']);
                $this->db->where('user_subscriptions.end_date >=', date('Y-m-d H:i:s'));
                break;

            case 'referral':
                if(empty($params['referral_code'])) {
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'Referans kodu gereklidir.'
                    ]);
                    return;
                }
                $this->db->join('user_references', 'user_references.buyer_id = user.id');
                $this->db->join('user as referrer', 'referrer.id = user_references.referrer_id');
                $this->db->where('referrer.ref_code', $params['referral_code']);
                break;

            case 'inactive':
                if(empty($params['inactive_days'])) {
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'Gün sayısı gereklidir.'
                    ]);
                    return;
                }
                $this->db->join('logs', 'logs.user_id = user.id', 'left');
                $this->db->where('logs.date <', date('Y-m-d H:i:s', strtotime("-{$params['inactive_days']} days")));
                break;

            case 'new':
                if(empty($params['new_user_days'])) {
                    echo json_encode([
                        'status' => 'error', 
                        'message' => 'Gün sayısı gereklidir.'
                    ]);
                    return;
                }
                $this->db->where('user.date >=', date('Y-m-d H:i:s', strtotime("-{$params['new_user_days']} days")));
                break;

            case 'no_purchase':
                if($params['no_purchase_period'] === 'recent') {
                    if(empty($params['no_purchase_days'])) {
                        echo json_encode([
                            'status' => 'error', 
                            'message' => 'Gün sayısı gereklidir.'
                        ]);
                        return;
                    }
                    $this->db->join('shop', 'shop.user_id = user.id', 'left');
                    $this->db->where('(shop.date < "' . date('Y-m-d H:i:s', strtotime("-{$params['no_purchase_days']} days")) . '" OR shop.id IS NULL)');
                } else {
                    $this->db->join('shop', 'shop.user_id = user.id', 'left');
                    $this->db->where('shop.id IS NULL');
                }
                break;
        }

        $count = $this->db->group_by('user.id')->get()->num_rows();

        echo json_encode([
            'status' => 'success',
            'count' => $count,
            'message' => $count . ' kullanıcıya mail gönderilecek.'
        ]);
    }

    // Eski mailleri failed olarak işaretleyen metot
    public function mark_old_emails_as_failed() {
        if (!$this->input->is_ajax_request() && !$this->input->is_cli_request()) {
            exit('No direct script access allowed');
        }

        $this->load->library('mailer');
        $affected_rows = $this->mailer->mark_old_mails_as_failed();
        
        if ($this->input->is_ajax_request()) {
            echo json_encode(['status' => 'success', 'affected_rows' => $affected_rows]);
        } else {
            echo "Affected rows: " . $affected_rows . "\n";
        }
    }
}
