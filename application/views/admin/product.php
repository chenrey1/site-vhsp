<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Ürünler</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/products">Mağaza</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ürünler</li>
                </ol>
            </nav>

            <div class="page-btn">
                <div class="btns">
                    <a href="#editProductPrice" data-bs-toggle="modal" data-bs-target="#editProductPrice" class="btn btn-success btn-sm"> Toplu Fiyatlandırma</a>
                    <a href="<?= base_url(); ?>admin/product/addProduct" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Ürün Ekle</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered border dataTable table-product">
                            <thead class="thead-light">
                                <tr>
                                    <th>Ürün Adı</th>
                                    <th>Fiyat</th>
                                    <th>Stok</th>
                                    <th>API Fiyat Farkı</th>
                                    <th>Kategori</th>
                                    <th>Satıcı</th>
                                    <th>Sıra / Etiket</th> 
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($product as $p) { ?>
                                    <tr>
                                        <td><img src="<?= base_url(); ?>assets/img/product/<?= $p->img ?>" alt=""> <?= $p->name ?> <?= ($p->isActive == 2) ? "<small class='text-danger'>(Deaktif)</small>" : NULL ?></td>
                                        <td><?= ($p->discount > 0) ? "<s class='text-danger'><small>" . $p->price . "₺</small></s><br> " . $p->discount : $p->price ?>₺</td>
                                        <td class="text-primary"><?php if ($p->isStock == 1) {
                                            $stok = $this->db->where('isActive', 1)->where('product_id', $p->id)->count_all_results('stock'); 
                                            echo $stok;
                                        }else{
                                          echo "Stok Gerektirmeyen Ürün";  
                                        } ?>
                                        </td>
                                        <td>%<?= $p->difference_percent ?></td>
                                        <td><?php $category = $this->db->where('id', $p->category_id)->get('category')->row(); ?> <?= ($category) ? $category->name : "Kategori Bulunamadı" ?> </td>
                                        <td><?php if ($p->seller_id == 0) {
                                            echo "Yönetici";
                                        }else if($p->seller_id != 0) {
                                            $seller = $this->db->where('id', $p->seller_id)->get('user')->row();
                                            echo ($seller && $seller->isAdmin == 1) ? "Yönetici" : ($seller ? $seller->name . " " . $seller->surname : "Bilinmiyor");
                                        } ?></td>
                                        
                                        <td>
                                            <input type="number" class="form-control update-rank" 
                                                   data-id="<?= $p->id ?>" 
                                                   value="<?= $p->rank ?>" 
                                                   style="width: 65px; text-align: center; border: 1px solid #3498db; padding: 2px; margin-bottom:8px; display:block;">

                                            <div style="font-size: 11px; display: flex; flex-direction: column; gap: 4px; border-top: 1px solid #eee; padding-top: 5px;">
                                                <label style="cursor:pointer; margin:0;">
                                                   <input type="checkbox" class="update-status" data-column="is_bestseller" data-id="<?= $p->id ?>" <?= ($p->is_bestseller == 1) ? 'checked' : '' ?>> Çok Satan 🔥
                                                </label>
                                                <label style="cursor:pointer; margin:0; display: block; margin-top: 4px;">
                                                    <input type="checkbox" class="update-status" data-column="is_best_seller" data-id="<?= $p->id ?>" <?= ($p->is_best_seller == 1) ? 'checked' : '' ?>> Ana Sayfada Göster 🚀
                                                </label>
                                                <label style="cursor:pointer; margin:0;">
                                                    <input type="checkbox" class="update-status" data-column="is_deal" data-id="<?= $p->id ?>" <?= ($p->is_deal == 1) ? 'checked' : '' ?>> Fırsat ⏳
                                                </label>
                                                <label style="cursor:pointer; margin:0;">
                                                    <input type="checkbox" class="update-status" data-column="is_new" data-id="<?= $p->id ?>" <?= ($p->is_new == 1) ? 'checked' : '' ?>> Yeni ✨
                                                </label>
                                            </div>
                                        </td>

                                        <td>
                                            <a href="javascript:void(0)" class="text-success" data-bs-toggle="modal" data-bs-target="#productDiscount<?= $p->id ?>" data-toggle="modal" data-target="#productDiscount<?= $p->id ?>"><i class="fas fa-tags"></i></a>
                                            
                                            <a href="<?= base_url(); ?>admin/product/detail/<?= $p->id ?>"><i class="fa fa-edit"></i></a>

                                            <div class="modal fade" id="productDiscount<?= $p->id ?>" tabindex="-1" aria-hidden="true" role="dialog">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">İndirim Yap: <?= $p->name ?></h5>
                                                            <button type="button" class="btn-close close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true" class="d-none d-sm-block">&times;</span>
                                                            </button>
                                                        </div>
                                                        
                                                        <div class="modal-body">
                                                            <div class="form-group mb-3">
                                                                <label>İndirimli Fiyat (TL)</label>
                                                                <input type="text" id="discount-val-<?= $p->id ?>" class="form-control" value="<?= $p->discount ?>" placeholder="Örn: 19.90">
                                                                <small class="text-muted">İndirimi kaldırmak için 0 yazın.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">İptal</button>
                                                            <button type="button" class="btn btn-primary" onclick="saveDiscount(<?= $p->id ?>)">Kaydet</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Global değişkenler
var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

// 1. İNDİRİM KAYDETME FONKSİYONU
function saveDiscount(id) {
    // Inputtaki değeri al
    var discountValue = $('#discount-val-' + id).val();
    
    // DEBUG: Veri doğru alınıyor mu görelim
    console.log("Ürün ID:", id, "Girilen İndirim:", discountValue);

    var postData = {
        // HATA BURADAYDI: Backend 'product_id' bekliyor, biz 'id' gönderiyorduk.
        product_id: id, 
        
        // Backend 'discount' bekliyor (Input adın discount olduğu için)
        discount: discountValue 
    };
    
    // Güvenlik anahtarını ekle
    postData[csrfName] = csrfHash;

    $.ajax({
        url: "<?= base_url('admin/product/add_discount') ?>",
        type: "POST",
        data: postData,
        dataType: "json",
        success: function(response) {
            // Güvenlik anahtarını güncelle
            if(response.csrf_hash) {
                csrfHash = response.csrf_hash;
            }
            
            // İşlem başarılıysa sayfayı yenile
            location.reload(); 
        },
        error: function(xhr, status, error) {
            console.log("Hata: " + error);
            // Cevap JSON olmasa bile işlem yapılmış olabilir, yenile
            location.reload(); 
        }
    });
}

// 2. DİĞER TOGGLE İŞLEMLERİ (Siralama, Status vb.)
$(document).ready(function() {
    // SIRALAMA
    $(document).on('keyup change', '.update-rank', function() {
        var id = $(this).data('id');
        var val = $(this).val();
        saveData(id, 'rank', val, $(this));
    });

    // ETİKETLER (Çok Satan vb.)
    $(document).on('change', '.update-status', function() {
        var id = $(this).data('id');
        var col = $(this).data('column');
        var val = $(this).is(':checked') ? 1 : 0;
        saveData(id, col, val, null);
    });

    function saveData(id, column, value, element) {
        var postData = { id: id, column: column, value: value };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: "<?= base_url('admin/product/update_product_status') ?>",
            type: "POST",
            data: postData,
            dataType: "json",
            success: function(response) {
                if(response.csrf_hash){
                    csrfHash = response.csrf_hash;
                }
                if(response.status == 'success'){
                    if(element !== null){
                        element.css('border', '2px solid #2ecc71');
                        setTimeout(function(){
                            element.css('border', '1px solid #3498db');
                        }, 800);
                    }
                }
            }
        });
    }
});
</script>