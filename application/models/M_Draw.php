<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class M_Draw extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
    // Ek log sistemi için yardımcı sınıfı yükle
    $this->load->helper('log');

    // Veritabanı tablolarını kontrol et ve oluştur
    $this->check_tables();
  }

  /**
   * Gerekli veritabanı tablolarının varlığını kontrol eder
   * Tablo yoksa otomatik olarak oluşturur
   */
  private function check_tables()
  {

  }

  /**
   * Çekiliş oluştur (hem ürün hem bakiye destekli)
   * 
   * @param array $data Çekiliş verileri
   * @return int Oluşturulan çekiliş ID'si
   */
  public function create_draw($data)
  {
    $this->db->trans_begin();

    try {
      // Ödül tipini belirle - varsayılan olarak hem bakiye hem ürün olabilir
      if (!isset($data['type'])) {
        $data['type'] = 'mixed';
      }

      $this->db->insert('draws', $data);
      $draw_id = $this->db->insert_id();

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        log_message('error', 'Çekiliş oluşturulamadı: ' . $this->db->error()['message']);
        return false;
      }

      $this->db->trans_commit();
      log_message('info', 'Yeni çekiliş oluşturuldu: #' . $draw_id);
      return $draw_id;
    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Çekiliş oluşturma hatası: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Çekiliş güncelle
   * 
   * @param int $id Çekiliş ID
   * @param array $data Güncellenecek veriler
   * @return bool İşlem sonucu
   */
  public function update_draw($id, $data)
  {
    $this->db->trans_begin();

    try {
      $this->db->where('id', $id)->update('draws', $data);

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        log_message('error', 'Çekiliş güncellenemedi: #' . $id . ' - ' . $this->db->error()['message']);
        return false;
      }

      $this->db->trans_commit();
      log_message('info', 'Çekiliş güncellendi: #' . $id);
      return true;
    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Çekiliş güncelleme hatası: #' . $id . ' - ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Çekiliş sil
   * 
   * @param int $id Çekiliş ID
   * @return bool İşlem sonucu
   */
  public function delete_draw($id)
  {
    $this->db->trans_begin();

    try {
      // İlişkili kayıtları sil - Foreign Key CASCADE yapısı sayesinde otomatik silinecek
      // Ancak güvenlik için manuel silme işlemi de yapıyoruz
      $this->db->where('draw_id', $id)->delete('draw_participants');
      $this->db->where('draw_id', $id)->delete('draw_rewards');
      $this->db->where('draw_id', $id)->delete('draw_winners');
      $this->db->where('id', $id)->delete('draws');

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        log_message('error', 'Çekiliş silinemedi: #' . $id . ' - ' . $this->db->error()['message']);
        return false;
      }

      $this->db->trans_commit();
      log_message('info', 'Çekiliş silindi: #' . $id);
      return true;
    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Çekiliş silme hatası: #' . $id . ' - ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Tüm çekilişleri getir (admin: tümü, kullanıcı: sadece aktifler)
   * 
   * @param bool $admin Admin mi?
   * @return array Çekilişler
   */
  public function get_all_draws($admin = false)
  {
    if (!$admin) {
      $this->db->where('status', 1);
    }
    return $this->db->get('draws')->result();
  }

  /**
   * Tek çekilişi getir
   * 
   * @param int $id Çekiliş ID
   * @return object Çekiliş
   */
  public function get_draw($id)
  {
    return $this->db->where('id', $id)->get('draws')->row();
  }

  /**
   * Katılımcı ekle
   * 
   * @param int $draw_id Çekiliş ID
   * @param int $user_id Kullanıcı ID
   * @return int|bool Eklenen kaydın ID'si veya false
   */
  public function add_participant($draw_id, $user_id)
  {
    $this->db->trans_begin();

    try {
      // Zaten katılmış mı kontrol et
      $exists = $this->is_user_joined($draw_id, $user_id);
      if ($exists) {
        log_message('info', 'Kullanıcı zaten çekilişe katılmış: #' . $user_id . ' - Çekiliş #' . $draw_id);
        return false;
      }

      $this->db->insert('draw_participants', [
        'draw_id' => $draw_id,
        'user_id' => $user_id,
        'created_at' => date('Y-m-d H:i:s')
      ]);

      $insert_id = $this->db->insert_id();

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        log_message('error', 'Çekiliş katılımcısı eklenemedi: Kullanıcı #' . $user_id . ' - Çekiliş #' . $draw_id);
        return false;
      }

      $this->db->trans_commit();
      log_message('info', 'Çekiliş katılımcısı eklendi: Kullanıcı #' . $user_id . ' - Çekiliş #' . $draw_id);
      return $insert_id;
    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Çekiliş katılımcısı ekleme hatası: ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Katılımcı sayısı
   * 
   * @param int $draw_id Çekiliş ID
   * @return int Katılımcı sayısı
   */
  public function get_participant_count($draw_id)
  {
    return $this->db->where('draw_id', $draw_id)->count_all_results('draw_participants');
  }

  /**
   * Katılımcı listesi
   * 
   * @param int $draw_id Çekiliş ID
   * @param int $limit Limit
   * @param int $offset Offset
   * @return array Katılımcılar
   */
  public function get_participants($draw_id, $limit = null, $offset = 0)
  {
    $this->db->where('draw_id', $draw_id);

    if ($limit !== null) {
      $this->db->limit($limit, $offset);
    }

    return $this->db->get('draw_participants')->result();
  }

  /**
   * Çekiliş ödülleri
   * 
   * @param int $draw_id Çekiliş ID
   * @param string $type Ödül tipi (boş bırakılırsa tümü)
   * @return array Ödüller
   */
  public function get_rewards($draw_id, $type = null)
  {
    $this->db->select('draw_rewards.*, product.name as product_name, product.img as product_img, product.slug as product_slug');
    $this->db->from('draw_rewards');
    $this->db->join('product', 'product.id = draw_rewards.product_id', 'left');
    $this->db->where('draw_rewards.draw_id', $draw_id);

    if ($type !== null) {
      $this->db->where('draw_rewards.type', $type);
    }

    return $this->db->get()->result();
  }

  /**
   * Çekilişi bitir ve kazananları seç + ödülleri otomatik teslim et
   * 
   * @param int $draw_id Çekiliş ID
   * @return bool İşlem sonucu
   */
  public function finish_draw($draw_id)
  {
    $this->db->trans_begin();

    try {
      $draw = $this->get_draw($draw_id);
      if (!$draw || $draw->status != 1) {
        log_message('error', 'Çekiliş bulunamadı veya aktif değil: #' . $draw_id);
        return false;
      }

      // Katılımcıları toplu olarak işlemek için sayfalama kullanıyoruz
      $participant_count = $this->get_participant_count($draw_id);
      if ($participant_count == 0) {
        log_message('info', 'Çekilişte katılımcı yok: #' . $draw_id);
        $this->update_draw($draw_id, ['status' => 2, 'ended_at' => date('Y-m-d H:i:s')]);
        return true;
      }

      $rewards = $this->get_rewards($draw_id);
      if (count($rewards) == 0) {
        log_message('info', 'Çekilişte ödül yok: #' . $draw_id);
        $this->update_draw($draw_id, ['status' => 2, 'ended_at' => date('Y-m-d H:i:s')]);
        return true;
      }

      // Manuel belirlenen kazananları al
      $manual_winners = $this->db->select('participant_id, reward_id')
        ->where('draw_id', $draw_id)
        ->get('draw_winners')
        ->result();

      $used_participants = array_map(function ($w) {
        return $w->participant_id; }, $manual_winners);
      $used_rewards = array_map(function ($w) {
        return $w->reward_id; }, $manual_winners);

      // Katılımcıları sayfalama ile getir
      $page_size = 500; // Her seferinde 500 katılımcı işlenecek
      $total_pages = ceil($participant_count / $page_size);

      for ($page = 0; $page < $total_pages; $page++) {
        $offset = $page * $page_size;
        $participants = $this->get_participants($draw_id, $page_size, $offset);
        $participant_ids = array_map(function ($p) {
          return $p->id; }, $participants);

        // Her ödül için kazanan seç
        foreach ($rewards as $reward) {
          // Manuel belirlenen ödülleri atla
          if (in_array($reward->id, $used_rewards))
            continue;

          // Kullanılmamış katılımcılar
          $available = array_diff($participant_ids, $used_participants);
          if (count($available) == 0)
            break;

          $winner_count = isset($reward->winner_count) ? (int) $reward->winner_count : 1;
          $winner_count = min($winner_count, count($available));
          if ($winner_count < 1)
            continue;

          // Daha güvenli rastgele seçim yöntemi kullan
          $selected = $this->secure_random_sample($available, $winner_count);

          foreach ($selected as $winner_id) {
            // Kazanan bilgilerini kaydet
            $this->db->insert('draw_winners', [
              'draw_id' => $draw_id,
              'participant_id' => $winner_id,
              'reward_id' => $reward->id,
              'created_at' => date('Y-m-d H:i:s')
            ]);

            // Kazanana ödülü teslim et (bakiye ise)
            $participant = $this->db->where('id', $winner_id)->get('draw_participants')->row();
            if ($reward->type == 'bakiye' && isset($reward->amount) && $reward->amount > 0) {
              // Bakiye güncellemesi için güvenli SQL kullan
              $this->db->where('id', $participant->user_id)
                ->set('balance', 'balance + ' . $this->db->escape($reward->amount), false)
                ->update('user');

              // Bakiye log tablosuna kayıt eklemeyi dene
              try {
                $log_data = [
                  'user_id' => $participant->user_id,
                  'amount' => $reward->amount,
                  'type' => 'çekiliş-kazanç',
                  'description' => 'Çekiliş #' . $draw_id . ' ödülü',
                  'date' => date('Y-m-d H:i:s')
                ];

              } catch (Exception $e) {
                // Hata oluşursa log mesajı 
                log_message('error', 'Bakiye log kaydı eklenirken hata: ' . $e->getMessage());
              }

              // Ödülü teslim edildi olarak işaretle
              $this->db->where('participant_id', $winner_id)
                ->where('reward_id', $reward->id)
                ->update('draw_winners', [
                  'is_delivered' => 1,
                  'delivery_date' => date('Y-m-d H:i:s')
                ]);
            }

            // Kullanılmış katılımcı olarak işaretle
            $used_participants[] = $winner_id;
          }
        }
      }

      // Çekilişi bitir
      $this->update_draw($draw_id, ['status' => 2, 'ended_at' => date('Y-m-d H:i:s')]);

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        log_message('error', 'Çekiliş bitirilemedi: #' . $draw_id . ' - ' . $this->db->error()['message']);
        return false;
      }

      $this->db->trans_commit();
      log_message('info', 'Çekiliş başarıyla tamamlandı: #' . $draw_id);
      return true;
    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Çekiliş tamamlama hatası: #' . $draw_id . ' - ' . $e->getMessage());
      return false;
    }
  }

  /**
   * Güvenli rastgele örnekleme (Güvenlik için)
   * Normal array_rand yerine daha güvenli bir metot.
   * 
   * @param array $array Örneklenecek dizi
   * @param int $count Kaç adet seçileceği
   * @return array Seçilen elemanlar
   */
  private function secure_random_sample($array, $count)
  {
    $result = [];
    $array_copy = $array;

    // random_int PHP 7+ için güvenli rastgele sayı üreteci
    for ($i = 0; $i < $count && count($array_copy) > 0; $i++) {
      try {
        // PHP 7+ için random_int kullan, yoksa mt_rand kullan
        if (function_exists('random_int')) {
          $index = random_int(0, count($array_copy) - 1);
        } else {
          $index = mt_rand(0, count($array_copy) - 1);
        }

        $keys = array_keys($array_copy);
        $selected_key = $keys[$index];
        $result[] = $array_copy[$selected_key];
        unset($array_copy[$selected_key]);
        $array_copy = array_values($array_copy); // Diziyi yeniden indeksle
      } catch (Exception $e) {
        log_message('error', 'Rastgele sayı üretme hatası: ' . $e->getMessage());
        // Hata durumunda basitçe mt_rand kullan
        $index = mt_rand(0, count($array_copy) - 1);
        $keys = array_keys($array_copy);
        $selected_key = $keys[$index];
        $result[] = $array_copy[$selected_key];
        unset($array_copy[$selected_key]);
        $array_copy = array_values($array_copy);
      }
    }

    return $result;
  }

  /**
   * Süresi dolan çekilişler
   * 
   * @return array Süresi dolan çekilişler
   */
  public function get_expired_draws()
  {
    $now = date('Y-m-d H:i:s');
    return $this->db->where('status', 1)
      ->where('end_time <=', $now)
      ->get('draws')
      ->result();
  }

  /**
   * Kullanıcının kazandığı ödüller
   * 
   * @param int $user_id Kullanıcı ID
   * @param string $type Ödül tipi (boş bırakılırsa tümü)
   * @return array Ödüller
   */
  public function get_user_rewards($user_id, $type = null)
  {
    $this->db->select('draw_rewards.*, draws.name as draw_name, draws.type as draw_type, draw_winners.is_delivered, draw_winners.delivery_date, draw_winners.delivery_info, draw_winners.created_at, product.name as product_name, product.img, product.slug as product_slug');
    $this->db->from('draw_winners');
    $this->db->join('draw_participants', 'draw_participants.id = draw_winners.participant_id');
    $this->db->join('draw_rewards', 'draw_rewards.id = draw_winners.reward_id');
    $this->db->join('draws', 'draws.id = draw_winners.draw_id');
    $this->db->join('product', 'product.id = draw_rewards.product_id', 'left');
    $this->db->where('draw_participants.user_id', $user_id);

    if ($type !== null) {
      $this->db->where('draw_rewards.type', $type);
    }

    $rewards = $this->db->get()->result();

    // Ürün bilgilerini düzelt
    foreach ($rewards as $reward) {
      // reward_type değişkenini ekle, görünüm dosyasında bunu kullanıyor
      $reward->reward_type = $reward->type;

      if ($reward->type == 'urun' && isset($reward->product_id) && empty($reward->product_name)) {
        $reward->product_name = 'Ürün #' . $reward->product_id;
        if (empty($reward->img)) {
          $reward->img = 'default.png';
        }
        if (empty($reward->product_slug)) {
          $reward->product_slug = 'urun/' . $reward->product_id;
        }
      }
    }

    return $rewards;
  }

  /**
   * Kullanıcı çekilişe katıldı mı?
   * 
   * @param int $draw_id Çekiliş ID
   * @param int $user_id Kullanıcı ID
   * @return bool Katıldı mı?
   */
  public function is_user_joined($draw_id, $user_id)
  {
    return $this->db->where('draw_id', $draw_id)
      ->where('user_id', $user_id)
      ->count_all_results('draw_participants') > 0;
  }

  /**
   * Katılım işlemi (kontrollü)
   * 
   * @param int $draw_id Çekiliş ID
   * @param int $user_id Kullanıcı ID
   * @return array İşlem sonucu
   */
  public function join_draw($draw_id, $user_id)
  {
    $this->db->trans_begin();

    try {
      // Girdi doğrulama
      if (!is_numeric($draw_id) || !is_numeric($user_id)) {
        return ['success' => false, 'message' => 'Geçersiz parametreler.'];
      }

      $draw = $this->get_draw($draw_id);
      if (!$draw) {
        return ['success' => false, 'message' => 'Çekiliş bulunamadı.'];
      }

      if ($draw->status != 1) {
        return ['success' => false, 'message' => 'Çekiliş aktif değil.'];
      }

      // Şu anki tarih çekiliş tarih aralığında mı?
      $now = date('Y-m-d H:i:s');
      if ($now < $draw->start_time) {
        return ['success' => false, 'message' => 'Çekiliş henüz başlamadı.'];
      }

      if ($now > $draw->end_time) {
        return ['success' => false, 'message' => 'Çekiliş sona erdi.'];
      }

      // Zaten katılmış mı?
      if ($this->is_user_joined($draw_id, $user_id)) {
        return ['success' => false, 'message' => 'Zaten bu çekilişe katıldınız.'];
      }

      // Maksimum katılımcı kontrolü
      $participant_count = $this->get_participant_count($draw_id);
      if (isset($draw->max_participants) && $draw->max_participants > 0 && $participant_count >= $draw->max_participants) {
        return ['success' => false, 'message' => 'Katılımcı limiti doldu.'];
      }

      // Katılımcı ekle
      $result = $this->add_participant($draw_id, $user_id);
      if (!$result) {
        $this->db->trans_rollback();
        log_message('error', 'Çekilişe katılım başarısız: Kullanıcı #' . $user_id . ' - Çekiliş #' . $draw_id);
        return ['success' => false, 'message' => 'Çekilişe katılırken bir hata oluştu.'];
      }

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        log_message('error', 'Çekilişe katılım başarısız (transaction): Kullanıcı #' . $user_id . ' - Çekiliş #' . $draw_id);
        return ['success' => false, 'message' => 'Çekilişe katılırken bir hata oluştu.'];
      }

      $this->db->trans_commit();
      log_message('info', 'Çekilişe katılım başarılı: Kullanıcı #' . $user_id . ' - Çekiliş #' . $draw_id);
      return ['success' => true, 'message' => 'Çekilişe başarıyla katıldınız!'];
    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Çekilişe katılım hatası: ' . $e->getMessage());
      return ['success' => false, 'message' => 'Çekilişe katılırken bir hata oluştu.'];
    }
  }

  /**
   * Çekiliş katılımcılarını ve kullanıcı bilgilerini getir
   * 
   * @param int $draw_id Çekiliş ID
   * @return array Katılımcılar
   */
  public function get_draw_participants_with_user($draw_id)
  {
    $this->db->select('draw_participants.*, user.name, user.email');
    $this->db->from('draw_participants');
    $this->db->join('user', 'user.id = draw_participants.user_id');
    $this->db->where('draw_participants.draw_id', $draw_id);
    $this->db->order_by('draw_participants.created_at', 'DESC');
    return $this->db->get()->result();
  }

  /**
   * Katılımcıların ad ve soyadını (anonim) döndür
   * 
   * @param int $draw_id Çekiliş ID
   * @return array Anonim isimler
   */
  public function get_draw_participant_names($draw_id)
  {
    $this->db->select('user.name');
    $this->db->from('draw_participants');
    $this->db->join('user', 'user.id = draw_participants.user_id');
    $this->db->where('draw_participants.draw_id', $draw_id);
    $names = $this->db->get()->result();

    // Sansürle
    $masked = [];
    foreach ($names as $n) {
      $parts = explode(' ', $n->name);
      $first = isset($parts[0]) ? mb_substr($parts[0], 0, 1, 'UTF-8') . str_repeat('*', max(0, mb_strlen($parts[0], 'UTF-8') - 1)) : '';
      $last = isset($parts[1]) ? mb_substr($parts[1], 0, 1, 'UTF-8') . str_repeat('*', max(0, mb_strlen($parts[1], 'UTF-8') - 1)) : '';
      $masked[] = trim($first . ' ' . $last);
    }

    return $masked;
  }

  /**
   * Kullanıcının katıldığı tüm çekilişler (kazansın veya kazanmasın)
   * 
   * @param int $user_id Kullanıcı ID
   * @return array Çekilişler
   */
  public function get_user_joined_draws($user_id)
  {
    $this->db->select('draws.*, draw_participants.created_at as joined_at');
    $this->db->from('draw_participants');
    $this->db->join('draws', 'draws.id = draw_participants.draw_id');
    $this->db->where('draw_participants.user_id', $user_id);
    $this->db->order_by('draw_participants.created_at', 'DESC');
    return $this->db->get()->result();
  }

  /**
   * Aktif ürünleri getir (çekiliş ödülü için)
   * 
   * @return array Aktif ürünler
   */
  public function get_active_products()
  {
    return $this->db->where('isActive', 1)->get('product')->result();
  }

  /**
   * Çekilişleri otomatik tamamla (cron için)
   * 
   * @return array Tamamlanan çekilişlerin listesi
   */
  public function auto_finish_draws()
  {
    $now = date('Y-m-d H:i:s');

    // Süresi dolmuş ve aktif olan çekilişleri bul
    $expired_draws = $this->db->where('status', 1)
      ->where('end_time <', $now)
      ->get('draws')->result();

    if (empty($expired_draws)) {
      return [];
    }

    $result = [];

    foreach ($expired_draws as $draw) {
      try {
        // Çekilişi tamamla
        $this->finish_draw($draw->id);

        $result[] = [
          'id' => $draw->id,
          'name' => $draw->name,
          'success' => true,
          'message' => 'Çekiliş başarıyla tamamlandı.'
        ];

        log_message('info', 'Çekiliş otomatik tamamlandı. ID: ' . $draw->id);
      } catch (Exception $e) {
        $result[] = [
          'id' => $draw->id,
          'name' => $draw->name,
          'success' => false,
          'message' => 'Hata: ' . $e->getMessage()
        ];

        log_message('error', 'Çekiliş tamamlanırken hata: ' . $e->getMessage() . ' - ID: ' . $draw->id);
      }
    }

    return $result;
  }

  /**
   * Çekiliş sonuçlarını getir (kazananlar)
   * 
   * @param int $draw_id Çekiliş ID
   * @return array Sonuçlar
   */
  public function get_draw_results($draw_id)
  {
    $this->db->select('draw_winners.*, draw_participants.user_id, draw_rewards.type as reward_type, draw_rewards.amount, draw_rewards.product_id, product.name as product_name, product.img as product_img');
    $this->db->from('draw_winners');
    $this->db->join('draw_participants', 'draw_winners.participant_id = draw_participants.id', 'inner');
    $this->db->join('draw_rewards', 'draw_winners.reward_id = draw_rewards.id', 'inner');
    $this->db->join('product', 'draw_rewards.product_id = product.id', 'left');
    $this->db->where('draw_winners.draw_id', $draw_id);
    return $this->db->get()->result();
  }

  /**
   * Manuel olarak kazanan belirleme
   * 
   * @param int $draw_id Çekiliş ID
   * @param int $participant_id Katılımcı ID
   * @param int $reward_id Ödül ID
   * @return bool İşlem sonucu
   */
  public function set_manual_winner($draw_id, $participant_id, $reward_id)
  {
    $this->db->trans_begin();

    try {
      // Çekilişin aktif olup olmadığını kontrol et
      $draw = $this->get_draw($draw_id);
      if (!$draw || $draw->status != 1) {
        return false;
      }

      // Katılımcının çekilişe katılıp katılmadığını kontrol et
      $participant = $this->db->where('id', $participant_id)
        ->where('draw_id', $draw_id)
        ->get('draw_participants')
        ->row();
      if (!$participant) {
        return false;
      }

      // Ödülün çekilişe ait olup olmadığını kontrol et
      $reward = $this->db->where('id', $reward_id)
        ->where('draw_id', $draw_id)
        ->get('draw_rewards')
        ->row();
      if (!$reward) {
        return false;
      }

      // Kazanan olarak ekle - is_manual sütunu olmadığı için kaldırıldı
      $this->db->insert('draw_winners', [
        'draw_id' => $draw_id,
        'participant_id' => $participant_id,
        'reward_id' => $reward_id,
        'created_at' => date('Y-m-d H:i:s')
      ]);

      // Bakiye ödülü ise otomatik olarak ekle
      if ($reward->type == 'bakiye' && isset($reward->amount) && $reward->amount > 0) {
        // Bakiye güncellemesi için güvenli SQL kullan
        $this->db->where('id', $participant->user_id)
          ->set('balance', 'balance + ' . $this->db->escape($reward->amount), false)
          ->update('user');

        // Ödülü teslim edildi olarak işaretle
        $this->db->where('participant_id', $participant_id)
          ->where('reward_id', $reward_id)
          ->update('draw_winners', [
            'is_delivered' => 1,
            'delivery_date' => date('Y-m-d H:i:s')
          ]);
      }

      if ($this->db->trans_status() === FALSE) {
        $this->db->trans_rollback();
        return false;
      }

      $this->db->trans_commit();
      return true;
    } catch (Exception $e) {
      $this->db->trans_rollback();
      log_message('error', 'Manuel kazanan belirleme hatası: ' . $e->getMessage());
      return false;
    }
  }
}
