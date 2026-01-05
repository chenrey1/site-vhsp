<section class="fp-section-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="fp-card fp-client-menu">
                    <div class="user-info">
                        <div class="icon"><i class="ri-user-3-line"></i></div>
                        <div class="content">
                            <?php if ($this->session->userdata('info')): ?>
                                <?php $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row(); ?>
                                <div class="mail"><?= $user->email ?></div>
                                <div class="money">Bakiye: <?= $user->balance ?> TL</div>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="content-menu">
                        <ul class="list-unstyled mb-0 list-menu client-menu">
                            <li>
                                <a href="#" class="link toggle-client-menu">
                                    <div class="icon"><i class="ri-menu-line"></i></div>
                                    <div class="text">Menü</div>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('client/product') ?>" class="link mobile-none">
                                    <div class="icon"><i class="ri-shopping-bag-line"></i></div>
                                    <div class="text">Siparişlerim</div>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('client/balance') ?>" class="link mobile-none">
                                    <div class="icon"><i class="ri-wallet-3-line"></i></div>
                                    <div class="text">Cüzdanım</div>
                                </a>
                            </li>
                            <?php if ($properties->isSubscription == 1) { ?>
                            <li>
                                <a href="<?= base_url('client/my_subscription') ?>" class="link mobile-none">
                                    <div class="icon"><i class="ri-vip-crown-line"></i></div>
                                    <div class="text">Aboneliklerim</div>
                                </a>
                            </li>
                            <?php } ?>
                            <li>
                                <a href="<?= base_url('client/my_dealer') ?>" class="link mobile-none">
                                    <div class="icon"><i class="ri-store-2-line"></i></div>
                                    <div class="text">Bayilik Bilgilerim</div>
                                </a>
                            </li>
                            <?php if ($properties->shop_active == 1) { ?>
                                <li>
                                    <a href="<?= base_url('client/orders') ?>" class="link mobile-none">
                                        <div class="icon"><i class="ri-store-line"></i></div>
                                        <div class="text">Pazaryeri</div>
                                    </a>
                                </li>
                            <?php } ?>
                            <li>
                                <a href="<?= base_url('client/streamer') ?>" class="link mobile-none">
                                    <div class="icon"><i class="ri-base-station-line"></i></div>
                                    <div class="text">Yayıncı Paneli</div>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('client/draw_rewards') ?>" class="link mobile-none">
                                    <div class="icon"><i class="ri-gift-line"></i></div>
                                    <div class="text">Çekiliş Ödülleri</div>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('client/my_donations') ?>" class="link mobile-none">
                                    <div class="icon"><i class="ri-chat-history-line"></i></div>
                                    <div class="text">Yaptığım Bağışlar</div>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('client/reference') ?>" class="link mobile-none">
                                    <div class="icon"><i class="ri-user-smile-line"></i></div>
                                    <div class="text">Referanslarım</div>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('client/settings') ?>" class="link mobile-none">
                                    <div class="icon"><i class="ri-user-settings-line"></i></div>
                                    <div class="text">Hesap Ayarları</div>
                                </a>
                            </li>
                            <?php if ($properties->api_is_active == 1) { ?>
                            <li>
                                <a href="<?= base_url('client/api_settings') ?>" class="link mobile-none">
                                    <div class="icon"><i class="ri-code-line"></i></div>
                                    <div class="text">API Ayarları</div>
                                </a>
                            </li>
                            <?php } ?>
                            <li>
                                <a href="<?= base_url('client/ticket') ?>" class="link mobile-none">
                                    <div class="icon"><i class="ri-question-answer-line"></i></div>
                                    <div class="text">Destek</div>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('client/logout') ?>" class="link mobile-none text-danger">
                                    <div class="icon"><i class="ri-logout-box-r-line"></i></div>
                                    <div class="text">Çıkış Yap</div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
