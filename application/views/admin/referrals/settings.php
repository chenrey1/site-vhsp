<div id="layoutSidenav_content" class="referrals-page">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Referans Sistemi Ayarları</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                                         <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Yönetim</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Referans Ayarları</li>
                </ol>
            </nav>

            <!-- Alert Messages -->
            <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $this->session->flashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>
            
            <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $this->session->flashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <div class="row">
                <!-- Ana Ayarlar -->
                <div class="col-lg-8">
                    <!-- Genel Ayarlar -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-users mr-1"></i>
                            Referans Sistemi Genel Ayarları
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('admin/referrals/update_referral_settings') ?>" method="POST">
                                <!-- CSRF Token -->
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox" class="custom-control-input" id="referral_system_enabled" name="settings[referral_system_enabled]" value="1" <?= isset($settings['referral_system_enabled']) && $settings['referral_system_enabled']->value == 1 ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="referral_system_enabled">Referans Sistemi Aktif</label>
                                                <small class="form-text text-muted">Referans sistemini etkinleştirmek veya devre dışı bırakmak için kullanın.</small>
                                            </div>
                                        </div>
                                    </div>

                                                                         <!-- Kayıt Bonusu Ayarları -->
                                     <div class="col-lg-12">
                                         <h6 class="text-primary mb-3"><i class="fas fa-user-plus mr-1"></i> Kayıt Bonusu Ayarları</h6>
                                     </div>
                                     
                                     <div class="col-lg-6">
                                         <div class="form-group">
                                             <label for="referral_register_bonus_fixed">Kayıt Bonusu (TL)</label>
                                             <input type="number" class="form-control" id="referral_register_bonus_fixed" name="settings[referral_register_bonus_fixed]" value="<?= isset($settings['referral_register_bonus_fixed']) ? $settings['referral_register_bonus_fixed']->value : '5.00' ?>" min="0" step="0.01">
                                             <small class="form-text text-muted">Referans veren kullanıcının her yeni kayıttan alacağı sabit bonus tutarı.</small>
                                         </div>
                                     </div>

                                     <div class="col-lg-6">
                                         <div class="form-group">
                                             <div class="custom-control custom-switch mt-4">
                                                 <input type="checkbox" class="custom-control-input" id="referral_require_purchase" name="settings[referral_require_purchase]" value="1" <?= isset($settings['referral_require_purchase']) && $settings['referral_require_purchase']->value == 1 ? 'checked' : '' ?>>
                                                 <label class="custom-control-label" for="referral_require_purchase">Kayıt Bonusu İçin Alışveriş Gerekli</label>
                                                 <small class="form-text text-muted d-block mt-1">Kayıt bonusu alabilmek için referansın alışveriş yapması zorunlu olsun.</small>
                                             </div>
                                         </div>
                                     </div>

                                     <!-- Alışveriş Bonusu Ayarları -->
                                     <div class="col-lg-12">
                                         <hr class="my-4">
                                         <h6 class="text-success mb-3"><i class="fas fa-shopping-cart mr-1"></i> Alışveriş Bonusu Ayarları</h6>
                                     </div>

                                     <div class="col-lg-6">
                                         <div class="form-group">
                                             <label for="referral_purchase_bonus_rate">Alışveriş Bonus Oranı (%)</label>
                                             <input type="number" class="form-control" id="referral_purchase_bonus_rate" name="settings[referral_purchase_bonus_rate]" value="<?= isset($settings['referral_purchase_bonus_rate']) ? $settings['referral_purchase_bonus_rate']->value : '5.00' ?>" min="0" max="100" step="0.01">
                                             <small class="form-text text-muted">Referans veren kullanıcının referansının alışverişinden alacağı bonus yüzdesi.</small>
                                         </div>
                                     </div>

                                     <div class="col-lg-6">
                                         <div class="form-group">
                                             <label for="referral_min_purchase_amount">Minimum Alışveriş Tutarı (TL)</label>
                                             <input type="number" class="form-control" id="referral_min_purchase_amount" name="settings[referral_min_purchase_amount]" value="<?= isset($settings['referral_min_purchase_amount']) ? $settings['referral_min_purchase_amount']->value : '10.00' ?>" min="0" step="0.01">
                                             <small class="form-text text-muted">Bonus alabilmek için gereken minimum alışveriş tutarı.</small>
                                         </div>
                                     </div>

                                     <!-- Genel Ayarlar -->
                                     <div class="col-lg-12">
                                         <hr class="my-4">
                                         <h6 class="text-info mb-3"><i class="fas fa-cog mr-1"></i> Genel Ayarlar</h6>
                                     </div>

                                     <div class="col-lg-12">
                                         <div class="form-group">
                                             <label for="referral_bonus_balance_type">Bonus Bakiye Türü</label>
                                             <select class="form-control" id="referral_bonus_balance_type" name="settings[referral_bonus_balance_type]">
                                                 <option value="spendable" <?= isset($settings['referral_bonus_balance_type']) && $settings['referral_bonus_balance_type']->value == 'spendable' ? 'selected' : '' ?>>Kullanılabilir Bakiye</option>
                                                 <option value="withdrawable" <?= isset($settings['referral_bonus_balance_type']) && $settings['referral_bonus_balance_type']->value == 'withdrawable' ? 'selected' : '' ?>>Çekilebilir Bakiye</option>
                                             </select>
                                             <small class="form-text text-muted">Referans bonuslarının hangi bakiye türüne ekleneceğini belirler.</small>
                                         </div>
                                     </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-block mt-3">
                                    <i class="fas fa-save mr-1"></i> Genel Ayarları Kaydet
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Limit Ayarları -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Bonus Limitleri ve Güvenlik
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('admin/referrals/update_referral_limits') ?>" method="POST">
                                <!-- CSRF Token -->
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                                
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="referral_max_bonus_per_transaction">İşlem Başına Max Bonus (TL)</label>
                                            <input type="number" class="form-control" id="referral_max_bonus_per_transaction" name="settings[referral_max_bonus_per_transaction]" value="<?= isset($settings['referral_max_bonus_per_transaction']) ? $settings['referral_max_bonus_per_transaction']->value : '50.00' ?>" min="0" step="0.01">
                                            <small class="form-text text-muted">Tek bir işlemden alınabilecek maksimum bonus tutarı.</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="referral_max_bonus_per_month">Aylık Max Bonus (TL)</label>
                                            <input type="number" class="form-control" id="referral_max_bonus_per_month" name="settings[referral_max_bonus_per_month]" value="<?= isset($settings['referral_max_bonus_per_month']) ? $settings['referral_max_bonus_per_month']->value : '500.00' ?>" min="0" step="0.01">
                                            <small class="form-text text-muted">Bir kullanıcının ayda alabileceği maksimum toplam bonus.</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="max_referrer_changes">Max Referans Değişiklik Sayısı</label>
                                            <input type="number" class="form-control" id="max_referrer_changes" name="settings[max_referrer_changes]" value="<?= isset($settings['max_referrer_changes']) ? $settings['max_referrer_changes']->value : '3' ?>" min="0">
                                            <small class="form-text text-muted">Bir kullanıcının kaç kez referansını değiştirebileceği.</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="referrer_change_cooldown_days">Referans Değişim Bekleme Süresi (Gün)</label>
                                            <input type="number" class="form-control" id="referrer_change_cooldown_days" name="settings[referrer_change_cooldown_days]" value="<?= isset($settings['referrer_change_cooldown_days']) ? $settings['referrer_change_cooldown_days']->value : '30' ?>" min="0">
                                            <small class="form-text text-muted">Referans değişikliği yapıldıktan sonra tekrar değişiklik için bekleme süresi.</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox" class="custom-control-input" id="allow_referrer_change" name="settings[allow_referrer_change]" value="1" <?= isset($settings['allow_referrer_change']) && $settings['allow_referrer_change']->value == 1 ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="allow_referrer_change">Referans Değiştirmeye İzin Ver</label>
                                                <small class="form-text text-muted">Kullanıcıların referanslarını değiştirebilmesine izin ver.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-warning btn-block mt-3">
                                    <i class="fas fa-shield-alt mr-1"></i> Limit Ayarlarını Kaydet
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Referans Kodu Ayarları -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-code mr-1"></i>
                            Referans Kodu Ayarları
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('admin/referrals/update_referral_codes') ?>" method="POST">
                                <!-- CSRF Token -->
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                                
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="ref_code_min_length">Minimum Uzunluk</label>
                                            <input type="number" class="form-control" id="ref_code_min_length" name="settings[ref_code_min_length]" value="<?= isset($settings['ref_code_min_length']) ? $settings['ref_code_min_length']->value : '4' ?>" min="3" max="10">
                                            <small class="form-text text-muted">Referans kodunun minimum karakter sayısı.</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="ref_code_max_length">Maksimum Uzunluk</label>
                                            <input type="number" class="form-control" id="ref_code_max_length" name="settings[ref_code_max_length]" value="<?= isset($settings['ref_code_max_length']) ? $settings['ref_code_max_length']->value : '20' ?>" min="5" max="50">
                                            <small class="form-text text-muted">Referans kodunun maksimum karakter sayısı.</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="ref_code_change_max_per_30_days">30 Günde Max Değişiklik Sayısı</label>
                                            <input type="number" class="form-control" id="ref_code_change_max_per_30_days" name="settings[ref_code_change_max_per_30_days]" value="<?= isset($settings['ref_code_change_max_per_30_days']) ? $settings['ref_code_change_max_per_30_days']->value : '3' ?>" min="0" max="50">
                                            <small class="form-text text-muted">Kullanıcının 30 gün içinde kaç kez kendi referans kodunu değiştirebileceği.</small>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="ref_code_change_cooldown_days">Kod Değişim Bekleme Süresi (Gün)</label>
                                            <input type="number" class="form-control" id="ref_code_change_cooldown_days" name="settings[ref_code_change_cooldown_days]" value="<?= isset($settings['ref_code_change_cooldown_days']) ? $settings['ref_code_change_cooldown_days']->value : '7' ?>" min="0" max="365">
                                            <small class="form-text text-muted">Referans kodu değişikliği yapıldıktan sonra tekrar değişiklik için bekleme süresi.</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-info btn-block mt-3">
                                    <i class="fas fa-code mr-1"></i> Kod Ayarlarını Kaydet
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Bilgi Paneli -->
                <div class="col-lg-4">
                    <!-- Sistem Özeti -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-line mr-1"></i>
                            Referans Sistemi Özeti
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Toplam Kullanıcı:</span>
                                <span class="font-weight-bold"><?= isset($stats['total_users']) ? number_format($stats['total_users']) : '0' ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Referansı Olan:</span>
                                <span class="font-weight-bold text-success"><?= isset($stats['users_with_referrer']) ? number_format($stats['users_with_referrer']) : '0' ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Referans Veren:</span>
                                <span class="font-weight-bold text-primary"><?= isset($stats['users_who_refer']) ? number_format($stats['users_who_refer']) : '0' ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Bu Ay Verilen Bonus:</span>
                                <span class="font-weight-bold text-warning"><?= isset($stats['monthly_bonus']) ? number_format($stats['monthly_bonus'], 2) : '0.00' ?> TL</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Toplam Verilen Bonus:</span>
                                <span class="font-weight-bold text-info"><?= isset($stats['total_bonus']) ? number_format($stats['total_bonus'], 2) : '0.00' ?> TL</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bilgilendirme -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-info-circle mr-1"></i>
                            Sistem Hakkında
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Nasıl Çalışır?</h6>
                                <p>Bu referans sistemi <strong>tek yönlü bonus</strong> sistemidir:</p>
                                <ul class="mb-0">
                                    <li><strong>Referans veren:</strong> Bonus kazanır</li>
                                    <li><strong>Referans olan:</strong> Bonus almaz</li>
                                    <li>Kategori bazlı farklı bonus oranları</li>
                                    <li>Güvenlik ve limit kontrolleri</li>
                                </ul>
                            </div>
                            
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">Dikkat!</h6>
                                <p class="mb-0">Referans ayarlarında yapacağınız değişiklikler derhal etkili olur. Özellikle bonus oranları ve limitler konusunda dikkatli olun.</p>
                            </div>
                            
                            <div class="alert alert-success">
                                <h6 class="alert-heading">İpucu</h6>
                                <p class="mb-0">Kategori bazlı komisyon ayarları için ayrı bir yönetim paneli mevcuttur. Bu sayede farklı ürün kategorileri için farklı bonus oranları belirleyebilirsiniz.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Hızlı Aksiyonlar -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-bolt mr-1"></i>
                            Hızlı İşlemler
                        </div>
                        <div class="card-body">
                            <a href="<?= base_url('admin/referrals/categories') ?>" class="btn btn-outline-primary btn-block mb-2">
                                <i class="fas fa-tags mr-1"></i> Kategori Bonuslarını Yönet
                            </a>
                            <a href="<?= base_url('admin/referrals/history') ?>" class="btn btn-outline-success btn-block mb-2">
                                <i class="fas fa-history mr-1"></i> Bonus Geçmişi
                            </a>
                            <a href="<?= base_url('admin/referrals/statistics') ?>" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-chart-bar mr-1"></i> Detaylı İstatistikler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
