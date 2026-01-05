<?php $amount = 0; ?>
<?php foreach ($this->advanced_cart->contents() as $items){
    $amount = $amount + $items['price'] * $items['qty'];
}

if (!empty($this->session->userdata('info')['id'])){
    $notifications = getNotification($this->session->userdata('info')['id']);
}else{
    $notifications = [];
}
?>
            <div class="right-area" id="cart">
<div class="right-area-basket position-relative">
    <a href="<?= base_url('sepet') ?>" class="right-link notification square btn" id="basket-button">
        <div class="icon">
            <i class="fi fi-sr-shopping-cart"></i>
        </div>
        <div class="number">
            <?= $this->advanced_cart->total_items(); ?>
        </div>
    </a>
</div>

                <div class="position-relative">
                    <a href="#" class="right-link notification square">
                        <div class="icon"><i class="fi fi-ss-bell"></i></div>
                        <div class="number"><?=count($notifications)?></div>
                    </a>
                    <div class="fp-nav-notification-menu">
                        <div class="fp-nnm-title">Bildirimler</div>
                        <?php foreach ($notifications as $notification){ ?>
                                <?php $notificationManagement = $this->db->where('id', $notification->notification_id)->get('notification_management')->row(); ?>
                        <a class="fp-nnm-item <?= ($notification->seen_at == 1 ? "new" : NULL); ?> notification_link" data-notification-id="<?= $notification->id ?>" href="<?=($notificationManagement ? $notificationManagement->link : $notification->link)?>">
                            <div class="fp-nnm-item-img"><img src="<?=base_url('assets/img/notifications/') . ($notificationManagement ? $notificationManagement->img : 'notification.png');?>" alt=""></div>
                            <div class="fp-nnm-item-content">
                                <div class="fp-nnm-item-title"><?=($notificationManagement ? $notificationManagement->title : $notification->title)?></div>
                                <p class="fp-nnm-item-text"><?=($notificationManagement ? $notificationManagement->contents : $notification->contents)?></p>
                                <div class="fp-nnm-item-date"><?=($notificationManagement ? $notificationManagement->created_at : $notification->created_at)?></div>
                            </div>
                        </a>
                        <?php } ?>
                        <a href="#" class="fp-nnm-link allSetSeen">Tümünü Okundu Olarak İşaretle</a>
                    </div>
                </div>

                <?php if (!empty($this->session->userdata('info'))){ ?>
                <?php $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row(); ?>
                    <div class="position-relative">
                      <a href="#" class="user-display user-dropdown-trigger">
                        <img src="/favicon.ico" alt="Profile" class="me-2" style="width: 28px; height: 28px; border-radius: 50%;">
                        <div class="user-info">
                          <div class="username"><?= $user->name ?></div>
                        </div>
                        <i class="ri-arrow-down-s-line dropdown-arrow"></i>
                      </a>
                      <div class="fp-topnav-dropdown">
                        <div class="fp-dropdown-header">
                          <div class="header-background"></div>
                          <div class="header-content">
                            <div class="user-avatar">
                              <img src="/favicon.ico" alt="Profile">
                            </div>
                            <div class="user-details">
                              <div class="user-name"><?= $user->name ?></div>
                              <div class="user-balance">₺ <?= $user->balance ?></div>
                            </div>
                          </div>
                        </div>
                        <div class="fp-dropdown-menu">
                          <a href="<?= base_url('client/settings') ?>" class="fp-td-link">
                            <i class="ri-user-line"></i> Profil </a>
                          <a href="<?= base_url('client/product') ?>" class="fp-td-link">
                            <i class="ri-shopping-basket-line"></i> Siparişlerim </a>
                          <a href="<?= base_url('client/balance') ?>" class="fp-td-link">
                            <i class="ri-wallet-3-line"></i> Cüzdanım </a>
                          <a href="<?= base_url('client/my_dealer') ?>" class="fp-td-link">
                            <i class="ri-store-2-line"></i> Bayiliğim </a>
                          <a href="<?= base_url('client/draw-rewards') ?>" class="fp-td-link">
                            <i class="ri-trophy-line"></i> Çekiliş Ödülleri </a>
                          <a href="<?= base_url('client/case-item') ?>" class="fp-td-link">
                            <i class="ri-gift-line"></i> Kasadan Çıkanlar </a>
                          <a href="<?= base_url('client/ticket') ?>" class="fp-td-link">
                            <i class="ri-customer-service-2-line"></i> Destek </a>
                          <a href="<?= base_url('client/logout') ?>" class="fp-td-link logout-link">
                            <i class="ri-logout-box-line"></i> Güvenli Çıkış </a>
                        </div>
                      </div>
                    </div>
                        <div class="balance-area">
  <div class="balance-container position-relative">
    <div class="balance balance-trigger">
      <span class="balance-text">₺ Bakiye : <?= $user->balance ?> TL</span>
      <i class="ri-arrow-down-s-line balance-arrow"></i>
    </div>
    <div class="balance-dropdown">
      <a href="<?= base_url('client/balance') ?>" class="balance-dropdown-header">
        <h6 class="mb-1">Güncel Bakiyeniz</h6>
        <div class="current-balance">₺ <?= $user->balance ?></div>
      </a>
      <a href="<?= base_url('client/balance') ?>" class="balance-load-btn">
        <i class="ri-add-circle-line me-2"></i> Bakiye Yükle </a>
      <div class="balance-transactions">
        <div class="transaction-header">
          <i class="ri-time-line me-2"></i> Son 5 Bakiye İşlemi <span style="font-size: 10px; opacity: 0.7;">(0 kayıt)</span>
        </div>
        <div class="no-transactions">
          <div class="no-transactions-icon">
            <i class="ri-inbox-line"></i>
          </div>
          <div class="no-transactions-text">
            <div class="no-transactions-title">Henüz işlem geçmişi yok (Bakımda)</div>
            <div class="no-transactions-desc">İlk bakiye yükleme işleminizi gerçekleştirin (Bakımda)</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
                <!-- old bakiye profil 
                <a href="<?= base_url('client') ?>" class="right-link">
                    <div class="icon"><i class="ri-user-3-line"></i></div>
                    <div class="content">
                        <?php if ($this->session->userdata('info')): ?>
                            <?php $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row(); ?>
                            <div class="key"><?= $user->name ?></div>
                            <div class="value">Bakiye: <?= $user->balance ?> TL</div>
                        <?php endif ?>
                    </div>
                </a>
                -->
                <?php }else{ ?>
                    <a href="<?= base_url('hesap') ?>" class="btn btn-primary header-login-btn">
                       <i class="ri-login-box-line me-1"></i>
                       <span>
                        Giriş Yap
                       </span>
                    </a>
                        <a href="<?= base_url('hesap') ?>" class="btn btn-success header-register-btn">
                    <i class="ri-user-add-line me-1"></i>
                       <span>
                        Kayıt Ol
                       </span>
                    </a>
                <?php } ?>
                <a class="d-none" href="<?= base_url('sepet') ?>" class="right-link">
                    <div class="icon"><i class="ri-shopping-cart-2-line"></i></div>
                    <div class="content">
                        <div class="key">Sepet (<?= $this->advanced_cart->total_items(); ?>)</div>
                        <div class="value"><?= $amount ?> TL</div>
                    </div>
                </a>
            </div>
        </div>
    </div>

<script>
    // Tüm .notification_link sınıfına sahip öğeleri seç
    var notificationLinks = document.querySelectorAll('.notification_link');

    // Her bir bildirim linki için tıklama olayını dinle ve .ajax isteği oluştur
    notificationLinks.forEach(function(link) {
        $(link).on('click', function(event) {
            event.preventDefault(); // Varsayılan tıklama davranışını engelle

            var redirectUrl = $(this).attr('href');

            // Tıklanan bildirimin ID'sini al
            var notificationId = $(this).data('notification-id');
            console.log('Tıklanan bildirimin ID\'si: ' + notificationId);

            // AJAX isteği oluştur
            $.ajax({
                type: "POST",
                url: '<?=base_url("API/setSeen")?>',
                data: {notification_id: notificationId},
                success: function(response) {
                    window.location.href = redirectUrl;
                },
                error: function(xhr, status, error) {
                    // Hata
                }
            });
        });
    });

    //tümünü okundu olarak işaretle
    $('.allSetSeen').on('click', function(event) {
        event.preventDefault(); // Varsayılan tıklama davranışını engelle

        // AJAX isteği oluştur
        $.ajax({
            type: "POST",
            url: '<?=base_url("API/setAllSeen")?>',
            success: function(response) {
                console.log(response);
                window.location.href = window.location.href;
                // İşlem başarılı olduysa burada yapılacak işlemleri gerçekleştirin
                console.log('Tüm bildirimler başarıyla okundu.');
            },
            error: function(xhr, status, error) {
                // Hata durumunda burada işlem yapabilirsiniz
                console.error('Bir hata oluştu: ' + error);
            }
        });
    });

</script>
