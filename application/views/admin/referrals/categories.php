<div id="layoutSidenav_content" class="referrals-page">
    <main>
        <div class="container-fluid">
            <!-- Başlık -->
            <div class="page-title d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <i class="fas fa-tags text-primary mr-2"></i>
                    <h5 class="mb-0">Kategori Bonus Yönetimi</h5>
                </div>
                <button type="button" class="btn btn-sm btn-info" data-toggle="collapse" data-target="#infoSection">
                    <i class="fas fa-info-circle"></i>
                    <span class="d-none d-md-inline ml-1">Bilgi</span>
                </button>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/referrals/settings') ?>">Referans Ayarları</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kategori Bonusları</li>
                </ol>
            </nav>

            <!-- Alert Messages -->
            <?php if($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i><?= $this->session->flashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>
            
            <?php if($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i><?= $this->session->flashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php endif; ?>

            <!-- Bilgi ve Varsayılan Ayarlar (Collapsible) -->
            <div class="collapse mb-4" id="infoSection">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-cog mr-2"></i>
                        Varsayılan Referans Ayarları
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="fas fa-percentage text-success mb-2" style="font-size: 1.5rem;"></i>
                                    <h5 class="text-success mb-1"><?= number_format($default_settings['purchase_bonus_rate'], 2) ?>%</h5>
                                    <small class="text-muted">Varsayılan Bonus Oranı</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="fas fa-money-bill-wave text-info mb-2" style="font-size: 1.5rem;"></i>
                                    <h5 class="text-info mb-1"><?= number_format($default_settings['min_purchase_amount'], 2) ?> ₺</h5>
                                    <small class="text-muted">Min. Tutar</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="fas fa-gift text-warning mb-2" style="font-size: 1.5rem;"></i>
                                    <h5 class="text-warning mb-1"><?= number_format($default_settings['max_bonus_per_transaction'], 2) ?> ₺</h5>
                                    <small class="text-muted">Max Bonus</small>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="alert alert-info mb-0">
                                    <h6 class="alert-heading"><i class="fas fa-lightbulb mr-1"></i> Nasıl Çalışır?</h6>
                                    <ul class="mb-0 small">
                                        <li>Her kategori için farklı bonus oranı belirleyebilirsiniz</li>
                                        <li>Boş bırakılan değerler varsayılan ayarları kullanır</li>
                                        <li>Değişiklikler anında etkili olur</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-warning mb-0">
                                    <h6 class="alert-heading"><i class="fas fa-exclamation-triangle mr-1"></i> Dikkat!</h6>
                                    <ul class="mb-0 small">
                                        <li>Pasif kategorilerde varsayılan bonus kullanılır</li>
                                        <li>Bonus oranı %0-100 arasında olmalıdır</li>
                                        <li>Genel sistem limitleri her zaman geçerlidir</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ana Kart -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <h6 class="mb-0">
                                <i class="fas fa-list mr-2 text-primary"></i>
                                Kategori Bazlı Bonus Ayarları
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2">
                                <!-- Arama -->
                                <div class="col-md-7">
                                    <div class="input-group input-group-sm">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                        <input type="text" id="categorySearch" class="form-control" placeholder="Kategori ara...">
                                    </div>
                                </div>
                                <!-- Filtre -->
                                <div class="col-md-5">
                                    <select id="statusFilter" class="form-control form-control-sm">
                                        <option value="">Tümü</option>
                                        <option value="active">Aktif Bonuslar</option>
                                        <option value="inactive">Varsayılan Bonuslar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if(!empty($categories)): ?>
                        <!-- Desktop Table -->
                        <div class="table-responsive d-none d-lg-block">
                            <table class="table table-hover mb-0" id="categoriesTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Kategori</th>
                                        <th width="130">
                                            Son 30 Gün (₺)
                                            <i class="fas fa-info-circle text-muted ml-1" 
                                               data-toggle="tooltip" 
                                               data-html="true"
                                               title="<strong>Son 30 Gün</strong><br>Bu kategoride son 30 günde gerçekleşen toplam alışveriş tutarı<br><small>Sadece ödenen faturalar</small>"></i>
                                        </th>
                                        <th width="130">
                                            Ödenen Bonus (₺)
                                            <i class="fas fa-info-circle text-muted ml-1" 
                                               data-toggle="tooltip" 
                                               data-html="true"
                                               title="<strong>Toplam Bonus</strong><br>Bu kategoride son 30 günde ödenen toplam referans bonusu<br><small>Sadece alışveriş bonusları</small>"></i>
                                        </th>
                                        <th width="120">
                                            Bonus (%)
                                            <i class="fas fa-info-circle text-muted ml-1" 
                                               data-toggle="tooltip" 
                                               data-html="true"
                                               title="<strong>Bonus Oranı</strong><br>Alışveriş tutarının yüzde kaçı bonus olacak<br><small>Varsayılan: <?= number_format($default_settings['purchase_bonus_rate'], 2) ?>%</small>"></i>
                                        </th>
                                        <th width="140">
                                            Min. Tutar (₺)
                                            <i class="fas fa-info-circle text-muted ml-1" 
                                               data-toggle="tooltip" 
                                               data-html="true"
                                               title="<strong>Minimum Alışveriş</strong><br>Bonus kazanmak için gereken minimum tutar<br><small>Varsayılan: <?= number_format($default_settings['min_purchase_amount'], 2) ?> ₺</small>"></i>
                                        </th>
                                        <th width="140">
                                            Max Bonus (₺)
                                            <i class="fas fa-info-circle text-muted ml-1" 
                                               data-toggle="tooltip" 
                                               data-html="true"
                                               title="<strong>Maksimum Bonus</strong><br>Tek işlemden alınabilecek maksimum bonus<br><small>Varsayılan: <?= number_format($default_settings['max_bonus_per_transaction'], 2) ?> ₺</small>"></i>
                                        </th>
                                        <th width="120">
                                            Durum
                                            <i class="fas fa-info-circle text-muted ml-1" 
                                               data-toggle="tooltip" 
                                               data-html="true"
                                               title="<strong>Kategori Bonusu</strong><br>Aktif: Bu kategori için özel bonus hesaplanır<br>Varsayılan: Genel bonus ayarları kullanılır"></i>
                                        </th>
                                        <th width="100">İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($categories as $category): ?>
                                    <tr class="category-row" 
                                        data-category-id="<?= $category->id ?>"
                                        data-category-name="<?= strtolower($category->name) ?>"
                                        data-bonus-status="<?= $category->bonus_active == 1 ? 'active' : 'inactive' ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if(!empty($category->img)): ?>
                                                <img src="<?= base_url('assets/img/category/' . $category->img) ?>" 
                                                     alt="<?= $category->name ?>" 
                                                     class="rounded mr-2" 
                                                     width="32" height="32"
                                                     onerror="this.style.display='none'">
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?= $category->name ?></strong>
                                                    <?php if(!empty($category->description)): ?>
                                                    <br><small class="text-muted"><?= character_limiter($category->description, 40) ?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="text-<?= $category->monthly_volume > 0 ? 'success' : 'muted' ?>">
                                                <?= number_format($category->monthly_volume, 2) ?> ₺
                                            </strong>
                                        </td>
                                        <td>
                                            <strong class="text-<?= $category->total_bonus > 0 ? 'warning' : 'muted' ?>">
                                                <?= number_format($category->total_bonus, 2) ?> ₺
                                            </strong>
                                            <?php if($category->monthly_volume > 0): ?>
                                            <br><small class="text-muted">
                                                (<?= number_format(($category->total_bonus / $category->monthly_volume) * 100, 2) ?>%)
                                            </small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control bonus-percentage" 
                                                       data-category-id="<?= $category->id ?>"
                                                       value="<?= $category->bonus_percentage ? number_format($category->bonus_percentage, 2, '.', '') : '' ?>" 
                                                       min="0" max="100" step="0.01"
                                                       placeholder="<?= number_format($default_settings['purchase_bonus_rate'], 2, '.', '') ?>"
                                                       <?= ($category->bonus_active != 1) ? 'disabled' : '' ?>>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control min-amount" 
                                                       data-category-id="<?= $category->id ?>"
                                                       value="<?= $category->min_amount ? number_format($category->min_amount, 2, '.', '') : '' ?>" 
                                                       min="0" step="0.01"
                                                       placeholder="<?= number_format($default_settings['min_purchase_amount'], 2, '.', '') ?>"
                                                       <?= ($category->bonus_active != 1) ? 'disabled' : '' ?>>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">₺</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control max-bonus" 
                                                       data-category-id="<?= $category->id ?>"
                                                       value="<?= $category->max_bonus ? number_format($category->max_bonus, 2, '.', '') : '' ?>" 
                                                       min="0" step="0.01" 
                                                       placeholder="<?= number_format($default_settings['max_bonus_per_transaction'], 2, '.', '') ?>"
                                                       <?= ($category->bonus_active != 1) ? 'disabled' : '' ?>>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">₺</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input bonus-status" 
                                                       id="statusSwitch<?= $category->id ?>" 
                                                       data-category-id="<?= $category->id ?>"
                                                       <?= ($category->bonus_active == 1) ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="statusSwitch<?= $category->id ?>">
                                                    <span class="badge badge-<?= ($category->bonus_active == 1) ? 'success' : 'secondary' ?> status-badge">
                                                        <?= ($category->bonus_active == 1) ? 'Aktif' : 'Varsayılan' ?>
                                                    </span>
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary save-category-bonus" 
                                                    data-category-id="<?= $category->id ?>"
                                                    <?= ($category->bonus_active != 1) ? 'disabled' : '' ?>>
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Cards -->
                        <div class="d-lg-none" id="categoryCardsContainer">
                            <?php foreach($categories as $category): ?>
                            <div class="category-card" 
                                 data-category-id="<?= $category->id ?>"
                                 data-category-name="<?= strtolower($category->name) ?>"
                                 data-bonus-status="<?= $category->bonus_active == 1 ? 'active' : 'inactive' ?>">
                                <div class="category-card-header" data-toggle="collapse" data-target="#mobileCard<?= $category->id ?>" role="button">
                                    <div class="d-flex align-items-center">
                                        <?php if(!empty($category->img)): ?>
                                        <img src="<?= base_url('assets/img/category/' . $category->img) ?>" 
                                             alt="<?= $category->name ?>" 
                                             class="rounded mr-2" 
                                             width="40" height="40"
                                             onerror="this.style.display='none'">
                                        <?php endif; ?>
                                        <div class="flex-grow-1">
                                            <strong><?= $category->name ?></strong>
                                            <?php if(!empty($category->description)): ?>
                                            <br><small class="text-muted"><?= character_limiter($category->description, 50) ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="custom-control custom-switch" onclick="event.stopPropagation();">
                                            <input type="checkbox" class="custom-control-input bonus-status" 
                                                   id="statusSwitchMobile<?= $category->id ?>" 
                                                   data-category-id="<?= $category->id ?>"
                                                   <?= ($category->bonus_active == 1) ? 'checked' : '' ?>>
                                            <label class="custom-control-label" for="statusSwitchMobile<?= $category->id ?>"></label>
                                        </div>
                                        <i class="fas fa-chevron-down collapse-icon ml-2"></i>
                                    </div>
                                </div>
                                <div class="collapse category-card-body <?= ($category->bonus_active == 1) ? 'show' : '' ?>" id="mobileCard<?= $category->id ?>">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="alert alert-info mb-2 py-2">
                                                <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">
                                                    <i class="fas fa-chart-line"></i> Alışveriş Hacmi
                                                </small>
                                                <strong class="text-<?= $category->monthly_volume > 0 ? 'success' : 'muted' ?>">
                                                    <?= number_format($category->monthly_volume, 2) ?> ₺
                                                </strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="alert alert-warning mb-2 py-2">
                                                <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">
                                                    <i class="fas fa-gift"></i> Ödenen Bonus
                                                </small>
                                                <strong class="text-<?= $category->total_bonus > 0 ? 'warning' : 'muted' ?>">
                                                    <?= number_format($category->total_bonus, 2) ?> ₺
                                                </strong>
                                                <?php if($category->monthly_volume > 0): ?>
                                                <br><small class="text-muted" style="font-size: 0.7rem;">
                                                    (<?= number_format(($category->total_bonus / $category->monthly_volume) * 100, 2) ?>%)
                                                </small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="small text-muted mb-1">
                                                <i class="fas fa-percentage"></i> Bonus Oranı (%)
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control bonus-percentage" 
                                                       data-category-id="<?= $category->id ?>"
                                                       value="<?= $category->bonus_percentage ? number_format($category->bonus_percentage, 2, '.', '') : '' ?>" 
                                                       min="0" max="100" step="0.01"
                                                       placeholder="<?= number_format($default_settings['purchase_bonus_rate'], 2, '.', '') ?>"
                                                       <?= ($category->bonus_active != 1) ? 'disabled' : '' ?>>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="small text-muted mb-1">
                                                <i class="fas fa-money-bill-wave"></i> Min. Tutar (₺)
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control min-amount" 
                                                       data-category-id="<?= $category->id ?>"
                                                       value="<?= $category->min_amount ? number_format($category->min_amount, 2, '.', '') : '' ?>" 
                                                       min="0" step="0.01"
                                                       placeholder="<?= number_format($default_settings['min_purchase_amount'], 2, '.', '') ?>"
                                                       <?= ($category->bonus_active != 1) ? 'disabled' : '' ?>>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">₺</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="small text-muted mb-1">
                                                <i class="fas fa-gift"></i> Max Bonus (₺)
                                            </label>
                                            <div class="input-group input-group-sm">
                                                <input type="number" class="form-control max-bonus" 
                                                       data-category-id="<?= $category->id ?>"
                                                       value="<?= $category->max_bonus ? number_format($category->max_bonus, 2, '.', '') : '' ?>" 
                                                       min="0" step="0.01" 
                                                       placeholder="<?= number_format($default_settings['max_bonus_per_transaction'], 2, '.', '') ?>"
                                                       <?= ($category->bonus_active != 1) ? 'disabled' : '' ?>>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">₺</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-primary btn-block mt-3 save-category-bonus" 
                                            data-category-id="<?= $category->id ?>"
                                            <?= ($category->bonus_active != 1) ? 'disabled' : '' ?>>
                                        <i class="fas fa-save mr-1"></i> Kaydet
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Sonuç Mesajı -->
                        <div id="noResultMessage" class="text-center py-5 d-none">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Arama kriterlerine uygun kategori bulunamadı.</p>
                        </div>

                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Henüz kategori bulunmuyor.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

<!-- Toastr CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


<script>
$(document).ready(function() {
    // Toastr konfigürasyonu
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: true,
        progressBar: true,
        positionClass: "toast-top-right",
        preventDuplicates: false,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "3000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut"
    };

    // Tooltip başlat (HTML içerik desteği ile)
    $('[data-toggle="tooltip"]').tooltip({
        html: true,
        placement: 'top'
    });

    // Toast bildirimi göster
    function showToast(message, type = 'info', title = '') {
        switch(type) {
            case 'success':
                toastr.success(message, title || 'Başarılı');
                break;
            case 'error':
                toastr.error(message, title || 'Hata', {
                    timeOut: 5000,
                    extendedTimeOut: 2000
                });
                break;
            case 'warning':
                toastr.warning(message, title || 'Uyarı');
                break;
            default:
                toastr.info(message, title || 'Bilgi');
        }
    }

    // Türkçe karakter normalizasyonu
    function turkishToLower(text) {
        const turkishMap = {
            'Ç': 'ç', 'Ğ': 'ğ', 'İ': 'i', 'Ö': 'ö', 'Ş': 'ş', 'Ü': 'ü',
            'I': 'ı'
        };
        
        return text.split('').map(char => turkishMap[char] || char).join('').toLowerCase();
    }

    // Arama ve filtreleme
    function filterCategories() {
        const searchTerm = turkishToLower($('#categorySearch').val());
        const statusFilter = $('#statusFilter').val();
        let visibleCount = 0;

        // Desktop table rows
        $('.category-row').each(function() {
            const categoryName = turkishToLower($(this).data('category-name'));
            const bonusStatus = $(this).data('bonus-status');
            
            let showRow = true;
            
            // Arama filtresi
            if (searchTerm && categoryName.indexOf(searchTerm) === -1) {
                showRow = false;
            }
            
            // Durum filtresi
            if (statusFilter && bonusStatus !== statusFilter) {
                showRow = false;
            }
            
            if (showRow) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        // Mobile cards
        $('.category-card').each(function() {
            const categoryName = turkishToLower($(this).data('category-name'));
            const bonusStatus = $(this).data('bonus-status');
            
            let showCard = true;
            
            // Arama filtresi
            if (searchTerm && categoryName.indexOf(searchTerm) === -1) {
                showCard = false;
            }
            
            // Durum filtresi
            if (statusFilter && bonusStatus !== statusFilter) {
                showCard = false;
            }
            
            if (showCard) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        // Sonuç mesajı
        if (visibleCount === 0) {
            $('#noResultMessage').removeClass('d-none');
        } else {
            $('#noResultMessage').addClass('d-none');
        }
    }

    // Arama input
    $('#categorySearch').on('keyup', filterCategories);
    
    // Filtre select
    $('#statusFilter').on('change', filterCategories);

    // Kategori bonus kaydetme
    $('.save-category-bonus').click(function() {
        var categoryId = $(this).data('category-id');
        var bonusPercentage = $(`.bonus-percentage[data-category-id="${categoryId}"]`).val();
        var minAmount = $(`.min-amount[data-category-id="${categoryId}"]`).val();
        var maxBonus = $(`.max-bonus[data-category-id="${categoryId}"]`).val();
        var isActive = $(`.bonus-status[data-category-id="${categoryId}"]`).is(':checked') ? 1 : 0;
        
        // Validasyon
        if (bonusPercentage && (bonusPercentage < 0 || bonusPercentage > 100)) {
            showToast('Bonus oranı 0-100 arasında olmalıdır!', 'warning', 'Geçersiz Değer');
            return;
        }
        
        if (minAmount && minAmount < 0) {
            showToast('Minimum tutar 0\'dan küçük olamaz!', 'warning', 'Geçersiz Değer');
            return;
        }

        if (maxBonus && maxBonus < 0) {
            showToast('Maksimum bonus 0\'dan küçük olamaz!', 'warning', 'Geçersiz Değer');
            return;
        }
        
        // Button durumu
        var $btn = $(`.save-category-bonus[data-category-id="${categoryId}"]`);
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        // Ajax ile kaydet
        $.ajax({
            url: '<?= base_url('admin/referrals/update_category_bonus') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                category_id: categoryId,
                bonus_percentage: bonusPercentage || null,
                min_amount: minAmount || null,
                max_bonus: maxBonus || null,
                is_active: isActive
            },
            success: function(response) {
                if (response.success) {
                    showToast('Kategori bonus ayarları başarıyla güncellendi!', 'success');
                    
                    // Status badge güncelle (desktop)
                    var statusBadge = $(`#statusSwitch${categoryId}`).siblings('label').find('.status-badge');
                    if (isActive) {
                        statusBadge.removeClass('badge-secondary').addClass('badge-success').text('Aktif');
                    } else {
                        statusBadge.removeClass('badge-success').addClass('badge-secondary').text('Varsayılan');
                    }

                    // Data attribute güncelle
                    $(`.category-row[data-category-id="${categoryId}"], .category-card[data-category-id="${categoryId}"]`)
                        .attr('data-bonus-status', isActive ? 'active' : 'inactive');
                    
                    // Success feedback
                    $btn.removeClass('btn-primary').addClass('btn-success').html('<i class="fas fa-check"></i>');
                    
                    setTimeout(function() {
                        $btn.removeClass('btn-success').addClass('btn-primary').html(originalHtml).prop('disabled', false);
                    }, 2000);
                } else {
                    showToast(response.message || 'Güncelleme sırasında bir hata oluştu!', 'error');
                    $btn.html(originalHtml).prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                showToast('Sunucu ile bağlantı kurulamadı. Lütfen tekrar deneyin.', 'error');
                $btn.html(originalHtml).prop('disabled', false);
            }
        });
    });
    
    // Enter tuşu ile kaydetme
    $('.bonus-percentage, .min-amount, .max-bonus').keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            var categoryId = $(this).data('category-id');
            $(`.save-category-bonus[data-category-id="${categoryId}"]`).click();
        }
    });

    // Switch değişikliğinde data attribute güncelle
    $('.bonus-status').on('change', function() {
        var categoryId = $(this).data('category-id');
        var isActive = $(this).is(':checked');
        
        // İlgili tüm elementlerin data attribute'ünü güncelle
        $(`.category-row[data-category-id="${categoryId}"], .category-card[data-category-id="${categoryId}"]`)
            .attr('data-bonus-status', isActive ? 'active' : 'inactive');
        
        // Inputları enable/disable et
        $(`.bonus-percentage[data-category-id="${categoryId}"], .min-amount[data-category-id="${categoryId}"], .max-bonus[data-category-id="${categoryId}"]`)
            .prop('disabled', !isActive);
        
        // Kaydet butonunu enable/disable et
        $(`.save-category-bonus[data-category-id="${categoryId}"]`)
            .prop('disabled', !isActive);
        
        // Mobilde collapse durumunu kontrol et
        var mobileCardBody = $(`#mobileCard${categoryId}`);
        if (mobileCardBody.length) {
            if (isActive) {
                mobileCardBody.collapse('show');
            } else {
                mobileCardBody.collapse('hide');
            }
        }
    });
});
</script>
