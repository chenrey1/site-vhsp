<div id="layoutSidenav_content" class="referrals-page">
    <main>
        <div class="container-fluid">
            <!-- Başlık -->
            <div class="page-title">
                <div class="d-flex align-items-center justify-content-between flex-wrap">
                    <h5 class="mb-0">Referans Bonus Geçmişi</h5>
                    <?php if(isset($filtered_user) && $filtered_user): ?>
                        <span class="badge badge-info badge-lg">
                            <i class="fas fa-user mr-1"></i>
                            Kullanıcı: <?= $filtered_user->name ?> <?= $filtered_user->surname ?>
                            <a href="<?= base_url('admin/referrals/history') ?>" class="text-white ml-2" title="Filtreyi Temizle">
                                <i class="fas fa-times"></i>
                            </a>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/referrals/settings'); ?>">Referans Ayarları</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bonus Geçmişi</li>
                </ol>
            </nav>

            <!-- Filtreler -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <button class="btn btn-link text-decoration-none p-0 w-100 text-left d-flex justify-content-between align-items-center" 
                            type="button" 
                            data-toggle="collapse" 
                            data-target="#filterSection" 
                            aria-expanded="false" 
                            aria-controls="filterSection">
                        <h6 class="mb-0">
                            <i class="fas fa-filter mr-2 text-primary"></i>
                            Filtreler ve Arama
                        </h6>
                        <i class="fas fa-chevron-down text-muted"></i>
                    </button>
                </div>
                <div class="collapse" id="filterSection">
                <div class="card-body">
                    <form method="GET" action="<?= base_url('admin/referrals/history') ?>" id="filterForm">
                        <!-- Periyot Seçimi -->
                        <div class="mb-3">
                            <label class="font-weight-600 mb-2">
                                <i class="fas fa-calendar-alt text-muted mr-1"></i>
                                Periyot Seçimi
                            </label>
                            <div class="period-buttons">
                                <input type="radio" class="btn-check" name="period" value="today" id="period-today" 
                                       <?= $this->input->get('period') === 'today' ? 'checked' : '' ?> 
                                       onchange="updateDateRange(this.value)">
                                <label class="btn btn-outline-primary" for="period-today">
                                    <i class="fas fa-calendar-day"></i>
                                    <span class="btn-text">Bugün</span>
                                </label>

                                <input type="radio" class="btn-check" name="period" value="yesterday" id="period-yesterday" 
                                       <?= $this->input->get('period') === 'yesterday' ? 'checked' : '' ?> 
                                       onchange="updateDateRange(this.value)">
                                <label class="btn btn-outline-primary" for="period-yesterday">
                                    <i class="fas fa-calendar-minus"></i>
                                    <span class="btn-text">Dün</span>
                                </label>

                                <input type="radio" class="btn-check" name="period" value="week" id="period-week" 
                                       <?= $this->input->get('period') === 'week' ? 'checked' : '' ?> 
                                       onchange="updateDateRange(this.value)">
                                <label class="btn btn-outline-primary" for="period-week">
                                    <i class="fas fa-calendar-week"></i>
                                    <span class="btn-text">Bu Hafta</span>
                                </label>

                                <input type="radio" class="btn-check" name="period" value="month" id="period-month" 
                                       <?= $this->input->get('period') === 'month' || !$this->input->get('period') ? 'checked' : '' ?> 
                                       onchange="updateDateRange(this.value)">
                                <label class="btn btn-outline-primary" for="period-month">
                                    <i class="fas fa-calendar"></i>
                                    <span class="btn-text">Bu Ay</span>
                                </label>

                                <input type="radio" class="btn-check" name="period" value="all" id="period-all" 
                                       <?= $this->input->get('period') === 'all' ? 'checked' : '' ?> 
                                       onchange="updateDateRange(this.value)">
                                <label class="btn btn-outline-primary" for="period-all">
                                    <i class="fas fa-infinity"></i>
                                    <span class="btn-text">Tümü</span>
                                </label>

                                <input type="radio" class="btn-check" name="period" value="custom" id="period-custom" 
                                       <?= $this->input->get('period') === 'custom' ? 'checked' : '' ?> 
                                       onchange="toggleCustomDate()">
                                <label class="btn btn-outline-primary" for="period-custom">
                                    <i class="fas fa-cog"></i>
                                    <span class="btn-text">Özel</span>
                                </label>
                            </div>
                        </div>

                        <div class="row g-3">
                            <!-- Özel Tarih Aralığı -->
                            <div class="col-12" id="customDateSection" style="display: <?= $this->input->get('period') === 'custom' ? 'block' : 'none' ?>;">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="small font-weight-600">Başlangıç Tarihi</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" 
                                               value="<?= $this->input->get('start_date') ?: date('Y-m-d', strtotime('-30 days')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small font-weight-600">Bitiş Tarihi</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" 
                                               value="<?= $this->input->get('end_date') ?: date('Y-m-d') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Arama -->
                            <div class="col-md-6 col-lg-3">
                                <label class="small font-weight-600">
                                    <i class="fas fa-search text-muted mr-1"></i>
                                    Kullanıcı Ara
                                </label>
                                <input type="text" name="search" id="searchInput" class="form-control form-control-sm" 
                                       placeholder="İsim, email, referans kodu..." 
                                       value="<?= $this->input->get('search') ?>">
                            </div>

                            <!-- Bonus Türü -->
                            <div class="col-md-6 col-lg-3">
                                <label class="small font-weight-600">
                                    <i class="fas fa-tag text-muted mr-1"></i>
                                    Bonus Türü
                                </label>
                                <select name="bonus_type" class="form-control form-control-sm">
                                    <option value="">Tümü</option>
                                    <option value="register" <?= $this->input->get('bonus_type') === 'register' ? 'selected' : '' ?>>Kayıt Bonusu</option>
                                    <option value="purchase" <?= $this->input->get('bonus_type') === 'purchase' ? 'selected' : '' ?>>Alışveriş Bonusu</option>
                                </select>
                            </div>

                            <!-- Durum -->
                            <div class="col-md-6 col-lg-3">
                                <label class="small font-weight-600">
                                    <i class="fas fa-toggle-on text-muted mr-1"></i>
                                    Durum
                                </label>
                                <select name="status" class="form-control form-control-sm">
                                    <option value="">Tümü</option>
                                    <option value="paid" <?= $this->input->get('status') === 'paid' ? 'selected' : '' ?>>Ödendi</option>
                                    <option value="pending" <?= $this->input->get('status') === 'pending' ? 'selected' : '' ?>>Beklemede</option>
                                    <option value="cancelled" <?= $this->input->get('status') === 'cancelled' ? 'selected' : '' ?>>İptal</option>
                                </select>
                            </div>

                            <!-- Butonlar -->
                            <div class="col-md-6 col-lg-3">
                                <label class="small font-weight-600 d-none d-lg-block">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                                        <i class="fas fa-filter"></i>
                                        <span class="d-none d-md-inline ml-1">Filtrele</span>
                        </button>
                                    <a href="<?= base_url('admin/referrals/history') ?>" class="btn btn-sm btn-secondary" title="Filtreleri Temizle">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-success" onclick="exportToExcel()" title="Excel'e Aktar">
                                        <i class="fas fa-file-excel"></i>
                        </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
            </div>

            <!-- İşlem Listesi -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-list mr-2 text-primary"></i>
                        Bonus İşlemleri
                    </h6>
                    <span class="badge badge-primary badge-pill"><?= !empty($bonus_history) ? count($bonus_history) : 0 ?> kayıt</span>
                </div>
                <div class="card-body p-0">
                    <?php if(!empty($bonus_history)): ?>
                    <!-- Desktop: Tablo -->
                    <div class="table-responsive d-none d-lg-block">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="80">ID</th>
                                    <th>Referans Veren</th>
                                    <th>Referans Olan</th>
                                    <th width="150">Bonus Türü</th>
                                    <th width="120" class="text-right">Tutar</th>
                                    <th width="100">Durum</th>
                                    <th width="130">Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($bonus_history as $bonus): ?>
                                <tr>
                                    <td>
                                        <span class="font-weight-bold text-primary">#<?= $bonus->id ?></span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <a href="<?= base_url('admin/product/userShopHistory/' . $bonus->referrer_id) ?>" class="font-weight-bold text-primary text-truncate" style="max-width: 200px;" title="<?= $bonus->referrer_name ?>">
                                                <?= $bonus->referrer_name ?>
                                            </a>
                                            <small class="text-muted text-truncate" style="max-width: 200px;"><?= $bonus->referrer_email ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <a href="<?= base_url('admin/product/userShopHistory/' . $bonus->referred_user_id) ?>" class="font-weight-bold text-primary text-truncate" style="max-width: 200px;" title="<?= $bonus->referred_name ?>">
                                                <?= $bonus->referred_name ?>
                                            </a>
                                            <small class="text-muted text-truncate" style="max-width: 200px;"><?= $bonus->referred_email ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($bonus->bonus_type == 'register'): ?>
                                            <span class="badge badge-primary">
                                                <i class="fas fa-user-plus"></i> Kayıt
                                            </span>
                                        <?php elseif($bonus->bonus_type == 'purchase'): ?>
                                            <span class="badge badge-success">
                                                <i class="fas fa-shopping-cart"></i> Alışveriş
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-right">
                                        <span class="font-weight-bold text-success">
                                            <?= number_format($bonus->bonus_amount, 2) ?> ₺
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $status_class = '';
                                        $status_text = '';
                                        switch($bonus->status) {
                                            case 'paid':
                                                $status_class = 'success';
                                                $status_text = 'Ödendi';
                                                break;
                                            case 'pending':
                                                $status_class = 'warning';
                                                $status_text = 'Beklemede';
                                                break;
                                            case 'cancelled':
                                                $status_class = 'danger';
                                                $status_text = 'İptal';
                                                break;
                                        }
                                        ?>
                                        <span class="badge badge-<?= $status_class ?>">
                                            <?= $status_text ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span><?= date('d.m.Y', strtotime($bonus->created_at)) ?></span>
                                            <small class="text-muted"><?= date('H:i', strtotime($bonus->created_at)) ?></small>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile: Kartlar -->
                    <div class="bonus-history-mobile d-lg-none">
                        <?php foreach($bonus_history as $bonus): ?>
                        <div class="bonus-card">
                            <div class="bonus-card__header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="bonus-card__id">#<?= $bonus->id ?></span>
                                    <div>
                                        <?php if($bonus->bonus_type == 'register'): ?>
                                            <span class="badge badge-primary">
                                                <i class="fas fa-user-plus"></i> Kayıt
                                            </span>
                                        <?php elseif($bonus->bonus_type == 'purchase'): ?>
                                            <span class="badge badge-success">
                                                <i class="fas fa-shopping-cart"></i> Alışveriş
                                            </span>
                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="bonus-card__body">
                                <div class="bonus-card__users">
                                    <div class="bonus-card__user">
                                        <small class="text-muted">Referans Veren</small>
                                        <a href="<?= base_url('admin/product/userShopHistory/' . $bonus->referrer_id) ?>" class="font-weight-bold text-primary">
                                            <?= $bonus->referrer_name ?>
                                        </a>
                                        <small class="text-muted"><?= $bonus->referrer_email ?></small>
                        </div>
                                    <div class="bonus-card__arrow">
                                        <i class="fas fa-arrow-right text-muted"></i>
                                    </div>
                                    <div class="bonus-card__user">
                                        <small class="text-muted">Referans Olan</small>
                                        <a href="<?= base_url('admin/product/userShopHistory/' . $bonus->referred_user_id) ?>" class="font-weight-bold text-primary">
                                            <?= $bonus->referred_name ?>
                                        </a>
                                        <small class="text-muted"><?= $bonus->referred_email ?></small>
                                    </div>
                                </div>
                                <div class="bonus-card__amount">
                                    <span class="text-success font-weight-bold">
                                        +<?= number_format($bonus->bonus_amount, 2) ?> ₺
                                    </span>
                                </div>
                            </div>
                            <div class="bonus-card__footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php 
                                        $status_class = '';
                                        $status_text = '';
                                        switch($bonus->status) {
                                            case 'paid':
                                                $status_class = 'success';
                                                $status_text = 'Ödendi';
                                                break;
                                            case 'pending':
                                                $status_class = 'warning';
                                                $status_text = 'Beklemede';
                                                break;
                                            case 'cancelled':
                                                $status_class = 'danger';
                                                $status_text = 'İptal';
                                                break;
                                        }
                                        ?>
                                        <span class="badge badge-<?= $status_class ?>"><?= $status_text ?></span>
                                        <small class="text-muted ml-2">
                                            <?= date('d.m.Y H:i', strtotime($bonus->created_at)) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                </div>

                    <!-- Pagination -->
                    <?php if(!empty($pagination)): ?>
                    <div class="p-3 border-top">
                        <div class="d-flex justify-content-center">
                            <?= $pagination ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Bonus işlemi bulunamadı</h5>
                        <p class="text-muted">Seçtiğiniz filtrelere uygun bonus işlemi bulunmuyor.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>


<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
// Tarih aralığını güncelle
function updateDateRange(period) {
    const today = new Date();
    let start, end;
    
    switch(period) {
        case 'today':
            start = end = today;
            break;
        case 'yesterday':
            start = end = new Date(today);
            start.setDate(today.getDate() - 1);
            end.setDate(today.getDate() - 1);
            break;
        case 'week':
            start = new Date(today);
            start.setDate(today.getDate() - today.getDay() + 1);
            end = today;
            break;
        case 'month':
            start = new Date(today.getFullYear(), today.getMonth(), 1);
            end = today;
            break;
        case 'all':
            // Tümü seçildiğinde tarih inputlarını temizle
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            document.getElementById('customDateSection').style.display = 'none';
            return;
        default:
            return;
    }
    
    if (period !== 'custom') {
        document.getElementById('customDateSection').style.display = 'none';
        document.getElementById('start_date').value = formatDate(start);
        document.getElementById('end_date').value = formatDate(end);
    }
}

// Özel tarih seçimini aç/kapat
function toggleCustomDate() {
    const section = document.getElementById('customDateSection');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}

// Tarih formatla
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Excel'e aktar
function exportToExcel() {
    const table = document.querySelector('.table');
    if (!table) {
        alert('Tablo bulunamadı!');
        return;
    }
    
    const wb = XLSX.utils.table_to_book(table, {sheet: "Bonus Geçmişi"});
    XLSX.writeFile(wb, "referans-bonus-gecmisi-" + new Date().toISOString().slice(0,10) + ".xlsx");
}

// Eğer filtre uygulanmışsa filtreleri otomatik aç
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = urlParams.has('period') || urlParams.has('search') || 
                       urlParams.has('bonus_type') || urlParams.has('status') ||
                       urlParams.has('start_date') || urlParams.has('end_date') ||
                       urlParams.has('referrer_id');
    
    if (hasFilters) {
        $('#filterSection').collapse('show');
    }
});
</script> 
