<?php $properties = $this->db->where('id', 1)->get('properties')->row(); ?>
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
<!DOCTYPE html>
<html lang="tr-TR" data-theme="dark">
<head>
    <meta charset="UTF-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="<?= $properties->title ?>">
<?php
if (!empty($meta->meta)) {
    // Ürüne özel meta açıklama varsa onu kullan
    $metaDescription = strip_tags($meta->meta);
} elseif (!empty($categories->description)) {
    // Ürün açıklaması fallback
    $metaDescription = strip_tags($categories->description);
} elseif (!empty($categories->description)) {
    // Kategori açıklaması fallback
    $metaDescription = strip_tags($categories->description);
} else {
    // Site genel açıklaması fallback     <meta http-equiv="Content-Type" content="text/html; charset=utf8">     <meta name="language" content="Turkish">
    $metaDescription = strip_tags($properties->description);
}
?>
<meta name="description" content="<?= $metaDescription ?>">


    <meta name="robots" content="index, follow">
    <?php
$brand = 'ValoHesap';
$seoSpecialTitle = 'Valorant Random Hesap';

// $title veya $properties->title'dan gelen başlık
$currentTitle = !empty($title) ? $title : $properties->title;

// Eğer başlıkta "Valorant Random Hesap" geçiyorsa özel SEO başlığı kullan
if (stripos($currentTitle, 'Valorant Random Hesap') !== false) {
    $finalTitle = $seoSpecialTitle;
} else {
    $finalTitle = $currentTitle;
}
?>
<title><?= htmlspecialchars($finalTitle, ENT_QUOTES, 'UTF-8') ?></title>

<script src="<?= base_url('assets/' . $properties->theme) ?>/js/jquery.min.js"></script>

<link rel="stylesheet" href="<?= base_url('assets/' . $properties->theme) ?>/css/bootstrap.min.css">

<script src="<?= base_url('assets/' . $properties->theme) ?>/js/jquery.min.js"></script>

<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/lang/summernote-tr-TR.js"></script>    <link rel="stylesheet" href="<?= base_url('assets/' . $properties->theme) ?>/css/swiper-bundle.min.css">    
    <link rel="stylesheet" href="<?= base_url('assets/' . $properties->theme) ?>/css/style.css?v=51213213">
    <link rel="stylesheet" href="<?= base_url('assets/' . $properties->theme) ?>/css/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-straight/css/uicons-solid-straight.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script type="text/javascript">
        var baseURL = "<?= base_url() ?>";
    </script>
    <script src="<?= base_url('assets/' . $properties->theme) ?>/js/shop.js"></script>
<?= $properties->google_analytics ?>
    
    <!-- Gündüz Modu Düzeltmesi -->
    <style>
    /* İlan kartlarını ters çevir AMA fiyat ve isim hariç */
    [data-theme="light"] .fp-product-item {
        filter: invert(1) !important;
        -webkit-filter: invert(1) !important;
    }
    
    /* Görselleri tekrar düzelt */
    [data-theme="light"] .fp-product-item img {
        filter: invert(1) !important;
        -webkit-filter: invert(1) !important;
    }
    
    /* Butonu tekrar düzelt */
    [data-theme="light"] .fp-product-item .btn {
        filter: invert(1) !important;
        -webkit-filter: invert(1) !important;
    }
    
    /* Badge'i tekrar düzelt */
    [data-theme="light"] .fp-product-item .rgb-badge-wide {
        filter: invert(1) !important;
        -webkit-filter: invert(1) !important;
    }
    
    /* Fiyat ve ürün ismini NORMAL BIRAK (invert etme) */
    [data-theme="light"] .fp-product-item .product-name,
    [data-theme="light"] .fp-product-item .price,
    [data-theme="light"] .fp-product-item .price-new {
        filter: none !important;
        -webkit-filter: none !important;
        color: #000000 !important;
    }
    </style>
</head>
<body>
<style>
  
    /* Profile dropdown container */
    .fp-topnav-dropdown {
        background: var(--bg-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        min-width: 280px;
        position: absolute;
        top: 100%;
        right: 0;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 99999;
        display: none;
    }

    .fp-topnav-dropdown.active {
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        display: block !important;
    }

    [data-theme="light"] .fp-topnav-dropdown {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    [data-theme="dark"] .fp-topnav-dropdown {
        background: #1a1a1a;
        border: 1px solid #333;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    /* Modern Header Styles */
    .fp-topnav {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.9));
        backdrop-filter: blur(20px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 4px 0;
        position: relative;
        z-index: 1000;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    [data-theme="dark"] .fp-topnav {
        background: linear-gradient(135deg, rgba(26, 26, 26, 0.95), rgba(26, 26, 26, 0.9));
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    /* Social Media Links */
    .social-media-links {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .social-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        color: var(--text-color);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-link i {
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        text-decoration: none;
        transform: scale(1.1);
    }

    /* Light theme - black icons */
    [data-theme="light"] .social-link {
        color: #000000;
    }

    [data-theme="light"] .social-link:hover {
        color: #333333;
    }

    /* Mobile cart badge */
    .mobile-cart-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #ff4444;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
        line-height: 1;
    }
    
    [data-theme="dark"] .mobile-cart-badge {
        background: #ff6666;
    }

    /* Dark theme - white icons */
    [data-theme="dark"] .social-link {
        color: #ffffff;
    }

    [data-theme="dark"] .social-link:hover {
        color: #cccccc;
    }

    .topnav-link {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 10px;
        color: #000000;
        text-decoration: none;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease;
        opacity: 1;
        position: relative;
    }

    .topnav-link .font-weight-bold {
        font-weight: 500;
    }

    .topnav-link:hover {
        opacity: 1;
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-1px);
        color: #000000;
        text-decoration: none;
    }

    [data-theme="dark"] .topnav-link {
        color: #ffffff;
    }

    [data-theme="dark"] .topnav-link:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #ffffff;
    }

    .topnav-link i {
        font-size: 14px;
    }

    .topnav-link .dropdown-arrow {
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    /* Topnav Dropdown Styles */
    .topnav-dropdown {
        position: relative;
        display: inline-block;
    }

    .topnav-dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        background: var(--bg-color);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 8px 0;
        min-width: 250px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        z-index: 9999;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    [data-theme="light"] .topnav-dropdown-menu {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    [data-theme="dark"] .topnav-dropdown-menu {
        background: #1a1a1a;
        border: 1px solid #333;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }

    .topnav-dropdown:hover .topnav-dropdown-menu,
    .topnav-dropdown.active .topnav-dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .topnav-dropdown:hover .dropdown-arrow,
    .topnav-dropdown.active .dropdown-arrow {
        transform: rotate(180deg);
    }

    .topnav-dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        color: #000000;
        text-decoration: none;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }

    .topnav-dropdown-item:hover {
        background: none;
        color: #0d6efd;
        text-decoration: none;
    }

    [data-theme="dark"] .topnav-dropdown-item {
        color: #ffffff;
    }

    [data-theme="dark"] .topnav-dropdown-item:hover {
        color: #0d6efd;
    }

    .topnav-dropdown-item i {
        font-size: 14px;
        width: 16px;
        text-align: center;
    }

    .topnav-dropdown-divider {
        height: 1px;
        background: var(--border-color);
        margin: 8px 0;
    }

    [data-theme="light"] .topnav-dropdown-divider {
        background: #e5e7eb;
    }

    [data-theme="dark"] .topnav-dropdown-divider {
        background: #333;
    }
    
</style>

<div id="leaf-container"></div>

<div class="bs-example" id="toastArea">
    <div style="position: relative; z-index: 9999; top: 70px; background-color: gray;">
        <div style="position: absolute; top: 0; right: 20px; min-width: 300px;">
            <?= alert() ?>
        </div>
    </div>
</div>
<nav class="fp-topnav d-none d-sm-block">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="social-media-links">
                    <a href="https://discord.gg/AjbGUurzEH" class="social-link" title="Discord">
                        <i class="ri-discord-fill"></i>
                    </a>
                    <a href="https://www.instagram.com/valohesap_com/" class="social-link" title="Instagram">
                        <i class="ri-instagram-fill"></i>
                    </a>
                    <a href="#" class="social-link" title="Twitter">
                        <i class="ri-twitter-x-fill"></i>
                    </a>
                    <a href="#" class="social-link" title="YouTube">
                        <i class="ri-youtube-fill"></i>
                    </a>
                    <a href="#" class="social-link" title="Telegram">
                        <i class="ri-telegram-fill"></i>
                    </a>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <!-- Destek Merkezi Dropdown -->
                <div class="topnav-dropdown">
                    <a href="#" class="topnav-link dropdown-trigger">
                        <i class="fi fi-sr-user-headset icon-left"></i>
                        <span class="font-weight-bold">Destek Merkezi</span>
                        <i class="ri-arrow-down-s-line dropdown-arrow"></i>
                    </a>
                    <div class="topnav-dropdown-menu">
                        <a href="https://valohesap.com/guvenilirlik" class="topnav-dropdown-item">
                            <i class="fi fi-sr-shield-check"></i>
                            <span>Güvenilirlik</span>
                        </a>
                        <a href="https://valohesap.com/destek-hatti" class="topnav-dropdown-item">
                            <i class="fi fi-sr-user-headset"></i>
                            <span>Destek Hattı</span>
                        </a>
                        <a href="https://valohesap.com/sss" class="topnav-dropdown-item">
                            <i class="fi fi-sr-messages-question"></i>
                            <span>Sıkça Sorulan Sorular</span>
                        </a>
                    </div>
                </div>

                <!-- Kurumsal Menü Dropdown -->
                <div class="topnav-dropdown">
                    <a href="#" class="topnav-link dropdown-trigger">
                        <i class="fi fi-sr-building"></i>
                        <span>Kurumsal</span>
                        <i class="ri-arrow-down-s-line dropdown-arrow"></i>
                    </a>
                    <div class="topnav-dropdown-menu">
                        <a href="https://valohesap.com/hakkimizda" class="topnav-dropdown-item">
                            <i class="fi fi-sr-info"></i>
                            <span>Hakkımızda</span>
                        </a>
                        <a href="https://valohesap.com/sayfa/iletisim" class="topnav-dropdown-item">
                            <i class="fi fi-sr-phone-office"></i>
                            <span>İletişim</span>
                        </a>
                        <a href="https://valohesap.com/guvenilirlik" class="topnav-dropdown-item">
                            <i class="fi fi-sr-shield-check"></i>
                            <span>Güvenilirlik</span>
                        </a>
                        <a href="https://valohesap.com/sözleşmeler" class="topnav-dropdown-item">
                            <i class="fi fi-sr-file"></i>
                            <span>Sözleşmeler</span>
                        </a>
                        <a href="https://valohesap.com/hizli-erişim" class="topnav-dropdown-item">
                            <i class="fi fi-sr-bolt"></i>
                            <span>Hızlı Erişim</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
<div class="header-top d-none">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                <div class="header-top-left">
                      <h1>
                       Türkiye'nin en güvenilir ve ucuz platformu ValoHesap'a hoş geldiniz!
                      </h1>  
                </div>
            </div>
           <div class="col-lg-6 col-md-12 col-sm-12 col-12">
                <div class="header-top-right">
                    <div class="htr-box">
                        <a href="">
                          <i class="fi fi-sr-blog-text"></i>
                            <span>
                                Blog
                            </span>
                        </a>
                    </div>
                    <div class="htr-box">
                        <a href="">
                          <i class="fi fi-rr-user-headset"></i>
                            <span>
                                İletişim
                            </span>
                        </a>
                    </div>
                    <div class="htr-box">
                        <a href="">
                        <i class="fi fi-rr-world"></i>
                            <span>
                                Turkish
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<header class="fp-header">
    <div class="container">
        <div class="grid">
<div class="logo-area">
    <a href="<?= base_url() ?>" class="logo">
        <?php if ($properties->choose == 0): ?>
            <img src="<?= base_url('assets/future/img/vhsp-aqua.png') ?>" class="logo-dark" alt="Logo">
            <img src="<?= base_url('assets/future/img/vhsp-siyah.png') ?>" class="logo-light" alt="Logo">
        <?php else: ?>
            <?= $properties->name ?>
        <?php endif; ?>
    </a>
    <div class="campaign-notification" data-tooltip="Açılışa özel birçok üründe kampanya sizleri bekliyor! 🎉">
        <span class="icon-area">
            <i class="ri-gift-fill"></i>
        </span>
    </div>
</div>

<div class="mobile-dark-light-area">
    <div class="fp-color-selector">
        <a href="javascript:void(0);" class="link link-light-theme">
            <i class="fi fi-sr-brightness"></i>
        </a>
        <a href="javascript:void(0);" class="link link-dark-theme">
            <i class="ri-moon-fill"></i>
        </a>
    </div>
</div>
            <a href="#" class="btn btn-primary btn-all-categories"><i class="ri-menu-line"></i></a>
            <div class="position-relative mobile-notification">
                <a href="#" class="right-link notification">
                    <div class="icon"><i class="ri-notification-3-line"></i></div>
                    <div class="number"><?=count($notifications)?></div>
                </a>
                <div class="fp-nav-notification-menu">
                    <div class="fp-nnm-title">Bildirimler</div>
                    <?php
                    foreach ($notifications as $notification){ ?>
                    <a class="fp-nnm-item <?= ($notification->seen_at == 1 ? "new" : NULL); ?> notification_link" data-notification-id="<?= $notification->id ?>" href="<?=$notification->link?>">
                        <div class="fp-nnm-item-img"><img src="<?=base_url('assets/img/notifications/') . $notification->img;?>" alt=""></div>
                        <div class="fp-nnm-item-content">
                            <div class="fp-nnm-item-title"><?=$notification->title?></div>
                            <p class="fp-nnm-item-text"><?=$notification->contents?></p>
                            <div class="fp-nnm-item-date"><?= format_date($notification->created_at); ?></div>
                        </div>
                    </a>
                    <?php } ?>
                    <a href="#" class="fp-nnm-link allSetSeen">Tümünü Okundu Olarak İşaretle</a>
                </div>
            </div>
                        <div class="mobile-actions-area">
                                  <a href="<?= base_url('hesap') ?>" class="mobile-auth-btn mobile-auth-login">
                       <i class="ri-login-box-line me-1"></i>
                       <span>
                        Giriş Yap
                       </span>
                    </a>
                        <a href="<?= base_url('hesap') ?>" class="mobile-auth-btn mobile-auth-register">
                    <i class="ri-user-add-line me-1"></i>
                       <span>
                        Kayıt Ol
                       </span>
                    </a>
            </div>
            <div class="search">
                <div class="search-box">
                    <input type="text" class="form-control rounded-pill" placeholder="Valorant hesap, Lol RP veya çeşitli ürün ara..." id="searchInput" onfocusout="disable_form()" oninput="search_form()" autocomplete="off">
                    <i class="ri-search-line icon"></i>
                </div>
                <div class="search-results d-none" id="serch-results"></div>
            </div>
            <div class="right-area" id="cart">
                <div class="right-area-dark-light">
                <div class="fp-color-selector">
                <a href="#" class="link link-light-theme">
                    <i class="fi fi-sr-brightness"></i>
                </a>
                <a href="#" class="link link-dark-theme">
                    <i class="ri-moon-fill"></i>
                </a>
            </div>
                </div>
<div class="right-area-basket position-relative">
    <a href="<?= base_url('sepet') ?>" class="right-link notification square btn" id="basket-button">
        <div class="icon">
            <i class="fi fi-sr-shopping-cart"></i>
        </div>
<div class="number" id="cart-count">
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
</header>
<nav class="fp-navbar main-menu-web">
  <div class="container">
    <div class="flex">

      <ul class="list-unstyled mb-0 list">
        <li>
          <a href="<?= base_url('tum-kategoriler') ?>" class="all-categories link">
            <span class="icon-area">
              <i class="ri-compass-fill me-0"></i>
            </span>
            <span>Kategoriler</span>
            <i class="fi fi-rs-angle-small-down"></i>
          </a>
        </li>

        <?php 
          // Kategorileri özel sıraya göre düzenle
          $ordered_categories = [];
          $category_order = ['valorant', 'steam', 'fortnite', 'pubg', 'discord'];
          
          // Önce sıralı kategorileri ekle
          foreach ($category_order as $order_slug) {
              foreach ($category as $c) {
                  $slug = strtolower($c->slug);
                  if (strpos($slug, $order_slug) !== false) {
                      // League of Legends'i atla
                      if (strpos($slug, 'league-of-legends') !== false || strpos($slug, 'lol') !== false) {
                          continue;
                      }
                      if (!in_array($c, $ordered_categories)) {
                          $ordered_categories[] = $c;
                      }
                  }
              }
          }
        ?>
        <?php foreach ($ordered_categories as $c): ?>
          <?php 
            $subCategory = $this->db
                ->where('isActive', 1)
                ->where('mother_category_id', $c->id)
                ->get('category')
                ->result();

            $hasSub = !empty($subCategory);
            $icon = '';
            $bgStyle = '';
            $slug = strtolower($c->slug);

            if (strpos($slug, 'valorant') !== false) {
                $icon = base_url('assets/future/img/valorant.png');
                $bgStyle = 'background-color: rgba(255, 67, 80, 0.1) !important';
            } elseif (strpos($slug, 'steam') !== false) {
                $icon = base_url('/assets/future/img/steams.png');
                $bgStyle = 'background-color: rgba(0, 137, 255, 0.1) !important';
            } elseif (strpos($slug, 'fortnite') !== false) {
                $icon = base_url('/assets/future/img/fortnite.png');
                $bgStyle = 'background-color: rgba(71, 172, 226, 0.1) !important';
            } elseif (strpos($slug, 'pubg') !== false) {
                $icon = base_url('/assets/future/img/pubg-m.png');
                $bgStyle = 'background-color: rgba(255, 187, 0, 0.1) !important';
            } elseif (strpos($slug, 'discord') !== false) {
                $icon = base_url('/assets/future/img/discord.png');
                $bgStyle = 'background-color: rgba(88, 101, 242, 0.1) !important';
            }
          ?>

          <?php if ($hasSub): ?>
            <li class="header-dropdown-item">
              <div class="d-flex link">
                <span class="icon-area" style="<?= $bgStyle ?>">
                  <?php if ($icon): ?><img src="<?= $icon ?>" alt=""><?php endif; ?>
                </span>
                <span class="header-dropdown-head"> <?= $c->name ?> </span>
                <i class="fi fi-rs-angle-small-down"></i>
              </div>
              <div class="header-dropdown-area">
                <div class="container">
                  <div class="grid-dropdown">
                    <?php foreach ($subCategory as $sc): ?>
                      <a class="fp-navbar-dropdown-link" href="<?= base_url('kategori/' . $sc->slug) ?>">
                        <img src="<?= base_url('assets/img/category/' . $sc->img) ?>" alt="">
                        <?= $sc->name ?>
                      </a>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </li>
          <?php else: ?>
            <li>
              <a class="link" href="<?= base_url('kategori/' . $c->slug) ?>">
                <span class="icon-area" style="<?= $bgStyle ?>">
                  <?php if ($icon): ?><img src="<?= $icon ?>" alt=""><?php endif; ?>
                </span>
                <span><?= $c->name ?></span>
              </a>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>

      <ul class="list-unstyled mb-0 list">
        <li>
          <a href="<?= base_url('paketler') ?>" class="link paketler">
            <span class="icon-area" style="background-color: rgba(52, 152, 219, 0.1)">
              <i class="ri-gift-line text-primary"></i>
            </span>
            <span class="paketler">Paketler</span>
          </a>
        </li>
        <li>
          <a href="/cekilisler" class="link cekilisler">
            <span class="icon-area" style="background-color: rgba(0, 137, 255, 0.1)">
              <i class="ri-heart-fill text-primary"></i>
            </span>
            <span class="cekilisler">Çekilişler</span>
            <span class="badge new">Yeni</span>
          </a>
        </li>
        <li>
          <a href="<?= base_url('yayincilar') ?>" class="link yayincilar">
            <span class="icon-area" style="background-color: rgb(56 40 108);">
              <i style="color: #a392dc;" class="ri-bard-fill"></i>
            </span>
            <span class="yayincilar">Yayıncılar</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="marquee-container d-none">
  <div class="container container-title">
    <div class="marquee-title">
      <i class="ri-megaphone-fill"></i> Duyuru
    </div>
  </div>
  <div class="container overflow-hidden">
    <div class="marquee-text"> <a href="/orius/">Valohesap.com</a>'a Hoşgeldiniz. Valorant Random VP Paketlerimizde ekstra şans aktif! Ayrıca Valorant random hesaplarımızda indirimlerimiz sizlerle !! </div>
  </div>
</div>


  <!-- old fp-navbar

<nav class="fp-navbar">
    <div class="container">
        <div class="flex">
            <ul class="list-unstyled mb-0 list">
                <li><a href="<?= base_url('tum-kategoriler') ?>" class="link button"><i class="ri-menu-line"></i> Tüm Kategoriler</a></li>
                <?php if ($this->db->where('seller_id >', 2)->count_all_results('product') > 0): ?>
                <li><a href="<?= base_url('ilan-pazari') ?>" class="link">İlan Pazarı</a></li>
                <?php endif ?>
                <?php if ($this->db->where('isStreamer', 1)->count_all_results('user') > 0): ?>
                    <li><a href="<?= base_url('yayincilar') ?>" class="link">Yayıncılar</a></li>
                <?php endif ?>
                <?php foreach (getActiveCategories() as $c) { ?>
                    <?php if ($c->has_subcategories) { ?>
                        <li class="fp-navbar-dropdown-item position-relative">
                            <a href="javascript:void(0)" class="fp-navbar-dropdown-item-open"><i class="ri-arrow-down-s-line"></i></a>
                            <a href="<?= base_url('kategori/') . $c->slug ?>" class="link"><?= $c->name ?></a>
                            <div class="fp-navbar-dropdown-menu">
                                <?php foreach ($c->subcategories as $sc) { ?>
                                    <a class="fp-navbar-dropdown-link" href="<?= base_url('kategori/') . $sc->slug ?>"><?= $sc->name ?></a>
                                <?php } ?>
                            </div>
                        </li>
                    <?php } else { ?>
                        <li><a class="link" href="<?= base_url('kategori/') . $c->slug ?>"><?= $c->name ?></a></li>
                    <?php } ?>
                <?php } ?>
            </ul>
            <div class="fp-color-selector">
                <a href="#" class="link link-light-theme"><i class="ri-sun-line"></i></a>
                <a href="#" class="link link-dark-theme"><i class="ri-moon-line"></i></a>
            </div>
        </div>
    </div>
</nav>

 -->

<div class="fp-mobile-bar">
    <div class="grid h-100">
        <a href="<?= base_url() ?>" class="link">
            <div class="icon"><i class="ri-home-5-line"></i></div>
            <div class="text">Ana Sayfa</div>
        </a>
        <a href="<?= base_url('tum-kategoriler') ?>" class="link">
            <div class="icon"><i class="ri-menu-line"></i></div>
            <div class="text">Kategoriler</div>
        </a>
        <a href="<?= base_url('sepet') ?>" class="link position-relative">
            <div class="icon"><i class="ri-shopping-cart-2-line"></i></div>
            <div class="text">Sepet</div>
            <?php if($this->advanced_cart->total_items() > 0): ?>
            <div class="mobile-cart-badge"><?= $this->advanced_cart->total_items(); ?></div>
            <?php endif; ?>
        </a>
        <a href="<?= base_url('ilan-pazari') ?>" class="link">
            <div class="icon"><i class="ri-store-line"></i></div>
            <div class="text">İlan Pazarı</div>
        </a>
        <a href="<?= !empty($this->session->userdata('info')) ? base_url('client') : base_url('hesap'); ?>" class="link">
            <div class="icon"><i class="ri-user-3-line"></i></div>
            <div class="text">Hesap</div>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Bildirim linklerine tıklayınca okundu yapma ve yönlendirme
    $('.notification_link').on('click', function (event) {
        event.preventDefault();

        var redirectUrl = $(this).attr('href');
        var notificationId = $(this).data('notification-id');

        $.ajax({
            type: "POST",
            url: 'https://valohesap.com/API/setSeen',
            data: { notification_id: notificationId },
            success: function () {
                window.location.href = redirectUrl;
            }
        });
    });

    // Tümünü okundu olarak işaretle
    $('.allSetSeen').on('click', function (event) {
        event.preventDefault();

        $.ajax({
            type: "POST",
            url: 'https://valohesap.com/API/setAllSeen',
            success: function () {
                location.reload();
            }
        });
    });

    // Bildirim menüsünü aç/kapat
    $('.right-link.notification').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var menu = $(this).siblings('.fp-nav-notification-menu');
        var isVisible = menu.hasClass('show-notification');

        $('.fp-nav-notification-menu').removeClass('show-notification');
        if (!isVisible) {
            menu.addClass('show-notification');
        }
    });

    // Menü dışına tıklayınca kapat
    $(document).on('click', function () {
        $('.fp-nav-notification-menu').removeClass('show-notification');
    });

    // Menü içindeyken kapanmasın
    $('.fp-nav-notification-menu').on('click', function (e) {
        e.stopPropagation();
    });

    // Sepet butonu kontrolü
    var basketBtn = document.getElementById('basket-button');
    if (basketBtn) {
        basketBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            window.location.href = this.href;
        });
    }

    // Dropdown menü kapatma
    const dropdowns = document.querySelectorAll('.header-dropdown-area');
    document.addEventListener('click', function (event) {
        dropdowns.forEach(function (dropdown) {
            const parentItem = dropdown.closest('.header-dropdown-item');
            if (dropdown.offsetParent !== null && !parentItem.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    });
});
<script>
$(document).ready(function() {
// Kesin ve Hızlı Geçiş Komutu
    $('.link-dark-theme, .link-light-theme').on('click mousedown', function(e) {
        // Tarayıcıya "önce görseli değiştir" emri veriyoruz
        var isDark = $(this).hasClass('link-dark-theme');
        
        // CSS Değişkenlerini anında manipüle edelim (Eğer siten değişken kullanıyorsa)
        if (isDark) {
            document.documentElement.setAttribute('data-theme', 'dark'); // Bazı temalar bunu kullanır
            $('body').addClass('dark-theme dark').removeClass('light-theme light');
        } else {
            document.documentElement.setAttribute('data-theme', 'light');
            $('body').addClass('light-theme light').removeClass('dark-theme dark');
        }
        
        // Gecikmeyi yaratan asıl AJAX işlemini arka plana itiyoruz
        // Eğer hala gecikiyorsa, bu fonksiyonun en başına e.stopImmediatePropagation(); ekleyebiliriz.
    });
</script>
<script>
    $(document).on('click', '#basket-button', function(e) {
        e.stopImmediatePropagation(); // Diğer engelleyen scriptleri sustur
        window.location.href = $(this).attr('href'); // Direkt linke git
    });
</script>
