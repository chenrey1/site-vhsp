<section class="fp-section-page fp-products-page">
    <div class="container">
        <div class="fp-breadcrumb">
            <ul class="list-inline list-unstyled mb-0 list">
                <li><a href="<?= base_url() ?>" class="link">Anasayfa</a></li>
                <li><a href="<?= base_url('paketler') ?>" class="link">Paketler</a></li>
                <li><a href="#" class="link active"><?= $package->name ?></a></li>
            </ul>
        </div>
        
        <div class="product-detail-box">
            <div class="product-detail-inner-box">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="fp-card fp-product-card fp-product-card-left" style="border-radius:10px; border:none;">
                            <div class="fp-card-body">
                                <div class="content">
                                    <div class="flex-top">
                                        <div class="product-info-title-area">
                                            <h1 class="product-name"><?= $package->name ?></h1>
                                            <?php if (!empty($package->description)): ?>
                                                <p class="text mt-3"><?= $package->description ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="package-products-list mt-4">
                                        <h3 style="color: #fff; margin-bottom: 20px; font-size: 20px;">Paket İçeriği (<?= count($package->products) ?> Ürün)</h3>
                                        
                                        <div class="row">
                                            <?php foreach ($package->products as $product): ?>
                                                <div class="col-md-6 col-lg-4 mb-3">
                                                    <div class="package-product-item" style="background: #1a1a1a; border: 1px solid #282828; border-radius: 8px; padding: 15px; display: flex; align-items: center; gap: 12px;">
                                                        <img src="<?= base_url('assets/img/product/') . $product->img ?>" 
                                                             alt="<?= $product->name ?>" 
                                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; flex-shrink: 0;">
                                                        <div style="flex: 1; min-width: 0;">
                                                            <h4 style="color: #fff; font-size: 14px; font-weight: 600; margin: 0 0 5px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                                <a href="<?= base_url($product->slug) ?>" style="color: #fff; text-decoration: none;">
                                                                    <?= $product->name ?>
                                                                </a>
                                                            </h4>
                                                            <div style="color: #00d2ff; font-size: 16px; font-weight: 700;">
                                                                <?= number_format($product->price, 2) ?> ₺
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="container my-4">
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-2 rounded shadow-sm product-detail-shadows">
                                                    <i class="ri-global-line fs-4 me-2"></i>
                                                    <span>Bölge: Türkiye</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-2 rounded shadow-sm product-detail-shadows">
                                                    <i class="ri-flashlight-fill fs-4 me-2"></i>
                                                    <span>Anında Teslimat</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-2 rounded shadow-sm product-detail-shadows">
                                                    <i class="ri-message-2-fill fs-4 me-2"></i>
                                                    <span>7/24 Destek</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="d-flex align-items-center p-2 rounded shadow-sm product-detail-shadows">
                                                    <i class="ri-package-line fs-4 me-2"></i>
                                                    <span>Paket İçeriği: <?= count($package->products) ?> Ürün</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="fp-card fp-product-card-right" style="position: sticky; top: 20px; border-radius:10px; border:none;">
                            <div class="fp-card-body">
                                <div class="price" style="margin-bottom: 20px;">
                                    <i class="fi fi-sr-coins me-2" style="font-size: 32px"></i>
                                    <?php if ($package->total_price > $package->price): ?>
                                        <div class="price-new" style="color: #3498db; font-size: 32px; font-weight: 700;">
                                            <?= number_format($package->price, 2) ?> TL
                                        </div>
                                        <div class="price-old" style="color: #888; font-size: 18px; text-decoration: line-through;">
                                            <?= number_format($package->total_price, 2) ?> TL
                                        </div>
                                        <div style="color: #e74c3c; font-size: 16px; font-weight: 600; margin-top: 5px;">
                                            %<?= number_format($package->discount_percent, 0) ?> İndirim
                                        </div>
                                    <?php else: ?>
                                        <div class="price-new" style="color: #3498db; font-size: 32px; font-weight: 700;">
                                            <?= number_format($package->price, 2) ?> TL
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="action-area">
                                    <?php if (!empty($this->session->userdata('info')) || $properties->isGuest == 1): ?>
                                        <a id="addPackageToCart" onclick="addPackageToCart(<?= $package->id ?>);" class="btn btn-primary" style="width: 100%; font-size: 16px; padding: 12px;">
                                            <i class="ri-shopping-cart-2-line icon icon-left"></i> Sepete Ekle
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('hesap') ?>" class="btn btn-warning" style="width: 100%;">
                                            <i class="ri-shopping-cart-2-line icon icon-left"></i> Giriş Yapmalısın
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #282828;">
                                    <div style="color: #888; font-size: 13px; margin-bottom: 10px;">
                                        <i class="ri-information-line"></i> Bu paket içindeki tüm ürünler sepete eklenecektir.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
function addPackageToCart(packageId) {
    $.post({
        url: "<?= base_url('home/addPackageToCart'); ?>",
        type: "POST",
        data: {
            package_id: packageId
        },
        success: function (response) {
            // Buton yazısı değişimi
            $("#addPackageToCart").html("Sepete Eklendi");
            setTimeout(function () {
                $("#addPackageToCart").html('<i class="ri-shopping-cart-2-line icon icon-left"></i> Sepete Ekle');
            }, 1000);

            // Sepet sayısını güncelle
            $.post("<?= base_url('API/getCartAmount'); ?>", {}, function(amount) {
                $('.number').html(amount);
                $('#MobileNavbarCart').html('Sepet (' + amount + ')');
            });

            // Başarı mesajı
            Swal.fire({
                icon: 'success',
                title: 'Başarılı!',
                text: 'Paket sepete eklendi.',
                timer: 2000,
                showConfirmButton: false
            });
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Hata!',
                text: 'Paket sepete eklenirken bir hata oluştu.',
            });
        }
    });
}
</script>

<?php $this->load->view('theme/future/includes/footer'); ?>

