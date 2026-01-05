<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Kullanıcı Bayilik Geçmişi</h4>
                <a href="<?= base_url('admin/dealer/dealerUsers'); ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-left mr-1"></i> Bayilik Kullanıcılarına Dön
                </a>
            </div>

            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dealer'); ?>">Bayilik Tipleri</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dealer/dealerUsers'); ?>">Bayilik Kullanıcıları</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bayilik Geçmişi</li>
                </ol>
            </nav>

            <div class="row">
                <!-- Kullanıcı Bilgileri -->
                <div class="col-lg-4">
                    <div class="card mb-4 h-100 border-primary border-top-0 border-right-0 border-bottom-0 border-left-3">
                        <div class="card-header">
                            <i class="fas fa-user mr-1 text-primary"></i>
                            <span class="font-weight-bold">Kullanıcı Bilgileri</span>
                        </div>
                        <div class="card-body p-3">
                            <div class="text-center mb-4">
                                <?php 
                                // Kullanıcının adının ve soyadının ilk harflerini alıyoruz
                                $initials = mb_substr($user->name, 0, 1) . mb_substr($user->surname, 0, 1); 
                                ?>
                                <div class="initials-avatar mx-auto mb-2 bg-primary">
                                    <?= strtoupper($initials) ?>
                                </div>
                                <h5 class="mt-3"><?= $user->name ?> <?= $user->surname ?></h5>
                                <p class="text-muted"><i class="fas fa-envelope mr-1"></i><?= $user->email ?></p>
                            </div>
                            
                            <div class="user-info-container bg-light rounded p-3 mb-4 border">
                                <dl class="row mb-0">
                                    <dt class="col-sm-6"><i class="fas fa-id-card text-primary mr-1"></i> Müşteri ID:</dt>
                                    <dd class="col-sm-6 text-right">#<?= $user->id ?></dd>
                                    
                                    <dt class="col-sm-6"><i class="fas fa-phone text-primary mr-1"></i> Telefon:</dt>
                                    <dd class="col-sm-6 text-right"><?= $user->phone ?? '-' ?></dd>
                                    
                                    <dt class="col-sm-6"><i class="fas fa-calendar text-primary mr-1"></i> Kayıt Tarihi:</dt>
                                    <dd class="col-sm-6 text-right"><?= date('d.m.Y', strtotime($user->register_date ?? date('Y-m-d'))) ?></dd>
                                    
                                    <dt class="col-sm-6"><i class="fas fa-sign-in-alt text-primary mr-1"></i> Son Giriş:</dt>
                                    <dd class="col-sm-6 text-right"><?= $user->last_login ?? '-' ?></dd>
                                    
                                    <dt class="col-sm-6"><i class="fas fa-wallet text-primary mr-1"></i> Bakiye:</dt>
                                    <dd class="col-sm-6 text-right font-weight-bold text-primary"><?= number_format($user->balance, 2, ',', '.') ?> TL</dd>
                                </dl>
                            </div>
                            
                            <h6 class="border-bottom pb-2"><i class="fas fa-store mr-1"></i> Mevcut Bayilik Durumu</h6>
                            <?php if ($dealer_info): ?>
                                <div class="alert alert-light border mb-3">
                                    <h6 class="mb-2 text-primary"><?= $dealer_info->dealer_name ?></h6>
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Başlangıç</small>
                                            <span><?= date('d.m.Y', strtotime($dealer_info->start_date)) ?></span>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Toplam Alım</small>
                                            <span class="text-primary font-weight-bold"><?= number_format($dealer_info->total_purchase, 2, ',', '.') ?> TL</span>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">İndirim</small>
                                            <span class="text-primary font-weight-bold">%<?= $dealer_info->discount_percentage ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <p class="mb-0">Bu kullanıcıya atanmış bayilik bulunmuyor.</p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Özet Ciro İstatistikleri -->
                            <?php if (!empty($monthly_earnings)): ?>
                                <h6 class="border-bottom pb-2 mt-4"><i class="fas fa-chart-line mr-1"></i> Ciro Özeti</h6>
                                <div class="user-info-container bg-light rounded p-3 mb-4 border">
                                    <?php
                                        // Mevcut ay toplam tutarı
                                        $current_month_total = 0;
                                        $current_month_count = 0;
                                        
                                        if (!empty($current_month_earnings)) {
                                            $current_month_total = $current_month_earnings[0]->total_amount;
                                            $current_month_count = $current_month_earnings[0]->transaction_count;
                                        }
                                        
                                        // Aylık ortalama hesaplaması
                                        $month_count = count($monthly_earnings);
                                        $monthly_average = $month_count > 0 ? ($yearly_earnings / $month_count) : 0;
                                    ?>
                                    <dl class="row mb-0">
                                        <dt class="col-sm-7"><i class="fas fa-calendar-day text-primary mr-1"></i> Bu Ay Toplam:</dt>
                                        <dd class="col-sm-5 text-right font-weight-bold"><?= number_format($current_month_total, 0, ',', '.') ?> TL</dd>
                                        
                                        <dt class="col-sm-7"><i class="fas fa-calculator text-success mr-1"></i> Aylık Ortalama:</dt>
                                        <dd class="col-sm-5 text-right font-weight-bold"><?= number_format($monthly_average, 0, ',', '.') ?> TL</dd>
                                        
                                        <dt class="col-sm-7"><i class="fas fa-calendar-alt text-info mr-1"></i> Son 12 Ay:</dt>
                                        <dd class="col-sm-5 text-right font-weight-bold"><?= number_format($yearly_earnings, 0, ',', '.') ?> TL</dd>
                                        
                                        <?php if ($current_month_count > 0): ?>
                                        <dt class="col-sm-7"><i class="fas fa-exchange-alt text-secondary mr-1"></i> Bu Ay İşlem:</dt>
                                        <dd class="col-sm-5 text-right">
                                            <span class="badge badge-primary rounded-pill px-2 py-1"><?= $current_month_count ?> adet</span>
                                        </dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Planlı Bayilik Değişimi Bilgisi -->
                            <?php if (isset($timed_change) && $timed_change): ?>
                                <div class="alert alert-info mt-4 mb-4">
                                    <h6 class="mb-1"><i class="fas fa-clock mr-1"></i> Planlı Bayilik Değişimi</h6>
                                    <hr class="my-2">
                                    <?php 
                                        $final_dealer_type = $this->M_Dealer->getDealerTypeById($timed_change->next_dealer_type_id); 
                                        $change_date = new DateTime($timed_change->change_date);
                                        $now = new DateTime();
                                        $diff = $now->diff($change_date);
                                        $days_left = $diff->days;
                                    ?>
                                    <p class="small mb-1">
                                        <strong>Hedef Bayilik:</strong> <?= $final_dealer_type ? $final_dealer_type->name : '?' ?>
                                    </p>
                                    <p class="small mb-1">
                                        <strong>Değişim Tarihi:</strong> <?= date('d.m.Y', strtotime($timed_change->change_date)) ?>
                                        <span class="text-muted">(<?= $days_left ?> gün kaldı)</span>
                                    </p>
                                    <div class="mt-2">
                                        <a href="<?= base_url('admin/dealer/cancelTimedChange/'.$user->id) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Planlı değişimi iptal etmek istediğinize emin misiniz?')">
                                            <i class="fas fa-times mr-1"></i> İptal Et
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-4">
                                <a href="#assignDealerModal" data-toggle="modal" class="btn btn-primary btn-block">
                                    <i class="fas fa-exchange-alt mr-1"></i> Bayilik Ata/Değiştir
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ciro Analizi -->
                <div class="col-lg-8">
                    <?php if (!empty($monthly_earnings)): ?>
                        <?php if ($revenue_targets): ?>
                        <!-- Ciro Hedefleri ve Karşılaştırma -->
                        <div class="card border mb-4 shadow-sm">
                            <div class="card-header bg-white d-flex align-items-center">
                                <i class="fas fa-bullseye text-primary mr-2 fa-lg"></i>
                                <h6 class="mb-0 font-weight-bold">Ciro Hedefleri ve Gerçekleşme</h6>
                            </div>
                            <div class="card-body">
                                <!-- Hedef Özeti -->
                                <div class="alert alert-light border rounded mb-4">
                                    <div class="row">
                                        <div class="col-md-6 text-center border-right">
                                            <h6 class="text-muted mb-1">Aylık Hedef</h6>
                                            <h2 class="text-primary mb-0"><?= number_format($revenue_targets->monthly_target, 0, ',', '.') ?> ₺</h2>
                                        </div>
                                        <div class="col-md-6 text-center">
                                            <h6 class="text-muted mb-1">Günlük Hedef</h6>
                                            <h2 class="text-primary mb-0"><?= number_format($revenue_targets->daily_target, 0, ',', '.') ?> ₺</h2>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- İlerlemeler -->
                                <div class="row mb-3">
                                    <!-- Aylık İlerleme -->
                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 bg-white rounded border">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 text-muted">Aylık İlerleme</h6>
                                                <h5 class="mb-0 text-<?= $revenue_targets->monthly_completion_rate >= $revenue_targets->expected_monthly_completion ? 'success' : 'warning' ?>">
                                                    <?= number_format($revenue_targets->monthly_completion_rate, 1) ?>%
                                                </h5>
                                            </div>
                                            
                                            <div class="progress mb-2" style="height: 12px; background-color: #f2f2f2;">
                                                <div class="progress-bar bg-<?= $revenue_targets->monthly_completion_rate >= $revenue_targets->expected_monthly_completion ? 'success' : 'warning' ?>" 
                                                     role="progressbar" 
                                                     style="width: <?= min($revenue_targets->monthly_completion_rate, 100) ?>%;" 
                                                     aria-valuenow="<?= $revenue_targets->monthly_completion_rate ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between small text-muted">
                                                <span>
                                                    <i class="fas fa-circle text-success mr-1"></i> Gerçekleşen: 
                                                    <strong><?= number_format($revenue_targets->monthly_earnings, 0, ',', '.') ?> TL</strong>
                                                </span>
                                                <span class="text-<?= $revenue_targets->monthly_completion_rate >= $revenue_targets->expected_monthly_completion ? 'success' : 'warning' ?>">
                                                    <?= $revenue_targets->monthly_completion_rate >= $revenue_targets->expected_monthly_completion ? "İyi gidiyor" : "Geride" ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Günlük İlerleme -->
                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 bg-white rounded border">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <h6 class="mb-0 text-muted">Günlük İlerleme</h6>
                                                <h5 class="mb-0 text-<?= $revenue_targets->daily_completion_rate >= 100 ? 'success' : 'warning' ?>">
                                                    <?= number_format($revenue_targets->daily_completion_rate, 1) ?>%
                                                </h5>
                                            </div>
                                            
                                            <div class="progress mb-2" style="height: 12px; background-color: #f2f2f2;">
                                                <div class="progress-bar bg-<?= $revenue_targets->daily_completion_rate >= 100 ? 'success' : 'warning' ?>" 
                                                     role="progressbar" 
                                                     style="width: <?= min($revenue_targets->daily_completion_rate, 100) ?>%;" 
                                                     aria-valuenow="<?= $revenue_targets->daily_completion_rate ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between small text-muted">
                                                <span>
                                                    <i class="fas fa-circle text-success mr-1"></i> Bugün: 
                                                    <strong><?= number_format($revenue_targets->daily_earnings, 0, ',', '.') ?> TL</strong>
                                                </span>
                                                <span class="text-<?= $revenue_targets->daily_completion_rate >= 100 ? 'success' : 'warning' ?>">
                                                    <?= $revenue_targets->daily_completion_rate >= 100 ? "Hedef aşıldı" : "Henüz tamamlanmadı" ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Tahmin ve Öneriler -->
                                <div class="bg-light p-3 rounded border">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="mr-3">
                                            <span class="bg-info rounded-circle d-inline-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                                <i class="fas fa-chart-line fa-lg"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 font-weight-bold">Ay Sonu Tahmini ve Öneriler</h6>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2 bg-white rounded border">
                                                <div class="small text-muted mb-1">Ay Sonu Tahmin</div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong class="text-<?= $revenue_targets->monthly_projection >= $revenue_targets->monthly_target ? 'success' : 'danger' ?> h5 mb-0">
                                                        <?= number_format($revenue_targets->monthly_projection, 0, ',', '.') ?> TL
                                                    </strong>
                                                    <span class="badge badge-<?= $revenue_targets->monthly_projection >= $revenue_targets->monthly_target ? 'success' : 'danger' ?> px-2 py-1">
                                                        <?= number_format(($revenue_targets->monthly_projection / $revenue_targets->monthly_target) * 100, 0) ?>% 
                                                        <i class="fas fa-<?= $revenue_targets->monthly_projection >= $revenue_targets->monthly_target ? 'check' : 'exclamation' ?>-circle ml-1"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2 bg-white rounded border">
                                                <div class="small text-muted mb-1">Günlük Yapılması Gereken</div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <?php
                                                    $remaining_days = max(1, $revenue_targets->total_days_in_month - $revenue_targets->current_day);
                                                    $daily_target_remaining = ($revenue_targets->monthly_target - $revenue_targets->monthly_earnings) / $remaining_days;
                                                    ?>
                                                    <strong class="text-primary h5 mb-0">
                                                        <?= number_format($daily_target_remaining, 0, ',', '.') ?> TL
                                                    </strong>
                                                    <span class="small text-muted">
                                                        Kalan <?= $remaining_days ?> gün için
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="small text-muted mt-2">
                                        <i class="fas fa-info-circle mr-1"></i> 
                                        Bugün ayın <strong><?= $revenue_targets->current_day ?></strong>. günü 
                                        (<?= number_format(($revenue_targets->current_day / $revenue_targets->total_days_in_month) * 100, 0) ?>% tamamlandı). 
                                        Ay sonunda hedef gerçekleşme tahmini: 
                                        <strong class="text-<?= $revenue_targets->monthly_projection >= $revenue_targets->monthly_target ? 'success' : 'danger' ?>">
                                        <?= $revenue_targets->monthly_projection >= $revenue_targets->monthly_target ? 'Hedefe Ulaşılacak' : 'Hedefe Ulaşılamayacak' ?>
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Aylık Ciro Tablosu -->
                        <div class="card shadow-sm border">
                            <div class="card-header bg-white">
                                <h6 class="mb-0 font-weight-bold">
                                    <i class="fas fa-table text-primary mr-1"></i>
                                    Aylık Ciro Detayları
                                </h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="border-top-0">Ay/Yıl</th>
                                                <th class="border-top-0 text-center">İşlem</th>
                                                <th class="border-top-0 text-right">Tutar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($monthly_earnings as $earning): ?>
                                                <?php 
                                                $date = new DateTime($earning->month_year . '-01');
                                                $month_name = $date->format('F Y');
                                                // Türkçe ay adları
                                                $tr_months = [
                                                    'January' => 'Ocak',
                                                    'February' => 'Şubat',
                                                    'March' => 'Mart',
                                                    'April' => 'Nisan',
                                                    'May' => 'Mayıs',
                                                    'June' => 'Haziran',
                                                    'July' => 'Temmuz',
                                                    'August' => 'Ağustos',
                                                    'September' => 'Eylül',
                                                    'October' => 'Ekim',
                                                    'November' => 'Kasım',
                                                    'December' => 'Aralık'
                                                ];
                                                
                                                $month = explode(' ', $month_name)[0];
                                                $year = explode(' ', $month_name)[1];
                                                $tr_month_name = $tr_months[$month] . ' ' . $year;
                                                ?>
                                                <tr>
                                                    <td class="align-middle">
                                                        <div class="d-flex align-items-center">
                                                            <span class="bg-light rounded p-2 mr-2 text-primary">
                                                                <i class="fas fa-calendar-alt"></i>
                                                            </span>
                                                            <div>
                                                                <span class="font-weight-medium"><?= $tr_month_name ?></span>
                                                                <div class="small text-muted"><?= $earning->month_year ?></div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center align-middle">
                                                        <span class="badge badge-soft-primary rounded-pill px-3 py-2">
                                                            <i class="fas fa-exchange-alt mr-1"></i>
                                                            <?= $earning->transaction_count ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-right align-middle">
                                                        <span class="font-weight-bold h5 mb-0"><?= number_format($earning->total_amount, 0, ',', '.') ?></span>
                                                        <small class="text-muted">TL</small>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle mb-2 fa-2x"></i>
                                    <p class="mb-0">Bu kullanıcı için henüz başarılı bir satın alım işlemi bulunmuyor.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Bayilik Geçmişi -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-white">
                            <i class="fas fa-history mr-1 text-primary"></i>
                            <span class="font-weight-bold">Bayilik İşlem Geçmişi</span>
                        </div>
                        <div class="card-body p-3">
                            <?php if (isset($dealer_history) && !empty($dealer_history)): ?>
                                <div class="modern-timeline">
                                    <?php foreach ($dealer_history as $history): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-badge bg-<?= $history->action == 'assign' ? 'success' : ($history->action == 'upgrade' ? 'primary' : ($history->action == 'downgrade' ? 'warning' : 'danger')) ?>">
                                                <i class="fas fa-<?= $history->action == 'assign' ? 'check' : ($history->action == 'upgrade' ? 'arrow-up' : ($history->action == 'downgrade' ? 'arrow-down' : 'times')) ?>"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span class="badge badge-<?= $history->action == 'assign' ? 'success' : ($history->action == 'upgrade' ? 'primary' : ($history->action == 'downgrade' ? 'warning' : 'danger')) ?>">
                                                        <?php if ($history->action == 'assign'): ?>
                                                            <i class="fas fa-check-circle mr-1"></i> Atama
                                                        <?php elseif ($history->action == 'upgrade'): ?>
                                                            <i class="fas fa-arrow-up mr-1"></i> Yükseltme
                                                        <?php elseif ($history->action == 'downgrade'): ?>
                                                            <i class="fas fa-arrow-down mr-1"></i> İndirgeme
                                                        <?php else: ?>
                                                            <i class="fas fa-times-circle mr-1"></i> İptal
                                                        <?php endif; ?>
                                                    </span>
                                                    <small class="text-muted"><?= date('d.m.Y H:i', strtotime($history->created_at)) ?></small>
                                                </div>
                                                
                                                <div class="title mb-2">
                                                    <?php if (isset($history->old_dealer_name) && $history->old_dealer_name): ?>
                                                        <span class="text-muted"><?= $history->old_dealer_name ?></span>
                                                        <i class="fas fa-long-arrow-alt-right mx-2 text-primary"></i>
                                                    <?php endif; ?>
                                                    <strong class="text-primary"><?= $history->new_dealer_name ?></strong>
                                                </div>
                                                
                                                <div class="description mb-2">
                                                    <?= $history->description ? $history->description : 'Açıklama yok' ?>
                                                </div>
                                                
                                                <div class="small text-muted">
                                                    <i class="fas fa-user mr-1"></i>
                                                    <?php if ($history->performed_by_name): ?>
                                                        <?= $history->performed_by_name ?> <?= $history->performed_by_surname ?> tarafından gerçekleştirildi
                                                    <?php else: ?>
                                                        Sistem (Otomatik) tarafından gerçekleştirildi
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle mb-2"></i>
                                    <p class="mb-0">Bu kullanıcı için bayilik işlem geçmişi bulunamadı.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bayilik Ata/Değiştir Modal -->
    <div class="modal fade" id="assignDealerModal" tabindex="-1" role="dialog" aria-labelledby="assignDealerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignDealerModalLabel">
                        <i class="fas fa-store mr-2 text-primary"></i>Bayilik Ata/Değiştir: <?= $user->name ?> <?= $user->surname ?>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-3">
                    <form action="<?= base_url('admin/dealer/assignDealer') ?>" method="post">
                        <input type="hidden" name="user_id" value="<?= $user->id ?>">
                        
                        <div class="form-group">
                            <label for="dealer_type_id">Bayilik Tipi</label>
                            <select class="form-control" id="dealer_type_id" name="dealer_type_id" required>
                                <?php foreach ($dealer_types as $type): ?>
                                    <option value="<?= $type->id ?>" <?= ($type->id == $dealer_info->dealer_type_id) ? 'selected' : '' ?>><?= $type->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Değişiklik Açıklaması</label>
                            <textarea class="form-control" id="description" name="description" rows="2" placeholder="Bayilik değişikliği için açıklama"></textarea>
                        </div>
                        
                        <!-- Süreli Bayilik Ataması -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input enable-timed-dealer" id="enable_timed_dealer" name="enable_timed_dealer" value="1">
                                <label class="custom-control-label" for="enable_timed_dealer">Süreli bayilik ataması yap</label>
                            </div>
                            <small class="form-text text-muted">Bu seçenek ile belirli süre sonra otomatik bayilik değişimi yapılabilir.</small>
                        </div>
                        
                        <div class="timed-dealer-settings" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dealer_period">Süre (Gün)</label>
                                        <input type="number" class="form-control" id="dealer_period" name="dealer_period" min="1" value="30">
                                        <small class="form-text text-muted">Kaç gün sonra bayilik tipi değiştirilecek</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="final_dealer_type_id">Son Bayilik Tipi</label>
                                        <select class="form-control" id="final_dealer_type_id" name="final_dealer_type_id">
                                            <option value="">Seçiniz</option>
                                            <?php foreach ($dealer_types as $type): ?>
                                                <option value="<?= $type->id ?>"><?= $type->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="form-text text-muted">Süre sonunda geçilecek bayilik tipi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-primary">Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        // DataTable
        $('#historyTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Turkish.json"
            },
            "order": [[0, "desc"]]
        });
        
        // Süreli bayilik ayarları görünürlüğü
        $('.enable-timed-dealer').change(function() {
            if($(this).is(':checked')) {
                $('.timed-dealer-settings').slideDown();
            } else {
                $('.timed-dealer-settings').slideUp();
            }
        });
    });
</script> 