<div id="layoutSidenav_content" class="referrals-page">
    <main>
        <div class="container-fluid">
            <!-- Başlık ve Breadcrumb -->
            <div class="page-title d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <i class="fas fa-chart-line text-primary mr-2"></i>
                    <h5 class="mb-0">Referans - Canlı</h5>
                    <span class="badge badge-success ml-3 pulse-badge">
                        <i class="fas fa-circle pulse-dot"></i>
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    <small class="text-muted mr-3 d-none d-md-inline">
                        Son Güncelleme: <strong id="lastUpdate">--:--:--</strong>
                    </small>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshAllData()" title="Yenile">
                        <i class="fas fa-sync-alt"></i>
                        <span class="d-none d-md-inline ml-1">Yenile</span>
                    </button>
                </div>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/referrals/settings'); ?>">Referans Ayarları</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>

            <!-- Zaman Aralığı Filtresi -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-3">
                    <div class="time-filter-wrapper">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <i class="fas fa-clock text-muted mr-2"></i>
                            <span class="font-weight-500 mr-3 d-none d-sm-inline">Zaman Aralığı:</span>
                        </div>
                        <div class="time-buttons" id="globalTimeFilter">
                            <button type="button" class="btn btn-sm btn-primary" data-range="1h">1 Saat</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-range="6h">6 Saat</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-range="24h">1 Gün</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-range="7d">7 Gün</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-range="30d">30 Gün</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ana Metrikler (4 Kart) -->
            <div class="row g-3 mb-4">
                <!-- Yeni Referanslar -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card__content">
                            <div class="stat-card__icon-wrapper">
                                <div class="stat-card__icon bg-primary">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                            </div>
                            <div class="stat-card__info">
                                <h6 class="stat-card__title">Yeni Referanslar</h6>
                                <div class="stat-card__stats">
                                    <div class="stat-card__stat">
                                        <span class="stat-card__label">Şimdi</span>
                                        <span class="stat-card__value" id="newReferralsTotal">
                                            <span class="skeleton-text">...</span>
                                        </span>
                                    </div>
                                    <div class="stat-card__divider"></div>
                                    <div class="stat-card__stat">
                                        <span class="stat-card__label">Önceki</span>
                                        <span class="stat-card__value" id="newReferralsPrevious">
                                            <span class="skeleton-text">...</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ödenen Bonuslar -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card__content">
                            <div class="stat-card__icon-wrapper">
                                <div class="stat-card__icon bg-success">
                                    <i class="fas fa-coins"></i>
                                </div>
                            </div>
                            <div class="stat-card__info">
                                <h6 class="stat-card__title">Ödenen Bonuslar</h6>
                                <div class="stat-card__stats">
                                    <div class="stat-card__stat">
                                        <span class="stat-card__label">Şimdi</span>
                                        <span class="stat-card__value" id="bonusPaidTotal">
                                            <span class="skeleton-text">...</span>
                                        </span>
                                    </div>
                                    <div class="stat-card__divider"></div>
                                    <div class="stat-card__stat">
                                        <span class="stat-card__label">Önceki</span>
                                        <span class="stat-card__value" id="bonusPaidPrevious">
                                            <span class="skeleton-text">...</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dönüşüm Oranı -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card__content">
                            <div class="stat-card__icon-wrapper">
                                <div class="stat-card__icon bg-warning">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                            <div class="stat-card__info">
                                <h6 class="stat-card__title">Dönüşüm Oranı</h6>
                                <div class="stat-card__stats">
                                    <div class="stat-card__stat">
                                        <span class="stat-card__label">Kayıt</span>
                                        <span class="stat-card__value" id="totalReferrals">
                                            <span class="skeleton-text">...</span>
                                        </span>
                                    </div>
                                    <div class="stat-card__divider"></div>
                                    <div class="stat-card__stat">
                                        <span class="stat-card__label">Alışveriş</span>
                                        <span class="stat-card__value" id="purchaseReferrals">
                                            <span class="skeleton-text">...</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="stat-card__footer mt-2">
                                    <small class="text-muted">Oran: <strong id="conversionRate">0%</strong></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ROI -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card">
                        <div class="stat-card__content">
                            <div class="stat-card__icon-wrapper">
                                <div class="stat-card__icon bg-info">
                                    <i class="fas fa-balance-scale"></i>
                                </div>
                            </div>
                            <div class="stat-card__info">
                                <h6 class="stat-card__title">ROI Performansı</h6>
                                <div class="stat-card__stats">
                                    <div class="stat-card__stat">
                                        <span class="stat-card__label">Bonus</span>
                                        <span class="stat-card__value" id="totalBonusPaid">
                                            <span class="skeleton-text">...</span>
                                        </span>
                                    </div>
                                    <div class="stat-card__divider"></div>
                                    <div class="stat-card__stat">
                                        <span class="stat-card__label">Gelir</span>
                                        <span class="stat-card__value" id="totalRevenue">
                                            <span class="skeleton-text">...</span>
                                        </span>
                                    </div>
                                </div>
                                <div class="stat-card__footer mt-2">
                                    <small class="text-muted">ROI: <strong id="bonusROI">Veri Yok</strong></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik ve Aktiviteler -->
            <div class="row g-3 mb-4">
                <!-- Gerçek Zamanlı Grafik -->
                <div class="col-12 col-xl-8">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="mb-0 mb-md-0">
                                <i class="fas fa-chart-area mr-2 text-primary"></i>
                                Gerçek Zamanlı Aktivite
                            </h6>
                            <div class="d-flex align-items-center mt-2 mt-md-0">
                                <label class="mb-0 mr-2 text-muted small d-none d-md-inline">Görünüm:</label>
                                <select class="form-control form-control-sm" id="chartType" onchange="updateChartType()" style="width: auto; min-width: 120px;">
                                    <option value="combined" selected>Tümü</option>
                                    <option value="referrals">Referanslar</option>
                                    <option value="bonuses">Bonuslar</option>
                                    <option value="revenue">Gelir</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="realtimeChart"></canvas>
                            </div>
                            <!-- Grafik İstatistikleri -->
                            <div class="chart-stats mt-3">
                                <div class="row text-center g-2">
                                    <div class="col-6 col-sm-3">
                                        <div class="chart-stat-item">
                                            <div class="chart-stat-value text-primary" id="chartAverage">0</div>
                                            <small class="text-muted">Ortalama</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="chart-stat-item">
                                            <div class="chart-stat-value text-success" id="chartPeak">0</div>
                                            <small class="text-muted">En Yüksek</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="chart-stat-item">
                                            <div class="chart-stat-value text-warning" id="chartTotal">0</div>
                                            <small class="text-muted">Toplam</small>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-3">
                                        <div class="chart-stat-item">
                                            <div class="chart-stat-value text-info" id="chartTrend">+0%</div>
                                            <small class="text-muted">Trend</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Son Aktiviteler -->
                <div class="col-12 col-xl-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-history mr-2 text-primary"></i>
                                Son Aktiviteler
                            </h6>
                            <span class="badge badge-pill badge-primary small">24 Saat</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="activity-feed" id="activityFeed">
                                <div class="activity-loading">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="sr-only">Yükleniyor...</span>
                                    </div>
                                    <div class="mt-2 text-muted small">Yükleniyor...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Haftanın Enleri -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h6 class="mb-0">
                            <i class="fas fa-trophy mr-2 text-warning"></i>
                            Haftanın Enleri
                        </h6>
                        <div class="weekly-controls">
                            <div class="week-display mb-2 mb-md-0">
                                <span class="week-label d-none d-lg-inline" id="weekLabel">Bu Hafta</span>
                                <span class="week-dates" id="weekRange">--</span>
                            </div>
                            <div class="week-buttons">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeWeek(-1)" title="Önceki Hafta">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="changeWeek(1)" title="Sonraki Hafta" id="nextWeekBtn">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" onclick="refreshWeeklyData()" title="Yenile">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading State -->
                    <div id="weeklyLoadingState" class="text-center py-4">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status">
                            <span class="sr-only">Yükleniyor...</span>
                        </div>
                        <div class="text-muted">Haftalık veriler yükleniyor...</div>
                    </div>

                    <!-- Winners Grid -->
                    <div class="row g-3 d-none" id="winnersGrid">
                        <!-- Referans Şampiyonu -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="winner-card border-start-primary">
                                <div class="winner-icon">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                </div>
                                <h6 class="winner-title">👑 Referans Şampiyonu</h6>
                                <div class="winner-name" id="championName">
                                    <span class="loading-text">Yükleniyor...</span>
                                </div>
                                <div class="winner-stats">
                                    <div class="winner-stat">
                                        <div class="stat-value" id="championCount">--</div>
                                        <div class="stat-label">Referans</div>
                                    </div>
                                    <div class="winner-stat">
                                        <div class="stat-value text-muted" id="championPrevious">--</div>
                                        <div class="stat-label">Önceki</div>
                                    </div>
                                </div>
                                <div class="winner-trend">
                                    <span class="badge badge-success" id="championTrend">
                                        <i class="fas fa-arrow-up"></i> +0%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- En Çok Kazanan -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="winner-card border-start-success">
                                <div class="winner-icon">
                                    <div class="icon-circle bg-success">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                </div>
                                <h6 class="winner-title">💰 En Çok Kazanan</h6>
                                <div class="winner-name" id="earnerName">
                                    <span class="loading-text">Yükleniyor...</span>
                                </div>
                                <div class="winner-stats">
                                    <div class="winner-stat">
                                        <div class="stat-value" id="earnerAmount">--</div>
                                        <div class="stat-label">₺ Kazanç</div>
                                    </div>
                                    <div class="winner-stat">
                                        <div class="stat-value text-muted" id="earnerPrevious">--</div>
                                        <div class="stat-label">Önceki</div>
                                    </div>
                                </div>
                                <div class="winner-trend">
                                    <span class="badge badge-success" id="earnerTrend">
                                        <i class="fas fa-arrow-up"></i> +0%
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Yükselen Yıldız -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="winner-card border-start-warning">
                                <div class="winner-icon">
                                    <div class="icon-circle bg-warning">
                                        <i class="fas fa-rocket"></i>
                                    </div>
                                </div>
                                <h6 class="winner-title">🚀 Yükselen Yıldız</h6>
                                <div class="winner-name" id="risingName">
                                    <span class="loading-text">Yükleniyor...</span>
                                </div>
                                <div class="winner-stats">
                                    <div class="winner-stat">
                                        <div class="stat-value" id="risingGrowth">--</div>
                                        <div class="stat-label">% Artış</div>
                                    </div>
                                    <div class="winner-stat">
                                        <div class="stat-value text-muted" id="risingRate">--</div>
                                        <div class="stat-label">Yeni</div>
                                    </div>
                                </div>
                                <div class="winner-trend">
                                    <span class="badge badge-warning" id="risingTrend">
                                        <i class="fas fa-rocket"></i> Yükseliş
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- En İstikrarlı -->
                        <div class="col-12 col-md-6 col-xl-3">
                            <div class="winner-card border-start-info">
                                <div class="winner-icon">
                                    <div class="icon-circle bg-info">
                                        <i class="fas fa-medal"></i>
                                    </div>
                                </div>
                                <h6 class="winner-title">🎯 En İstikrarlı</h6>
                                <div class="winner-name" id="consistentName">
                                    <span class="loading-text">Yükleniyor...</span>
                                </div>
                                <div class="winner-stats">
                                    <div class="winner-stat">
                                        <div class="stat-value" id="consistentDays">--</div>
                                        <div class="stat-label">Gün</div>
                                    </div>
                                    <div class="winner-stat">
                                        <div class="stat-value text-muted" id="consistentDaily">--</div>
                                        <div class="stat-label">Günlük Ort.</div>
                                    </div>
                                </div>
                                <div class="winner-trend">
                                    <span class="badge badge-info" id="consistentTrend">
                                        <i class="fas fa-check-circle"></i> İstikrar
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Data Message -->
                    <div id="noDataMessage" class="text-center py-5 d-none">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted mb-2">Bu Hafta İçin Veri Bulunamadı</h5>
                        <p class="text-muted mb-0">
                            Henüz referans aktivitesi gerçekleşmemiş.<br>
                            <small>Veri oluştuğunda burada otomatik olarak görünecek.</small>
                        </p>
                    </div>

                    <!-- Bilgi Satırı -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded flex-wrap">
                                <small class="text-muted mb-2 mb-md-0">
                                    <i class="fas fa-info-circle text-info mr-1"></i>
                                    <strong>Haftalık dönem:</strong> Pazartesi - Pazar.
                                    <span id="weeklyUpdateTime">Son güncelleme: --:--:--</span>
                                </small>
                                <small class="text-muted">
                                    Toplam katılımcı: <strong id="totalParticipants">--</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Global değişkenler
let autoRefreshInterval = null;
let currentTimeRange = '1h';
let currentChartType = 'combined';
let realtimeChart = null;
let currentWeekOffset = 0;

// Sayfa yüklendiğinde başlat
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
});

// Dashboard'u başlat
function initializeDashboard() {
    initializeTimeFilter();
    initializeChart();
    loadInitialData();
    initializeWeeklyData();
    startAutoRefresh();
    updateLastUpdateTime();
}

// Global zaman filtresi başlat
function initializeTimeFilter() {
    const buttons = document.querySelectorAll('#globalTimeFilter button');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            buttons.forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-secondary');
            });
            this.classList.remove('btn-outline-secondary');
            this.classList.add('btn-primary');
            currentTimeRange = this.getAttribute('data-range');
            refreshAllData();
        });
    });
}

// Grafik başlatma
function initializeChart() {
    const ctx = document.getElementById('realtimeChart').getContext('2d');
    realtimeChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Referanslar',
                    data: [],
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: 'Bonuslar (TL)',
                    data: [],
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                },
                {
                    label: 'Gelir (TL)',
                    data: [],
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
                    title: {
                        display: true,
                        text: 'Zaman'
                    },
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
                        text: 'Referans Sayısı'
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
                        text: 'Tutar (TL)'
                    },
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false,
                    },
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
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: '#ddd',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: true
                }
            },
            animation: {
                duration: 750
            }
        }
    });
}

// İlk veri yükleme
function loadInitialData() {
    fetchRealtimeData();
    fetchRecentActivities();
}

// Tüm verileri yenile
function refreshAllData() {
    fetchRealtimeData();
    fetchRecentActivities();
    updateLastUpdateTime();
}

// Gerçek zamanlı veri çekme
function fetchRealtimeData() {
    const params = new URLSearchParams({
        timeRange: currentTimeRange,
        chartType: currentChartType
    });
    
    fetch('<?= base_url("admin/referrals/get_realtime_data") ?>?' + params)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateMetrics(data.metrics);
                updateChart(data.chart_data);
            }
        })
        .catch(error => console.error('Veri çekme hatası:', error));
}

// Son aktiviteleri çek
function fetchRecentActivities() {
    fetch('<?= base_url("admin/referrals/get_recent_activities") ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateActivityFeed(data.activities);
            }
        })
        .catch(error => console.error('Aktivite çekme hatası:', error));
}

// Metrikleri güncelle
function updateMetrics(metrics) {
    document.getElementById('newReferralsTotal').textContent = metrics.new_referrals_total || 0;
    document.getElementById('newReferralsPrevious').textContent = metrics.new_referrals_previous || 0;
    document.getElementById('bonusPaidTotal').textContent = (metrics.bonus_paid_total || 0).toFixed(2) + ' TL';
    document.getElementById('bonusPaidPrevious').textContent = (metrics.bonus_paid_previous || 0).toFixed(2) + ' TL';
    document.getElementById('totalReferrals').textContent = metrics.total_referrals || 0;
    document.getElementById('purchaseReferrals').textContent = metrics.purchase_referrals || 0;
    
    // Dönüşüm oranı
    const totalRefs = metrics.total_referrals || 0;
    const purchaseRefs = metrics.purchase_referrals || 0;
    const conversionRate = totalRefs > 0 ? ((purchaseRefs / totalRefs) * 100).toFixed(1) + '%' : '0%';
    document.getElementById('conversionRate').textContent = conversionRate;
    
    document.getElementById('totalBonusPaid').textContent = (metrics.total_bonus_paid || 0).toFixed(2) + ' TL';
    document.getElementById('totalRevenue').textContent = (metrics.total_revenue || 0).toFixed(2) + ' TL';
    
    // ROI
    const bonusPaid = metrics.total_bonus_paid || 0;
    const revenue = metrics.total_revenue || 0;
    let roiText = 'Veri Yok';
    let roiClass = 'text-muted';
    
    if (bonusPaid > 0) {
        const roi = ((revenue - bonusPaid) / bonusPaid * 100);
        if (roi > 0) {
            roiText = '+' + roi.toFixed(1) + '% Karlı';
            roiClass = 'text-success';
        } else if (roi < 0) {
            roiText = roi.toFixed(1) + '% Zarar';
            roiClass = 'text-danger';
        } else {
            roiText = 'Başabaş';
            roiClass = 'text-warning';
        }
    } else if (revenue > 0 && bonusPaid === 0) {
        roiText = '∞% Karlı';
        roiClass = 'text-success';
    }
    
    const roiElement = document.getElementById('bonusROI');
    roiElement.textContent = roiText;
    roiElement.className = 'font-weight-bold ' + roiClass;
}

// Grafiği güncelle
function updateChart(chartData) {
    if (!realtimeChart || !chartData) return;
    
    realtimeChart.data.labels = chartData.labels || [];
    
    if (currentChartType === 'combined') {
        realtimeChart.data.datasets[0].data = chartData.referrals || [];
        realtimeChart.data.datasets[1].data = chartData.bonuses || [];
        realtimeChart.data.datasets[2].data = chartData.revenue || [];
        realtimeChart.data.datasets[0].hidden = false;
        realtimeChart.data.datasets[1].hidden = false;
        realtimeChart.data.datasets[2].hidden = false;
    } else {
        realtimeChart.data.datasets[0].data = chartData[currentChartType] || [];
        realtimeChart.data.datasets[1].data = [];
        realtimeChart.data.datasets[2].data = [];
        realtimeChart.data.datasets[0].hidden = false;
        realtimeChart.data.datasets[1].hidden = true;
        realtimeChart.data.datasets[2].hidden = true;
    }
    
    realtimeChart.update('none');
    updateChartStats(chartData);
}

// Grafik istatistiklerini güncelle
function updateChartStats(chartData) {
    if (!chartData) return;
    
    let dataArray = currentChartType === 'combined' ? 
        (chartData.referrals || []) : (chartData[currentChartType] || []);
    
    if (dataArray.length === 0) {
        document.getElementById('chartAverage').textContent = '0';
        document.getElementById('chartPeak').textContent = '0';
        document.getElementById('chartTotal').textContent = '0';
        document.getElementById('chartTrend').textContent = '0%';
        return;
    }
    
    const total = dataArray.reduce((sum, val) => sum + val, 0);
    const average = total / dataArray.length;
    const peak = Math.max(...dataArray);
    const previousTotal = dataArray.slice(0, -1).reduce((sum, val) => sum + val, 0);
    const trend = previousTotal > 0 ? ((total - previousTotal) / previousTotal * 100) : 0;
    
    document.getElementById('chartAverage').textContent = Math.round(average);
    document.getElementById('chartPeak').textContent = peak;
    document.getElementById('chartTotal').textContent = total;
    document.getElementById('chartTrend').textContent = (trend >= 0 ? '+' : '') + trend.toFixed(1) + '%';
}

// Aktivite beslemesini güncelle
function updateActivityFeed(activities) {
    const feed = document.getElementById('activityFeed');
    
    if (!activities || activities.length === 0) {
        feed.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                <div class="text-muted">Henüz aktivite bulunmuyor</div>
            </div>
        `;
        return;
    }
    
    let html = '';
    activities.forEach(activity => {
        const iconClass = getActivityIcon(activity.type);
        const timeAgo = formatTimeAgo(activity.created_at);
        
        html += `
            <div class="activity-item">
                <div class="d-flex align-items-start">
                    <div class="activity-icon mr-3">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="activity-content flex-grow-1">
                        <div>
                            <strong>${activity.user_name || 'Sistem'}</strong>
                            <span class="text-muted">${activity.description}</span>
                        </div>
                        <div class="activity-meta">
                            <small class="text-muted">
                                <i class="fas fa-clock mr-1"></i>
                                ${timeAgo}
                            </small>
                            ${activity.amount ? `<span class="badge badge-success ml-2">${activity.amount} TL</span>` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    feed.innerHTML = html;
}

// Aktivite tipine göre ikon döndür
function getActivityIcon(type) {
    const icons = {
        'referral': 'fas fa-user-plus text-primary',
        'bonus': 'fas fa-coins text-success',
        'payment': 'fas fa-credit-card text-info',
        'suspicious': 'fas fa-exclamation-triangle text-warning',
        'error': 'fas fa-times-circle text-danger',
        'system': 'fas fa-cog text-secondary'
    };
    return icons[type] || 'fas fa-info-circle text-info';
}

// Zaman farkını formatla
function formatTimeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);
    
    if (diffMins < 1) return 'Az önce';
    if (diffMins < 60) return diffMins + ' dakika önce';
    if (diffHours < 24) return diffHours + ' saat önce';
    return diffDays + ' gün önce';
}

// Grafik türünü güncelle
function updateChartType() {
    const select = document.getElementById('chartType');
    currentChartType = select.value;
    fetchRealtimeData();
}

// Otomatik yenilemeyi başlat
function startAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    autoRefreshInterval = setInterval(() => {
        refreshAllData();
    }, 10000); // 10 saniye
}

// Son güncelleme zamanını güncelle
function updateLastUpdateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('tr-TR');
    document.getElementById('lastUpdate').textContent = timeString;
}

// Haftalık veriler
function initializeWeeklyData() {
    updateWeekDisplay();
    loadWeeklyWinners();
}

function changeWeek(direction) {
    if (direction === 1 && currentWeekOffset >= 0) return;
    currentWeekOffset += direction;
    document.getElementById('nextWeekBtn').disabled = currentWeekOffset >= 0;
    updateWeekDisplay();
    loadWeeklyWinners();
}

function updateWeekDisplay() {
    const now = new Date();
    const dayOfWeek = now.getDay() === 0 ? 6 : now.getDay() - 1;
    const currentMonday = new Date(now);
    currentMonday.setDate(now.getDate() - dayOfWeek);
    
    const targetMonday = new Date(currentMonday);
    targetMonday.setDate(currentMonday.getDate() + (currentWeekOffset * 7));
    
    const targetSunday = new Date(targetMonday);
    targetSunday.setDate(targetMonday.getDate() + 6);
    
    const options = { day: 'numeric', month: 'short' };
    const mondayStr = targetMonday.toLocaleDateString('tr-TR', options);
    const sundayStr = targetSunday.toLocaleDateString('tr-TR', options);
    
    // Tarih aralığını güncelle
    document.getElementById('weekRange').textContent = `${mondayStr} - ${sundayStr}`;
    
    // Label'ı güncelle
    const weekLabel = document.getElementById('weekLabel');
    if (currentWeekOffset === 0) {
        weekLabel.textContent = 'Bu Hafta';
    } else if (currentWeekOffset === -1) {
        weekLabel.textContent = 'Geçen Hafta';
    } else {
        const weeksAgo = Math.abs(currentWeekOffset);
        weekLabel.textContent = `${weeksAgo} Hafta Önce`;
    }
}

function loadWeeklyWinners() {
    showLoadingState();
    
    fetch('<?= base_url("admin/referrals/get_weekly_winners") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ week_offset: currentWeekOffset })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateWinners(data.winners);
                updateWeeklyStats(data.stats);
            } else {
                showNoDataMessage();
            }
            updateWeeklyUpdateTime();
        })
        .catch(error => {
            console.error('Haftalık veri yükleme hatası:', error);
            showNoDataMessage();
        });
}

function showLoadingState() {
    document.getElementById('weeklyLoadingState').classList.remove('d-none');
    document.getElementById('winnersGrid').classList.add('d-none');
    document.getElementById('noDataMessage').classList.add('d-none');
}

function showNoDataMessage() {
    document.getElementById('weeklyLoadingState').classList.add('d-none');
    document.getElementById('winnersGrid').classList.add('d-none');
    document.getElementById('noDataMessage').classList.remove('d-none');
}

function updateWinners(winners) {
    const isEmpty = !winners || 
                   (Array.isArray(winners) && winners.length === 0) ||
                   (typeof winners === 'object' && Object.keys(winners).length === 0);
    
    if (isEmpty) {
        showNoDataMessage();
        return;
    }
    
    let hasAnyWinner = false;
    
    // Referans Şampiyonu
    if (winners.champion && winners.champion.name) {
        updateWinner('champion', winners.champion);
        hasAnyWinner = true;
    } else {
        updateWinner('champion', {});
    }
    
    // En Çok Kazanan
    if (winners.earner && winners.earner.name) {
        updateWinner('earner', winners.earner);
        hasAnyWinner = true;
    } else {
        updateWinner('earner', {});
    }
    
    // Yükselen Yıldız
    if (winners.rising && winners.rising.name) {
        updateWinner('rising', winners.rising);
        hasAnyWinner = true;
    } else {
        updateWinner('rising', {});
    }
    
    // En İstikrarlı
    if (winners.consistent && winners.consistent.name) {
        updateWinner('consistent', winners.consistent);
        hasAnyWinner = true;
    } else {
        updateWinner('consistent', {});
    }
    
    if (!hasAnyWinner) {
        showNoDataMessage();
    } else {
        document.getElementById('weeklyLoadingState').classList.add('d-none');
        document.getElementById('winnersGrid').classList.remove('d-none');
        document.getElementById('noDataMessage').classList.add('d-none');
    }
}

function updateWinner(type, data) {
    const nameElement = document.getElementById(type + 'Name');
    const trendElement = document.getElementById(type + 'Trend');
    
    if (data.name) {
        nameElement.textContent = data.name + ' ' + (data.surname || '');
        
        switch(type) {
            case 'champion':
                document.getElementById('championCount').textContent = data.referral_count || 0;
                document.getElementById('championPrevious').textContent = (data.previous_week || 0);
                updateTrendBadge(trendElement, data.trend_percent || 0);
                break;
            case 'earner':
                document.getElementById('earnerAmount').textContent = (data.total_earned || 0) + ' ₺';
                document.getElementById('earnerPrevious').textContent = (data.previous_week || 0) + ' ₺';
                updateTrendBadge(trendElement, data.trend_percent || 0);
                break;
            case 'rising':
                document.getElementById('risingGrowth').textContent = data.growth_percent || 0;
                document.getElementById('risingRate').textContent = (data.new_referrals || 0);
                trendElement.innerHTML = '<i class="fas fa-rocket"></i> Yükseliş';
                trendElement.className = 'badge badge-warning';
                break;
            case 'consistent':
                document.getElementById('consistentDays').textContent = data.active_days || 0;
                document.getElementById('consistentDaily').textContent = (data.daily_average || 0);
                trendElement.innerHTML = '<i class="fas fa-check-circle"></i> İstikrar';
                trendElement.className = 'badge badge-info';
                break;
        }
    } else {
        nameElement.innerHTML = '<span class="text-muted">Veri yok</span>';
        trendElement.innerHTML = '<i class="fas fa-minus"></i> --';
        trendElement.className = 'badge badge-secondary';
        
        if (type === 'champion') {
            document.getElementById('championCount').textContent = '0';
            document.getElementById('championPrevious').textContent = '0';
        } else if (type === 'earner') {
            document.getElementById('earnerAmount').textContent = '0 ₺';
            document.getElementById('earnerPrevious').textContent = '0 ₺';
        } else if (type === 'rising') {
            document.getElementById('risingGrowth').textContent = '0';
            document.getElementById('risingRate').textContent = '0';
        } else if (type === 'consistent') {
            document.getElementById('consistentDays').textContent = '0';
            document.getElementById('consistentDaily').textContent = '0';
        }
    }
}

function updateTrendBadge(element, trendPercent) {
    const trend = parseFloat(trendPercent) || 0;
    
    if (trend > 0) {
        element.innerHTML = `<i class="fas fa-arrow-up"></i> +${trend}%`;
        element.className = 'badge badge-success';
    } else if (trend < 0) {
        element.innerHTML = `<i class="fas fa-arrow-down"></i> ${trend}%`;
        element.className = 'badge badge-danger';
    } else {
        element.innerHTML = '<i class="fas fa-minus"></i> Sabit';
        element.className = 'badge badge-secondary';
    }
}

function updateWeeklyStats(stats) {
    document.getElementById('totalParticipants').textContent = stats.total_participants || 0;
}

function updateWeeklyUpdateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('tr-TR');
    document.getElementById('weeklyUpdateTime').textContent = 'Son güncelleme: ' + timeString;
}

function refreshWeeklyData() {
    loadWeeklyWinners();
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    icon.classList.add('fa-spin');
    setTimeout(() => {
        icon.classList.remove('fa-spin');
    }, 1000);
}
</script>
