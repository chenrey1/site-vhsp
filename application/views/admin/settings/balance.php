<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Bakiye Modülü Ayarları</h5>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('admin/dashboard') ?>">Ana Sayfa</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('admin/publicSettings') ?>">Ayarlar</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Bakiye Ayarları</li>
                </ol>
            </nav>

            <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $this->session->flashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-7">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-cogs mr-1"></i>
                            Bakiye Modülü Genel Ayarları
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('admin/settings/update_settings') ?>" method="POST">
                                <!-- CSRF Token -->
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox" class="custom-control-input" id="enable_balance_transfer" name="settings[enable_balance_transfer]" value="1" <?= isset($settings['enable_balance_transfer']) && $settings['enable_balance_transfer']->value == 1 ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="enable_balance_transfer">Bakiye Transferi</label>
                                                <small class="form-text text-muted">Kullanıcıların birbirlerine bakiye transferi yapabilmesini sağlar.</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox" class="custom-control-input" id="enable_balance_exchange" name="settings[enable_balance_exchange]" value="1" <?= isset($settings['enable_balance_exchange']) && $settings['enable_balance_exchange']->value == 1 ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="enable_balance_exchange">Bakiyeler Arası Transfer</label>
                                                <small class="form-text text-muted">Kullanılabilir bakiye ile çekilebilir bakiye arasında transfer yapılabilmesini sağlar.</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="custom-control custom-switch mb-3">
                                                <input type="checkbox" class="custom-control-input" id="enable_credit_operations" name="settings[enable_credit_operations]" value="1" <?= isset($settings['enable_credit_operations']) && $settings['enable_credit_operations']->value == 1 ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="enable_credit_operations">Cari Bakiye İşlemleri</label>
                                                <small class="form-text text-muted">Kullanıcıların cari borç almasını / ödemesini sağlar.</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="usable2withdraw_commission">Kullanılabilir → Çekilebilir Bakiye Transferi Komisyonu (%)</label>
                                            <input type="number" class="form-control" id="usable2withdraw_commission" name="settings[usable2withdraw_commission]" value="<?= isset($settings['usable2withdraw_commission']) ? $settings['usable2withdraw_commission']->value : '10' ?>" min="0" max="100" step="0.01" required>
                                            <small class="form-text text-muted">Kullanılabilir bakiyeden çekilebilir bakiyeye transfer yaparken alınacak komisyon oranını belirler.</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary btn-block mt-3">Ayarları Kaydet</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-info-circle mr-1"></i>
                            Bakiye Modülü Hakkında
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">Bakiye Modülü Nedir?</h6>
                                <p>Bakiye modülü, kullanıcıların site içi bakiye işlemlerini yönetebilecekleri bir sistemdir. Bu modül sayesinde kullanıcılar:</p>
                                <ul>
                                    <li>Birbirlerine bakiye transferi yapabilir</li>
                                    <li>Kullanılabilir ve çekilebilir bakiyelerini yönetebilir</li>
                                    <li>Kredi işlemleri gerçekleştirebilir</li>
                                </ul>
                            </div>
                            
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">Dikkat!</h6>
                                <p>Bakiye ayarlarında yapacağınız değişiklikler, sistemin işleyişini doğrudan etkiler. Komisyon oranını değiştirmeniz durumunda, kullanıcılar farklı oranlarda ücretlendirilebilir.</p>
                            </div>
                            
                            <div class="alert alert-success">
                                <h6 class="alert-heading">İpucu</h6>
                                <p>Kullanılabilir bakiyeden çekilebilir bakiyeye transfer aktif edildiğinde kullanıcılarınız kart ile de yükleme yaptıklarında çekim talebi verebilirler.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>