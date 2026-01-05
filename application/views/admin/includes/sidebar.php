<?php 
    // Tüm veritabanı sorgularını başta yapalım
    $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row(); 
    $properties = $this->db->where('id', 1)->get('properties')->row();
    
    // Bekleyen bildirimleri ve sayaçları başta hesaplayalım
    $bank_transfer = $this->db->where('isActive',1)->count_all_results('bank_transfer');
    $product_comments = $this->db->where('isActive',0)->count_all_results('product_comments');
    $pending_products = $this->db->where('isActive',3)->count_all_results('product');
    $pending_tickets = $this->db->where('status',1)->count_all_results('ticket');
    $pending_requests = $this->db->where('status',2)->count_all_results('request');
    $pending_objections = $this->db->where('status',2)->count_all_results('product_objections');
    $pending_applications = $this->db->where('status', 'pending')->count_all_results('dealer_applications');
    
    // Kredi teklifleri ve vadesi geçen kredileri hesapla
    $pending_credit_offers = $this->db->where('status', 1)->count_all_results('credit_offers');
    $overdue_credits = $this->db->where('status', 4)->count_all_results('user_credits');
    $total_credit_notifications = $pending_credit_offers + $overdue_credits;
    
    // Menü öğesi oluşturmak için yardımcı fonksiyonlar
    function renderMenuItem($link, $icon, $title, $activeStatus, $currentStatus, $badge = null, $counter = 0) {
        $active = ($activeStatus == $currentStatus) ? 'active' : NULL;
        $badgeHtml = !empty($badge) ? '<span class="badge badge-info ml-2">'.$badge.'</span>' : '';
        $counterHtml = ($counter > 0) ? '<span class="badge badge-primary ml-2 text-right">+'.$counter.'</span>' : '';
        
        echo '<a class="nav-link '.$active.'" href="'.base_url($link).'">';
        echo '<div class="sb-nav-link-icon"><i class="'.$icon.'"></i></div>';
        echo $title . $badgeHtml . $counterHtml;
        echo '</a>';
    }
    
    function renderSubMenu($id, $icon, $title, $activeStatus, $currentStatus, $badge = null, $counter = 0) {
        $active = (in_array($currentStatus, (array)$activeStatus)) ? 'active' : NULL;
        $badgeHtml = !empty($badge) ? '<span class="badge badge-info ml-2">'.$badge.'</span>' : '';
        $counterHtml = ($counter > 0) ? '<span class="badge badge-'.($badge === 'Yeni' ? 'warning' : 'primary').' ml-2">+'.$counter.'</span>' : '';
        
        echo '<a class="nav-link collapsed '.$active.'" href="#" data-toggle="collapse" data-target="#'.$id.'" aria-expanded="false" aria-controls="'.$id.'">';
        echo '<div class="sb-nav-link-icon"><i class="'.$icon.'"></i></div>';
        echo $title . $badgeHtml . $counterHtml;
        echo '<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>';
        echo '</a>';
    }
    
    function renderSubMenuItem($link, $icon, $title, $counter = 0) {
        $counterHtml = ($counter > 0) ? '<span class="badge badge-primary ml-2 text-right">+'.$counter.'</span>' : '';
        
        echo '<a class="nav-link" href="'.base_url($link).'">';
        echo '<div class="sb-nav-link-icon"><i class="'.$icon.'"></i></div>';
        echo $title . $counterHtml;
        echo '</a>';
    }
    
    // Başlık oluşturmak için yeni yardımcı fonksiyon 
    function renderSection($id, $title, $icon, $badge = null, $activeStatuses = [], $currentStatus = null, $counter = 0) {
        $badgeHtml = !empty($badge) ? '<span class="badge badge-info ml-2">'.$badge.'</span>' : '';
        $counterHtml = ($counter > 0) ? '<span class="badge badge-primary ml-2 text-right">+'.$counter.'</span>' : '';
        $isActive = false;
        
        // Alt menülerden herhangi biri aktif mi kontrol et
        if (!empty($activeStatuses) && !empty($currentStatus)) {
            foreach ((array)$activeStatuses as $activeStatus) {
                if ($currentStatus == $activeStatus || (is_array($activeStatus) && in_array($currentStatus, $activeStatus))) {
                    $isActive = true;
                    break;
                }
            }
        }
        
        $active = $isActive ? 'active' : '';
        $collapsed = $isActive ? '' : 'collapsed';
        $expanded = $isActive ? 'true' : 'false';
        $show = $isActive ? 'show' : '';
        
        echo '<a class="nav-link section-title-sidebar '.$collapsed.' '.$active.'" href="#" data-toggle="collapse" data-target="#'.$id.'" aria-expanded="'.$expanded.'" aria-controls="'.$id.'">';
        echo '<div class="sb-nav-link-icon"><i class="'.$icon.'"></i></div>';
        echo $title . $badgeHtml . $counterHtml;
        echo '<div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>';
        echo '</a>';
        
        return $show;
    }
?>

<!-- MacBook tarzı arama modali -->
<div class="search-modal" id="searchModal">
    <div class="search-modal-content">
        <div class="search-modal-header">
            <div class="search-input-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" class="search-input" placeholder="Ne aramak istiyorsunuz?" autofocus>
                <button class="search-close-btn" onclick="closeSearchModal()"><i class="fas fa-times"></i></button>
            </div>
        </div>
        <div class="search-modal-body">
            <div class="search-results" id="searchResults">
                <!-- Arama sonuçları burada görüntülenecek -->
                <div class="search-categories" style="display: none;">
                    <div class="search-category" style="display: none;">
                        <h6>Ürünler</h6>
                        <div class="search-category-items" id="searchProductsResults">
                            <!-- Ürün sonuçları -->
                        </div>
                    </div>
                    <div class="search-category" style="display: none;">
                        <h6>Kullanıcılar</h6>
                        <div class="search-category-items" id="searchUsersResults">
                            <!-- Kullanıcı sonuçları -->
                        </div>
                    </div>
                    <div class="search-category" style="display: none;">
                        <h6>Sayfalar ve Menüler</h6>
                        <div class="search-category-items" id="searchPagesResults">
                            <!-- Sayfa sonuçları -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sidebar -->
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <!-- Logo ve Arama alanı -->
                <div class="sidebar-header mb-4 mt-3">
                    <div class="logo-container d-flex align-items-center px-3">
                        <div class="logo-icon mr-2">
                            <i class="fas fa-code-branch text-dark"></i>
                        </div>
                        <div class="logo-text font-weight-bold">Orius V2.1.9</div>
                    </div>
                    
                    <!-- Yeni sade arama alanı -->
                    <div class="simple-search mt-3 px-3">
                        <div class="search-box d-flex align-items-center p-2" onclick="openSearchModal()">
                            <i class="fas fa-search text-muted mr-2"></i>
                            <span class="search-placeholder text-muted">Ara...</span>
                            <div class="ml-auto">
                                <kbd>Ctrl</kbd><kbd>K</kbd>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar menü öğeleri -->
                <div class="nav">
                    <?php renderMenuItem('admin/dashboard', 'fas fa-tachometer-alt', 'Ana Sayfa', 'dashboard', $status, null); ?>
                    
                    <?php if (isPermFunction('seeSellHistory') == true): ?>
                        <?php renderMenuItem('admin/productHistory', 'fas fa-chart-line', 'Satış Geçmişi', 'sell-history', $status, null); ?>
                    <?php endif; ?>
                    
                    <!-- Ana menü ile kategoriler arasındaki ayırıcı -->
                    <div class="sidebar-divider">
                        <span class="sidebar-divider-text">Menü</span>
                    </div>
                    
                    <?php 
                    // Genel Bakış için bildirim sayısı hesapla
                    $genel_bakis_notification = $product_comments;
                    $genelBakisActive = renderSection('collapseGenelBakis', 'Genel Bakış', 'fas fa-eye', null, [
                        'store', 'notificationList', 'blog', 'pages'
                    ], $status, $genel_bakis_notification); 
                    ?>
                    <div class="collapse <?= $genelBakisActive ?>" id="collapseGenelBakis" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionGenelBakis">
                            <?php 
                            $total = $product_comments;
                            renderSubMenu('collapseMarket', 'fas fa-boxes', 'Mağaza', 'store', $status, null, $total); 
                            ?>
                            <div class="collapse" id="collapseMarket" aria-labelledby="headingOne" data-parent="#sidenavAccordionGenelBakis">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <?php if (isPermFunction('seeProduct') == true): ?>
                                        <?php renderSubMenuItem('admin/products', 'fas fa-box', 'Ürünler'); ?>
                                    <?php endif; ?>
                                    
                                    <?php if (isPermFunction('seeStocks') == true): ?>
                                        <?php renderSubMenuItem('admin/stock', 'fas fa-archive', 'Stok'); ?>
                                    <?php endif; ?>
                                    
                                    <?php if (isPermFunction('seeSettings') == true): ?>
                                        <?php renderSubMenuItem('admin/providers', 'fas fa-plug', 'Tedarikçi Yönetimi'); ?>
                                    <?php endif; ?>
                                    
                                    <?php if (isPermFunction('seeCoupons') == true): ?>
                                        <?php renderSubMenuItem('admin/coupons', 'fas fa-tags', 'Kuponlar'); ?>
                                    <?php endif; ?>
                                    
                                    <?php if (isPermFunction('seeCategory') == true): ?>
                                        <?php renderSubMenuItem('admin/category', 'fas fa-th-large', 'Kategoriler', 0); ?>
                                    <?php endif; ?>
                                    
                                    <?php if (isPermFunction('seeProduct') == true): ?>
                                        <?php renderSubMenuItem('admin/product/packages', 'fas fa-gift', 'Paketler', 0); ?>
                                    <?php endif; ?>
                                    
                                    <?php if (isPermFunction('seeProductComments') == true): ?>
                                        <?php renderSubMenuItem('admin/comments', 'fas fa-comment', 'Ürün Yorumları', $product_comments); ?>
                                    <?php endif; ?>
                                </nav>
                            </div>
                            
                            <?php if (isPermFunction('seeNotification') == true): ?>
                                <?php renderMenuItem('admin/Notification/notificationList', 'fa fa-bell', 'Bildirim Yönetimi', 'notificationList', $status); ?>
                            <?php endif; ?>
                            
                            <?php if (isPermFunction('seeBlogs') == true): ?>
                                <?php renderMenuItem('admin/blog', 'fa fa-rss', 'Blog', 'blog', $status); ?>
                            <?php endif; ?>
                            
                            <?php if (isPermFunction('seePages') == true): ?>
                                <?php renderMenuItem('admin/pages', 'fas fa-file-alt', 'Sayfalar', 'pages', $status); ?>
                            <?php endif; ?>
                        </nav>
                    </div>

                    <?php 
                    // Kullanıcı Yönetimi için bildirim sayısı hesapla
                    $kullanici_yonetimi_notification = $pending_tickets + $pending_applications + $total_credit_notifications;
                    $kullaniciYonetimiActive = renderSection('collapseKullaniciYonetimi', 'Kullanıcı Yönetimi', 'fas fa-users', null, [
                        'users', 'listSupports', 'listLogs', 'auth', 'subList', 'reference',
                        'dealerSettings', 'dealerUsers', 'productPricing', 'dealerUserHistory', 'dealerApplications',
                        'creditManagement', 'creditPayments'
                    ], $status, $kullanici_yonetimi_notification); 
                    ?>
                    <div class="collapse <?= $kullaniciYonetimiActive ?>" id="collapseKullaniciYonetimi" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionKullaniciYonetimi">
                            <?php if (isPermFunction('seeUsers') == true): ?>
                                <?php renderMenuItem('admin/users', 'fas fa-users', 'Üyeler', 'users', $status, null); ?>
                            <?php endif; ?>
                            
                            <?php if (isPermFunction('seeTickets') == true): ?>
                                <?php renderMenuItem('admin/listSupports', 'fas fa-ticket-alt', 'Destek Talepleri', 'listSupports', $status, null, $pending_tickets); ?>
                            <?php endif; ?>
                            
                            <?php if (isPermFunction('seeLogs') == true): ?>
                                <?php renderMenuItem('admin/listLogs', 'fas fa-history', 'Kayıt Geçmişi', 'listLogs', $status); ?>
                            <?php endif; ?>
                            
                            <?php if (isPermFunction('seeReferenceSettings') == true || isPermFunction('seeReferences') == true): ?>
                                <?php renderSubMenu('collapseRef', 'fas fa-link', 'Referans', 'reference', $status); ?>
                                <div class="collapse" id="collapseRef" aria-labelledby="headingOne" data-parent="#sidenavAccordionKullaniciYonetimi">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <?php if (isPermFunction('seeReferences') == true): ?>
                                            <?php renderSubMenuItem('admin/referenceList', 'fas fa-bars', 'Referans Listesi'); ?>
                                        <?php endif; ?>
                                        
                                        <?php if (isPermFunction('seeReferenceSettings') == true): ?>
                                            <?php renderSubMenuItem('admin/referenceSettings', 'fas fa-cog', 'Referans Ayarları'); ?>
                                        <?php endif; ?>
                                    </nav>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($user->role_id == 1): ?>
                                <?php renderSubMenu('collapseAuth', 'fas fa-link', 'Yetki Ayarları', 'auth', $status); ?>
                                <div class="collapse" id="collapseAuth" aria-labelledby="headingOne" data-parent="#sidenavAccordionKullaniciYonetimi">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <?php renderSubMenuItem('admin/authList', 'fas fa-bars', 'Yetkili Listesi'); ?>
                                        <?php renderSubMenuItem('admin/authSettings', 'fas fa-cog', 'Yetkili Ayarları'); ?>
                                    </nav>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Kredi Yönetimi Sekmesi -->
                            <?php if (isPermFunction('seeProduct') == true): ?>
                                <?php renderSubMenu('collapseCredit', 'fas fa-credit-card', 'Kredi Yönetimi', ['creditManagement', 'creditPayments'], $status, null, $total_credit_notifications); ?>
                                <div class="collapse" id="collapseCredit" aria-labelledby="headingOne" data-parent="#sidenavAccordionKullaniciYonetimi">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <?php renderSubMenuItem('admin/credit_management', 'fas fa-hand-holding-usd', 'Kredi Teklifleri', $pending_credit_offers); ?>
                                        <?php renderSubMenuItem('admin/credit_management/payments', 'fas fa-money-bill-wave', 'Kredi Ödemeleri'); ?>
                                        <?php renderSubMenuItem('admin/credit_management/check_overdue_credits', 'fas fa-exclamation-circle', 'Vadesi Geçen Krediler', $overdue_credits); ?>
                                    </nav>
                                </div>
                            <?php endif; ?>
                            <!-- /Kredi Yönetimi Sekmesi -->
                            
                            <!-- Bayilik Sistemi Sekmesi -->
                            <?php 
                            $dealer_active = ['dealerSettings', 'dealerUsers', 'productPricing', 'dealerUserHistory', 'dealerApplications'];
                            renderSubMenu('collapseDealer', 'fas fa-handshake', 'Bayilik Sistemi', $dealer_active, $status, null, $pending_applications); 
                            ?>
                            <div class="collapse" id="collapseDealer" aria-labelledby="headingOne" data-parent="#sidenavAccordionKullaniciYonetimi">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <?php renderSubMenuItem('admin/dealer', 'fas fa-layer-group', 'Bayilik Tipleri'); ?>
                                    <?php renderSubMenuItem('admin/dealer/dealerUsers', 'fas fa-users', 'Bayilik Kullanıcıları'); ?>
                                    <?php renderSubMenuItem('admin/dealer/applications', 'fas fa-clipboard-list', 'Bayilik Başvuruları', $pending_applications); ?>
                                    <?php renderSubMenuItem('admin/dealer/productPricing', 'fas fa-tag', 'Ürün Fiyatlandırma'); ?>
                                    <?php renderSubMenuItem('admin/dealer/settings', 'fas fa-cogs', 'Bayilik Ayarları'); ?>
                                </nav>
                            </div>
                            <!-- /Bayilik Sistemi Sekmesi -->
                            
                            <!-- Abonelik Sekmesi -->
                            <?php renderSubMenu('collapseSubs', 'fas fa-stream', 'Abonelik Yönetimi', 'subList', $status); ?>
                            <div class="collapse" id="collapseSubs" aria-labelledby="headingOne" data-parent="#sidenavAccordionKullaniciYonetimi">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <?php renderSubMenuItem('admin/subscription/subList', 'fas fa-bars', 'Abone Listesi'); ?>
                                    <?php renderSubMenuItem('admin/subscription/subSettings', 'fas fa-cog', 'Abonelik Ayarları'); ?>
                                </nav>
                            </div>
                            <!-- /Abonelik Sekmesi -->
                        </nav>
                    </div>

                    <!-- Finansman Sekmesi -->
                    <?php 
                    // Finansman için bildirim sayısı hesapla
                    $finansman_notification = $bank_transfer + $pending_requests;
                    $finansmanActive = renderSection('collapseFinansman', 'Finansman', 'fas fa-credit-card', null, ['invoiceList', 'request', 'bankTransfer'], $status, $finansman_notification); 
                    ?>
                    <div class="collapse <?= $finansmanActive ?>" id="collapseFinansman" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <?php if (isPermFunction('seePages') == true): ?>
                                <?php renderMenuItem('admin/finance/invoices', 'fas fa-file-alt', 'Fatura Listesi', 'invoiceList', $status); ?>
                            <?php endif; ?>
                            
                            <?php if (isPermFunction('seeRequests') == true): ?>
                                <?php renderMenuItem('admin/request', 'fas fa-coins', 'Çekim Talepleri', 'request', $status, null, $pending_requests); ?>
                            <?php endif; ?>
                            
                            <?php if (isPermFunction('seeTransfer') == true): ?>
                                <?php renderMenuItem('admin/bankTransfer', 'fas fa-money-check', 'Havale Bildirimi', 'bankTransfer', $status, null, $bank_transfer); ?>
                            <?php endif; ?>
                        </nav>
                    </div>
                    <!-- /Finansman Sekmesi -->
                    
                    <?php 
                    // Pazar Yeri için bildirim sayısı hesapla
                    $pazaryeri_notification = $pending_products + $pending_objections;
                    $pazaryeriActive = renderSection('collapsePazarYeri', 'Pazar Yeri', 'fas fa-store', null, ['userShops', 'pendingUserProductList', 'collapseObjection'], $status, $pazaryeri_notification); 
                    ?>
                    <div class="collapse <?= $pazaryeriActive ?>" id="collapsePazarYeri" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPazarYeri">
                            <?php if (isPermFunction('seeShops') == true): ?>
                                <?php renderMenuItem('admin/userShops', 'fas fa-store', 'Üye Mağazaları', 'userShops', $status); ?>
                            <?php endif; ?>
                            
                            <?php if (isPermFunction('seePendingProducts') == true): ?>
                                <?php renderMenuItem('admin/pendingUserProductList', 'fas fa-hourglass-start', 'Onay Bekleyen Ürünler', 'pendingUserProductList', $status, null, $pending_products); ?>
                            <?php endif; ?>
                            
                            <?php if (isPermFunction('seeObjections') == true): ?>
                                <?php renderSubMenu('collapseObjection', 'fas fa-exclamation-triangle', 'İtirazlar', 'collapseObjection', $status, null, $pending_objections); ?>
                                <div class="collapse" id="collapseObjection" aria-labelledby="headingOne" data-parent="#sidenavAccordionPazarYeri">
                                    <nav class="sb-sidenav-menu-nested nav">
                                        <?php renderSubMenuItem('admin/pendingProductObjectionList', 'fas fa-bars', 'Bekleyen İtirazlar', $pending_objections); ?>
                                        <?php renderSubMenuItem('admin/productObjectionList', 'fas fa-cog', 'Diğer İtirazlar'); ?>
                                    </nav>
                                </div>
                            <?php endif; ?>
                        </nav>
                    </div>
                    
                    <?php 
                    // Yayıncı için bildirim sayısını burada hesaplayabiliriz, örn: bekleyen yayıncılar gibi
                    $yayinci_notification = 0; // Şu an bildirim sayısı yok, gerekirse eklenebilir
                    $yayinciActive = renderSection('collapseYayinci', 'Yayıncı', 'fas fa-broadcast-tower', null, ['streamers', 'streamersPending', 'streamersDonations'], $status, $yayinci_notification); 
                    ?>
                    <div class="collapse <?= $yayinciActive ?>" id="collapseYayinci" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <?php renderMenuItem('admin/streamers', 'fas fa-users', 'Yayıncılar', 'streamers', $status); ?>
                            <?php renderMenuItem('admin/pendingStreamerList', 'fas fa-hourglass-start', 'Onay Bekleyen Yayıncılar', 'streamersPending', $status); ?>
                            <?php renderMenuItem('admin/donations', 'fas fa-donate', 'Bağışlar', 'streamersDonations', $status); ?>
                        </nav>
                    </div>
                    <?php 
                    // Çekiliş için bildirim sayısı
                    $cekilisler_notification = 0; // Şu an bildirim sayısı yok, gerekirse eklenebilir
                    $cekilislerActive = renderSection('collapseCekilisler', 'Çekilişler', 'fas fa-gift', null, ['draws'], $status, $cekilisler_notification); 
                    ?>
                    <div class="collapse <?= $cekilislerActive ?>" id="collapseCekilisler" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <?php renderMenuItem('admin/draw/index', 'fas fa-list', 'Çekiliş Listesi', 'draws', $status); ?>
                            <?php renderMenuItem('admin/draw/add', 'fas fa-plus', 'Yeni Çekiliş Ekle', 'draws', $status); ?>
                            <?php renderMenuItem('admin/draw/deliveries', 'fas fa-truck', 'Teslimatlar', 'draws', $status); ?>
                        </nav>
                    </div>

                    <?php 
                    // Site Ayarları için bildirim sayısı
                    $site_ayarlari_notification = 0; // Şu an bildirim sayısı yok, gerekirse eklenebilir
                    $siteAyarlariActive = renderSection('collapseSiteAyarlari', 'Site Ayarları', 'fas fa-cogs', null, ['themeSettings', 'publicSettings', 'apiSettings', 'mailTemplates', 'mailLogs'], $status, $site_ayarlari_notification); 
                    ?>
                    <div class="collapse <?= $siteAyarlariActive ?>" id="collapseSiteAyarlari" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            <!-- Mail Ayarları  -->
                            <?php renderSubMenu('collapseMailSettings', 'fas fa-envelope', 'Mail Ayarları', ['mailTemplates', 'mailLogs'], $status, null); ?>
                            <div class="collapse" id="collapseMailSettings" aria-labelledby="headingOne" data-parent="#collapseSiteAyarlari">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <?php if (isPermFunction('seePages') == true): ?>
                                        <?php renderSubMenuItem('admin/mail/templates', 'fas fa-file-alt', 'Şablonlar'); ?>
                                    <?php endif; ?>
                                    
                                    <?php if (isPermFunction('seeSellHistory') == true): ?>
                                        <?php renderSubMenuItem('admin/mail/logs', 'fas fa-history', 'Geçmiş Gönderimler'); ?>
                                    <?php endif; ?>
                                </nav>
                            </div>
                            <!-- /Mail Ayarları -->
                            
                            <?php if (isPermFunction('seeThemeSettings') == true): ?>
                                <?php renderMenuItem('admin/themeSettings', 'fas fa-sitemap', 'Tema', 'themeSettings', $status); ?>
                            <?php endif; ?>
                            
                            <?php if (isPermFunction('seeSettings') == true): ?>
                                <?php renderMenuItem('admin/publicSettings', 'fas fa-bars', 'Genel Ayarlar', 'publicSettings', $status, null); ?>
                            <?php endif; ?>
                            
                            <?php if (isPermFunction('seeSettings') == true): ?>
                                <?php renderMenuItem('admin/apiSettings', 'fas fa-bars', 'API Ayarları', 'apiSettings', $status); ?>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Footer - Kullanıcı Bilgileri ve Marka Alanı -->
            <div class="sb-sidenav-footer">
                <div class="d-flex align-items-center">
                    <div class="user-avatar mr-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 14px;">
                            <?= substr($user->name, 0, 2) . substr($user->surname, 0, 1) ?>
                        </div>
                    </div>
                    <div class="user-info">
                        <div class="small font-weight-bold"><?= $user->name ?></div>
                        <div class="small text-muted"><?= $user->email ?></div>
                    </div>
                </div>
                <div class="mt-2 d-flex justify-content-center">
                    <a href="<?= base_url('login/logout') ?>" class="btn-logout">
                        <i class="fas fa-sign-out-alt mr-1"></i>
                        <span>Çıkış Yap</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>

<style>
.sb-sidenav-footer {
    background-color: #f8f9fa;
    padding: 0.8rem;
    border-top: 1px solid #dee2e6;
}
.user-info {
    overflow: hidden;
    flex: 1;
}
.user-info .small {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btn-logout {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 6px;
    color: #6c757d;
    font-size: 0.85rem;
    text-decoration: none;
    transition: all 0.2s ease;
    background-color: #f0f2f5;
    border: 1px solid transparent;
}

.btn-logout:hover {
    background-color: #dc3545;
    color: white;
    text-decoration: none;
}

.btn-logout:hover i {
    transform: translateX(2px);
}

.btn-logout i {
    transition: transform 0.2s ease;
}

/* Sidebar ayırıcı stili */
.sidebar-divider {
    position: relative;
    height: 1px;
    margin: 16px 0;
    background: linear-gradient(90deg, rgba(0,0,0,0.03) 0%, rgba(0,0,0,0.1) 50%, rgba(0,0,0,0.03) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-divider-text {
    position: absolute;
    background-color: #f8f9fa;
    padding: 0 10px;
    font-size: 11px;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 1px;
    font-weight: 600;
}

/* Sidebar'ı scrollable yapan stiller */
#layoutSidenav_nav {
    height: 100vh;
    position: sticky;
    top: 0;
}
.sb-sidenav {
    height: 100%;
    display: flex;
    flex-direction: column;
}
.sb-sidenav-menu {
    flex-grow: 1;
    overflow-y: auto;
}
</style>
