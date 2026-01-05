<div id="layoutSidenav_content" class="referrals-page">
    <main>
        <div class="container-fluid">
            <!-- Başlık -->
            <div class="page-title">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chart-pie text-primary mr-2"></i>
                <h5 class="mb-0">Referans İstatistikleri</h5>
                </div>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/referrals/settings'); ?>">Referans Ayarları</a></li>
                    <li class="breadcrumb-item active" aria-current="page">İstatistikler</li>
                </ol>
            </nav>

            <!-- Filtreler -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-filter mr-2 text-primary"></i>
                        Filtreler
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= base_url('admin/referrals/statistics') ?>" id="filterForm">
                            <!-- Periyot Seçimi -->
                        <div class="mb-3">
                            <label class="font-weight-600 mb-2">
                                <i class="fas fa-calendar-alt text-muted mr-1"></i>
                                Periyot Seçimi
                                    </label>
                            <div class="period-buttons">
                                <input type="radio" class="btn-check" name="period" value="today" id="period-today" 
                                       <?= $this->input->get('period') === 'today' || !$this->input->get('period') ? 'checked' : '' ?> 
                                       onchange="updateDateRange(this.value)">
                                <label class="btn btn-outline-primary" for="period-today">
                                    <i class="fas fa-calendar-day"></i>
                                    <span class="btn-text">Bugün</span>
                                    </label>

                                <input type="radio" class="btn-check" name="period" value="week" id="period-week" 
                                       <?= $this->input->get('period') === 'week' ? 'checked' : '' ?> 
                                       onchange="updateDateRange(this.value)">
                                <label class="btn btn-outline-primary" for="period-week">
                                    <i class="fas fa-calendar-week"></i>
                                    <span class="btn-text">Hafta</span>
                                    </label>

                                <input type="radio" class="btn-check" name="period" value="month" id="period-month" 
                                       <?= $this->input->get('period') === 'month' ? 'checked' : '' ?> 
                                       onchange="updateDateRange(this.value)">
                                <label class="btn btn-outline-primary" for="period-month">
                                    <i class="fas fa-calendar"></i>
                                    <span class="btn-text">Ay</span>
                                    </label>

                                <input type="radio" class="btn-check" name="period" value="quarter" id="period-quarter" 
                                       <?= $this->input->get('period') === 'quarter' ? 'checked' : '' ?> 
                                       onchange="updateDateRange(this.value)">
                                <label class="btn btn-outline-primary" for="period-quarter">
                                    <i class="fas fa-calendar-check"></i>
                                    <span class="btn-text">Çeyrek</span>
                                    </label>

                                <input type="radio" class="btn-check" name="period" value="year" id="period-year" 
                                       <?= $this->input->get('period') === 'year' ? 'checked' : '' ?> 
                                       onchange="updateDateRange(this.value)">
                                <label class="btn btn-outline-primary" for="period-year">
                                    <i class="fas fa-chart-line"></i>
                                    <span class="btn-text">Yıl</span>
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
                            
                            <!-- Bonus Türü -->
                            <div class="col-md-6 col-lg-4">
                                <label class="small font-weight-600">
                                    <i class="fas fa-tag text-muted mr-1"></i>
                                    Bonus Türü
                                </label>
                                <select name="bonus_type" class="form-control form-control-sm">
                                    <option value="">Tümü</option>
                                    <option value="register" <?= $this->input->get('bonus_type') === 'register' ? 'selected' : '' ?>>Kayıt Bonusu</option>
                                    <option value="purchase" <?= $this->input->get('bonus_type') === 'purchase' ? 'selected' : '' ?>>Alışveriş Bonusu</option>
                                    <option value="other" <?= $this->input->get('bonus_type') === 'other' ? 'selected' : '' ?>>Diğer</option>
                                </select>
                            </div>
                            
                            <!-- Seçili Tarih Gösterimi -->
                            <div class="col-md-6 col-lg-4">
                                <label class="small font-weight-600">
                                    <i class="fas fa-calendar-check text-muted mr-1"></i>
                                    Seçili Tarih Aralığı
                                </label>
                                <div class="form-control form-control-sm bg-light text-center font-weight-bold" id="selectedDateRange">
                                    <?php
                                    $start = $this->input->get('start_date') ?: date('Y-m-d', strtotime('-30 days'));
                                    $end = $this->input->get('end_date') ?: date('Y-m-d');
                                    echo date('d.m.Y', strtotime($start)) . ' - ' . date('d.m.Y', strtotime($end));
                                    ?>
                            </div>
                        </div>
                        
                            <!-- Butonlar -->
                            <div class="col-md-12 col-lg-4">
                                <label class="small font-weight-600 d-none d-lg-block">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="navigateDate('prev')">
                                        <i class="fas fa-chevron-left"></i>
                                        <span class="d-none d-md-inline ml-1">Önceki</span>
                                        </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary flex-fill" onclick="navigateDate('next')">
                                        <span class="d-none d-md-inline mr-1">Sonraki</span>
                                        <i class="fas fa-chevron-right"></i>
                                        </button>
                                    <button type="submit" class="btn btn-sm btn-primary flex-fill">
                                        <i class="fas fa-filter"></i>
                                        <span class="d-none d-md-inline ml-1">Filtrele</span>
                                        </button>
                                    <a href="<?= base_url('admin/referrals/statistics') ?>" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-times"></i>
                                        </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Özet Kartları -->
            <div class="row g-3 mb-4">
                <!-- Toplam Kullanıcı -->
                <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div class="stat-card__content">
                                <div class="stat-card__icon-wrapper">
                                    <div class="stat-card__icon bg-primary">
                                    <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div class="stat-card__info">
                                    <h6 class="stat-card__title">Toplam Kullanıcı</h6>
                                    <div class="stat-card__stats">
                                        <div class="stat-card__stat">
                                            <span class="stat-card__label">Toplam</span>
                                            <span class="stat-card__value"><?= number_format($stats['total_users']) ?></span>
                                        </div>
                                        <div class="stat-card__divider"></div>
                                        <div class="stat-card__stat">
                                        <span class="stat-card__label">Referanslı</span>
                                            <span class="stat-card__value"><?= number_format($stats['users_with_referrer']) ?></span>
                                        </div>
                                    </div>
                                <div class="stat-card__footer mt-2">
                                    <small class="text-muted">
                                        Oran: <strong><?= $stats['total_users'] > 0 ? number_format(($stats['users_with_referrer'] / $stats['total_users']) * 100, 1) : '0' ?>%</strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Referans Veren -->
                <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div class="stat-card__content">
                                <div class="stat-card__icon-wrapper">
                                    <div class="stat-card__icon bg-success">
                                    <i class="fas fa-share-alt"></i>
                                    </div>
                                </div>
                                <div class="stat-card__info">
                                    <h6 class="stat-card__title">Referans Veren</h6>
                                    <div class="stat-card__stats">
                                        <div class="stat-card__stat">
                                            <span class="stat-card__label">Toplam</span>
                                            <span class="stat-card__value"><?= number_format($stats['users_who_refer']) ?></span>
                                        </div>
                                        <div class="stat-card__divider"></div>
                                        <div class="stat-card__stat">
                                            <span class="stat-card__label">Aktif</span>
                                            <span class="stat-card__value"><?= number_format($stats['user_segments']['active_referrers']) ?></span>
                                        </div>
                                    </div>
                                <div class="stat-card__footer mt-2">
                                    <small class="text-muted">
                                        Aktivite: <strong><?= $stats['users_who_refer'] > 0 ? number_format(($stats['user_segments']['active_referrers'] / $stats['users_who_refer']) * 100, 1) : '0' ?>%</strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Toplam Bonus -->
                <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div class="stat-card__content">
                                <div class="stat-card__icon-wrapper">
                                    <div class="stat-card__icon bg-warning">
                                    <i class="fas fa-coins"></i>
                                    </div>
                                </div>
                                <div class="stat-card__info">
                                    <h6 class="stat-card__title">Toplam Bonus</h6>
                                    <div class="stat-card__stats">
                                        <div class="stat-card__stat">
                                            <span class="stat-card__label">Toplam</span>
                                            <span class="stat-card__value"><?= number_format($stats['total_bonus'], 2) ?>₺</span>
                                        </div>
                                        <div class="stat-card__divider"></div>
                                        <div class="stat-card__stat">
                                            <span class="stat-card__label">Bu Ay</span>
                                            <span class="stat-card__value"><?= number_format($stats['monthly_bonus'], 2) ?>₺</span>
                                        </div>
                                    </div>
                                <div class="stat-card__footer mt-2">
                                    <small class="text-muted">
                                        İşlem: <strong><?= number_format($stats['total_transactions']) ?></strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bonus Analizi -->
                <div class="col-12 col-sm-6 col-xl-3">
                        <div class="stat-card">
                            <div class="stat-card__content">
                                <div class="stat-card__icon-wrapper">
                                    <div class="stat-card__icon bg-info">
                                    <i class="fas fa-chart-line"></i>
                                    </div>
                                </div>
                                <div class="stat-card__info">
                                    <h6 class="stat-card__title">Bonus Analizi</h6>
                                    <div class="stat-card__stats">
                                        <div class="stat-card__stat">
                                        <span class="stat-card__label">Ort. İşlem</span>
                                            <span class="stat-card__value"><?= $stats['total_transactions'] > 0 ? number_format($stats['total_bonus'] / $stats['total_transactions'], 2) : '0' ?>₺</span>
                                        </div>
                                        <div class="stat-card__divider"></div>
                                        <div class="stat-card__stat">
                                        <span class="stat-card__label">Bonus/Satış</span>
                                            <span class="stat-card__value"><?= $stats['total_sales'] > 0 ? number_format(($stats['total_bonus'] / $stats['total_sales']) * 100, 2) : '0' ?>%</span>
                                        </div>
                                    </div>
                                <div class="stat-card__footer mt-2">
                                    <small class="text-muted">
                                        Toplam Satış: <strong><?= number_format($stats['total_sales'], 2) ?>₺</strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafikler ve Tablolar -->
            <div class="row g-3 mb-4">
                <!-- Günlük/Aylık Trend Grafiği -->
                <div class="col-12 col-xl-8">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="mb-2 mb-md-0">
                                <i class="fas fa-chart-area mr-2 text-primary"></i>
                                Aktivite Trendi
                            </h6>
                            <div class="chart-toggle">
                                <button type="button" class="btn btn-sm btn-primary" onclick="showChart('daily')">
                                    <i class="fas fa-calendar-day"></i>
                                    <span class="d-none d-md-inline ml-1">Günlük</span>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="showChart('monthly')">
                                    <i class="fas fa-calendar"></i>
                                    <span class="d-none d-md-inline ml-1">Aylık</span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="dailyChart" style="display: block;"></canvas>
                                <canvas id="monthlyChart" style="display: none;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- En İyi Referans Verenler -->
                <div class="col-12 col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="fas fa-trophy mr-2 text-warning"></i>
                                En İyi Referans Verenler
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="top-referrers-list">
                            <?php if(!empty($stats['top_referrers'])): ?>
                                <?php foreach($stats['top_referrers'] as $index => $referrer): ?>
                                    <div class="referrer-item">
                                        <div class="referrer-rank">
                                            <span class="rank-badge rank-<?= $index + 1 ?>">
                                                <?= $index + 1 ?>
                                        </span>
                                    </div>
                                        <div class="referrer-info">
                                            <div class="referrer-name"><?= $referrer->name . ' ' . $referrer->surname ?></div>
                                            <div class="referrer-email"><?= $referrer->email ?></div>
                                            <div class="referrer-stats">
                                                <span class="stat-item">
                                                    <i class="fas fa-users text-muted"></i> <?= $referrer->referral_count ?>
                                                </span>
                                                <span class="stat-item">
                                                    <i class="fas fa-coins text-muted"></i> <?= number_format($referrer->total_earned, 2) ?> ₺
                                                </span>
                                        </div>
                                        </div>
                                        <div class="referrer-code">
                                            <span class="badge badge-secondary"><?= $referrer->ref_code ?></span>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                    <div class="empty-state">
                                        <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Henüz veri bulunmuyor</p>
                                </div>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kullanıcı Segmentasyonu ve Bonus Türü Dağılımı -->
            <div class="row g-3 mb-4">
                <!-- Kullanıcı Segmentasyonu -->
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="fas fa-users-cog mr-2 text-primary"></i>
                                Kullanıcı Segmentasyonu
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="segment-grid">
                                <div class="segment-item">
                                    <div class="segment-icon bg-success">
                                        <i class="fas fa-user-check"></i>
                                        </div>
                                    <div class="segment-info">
                                        <div class="segment-label">Aktif Referans Verenler</div>
                                        <div class="segment-value"><?= number_format($stats['user_segments']['active_referrers']) ?></div>
                                    </div>
                                        </div>

                                <div class="segment-item">
                                    <div class="segment-icon bg-secondary">
                                        <i class="fas fa-user-clock"></i>
                                    </div>
                                    <div class="segment-info">
                                        <div class="segment-label">Pasif Referans Verenler</div>
                                        <div class="segment-value"><?= number_format($stats['user_segments']['inactive_referrers']) ?></div>
                                        </div>
                                    </div>

                                <div class="segment-item">
                                    <div class="segment-icon bg-primary">
                                        <i class="fas fa-user-plus"></i>
                                </div>
                                    <div class="segment-info">
                                        <div class="segment-label">Yeni Kullanıcılar</div>
                                        <div class="segment-value"><?= number_format($stats['user_segments']['new_users']) ?></div>
                    </div>
                </div>

                                <div class="segment-item">
                                    <div class="segment-icon bg-info">
                                        <i class="fas fa-user-friends"></i>
                        </div>
                                    <div class="segment-info">
                                        <div class="segment-label">Yeni Referanslar</div>
                                        <div class="segment-value"><?= number_format($stats['user_segments']['new_referrals']) ?></div>
                                        </div>
                                    </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                <!-- Bonus Türü Dağılımı -->
                <div class="col-12 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-pie mr-2 text-primary"></i>
                                Bonus Türü Dağılımı
                            </h6>
                                    </div>
                        <div class="card-body">
                            <div class="bonus-distribution">
                                <!-- Kayıt Bonusu -->
                                <div class="distribution-item">
                                    <div class="distribution-header">
                                        <div class="distribution-label">
                                            <i class="fas fa-user-plus text-success mr-2"></i>
                                            Kayıt Bonusu
                                </div>
                                        <div class="distribution-value">
                                            <?= number_format($stats['bonus_type_distribution']['registration']['amount'], 2) ?> ₺
                            </div>
                                            </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: <?= $stats['total_bonus'] > 0 ? (($stats['bonus_type_distribution']['registration']['amount'] / $stats['total_bonus']) * 100) : 0 ?>%">
                                        </div>
                                    </div>
                                    <div class="distribution-meta">
                                        <span class="badge badge-success badge-pill"><?= $stats['bonus_type_distribution']['registration']['count'] ?> işlem</span>
                                        <span class="text-muted small">
                                            <?= $stats['total_bonus'] > 0 ? number_format(($stats['bonus_type_distribution']['registration']['amount'] / $stats['total_bonus']) * 100, 1) : '0' ?>%
                                        </span>
                                            </div>
                                        </div>

                                <!-- Alışveriş Bonusu -->
                                <div class="distribution-item">
                                    <div class="distribution-header">
                                        <div class="distribution-label">
                                            <i class="fas fa-shopping-cart text-primary mr-2"></i>
                                            Alışveriş Bonusu
                                    </div>
                                        <div class="distribution-value">
                                            <?= number_format($stats['bonus_type_distribution']['purchase']['amount'], 2) ?> ₺
                                </div>
                            </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                             style="width: <?= $stats['total_bonus'] > 0 ? (($stats['bonus_type_distribution']['purchase']['amount'] / $stats['total_bonus']) * 100) : 0 ?>%">
                        </div>
                    </div>
                                    <div class="distribution-meta">
                                        <span class="badge badge-primary badge-pill"><?= $stats['bonus_type_distribution']['purchase']['count'] ?> işlem</span>
                                        <span class="text-muted small">
                                            <?= $stats['total_bonus'] > 0 ? number_format(($stats['bonus_type_distribution']['purchase']['amount'] / $stats['total_bonus']) * 100, 1) : '0' ?>%
                                        </span>
                </div>
            </div>

                                <!-- Diğer Bonuslar -->
                                <?php if($stats['bonus_type_distribution']['other']['amount'] > 0): ?>
                                <div class="distribution-item">
                                    <div class="distribution-header">
                                        <div class="distribution-label">
                                            <i class="fas fa-gift text-warning mr-2"></i>
                                            Diğer Bonuslar
                        </div>
                                        <div class="distribution-value">
                                            <?= number_format($stats['bonus_type_distribution']['other']['amount'], 2) ?> ₺
                                </div>
                                </div>
                                    <div class="progress mb-2" style="height: 8px;">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                             style="width: <?= $stats['total_bonus'] > 0 ? (($stats['bonus_type_distribution']['other']['amount'] / $stats['total_bonus']) * 100) : 0 ?>%">
                            </div>
                                                </div>
                                    <div class="distribution-meta">
                                        <span class="badge badge-warning badge-pill"><?= $stats['bonus_type_distribution']['other']['count'] ?> işlem</span>
                                        <span class="text-muted small">
                                            <?= $stats['total_bonus'] > 0 ? number_format(($stats['bonus_type_distribution']['other']['amount'] / $stats['total_bonus']) * 100, 1) : '0' ?>%
                                        </span>
                                            </div>
                                        </div>
                                <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                                </div>
                                            </div>

        </div>
    </main>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Grafik değişkenleri
let dailyChart = null;
let monthlyChart = null;
let currentChart = 'daily';

// Sayfa yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

// Grafikleri başlat
function initializeCharts() {
    // Günlük grafik
    const dailyCtx = document.getElementById('dailyChart').getContext('2d');
    const dailyData = <?= json_encode($stats['daily_trends']) ?>;
    const registrationData = <?= json_encode($stats['registration_trends']) ?>;
    
    dailyChart = new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: dailyData.map(d => new Date(d.date).toLocaleDateString('tr-TR', {day: 'numeric', month: 'short'})),
            datasets: [
                {
                    label: 'Bonus (TL)',
                    data: dailyData.map(d => d.amount),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: 'İşlem Sayısı',
                    data: dailyData.map(d => d.count),
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                },
                {
                    label: 'Yeni Kayıt',
                    data: registrationData.map(d => d.count),
                    borderColor: '#f6c23e',
                    backgroundColor: 'rgba(246, 194, 62, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Bonus (TL)'
                    },
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Adet'
                    },
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 10
                }
            }
        }
    });
    
    // Aylık grafik
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyData = <?= json_encode($stats['monthly_trends']) ?>;
    
    monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: monthlyData.map(d => {
                const date = new Date(d.month + '-01');
                return date.toLocaleDateString('tr-TR', {month: 'short', year: 'numeric'});
            }),
            datasets: [
                {
                    label: 'Bonus (TL)',
                    data: monthlyData.map(d => d.amount),
                    backgroundColor: 'rgba(78, 115, 223, 0.8)',
                    borderColor: '#4e73df',
                    borderWidth: 2,
                    yAxisID: 'y'
                },
                {
                    label: 'İşlem Sayısı',
                    data: monthlyData.map(d => d.count),
                    backgroundColor: 'rgba(28, 200, 138, 0.8)',
                    borderColor: '#1cc88a',
                    borderWidth: 2,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    display: true,
                    grid: {
                        display: false
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Bonus (TL)'
                    },
                    beginAtZero: true
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'İşlem Sayısı'
                    },
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                }
            }
        }
    });
}

// Grafik değiştir
function showChart(type) {
    const buttons = document.querySelectorAll('.chart-toggle .btn');
    buttons.forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    
    if (type === 'daily') {
        document.getElementById('dailyChart').style.display = 'block';
        document.getElementById('monthlyChart').style.display = 'none';
        buttons[0].classList.remove('btn-outline-primary');
        buttons[0].classList.add('btn-primary');
    } else {
        document.getElementById('dailyChart').style.display = 'none';
        document.getElementById('monthlyChart').style.display = 'block';
        buttons[1].classList.remove('btn-outline-primary');
        buttons[1].classList.add('btn-primary');
    }
    
    currentChart = type;
}

// Tarih aralığını güncelle
function updateDateRange(period) {
    const today = new Date();
    let start, end;
    
    switch(period) {
        case 'today':
            start = end = today;
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
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            start = new Date(today.getFullYear(), quarter * 3, 1);
            end = today;
            break;
        case 'year':
            start = new Date(today.getFullYear(), 0, 1);
            end = today;
            break;
        default:
            return;
    }
    
    if (period !== 'custom') {
        document.getElementById('customDateSection').style.display = 'none';
        document.getElementById('start_date').value = formatDate(start);
        document.getElementById('end_date').value = formatDate(end);
        updateSelectedDateRange(start, end);
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

// Seçili tarih aralığını güncelle
function updateSelectedDateRange(start, end) {
    const options = {day: 'numeric', month: 'numeric', year: 'numeric'};
    const startStr = start.toLocaleDateString('tr-TR', options);
    const endStr = end.toLocaleDateString('tr-TR', options);
    document.getElementById('selectedDateRange').textContent = `${startStr} - ${endStr}`;
}

// Tarih navigasyonu
function navigateDate(direction) {
    const period = document.querySelector('input[name="period"]:checked').value;
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    const start = new Date(startDate.value);
    const end = new Date(endDate.value);
    const diff = direction === 'prev' ? -1 : 1;
    
    switch(period) {
        case 'today':
            start.setDate(start.getDate() + diff);
            end.setDate(end.getDate() + diff);
            break;
        case 'week':
            start.setDate(start.getDate() + (diff * 7));
            end.setDate(end.getDate() + (diff * 7));
            break;
        case 'month':
            start.setMonth(start.getMonth() + diff);
            end.setMonth(end.getMonth() + diff);
            break;
        case 'quarter':
            start.setMonth(start.getMonth() + (diff * 3));
            end.setMonth(end.getMonth() + (diff * 3));
            break;
        case 'year':
            start.setFullYear(start.getFullYear() + diff);
            end.setFullYear(end.getFullYear() + diff);
            break;
        default:
            const daysDiff = Math.floor((end - start) / (1000 * 60 * 60 * 24));
            start.setDate(start.getDate() + (diff * daysDiff));
            end.setDate(end.getDate() + (diff * daysDiff));
    }
    
    startDate.value = formatDate(start);
    endDate.value = formatDate(end);
    updateSelectedDateRange(start, end);
    
    document.getElementById('filterForm').submit();
}
</script>
