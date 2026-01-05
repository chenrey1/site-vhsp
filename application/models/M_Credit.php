<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Credit extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Kredi tekliflerini listele
     */
    public function getAllCreditOffers()
    {
        $this->db->select('co.*, u.name, u.surname, u.email');
        $this->db->from('credit_offers co');
        $this->db->join('user u', 'co.user_id = u.id');
        $this->db->order_by('co.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Kullanıcıya ait kredi tekliflerini listele
     */
    public function getUserCreditOffers($user_id, $status = null)
    {
        $this->db->select('co.*');
        $this->db->from('credit_offers co');
        $this->db->where('co.user_id', $user_id);
        
        if ($status !== null) {
            $this->db->where('co.status', $status);
        }
        
        $this->db->order_by('co.created_at', 'DESC');
        
        return $this->db->get()->result();
    }

    /**
     * Kredi teklifi oluştur
     */
    public function createCreditOffer($data)
    {
        $this->db->insert('credit_offers', $data);
        return $this->db->insert_id();
    }

    /**
     * Kredi teklifi detaylarını getir
     */
    public function getCreditOfferById($id)
    {
        return $this->db->where('id', $id)->get('credit_offers')->row();
    }

    /**
     * Kredi teklifi durumunu güncelle
     */
    public function updateCreditOfferStatus($id, $status, $accepted_amount = 0, $note = '')
    {
        $data = [
            'status' => $status,
            'admin_note' => $note,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        if ($accepted_amount > 0) {
            $data['accepted_amount'] = $accepted_amount;
        }
        
        $this->db->where('id', $id);
        return $this->db->update('credit_offers', $data);
    }

    /**
     * Kredi teklifi sil
     */
    public function deleteCreditOffer($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('credit_offers');
    }

    /**
     * Kullanıcının kredilerini getir
     */
    public function getUserCredits($user_id)
    {
        $this->db->select('uc.*, co.fee_percentage, co.term_days');
        $this->db->from('user_credits uc');
        $this->db->join('credit_offers co', 'uc.offer_id = co.id');
        $this->db->where('uc.user_id', $user_id);
        $this->db->order_by('uc.created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Kredi detayını getir
     */
    public function getCreditById($id)
    {
        return $this->db->where('id', $id)->get('user_credits')->row();
    }

    /**
     * Kredi ödemesi ekle
     */
    public function addCreditPayment($data)
    {
        // Önce ödemeyi ekle
        $this->db->insert('credit_payments', $data);
        $payment_id = $this->db->insert_id();
        
        // Kredi bilgilerini al
        $credit = $this->getCreditById($data['credit_id']);
        
        // Yeni kalan tutarı hesapla
        $new_remaining_amount = $credit->remaining_amount - $data['amount'];
        
        // Kredi durumunu belirle
        $credit_status = 1; // Aktif
        if ($new_remaining_amount <= 0 || $data['is_final_payment'] == 1) {
            $credit_status = 2; // Ödendi
            $new_remaining_amount = 0;
        } else if ($new_remaining_amount < $credit->remaining_amount) {
            $credit_status = 3; // Kısmi Ödendi
        }
        
        // Krediyi güncelle
        $this->db->where('id', $data['credit_id']);
        $this->db->update('user_credits', [
            'remaining_amount' => $new_remaining_amount,
            'status' => $credit_status,
            'payment_count' => $credit->payment_count + 1,
            'last_payment_date' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Kredi işlem logunu kaydet
        $log_data = [
            'credit_id' => $data['credit_id'],
            'user_id' => $data['user_id'],
            'action' => 'payment_add',
            'amount' => $data['amount'],
            'description' => $data['is_final_payment'] == 1 ? 'Son kredi ödemesi yapıldı' : 'Kredi ödemesi yapıldı',
            'admin_id' => $this->session->userdata('info')['id'],
            'created_at' => date('Y-m-d H:i:s'),
            'ip_address' => $this->input->ip_address()
        ];
        
        $this->db->insert('credit_logs', $log_data);
        
        // Bildirim gönder
        $notification_data = [
            'user_id' => $data['user_id'],
            'title' => 'Kredi Ödemeniz Alındı',
            'contents' => $data['amount'] . ' TL tutarındaki kredi ödemeniz alındı. ' . 
                          ($credit_status == 2 ? 'Krediniz tamamen ödendi.' : 'Kalan ödeme: ' . $new_remaining_amount . ' TL'),
            'link' => base_url('client/balance'),
            'seen_at' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'isActive' => 'Active',
            'sender' => 'admin'
        ];
        
        $this->db->insert('notifications', $notification_data);
        
        return $payment_id;
    }

    /**
     * Kredi ödemelerini getir
     */
    public function getCreditPayments($credit_id)
    {
        return $this->db->where('credit_id', $credit_id)
                 ->order_by('created_at', 'DESC')
                 ->get('credit_payments')
                 ->result();
    }

    /**
     * Vadesi geçmiş kredileri kontrol et ve güncelle
     */
    public function checkOverdueCredits()
    {
        // Şu anki tarih
        $current_date = date('Y-m-d H:i:s');
        
        // Vadesi geçmiş aktif kredileri bul
        $this->db->where('due_date <', $current_date);
        $this->db->where('status', 1); // Aktif
        $this->db->or_where('status', 3); // Kısmi Ödendi
        $overdue_credits = $this->db->get('user_credits')->result();
        
        $updated_count = 0;
        
        // Her bir vadesi geçmiş krediyi güncelle
        foreach ($overdue_credits as $credit) {
            $this->db->where('id', $credit->id);
            $this->db->update('user_credits', [
                'status' => 4, // Vadesi Geçmiş
                'updated_at' => $current_date
            ]);
            
            // Kredi işlem logunu kaydet
            $log_data = [
                'credit_id' => $credit->id,
                'user_id' => $credit->user_id,
                'action' => 'credit_overdue',
                'description' => 'Cari hesap vadesi geçti. Vade tarihi: ' . $credit->due_date,
                'created_at' => $current_date,
                'ip_address' => $this->input->ip_address()
            ];
            
            $this->db->insert('credit_logs', $log_data);
            
            // Bildirim gönder
            $notification_data = [
                'user_id' => $credit->user_id,
                'title' => 'Cari Hesap Vade Uyarısı',
                'contents' => 'Cari hesabınızın vadesi geçti. Lütfen en kısa sürede ödeme yapınız. Kalan tutar: ' . $credit->remaining_amount . ' TL',
                'link' => base_url('client/balance'),
                'seen_at' => 1,
                'created_at' => $current_date,
                'isActive' => 'Active',
                'sender' => 'admin'
            ];
            
            $this->db->insert('notifications', $notification_data);
            
            $updated_count++;
        }
        
        return $updated_count;
    }

    /**
     * Kullanıcının toplam kalan kredi borcunu hesapla
     */
    public function getUserTotalRemainingCreditAmount($user_id)
    {
        $this->db->select_sum('remaining_amount');
        $this->db->where('user_id', $user_id);
        $this->db->where_in('status', [1, 3, 4]); // Aktif, Kısmi Ödeme, Gecikti
        
        $result = $this->db->get('user_credits')->row();
        
        return $result ? $result->remaining_amount : 0;
    }

    /**
     * Kullanıcının ödenmiş toplam kredi tutarını hesapla
     */
    public function getUserTotalPaidCreditAmount($user_id)
    {
        $this->db->select_sum('amount');
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 1); // Onaylanmış ödemeler
        
        $result = $this->db->get('credit_payments')->row();
        
        return $result ? $result->amount : 0;
    }

    /**
     * Kullanıcı kredisi ekle
     */
    public function addUserCredit($data)
    {
        $this->db->insert('user_credits', $data);
        return $this->db->insert_id();
    }

    /**
     * Kullanıcının tüm kredi ödemelerini getir
     */
    public function getUserCreditPayments($user_id)
    {
        $this->db->select('cp.*, uc.amount as credit_amount, uc.remaining_amount');
        $this->db->from('credit_payments cp');
        $this->db->join('user_credits uc', 'cp.credit_id = uc.id');
        $this->db->where('cp.user_id', $user_id);
        $this->db->order_by('cp.created_at', 'DESC');
        return $this->db->get()->result();
    }
} 