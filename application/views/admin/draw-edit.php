<?php $this->load->view('admin/includes/header'); ?>
<?php $this->load->view('admin/includes/sidebar'); ?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mt-4">Çekilişi Düzenle</h1>
                <div>
                    <a href="/admin/draw/detail/<?= $draw->id ?>" class="btn btn-outline-info me-2">
                        <i class="fas fa-eye"></i> Detaylar
                    </a>
                    <a href="/admin/draw/index" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Geri
                    </a>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-edit me-1"></i>
                                Çekiliş #<?= $draw->id ?> Düzenleme
                            </div>
                            <div>
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
                                <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="post" id="drawEditForm">
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
                                
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Çekiliş Adı <span class="text-danger">*</span></label>
                                            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($draw->name) ?>" required>
                                            <div class="form-text">Çekilişin kısa ve açıklayıcı bir ismi</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="start_time" class="form-label">Başlangıç Tarihi <span class="text-danger">*</span></label>
                                            <input type="datetime-local" id="start_time" name="start_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($draw->start_time)) ?>" required>
                                            <div class="form-text">Çekilişin ne zaman başlayacağı</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="end_time" class="form-label">Bitiş Tarihi <span class="text-danger">*</span></label>
                                            <input type="datetime-local" id="end_time" name="end_time" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($draw->end_time)) ?>" required>
                                            <div class="form-text">Çekilişin ne zaman sona ereceği</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="max_participants" class="form-label">Maksimum Katılımcı</label>
                                            <input type="number" id="max_participants" name="max_participants" class="form-control" value="<?= $draw->max_participants ?>" min="0">
                                            <div class="form-text">Boş bırakılırsa sınırsız katılımcı kabul edilir</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Mevcut Çekiliş Görseli</label>
                                            <?php if (!empty($draw->image)): ?>
                                                <div class="mb-2">
                                                    <img src="<?= base_url($draw->image) ?>" alt="<?= htmlspecialchars($draw->name) ?>" class="img-thumbnail" style="max-height: 150px;">
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-exclamation-triangle me-2"></i> Bu çekiliş için görsel yüklenmemiş
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Açıklama</label>
                                            <textarea id="description" name="description" class="form-control" rows="4"><?= isset($draw) ? htmlspecialchars($draw->description) : '' ?></textarea>
                                            <div class="form-text">Çekilişle ilgili detaylı bilgiler</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-trophy me-1"></i> Çekiliş Ödülleri
                                    </label>
                                    
                                    <div class="alert alert-warning">
                                        <div class="d-flex">
                                            <div class="me-2">
                                                <i class="fas fa-info-circle fa-2x"></i>
                                            </div>
                                            <div>
                                                <strong>Bilgi:</strong> Ödül bilgileri yalnızca gösteriliyor. Çekilişin ödüllerini değiştirmek için yeni bir çekiliş oluşturmanız gerekmektedir.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <?php if (empty($rewards)): ?>
                                        <div class="alert alert-danger">
                                            <i class="fas fa-exclamation-circle me-2"></i> Bu çekiliş için ödül tanımlanmamış!
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Ödül Türü</th>
                                                        <th>Ödül Detayı</th>
                                                        <th class="text-center">Kazanan Sayısı</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($rewards as $index => $reward): ?>
                                                        <tr>
                                                            <td><?= $index + 1 ?></td>
                                                            <td>
                                                                <?php if($reward->type == 'bakiye'): ?>
                                                                    <span class="badge bg-success">Bakiye</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-primary">Ürün</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if($reward->type == 'bakiye'): ?>
                                                                    <strong><?= number_format($reward->amount, 2) ?> ₺</strong>
                                                                <?php else: ?>
                                                                    <?php
                                                                    $product = $this->db->where('id', $reward->product_id)->get('product')->row();
                                                                    $product_name = $product ? $product->name : 'Ürün #'.$reward->product_id;
                                                                    ?>
                                                                    <div class="d-flex align-items-center">
                                                                        <?php if ($product && !empty($product->img)): ?>
                                                                            <img src="<?= base_url('assets/img/product/'.$product->img) ?>" alt="<?= htmlspecialchars($product_name) ?>" class="me-2" width="40" height="40">
                                                                        <?php endif; ?>
                                                                        <div>
                                                                            <strong><?= htmlspecialchars($product_name) ?></strong>
                                                                            <div class="small text-muted">Ürün ID: <?= $reward->product_id ?></div>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-secondary"><?= $reward->winner_count ?> kişi</span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-outline-secondary me-2" onclick="location.href='/admin/draw/index'">İptal</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Değişiklikleri Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<?php $this->load->view('admin/includes/footer'); ?>
<script>
$(document).ready(function() {
    // Form doğrulama
    $('#drawEditForm').on('submit', function(e) {
        // Bitiş tarihi başlangıç tarihinden sonra mı kontrol et
        var startTime = new Date($('#start_time').val());
        var endTime = new Date($('#end_time').val());
        
        if (endTime <= startTime) {
            e.preventDefault();
            Swal.fire({
                title: 'Hata!',
                text: 'Bitiş tarihi başlangıç tarihinden sonra olmalıdır',
                icon: 'error',
                confirmButtonText: 'Tamam'
            });
            return false;
        }
        
        return true;
    });
});
</script>
