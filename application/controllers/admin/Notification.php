<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends G_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
            redirect(base_url(), 'refresh');
            exit();
        }
        (isPermFunction('seeNotification') != true) ? redirect(base_url('admin')) : NULL;
    }

    public function notificationList()
    {
        //Mevcut bildirimleri kontrol et. Tarihi biteni sonlandır.
        $notifications = $this->db->where('isActive', 'Active')->get('notification_management')->result();
        foreach ($notifications as $notification) {
            if ($notification->end_up < date('Y-m-d H:i:s')) {
                $this->autoCancelNotification($notification->id, 'Süresi Dolduğu İçin Otomatik Sonlandırıldı.');
            }
        }
        $data = [
            'notifications' => $notifications,
            'endedNotifications' => $this->db->limit(250)->where('isActive', 'Passive')->or_where('isActive', 'Cancelled')->order_by('id', 'DESC')->get('notification_management')->result(),
            'systemNotifications' => $this->db->limit(250)->where('sender', 'system')->order_by('id', 'DESC')->get('notifications')->result(),
            'status' => 'notificationList'
        ];

        $this->adminView('notification-list', $data);
    }

    public function newNotification()
    {
        $data = [
            'properties' => $this->db->where('id', 1)->get('properties')->row(),
            'status' => 'notificationList'
        ];

        $this->adminView('new-notification', $data);
    }

    public function createNotification() {
        // Form verilerini al
        $notification_name = $this->input->post('notification_name');
        $notification_title = $this->input->post('notification_title');
        $notification_contents = $this->input->post('notification_contents');
        $notification_link = $this->input->post('notification_link');
        $start_at = $this->input->post('start_at');
        $end_up = $this->input->post('end_up');
        $target_group = $this->input->post('target_group');
        $sender = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();

        /*
        // Kontrol: Başlangıç tarihi geçmiş mi?
        $current_time = date('Y-m-d H:i:s');
        if ($start_at < $current_time) {
            // Başlangıç tarihi geçmişse, hata mesajı göster ve işlemi durdur
            $this->session->set_flashdata('error', 'Başlangıç tarihi geçmiş bir zamanı seçemezsiniz.');
            redirect('admin/notification/newNotification');
            return;
        }
        */

        $notification_data = array(
            'name' => $notification_name,
            'title' => $notification_title,
            'contents' => $notification_contents,
            'link' => $notification_link,
            'img' => changePhoto('assets/img/notifications', 'img'),
            'end_up' => $end_up,
            'target_group' => $target_group,
            'views' => 0, // Başlangıçta görüntülenme sayısı 0
            'maximum_views' => $this->db->count_all('user'), // Maximum görüntülenme sayısı
            'isActive' => 'Active' // Aktif bildirim
        );

        // Bildirimi veritabanına ekle
        $result = $this->db->insert('notification_management', $notification_data);
        $notification_id = $this->db->insert_id();
        if ($result){
            $user_ids = range(1,$this->db->count_all('user'));

            $notifications = array();
            $seen_at = 1;
            $isActive = "Active";

            foreach ($user_ids as $user_id) {
                $notifications[] = array(
                    'user_id' => $user_id,
                    'notification_id' => $notification_id,
                    'seen_at' => $seen_at,
                    'isActive' => $isActive,
                    'created_at' => date('Y-m-d H:i:s'),
                    'sender' => $sender->name . ' ' . $sender->surname . ' (' . $sender->email . ')'
                );
            }

            $result2 = $this->db->insert_batch('notifications', $notifications);

            flash('Başarılı', 'Bildirim oluşturuldu. Gönderim tamamlandı.');
            redirect(base_url('admin/notification/newNotification'), 'refresh');
        }else{
            flash('Başarısız.', 'Bildirim oluşturulamadı.');
            redirect(base_url('admin/notification/newNotification'), 'refresh');
        }
    }

    public function cancelNotification($notification_id)
    {
        $admin = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        //Notification Management tablosundaki veriyi iptal et.
        $result = $this->db->where('id', $notification_id)->update('notification_management', ['isActive' => 'Passive', 'cancel_reason' => $admin->name . ' Tarafından iptal edildi.']);
        //Notification tablosundaki veriyi iptal et.
        $result2 = $this->db->set('isActive', 'Passive')->where('notification_id', $notification_id)->update('notifications');

        if ($result && $result2) {
            flash('Başarılı', 'Bildirim iptal edildi. Tüm bildirimler geri çekildi.');
            redirect(base_url('admin/notification/notificationList'), 'refresh');
        } else {
            flash('Başarısız.', 'Bildirim iptal edilemedi.');
            redirect(base_url('admin/notification/notificationList'), 'refresh');
        }
    }

    public function autoCancelNotification($notification_id, $cancelReason)
    {
        $admin = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        //Notification Management tablosundaki veriyi iptal et.
        $result = $this->db->where('id', $notification_id)->update('notification_management', ['isActive' => 'Passive', 'cancel_reason' => $cancelReason]);
        //Notification tablosundaki veriyi iptal et.
        $result2 = $this->db->set('isActive', 'Passive')->where('notification_id', $notification_id)->update('notifications');

    }
    public function statistics($nt_id)
    {
        $notification = $this->db->where('id', $nt_id)->get('notification_management')->row();
        if (!$notification) {
            flash('Hata', 'Bildirim bulunamadı.');
            redirect(base_url('admin/notification/notificationList'), 'refresh');
        }
        $data = [
            'notification' => $notification,
            'notification_statistics' => $this->db->where('notification_id', $nt_id)->get('notifications')->result(),
            'status' => 'statistics'
        ];

        $this->adminView('notification_statistics', $data);
    }

    public function get_daily_notification_counts($notification_id) {
        $this->db->select('DATE(n.seen_date) as date, COUNT(n.id) as notification_count');
        $this->db->from('notifications n');
        $this->db->where('n.notification_id', $notification_id);
        $this->db->where('n.seen_at', 0); // Sadece görülen bildirimleri say
        $this->db->where('n.seen_date >= CURDATE() - INTERVAL 7 DAY');
        $this->db->group_by('DATE(n.seen_date)');
        $query = $this->db->get();
        $result = $query->result();

        echo json_encode($result);
    }




}