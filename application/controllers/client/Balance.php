<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Balance extends G_Controller {

    // Tekrarlanan verileri saklayacak sınıf değişkenleri
    protected $properties;
    protected $category;
    protected $pages;
    protected $footerBlog;
    protected $footerPage;
    protected $footerProduct;

    public function __construct()
    {
        parent::__construct();
        if (!isset($this->session->userdata('info')['id'])) {
            flash('Ups.', 'Yetkin Olmayan Bir Yere Giriş Yapmaya Çalışıyorsun.');
            redirect(base_url(), 'refresh');
            exit;
        }

        $this->properties = $this->db->where('id', 1)->get('properties')->row();
        $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
        if ($this->properties->isConfirmTc == 1 && $user->tc == "11111111111") {
            flash('Eksik Bilgiler.', 'Lütfen üyeliğindeki eksik bilgileri tamamla.');
            redirect(base_url('tc-dogrulama'), 'refresh');
        }

        // Tekrarlanan verileri yükle
        $this->load->helper("shop");
        $this->load->library('form_validation');
        $this->load->model('M_Balance');
        
        $this->category = getActiveCategories();
        $this->pages = $this->db->get('pages')->result();
        $this->footerBlog = $this->db->limit(3)->order_by('id', 'DESC')->get('blog')->result();
        $this->footerPage = $this->db->limit(6)->order_by('id', 'DESC')->get('pages')->result();
        $this->footerProduct = $this->db->limit(3)->where('isActive', 1)->order_by('id', 'DESC')->get('product')->result();
    }
    
    /**
     * Bakiye sayfasını gösterir
     */
    public function index() {
        // Kullanıcı bilgilerini al
        addlog('balance', 'Sayfa ziyaret edildi: Cüzdanım');
        
        // Kullanıcı bilgilerini al
        $user_id = $this->session->userdata('info')['id'];
        $user = $this->db->where('id', $user_id)->get('user')->row();
        
        // Kullanılabilir bakiye işlemleri
        $spendable_transactions = $this->db->where('user_id', $user_id)
                                     ->where('balance_type', 'spendable')
                                     ->where('status !=', 3) // Status 3 olan kayıtları hariç tut
                                     ->order_by('id', 'DESC')
                                     ->get('wallet_transactions')
                                     ->result();
        
        // Çekilebilir bakiye işlemleri
        $withdrawable_transactions = $this->db->where('user_id', $user_id)
                                        ->where('balance_type', 'withdrawable')
                                        ->where('status !=', 3) // Status 3 olan kayıtları hariç tut
                                        ->order_by('id', 'DESC')
                                        ->get('wallet_transactions')
                                        ->result();
        
        // Tüm işlemler (filtreleme için)
        $all_transactions = $this->db->where('user_id', $user_id)
                                 ->where('status !=', 3) // Status 3 olan kayıtları hariç tut
                                 ->order_by('id', 'DESC')
                                 ->get('wallet_transactions')
                                 ->result();
        
        // Bankalar
        $banks = $this->db->where('isActive', 1)->get('banks')->result();
        
        // Aktif cari hesap kontrolü
        $has_active_credit = false;
        $active_credit = $this->db->select('user_credits.*, credit_offers.fee_percentage, credit_offers.term_days')
                                ->from('user_credits')
                                ->join('credit_offers', 'user_credits.offer_id = credit_offers.id')
                                ->where('user_credits.user_id', $user_id)
                                ->where_in('user_credits.status', [1, 3]) // 1: Aktif, 3: Kısmi Ödendi
                                ->get()->row();
        
        if ($active_credit) {
            $has_active_credit = true;
            
            // Cari hesap ödemeleri
            $credit_payments = $this->db->where('credit_id', $active_credit->id)
                                      ->where('status', 1) // Onaylanmış ödemeler
                                      ->order_by('created_at', 'DESC')
                                      ->get('credit_payments')
                                      ->result();
        } else {
            $credit_payments = [];
        }
        
        // Aktif teklif kontrolü
        $has_credit_offers = false;
        $credit_offers = $this->db->where('user_id', $user_id)
                                 ->where('status', 1) // 1: Aktif
                                 ->where('offer_valid_until >', date('Y-m-d H:i:s'))
                                 ->get('credit_offers')->result();
        
        if (count($credit_offers) > 0) {
            $has_credit_offers = true;
        }
        
        // Kullanıcının geçmiş cari hesapları
        $credit_history = $this->db->select('user_credits.*, credit_offers.fee_percentage, credit_offers.term_days')
                                 ->from('user_credits')
                                 ->join('credit_offers', 'user_credits.offer_id = credit_offers.id')
                                 ->where('user_credits.user_id', $user_id)
                                 ->where_in('user_credits.status', [2, 4]) // 2: Ödendi, 4: Vadesi Geçmiş
                                 ->order_by('user_credits.created_at', 'DESC')
                                 ->get()->result();
        
        $data = [
            'properties' => $this->properties,
            'category' => $this->category,
            'pages' => $this->pages,
            'footerBlog' => $this->footerBlog,
            'footerPage' => $this->footerPage,
            'footerProduct' => $this->footerProduct,
            'banks' => $banks,
            'user' => $user,
            'spendable_transactions' => $spendable_transactions,
            'withdrawable_transactions' => $withdrawable_transactions,
            'all_transactions' => $all_transactions,
            'has_active_credit' => $has_active_credit,
            'active_credit' => $active_credit,
            'has_credit_offers' => $has_credit_offers,
            'credit_offers' => $credit_offers,
            'credit_payments' => $credit_payments,
            'credit_history' => $credit_history,
            'mini' => 1
        ];
        $this->clientView('balance', $data);
    }
    
    /**
     * Bakiye çekme işlemi
     */
    public function withdrawBalance() {
        // Form kontrolü
        $this->form_validation->set_rules('amount', 'Miktar', 'required|numeric|greater_than[0]');
        
        $this->form_validation->set_message('required', 'Lütfen %s alanını doldurunuz.');
        $this->form_validation->set_message('numeric', 'Lütfen %s alanına sadece sayısal değer giriniz.');
        $this->form_validation->set_message('greater_than', 'Lütfen %s alanına 0\'dan büyük bir değer giriniz.');
        
        if ($this->form_validation->run() == FALSE) {
            flash('Hata.', validation_errors());
            redirect(base_url('client/balance?tab=bakiye-cekimi'), 'refresh');
        } else {
            // XSS temizliği
            $amount = filter_var($this->input->post('amount', TRUE), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $user_id = $this->session->userdata('info')['id'];
            
            // Kullanıcı bilgilerini veritabanından al
            $user = $this->db->where('id', $user_id)->get('user')->row();
            
            // Banka bilgileri kontrolü
            if (empty($user->bank_name) || empty($user->bank_owner) || empty($user->bank_iban)) {
                flash('Hata.', 'Lütfen çekim talebi vermeden önce banka bilgilerinizi ekleyin.');
                redirect(base_url('client/balance?tab=bakiye-cekimi'), 'refresh');
                return;
            }
            
            // Bakiye kontrolü
            if ($user->balance2 < $amount) {
                flash('Hata.', 'Çekilebilir bakiyeniz yetersiz.');
                redirect(base_url('client/balance?tab=bakiye-cekimi'), 'refresh');
                return;
            }
            
            $result = $this->M_Balance->withdrawBalance($user_id, $amount, $user->bank_iban, $user->bank_owner);
            
            if ($result['status']) {
                flash('Başarılı!', $result['message']);
            } else {
                flash('Hata.', $result['message']);
            }
            
            redirect(base_url('client/balance?tab=bakiye-gecmisi'), 'refresh');
        }
    }
    
    /**
     * Bakiye transferi (kullanıcılar arası)
     */
    public function transferBalance() {
        // Form kontrolü
        $this->form_validation->set_rules('recipient_email', 'Alıcı E-posta', 'required|valid_email|trim');
        $this->form_validation->set_rules('amount', 'Miktar', 'required|numeric|greater_than[0]');
        
        $this->form_validation->set_message('required', 'Lütfen %s alanını doldurunuz.');
        $this->form_validation->set_message('numeric', 'Lütfen %s alanına sadece sayısal değer giriniz.');
        $this->form_validation->set_message('greater_than', 'Lütfen %s alanına 0\'dan büyük bir değer giriniz.');
        $this->form_validation->set_message('valid_email', 'Lütfen geçerli bir e-posta adresi giriniz.');
        
        if ($this->form_validation->run() == FALSE) {
            flash('Hata.', validation_errors());
            redirect(base_url('client/balance?tab=bakiye-transferi'), 'refresh');
        } else {
            // XSS temizliği
            $recipient_email = trim($this->input->post('recipient_email', TRUE));
            $amount = filter_var($this->input->post('amount', TRUE), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $description = $this->input->post('description', TRUE);
            $user_id = $this->session->userdata('info')['id'];
            
            // Kullanıcı bilgilerini al
            $user = $this->db->where('id', $user_id)->get('user')->row();
            
            // Aktif kredi kontrolü
            $active_credit = $this->db->select('user_credits.*, credit_offers.fee_percentage, credit_offers.term_days')
                                     ->from('user_credits')
                                     ->join('credit_offers', 'user_credits.offer_id = credit_offers.id')
                                     ->where('user_credits.user_id', $user_id)
                                     ->where_in('user_credits.status', [1, 3]) // 1: Aktif, 3: Kısmi Ödendi
                                     ->get()->row();
            
            if ($active_credit) {
                flash('Hata.', 'Aktif bir cari hesap borcunuz bulunduğu için bakiye transferi yapamazsınız. Lütfen önce mevcut cari hesap borcunuzu ödeyin.');
                redirect(base_url('client/balance?tab=bakiye-transferi'), 'refresh');
                return;
            }
            
            // Bakiye kontrolü
            if ($user->balance < $amount) {
                flash('Hata.', 'Kullanılabilir bakiyeniz yetersiz.');
                redirect(base_url('client/balance?tab=bakiye-transferi'), 'refresh');
                return;
            }
            
            // Kendi kendine transfer kontrolü
            $recipient = $this->db->where('email', $recipient_email)->get('user')->row();
            if ($recipient && $recipient->id == $user_id) {
                flash('Hata.', 'Kendinize transfer yapamazsınız.');
                redirect(base_url('client/balance?tab=bakiye-transferi'), 'refresh');
                return;
            }
            
            $result = $this->M_Balance->transferBalance($user_id, $recipient_email, $amount, $description);
            
            if ($result['status']) {
                flash('Başarılı!', $result['message']);
            } else {
                flash('Hata.', $result['message']);
            }
            
            redirect(base_url('client/balance?tab=kredi-gecmisim'), 'refresh');
        }
    }
    
    /**
     * Bakiyeler arası transfer
     */
    public function transferBetweenBalances() {
        // Form kontrolü
        $this->form_validation->set_rules('amount', 'Miktar', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('transfer_direction', 'Transfer Yönü', 'required|in_list[normal_to_withdrawable,withdrawable_to_normal]');
        
        $this->form_validation->set_message('required', 'Lütfen %s alanını doldurunuz.');
        $this->form_validation->set_message('numeric', 'Lütfen %s alanına sadece sayısal değer giriniz.');
        $this->form_validation->set_message('greater_than', 'Lütfen %s alanına 0\'dan büyük bir değer giriniz.');
        $this->form_validation->set_message('in_list', 'Lütfen geçerli bir %s seçiniz.');
        
        if ($this->form_validation->run() == FALSE) {
            flash('Hata.', validation_errors());
            redirect(base_url('client/balance?tab=bakiyeler-arasi'), 'refresh');
        } else {
            // XSS temizliği
            $amount = filter_var($this->input->post('amount', TRUE), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $transfer_direction = $this->input->post('transfer_direction', TRUE);
            $user_id = $this->session->userdata('info')['id'];
            
            // Kullanıcı bilgilerini al
            $user = $this->db->where('id', $user_id)->get('user')->row();
            
            // Bakiye kontrolü
            if ($transfer_direction == 'normal_to_withdrawable' && $user->balance < $amount) {
                flash('Hata.', 'Kullanılabilir bakiyeniz yetersiz.');
                redirect(base_url('client/balance?tab=bakiyeler-arasi'), 'refresh');
                return;
            } elseif ($transfer_direction == 'withdrawable_to_normal' && $user->balance2 < $amount) {
                flash('Hata.', 'Çekilebilir bakiyeniz yetersiz.');
                redirect(base_url('client/balance?tab=bakiyeler-arasi'), 'refresh');
                return;
            }
            
            // Aktif kredi kontrolü (normal_to_withdrawable yönünde transfer için)
            if ($transfer_direction == 'normal_to_withdrawable') {
                // Aktif kredi var mı kontrol et
                $active_credit = $this->db->select('user_credits.*, credit_offers.fee_percentage, credit_offers.term_days')
                                         ->from('user_credits')
                                         ->join('credit_offers', 'user_credits.offer_id = credit_offers.id')
                                         ->where('user_credits.user_id', $user_id)
                                         ->where_in('user_credits.status', [1, 3]) // 1: Aktif, 3: Kısmi Ödendi
                                         ->get()->row();
                
                if ($active_credit) {
                    flash('Hata.', 'Aktif bir cari hesap borcunuz bulunduğu için harcanabilir bakiyeden çekilebilir bakiyeye transfer yapamazsınız. Lütfen önce mevcut cari hesap borcunuzu ödeyin.');
                    redirect(base_url('client/balance?tab=bakiyeler-arasi'), 'refresh');
                    return;
                }
            }
            
            $result = $this->M_Balance->transferBetweenBalances($user_id, $amount, $transfer_direction);
            
            if ($result['status']) {
                flash('Başarılı!', $result['message']);
            } else {
                flash('Hata.', $result['message']);
            }
            
            redirect(base_url('client/balance?tab=bakiye-gecmisi'), 'refresh');
        }
    }
    
    /**
     * Kredi teklifi kabul etme işlemi
     */
    public function acceptCreditOffer() {
        // Form kontrolü
        $this->form_validation->set_rules('offer_id', 'Teklif ID', 'required|numeric');
        $this->form_validation->set_rules('amount', 'Miktar', 'required|numeric|greater_than[0]');
        
        if ($this->form_validation->run() == FALSE) {
            flash('Hata.', validation_errors());
            redirect(base_url('client/balance?tab=kredi-gecmisim'), 'refresh');
        } else {
            $offer_id = $this->input->post('offer_id');
            $amount = $this->input->post('amount');
            $user_id = $this->session->userdata('info')['id'];
            
            $result = $this->M_Balance->acceptCreditOffer($user_id, $offer_id, $amount);
            
            if ($result['status']) {
                flash('Başarılı!', $result['message']);
            } else {
                flash('Hata.', $result['message']);
            }
            
            redirect(base_url('client/balance?tab=kredi-gecmisim'), 'refresh');
        }
    }
    
    /**
     * Kredi teklifi reddetme işlemi
     */
    public function rejectCreditOffer() {
        // Form kontrolü
        $this->form_validation->set_rules('offer_id', 'Teklif ID', 'required|numeric');
        
        if ($this->form_validation->run() == FALSE) {
            flash('Hata.', validation_errors());
            redirect(base_url('client/balance?tab=kredi-gecmisim'), 'refresh');
        } else {
            $offer_id = $this->input->post('offer_id');
            $user_id = $this->session->userdata('info')['id'];
            
            $result = $this->M_Balance->rejectCreditOffer($user_id, $offer_id);
            
            if ($result['status']) {
                flash('Başarılı!', $result['message']);
            } else {
                flash('Hata.', $result['message']);
            }
            
            redirect(base_url('client/balance?tab=kredi-gecmisim'), 'refresh');
        }
    }
    
    /**
     * Kredi ödeme işlemi
     */
    public function payCreditDebt() {
        // Form kontrolü
        $this->form_validation->set_rules('credit_id', 'Cari Hesap ID', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('amount', 'Miktar', 'required|numeric|greater_than[0]');

        //form validation kontrolü için türkçe açıklamalar  
        $this->form_validation->set_message('required', 'Lütfen %s alanını doldurunuz.');
        $this->form_validation->set_message('numeric', 'Lütfen %s alanına sadece sayısal değer giriniz.');
        $this->form_validation->set_message('greater_than', 'Lütfen %s alanına 0\'dan büyük bir değer giriniz.');
        
        if ($this->form_validation->run() == FALSE) {
            flash('Hata.', validation_errors());
            redirect(base_url('client/balance?tab=kredi-gecmisim'), 'refresh');
        } else {
            // XSS temizliği ve veri güvenliği
            $credit_id = (int)$this->input->post('credit_id', TRUE);
            $amount = filter_var($this->input->post('amount', TRUE), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $user_id = $this->session->userdata('info')['id'];
            
            // Kredi sahibi kontrolü
            $credit = $this->db->where('id', $credit_id)->where('user_id', $user_id)->get('user_credits')->row();
            if (!$credit) {
                flash('Hata.', 'Geçersiz Cari Hesap ID\'si veya bu cari hesaba erişim yetkiniz yok.');
                redirect(base_url('client/balance?tab=kredi-gecmisim'), 'refresh');
                return;
            }
            
            // Kullanıcı bilgilerini al
            $user = $this->db->where('id', $user_id)->get('user')->row();
            
            // Bakiye kontrolü
            if ($user->balance < $amount) {
                flash('Hata.', 'Kullanılabilir bakiyeniz yetersiz.');
                redirect(base_url('client/balance?tab=kredi-gecmisim'), 'refresh');
                return;
            }
            
            // Kredi durumu kontrolü
            if ($credit->status != 1 && $credit->status != 3 && $credit->status != 4) {
                flash('Hata.', 'Bu cari hesap borcu için ödeme yapamazsınız.');
                redirect(base_url('client/balance?tab=kredi-gecmisim'), 'refresh');
                return;
            }
            
            // Kalan borç kontrolü
            if ($amount > $credit->remaining_amount) {
                flash('Hata.', 'Ödeme tutarı kalan borç tutarından büyük olamaz.');
                redirect(base_url('client/balance?tab=kredi-gecmisim'), 'refresh');
                return;
            }
            
            $result = $this->M_Balance->payCreditDebt($user_id, $credit_id, $amount);
            
            if ($result['status']) {
                flash('Başarılı!', $result['message']);
            } else {
                flash('Hata.', $result['message']);
            }
            
            redirect(base_url('client/balance?tab=kredi-gecmisim'), 'refresh');
        }
    }
    
    /**
     * Banka Havalesi Bildirimi
     */
    public function addTransfer() {
        // Form kontrolü
        $this->form_validation->set_rules('bank', 'Banka', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('name', 'Gönderen Adı', 'required|trim');
        $this->form_validation->set_rules('date', 'İşlem Tarihi', 'required|trim');
        $this->form_validation->set_rules('price', 'Yatırılan Tutar', 'required|numeric|greater_than[0]');
        
        $this->form_validation->set_message('required', 'Lütfen %s alanını doldurunuz.');
        $this->form_validation->set_message('numeric', 'Lütfen %s alanına sadece sayısal değer giriniz.');
        $this->form_validation->set_message('greater_than', 'Lütfen %s alanına 0\'dan büyük bir değer giriniz.');
        
        if ($this->form_validation->run() == FALSE) {
            flash('Hata.', validation_errors());
            redirect(base_url('client/balance?tab=bakiye-ekle'), 'refresh');
        } else {
            // XSS temizliği
            $bank_id = (int)$this->input->post('bank', TRUE);
            $name = $this->input->post('name', TRUE);
            $date = $this->input->post('date', TRUE);
            $price = filter_var($this->input->post('price', TRUE), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $user_id = $this->session->userdata('info')['id'];
            
            // Banka varlığını kontrol et
            $bank = $this->db->where('id', $bank_id)->where('isActive', 1)->get('banks')->row();
            if (!$bank) {
                flash('Hata.', 'Geçersiz banka seçimi.');
                redirect(base_url('client/balance?tab=bakiye-ekle'), 'refresh');
                return;
            }
            
            // Tutar kontrolü
            if ($price <= 0) {
                flash('Hata.', 'Lütfen geçerli bir tutar giriniz.');
                redirect(base_url('client/balance?tab=bakiye-ekle'), 'refresh');
                return;
            }
            
            $result = $this->M_Balance->addBankTransfer($user_id, $bank_id, $name, $date, $price);
            
            if ($result['status']) {
                flash('Başarılı!', $result['message']);
            } else {
                flash('Hata.', $result['message']);
            }
            
            redirect(base_url('client/balance?tab=bakiye-gecmisi'), 'refresh');
        }
    }
    
    /**
     * Banka bilgilerini güncelleme
     */
    public function changeBank() {
        // Form kontrolü
        $this->form_validation->set_rules('bank_name', 'Banka', 'required|trim');
        $this->form_validation->set_rules('bank_owner', 'Hesap Sahibi', 'required|trim');
        $this->form_validation->set_rules('bank_iban', 'IBAN', 'required|trim|min_length[24]|max_length[34]');
        
        $this->form_validation->set_message('required', 'Lütfen %s alanını doldurunuz.');
        $this->form_validation->set_message('min_length', '%s en az %s karakter olmalıdır.');
        $this->form_validation->set_message('max_length', '%s en fazla %s karakter olmalıdır.');
        
        if ($this->form_validation->run() == FALSE) {
            flash('Hata.', validation_errors());
            redirect(base_url('client/balance?tab=bakiye-cekimi'), 'refresh');
        } else {
            // XSS temizliği
            $bank_name = $this->input->post('bank_name', TRUE);
            $bank_owner = $this->input->post('bank_owner', TRUE);
            $bank_iban = preg_replace('/\s+/', '', $this->input->post('bank_iban', TRUE)); // Boşlukları temizle
            $user_id = $this->session->userdata('info')['id'];
            
            // IBAN doğrulama
            if (!preg_match('/^[A-Z]{2}[0-9]{2}[A-Z0-9]{4}[0-9]{7}([A-Z0-9]?){0,16}$/', $bank_iban)) {
                flash('Hata.', 'Lütfen geçerli bir IBAN numarası giriniz.');
                redirect(base_url('client/balance?tab=bakiye-cekimi'), 'refresh');
                return;
            }
            
            // Kullanıcı banka bilgilerini güncelle
            $this->db->where('id', $user_id);
            $this->db->update('user', [
                'bank_name' => $bank_name,
                'bank_owner' => $bank_owner,
                'bank_iban' => $bank_iban
            ]);
            
            flash('Başarılı!', 'Banka bilgileriniz başarıyla güncellendi.');
            redirect(base_url('client/balance?tab=bakiye-cekimi'), 'refresh');
        }
    }
}
