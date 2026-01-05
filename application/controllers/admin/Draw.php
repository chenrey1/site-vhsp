<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Draw extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('M_Draw');
    }

    // Çekiliş listesi
    public function index() {
        $data = [
            'draws' => $this->M_Draw->get_all_draws(true),
            'status' => 'draws',
            'tab' => 'active'
        ];
        $data['draws'] = array_filter($data['draws'], function($d) { return $d->status == 1; });
        $this->load->view('admin/draws', $data);
    }

    // Çekiliş ekle
    public function add() {
        $data['status'] = 'draws';
        $this->load->model('M_Draw');
        $data['products'] = $this->M_Draw->get_active_products();
        if ($this->input->post()) {
            $draw_data = [
                'name' => $this->input->post('name', true),
                'start_time' => $this->input->post('start_time', true),
                'end_time' => $this->input->post('end_time', true),
                'max_participants' => $this->input->post('max_participants', true) ?: null,
                'status' => 1,
                'description' => $this->input->post('description', true)
            ];
            // Görsel yükleme
            if (!empty($_FILES['image']['name'])) {
                $config['upload_path'] = './uploads/draws/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['encrypt_name'] = TRUE;
                if (!is_dir($config['upload_path'])) { mkdir($config['upload_path'], 0777, true); }
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('image')) {
                    $upload_data = $this->upload->data();
                    $draw_data['image'] = 'uploads/draws/' . $upload_data['file_name'];
                }
            }
            $rewards = $this->input->post('rewards');
            $draw_id = $this->M_Draw->create_draw($draw_data);
            // Ödülleri kaydet
            if ($rewards && is_array($rewards)) {
                foreach ($rewards as $reward) {
                    $reward_data = [
                        'draw_id' => $draw_id,
                        'type' => $reward['type'],
                        'winner_count' => isset($reward['winner_count']) ? (int)$reward['winner_count'] : 1
                    ];
                    if ($reward['type'] == 'bakiye') {
                        $reward_data['amount'] = $reward['amount'];
                        $reward_data['product_id'] = null;
                    } else {
                        $reward_data['amount'] = null;
                        $reward_data['product_id'] = $reward['product_id'];
                    }
                    $this->db->insert('draw_rewards', $reward_data);
                }
            }
            redirect('admin/draw/index');
        }
        $this->load->view('admin/draw-add', $data);
    }

    // Çekiliş düzenle
    public function edit($id) {
        $this->load->model('M_Draw');
        $draw = $this->M_Draw->get_draw($id);
        if (!$draw) show_404();
        $data = [
            'draw' => $draw,
            'rewards' => $this->M_Draw->get_rewards($id),
            'status' => 'draws',
            'products' => $this->M_Draw->get_active_products()
        ];
        if ($this->input->post()) {
            $draw_data = [
                'name' => $this->input->post('name', true),
                'start_time' => $this->input->post('start_time', true),
                'end_time' => $this->input->post('end_time', true),
                'max_participants' => $this->input->post('max_participants', true) ?: null,
                'description' => $this->input->post('description', true)
            ];
            $this->M_Draw->update_draw($id, $draw_data);
            redirect('admin/draw/index');
        }
        $this->load->view('admin/draw-edit', $data);
    }

    // Çekiliş sil
    public function delete($id) {
        $this->M_Draw->delete_draw($id);
        redirect('admin/draw/index');
    }

    // Çekilişi bitir (manuel)
    public function finish($id) {
        $this->M_Draw->finish_draw($id);
        redirect('admin/draw/index');
    }

    /**
     * Süresi dolmuş tüm çekilişleri otomatik olarak bitir
     * Bu fonksiyon manuel olarak tarayıcıdan çağrılabilir
     */
    public function finish_expired() {
        $completed = $this->M_Draw->auto_finish_draws();
        
        if (empty($completed)) {
            flash('Bilgi', 'Süresi dolmuş çekiliş bulunamadı.');
            redirect('admin/draw/index');
            return;
        }
        
        $success_count = 0;
        $failed_count = 0;
        
        foreach ($completed as $draw) {
            if ($draw['success']) {
                $success_count++;
            } else {
                $failed_count++;
            }
        }
        
        flash('Başarılı', "Toplam $success_count çekiliş başarıyla tamamlandı" . ($failed_count > 0 ? ", $failed_count çekiliş tamamlanamadı." : "."));
        redirect('admin/draw/index');
    }

    // Çekiliş detay ve katılımcı listesi
    public function detail($id) {
        $this->load->model('M_Draw');
        $draw = $this->M_Draw->get_draw($id);
        if (!$draw) show_404();
        $data['draw'] = $draw;
        $data['participants'] = $this->M_Draw->get_draw_participants_with_user($id);
        $data['rewards'] = $this->M_Draw->get_rewards($id);
        $data['status'] = 'draws';
        $this->load->view('admin/draw-detail', $data);
    }

    // Sonlanmış çekilişler
    public function finished() {
        $data = [
            'draws' => $this->M_Draw->get_all_draws(true),
            'status' => 'draws',
            'tab' => 'finished'
        ];
        $data['draws'] = array_filter($data['draws'], function($d) { return $d->status == 2; });
        $this->load->view('admin/draws', $data);
    }
    
    /**
     * Süresi dolmuş çekilişleri kontrol et ve tamamla
     * Ajax isteği için kullanılır
     */
    public function check_expired() {
        $this->output->set_content_type('application/json');
        
        try {
            $completed = $this->M_Draw->auto_finish_draws();
            
            if (empty($completed)) {
                $this->output->set_output(json_encode([
                    'success' => true,
                    'message' => 'Süresi dolmuş çekiliş bulunamadı.'
                ]));
                return;
            }
            
            $success_count = 0;
            $failed_count = 0;
            
            foreach ($completed as $draw) {
                if ($draw['success']) {
                    $success_count++;
                } else {
                    $failed_count++;
                }
            }
            
            $this->output->set_output(json_encode([
                'success' => true,
                'message' => "Toplam $success_count çekiliş başarıyla tamamlandı" . ($failed_count > 0 ? ", $failed_count çekiliş tamamlanamadı." : "."),
                'data' => $completed
            ]));
        } catch (Exception $e) {
            $this->output->set_output(json_encode([
                'success' => false,
                'message' => 'İşlem sırasında bir hata oluştu: ' . $e->getMessage()
            ]));
        }
    }
    
    /**
     * Ürün teslimatlarını yönetme sayfası
     * Kazanılan ürünlerin teslimat bilgilerini güncelleme
     */
    public function deliveries() {
        // Ürün ödüllü tüm çekilişleri getir
        $this->db->select('draws.*, draw_rewards.type');
        $this->db->from('draws');
        $this->db->join('draw_rewards', 'draws.id = draw_rewards.draw_id', 'inner');
        $this->db->where('draws.status', 2); // Sadece tamamlanmış çekilişler
        $this->db->where('draw_rewards.type', 'ürün');
        $this->db->group_by('draws.id');
        $data['draws'] = $this->db->get()->result();
        
        // Her çekiliş için ürün kazananları getir
        foreach ($data['draws'] as &$draw) {
            $this->db->select('draw_winners.*, draw_participants.user_id, user.name as user_name, user.email, draw_rewards.product_id, product.name as product_name');
            $this->db->from('draw_winners');
            $this->db->join('draw_participants', 'draw_winners.participant_id = draw_participants.id', 'inner');
            $this->db->join('user', 'draw_participants.user_id = user.id', 'inner');
            $this->db->join('draw_rewards', 'draw_winners.reward_id = draw_rewards.id', 'inner');
            $this->db->join('product', 'draw_rewards.product_id = product.id', 'inner');
            $this->db->where('draw_winners.draw_id', $draw->id);
            $this->db->where('draw_rewards.type', 'ürün');
            $draw->winners = $this->db->get()->result();
        }
        
        $data['status'] = 'draws';
        $data['tab'] = 'deliveries';
        $this->load->view('admin/draw-deliveries', $data);
    }
    
    /**
     * Teslimat bilgilerini güncelle
     */
    public function update_delivery() {
        // CSRF doğrulama ve oturum kontrolü
        if (!$this->session->userdata('info')) {
            flash('Hata', 'Bu işlemi gerçekleştirmek için giriş yapın.');
            redirect('admin');
            return;
        }
        
        // Form verilerini al
        $winner_id = $this->input->post('winner_id');
        $delivery_info = $this->input->post('delivery_info');
        $is_delivered = $this->input->post('is_delivered') ? 1 : 0;
        
        if (!$winner_id) {
            flash('Hata', 'Geçersiz kazanan ID.');
            redirect('admin/draw/deliveries');
            return;
        }
        
        // Teslimat bilgilerini güncelle
        $this->db->where('id', $winner_id);
        $this->db->update('draw_winners', [
            'delivery_info' => $delivery_info,
            'is_delivered' => $is_delivered,
            'delivery_date' => $is_delivered ? date('Y-m-d H:i:s') : null
        ]);
        
        flash('Başarılı', 'Teslimat bilgileri güncellendi.');
        redirect('admin/draw/deliveries');
    }

    // Manuel kazanan belirleme
    public function set_winner($draw_id) {
        // Form helper'ı yükle (CSRF token için)
        $this->load->helper('form');
        
        $this->load->model('M_Draw');
        $draw = $this->M_Draw->get_draw($draw_id);
        
        if (!$draw || $draw->status != 1) {
            flash('Hata', 'Çekiliş bulunamadı veya aktif değil.');
            redirect('admin/draw/index');
            return;
        }
        
        $data = [
            'draw' => $draw,
            'participants' => $this->M_Draw->get_draw_participants_with_user($draw_id),
            'rewards' => $this->M_Draw->get_rewards($draw_id),
            'status' => 'draws'
        ];
        
        if ($this->input->post()) {
            $participant_id = $this->input->post('participant_id');
            $reward_id = $this->input->post('reward_id');
            
            if (!$participant_id || !$reward_id) {
                flash('Hata', 'Katılımcı ve ödül seçilmelidir.');
                redirect('admin/draw/set_winner/' . $draw_id);
                return;
            }
            
            $result = $this->M_Draw->set_manual_winner($draw_id, $participant_id, $reward_id);
            
            if ($result) {
                flash('Başarılı', 'Kazanan başarıyla belirlendi.');
            } else {
                flash('Hata', 'Kazanan belirlenirken bir hata oluştu.');
            }
            
            redirect('admin/draw/detail/' . $draw_id);
        }
        
        $this->load->view('admin/draw-set-winner', $data);
    }
} 
