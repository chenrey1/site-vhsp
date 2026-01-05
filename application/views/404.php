    <style>
        /* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        @@                               @@
        @@        Orius - Oritorius      @@
        @@                               @@
        @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
        img {user-select: none !important}
        .gp-card-content {
            text-align: center;
        }
        .gp-card-content img {
            width: 50%;
            display: block;
            margin: auto;
        }
        .gp-card-content a {
            color: #fff;
            font-weight: 600;
            font-size: 26px;
        }
        .gp-vcenter {
            min-height: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
    </style>

    <div class="gp-vcenter">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto">
    
                    <div class="gp-card">
                        <div class="gp-card-content">
                            <?php if ($status == 0) {?>
                                <img src="<?= base_url('assets/img/404.png') ?>" alt="404 Resmi">
                                <a href="<?= base_url(); ?>" class="text-primary">Sitemizde yer alan tüm klasörleri tek tek inceledik ancak senin aradığını bulamadık. Tekrar deneyelim mi? <i class="fa fa-long-arrow-alt-right"></i></a>
                            <?php }else if($status == 1){ ?>
                                    <?php
                                        //destroy cart
                                        $this->advanced_cart->destroy();
                                    ?>
                                <img src="<?= base_url('assets/img/ok.png') ?>" alt="İşlem Durumu Resmi">
                                <a href="<?= base_url(); ?>" class="text-primary">Yatırımın Onaylandı! Geri dön ve bakiyeni dilediğin gibi harca <i class="fa fa-long-arrow-alt-right"></i></a>
                            <?php }else if($status == 2){ ?>
                                <img src="<?= base_url('assets/img/fail.png') ?>" alt="İşlem Durumu Resmi">
                                <a href="<?= base_url(); ?>" class="text-primary">Ödeme şirketinde oluşan bir problemden dolayı yatırımın reddedildi. Eğer bunun bir hata olduğunu düşünüyorsan bize bildir. <i class="fa fa-long-arrow-alt-right"></i></a>
                            <?php } ?>
                        </div>
                    </div>
    
                </div>
            </div>
        </div>
    </div>
    
