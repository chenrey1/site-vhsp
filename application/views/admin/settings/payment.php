<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Ödeme Ayarları</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ödeme Ayarları</li>
                </ol>
            </nav>

            <!-- Uyarı mesajları -->
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

            <!-- Ödeme Yöntemleri - EN ÜSTE TAŞINDI -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Ödeme Yöntemleri</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php 
                        if(!empty($payment_methods)): 
                            foreach($payment_methods as $method): 
                        ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card rounded-lg shadow-sm h-100 payment-card border-0 hoverable <?= $method->is_default ? 'border-left-primary' : '' ?>">
                                <div class="card-body position-relative">
                                    <?php if($method->is_default): ?>
                                    <div class="position-absolute badge badge-primary badge-pill" style="top: 10px; right: 10px;" title="Varsayılan Ödeme Yöntemi">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <?php endif; ?>

                                    <div class="d-flex align-items-center mb-3">
                                        <div class="payment-icon-wrapper mr-3">
                                            <?php if(!empty($method->icon) && file_exists(FCPATH . 'assets/img/payments/' . $method->icon)): ?>
                                                <img src="<?= base_url('assets/img/payments/' . $method->icon) ?>" alt="<?= $method->payment_name ?>" class="img-fluid payment-icon">
                                            <?php else: ?>
                                                <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <h5 class="card-title mb-0 font-weight-bold"><?= $method->payment_name ?></h5>
                                            <div class="custom-control custom-switch mt-1">
                                                <input type="checkbox" class="custom-control-input payment-status-toggle" 
                                                    id="statusToggle<?= $method->id ?>" 
                                                    data-id="<?= $method->id ?>" 
                                                    <?= $method->status == 1 ? 'checked' : '' ?>>
                                                <label class="custom-control-label small" for="statusToggle<?= $method->id ?>">
                                                    <?= $method->status == 1 ? 'Aktif' : 'Pasif' ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <p class="card-text text-muted small mb-3">
                                        <?= !empty($method->description) ? $method->description : 'Bu ödeme yöntemi için henüz açıklama eklenmemiş.' ?>
                                    </p>
                                    
                                    <div class="payment-stats mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="small text-muted">Komisyon:</span>
                                            <span class="font-weight-bold">%<?= number_format($method->commission_rate, 2) ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="small text-muted">Sıra:</span>
                                            <span><?= isset($method->display_order) ? $method->display_order : '-' ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="small text-muted">Kullanım Oranı:</span>
                                            <?php 
                                            // Veritabanından alınan gerçek kullanım oranı
                                            $usage = isset($payment_usage_stats[$method->id]) ? $payment_usage_stats[$method->id] : 0;
                                            $color = 'success';
                                            if($usage < 10) $color = 'danger';
                                            else if($usage < 20) $color = 'warning';
                                            ?>
                                            <span class="text-<?= $color ?>">%<?= number_format($usage, 1, ',', '.') ?></span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-<?= $color ?>" role="progressbar" style="width: <?= $usage ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-light pt-2 pb-2 border-0">
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-primary btn-sm edit-payment-btn" 
                                                data-toggle="modal"
                                                data-target="#editPaymentMethodModal"
                                                data-id="<?= $method->id ?>"
                                                data-name="<?= $method->payment_name ?>"
                                                data-commission="<?= $method->commission_rate ?>"
                                                data-description="<?= htmlspecialchars($method->description ?? '') ?>"
                                                data-display-order="<?= $method->display_order ?? '' ?>"
                                                data-is-default="<?= $method->is_default ?>"
                                                data-status="<?= $method->status ?>"
                                                data-config='<?= json_encode($method->config ? (is_string($method->config) ? json_decode($method->config) : $method->config) : (object)[]) ?>'>
                                            <i class="fas fa-cog"></i> Düzenle
                                        </button>
                                        
                                        <?php if(!$method->is_default): ?>
                                        <button type="button" class="btn btn-outline-success btn-sm set-default-btn" data-id="<?= $method->id ?>">
                                            <i class="fas fa-star"></i> Varsayılan Yap
                                        </button>
                                        <?php else: ?>
                                        <button type="button" class="btn btn-success btn-sm" disabled>
                                            <i class="fas fa-star"></i> Varsayılan
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            endforeach; 
                        else: 
                        ?>
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i> Henüz ödeme yöntemi bulunmuyor.
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- İstatistik Kartları -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Toplam İşlem</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₺<?= number_format($total_payment, 2, ',', '.') ?></div>
                                    <div class="text-xs <?= $monthly_change_rate >= 0 ? 'text-success' : 'text-danger' ?> mt-2">
                                        <i class="fas fa-<?= $monthly_change_rate >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i> 
                                        %<?= abs(number_format($monthly_change_rate, 1, ',', '.')) ?> geçen aya göre
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Başarılı İşlem Oranı</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">%<?= number_format($success_rate, 2, ',', '.') ?></div>
                                    <div class="text-xs <?= $weekly_rate_change >= 0 ? 'text-success' : 'text-danger' ?> mt-2">
                                        <i class="fas fa-<?= $weekly_rate_change >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i> 
                                        %<?= abs(number_format($weekly_rate_change, 1, ',', '.')) ?> geçen haftaya göre
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Günlük Ortalama İşlem</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₺<?= number_format($daily_average, 2, ',', '.') ?></div>
                                    <div class="text-xs <?= $weekly_avg_change >= 0 ? 'text-success' : 'text-danger' ?> mt-2">
                                        <i class="fas fa-<?= $weekly_avg_change >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i> 
                                        %<?= abs(number_format($weekly_avg_change, 1, ',', '.')) ?> geçen haftaya göre
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Ödeme Komisyonu</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">₺<?= number_format($total_commission, 2, ',', '.') ?></div>
                                    <div class="text-xs <?= $commission_change_rate >= 0 ? 'text-success' : 'text-danger' ?> mt-2">
                                        <i class="fas fa-<?= $commission_change_rate >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i> 
                                        %<?= abs(number_format($commission_change_rate, 1, ',', '.')) ?> geçen aya göre
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- POS Kullanım Oranları Grafiği -->
            <div class="row mb-4">
                <div class="col-lg-8 mb-4">
                    <div class="card shadow">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Ödeme Yöntemi Kullanım Oranları</h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                                    <div class="dropdown-header">Zaman Aralığı</div>
                                    <a class="dropdown-item active" href="#">Bu Ay</a>
                                    <a class="dropdown-item" href="#">Son 3 Ay</a>
                                    <a class="dropdown-item" href="#">Son 6 Ay</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Rapor İndir</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="paymentMethodChart"></canvas>
                            </div>
                            <div class="mt-4 text-center small">
                                <?php 
                                $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
                                $i = 0;
                                foreach($payment_methods as $method): 
                                    if($method->status != 1) continue;
                                    $color = $colors[$i % count($colors)];
                                    $i++;
                                ?>
                                <span class="mr-2">
                                    <i class="fas fa-circle text-<?= $color ?>"></i> <?= $method->payment_name ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Günlük İşlem Dağılımı</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-bar">
                                <canvas id="dailyTransactionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ödeme Sistemi Hakkında Bilgi Kartı -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ödeme Sistemi Hakkında</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p>Ödeme sistemi, müşterilerinize güvenli ve çeşitli ödeme seçenekleri sunmanızı sağlar.</p>
                            <p>Özellikler:</p>
                            <ul>
                                <li><strong>Çoklu Ödeme Yöntemleri:</strong> Farklı ödeme sağlayıcılarını entegre edebilirsiniz (Kredi kartı, Havale, EFT vb.)</li>
                                <li><strong>Komisyon Yönetimi:</strong> Her ödeme yöntemi için farklı komisyon oranları belirleyebilirsiniz</li>
                                <li><strong>Varsayılan Ödeme Yöntemi:</strong> Kullanıcıların en sık kullandığı ödeme yöntemini varsayılan olarak ayarlayabilirsiniz</li>
                                <li><strong>Ödeme İstatistikleri:</strong> Hangi ödeme yöntemlerinin daha çok kullanıldığını görebilirsiniz</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <p>Ödeme sistemini etkin kullanmak için:</p>
                            <ol>
                                <li>Ödeme yöntemlerinin API bilgilerini eksiksiz doldurun</li>
                                <li>Komisyon oranlarını rekabetçi ama kârlı şekilde ayarlayın</li>
                                <li>Sorun yaşanan ödeme yöntemlerini geçici olarak devre dışı bırakın</li>
                                <li>Düzenli olarak ödeme istatistiklerini kontrol edin</li>
                                <li>Kullanıcı geri bildirimlerine göre ödeme süreçlerini optimize edin</li>
                            </ol>
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Önemli:</strong> Ödeme yöntemlerini eklemek/silmek için lütfen teknik destek ekibiyle iletişime geçin.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<!-- Ödeme Yöntemi Düzenleme Modal -->
<div class="modal fade" id="editPaymentMethodModal" tabindex="-1" role="dialog" aria-labelledby="editPaymentMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editPaymentMethodModalLabel">Ödeme Yöntemi Düzenle</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('admin/settings/updatePaymentMethod') ?>" method="POST" enctype="multipart/form-data" id="editPaymentMethodForm">
                    <input type="hidden" id="edit_payment_id" name="payment_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_payment_name">Ödeme Yöntemi Adı</label>
                                <input type="text" class="form-control" id="edit_payment_name" name="payment_name" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_commission_rate">Komisyon Oranı (%) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_commission_rate" name="commission_rate" 
                                       step="0.01" min="0" max="100" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_display_order">Görüntüleme Sırası</label>
                                <input type="number" class="form-control" id="edit_display_order" name="display_order" 
                                       step="1" min="0">
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_description">Açıklama</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_icon">İkon (Resim)</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="edit_icon" name="icon" accept="image/*">
                                    <label class="custom-file-label" for="edit_icon">Resim seçin...</label>
                                </div>
                                <small class="form-text text-muted">Değiştirmek istemiyorsanız boş bırakın.</small>
                                <div id="current_icon_preview" class="mt-2"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>API Yapılandırma Bilgileri</label>
                                <div id="edit-config-fields" class="border p-3 rounded bg-light">
                                    <!-- Bu alan JavaScript ile doldurulacak -->
                                </div>
                            </div>
                            
                            <div class="form-group mt-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="edit_status" name="status">
                                    <label class="custom-control-label" for="edit_status">Aktif</label>
                                </div>
                            </div>
                            
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox" class="custom-control-input" id="edit_is_default" name="is_default">
                                <label class="custom-control-label" for="edit_is_default">Varsayılan Ödeme Yöntemi</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="submitEditPaymentForm">
                    <i class="fas fa-save mr-1"></i> Değişiklikleri Kaydet
                </button>
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

.payment-icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: rgba(0, 123, 255, 0.1);
}

.payment-icon {
    max-height: 35px;
    max-width: 35px;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.payment-card {
    overflow: hidden;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<script>
$(document).ready(function() {
    // Ödeme Yöntemi Kullanım Oranları Pasta Grafiği
    var ctx = document.getElementById("paymentMethodChart");
    var paymentMethodChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [
                <?php 
                foreach($payment_methods as $method): 
                    if($method->status != 1) continue;
                    echo '"' . $method->payment_name . '", ';
                endforeach; 
                ?>
            ],
            datasets: [{
                data: [
                    <?php 
                    foreach($payment_methods as $method): 
                        if($method->status != 1) continue;
                        echo isset($payment_usage_stats[$method->id]) ? $payment_usage_stats[$method->id] : 0;
                        echo ', ';
                    endforeach; 
                    ?>
                ],
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69'
                ],
                hoverBackgroundColor: [
                    '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#3a3b45'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: false
            },
            cutoutPercentage: 70,
        },
    });

    // Günlük İşlem Dağılımı Çubuk Grafiği
    var ctxBar = document.getElementById("dailyTransactionChart");
    var dailyTransactionChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_keys($daily_stats)) ?>,
            datasets: [{
                label: "İşlemler",
                backgroundColor: "#4e73df",
                hoverBackgroundColor: "#2e59d9",
                borderColor: "#4e73df",
                data: <?= json_encode(array_values($daily_stats)) ?>,
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return value + ' adet';
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': ' + tooltipItem.yLabel + ' adet';
                    }
                }
            }
        }
    });

    // Dosya seçildiğinde custom-file-label güncelleme
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });
    
    // Ödeme yöntemi düzenleme formunu gönder
    $('#submitEditPaymentForm').on('click', function() {
        $('#editPaymentMethodForm').submit();
    });
    
    // Ödeme yöntemi düzenleme modalını doldur - BUG FIX: Modal açılmama sorunu çözümü
    $(document).on('click', '.edit-payment-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var commission = $(this).data('commission');
        var description = $(this).data('description');
        var displayOrder = $(this).data('display-order');
        var isDefault = $(this).data('is-default');
        var status = $(this).data('status');
        var config = $(this).data('config');
        
        $('#edit_payment_id').val(id);
        $('#edit_payment_name').val(name);
        $('#edit_commission_rate').val(commission);
        $('#edit_description').val(description);
        $('#edit_display_order').val(displayOrder);
        $('#edit_status').prop('checked', status == 1);
        $('#edit_is_default').prop('checked', isDefault == 1);
        
        // İkon önizleme
        var iconPath = $(this).closest('.card').find('.payment-icon').attr('src');
        if (iconPath) {
            $('#current_icon_preview').html('<img src="' + iconPath + '" alt="Mevcut İkon" class="img-fluid mb-2" style="max-height: 40px;"><br><small class="text-muted">Mevcut ikon</small>');
        } else {
            $('#current_icon_preview').html('');
        }
        
        // Yapılandırma alanlarını temizle ve yeniden doldur
        $('#edit-config-fields').empty();
        
        try {
            // Config verilerini güvenli bir şekilde işle
            var configObj = {};
            
            // String olarak geldiyse JSON parse et
            if (typeof config === 'string') {
                try {
                    configObj = JSON.parse(config);
                } catch (parseError) {
                    console.error("JSON parse hatası:", parseError);
                    configObj = {};
                }
            } 
            // Obje olarak geldiyse doğrudan kullan
            else if (config && typeof config === 'object') {
                configObj = config;
            }
            
            // Config nesnesi var ve boş değilse, alanları oluştur
            if (configObj && typeof configObj === 'object' && Object.keys(configObj).length > 0) {
                $.each(configObj, function(key, value) {
                    $('#edit-config-fields').append(`
                        <div class="form-group mb-2">
                            <label class="mb-1 font-weight-bold">${key}</label>
                            <input type="hidden" name="config_keys[]" value="${key}">
                            <input type="text" class="form-control" name="config_values[]" value="${value}">
                        </div>
                    `);
                });
            } else {
                $('#edit-config-fields').html('<div class="alert alert-warning">Yapılandırma bilgisi bulunamadı</div>');
            }
        } catch(e) {
            console.error("Config işleme hatası:", e);
            $('#edit-config-fields').html('<div class="alert alert-danger">Yapılandırma bilgisi yüklenirken hata oluştu</div>');
        }
    });
    
    // Varsayılan yapma formu
    $('.set-default-btn').on('click', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '<?= base_url('admin/settings/setDefaultPaymentMethod') ?>',
            type: 'POST',
            data: {
                payment_id: id,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(response) {
                location.reload();
            }
        });
    });
    
    // Durum değiştirme
    $('.payment-status-toggle').on('change', function() {
        var id = $(this).data('id');
        var status = $(this).prop('checked') ? 1 : 0;
        var label = $(this).next('.custom-control-label');
        
        label.text(status == 1 ? 'Aktif' : 'Pasif');
        
        $.ajax({
            url: '<?= base_url('admin/settings/updatePaymentStatus') ?>',
            type: 'POST',
            data: {
                payment_id: id,
                status: status,
                '<?= $this->security->get_csrf_token_name() ?>': '<?= $this->security->get_csrf_hash() ?>'
            },
            success: function(response) {
                // Başarılı mesajı gösterme
            }
        });
    });
});
</script> 