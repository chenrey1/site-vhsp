<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!-- Son Katılınan Çekiliş Bildirimi -->
<?php if (isset($last_joined_draw)): ?>
<div class="container mt-4">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-check-circle me-1"></i>
        <strong>"<?= htmlspecialchars($last_joined_draw['name']) ?>"</strong> çekilişine başarıyla katıldınız!
        <div class="mt-1 small">
            <i class="fa-regular fa-calendar-check me-1"></i> Çekiliş bitiş tarihi: <?= date('d.m.Y H:i', strtotime($last_joined_draw['end_time'])) ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Kapat"></button>
    </div>
</div>
<?php endif; ?>

<div class="container py-4">
    <div class="alert alert-info" style="background:#1f1f1f;color:#fff;border:1px solid #282828;">
        <strong>Bilgi:</strong> Çekilişlere katılmak için giriş yapmalısınız. Kazananlar çekiliş bitiminde otomatik olarak belirlenir ve ödüller (bakiye veya ürün) hesabınıza aktarılır. Kazandığınız ödülleri <b>Çekiliş Ödülleri</b> sayfasından takip edebilirsiniz.
    </div>
    <h2 class="mb-4 text-center fw-bold"><i class="fa-solid fa-gift"></i> Çekilişler</h2>
    <ul class="nav nav-tabs mb-4" id="drawTabs" role="tablist">
        <li class="nav-item"><a class="nav-link active" id="active-tab" data-bs-toggle="tab" href="#active"
                role="tab">Aktif Çekilişler</a></li>
        <li class="nav-item"><a class="nav-link" id="finished-tab" data-bs-toggle="tab" href="#finished"
                role="tab">Biten Çekilişler</a></li>
    </ul>
    
    <div class="tab-content">
        <!-- Aktif Çekilişler -->
        <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
            <?php if (empty($draws)): ?>
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle me-2"></i> Şu anda aktif çekiliş bulunmuyor. Lütfen daha sonra tekrar kontrol edin.
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($draws as $draw): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm">
                                <!-- Çekiliş Görseli -->
                                <?php if (!empty($draw->image)): ?>
                                    <img src="<?= base_url($draw->image) ?>" class="card-img-top" alt="<?= htmlspecialchars($draw->name) ?>" style="height: 180px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                        <i class="fa-solid fa-gift fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title fw-bold mb-2"><?= htmlspecialchars($draw->name) ?></h5>
                                    <?php if (!empty($draw->description)): ?>
                                        <div class="mb-2 small" style="color:#828282">
                                            <?= mb_substr(strip_tags($draw->description), 0, 100, 'UTF-8') ?>...
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Ödül Bilgisi -->
                                    <div class="mb-3">
                                        <div class="small fw-bold mb-1">Ödüller:</div>
                                        <?php if (!empty($draw->rewards)): ?>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php foreach ($draw->rewards as $reward): ?>
                                                    <?php if ($reward->type == 'bakiye'): ?>
                                                        <span class="badge bg-primary">
                                                            <i class="fa-solid fa-coins me-1"></i> <?= number_format($reward->amount, 2, ',', '.') ?> TL
                                                            <?php if ($reward->winner_count > 1): ?>
                                                                <small>(<?= $reward->winner_count ?> kişi)</small>
                                                            <?php endif; ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <?php
                                                        // Ürün adını göster
                                                        $product_name = isset($reward->product_name) ? $reward->product_name : null;
                                                        
                                                        // Eğer product_name yoksa ama product_id varsa, veritabanından çek
                                                        if (empty($product_name) && isset($reward->product_id)) {
                                                            $product = $this->db->where('id', $reward->product_id)->get('product')->row();
                                                            if ($product) {
                                                                $product_name = $product->name;
                                                            } else {
                                                                $product_name = 'Ürün #'.$reward->product_id;
                                                            }
                                                        } else if (empty($product_name)) {
                                                            $product_name = 'Ürün';
                                                        }
                                                        ?>
                                                        <span class="badge bg-success">
                                                            <i class="fa-solid fa-gift me-1"></i> 
                                                            <?= htmlspecialchars($product_name) ?>
                                                            <?php if ($reward->winner_count > 1): ?>
                                                                <small>(<?= $reward->winner_count ?> kişi)</small>
                                                            <?php endif; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Ödül Bilgisi Yok</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="small text-muted">
                                            <i class="fa-solid fa-users me-1"></i> <?= $draw->participant_count ?> Katılımcı
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fa-regular fa-clock me-1"></i> 
                                            <?php
                                            $end_time = strtotime($draw->end_time);
                                            $now = time();
                                            $diff = $end_time - $now;
                                            
                                            if ($diff < 0) {
                                                echo 'Sona Erdi';
                                            } elseif ($diff < 3600) {
                                                echo ceil($diff / 60) . ' dakika';
                                            } elseif ($diff < 86400) {
                                                echo ceil($diff / 3600) . ' saat';
                                            } else {
                                                echo ceil($diff / 86400) . ' gün';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto d-flex gap-2">
                                        <a href="<?= base_url('client/draw_detail/' . $draw->id) ?>" class="btn btn-sm btn-outline-primary w-50">
                                            <i class="fa-solid fa-eye me-1"></i> Detay
                                        </a>
                                        
                                        <?php if ($draw->joined): ?>
                                            <button class="btn btn-sm btn-success w-50" disabled>
                                                <i class="fa-solid fa-check-circle me-1"></i> Katıldınız
                                            </button>
                                        <?php else: ?>
                                            <form action="<?= base_url('client/join_draw/' . $draw->id) ?>" method="post" class="w-50">
                                                <input type="hidden" name="csrf_token" value="<?= $this->security->get_csrf_hash() ?>">
                                                <button type="submit" class="btn btn-sm btn-primary w-100">
                                                    <i class="fa-solid fa-check-circle me-1"></i> Katıl
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent small text-muted">
                                    <i class="fa-regular fa-calendar me-1"></i> Bitiş: <?= date('d.m.Y H:i', strtotime($draw->end_time)) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Biten Çekilişler -->
        <div class="tab-pane fade" id="finished" role="tabpanel" aria-labelledby="finished-tab">
            <?php if (empty($past_draws)): ?>
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle me-2"></i> Henüz sonlanmış çekiliş bulunmuyor.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Çekiliş</th>
                                <th>Ödüller</th>
                                <th>Katılımcı</th>
                                <th>Bitiş Tarihi</th>
                                <th>Katılım</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($past_draws as $draw): ?>
                                <tr>
                                    <td><?= htmlspecialchars($draw->name) ?></td>
                                    <td>
                                        <?php if (!empty($draw->rewards)): ?>
                                            <?php foreach ($draw->rewards as $index => $reward): ?>
                                                <?php if ($index > 0) echo ', '; ?>
                                                <?php if ($reward->type == 'bakiye'): ?>
                                                    <span class="badge bg-primary">
                                                        <?= number_format($reward->amount, 2, ',', '.') ?> TL
                                                        <?php if ($reward->winner_count > 1): ?>
                                                            <small>(<?= $reward->winner_count ?> kişi)</small>
                                                        <?php endif; ?>
                                                    </span>
                                                <?php else: ?>
                                                    <?php
                                                    // Ürün adını göster
                                                    $product_name = isset($reward->product_name) ? $reward->product_name : null;
                                                    
                                                    // Eğer product_name yoksa ama product_id varsa, veritabanından çek
                                                    if (empty($product_name) && isset($reward->product_id)) {
                                                        $product = $this->db->where('id', $reward->product_id)->get('product')->row();
                                                        if ($product) {
                                                            $product_name = $product->name;
                                                        } else {
                                                            $product_name = 'Ürün #'.$reward->product_id;
                                                        }
                                                    } else if (empty($product_name)) {
                                                        $product_name = 'Ürün';
                                                    }
                                                    ?>
                                                    <span class="badge bg-success">
                                                        <i class="fa-solid fa-gift me-1"></i> 
                                                        <?= htmlspecialchars($product_name) ?>
                                                        <?php if ($reward->winner_count > 1): ?>
                                                            <small>(<?= $reward->winner_count ?> kişi)</small>
                                                        <?php endif; ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $draw->participant_count ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($draw->end_time)) ?></td>
                                    <td>
                                        <?php if ($draw->joined): ?>
                                            <span class="badge bg-success">Katıldınız</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Katılmadınız</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('client/draw_detail/' . $draw->id) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fa-solid fa-eye me-1"></i> Detay
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Bootstrap tab aktivasyonu
        const triggerTabList = [].slice.call(document.querySelectorAll('#drawTabs a'));
        triggerTabList.forEach(function (triggerEl) {
            const tabTrigger = new bootstrap.Tab(triggerEl);
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });
        
        // URL'de tab parametresi varsa onu aktifleştir
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'finished') {
            const finishedTab = document.getElementById('finished-tab');
            if (finishedTab) {
                const tabInstance = new bootstrap.Tab(finishedTab);
                tabInstance.show();
            }
        }
    });
</script> 
