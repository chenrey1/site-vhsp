<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5>Ayarlar</h5>
            </div>

            <div class="card">
                <div class="card-header card-header-nav">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#site_choices" role="tab" aria-selected="true">Site Seçimleri</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#site" role="tab" aria-selected="false">Site Ayarları</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#banka" role="tab" aria-selected="false">Banka Hesapları</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#smtp" role="tab" aria-selected="false">SMTP Ayarları</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#odeme" role="tab" aria-selected="false">Ödeme Ayarları</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#admin" role="tab" aria-selected="false">Hesap Ayarları</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#api" role="tab" aria-selected="false">Google / Canlı Destek</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#user_shops" role="tab" aria-selected="false">Üye Mağazası Ayarları</a>
                        </li>
                        <!--<li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#upload" role="tab" aria-selected="false"><span class="badge badge-info">Yakında</span> Modül Yükleme Alanı</a>
                        </li>-->
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">

                        <div class="tab-pane fade" id="site" role="tabpanel">
                            <div class="row">
                                <div class="col-12 col-lg-7">
                                    <form action="<?= base_url('admin/product/edit/publicSettings/properties/1/site') ?>" method="POST" enctype="multipart/form-data">
                                        <div class="form-group">
                                            <label for="inputSLogo">Logo</label>
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="customRadio1" name="choose" class="custom-control-input" value="1" <?php if($properties->choose == 1) {echo "checked";} ?>>
                                                        <label class="custom-control-label" for="customRadio1">Yazı</label>
                                                    </div>
                                                </div>
                                                <div class="col-9">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" id="customRadio2" name="choose" class="custom-control-input" value="0" <?php if($properties->choose == 0) {echo "checked";} ?>>
                                                        <label class="custom-control-label" for="customRadio2">Görsel</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="nameofsite">Site Adı <small class="text-muted">Eğer logo yüklediysen logo kısmında görünmez</small></label>
                                            <input type="text" class="form-control" id="nameofsite" value="<?= $properties->name ?>" name="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="Title">META Başlık (Title)</label>
                                            <input type="text" class="form-control" id="Title" value="<?= $properties->title ?>" name="title" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="Description">META Açıklama (Description)</label>
                                            <input type="text" class="form-control" id="Description" value="<?= $properties->description ?>" name="description" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputSPhone">Telefon Numarası</label>
                                            <input type="text" class="form-control" id="inputSPhone" value="<?= $properties->contact ?>" name="contact" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputSPhone">E-Mail</label>
                                            <input type="text" class="form-control" id="inputSPhone" value="<?= $properties->email ?>" name="email" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputSPCommission">Ödeme Komisyonu Oranı (%)</label>
                                            <input type="number" class="form-control" id="inputSPCommission" value="<?= $properties->commission ?>" name="commission" step="any" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputSFace">Facebook</label>
                                            <input type="text" class="form-control" id="inputSFace" value="<?= $properties->facebook ?>" name="facebook">
                                        </div>
                                        <div class="form-group">
                                            <label for="inputSTw">Twitter</label>
                                            <input type="text" class="form-control" id="inputSTw" value="<?= $properties->twitter ?>" name="twitter">
                                        </div>
                                        <div class="form-group">
                                            <label for="inputSInsta">Instagram</label>
                                            <input type="text" class="form-control" id="inputSInsta" value="<?= $properties->instagram ?>" name="instagram">
                                        </div>
                                        <div class="form-group">
                                            <label for="inputSAddress">Adres</label>
                                            <textarea rows="5" class="form-control" id="inputSAddress" name="address"><?= $properties->address ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputSContract">Sözleşme</label>
                                            <textarea rows="5" class="form-control" id="inputSContract" name="contract"><?= $properties->contract ?></textarea>
                                        </div>
                                </div>
                                <div class="col-12 col-lg-5 border-left">
                                    <img src="<?= base_url('assets/img/site/') . $properties->img ?>" width="280px" max-height="80px" class="mb-2 mx-auto d-block">
                                    <small class="text-muted mb-2 d-block">Yandaki kısımdan hangisinin görünmek istediğini seçebilirsin.</small>
                                    <div class="form-group">
                                        <label>Görsel</label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFileLangHTML" name="img">
                                            <label class="custom-file-label" for="customFileLangHTML" data-browse="Seç">Logoyu Değiştirmek İçin Tıkla</label>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <b><label for="inputSLogo">Abonelik Sistemi<span class="badge badge-info ml-2">Yeni</span></label></b>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="subscriptionradio" name="isSubscription" class="custom-control-input" value="1" <?php if($properties->isSubscription == 1) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="subscriptionradio">Abonelik Sistemi Açık</label>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="subscriptionradio2" name="isSubscription" class="custom-control-input" value="0" <?php if($properties->isSubscription == 0) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="subscriptionradio2">Abonelik Sistemi Kapalı</label>
                                                </div>
                                            </div>
                                        </div>
                                        <small>Uyarı: Abonelik sistemini kapattığınızda mevcut aboneleriniz günleri bitene kadar avantajlarını kullanmaya devam eder. Ancak yenileme yapamazlar.</small>
                                    </div>
                                    <div class="form-group">
                                        <b><label for="inputSLogo">Stok Bildirimi (Ürün stoğu 3 altına düşerse mail ile bilgilendir)</label></b>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio3" name="stock" class="custom-control-input" value="1" <?php if($properties->stock == 1) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio3">Bildirim Açık</label>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio4" name="stock" class="custom-control-input" value="0" <?php if($properties->stock == 0) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio4">Bildirim Kapalı</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <b><label for="inputSLogo">Yapay Zekaya Bağlı Ürün Listeleme</label></b>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio5" name="autoShow" class="custom-control-input" value="1" <?php if($properties->autoShow == 1) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio5">Akıllı Listeleme Açık</label>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio6" name="autoShow" class="custom-control-input" value="0" <?php if($properties->autoShow == 0) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio6">Akıllı Listeleme Kapalı</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <b><label for="inputSLogo">Stoksuz Ürün Satışı (Eğer aktif ederseniz stok olmasa bile satış yapılır)</label></b>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio7" name="isStock" class="custom-control-input" value="1" <?php if($properties->isStock == 1) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio7">Stoksuz Ürün Satışı Açık</label>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio8" name="isStock" class="custom-control-input" value="0" <?php if($properties->isStock == 0) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio8">Stoksuz Ürün Satışı Kapalı</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <b><label for="inputSLogo">API Teslimatı (Eğer aktif ederseniz stok yok ise API'den satın alır)</label></b>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio9" name="autoGive" class="custom-control-input" value="1" <?php if($properties->autoGive == 1) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio9">API Teslimatı Açık</label>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio10" name="autoGive" class="custom-control-input" value="0" <?php if($properties->autoGive == 0) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio10">API Teslimatı Kapalı</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <b><label for="inputSLogo">Misafir Girişi</label></b>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio11" name="isGuest" class="custom-control-input" value="1" <?php if($properties->isGuest == 1) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio11">Misafir Girişi Açık</label>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio12" name="isGuest" class="custom-control-input" value="0" <?php if($properties->isGuest == 0) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio12">Misafir Girişi Kapalı</label>
                                                </div>
                                            </div>
                                            <small>Uyarı: Bu seçeneği aktif ederseniz SMTP mail aktif olmalıdır. Aksi taktirde teslimat yapılamaz.</small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <b><label for="inputSLogo">İlk Kayıtta Mail Onayı</label></b>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio13" name="isConfirmMail" class="custom-control-input" value="1" <?php if($properties->isConfirmMail == 1) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio13">Mail Onayı Açık</label>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio14" name="isConfirmMail" class="custom-control-input" value="0" <?php if($properties->isConfirmMail == 0) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio14">Mail Onayı Kapalı</label>
                                                </div>
                                            </div>
                                            <small>Uyarı: Bu seçeneği aktif ederseniz SMTP mail aktif olmalıdır. Aksi taktirde üyelik onaylanamaz.</small>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <b><label for="inputSLogo">Kullanıcıdan TC Alma</label></b>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio15" name="isConfirmTc" class="custom-control-input" value="1" <?php if($properties->isConfirmTc == 1) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio15">TC Onayı Açık</label>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio16" name="isConfirmTc" class="custom-control-input" value="0" <?php if($properties->isConfirmTc == 0) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio16">TC Onayı Kapalı</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <b><label for="inputSLogo">Pazar Yeri</label></b>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio17" name="shop_active" class="custom-control-input" value="1" <?php if($properties->shop_active == 1) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio17">Pazar Yeri Açık</label>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio18" name="shop_active" class="custom-control-input" value="0" <?php if($properties->shop_active == 0) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio18">Pazar Yeri Kapalı</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <b><label for="inputSAPI">Dış API (Kendi Sitenizden API Verin)</label></b>
                                        <div class="row">
                                            <div class="col-4">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio19" name="api_is_active" class="custom-control-input" value="1" <?php if($properties->api_is_active == 1) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio19">Dış API Açık</label>
                                                </div>
                                            </div>
                                            <div class="col-8">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" id="customRadio20" name="api_is_active" class="custom-control-input" value="0" <?php if($properties->api_is_active == 0) {echo "checked";} ?>>
                                                    <label class="custom-control-label" for="customRadio20">Dış API Kapalı</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center"><button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Kaydet</button></div>
                            </form>
                            <hr>
                            <form action="<?= base_url('admin/product/changeFavicon') ?>" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label>Favicon</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="customFileLangHTML" name="img">
                                        <label class="custom-file-label" for="customFileLangHTML" data-browse="Seç">Faviconu Değiştirmek İçin Tıkla</label>
                                    </div>
                                </div>
                                <div class="text-left"><button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Kaydet</button></div>
                            </form>
                        </div>

                        <div class="tab-pane fade show active" id="site_choices" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <div class="input-group">
                                        <input type="text" id="menuSearch" class="form-control" placeholder="Menüde ara...">
                                        <div class="input-group-append">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <h6 class="text-primary font-weight-bold">Mağaza Yönetimi</h6>
                                </div>
                                
                                <div class="col-md-3 col-sm-6 mb-3 menu-item">
                                    <div class="card h-100 border-0 shadow-sm hoverable">
                                        <div class="card-body p-3 text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-wallet text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-1">Bakiye Modülü</h6>
                                            <p class="card-text small text-muted mb-2">Kullanıcı bakiye ayarlarını ve transferlerini yönetin</p>
                                            <a href="<?= base_url('admin/settings/balance') ?>" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 col-sm-6 mb-3 menu-item">
                                    <div class="card h-100 border-0 shadow-sm hoverable">
                                        <div class="card-body p-3 text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-store text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-1">Bayilik Ayarları</h6>
                                            <p class="card-text small text-muted mb-2">Bayilik ayarlarınızı düzenleyin</p>
                                            <a href="<?= base_url('admin/dealer/settings') ?>" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 col-sm-6 mb-3 menu-item">
                                    <div class="card h-100 border-0 shadow-sm hoverable">
                                        <div class="card-body p-3 text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-users text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-1">Yetki Yönetimi</h6>
                                            <p class="card-text small text-muted mb-2">Yetkili ve yetki listesini yönetin</p>
                                            <a href="<?= base_url('admin/authSettings') ?>" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                                
                                <!--
                                <div class="col-md-3 col-sm-6 mb-3 menu-item">
                                    <div class="card h-100 border-0 shadow-sm hoverable">
                                        <div class="card-body p-3 text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-box text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-1">Stok Yönetimi</h6>
                                            <p class="card-text small text-muted mb-2">Stok takibi ve stok bildirimleri ayarları</p>
                                            <a href="<?= base_url('admin/stockManagement') ?>" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                                -->
                                
                                
                                <!--<div class="col-12 mt-2 mb-2">
                                    <h6 class="text-primary font-weight-bold">Sistem Ayarları</h6>
                                </div>-->
                                
                                
                                <!--
                                <div class="col-md-3 col-sm-6 mb-3 menu-item">
                                    <div class="card h-100 border-0 shadow-sm hoverable">
                                        <div class="card-body p-3 text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-search text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-1">Ürün Arama</h6>
                                            <p class="card-text small text-muted mb-2">Arama motoru ve filtreleme ayarlarını yönetin</p>
                                            <a href="<?= base_url('admin/searchSettings') ?>" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                                -->
                                <!--
                                <div class="col-md-3 col-sm-6 mb-3 menu-item">
                                    <div class="card h-100 border-0 shadow-sm hoverable">
                                        <div class="card-body p-3 text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-tag text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-1">Sipariş Etiketleri</h6>
                                            <p class="card-text small text-muted mb-2">Kargo ve ürün etiketleri tasarımı</p>
                                            <a href="<?= base_url('admin/labelSettings') ?>" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                                -->
                                <!--
                                <div class="col-md-3 col-sm-6 mb-3 menu-item">
                                    <div class="card h-100 border-0 shadow-sm hoverable">
                                        <div class="card-body p-3 text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-lock text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-1">Güvenlik</h6>
                                            <p class="card-text small text-muted mb-2">Şifre politikaları ve erişim güvenliği</p>
                                            <a href="<?= base_url('admin/publicSettings#site') ?>" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                                -->
                                
                                
                                <div class="col-12 mt-2 mb-2">
                                    <h6 class="text-primary font-weight-bold">İletişim ve Entegrasyonlar</h6>
                                </div>
                                
                                <div class="col-md-3 col-sm-6 mb-3 menu-item">
                                    <div class="card h-100 border-0 shadow-sm hoverable">
                                        <div class="card-body p-3 text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-envelope text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-1">E-posta Ayarları</h6>
                                            <p class="card-text small text-muted mb-2">E-posta şablonları ve SMTP ayarları</p>
                                            <a href="<?= base_url('admin/mail/templates') ?>" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                                
                                <!--
                                <div class="col-md-3 col-sm-6 mb-3 menu-item">
                                    <div class="card h-100 border-0 shadow-sm hoverable">
                                        <div class="card-body p-3 text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-sms text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-1">SMS Ayarları</h6>
                                            <p class="card-text small text-muted mb-2">SMS gönderimi ve şablon yönetimi</p>
                                            <a href="<?= base_url('admin/smsSettings') ?>" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                                -->
                                <!--
                                <div class="col-md-3 col-sm-6 mb-3 menu-item">
                                    <div class="card h-100 border-0 shadow-sm hoverable">
                                        <div class="card-body p-3 text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-money-bill-wave text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-1">Ödeme Ayarları</h6>
                                            <p class="card-text small text-muted mb-2">Ödeme yöntemleri ve komisyon oranları</p>
                                            <a href="<?= base_url('admin/paymentSettings') ?>" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                                -->
                                
                                <div class="col-md-3 col-sm-6 mb-3 menu-item">
                                    <div class="card h-100 border-0 shadow-sm hoverable">
                                        <div class="card-body p-3 text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-cogs text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-1">API Ayarları</h6>
                                            <p class="card-text small text-muted mb-2">Harici servis entegrasyonları</p>
                                            <a href="<?= base_url('admin/apiSettings') ?>" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 col-sm-6 mb-3 menu-item">
                                    <div class="card h-100 border-0 shadow-sm hoverable">
                                        <div class="card-body p-3 text-center">
                                            <div class="icon-wrapper mb-2">
                                                <i class="fas fa-credit-card text-primary"></i>
                                            </div>
                                            <h6 class="card-title mb-1">Ödeme Ayarları</h6>
                                            <p class="card-text small text-muted mb-2">Ödeme yöntemleri ve komisyon oranları</p>
                                            <a href="<?= base_url('admin/settings/payment') ?>" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <style>
                                .hoverable {
                                    transition: all 0.3s;
                                }
                                .hoverable:hover {
                                    transform: translateY(-5px);
                                    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
                                }
                                .icon-wrapper {
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    width: 40px;
                                    height: 40px;
                                    border-radius: 50%;
                                    background-color: rgba(0, 123, 255, 0.1);
                                    margin-bottom: 10px;
                                }
                                .icon-wrapper i {
                                    font-size: 18px;
                                }
                                .card-title {
                                    font-size: 14px;
                                    font-weight: 600;
                                }
                                .card-text {
                                    font-size: 12px;
                                    line-height: 1.4;
                                }
                            </style>
                            
                            <script>
                                $(document).ready(function() {
                                    $("#menuSearch").on("keyup", function() {
                                        var value = $(this).val().toLowerCase();
                                        $(".menu-item").filter(function() {
                                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                                        });
                                    });
                                });
                            </script>
                        </div>

                        <div class="tab-pane fade" id="banka" role="tabpanel">
                            <button type="submit" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#modalIban"><i class="fa fa-plus"></i></button>
                            <div class="table-responsive">
                                <table class="table border table-why">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>Banka</th>
                                        <th>Alıcı</th>
                                        <th>IBAN</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($banks as $b) {?>
                                        <tr>
                                            <td><?= $b->bank_name ?></td>
                                            <td><?= $b->owner_name ?></td>
                                            <td><?= $b->iban ?></td>
                                            <td><a href="<?= base_url('admin/product/deleteBank/') . $b->id ?>" class="text-danger"><i class="fa fa-trash-alt"></i></a></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="smtp" role="tabpanel">
                            <form action="<?= base_url('admin/product/edit/publicSettings/smtp/1') ?>" method="POST">
                                <div class="form-group">
                                    <label for="inputExample1">Host</label>
                                    <input type="text" class="form-control" value="<?= $smtp->host ?>" id="host" name="host">
                                </div>
                                <div class="form-group">
                                    <label for="inputExample2">Port</label>
                                    <input type="text" class="form-control" value="<?= $smtp->port ?>" id="port" name="port">
                                </div>
                                <div class="form-group">
                                    <label for="inputExample3">Mail Adresi</label>
                                    <input type="text" class="form-control" value="<?= $smtp->mail ?>" id="mail" name="mail">
                                </div>
                                <div class="form-group">
                                    <label for="inputExample4">Mail Şifresi</label>
                                    <input type="text" class="form-control" value="<?= $smtp->password ?>" id="mailpassword" name="password">
                                </div>
                                <button type="submit" class="btn btn-primary float-right"><i class="far fa-save"></i> Kaydet</button>
                            </form>
                            <button class="btn btn-success" id="testSMTP"><i class="fas fa-cubes"></i> SMTP'yi Sına</button> <span class="text-info ml-3" id="smtpinfo"></span>
                        </div>
                        
                        <div class="tab-pane fade" id="odeme" role="tabpanel">
                        </div>

                        <div class="tab-pane fade" id="admin" role="tabpanel">
                            <div class="row">
                                <div class="col-12 col-lg-6">
                                    <form action="<?= base_url('admin/product/changeMail') ?>" method="POST">
                                        <div class="form-group">
                                            <label for="inputAdminMail">Yönetici E-Posta Adresi</label>
                                            <input type="email" class="form-control" placeholder="Şu an kullandığınız mail adresi" required name="email">
                                        </div>
                                        <div class="form-group">
                                            <label for="inputAdminMail">Yeni E-Posta Adresi</label>
                                            <input type="email" class="form-control" placeholder="Kullanmak istediğiniz mail adresi" required name="newmail">
                                        </div>
                                        <button type="submit" class="btn btn-primary float-right"><i class="far fa-save"></i> Kaydet</button>
                                    </form>
                                </div>
                                <div class="col-12 col-lg-6 border-left">
                                    <form action="<?= base_url('admin/product/changePassword') ?>" method="POST">
                                        <div class="form-group">
                                            <label for="inputAdminMail">Eski Şifre</label>
                                            <input type="password" class="form-control" placeholder="Şuanda kullanılan şifreyi yazın" name="password" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputAdminMail">Yeni Şifre</label>
                                            <input type="password" class="form-control" placeholder="Yeni Şifrenizi Yazın" required name="newPassword">
                                        </div>
                                        <button type="submit" class="btn btn-primary float-right"><i class="far fa-save"></i> Kaydet</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="api" role="tabpanel">
                            <form action="<?= base_url('admin/product/updateApi') ?>" method="POST">
                                <div class="form-group">
                                    <label for="inputSAnalytics">Google Analytics Kodu</label>
                                    <textarea rows="4" class="form-control" id="inputSAnalytics" name="google_analytics"><?= $properties->google_analytics ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="inputSupport">Canlı Destek Kodu</label>
                                    <textarea rows="5" class="form-control" id="inputSupport" name="online_support"><?= $properties->online_support ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary float-right"><i class="far fa-save"></i> Kaydet</button>
                            </form>
                        </div>


                        <div class="tab-pane fade" id="user_shops" role="tabpanel">
                            <form action="<?= base_url('admin/product/edit/publicSettings/properties/1') ?>" method="POST">

                                <div class="form-group">
                                    <label for="shop_commission">Mağaza Komisyonu (%)</label>
                                    <input type="number" class="form-control" value="<?= $properties->shop_commission ?>" name="shop_commission" min="0"  step="0.01" id="shop_commission" required="">
                                </div>
                                <div class="form-group">
                                    <label for="shop_commission">En Düşük Çekim (TL)</label>
                                    <input type="number" class="form-control" value="<?= $properties->min_draw ?>" name="min_draw" min="0"  step="0.01" id="min_draw" required="">
                                </div>
                                <button type="submit" class="btn btn-primary float-right"><i class="far fa-save"></i> Kaydet</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </main>

    <div class="modal fade" id="modalIban" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Banka Hesabı Ekle</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('admin/product/insert/publicSettings/banks') ?>" method="POST">
                        <div class="form-group">
                            <label for="inputHB">Banka</label>
                            <input type="text" class="form-control form-control-sm" id="inputHB" name="bank_name" required>
                        </div>
                        <div class="form-group">
                            <label for="inputHA">Alıcı</label>
                            <input type="text" class="form-control form-control-sm" id="inputHA" name="owner_name" required>
                        </div>
                        <div class="form-group">
                            <label for="inputIB">IBAN veya Barkod</label>
                            <input type="text" class="form-control form-control-sm" id="inputIB" name="iban" required>
                        </div>
                        <div class="float-right">
                            <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Ekle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('#testSMTP').click(function(){
                $('#smtpinfo').html('SMTP Sınanıyor...');
                $.ajax({
                    url: '<?= base_url('admin/product/sendTestMail') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        host: $('#host').val(),
                        port: $('#port').val(),
                        mail: $('#mail').val(),
                        password: $('#mailpassword').val()
                    },
                    success: function(response){
                        if(response.status === 'success'){
                            $('#smtpinfo').html('SMTP Testi Başarılı. Girdiğiniz bilgileri Kaydet butonu ile kaydetmeyi unutmayın.');
                        }else{
                            $('#smtpinfo').html('SMTP Testi Başarısız: ' + response.message);
                            console.log('Debug bilgisi:', response.debug); // Hata ayıklama için
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#smtpinfo').html('SMTP Testi sırasında bir hata oluştu: ' + error);
                        console.log('XHR:', xhr.responseText); // Hata ayıklama için
                    }
                });
            });
        });
    </script>