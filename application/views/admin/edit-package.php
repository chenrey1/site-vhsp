
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Paket Düzenle</h5>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/product">Mağaza</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/product/packages">Paketler</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Paket Düzenle</li>
                </ol>
            </nav>
            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url(); ?>admin/product/updatePackage/<?= $package->id ?>" method="POST" id="packageForm">
                        <div class="form-group row">
                            <label for="packageName" class="col-sm-2 col-form-label">Paket Adı:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="packageName" value="<?= $package->name ?>" onchange="doSlug()" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="packageSlug" class="col-sm-2 col-form-label">Slug:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="slug" id="packageSlug" value="<?= $package->slug ?>" placeholder="Otomatik oluşturulacak, boş bırakabilirsiniz">
                                <small class="form-text text-muted">URL için kullanılacak kısa ad (örn: rockstar-paketi). Boş bırakılırsa paket adından otomatik oluşturulur.</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="packageDescription" class="col-sm-2 col-form-label">Açıklama:</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="description" id="packageDescription" rows="4" placeholder="Paket hakkında açıklama (opsiyonel)"><?= $package->description ?></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="packagePrice" class="col-sm-2 col-form-label">Paket Fiyatı:</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="price" id="packagePrice" value="<?= $package->price ?>" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">₺</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Paket satış fiyatı</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="packageDiscount" class="col-sm-2 col-form-label">İndirim Yüzdesi:</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" name="discount_percent" id="packageDiscount" value="<?= $package->discount_percent ?>" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Ürünlerin toplam fiyatı: <strong id="totalProductsPrice">0.00</strong>₺ | Otomatik hesaplamak için ürünleri seçin</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="packageSort" class="col-sm-2 col-form-label">Sıra:</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" name="sort_order" id="packageSort" value="<?= $package->sort_order ?>">
                                <small class="form-text text-muted">Listeleme sırası (düşük sayı önce gösterilir)</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="packageStatus" class="col-sm-2 col-form-label">Durum:</label>
                            <div class="col-sm-10">
                                <select class="custom-select" name="isActive" id="packageStatus" required>
                                    <option value="1" <?= $package->isActive == 1 ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= $package->isActive == 0 ? 'selected' : '' ?>>Pasif</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Paket Ürünleri:</label>
                            <div class="col-sm-10">
                                <div class="alert alert-info">
                                    <strong>Önemli:</strong> En az bir ürün seçmelisiniz. Ürünlerin toplam fiyatı otomatik hesaplanacak ve indirim yüzdesi güncellenecek.
                                </div>
                                <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 15px; border-radius: 4px; background: #f9f9f9;">
                                    <?php foreach ($products as $product) { 
                                        $category = $this->db->where('id', $product->category_id)->get('category')->row();
                                        $isSelected = in_array($product->id, $selected_product_ids);
                                    ?>
                                        <div class="form-check mb-2" style="padding: 10px; background: white; border-radius: 4px; border: 1px solid #eee;">
                                            <input class="form-check-input product-checkbox" 
                                                   type="checkbox" 
                                                   name="products[]" 
                                                   value="<?= $product->id ?>" 
                                                   id="product_<?= $product->id ?>"
                                                   data-price="<?= $product->price ?>"
                                                   <?= $isSelected ? 'checked' : '' ?>
                                                   onchange="calculateDiscount()">
                                            <label class="form-check-label d-flex align-items-center" for="product_<?= $product->id ?>" style="cursor: pointer; width: 100%;">
                                                <img src="<?= base_url('assets/img/product/') . $product->img ?>" 
                                                     alt="<?= $product->name ?>" 
                                                     style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; border-radius: 4px;">
                                                <div style="flex: 1;">
                                                    <strong><?= $product->name ?></strong><br>
                                                    <small class="text-muted">
                                                        <?= $category ? $category->name : 'Kategori yok' ?> | 
                                                        <?= number_format($product->price, 2, ',', '.') ?>₺
                                                    </small>
                                                </div>
                                            </label>
                                        </div>
                                    <?php } ?>
                                </div>
                                <small class="form-text text-danger" id="productError" style="display: none;">En az bir ürün seçmelisiniz!</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary float-right" id="submitButton">
                            <i class="far fa-save"></i> Paketi Güncelle
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function doSlug() {
    var title = document.getElementById("packageName").value;
    title = str(title);
    document.getElementById("packageSlug").value = title;
}

function str(str) {
    str = str.replace(/^\s+|\s+$/g, ''); // trim
    str = str.toLowerCase();

    // remove accents, swap ñ for n, etc
    var from = "ãàáäâẽèéëêìíïîıõòóöôùúüûñç·/_,:;şğ";
    var to = "aaaaaeeeeeiiiiiooooouuuunc------sg";
    for (var i = 0, l = from.length; i < l; i++) {
        str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
    }

    str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
        .replace(/\s+/g, '-') // collapse whitespace and replace by -
        .replace(/-+/g, '-'); // collapse dashes

    return str;
}

var isManualDiscount = false; // Manuel yüzde girildi mi?

function calculateDiscount() {
    var checkboxes = document.querySelectorAll('.product-checkbox:checked');
    var totalPrice = 0;
    
    checkboxes.forEach(function(checkbox) {
        totalPrice += parseFloat(checkbox.getAttribute('data-price')) || 0;
    });
    
    document.getElementById('totalProductsPrice').textContent = totalPrice.toFixed(2);
    
    var packagePrice = parseFloat(document.getElementById('packagePrice').value) || 0;
    var discountPercent = parseFloat(document.getElementById('packageDiscount').value) || 0;
    
    if (isManualDiscount && totalPrice > 0 && discountPercent > 0) {
        // Manuel yüzde girildiyse, fiyatı hesapla
        var calculatedPrice = totalPrice * (1 - discountPercent / 100);
        document.getElementById('packagePrice').value = calculatedPrice.toFixed(2);
    } else if (totalPrice > 0 && packagePrice > 0 && !isManualDiscount) {
        // Fiyat girildiyse, yüzdeyi hesapla
        var discount = ((totalPrice - packagePrice) / totalPrice) * 100;
        if (discount > 0) {
            document.getElementById('packageDiscount').value = discount.toFixed(2);
        } else {
            document.getElementById('packageDiscount').value = '0';
        }
    }
}

// Sayfa yüklendiğinde toplam fiyatı hesapla
window.onload = function() {
    calculateDiscount();
};

// Paket fiyatı değiştiğinde hesapla (manuel yüzde modu kapalıysa)
document.getElementById('packagePrice').addEventListener('input', function() {
    isManualDiscount = false;
    calculateDiscount();
});

// İndirim yüzdesi değiştiğinde (manuel mod)
document.getElementById('packageDiscount').addEventListener('input', function() {
    isManualDiscount = true;
    calculateDiscount();
});

// Form submit kontrolü
document.getElementById('packageForm').addEventListener('submit', function(e) {
    var checkboxes = document.querySelectorAll('.product-checkbox:checked');
    if (checkboxes.length === 0) {
        e.preventDefault();
        document.getElementById('productError').style.display = 'block';
        return false;
    }
    document.getElementById('productError').style.display = 'none';
});
</script>

