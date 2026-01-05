<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cron extends CI_Controller
{

  public function __construct()
  {
    parent::__construct();

  }
  // Süresi dolan çekilişleri otomatik bitir (örnek: /cron/finish_expired_draws?key=EYKJzzeKoF)
  public function finish_expired_draws()
  {
    $key = $this->input->get('key');
    if ($key !== 'EYKJzzeKoF') {
      echo 'Yetkisiz erişim';
      return;
    }
    $this->load->model('M_Draw');
    $expired = $this->M_Draw->get_expired_draws();
    foreach ($expired as $draw) {
      $this->M_Draw->finish_draw($draw->id);
    }
    echo count($expired) . " çekiliş bitirildi.";
  }

  public function finish_draws()
  {
    $this->load->model('M_Draw');

    // Loglama için
    echo "Çekiliş sonlandırma görevi başladı: " . date('Y-m-d H:i:s') . "\n";

    // Süresi dolan çekilişleri sonlandır
    $completed_draws = $this->M_Draw->auto_finish_draws();

    if (empty($completed_draws)) {
      echo "Sonlandırılacak çekiliş bulunamadı.\n";
    } else {
      echo "Toplam " . count($completed_draws) . " çekiliş sonlandırıldı:\n";

      foreach ($completed_draws as $draw) {
        $status = $draw['success'] ? 'Başarılı' : 'Başarısız';
        echo "- Çekiliş #{$draw['id']} ({$draw['name']}): {$status}\n";
      }
    }

    echo "Çekiliş sonlandırma görevi tamamlandı: " . date('Y-m-d H:i:s') . "\n";
  }

}
