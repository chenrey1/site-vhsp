<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Bayilik Ayarları</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dealer'); ?>">Bayilik Yönetimi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bayilik Ayarları</li>
                </ol>
            </nav>

            <?php if (isset($_SESSION['success'])) { ?>
                <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php } ?>
            <?php if (isset($_SESSION['error'])) { ?>
                <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php } ?>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cogs mr-1"></i>
                    Bayilik Başvuru Ayarları
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/dealer/saveSettings') ?>" method="post">
                        <!-- Otomatik Onaylama Ayarı -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="auto_approve" name="auto_approve" value="1" <?= isset($settings) && property_exists($settings, 'auto_approve') && $settings->auto_approve == '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="auto_approve">Yeni bayilik başvurularını otomatik olarak onayla</label>
                            </div>
                            <small class="form-text text-muted">Bu seçenek aktif olduğunda, yeni yapılan bayilik başvuruları otomatik olarak onaylanacaktır.</small>
                        </div>

                        <div class="form-group mt-4">
                            <label for="default_dealer_type_id"><b>Otomatik Bayilik Ataması</b></label>
                            <select class="form-control" id="default_dealer_type_id" name="default_dealer_type_id">
                                <option value="">Otomatik bayilik ataması yapma</option>
                                <?php if (isset($dealer_types) && !empty($dealer_types)): ?>
                                    <?php foreach ($dealer_types as $type): ?>
                                        <option value="<?= $type->id ?>" <?= isset($settings) && property_exists($settings, 'default_dealer_type_id') && $settings->default_dealer_type_id == $type->id ? 'selected' : '' ?>>
                                            <?= $type->name ?> (Min: <?= number_format($type->min_purchase_amount, 2, ',', '.') ?> TL - İndirim: %<?= $type->discount_percentage ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="form-text text-muted">Onaylanan başvurular otomatik olarak seçilen bayilik tipine atanacaktır. Boş bırakılırsa, onay sonrası manuel bayilik ataması gerekir.</small>
                        </div>

                        <hr class="my-4">

                        <!-- Süreli Bayilik Ataması -->
                        <h5>Süreli Bayilik Ataması</h5>
                        <p class="text-muted small">Bayilik başvurularını önce geçici bir bayiliğe atayıp, belirli süre sonra başka bir bayiliğe geçirebilirsiniz.</p>
                        
                        <!-- Yeni eklenen uyarı bilgisi -->
                        <div class="alert alert-light border small p-2 mb-3">
                            <i class="fas fa-lightbulb text-warning mr-1"></i> <strong>İpucu:</strong> Bu özelliği örneğin deneme süresi uygulamak veya yeni bayilere geçici statü vermek için kullanabilirsiniz.
                            <hr class="my-2">
                            <strong>Örnek kullanım senaryoları:</strong>
                            <ul class="mb-0 pl-3">
                                <li>30 günlük deneme süresi sonunda tam bayiliğe geçiş</li>
                                <li>Yeni bayileri önce düşük indirimli kategoriye atayıp, belirli süre sonra standart bayilik vermek</li>
                                <li>Bayilere belirli bir süre tanıtım indirimi sunmak</li>
                            </ul>
                        </div>

                        <div class="form-group mt-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="enable_timed_dealer" name="enable_timed_dealer" value="1" <?= isset($settings) && property_exists($settings, 'enable_timed_dealer') && $settings->enable_timed_dealer == '1' ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="enable_timed_dealer">Süreli bayilik atamasını aktifleştir</label>
                            </div>
                        </div>

                        <div class="row" id="timed_dealer_settings" style="<?= isset($settings) && property_exists($settings, 'enable_timed_dealer') && $settings->enable_timed_dealer == '1' ? '' : 'display:none;' ?>">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="initial_dealer_type_id">İlk Bayilik Tipi</label>
                                    <select class="form-control" id="initial_dealer_type_id" name="initial_dealer_type_id">
                                        <option value="">Seçiniz</option>
                                        <?php if (isset($dealer_types) && !empty($dealer_types)): ?>
                                            <?php foreach ($dealer_types as $type): ?>
                                                <option value="<?= $type->id ?>" <?= isset($settings) && property_exists($settings, 'initial_dealer_type_id') && $settings->initial_dealer_type_id == $type->id ? 'selected' : '' ?>>
                                                    <?= $type->name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <small class="form-text text-muted">Başvuru sonrası ilk atanacak bayilik tipi</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dealer_period">Süre (Gün)</label>
                                    <input type="number" class="form-control" id="dealer_period" name="dealer_period" min="1" value="<?= isset($settings) && property_exists($settings, 'dealer_period') && !empty($settings->dealer_period) ? $settings->dealer_period : 30 ?>">
                                    <small class="form-text text-muted">Kaç gün sonra bayilik tipi değiştirilecek</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="final_dealer_type_id">Son Bayilik Tipi</label>
                                    <select class="form-control" id="final_dealer_type_id" name="final_dealer_type_id">
                                        <option value="">Seçiniz</option>
                                        <?php if (isset($dealer_types) && !empty($dealer_types)): ?>
                                            <?php foreach ($dealer_types as $type): ?>
                                                <option value="<?= $type->id ?>" <?= isset($settings) && property_exists($settings, 'final_dealer_type_id') && $settings->final_dealer_type_id == $type->id ? 'selected' : '' ?>>
                                                    <?= $type->name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <small class="form-text text-muted">Süre sonunda geçilecek bayilik tipi</small>
                                </div>
                            </div>
                        </div>

                        <!-- Onaylama butonu öncesi uyarı -->
                        <div class="alert alert-light border small p-2 mt-3 mb-3" id="settings_conflict_warning" style="display:none;">
                            <i class="fas fa-exclamation-triangle text-warning mr-1"></i> <strong>Uyarı:</strong> Otomatik bayilik ataması ve süreli bayilik ataması aynı anda aktif. Bu durumda, <u>süreli bayilik ayarları öncelikli olarak uygulanacaktır</u>.
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-save mr-1"></i> Ayarları Kaydet
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Sadeleştirilmiş Bilgilendirme Alanı - Yeni konumu -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle mr-1"></i>
                    Ayarların Çalışma Prensibi
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-check-circle text-primary mr-1"></i> Otomatik Onaylama ve Atama</h6>
                            <ul class="pl-4">
                                <li>Bayilik başvuruları otomatik onaylanır</li>
                                <li>Seçilen bayilik tipine doğrudan atanır</li>
                                <li>Örnek: "Bayi 1" seçili ise, tüm başvurular otomatik Bayi 1 olur</li>
                            </ul>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-clock text-primary mr-1"></i> Süreli Bayilik Ataması</h6>
                            <ul class="pl-4">
                                <li>Önce ilk bayilik tipi atanır (örn. "Deneme Bayi")</li>
                                <li>Belirli süre sonra ikinci bayilik tipine geçilir (örn. "Standart Bayi")</li>
                                <li>Örnek: 30 günlük tanıtım indirimi</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning mt-2 mb-0 p-2 small">
                        <strong>ÖNEMLİ:</strong> Hem otomatik atama hem de süreli bayilik seçili ise, süreli bayilik kuralları öncelikli uygulanır.
                    </div>
                </div>
            </div>
        </div>
    </main>

<script>
    $(document).ready(function() {
        // Süreli bayilik ayarları görünürlüğü
        $('#enable_timed_dealer').change(function() {
            if($(this).is(':checked')) {
                $('#timed_dealer_settings').slideDown();
                checkForConflictingSettings();
            } else {
                $('#timed_dealer_settings').slideUp();
                $('#settings_conflict_warning').hide();
            }
        });
        
        // Otomatik bayilik ataması değişikliği
        $('#default_dealer_type_id').change(function() {
            checkForConflictingSettings();
        });
        
        // Çelişen ayarları kontrol et
        function checkForConflictingSettings() {
            var timedDealerEnabled = $('#enable_timed_dealer').is(':checked');
            var defaultDealerSelected = $('#default_dealer_type_id').val() !== '';
            
            if (timedDealerEnabled && defaultDealerSelected) {
                $('#settings_conflict_warning').slideDown();
            } else {
                $('#settings_conflict_warning').slideUp();
            }
        }
        
        // Sayfa yüklendiğinde çelişen ayarları kontrol et
        checkForConflictingSettings();
    });
</script> 