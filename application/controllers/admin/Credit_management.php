<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Credit_management extends G_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Admin girişi kontrolü
        if (!isset($this->session->userdata('info')['isAdmin']) || $this->session->userdata('info')['isAdmin'] != 1) {
            redirect(base_url(), 'refresh');
            exit();
        }
        
        // Admin yetkisi kontrolü - kredi verme yetkisi kontrol edilebilir
        (isPermFunction('seeProduct') != true) ? redirect(base_url('admin')) : NULL;
        
        // Kredi modeli yükleniyor
        $this->load->model('M_Credit');
    }

    /**
     * Kredi yönetimi ana sayfası
     */
    public function index()
    {
        // Kredi verilebilecek kullanıcıların listesini al
        $allowed_users = $this->get_allowed_credit_users();
        
        // Değişkenleri view'a aktar
        $data['dealers'] = $allowed_users['dealers'];
        $data['user_types'] = $allowed_users['user_types'];
        
        // İleride burası güncellenecek
        // $data['sellers'] = $allowed_users['sellers'];
        
        // Kredi tekliflerini listele
        $data['offers'] = $this->db->select('co.*, u.name, u.surname, u.email')
                             ->from('credit_offers co')
                             ->join('user u', 'co.user_id = u.id')
                             ->order_by('co.created_at', 'DESC')
                             ->get()->result();
        
        $data['status'] = 'creditManagement';
        $data['properties'] = $this->db->where('id', 1)->get('properties')->row();
        
        $this->adminView('credit/management', $data);
    }
    
    /**
     * Kredi teklifi oluştur
     */
    public function create_offer()
    {
        $user_id = $this->input->post('user_id');
        $amount = $this->input->post('amount');
        $description = $this->input->post('description');
        $fee_percentage = $this->input->post('fee_percentage');
        $term_days = $this->input->post('term_days') ? $this->input->post('term_days') : 30;
        
        // Kullanıcının kredi almaya uygun olup olmadığını kontrol et
        $eligibility = $this->check_user_credit_eligibility($user_id);
        
        if (!$eligibility['eligible']) {
            flash('Hata', $eligibility['message']);
            redirect(base_url('admin/credit_management'));
            return;
        }
        
        // Kullanıcının aktif kredesini ve bekleyen tekliflerini kontrol et
        $credit_status = $this->check_user_credit_status($user_id);
        
        // Aktif kredi varsa, işlemi reddet
        if ($credit_status['has_active_credit']) {
            flash('Hata', 'Kullanıcının aktif kredisi bulunmaktadır. Yeni kredi teklifi verilemez.');
            redirect(base_url('admin/credit_management'));
            return;
        }
        
        // Bekleyen teklif varsa önce onları sil
        if (count($credit_status['pending_offers']) > 0) {
            foreach ($credit_status['pending_offers'] as $offer) {
                $this->db->where('id', $offer->id)->delete('credit_offers');
                addlog('deleteCreditOffer', 'Yeni teklif için eski kredi teklifi silindi: ID: ' . $offer->id);
            }
        }
        
        // Teklifin geçerlilik süresi (varsayılan 7 gün)
        $valid_days = $this->input->post('valid_days') ? $this->input->post('valid_days') : 7;
        $offer_valid_until = date('Y-m-d H:i:s', strtotime('+' . $valid_days . ' days'));
        
        // Kredi teklifi verisi
        $data = [
            'user_id' => $user_id,
            'amount' => $amount,
            'fee_percentage' => $fee_percentage,
            'term_days' => $term_days,
            'offer_valid_until' => $offer_valid_until,
            'status' => 1, // 1: Aktif
            'created_at' => date('Y-m-d H:i:s'),
            'admin_id' => $this->session->userdata('info')['id']
        ];
        
        // Teklifi kaydet
        $this->db->insert('credit_offers', $data);
        $offer_id = $this->db->insert_id();
        
        // Log ekle
        addlog('createCreditOffer', 'Kredi teklifi oluşturuldu: ' . $amount . ' TL, Kullanıcı ID: ' . $user_id);
        
        // Bildirim gönder
        $notification_data = [
            'user_id' => $user_id, 
            'title' => 'Yeni Kredi Teklifiniz Var',
            'contents' => $amount . ' TL tutarında yeni bir kredi teklifiniz bulunmaktadır.',
            'link' => base_url('client/balance'),
            'seen_at' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'isActive' => 'Active',
            'sender' => 'admin'
        ];
        
        $this->db->insert('notifications', $notification_data);
        
        // Kredi işlem logunu kaydet
        $log_data = [
            'offer_id' => $offer_id,
            'user_id' => $user_id,
            'action' => 'offer_create',
            'amount' => $amount,
            'description' => $description ? $description : 'Kredi teklifi oluşturuldu',
            'admin_id' => $this->session->userdata('info')['id'],
            'created_at' => date('Y-m-d H:i:s'),
            'ip_address' => $this->input->ip_address()
        ];
        
        $this->db->insert('credit_logs', $log_data);
        
        flash('Başarılı', 'Kredi teklifi başarıyla oluşturuldu.');
        redirect(base_url('admin/credit_management'));
    }
    
    /**
     * Kredi teklifini sil
     */
    public function delete_offer($id)
    {
        // Teklifi sil öncesinde durumunu kontrol et
        $offer = $this->db->where('id', $id)->get('credit_offers')->row();
        
        if (!$offer) {
            flash('Hata', 'Teklif bulunamadı.');
            redirect(base_url('admin/credit_management'));
            return;
        }
        
        // Teklif kabul edilmiş ise (2: Kısmen Kabul Edildi, 3: Tamamen Kabul Edildi)
        if ($offer->status == 2 || $offer->status == 3) {
            flash('Hata', 'Bu teklif kabul edilmiş ve aktif kredi haline gelmiştir. Silinemez.');
            redirect(base_url('admin/credit_management'));
            return;
        }
        
        // Teklifi sil
        $this->db->where('id', $id)->delete('credit_offers');
        
        // Log ekle
        addlog('deleteCreditOffer', 'Kredi teklifi silindi: ID: ' . $id);
        
        flash('Başarılı', 'Teklif başarıyla silindi.');
        redirect(base_url('admin/credit_management'));
    }
    
    /**
     * Kredi ödemeleri listesi
     */
    public function payments()
    {
        // Kredi ödemelerini listele
        $data['payments'] = $this->db->select('cp.*, uc.amount as credit_amount, uc.remaining_amount, u.name, u.surname, u.email')
                               ->from('credit_payments cp')
                               ->join('user_credits uc', 'cp.credit_id = uc.id')
                               ->join('user u', 'cp.user_id = u.id')
                               ->order_by('cp.created_at', 'DESC')
                               ->get()->result();
        
        $data['status'] = 'creditPayments';
        $data['properties'] = $this->db->where('id', 1)->get('properties')->row();
        
        $this->adminView('credit/payments', $data);
    }
    
    /**
     * Kullanıcı kredi detayları
     */
    public function user_credits($user_id)
    {
        // Kullanıcı bilgilerini al
        $data['user'] = $this->db->where('id', $user_id)->get('user')->row();
        
        if (!$data['user']) {
            flash('Hata', 'Kullanıcı bulunamadı.');
            redirect(base_url('admin/credit_management'));
            return;
        }
        
        // Kullanıcının kredilerini listele
        $data['credits'] = $this->db->select('uc.*, co.fee_percentage, co.term_days')
                              ->from('user_credits uc')
                              ->join('credit_offers co', 'uc.offer_id = co.id')
                              ->where('uc.user_id', $user_id)
                              ->order_by('uc.created_at', 'DESC')
                              ->get()->result();
        
        // Kullanıcının kredi ödemelerini listele
        $data['payments'] = $this->db->select('cp.*')
                               ->from('credit_payments cp')
                               ->where('cp.user_id', $user_id)
                               ->order_by('cp.created_at', 'DESC')
                               ->get()->result();
        
        $data['status'] = 'creditManagement';
        $data['properties'] = $this->db->where('id', 1)->get('properties')->row();
        
        $this->adminView('credit/user_credits', $data);
    }
    
    /**
     * Kredi ödemesi ekle
     */
    public function add_payment()
    {
        $credit_id = $this->input->post('credit_id');
        $user_id = $this->input->post('user_id');
        $amount = $this->input->post('amount');
        $payment_method = $this->input->post('payment_method');
        $payment_type = $this->input->post('payment_type');
        $is_final_payment = $this->input->post('is_final_payment') ? 1 : 0;
        
        // Kredi bilgilerini al
        $credit = $this->db->where('id', $credit_id)->get('user_credits')->row();
        
        if (!$credit) {
            flash('Hata', 'Kredi bulunamadı.');
            redirect(base_url('admin/credit_management/user_credits/' . $user_id));
            return;
        }
        
        // Ödeme tutarı kalan tutardan büyük olamaz
        if ($amount > $credit->remaining_amount) {
            flash('Hata', 'Ödeme tutarı, kalan borçtan büyük olamaz.');
            redirect(base_url('admin/credit_management/user_credits/' . $user_id));
            return;
        }
        
        // Kullanıcı bilgilerini al
        $user = $this->db->where('id', $user_id)->get('user')->row();
        
        // Eğer bakiyeden düşme seçilmişse, bakiye kontrolü yap
        if ($payment_method == 'balance') {
            if ($user->balance < $amount) {
                flash('Hata', 'Kullanıcının bakiyesi yetersiz.');
                redirect(base_url('admin/credit_management/user_credits/' . $user_id));
                return;
            }
            
            // Kullanıcı bakiyesinden düş
            $this->db->query("UPDATE user SET balance = balance - ? WHERE id = ?", [$amount, $user_id]);
            
            // Bakiye işlemi kaydet
            $transaction_data = [
                'user_id' => $user_id,
                'transaction_type' => 'payment',
                'balance_type' => 'spendable',
                'amount' => -$amount, // Bakiyeden çıkış olduğu için negatif
                'balance_before' => $user->balance,
                'balance_after_transaction' => $user->balance - $amount,
                'description' => 'Cari hesap ödemesi (ID: ' . $credit_id . ')',
                'related_id' => $credit_id,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert('wallet_transactions', $transaction_data);
        } else {
            // Diğer ödeme yöntemleri için wallet_transaction kaydı
            $transaction_data = [
                'user_id' => $user_id,
                'transaction_type' => 'credit_payment',
                'balance_type' => 'spendable',
                'amount' => 0, // Bakiye değişimi olmadığı için 0
                'balance_before' => $user->balance,
                'balance_after_transaction' => $user->balance,
                'description' => 'Cari hesap ödemesi - ' . ucfirst($payment_method) . ' (ID: ' . $credit_id . ')',
                'related_id' => $credit_id,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert('wallet_transactions', $transaction_data);
        }
        
        // Ödeme verisini oluştur
        $payment_data = [
            'credit_id' => $credit_id,
            'user_id' => $user_id,
            'amount' => $amount,
            'payment_method' => $payment_method,
            'status' => 1, // Onaylandı
            'payment_type' => $payment_type,
            'is_final_payment' => $is_final_payment,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Ödemeyi ekle
        $this->db->insert('credit_payments', $payment_data);
        $payment_id = $this->db->insert_id();
        
        // Yeni kalan tutarı hesapla
        $new_remaining_amount = $credit->remaining_amount - $amount;
        
        // Kredi durumunu belirle
        $credit_status = 1; // Aktif
        if ($new_remaining_amount <= 0 || $is_final_payment == 1) {
            $credit_status = 2; // Ödendi
            $new_remaining_amount = 0;
        } else if ($new_remaining_amount < $credit->remaining_amount) {
            $credit_status = 3; // Kısmi Ödendi
        }
        
        // Krediyi güncelle
        $this->db->where('id', $credit_id);
        $this->db->update('user_credits', [
            'remaining_amount' => $new_remaining_amount,
            'status' => $credit_status,
            'payment_count' => $credit->payment_count + 1,
            'last_payment_date' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Kredi işlem logunu kaydet
        $log_data = [
            'credit_id' => $credit_id,
            'user_id' => $user_id,
            'action' => 'payment_add',
            'amount' => $amount,
            'description' => $is_final_payment == 1 ? 'Son cari hesap ödemesi yapıldı' : 'Cari hesap ödemesi yapıldı',
            'admin_id' => $this->session->userdata('info')['id'],
            'created_at' => date('Y-m-d H:i:s'),
            'ip_address' => $this->input->ip_address()
        ];
        
        $this->db->insert('credit_logs', $log_data);
        
        // Bildirim gönder
        $notification_data = [
            'user_id' => $user_id,
            'title' => 'Cari Hesap Ödemeniz Alındı',
            'contents' => $amount . ' TL tutarındaki cari hesap ödemeniz alındı. ' . 
                         ($credit_status == 2 ? 'Cari hesap borcunuz tamamen ödendi.' : 'Kalan ödeme: ' . $new_remaining_amount . ' TL'),
            'link' => 'hesabim/krediler',
            'seen_at' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'isActive' => 'Active',
            'sender' => 'admin'
        ];
        
        $this->db->insert('notifications', $notification_data);
        
        flash('Başarılı', 'Cari hesap ödemesi başarıyla eklendi.');
        redirect(base_url('admin/credit_management/user_credits/' . $user_id));
    }
    
    /**
     * Vadesi geçmiş kredileri kontrol et
     */
    public function check_overdue_credits()
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
                'description' => 'Kredi vadesi geçti. Vade tarihi: ' . $credit->due_date,
                'created_at' => $current_date,
                'ip_address' => $this->input->ip_address()
            ];
            
            $this->db->insert('credit_logs', $log_data);
            
            // Bildirim gönder
            $notification_data = [
                'user_id' => $credit->user_id,
                'title' => 'Kredi Vade Uyarısı',
                'contents' => 'Kredinizin vadesi geçti. Lütfen en kısa sürede ödeme yapınız. Kalan tutar: ' . $credit->remaining_amount . ' TL',
                'link' => 'hesabim/krediler',
                'seen_at' => 1,
                'created_at' => $current_date,
                'isActive' => 'Active',
                'sender' => 'admin'
            ];
            
            $this->db->insert('notifications', $notification_data);
            
            $updated_count++;
        }
        
        addlog('checkOverdueCredits', 'Vadesi geçmiş krediler kontrol edildi. Güncellenen kredi sayısı: ' . $updated_count);
        
        flash('Bilgi', 'Vadesi geçmiş krediler kontrol edildi. Güncellenen kredi sayısı: ' . $updated_count);
        redirect(base_url('admin/credit_management'));
    }
    
    /**
     * Teklif detaylarını JSON olarak döndür
     */
    public function get_offer_details()
    {
        $offer_id = $this->input->post('offer_id');
        
        $offer = $this->db->select('co.*, u.name, u.surname, u.email')
                     ->from('credit_offers co')
                     ->join('user u', 'co.user_id = u.id')
                     ->where('co.id', $offer_id)
                     ->get()->row();
                     
        if (!$offer) {
            echo json_encode(['status' => false, 'message' => 'Teklif bulunamadı']);
            return;
        }
        
        echo json_encode(['status' => true, 'data' => $offer]);
    }
    
    /**
     * Kredi detaylarını JSON olarak döndür
     */
    public function get_credit_details()
    {
        $credit_id = $this->input->post('credit_id');
        
        $credit = $this->db->select('uc.*, u.name, u.surname, u.email, co.fee_percentage, co.term_days')
                      ->from('user_credits uc')
                      ->join('user u', 'uc.user_id = u.id')
                      ->join('credit_offers co', 'uc.offer_id = co.id')
                      ->where('uc.id', $credit_id)
                      ->get()->row();
                      
        if (!$credit) {
            echo json_encode(['status' => false, 'message' => 'Kredi bulunamadı']);
            return;
        }
        
        $payments = $this->db->where('credit_id', $credit_id)
                        ->where('status', 1) // Onaylanmış ödemeler
                        ->order_by('created_at', 'DESC')
                        ->get('credit_payments')->result();
                        
        echo json_encode(['status' => true, 'data' => ['credit' => $credit, 'payments' => $payments]]);
    }
    
    /**
     * Kullanıcı kredi verilerini ve kredi puanını hesapla
     */
    public function get_user_credit_data()
    {
        // CSRF korumasını bu metod için devre dışı bırak
        $this->output->set_header('Access-Control-Allow-Origin: *');
        $this->output->set_header('Access-Control-Allow-Methods: POST');
        $this->output->set_header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
        
        // Admin kontrolünü devre dışı bırak (bu metod için)
        if ($this->input->is_ajax_request()) {
            $user_id = $this->input->post('user_id');
            
            if (!$user_id) {
                echo json_encode(['status' => false, 'message' => 'Kullanıcı ID gereklidir']);
                return;
            }
            
            // Kullanıcı varlığını kontrol et
            $user = $this->db->where('id', $user_id)->get('user')->row();
            if (!$user) {
                echo json_encode(['status' => false, 'message' => 'Kullanıcı bulunamadı']);
                return;
            }
            
            // Bayi bilgilerini al
            $dealer_info = $this->db->select('dt.name as dealer_type')
                            ->from('user_dealers ud')
                            ->join('dealer_types dt', 'ud.dealer_type_id = dt.id')
                            ->where('ud.user_id', $user_id)
                            ->get()->row();
            
            // Toplam kredi sayısı ve tutarı
            $total_credits = $this->db->where('user_id', $user_id)->count_all_results('user_credits');
            $total_credit_amount = $this->db->select_sum('amount')->where('user_id', $user_id)->get('user_credits')->row()->amount ?? 0;
            
            // Aktif krediler
            $active_credits = $this->db->where('user_id', $user_id)->where_in('status', [1, 3])->count_all_results('user_credits');
            
            // Toplam ödeme tutarı
            $total_payments = $this->db->select_sum('amount')
                                ->where('user_id', $user_id)
                                ->where('status', 1)
                                ->get('credit_payments')->row()->amount ?? 0;
            
            // Zamanında yapılan ödeme oranı
            $on_time_payments = 0;
            $late_payments = 0;
            
            // Vadesi geçmiş kredi sayısı
            $overdue_credits = $this->db->where('user_id', $user_id)->where('status', 4)->count_all_results('user_credits');
            
            // Kullanıcının tüm kredi ödemeleri
            $payments = $this->db->select('cp.*, uc.due_date')
                        ->from('credit_payments cp')
                        ->join('user_credits uc', 'cp.credit_id = uc.id')
                        ->where('cp.user_id', $user_id)
                        ->where('cp.status', 1)
                        ->get()->result();
            
            // Zamanında ve geç ödemeleri hesapla
            foreach ($payments as $payment) {
                if (isset($payment->payment_date) && isset($payment->due_date)) {
                    if (strtotime($payment->payment_date) <= strtotime($payment->due_date)) {
                        $on_time_payments++;
                    } else {
                        $late_payments++;
                    }
                }
            }
            
            // Zamanında ödeme oranı
            $total_payment_count = $on_time_payments + $late_payments;
            $on_time_payment_rate = $total_payment_count > 0 ? round(($on_time_payments / $total_payment_count) * 100) : 0;
            
            // Kredi puanı hesapla (0-100 arası)
            $credit_score = 0;
            
            // Hiç kredi kullanmamış kullanıcılar için özel hesaplama
            if ($total_credits == 0) {
                // Yeni kullanıcılara varsayılan iyi bir puan ver
                $credit_score = 75;
            } else {
                // Baz puan - her kullanıcı 30 puanla başlar
                $credit_score += 30;
                
                // Zamanında ödeme puanı - maksimum 40 puan
                $credit_score += min(40, $on_time_payment_rate * 0.4);
                
                // Toplam kredi sayısı puanı - maksimum 10 puan
                $credit_score += min(10, $total_credits * 2);
                
                // Aktif kredi durumu - çok fazla aktif kredi varsa puan kırpılır
                $credit_score -= min(15, max(0, $active_credits - 1) * 5);
                
                // Vadesi geçmiş kredi cezası - her vadesi geçmiş kredi için 10 puan kırpılır
                $credit_score -= min(25, $overdue_credits * 10);
            }
            
            // Son puanı 0-100 arasında tut
            $credit_score = max(0, min(100, round($credit_score)));
            
            $result = [
                'dealer_type' => $dealer_info->dealer_type ?? '-',
                'total_credits' => $total_credits,
                'total_credit_amount' => number_format($total_credit_amount, 2, ',', '.') . ' TL',
                'active_credits' => $active_credits,
                'total_payments' => number_format($total_payments, 2, ',', '.') . ' TL',
                'on_time_payment_rate' => '%' . $on_time_payment_rate,
                'credit_score' => $credit_score,
                'is_new_user' => ($total_credits == 0) ? true : false
            ];
            
            echo json_encode(['status' => true, 'data' => $result]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Geçersiz istek']);
        }
    }
    
    /**
     * Kredi verilebilecek kullanıcıları getir
     * Bu fonksiyon kredi verilebilecek kullanıcı tiplerini ve listesini döndürür
     * İleride eklenecek yeni kullanıcı tipleri için kullanılabilir
     */
    public function get_allowed_credit_users()
    {
        $result = [];
        
        // İzin verilen kullanıcı tipleri
        $allowed_user_types = [
            'dealer' => 'Bayiler'
            // 'seller' => 'Pazar Yeri Satıcıları' (İleride eklenecek)
        ];
        
        $result['user_types'] = $allowed_user_types;
        
        // Bayileri getir
        $result['dealers'] = $this->db->select('ud.*, u.id as user_id, u.name, u.surname, u.email, dt.name as dealer_type_name')
                              ->from('user_dealers ud')
                              ->join('user u', 'ud.user_id = u.id')
                              ->join('dealer_types dt', 'ud.dealer_type_id = dt.id')
                              ->where('ud.active_status', 1)
                              ->get()->result();
        
        // Pazar yeri satıcıları (ileride eklenecek)
        /*
        $result['sellers'] = $this->db->select('ms.*, u.id as user_id, u.name, u.surname, u.email')
                              ->from('marketplace_sellers ms')
                              ->join('user u', 'ms.user_id = u.id')
                              ->where('ms.status', 'active')
                              ->get()->result();
        */
        
        return $result;
    }
    
    /**
     * Kullanıcının kredi almaya uygun olup olmadığını kontrol eder
     * 
     * @param int $user_id Kullanıcı ID
     * @return array ['eligible' => bool, 'user_type' => string, 'message' => string]
     */
    public function check_user_credit_eligibility($user_id)
    {
        $result = [
            'eligible' => false,
            'user_type' => '',
            'message' => 'Bu kullanıcı kredi almaya uygun değil.'
        ];
        
        if (!$user_id) {
            return $result;
        }
        
        // Kullanıcı varlığını kontrol et
        $user = $this->db->where('id', $user_id)->get('user')->row();
        if (!$user) {
            $result['message'] = 'Kullanıcı bulunamadı.';
            return $result;
        }
        
        // Kullanıcı tipini kontrol et
        $allowed_credit_users = $this->get_allowed_credit_users();
        
        // Bayiler içinde ara
        foreach ($allowed_credit_users['dealers'] as $dealer) {
            if ($dealer->user_id == $user_id) {
                $result['eligible'] = true;
                $result['user_type'] = 'dealer';
                $result['message'] = 'Kullanıcı kredi almaya uygun (Bayi).';
                
                // Aktif kredileri kontrol et
                $active_credits = $this->db->where('user_id', $user_id)
                                     ->where_in('status', [1, 3]) // 1: Aktif, 3: Kısmi Ödendi
                                     ->get('user_credits')->result();
                                     
                if (count($active_credits) > 0) {
                    $result['eligible'] = false;
                    $result['message'] = 'Kullanıcının aktif kredisi bulunmaktadır. Yeni kredi teklifi verilemez.';
                    return $result;
                }
                
                return $result;
            }
        }
        
        // İleride seller tipi geldiğinde eklenecek
        /*
        if (isset($allowed_credit_users['sellers'])) {
            foreach ($allowed_credit_users['sellers'] as $seller) {
                if ($seller->user_id == $user_id) {
                    $result['eligible'] = true;
                    $result['user_type'] = 'seller';
                    $result['message'] = 'Kullanıcı kredi almaya uygun (Satıcı).';
                    return $result;
                }
            }
        }
        */
        
        return $result;
    }
    
    /**
     * Kullanıcının aktif kredisini ve bekleyen kredi tekliflerini kontrol eder
     * 
     * @param int $user_id Kullanıcı ID
     * @return array ['active_credits' => array, 'pending_offers' => array, 'has_active_credit' => bool]
     */
    public function check_user_credit_status($user_id) 
    {
        $result = [
            'active_credits' => [],
            'pending_offers' => [],
            'has_active_credit' => false
        ];
        
        if (!$user_id) {
            return $result;
        }
        
        // Aktif kredileri kontrol et 
        $active_credits = $this->db->where('user_id', $user_id)
                               ->where_in('status', [1, 3]) // 1: Aktif, 3: Kısmi Ödendi
                               ->get('user_credits')->result();
        
        $result['active_credits'] = $active_credits;
        $result['has_active_credit'] = (count($active_credits) > 0);
        
        // Bekleyen kredi tekliflerini kontrol et
        $pending_offers = $this->db->where('user_id', $user_id)
                               ->where_in('status', [0, 1]) // 0: Beklemede, 1: Aktif
                               ->get('credit_offers')->result();
        
        $result['pending_offers'] = $pending_offers;
        
        return $result;
    }
    
    /**
     * Kullanıcının kredi durumunu ve tekliflerini JSON olarak döndürür
     */
    public function get_user_credit_status()
    {
        // CSRF korumasını bu metod için devre dışı bırak
        $this->output->set_header('Access-Control-Allow-Origin: *');
        $this->output->set_header('Access-Control-Allow-Methods: POST');
        $this->output->set_header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
        
        if ($this->input->is_ajax_request()) {
            $user_id = $this->input->post('user_id');
            
            if (!$user_id) {
                echo json_encode(['status' => false, 'message' => 'Kullanıcı ID gereklidir']);
                return;
            }
            
            $credit_status = $this->check_user_credit_status($user_id);
            
            echo json_encode([
                'status' => true, 
                'data' => $credit_status,
                'has_active_credit' => $credit_status['has_active_credit'],
                'has_pending_offers' => (count($credit_status['pending_offers']) > 0)
            ]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Geçersiz istek']);
        }
    }
} 