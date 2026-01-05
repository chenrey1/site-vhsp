<?php $this->load->view('admin/includes/header'); ?>
<?php $this->load->view('admin/includes/sidebar'); ?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mt-4"><?= htmlspecialchars($draw->name) ?></h1>
                <div>
                    <?php if ($draw->status == 1): ?>
                        <a href="/admin/draw/edit/<?= $draw->id ?>" class="btn btn-primary me-2">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="/admin/draw/finish/<?= $draw->id ?>" class="btn btn-warning me-2 finish-draw">
                            <i class="fas fa-trophy"></i> Çekilişi Bitir
                        </a>
                    <?php endif; ?>
                    <a href="/admin/draw/index" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Geri
                    </a>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-xl-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-info-circle me-1"></i>
                            Çekiliş Bilgileri
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <h5 class="text-muted mb-2">Durum</h5>
                                        <?php 
                                        $now = time();
                                        $start_time = strtotime($draw->start_time);
                                        $end_time = strtotime($draw->end_time);
                                        
                                        if ($draw->status == 1) {
                                            if ($now < $start_time) {
                                                $status_text = 'Yakında Başlayacak';
                                                $status_class = 'bg-info';
                                            } elseif ($now > $end_time) {
                                                $status_text = 'Süresi Doldu';
                                                $status_class = 'bg-warning text-dark';
                                            } else {
                                                $status_text = 'Aktif';
                                                $status_class = 'bg-success';
                                            }
                                        } elseif ($draw->status == 2) {
                                            $status_text = 'Tamamlandı';
                                            $status_class = 'bg-primary';
                                        } else {
                                            $status_text = 'Silindi';
                                            $status_class = 'bg-danger';
                                        }
                                        ?>
                                        <div>
                                            <span class="badge <?= $status_class ?> fs-6 px-3 py-2"><?= $status_text ?></span>
                                            <?php if ($draw->status == 1 && $now < $end_time): ?>
                                                <div class="mt-2 small text-muted countdown" data-end="<?= $draw->end_time ?>"></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h5 class="text-muted mb-2">Tarih Bilgileri</h5>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-1"><strong>Başlangıç</strong></div>
                                                <div><?= date('d.m.Y H:i', strtotime($draw->start_time)) ?></div>
                                            </div>
                                            <div class="col-6">
                                                <div class="mb-1"><strong>Bitiş</strong></div>
                                                <div><?= date('d.m.Y H:i', strtotime($draw->end_time)) ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if (!empty($draw->description)): ?>
                                    <div class="mb-3">
                                        <h5 class="text-muted mb-2">Açıklama</h5>
                                        <p><?= nl2br(htmlspecialchars($draw->description)) ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6">
                                    <?php if (!empty($draw->image)): ?>
                                    <div class="mb-3">
                                        <h5 class="text-muted mb-2">Görsel</h5>
                                        <img src="<?= base_url($draw->image) ?>" alt="<?= htmlspecialchars($draw->name) ?>" class="img-fluid rounded">
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <h5 class="text-muted mb-2">Katılımcı Bilgileri</h5>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="display-6"><?= isset($participants) ? count($participants) : 0 ?></div>
                                                <div class="text-muted">Toplam Katılımcı</div>
                                            </div>
                                            <?php if(isset($draw->max_participants) && $draw->max_participants > 0): ?>
                                            <div>
                                                <div class="progress" style="height: 10px; width: 100px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?= min(100, (count($participants) / $draw->max_participants) * 100) ?>%" aria-valuenow="<?= count($participants) ?>" aria-valuemin="0" aria-valuemax="<?= $draw->max_participants ?>"></div>
                                                </div>
                                                <div class="small text-muted mt-1 text-center"><?= count($participants) ?>/<?= $draw->max_participants ?></div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-trophy me-1"></i>
                            Çekiliş Ödülleri
                        </div>
                        <div class="card-body">
                            <?php if (empty($rewards)): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i> Bu çekiliş için ödül tanımlanmamış!
                                </div>
                            <?php else: ?>
                                <ul class="list-group">
                                    <?php foreach ($rewards as $reward): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <?php if ($reward->type == 'bakiye'): ?>
                                                        <div class="bg-success text-white rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                            <i class="fas fa-coins fa-2x"></i>
                                                        </div>
                                                    <?php else: ?>
                                                        <?php 
                                                        $product = $this->db->where('id', $reward->product_id)->get('product')->row();
                                                        $has_image = $product && !empty($product->img);
                                                        ?>
                                                        <?php if ($has_image): ?>
                                                            <img src="<?= base_url('assets/img/product/' . $product->img) ?>" alt="<?= htmlspecialchars($product->name) ?>" class="rounded" width="48" height="48">
                                                        <?php else: ?>
                                                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                                <i class="fas fa-gift fa-2x"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <?php if ($reward->type == 'bakiye'): ?>
                                                        <div class="fw-bold"><?= number_format($reward->amount, 2) ?> ₺</div>
                                                        <div class="small text-muted">Bakiye</div>
                                                    <?php else: ?>
                                                        <?php
                                                        $product = $this->db->where('id', $reward->product_id)->get('product')->row();
                                                        $product_name = $product ? $product->name : 'Ürün #'.$reward->product_id;
                                                        ?>
                                                        <div class="fw-bold"><?= htmlspecialchars($product_name) ?></div>
                                                        <div class="small text-muted">Ürün ID: <?= $reward->product_id ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ms-auto">
                                                    <span class="badge bg-secondary"><?= $reward->winner_count ?> kişi</span>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-users me-1"></i>
                                Katılımcılar
                            </div>
                            <div>
                                <span class="badge bg-secondary"><?= isset($participants) ? count($participants) : 0 ?> kişi</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <?php if (empty($participants)): ?>
                                <div class="p-4 text-center">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p>Bu çekilişe henüz katılımcı bulunmuyor.</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0" id="participantsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Kullanıcı</th>
                                                <th>E-posta</th>
                                                <th>Katılım Tarihi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($participants as $i => $p): ?>
                                                <tr>
                                                    <td><?= $i + 1 ?></td>
                                                    <td>
                                                        <?= htmlspecialchars($p->name) ?>
                                                    </td>
                                                    <td><?= htmlspecialchars($p->email) ?></td>
                                                    <td><?= date('d.m.Y H:i:s', strtotime($p->created_at)) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-award me-1"></i>
                                Kazananlar ve Ödül Teslimi
                            </div>
                            <?php if ($draw->status == 2): ?>
                                <span class="badge bg-success">Çekiliş Tamamlandı</span>
                            <?php elseif ($draw->status == 1): ?>
                                <span class="badge bg-warning text-dark">Çekiliş Devam Ediyor</span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <?php
                            // Dosyanın başında bir kez tanımlıysa tekrar tanımlama, değilse burada tanımla
                            if (!isset($CI)) {
                                $CI = $this;
                            }
                            $winners = $CI->db->where('draw_id', $draw->id)->get('draw_winners')->result();
                            
                            if (empty($winners)):
                            ?>
                                <div class="p-4 text-center">
                                    <?php if ($draw->status == 1): ?>
                                        <div class="text-muted">
                                            <i class="fas fa-hourglass-half fa-3x mb-3"></i>
                                            <p>Çekiliş henüz tamamlanmadı. Kazananlar çekiliş tamamlandığında belirlenecek.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-muted">
                                            <i class="fas fa-trophy fa-3x mb-3"></i>
                                            <p>Bu çekilişe ait kazanan bulunamadı.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Kullanıcı</th>
                                                <th>E-posta</th>
                                                <th>Ödül</th>
                                                <th>Teslim Durumu</th>
                                                <th>Teslim Tarihi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($winners as $i => $winner): 
                                                $participant = $CI->db->where('id', $winner->participant_id)->get('draw_participants')->row();
                                                $user = $CI->db->where('id', $participant->user_id)->get('user')->row();
                                                $reward = $CI->db->where('id', $winner->reward_id)->get('draw_rewards')->row();
                                            ?>
                                                <tr>
                                                    <td><?= $i + 1 ?></td>
                                                    <td><?= htmlspecialchars($user->name) ?></td>
                                                    <td><?= htmlspecialchars($user->email) ?></td>
                                                    <td>
                                                        <?php if ($reward->type == 'bakiye'): ?>
                                                            <span class="badge bg-success"><?= number_format($reward->amount, 2) ?> ₺</span>
                                                        <?php else: ?>
                                                            <?php
                                                            $product = $CI->db->where('id', $reward->product_id)->get('product')->row();
                                                            $product_name = $product ? $product->name : 'Ürün #'.$reward->product_id;
                                                            ?>
                                                            <span class="badge bg-primary"><?= htmlspecialchars($product_name) ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($winner->is_delivered): ?>
                                                            <span class="badge bg-success">Teslim Edildi</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning text-dark">Bekliyor</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($winner->delivery_date): ?>
                                                            <?= date('d.m.Y H:i:s', strtotime($winner->delivery_date)) ?>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
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
            </div>

            <!-- Çekiliş aktif ise kazanan belirleme butonu ekle -->
            <?php if ($draw->status == 1): ?>
                <div class="text-center mb-3">
                    <a href="<?= base_url('admin/draw/set_winner/' . $draw->id) ?>" class="btn btn-warning">
                        <i class="fas fa-trophy"></i> Manuel Kazanan Belirle
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
<?php $this->load->view('admin/includes/footer'); ?>
<script>
$(document).ready(function() {
    // Tooltip'leri etkinleştir
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Katılımcı tablosunu veri tablosu olarak ayarla
    $('#participantsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json',
        },
        order: [[0, 'asc']],
        pageLength: 25,
        responsive: true
    });
    
    // Çekilişi bitirme işlemi için onay
    $('.finish-draw').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        
        Swal.fire({
            title: 'Çekilişi bitirmek istiyor musunuz?',
            text: "Bu işlem sonucunda kazananlar belirlenecek ve ödüller dağıtılacaktır!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Evet, Bitir!',
            cancelButtonText: 'İptal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    });
    
    // Geri sayım işlevselliği
    function updateCountdowns() {
        $('.countdown').each(function() {
            var endTime = new Date($(this).data('end')).getTime();
            var now = new Date().getTime();
            var distance = endTime - now;
            
            if (distance < 0) {
                $(this).html('Süresi doldu');
                return;
            }
            
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            var countdownText = '';
            if (days > 0) countdownText += days + ' gün ';
            countdownText += hours + ' saat ' + minutes + ' dakika ' + seconds + ' saniye kaldı';
            
            $(this).html(countdownText);
        });
    }
    
    updateCountdowns();
    setInterval(updateCountdowns, 1000);
});
</script>

