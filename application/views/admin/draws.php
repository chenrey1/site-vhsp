<?php 
// Tarih yardımcı fonksiyonları 
function time_ago($time) {
    if (is_numeric($time)) {
        // Zaten timestamp olarak geçilmiş
    } elseif (is_string($time)) {
        $time = strtotime($time);
    } elseif (is_object($time) && $time instanceof DateTime) {
        $time = $time->getTimestamp();
    } else {
        $time = time();
    }
    
    $time_formats = [
        [60, 'saniye', 1], // 60
        [120, '1 dakika önce', '1 dakika içinde'], // 60*2
        [3600, 'dakika', 60], // 60*60, 60
        [7200, '1 saat önce', '1 saat içinde'], // 60*60*2
        [86400, 'saat', 3600], // 60*60*24, 60*60
        [172800, 'Dün', 'Yarın'], // 60*60*24*2
        [604800, 'gün', 86400], // 60*60*24*7, 60*60*24
        [1209600, 'Geçen hafta', 'Gelecek hafta'], // 60*60*24*7*4*2
        [2419200, 'hafta', 604800], // 60*60*24*7*4, 60*60*24*7
        [4838400, 'Geçen ay', 'Gelecek ay'], // 60*60*24*7*4*2
        [29030400, 'ay', 2419200], // 60*60*24*7*4*12, 60*60*24*7*4
        [58060800, 'Geçen yıl', 'Gelecek yıl'], // 60*60*24*7*4*12*2
        [2903040000, 'yıl', 29030400], // 60*60*24*7*4*12*100, 60*60*24*7*4*12
        [5806080000, 'Geçen yüzyıl', 'Gelecek yüzyıl'], // 60*60*24*7*4*12*100*2
        [58060800000, 'yüzyıl', 2903040000] // 60*60*24*7*4*12*100*20, 60*60*24*7*4*12*100
    ];
    
    $seconds = (time() - $time);
    $token = 'önce';
    $list_choice = 1;
    
    if ($seconds == 0) {
        return 'Şimdi';
    }
    if ($seconds < 0) {
        $seconds = abs($seconds);
        $token = 'içinde';
        $list_choice = 2;
    }
    
    $i = 0;
    while (isset($time_formats[$i]) && $seconds >= $time_formats[$i][0]) {
        $i++;
    }
    
    if (isset($time_formats[$i])) {
        $format = $time_formats[$i];
        if (isset($format[2]) && is_string($format[2])) {
            return $format[$list_choice];
        } else {
            return floor($seconds / $format[2]) . ' ' . $format[1] . ' ' . $token;
        }
    }
    
    return date('d.m.Y H:i', $time);
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    
    if ($ago < $now) {
        return "Süresi doldu";
    }
    
    $diff = $ago->getTimestamp() - $now->getTimestamp();
    
    if ($diff < 0) {
        return "Süresi doldu";
    }
    
    $day_diff = floor($diff / 86400);
    
    if (!is_numeric($day_diff) || $day_diff < 0) {
        return "Süresi doldu";
    }
    
    if ($day_diff == 0) {
        if ($diff < 60) return "Birkaç saniye içinde";
        if ($diff < 120) return "1 dakika içinde";
        if ($diff < 3600) return floor($diff / 60) . " dakika içinde";
        if ($diff < 7200) return "1 saat içinde";
        if ($diff < 86400) return floor($diff / 3600) . " saat içinde";
    }
    if ($day_diff == 1) return "Yarın";
    if ($day_diff < 7) return $day_diff . " gün içinde";
    if ($day_diff < 31) return ceil($day_diff / 7) . " hafta içinde";
    
    // Varsayılan format
    return date('d.m.Y H:i', $ago->getTimestamp());
}

$this->load->view('admin/includes/header'); ?>
<?php $this->load->view('admin/includes/sidebar'); ?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mt-4">Çekilişler</h1>
                <div>
                    <a href="/admin/draw/finish_expired" class="btn btn-warning me-2">
                        <i class="fas fa-sync"></i> Süresi Dolan Çekilişleri Sonlandır
                    </a>
                    <a href="/admin/draw/add" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Yeni Çekiliş Ekle
                    </a>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link<?= ($tab == 'active' ? ' active' : '') ?>" href="/admin/draw/index">
                                <i class="fas fa-play-circle"></i> Aktif Çekilişler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?= ($tab == 'finished' ? ' active' : '') ?>" href="/admin/draw/finished">
                                <i class="fas fa-check-circle"></i> Sonlanmış Çekilişler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link<?= ($tab == 'deliveries' ? ' active' : '') ?>" href="/admin/draw/deliveries">
                                <i class="fas fa-truck"></i> Teslimatlar
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <!-- Arama ve Filtreleme Bölümü -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" id="searchInput" class="form-control" placeholder="Çekiliş ara...">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <button class="btn btn-outline-primary" id="refreshTable">
                                <i class="fas fa-sync-alt"></i> Yenile
                            </button>
                            <?php if($tab == 'active'): ?>
                                <button class="btn btn-outline-warning" id="checkExpired">
                                    <i class="fas fa-clock"></i> Süresi Dolmuş Çekilişleri Kontrol Et
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle" id="drawsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 60px;" class="text-center">ID</th>
                                    <th>Çekiliş Adı</th>
                                    <th style="width: 180px;">Başlangıç Tarihi</th>
                                    <th style="width: 180px;">Bitiş Tarihi</th>
                                    <th style="width: 120px;" class="text-center">Katılımcılar</th>
                                    <th style="width: 120px;" class="text-center">Durum</th>
                                    <th style="width: 220px;" class="text-center">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($draws)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i> Listelenecek çekiliş bulunamadı
                                        </div>
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($draws as $draw): ?>
                                        <?php 
                                        $now = time();
                                        $start_time = strtotime($draw->start_time);
                                        $end_time = strtotime($draw->end_time);
                                        $status_class = '';
                                        
                                        if ($draw->status == 1) {
                                            if ($now < $start_time) {
                                                $status_text = 'Yakında Başlayacak';
                                                $status_class = 'bg-info text-white';
                                            } elseif ($now > $end_time) {
                                                $status_text = 'Süresi Doldu';
                                                $status_class = 'bg-warning text-dark';
                                            } else {
                                                $status_text = 'Aktif';
                                                $status_class = 'bg-success text-white';
                                            }
                                        } elseif ($draw->status == 2) {
                                            $status_text = 'Tamamlandı';
                                            $status_class = 'bg-primary text-white';
                                        } else {
                                            $status_text = 'Silindi';
                                            $status_class = 'bg-danger text-white';
                                        }
                                        
                                        // Katılımcı sayısını al
                                        $participant_count = $this->M_Draw->get_participant_count($draw->id);
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $draw->id ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($draw->image)): ?>
                                                    <div class="me-2">
                                                        <img src="<?= base_url($draw->image) ?>" alt="<?= htmlspecialchars($draw->name) ?>" width="40" height="40" class="rounded">
                                                    </div>
                                                    <?php else: ?>
                                                    <div class="me-2">
                                                        <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                            <i class="fas fa-gift"></i>
                                                        </div>
                                                    </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($draw->name) ?></div>
                                                        <small class="text-muted"><?= substr(strip_tags($draw->description), 0, 50) ?><?= strlen(strip_tags($draw->description)) > 50 ? '...' : '' ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div><?= date('d.m.Y H:i', strtotime($draw->start_time)) ?></div>
                                                <small class="text-muted"><?= time_ago(strtotime($draw->start_time)) ?></small>
                                            </td>
                                            <td>
                                                <div><?= date('d.m.Y H:i', strtotime($draw->end_time)) ?></div>
                                                <small class="text-muted"><?= time_elapsed_string($draw->end_time) ?></small>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary"><?= $participant_count ?></span>
                                                <?php if(isset($draw->max_participants) && $draw->max_participants > 0): ?>
                                                <div class="progress mt-1" style="height: 5px;">
                                                    <div class="progress-bar" role="progressbar" style="width: <?= min(100, ($participant_count / $draw->max_participants) * 100) ?>%"></div>
                                                </div>
                                                <small class="text-muted"><?= $participant_count ?>/<?= $draw->max_participants ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                                                <?php if ($draw->status == 1 && $now < $end_time): ?>
                                                <div class="mt-1">
                                                    <small class="text-muted countdown" data-end="<?= $draw->end_time ?>"></small>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="/admin/draw/detail/<?= $draw->id ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detay">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if($tab == 'active'): ?>
                                                        <a href="/admin/draw/edit/<?= $draw->id ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Düzenle">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <?php if ($draw->status == 1): ?>
                                                            <a href="/admin/draw/finish/<?= $draw->id ?>" class="btn btn-sm btn-warning finish-draw" data-bs-toggle="tooltip" title="Çekilişi Bitir">
                                                                <i class="fas fa-trophy"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <a href="/admin/draw/delete/<?= $draw->id ?>" class="btn btn-sm btn-danger delete-draw" data-bs-toggle="tooltip" title="Sil">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php elseif($tab == 'finished'): ?>
                                                        <a href="/admin/draw/detail/<?= $draw->id ?>" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Sonuçları Görüntüle">
                                                            <i class="fas fa-list-ol"></i>
                                                        </a>
                                                        <a href="/admin/draw/delete/<?= $draw->id ?>" class="btn btn-sm btn-danger delete-draw" data-bs-toggle="tooltip" title="Sil">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
<?php $this->load->view('admin/includes/footer'); ?>

<script>
$(document).ready(function() {
    // Tooltip'leri etkinleştir
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Silme işlemi için onay
    $(document).on('click', '.delete-draw', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        
        if (confirm('Çekiliş ve ilişkili tüm veriler silinecek! Emin misiniz?')) {
            window.location.href = url;
        }
    });
    
    // Çekilişi bitirme işlemi için onay
    $(document).on('click', '.finish-draw', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        
        if (confirm('Çekilişi bitirmek istiyor musunuz? Bu işlem sonucunda kazananlar belirlenecek ve ödüller dağıtılacaktır!')) {
            window.location.href = url;
        }
    });
    
    // Arama işlevselliği
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#drawsTable tbody tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
    
    // Yenileme butonu
    $("#refreshTable").click(function() {
        location.reload();
    });
    
    // Süresi dolmuş çekilişleri kontrol et
    $("#checkExpired").click(function() {
        // SweetAlert kaldırıldı, doğrudan AJAX çağrısı yapılıyor
        $.ajax({
            url: '/admin/draw/check_expired',
            type: 'GET',
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        // Başarılı olursa sayfayı yenile
                        location.reload();
                    } else {
                        alert('Hata: ' + data.message);
                    }
                } catch (e) {
                    alert('İşlem sırasında bir hata oluştu');
                }
            },
            error: function() {
                alert('İşlem sırasında bir hata oluştu');
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
            if (days > 0) countdownText += days + 'g ';
            countdownText += hours + 's ' + minutes + 'd ' + seconds + 'sn';
            
            $(this).html(countdownText);
        });
    }
    
    updateCountdowns();
    setInterval(updateCountdowns, 1000);
});
</script>
