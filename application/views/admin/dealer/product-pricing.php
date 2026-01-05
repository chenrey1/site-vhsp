<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Bayilik Ürün Fiyatlandırma</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dealer'); ?>">Bayilik Tipleri</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ürün Fiyatlandırma</li>
                </ol>
            </nav>

            <div class="row mb-4">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-tag mr-1"></i>
                            Bayilik Tipi: <?= $dealer_type->name ?>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-6">Varsayılan İndirim Oranı:</dt>
                                <dd class="col-sm-6">%<?= number_format($dealer_type->discount_percentage, 2, ',', '.') ?></dd>
                                
                                <dt class="col-sm-6">Minimum Alım Miktarı:</dt>
                                <dd class="col-sm-6"><?= number_format($dealer_type->min_purchase_amount, 2, ',', '.') ?> TL</dd>
                            </dl>
                            
                            <div class="alert alert-info small">
                                <i class="fas fa-info-circle mr-1"></i> 
                                Bu bayilik tipi için varsayılan indirim oranı, özel fiyatlandırma yapılmamış ürünlere uygulanır.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-sliders-h mr-1"></i>
                            Bayilik Tipi Seçimi
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="dealer_type_select">Bayilik Tipi Değiştir:</label>
                                <select class="form-control" id="dealer_type_select" onchange="changeDealerType(this.value)">
                                    <?php foreach ($dealer_types as $type): ?>
                                        <option value="<?= $type->id ?>" <?= ($type->id == $dealer_type->id) ? 'selected' : '' ?>><?= $type->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mt-3">
                                <a href="#bulkUpdateModal" data-toggle="modal" class="btn btn-primary">
                                    <i class="fas fa-layer-group mr-1"></i> Toplu Fiyat Güncelleme
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ürün Fiyatlandırma Tablosu -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-table mr-1"></i>
                            Ürün Fiyatlandırma
                        </div>
                        <div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Kategori Filtresi</span>
                                </div>
                                <select class="form-control" id="category_filter">
                                    <option value="">Tüm Kategoriler</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category->id ?>"><?= $category->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="productTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Ürün Adı</th>
                                    <th>Kategori</th>
                                    <th>Normal Fiyat</th>
                                    <th>Fiyat Tipi</th>
                                    <th>Bayi Fiyatı / İndirimi</th>
                                    <th>Son Fiyat</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr data-category="<?= $product->category_id ?>">
                                        <td><?= $product->name ?></td>
                                        <td>
                                            <?php 
                                            foreach ($categories as $category): 
                                                if ($category->id == $product->category_id):
                                                    echo $category->name;
                                                    break;
                                                endif;
                                            endforeach; 
                                            ?>
                                        </td>
                                        <td>
                                            <?php if ($product->discount > 0): ?>
                                                <del><?= number_format($product->price, 2, ',', '.') ?> TL</del>
                                                <?= number_format($product->discount, 2, ',', '.') ?> TL
                                            <?php else: ?>
                                                <?= number_format($product->price, 2, ',', '.') ?> TL
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($product->dealer_price) && $product->dealer_price): ?>
                                                <?php if ($product->dealer_price->special_price !== null): ?>
                                                    <span class="badge badge-info">Özel Fiyat</span>
                                                <?php elseif ($product->dealer_price->discount_percentage !== null): ?>
                                                    <span class="badge badge-warning">Özel İndirim</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Varsayılan İndirim</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Varsayılan İndirim</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($product->dealer_price) && $product->dealer_price): ?>
                                                <?php if ($product->dealer_price->special_price !== null): ?>
                                                    <?= number_format($product->dealer_price->special_price, 2, ',', '.') ?> TL
                                                <?php elseif ($product->dealer_price->discount_percentage !== null): ?>
                                                    %<?= number_format($product->dealer_price->discount_percentage, 2, ',', '.') ?>
                                                <?php else: ?>
                                                    %<?= number_format($dealer_type->discount_percentage, 2, ',', '.') ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                %<?= number_format($dealer_type->discount_percentage, 2, ',', '.') ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            // Base price: ürün indirimi varsa, discount değeri kullanılır
                                            $basePrice = ($product->discount > 0) ? $product->discount : $product->price;
                                            if (isset($product->dealer_price) && $product->dealer_price) {
                                                // Özel fiyat tanımlıysa direkt kullan
                                                if ($product->dealer_price->special_price !== null) {
                                                    $finalPrice = $product->dealer_price->special_price;
                                                } elseif ($product->dealer_price->discount_percentage !== null) {
                                                    // Bayi özel indirim yüzdesi
                                                    $finalPrice = $basePrice - (($basePrice * $product->dealer_price->discount_percentage) / 100);
                                                } else {
                                                    // Varsayılan indirim yüzdesi
                                                    $finalPrice = $basePrice - (($basePrice * $dealer_type->discount_percentage) / 100);
                                                }
                                            } else {
                                                // Özel fiyat/indirim yoksa varsayılan indirim
                                                $finalPrice = $basePrice - (($basePrice * $dealer_type->discount_percentage) / 100);
                                            }
                                            echo number_format($finalPrice, 2, ',', '.') . ' TL';
                                            ?>
                                        </td>
                                        <td>
                                            <a href="#editPriceModal<?= $product->id ?>" data-toggle="modal" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i> Düzenle
                                            </a>
                                        </td>
                                    </tr>
                                    
                                    <!-- Edit Price Modal -->
                                    <div class="modal fade" id="editPriceModal<?= $product->id ?>" tabindex="-1" role="dialog" aria-labelledby="editPriceModal<?= $product->id ?>Label" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editPriceModal<?= $product->id ?>Label">
                                                        Fiyat Güncelleme: <?= $product->name ?>
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="priceForm<?= $product->id ?>" action="javascript:void(0);" onsubmit="updateProductPrice(<?= $product->id ?>)">
                                                        <input type="hidden" name="dealer_type_id" value="<?= $dealer_type->id ?>">
                                                        <input type="hidden" name="product_id" value="<?= $product->id ?>">
                                                        
                                                        <div class="form-group">
                                                            <label>Normal Fiyat:</label>
                                                            <?php if ($product->discount > 0): ?>
                                                                <p class="form-control-static"><del><?= number_format($product->price, 2, ',', '.') ?> TL</del> <?= number_format($product->discount, 2, ',', '.') ?> TL</p>
                                                            <?php else: ?>
                                                                <p class="form-control-static"><?= number_format($product->price, 2, ',', '.') ?> TL</p>
                                                            <?php endif; ?>
                                                        </div>
                                                        
                                                        <div class="form-group">
                                                            <label>Fiyat Tipi:</label>
                                                            <div class="custom-control custom-radio">
                                                                <input type="radio" id="price_type_default<?= $product->id ?>" name="price_type" value="default" class="custom-control-input" onchange="togglePriceFields(<?= $product->id ?>, 'default')" <?= (!isset($product->dealer_price) || !$product->dealer_price) ? 'checked' : '' ?>>
                                                                <label class="custom-control-label" for="price_type_default<?= $product->id ?>">
                                                                    Varsayılan İndirim Kullan (%<?= number_format($dealer_type->discount_percentage, 2, ',', '.') ?>)
                                                                </label>
                                                            </div>
                                                            <div class="custom-control custom-radio mt-2">
                                                                <input type="radio" id="price_type_discount<?= $product->id ?>" name="price_type" value="discount" class="custom-control-input" onchange="togglePriceFields(<?= $product->id ?>, 'discount')" <?= (isset($product->dealer_price) && $product->dealer_price && $product->dealer_price->discount_percentage !== null) ? 'checked' : '' ?>>
                                                                <label class="custom-control-label" for="price_type_discount<?= $product->id ?>">
                                                                    Özel İndirim Oranı Belirle
                                                                </label>
                                                            </div>
                                                            <div class="custom-control custom-radio mt-2">
                                                                <input type="radio" id="price_type_special_price<?= $product->id ?>" name="price_type" value="special_price" class="custom-control-input" onchange="togglePriceFields(<?= $product->id ?>, 'special_price')" <?= (isset($product->dealer_price) && $product->dealer_price && $product->dealer_price->special_price !== null) ? 'checked' : '' ?>>
                                                                <label class="custom-control-label" for="price_type_special_price<?= $product->id ?>">
                                                                    Özel Fiyat Belirle
                                                                </label>
                                                            </div>
                                                        </div>
                                                        
                                                        <div id="discount_field<?= $product->id ?>" class="form-group" style="display: none;">
                                                            <label for="discount_percentage<?= $product->id ?>">İndirim Yüzdesi (%):</label>
                                                            <input type="number" class="form-control" id="discount_percentage<?= $product->id ?>" name="discount_percentage" min="0" max="100" step="0.01" value="<?= (isset($product->dealer_price) && $product->dealer_price && $product->dealer_price->discount_percentage !== null) ? $product->dealer_price->discount_percentage : ((isset($product->dealer_price) && $product->dealer_price && $product->dealer_price->special_price !== null) ? (100 - (($product->dealer_price->special_price / $product->price) * 100)) : '') ?>">
                                                            <small class="form-text text-muted">Ürünün fiyatına uygulanacak indirim yüzdesi. Örn: 20 yazarsanız, normal fiyat üzerinden %20 indirim uygulanır.</small>
                                                        </div>
                                                        <div id="special_price_field<?= $product->id ?>" class="form-group" style="display: none;">
                                                            <label for="special_price<?= $product->id ?>">Özel Fiyat (TL):</label>
                                                            <input type="number" class="form-control" id="special_price<?= $product->id ?>" name="special_price" min="0" step="0.01" value="<?= (isset($product->dealer_price) && $product->dealer_price && $product->dealer_price->special_price !== null) ? $product->dealer_price->special_price : '' ?>">
                                                            <small class="form-text text-muted">Ürünün direkt bayi için belirlenen fiyatını girin.</small>
                                                        </div>
                                                        
                                                        <div id="result_price<?= $product->id ?>" class="alert alert-info mt-3" style="display: none;">
                                                            <strong>Sonuç Fiyat:</strong> <span id="calculated_price<?= $product->id ?>"></span>
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
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Toplu Güncelleme Modal -->
    <div class="modal fade" id="bulkUpdateModal" tabindex="-1" role="dialog" aria-labelledby="bulkUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkUpdateModalLabel">Toplu Fiyat Güncelleme</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('admin/dealer/bulkUpdatePrices') ?>" method="post">
                        <input type="hidden" name="dealer_type_id" value="<?= $dealer_type->id ?>">
                        
                        <div class="form-group">
                            <label for="category_id">Kategori:</label>
                            <select class="form-control" id="category_id" name="category_id">
                                <option value="">Tüm Kategoriler</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category->id ?>"><?= $category->name ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Belirli bir kategorideki ürünleri güncellemek için seçin.</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Fiyat Tipi:</label>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="bulk_price_type_discount" name="price_type" value="discount" class="custom-control-input" onchange="toggleBulkPriceFields('discount')" checked>
                                <label class="custom-control-label" for="bulk_price_type_discount">
                                    Özel İndirim Oranı Belirle
                                </label>
                            </div>
                        </div>
                        
                        <div id="bulk_discount_field" class="form-group">
                            <label for="bulk_discount_percentage">İndirim Yüzdesi (%):</label>
                            <input type="number" class="form-control" id="bulk_discount_percentage" name="discount_percentage" min="0" max="100" step="0.01" value="<?= $dealer_type->discount_percentage ?>">
                            <small class="form-text text-muted">Tüm seçili ürünlere uygulanacak indirim oranı.</small>
                        </div>
                        
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle mr-1"></i> Bu işlem seçilen kategorideki tüm ürünlerin bayilik fiyatlarını güncelleyecektir.
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-primary">Toplu Güncelle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
function changeDealerType(dealer_type_id) {
    window.location.href = "<?= base_url('admin/dealer/productPricing/') ?>" + dealer_type_id;
}

function togglePriceFields(product_id, type) {
    document.getElementById('discount_field' + product_id).style.display = 'none';
    document.getElementById('special_price_field' + product_id).style.display = 'none';
    if (type === 'discount') {
        document.getElementById('discount_field' + product_id).style.display = 'block';
    } else if (type === 'special_price') {
        document.getElementById('special_price_field' + product_id).style.display = 'block';
    }
    calculateResultPrice(product_id);
}

function calculateResultPrice(product_id) {
    var price_type = document.querySelector('#editPriceModal' + product_id + ' input[name="price_type"]:checked').value;
    var normal_price = <?= json_encode(array_column($products, 'price', 'id')) ?>;
    var discounted_price = <?php
        $dp = [];
        foreach ($products as $prod) {
            $dp[$prod->id] = ($prod->discount > 0) ? $prod->discount : $prod->price;
        }
        echo json_encode($dp);
    ?>;
    var result_div = document.getElementById('result_price' + product_id);
    var price_span = document.getElementById('calculated_price' + product_id);
    
    if (price_type === 'default') {
        var default_discount = <?= $dealer_type->discount_percentage ?>;
        var final_price = discounted_price[product_id] - (discounted_price[product_id] * default_discount / 100);
        result_div.style.display = 'block';
        price_span.textContent = final_price.toFixed(2) + ' TL';
    } else if (price_type === 'discount') {
        var discount = parseFloat(document.getElementById('discount_percentage' + product_id).value) || 0;
        var final_price = discounted_price[product_id] - (discounted_price[product_id] * discount / 100);
        result_div.style.display = 'block';
        price_span.textContent = final_price.toFixed(2) + ' TL';
    } else if (price_type === 'special_price') {
        var sp = parseFloat(document.getElementById('special_price' + product_id).value) || 0;
        var final_price = sp;
        result_div.style.display = 'block';
        price_span.textContent = final_price.toFixed(2) + ' TL';
    }
}

function updateProductPrice(product_id) {
    var form = document.getElementById('priceForm' + product_id);
    var formData = new FormData(form);
    
    fetch('<?= base_url('admin/dealer/updateProductPrice') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            $('#editPriceModal' + product_id).modal('hide');
            // Sayfayı yenile
            location.reload();
        } else {
            alert('Fiyat güncellenirken bir hata oluştu.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Fiyat güncellenirken bir hata oluştu.');
    });
}

function toggleBulkPriceFields(type) {
    // Sadece indirim oranı modalında, özel fiyat toplu güncelleme desteklemiyoruz
    document.getElementById('bulk_discount_field').style.display = 'block';
}

// Kategori filtreleme
document.getElementById('category_filter').addEventListener('change', function() {
    var category_id = this.value;
    var rows = document.querySelectorAll('#productTable tbody tr');
    
    rows.forEach(function(row) {
        if (!category_id || row.dataset.category === category_id) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Sayfa yüklendiğinde hesaplama dinleyicisi
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach ($products as $product): ?>
    document.querySelectorAll('#editPriceModal<?= $product->id ?> input[name="price_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            calculateResultPrice(<?= $product->id ?>);
        });
    });
    
    document.getElementById('discount_percentage<?= $product->id ?>').addEventListener('input', function() {
        calculateResultPrice(<?= $product->id ?>);
    });
    document.getElementById('special_price<?= $product->id ?>').addEventListener('input', function() {
        calculateResultPrice(<?= $product->id ?>);
    });
    <?php endforeach; ?>
});
</script> 