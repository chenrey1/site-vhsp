<?php $this->load->view('admin/includes/header'); ?>
<?php $this->load->view('admin/includes/sidebar'); ?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="mt-4">Yeni Çekiliş Ekle</h1>
                <a href="/admin/draw/index" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Geri
                </a>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <i class="fas fa-gift me-1"></i>
                            Çekiliş Bilgileri
                        </div>
                        <div class="card-body">
                            <form method="post" enctype="multipart/form-data" id="drawForm">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Çekiliş Adı <span class="text-danger">*</span></label>
                                            <input type="text" id="name" name="name" class="form-control" required>
                                            <div class="form-text">Çekilişin kısa ve açıklayıcı bir ismi</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="start_time" class="form-label">Başlangıç Tarihi <span class="text-danger">*</span></label>
                                            <input type="datetime-local" id="start_time" name="start_time" class="form-control" required>
                                            <div class="form-text">Çekilişin ne zaman başlayacağı</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="end_time" class="form-label">Bitiş Tarihi <span class="text-danger">*</span></label>
                                            <input type="datetime-local" id="end_time" name="end_time" class="form-control" required>
                                            <div class="form-text">Çekilişin ne zaman sona ereceği</div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="max_participants" class="form-label">Maksimum Katılımcı</label>
                                            <input type="number" id="max_participants" name="max_participants" class="form-control" min="0">
                                            <div class="form-text">Boş bırakılırsa sınırsız katılımcı kabul edilir</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Çekiliş Görseli</label>
                                            <input type="file" id="image" name="image" class="form-control" accept="image/*">
                                            <div class="form-text">Önerilen boyut: 800x400px, max 2MB</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Açıklama</label>
                                            <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                                            <div class="form-text">Çekilişle ilgili detaylı bilgiler</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="my-4">
                                
                                <div class="mb-3">
                                    <label class="form-label d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-trophy me-1"></i> Çekiliş Ödülleri <span class="text-danger">*</span></span>
                                        <button type="button" class="btn btn-sm btn-primary" id="add-reward">
                                            <i class="fas fa-plus"></i> Yeni Ödül Ekle
                                        </button>
                                    </label>
                                    
                                    <div class="alert alert-info">
                                        <div class="d-flex">
                                            <div class="me-2">
                                                <i class="fas fa-info-circle fa-2x"></i>
                                            </div>
                                            <div>
                                                <strong>Bilgi:</strong> Aynı çekilişe hem bakiye hem ürün ödülleri ekleyebilirsiniz. Birden fazla ödül eklemek için "Yeni Ödül Ekle" düğmesini kullanın.
                                                <ul class="mb-0 mt-1">
                                                    <li>Bakiye ödülü için TL cinsinden tutarı girin</li>
                                                    <li>Ürün ödülü için listeden bir ürün seçin</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="rewards-area" class="mt-3">
                                        <div class="reward-row card mb-3">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="card-title mb-0">Ödül #1</h6>
                                                    <button type="button" class="btn btn-sm btn-danger remove-reward">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="mb-2">
                                                            <label class="form-label">Ödül Türü <span class="text-danger">*</span></label>
                                                            <select name="rewards[0][type]" class="form-control reward-type" required>
                                                                <option value="bakiye">Bakiye (TL)</option>
                                                                <option value="urun">Ürün</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="mb-2 reward-amount-container">
                                                            <label class="form-label">Tutar (TL) <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <input type="number" step="0.01" min="0.01" name="rewards[0][amount]" class="form-control reward-amount" placeholder="Örn: 100.00" required>
                                                                <span class="input-group-text">₺</span>
                                                            </div>
                                                        </div>
                                                        <div class="mb-2 reward-product-container" style="display:none;">
                                                            <label class="form-label">Ürün <span class="text-danger">*</span></label>
                                                            <select name="rewards[0][product_id]" class="form-control reward-product">
                                                                <option value="">Ürün Seçin</option>
                                                                <?php foreach($products as $product): ?>
                                                                    <option value="<?= $product->id ?>"><?= htmlspecialchars($product->name) ?> (ID: <?= $product->id ?>)</option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="mb-2">
                                                            <label class="form-label">Kazanan Sayısı <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <input type="number" min="1" name="rewards[0][winner_count]" class="form-control reward-winner-count" value="1" required>
                                                                <span class="input-group-text">kişi</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="no-rewards" class="d-none">
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i> En az bir ödül eklemelisiniz.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" class="btn btn-outline-secondary me-2" onclick="location.href='/admin/draw/index'">İptal</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Çekilişi Oluştur
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
    let rewardIndex = 1;
    
    // Yeni ödül ekle
    $('#add-reward').click(function() {
        let template = $('.reward-row').first().clone();
        
        // Başlığı güncelle
        template.find('.card-title').text('Ödül #' + (rewardIndex + 1));
        
        // Form elemanlarını sıfırla ve isimleri güncelle
        template.find('input, select').each(function() {
            let name = $(this).attr('name').replace(/\[\d+\]/, '['+rewardIndex+']');
            $(this).attr('name', name);
            
            // Değeri sıfırla (kazanan sayısı hariç)
            if ($(this).hasClass('reward-winner-count')) {
                $(this).val(1);
            } else {
                $(this).val('');
            }
        });
        
        // Ödül türünü bakiye olarak ayarla ve container'ları göster/gizle
        template.find('.reward-type').val('bakiye');
        template.find('.reward-amount-container').show();
        template.find('.reward-product-container').hide();
        
        // Kullanılmayan required attributelerini yönet
        template.find('.reward-amount').prop('required', true);
        template.find('.reward-product').prop('required', false);
        
        // Kaldır düğmesini etkinleştir
        template.find('.remove-reward').show();
        
        // Yeni kartı ekle
        $('#rewards-area').append(template);
        rewardIndex++;
        
        // "No rewards" uyarısını gizle
        $('#no-rewards').addClass('d-none');
    });
    
    // Ödül satırını kaldır
    $(document).on('click', '.remove-reward', function() {
        if ($('.reward-row').length > 1) {
            $(this).closest('.reward-row').remove();
            
            // İndeksleri yeniden düzenle
            $('.reward-row').each(function(index) {
                $(this).find('.card-title').text('Ödül #' + (index + 1));
            });
        } else {
            // Son kalan ödülü silmeye çalışırsa uyarı göster
            $('#no-rewards').removeClass('d-none');
        }
    });
    
    // Ödül türü değiştiğinde
    $(document).on('change', '.reward-type', function() {
        let row = $(this).closest('.reward-row');
        let type = $(this).val();
        
        if (type === 'bakiye') {
            row.find('.reward-amount-container').show();
            row.find('.reward-product-container').hide();
            
            // Required alanları düzenle
            row.find('.reward-amount').prop('required', true);
            row.find('.reward-product').prop('required', false);
        } else {
            row.find('.reward-amount-container').hide();
            row.find('.reward-product-container').show();
            
            // Required alanları düzenle
            row.find('.reward-amount').prop('required', false);
            row.find('.reward-product').prop('required', true);
        }
    });
    
    // Form gönderildiğinde kontrol
    $('#drawForm').on('submit', function(e) {
        // En az bir ödül var mı kontrol et
        if ($('.reward-row').length === 0) {
            e.preventDefault();
            $('#no-rewards').removeClass('d-none');
            $('html, body').animate({
                scrollTop: $('#no-rewards').offset().top - 100
            }, 500);
            return false;
        }
        
        // Herhangi bir ürün ödülünde ürün seçili mi kontrol et
        let valid = true;
        $('.reward-row').each(function() {
            let type = $(this).find('.reward-type').val();
            if (type === 'urun') {
                let productId = $(this).find('.reward-product').val();
                if (!productId) {
                    valid = false;
                    $(this).find('.reward-product').addClass('is-invalid');
                } else {
                    $(this).find('.reward-product').removeClass('is-invalid');
                }
            }
        });
        
        if (!valid) {
            e.preventDefault();
            Swal.fire({
                title: 'Hata!',
                text: 'Lütfen tüm ürün ödülleri için ürün seçin',
                icon: 'error',
                confirmButtonText: 'Tamam'
            });
            return false;
        }
        
        return true;
    });
    
    // Başlangıçta ilk satırın ödül türünü tetikle
    $('.reward-type').first().trigger('change');
});
</script>
