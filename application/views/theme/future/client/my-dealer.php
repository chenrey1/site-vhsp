<?php if ($this->session->userdata('info')): ?>
<div class="col-lg-9">
    <div class="fp-content mb-5">
        <div class="fp-card fp-card-client mb-4">
            <div class="fp-cc-head d-flex align-items-center justify-content-between">
                <div class="cc-text">
                    <h5 class="title mb-0">Bayilik Bilgilerim</h5>
                </div>
                <?php if (isset($dealer_info) && $dealer_info): ?>
                <a href="<?= base_url('products') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="ri-shopping-cart-line me-1"></i> Bayilik İndirimiyle Alışveriş Yap
                </a>
                <?php endif; ?>
            </div>
            <?php if (isset($dealer_info) && $dealer_info): ?>
                <div class="fp-cc-body">
                    <!-- Bayilik Özet Bilgileri -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="fp-info-item fp-card">
                                <div class="content">
                                    <div class="key">Bayilik Seviyesi</div>
                                    <div class="value"><?= $dealer_info->dealer_name ?></div>
                                </div>
                                <div class="icon">
                                    <i class="ri-store-2-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fp-info-item fp-card">
                                <div class="content">
                                    <div class="key">Toplam Alım</div>
                                    <div class="value"><?= number_format($dealer_info->total_purchase, 2, ',', '.') ?> TL</div>
                                </div>
                                <div class="icon">
                                    <i class="ri-money-dollar-circle-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="fp-info-item fp-card">
                                <div class="content">
                                    <div class="key">Bayilik İndirimi</div>
                                    <div class="value">%<?= number_format($dealer_info->discount_percentage, 2, ',', '.') ?></div>
                                </div>
                                <div class="icon">
                                    <i class="ri-percent-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bayilik Detay Kartı -->
                    <div class="fp-card fp-card-client mb-4">
                        <div class="fp-cc-head">
                            <h6 class="title mb-0">Bayilik Detayları</h6>
                        </div>
                        <div class="fp-cc-body">
                            <div class="row">
                                <!-- Bayilik Bilgileri -->
                                <div class="col-md-8">
                                    <div class="dealer-details p-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="dealer-icon me-3">
                                                <i class="ri-store-2-line fs-3 text-primary"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-medium mb-0"><?= $dealer_info->dealer_name ?> Bayiliği</h6>
                                                <p class="text-muted small mb-0">Başlangıç: <?= date('d.m.Y', strtotime($dealer_info->start_date)) ?></p>
                                            </div>
                                        </div>
                                        
                                        <?php if (isset($timed_change) && $timed_change): ?>
                                        <div class="alert alert-info mb-3">
                                            <div class="d-flex align-items-center">
                                                <i class="ri-time-line fs-4 me-3 text-primary"></i>
                                                <div>
                                                    <?php 
                                                        $final_dealer_type = $this->M_Dealer->getDealerTypeById($timed_change->next_dealer_type_id); 
                                                        $change_date = new DateTime($timed_change->change_date);
                                                        $now = new DateTime();
                                                        $diff = $now->diff($change_date);
                                                        $days_left = $diff->days;
                                                    ?>
                                                    <h6 class="fw-medium mb-1">Planlı Bayilik Değişimi</h6>
                                                    <p class="small mb-0">
                                                        <span class="fw-medium"><?= date('d.m.Y', strtotime($timed_change->change_date)) ?></span> 
                                                        tarihinde (<strong><?= $days_left ?> gün</strong> sonra) 
                                                        bayiliğiniz <span class="fw-medium text-primary"><?= $final_dealer_type ? $final_dealer_type->name : '' ?></span> 
                                                        olarak güncellenecektir.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($next_dealer_type): ?>
                                        <div class="dealer-upgrade mb-4">
                                            <h6 class="fw-medium mb-2">Bir Sonraki Seviye: <?= $next_dealer_type->name ?></h6>
                                            <p class="text-muted mb-2 small">Bir üst bayiliğe geçmek için gereken alım miktarı:</p>
                                            
                                            <?php
                                            $remaining = $next_dealer_type->min_purchase_amount - $dealer_info->total_purchase;
                                            $progress = min(100, ($dealer_info->total_purchase / $next_dealer_type->min_purchase_amount) * 100);
                                            ?>
                                            
                                            <div class="progress mb-2" style="height: 10px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?= $progress ?>%" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted"><?= number_format($dealer_info->total_purchase, 2, ',', '.') ?> TL</small>
                                                <small class="text-muted"><?= $remaining > 0 ? 'Kalan: ' . number_format($remaining, 2, ',', '.') . ' TL' : '<span class="text-success">Yükseltme Şartı Sağlandı!</span>' ?></small>
                                                <small class="text-muted"><?= number_format($next_dealer_type->min_purchase_amount, 2, ',', '.') ?> TL</small>
                                            </div>
                                        </div>
                                        <?php else: ?>
                                        <div class="dealer-upgrade mb-4">
                                            <div class="alert alert-success mb-0">
                                                <div class="d-flex">
                                                    <i class="ri-award-fill me-2 fs-5"></i>
                                                    <div>
                                                        <h6 class="fw-medium mb-1">Tebrikler!</h6>
                                                        <p class="mb-0 small">En yüksek bayilik seviyesine ulaştınız.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- İşlem Geçmişi -->
                                <div class="col-md-4 border-start">
                                    <div class="px-3 py-2">
                                        <h6 class="fw-medium mb-3">Son İşlemler</h6>
                                        
                                        <?php if (!empty($dealer_history)): ?>
                                        <div class="dealer-timeline">
                                            <?php 
                                            $count = 0;
                                            foreach ($dealer_history as $history): 
                                                if ($count >= 5) break; // Son 5 işlemi göster
                                            ?>
                                            <div class="timeline-item d-flex mb-3">
                                                <div class="timeline-icon me-2">
                                                    <?php if ($history->action == 'assign'): ?>
                                                        <span class="badge bg-primary rounded-circle"><i class="ri-user-add-line"></i></span>
                                                    <?php elseif ($history->action == 'upgrade'): ?>
                                                        <span class="badge bg-success rounded-circle"><i class="ri-arrow-up-circle-line"></i></span>
                                                    <?php elseif ($history->action == 'downgrade'): ?>
                                                        <span class="badge bg-warning rounded-circle"><i class="ri-arrow-down-circle-line"></i></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger rounded-circle"><i class="ri-close-circle-line"></i></span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="timeline-content">
                                                    <div class="timeline-date small text-muted"><?= date('d.m.Y H:i', strtotime($history->created_at)) ?></div>
                                                    <p class="small mb-0">
                                                        <?php if ($history->action == 'assign'): ?>
                                                            <?= $history->new_dealer_name ?> bayiliği atandı.
                                                        <?php elseif ($history->action == 'upgrade'): ?>
                                                            <?= $history->old_dealer_name ?> → <?= $history->new_dealer_name ?> bayiliğine yükseltildi.
                                                        <?php elseif ($history->action == 'downgrade'): ?>
                                                            <?= $history->old_dealer_name ?> → <?= $history->new_dealer_name ?> bayiliğine düşürüldü.
                                                        <?php else: ?>
                                                            Bayilik iptal edildi.
                                                        <?php endif; ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <?php 
                                                $count++;
                                            endforeach; 
                                            ?>

                                            <?php if (count($dealer_history) > 5): ?>
                                            <div class="text-center mt-3">
                                                <a href="#dealerHistoryModal" data-bs-toggle="modal" class="btn btn-sm btn-outline-primary">
                                                    Tüm İşlem Geçmişini Görüntüle
                                                </a>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="ri-information-line me-2"></i> Henüz bayilik işlem geçmişi bulunmuyor.
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- API Entegrasyon Fırsatı - Reklam Alanı -->
                    <div class="fp-card fp-card-client mb-4">
                        <div class="fp-cc-body p-0">
                            <div class="dealer-promo-banner">
                                <div class="row g-0 align-items-center">
                                    <div class="col-md-4">
                                        <div class="promo-image">
                                            <div class="text-center">
                                                <i class="ri-code-s-slash-line fs-1 text-primary" aria-label="API Entegrasyonu"></i>
                                                <h5 class="promo-title mt-2 fw-medium">API Entegrasyonu</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="promo-content p-4">
                                            <h5 class="fw-bold mb-2">Kendi Satış Sitenizi Oluşturun!</h5>
                                            <p class="mb-3">Bayilik avantajlarınızı en üst düzeye çıkarın! API entegrasyonu ile ürünlerimizi kendi sitenizde otomatik olarak satışa sunabilirsiniz.</p>
                                            <div class="promo-features mb-3">
                                                <div class="feature-item d-flex align-items-center mb-2">
                                                    <i class="ri-check-line text-success me-2"></i>
                                                    <span>Otomatik ürün tedariki</span>
                                                </div>
                                                <div class="feature-item d-flex align-items-center mb-2">
                                                    <i class="ri-check-line text-success me-2"></i>
                                                    <span>Otomatik stok teslimatı</span>
                                                </div>
                                                <div class="feature-item d-flex align-items-center">
                                                    <i class="ri-check-line text-success me-2"></i>
                                                    <span>Kolay entegrasyon ve kurulum</span>
                                                </div>
                                            </div>
                                            <a href="https://wa.me/905551636237" target="_blank" class="btn btn-primary">
                                                <i class="ri-external-link-line me-2"></i> Hemen Başlayın
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bayilik Avantajları -->
                    <div class="fp-card fp-card-client">
                        <div class="fp-cc-head">
                            <h6 class="title mb-0">Bayilik Avantajları</h6>
                        </div>
                        <div class="fp-cc-body">
                            <div class="row g-4">
                                <div class="col-12 col-md-4">
                                    <div class="dealer-advantage-card text-center p-3 h-100">
                                        <div class="advantage-icon mb-3">
                                            <i class="ri-price-tag-3-line fs-4 text-primary"></i>
                                        </div>
                                        <h6 class="advantage-title mb-2">İndirimli Fiyatlar</h6>
                                        <p class="advantage-desc small mb-0">Tüm ürünlerde %<?= number_format($dealer_info->discount_percentage, 2, ',', '.') ?> indirim</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="dealer-advantage-card text-center p-3 h-100">
                                        <div class="advantage-icon mb-3">
                                            <i class="ri-customer-service-2-line fs-4 text-primary"></i>
                                        </div>
                                        <h6 class="advantage-title mb-2">Öncelikli Destek</h6>
                                        <p class="advantage-desc small mb-0">Bayilere özel ayrıcalıklı destek hizmeti</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="dealer-advantage-card text-center p-3 h-100">
                                        <div class="advantage-icon mb-3">
                                            <i class="ri-arrow-up-circle-line fs-4 text-primary"></i>
                                        </div>
                                        <h6 class="advantage-title mb-2">Otomatik Yükseltme</h6>
                                        <p class="advantage-desc small mb-0">Alım miktarınıza göre otomatik bayilik yükseltme</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="fp-cc-body">
                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <i class="ri-information-line me-3 fs-4"></i>
                            <div>
                                <h6 class="fw-medium mb-1">Henüz Bayilik Bilginiz Bulunmuyor</h6>
                                <p class="mb-0">Bayilik avantajlarından yararlanmak için aşağıdaki formu doldurarak başvuru yapabilirsiniz.</p>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (isset($has_pending_application) && $has_pending_application): ?>
                    <!-- Başvuru Değerlendiriliyor Mesajı -->
                    <div class="fp-card fp-card-client mb-4">
                        <div class="fp-cc-head">
                            <h6 class="title mb-0">Bayilik Başvurunuz</h6>
                        </div>
                        <div class="fp-cc-body">
                            <div class="alert alert-warning">
                                <div class="d-flex align-items-center">
                                    <i class="ri-time-line me-3 fs-3"></i>
                                    <div>
                                        <h6 class="fw-medium mb-1">Başvurunuz Değerlendiriliyor</h6>
                                        <p class="mb-0">Bayilik başvurunuz sistemimize ulaşmıştır ve yetkili ekibimiz tarafından incelenmektedir. En kısa sürede size dönüş yapılacaktır.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <div class="d-flex align-items-center justify-content-center mb-4">
                                    <div class="timeline-step">
                                        <div class="timeline-content">
                                            <div class="inner-circle bg-primary">
                                                <i class="ri-check-line text-white"></i>
                                            </div>
                                            <p class="mt-2 fw-medium">Başvuru Gönderildi</p>
                                        </div>
                                    </div>
                                    <div class="timeline-line"></div>
                                    <div class="timeline-step">
                                        <div class="timeline-content">
                                            <div class="inner-circle bg-warning pulse">
                                                <i class="ri-time-line text-white"></i>
                                            </div>
                                            <p class="mt-2 fw-medium">İnceleniyor</p>
                                        </div>
                                    </div>
                                    <div class="timeline-line"></div>
                                    <div class="timeline-step">
                                        <div class="timeline-content">
                                            <div class="inner-circle bg-light">
                                                <i class="ri-store-2-line text-muted"></i>
                                            </div>
                                            <p class="mt-2 fw-medium text-muted">Bayilik Aktif</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="<?= base_url('client/ticket/add') ?>" class="btn btn-outline-primary">
                                    <i class="ri-customer-service-2-line me-2"></i> Başvuru Durumu Hakkında Bilgi Al
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <!-- Bayilik Başvuru Formu -->
                    <div class="fp-card fp-card-client mb-4">
                        <div class="fp-cc-head">
                            <h6 class="title mb-0">Bayilik Başvuru Formu</h6>
                        </div>
                        <div class="fp-cc-body">
                            <form action="<?= base_url('client/submit_dealer_application') ?>" method="post">
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="company_name" class="form-label">Firma Adı <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="company_name" name="company_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="website" class="form-label">Web Sitesi</label>
                                        <input type="url" class="form-control" id="website" name="website" placeholder="<?=base_url()?>">
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="tax_number" class="form-label">Vergi Numarası</label>
                                        <input type="text" class="form-control" id="tax_number" name="tax_number">
                                        <small class="text-muted">Bireysel başvurularda doldurmanız gerekmez</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tax_office" class="form-label">Vergi Dairesi</label>
                                        <input type="text" class="form-control" id="tax_office" name="tax_office">
                                        <small class="text-muted">Bireysel başvurularda doldurmanız gerekmez</small>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="estimated_revenue" class="form-label">Tahmini Aylık Ciro</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="estimated_revenue" name="estimated_revenue" placeholder="0.00" min="0" step="1000">
                                            <span class="input-group-text">TL</span>
                                        </div>
                                        <small class="text-muted">Ortalama aylık ciro tahmininiz</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="address" class="form-label">Adres <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Eklemek İstediğiniz Bilgiler</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Bayilik başvurunuzla ilgili eklemek istediğiniz detaylar..."></textarea>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ri-store-2-line me-2"></i> Bayilik Başvurusu Yap
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Bayilik Avantajları -->
                    <div class="fp-card fp-card-client">
                        <div class="fp-cc-head">
                            <h6 class="title mb-0">Bayilik Avantajları</h6>
                        </div>
                        <div class="fp-cc-body">
                            <div class="row g-4">
                                <div class="col-12 col-md-4">
                                    <div class="dealer-advantage-card text-center p-3 h-100">
                                        <div class="advantage-icon mb-3">
                                            <i class="ri-price-tag-3-line fs-4 text-primary"></i>
                                        </div>
                                        <h6 class="advantage-title mb-2">İndirimli Fiyatlar</h6>
                                        <p class="advantage-desc small mb-0">Tüm ürünlerde özel bayilik indirimleri</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="dealer-advantage-card text-center p-3 h-100">
                                        <div class="advantage-icon mb-3">
                                            <i class="ri-vip-crown-line fs-4 text-primary"></i>
                                        </div>
                                        <h6 class="advantage-title mb-2">Özel Fırsatlar</h6>
                                        <p class="advantage-desc small mb-0">Bayilere özel kampanya ve fırsatlar</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="dealer-advantage-card text-center p-3 h-100">
                                        <div class="advantage-icon mb-3">
                                            <i class="ri-customer-service-2-line fs-4 text-primary"></i>
                                        </div>
                                        <h6 class="advantage-title mb-2">Öncelikli Destek</h6>
                                        <p class="advantage-desc small mb-0">Bayilere özel ayrıcalıklı destek hizmeti</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="<?= base_url('client/ticket/add') ?>" class="btn btn-primary">
                            <i class="ri-customer-service-2-line me-2"></i> Bayilik Bilgisi İçin İletişime Geçin
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tüm İşlem Geçmişi Modal -->
<?php if (isset($dealer_info) && $dealer_info && !empty($dealer_history)): ?>
<div class="modal fade" id="dealerHistoryModal" tabindex="-1" aria-labelledby="dealerHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dealerHistoryModalLabel">Bayilik İşlem Geçmişi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive fp-table-border">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>İşlem</th>
                                <th>Detay</th>
                                <th>Tarih</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dealer_history as $history): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if ($history->action == 'assign'): ?>
                                            <span class="badge bg-primary me-2"><i class="ri-user-add-line"></i></span>
                                            <span>Bayilik Atama</span>
                                        <?php elseif ($history->action == 'upgrade'): ?>
                                            <span class="badge bg-success me-2"><i class="ri-arrow-up-circle-line"></i></span>
                                            <span>Bayilik Yükseltme</span>
                                        <?php elseif ($history->action == 'downgrade'): ?>
                                            <span class="badge bg-warning me-2"><i class="ri-arrow-down-circle-line"></i></span>
                                            <span>Bayilik Düşürme</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger me-2"><i class="ri-close-circle-line"></i></span>
                                            <span>Bayilik İptali</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($history->action == 'assign'): ?>
                                        <?= $history->new_dealer_name ?> bayiliği atandı.
                                    <?php elseif ($history->action == 'upgrade'): ?>
                                        <?= $history->old_dealer_name ?> → <?= $history->new_dealer_name ?> bayiliğine yükseltildi.
                                    <?php elseif ($history->action == 'downgrade'): ?>
                                        <?= $history->old_dealer_name ?> → <?= $history->new_dealer_name ?> bayiliğine düşürüldü.
                                    <?php else: ?>
                                        Bayilik iptal edildi.
                                    <?php endif; ?>
                                    <?php if (!empty($history->description)): ?>
                                    <p class="text-muted small mt-1 mb-0"><?= $history->description ?></p>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d.m.Y H:i', strtotime($history->created_at)) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>
</div>
</div>
</section> 