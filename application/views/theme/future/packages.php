<section class="fp-section-page fp-products-page">
  <style>
    .packages-header-section {
        background: #181818;
        border: 1px solid #282828;
        border-radius: 12px;
        padding: 28px 32px;
        margin-bottom: 32px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3);
        position: relative;
    }

    .packages-header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 24px;
    }

    .packages-title-section {
        flex: 1;
        min-width: 200px;
    }

    .packages-main-title {
        font-size: 28px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 6px;
        letter-spacing: -0.3px;
    }

    .packages-subtitle {
        font-size: 14px;
        color: #999;
        font-weight: 400;
        margin: 0;
    }

    .packages-filter-bar {
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 10px;
        padding: 10px 16px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .packages-search-wrapper {
        position: relative;
        flex: 1;
        min-width: 200px;
        max-width: 300px;
    }

    .packages-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #888;
        font-size: 18px;
        pointer-events: none;
        z-index: 2;
    }

    .packages-search-input {
        width: 100%;
        padding: 10px 12px 10px 36px;
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
        outline: none;
        transition: all 0.3s ease;
        height: 38px;
        box-sizing: border-box;
    }

    .packages-search-input:focus {
        border-color: #00d2ff;
        box-shadow: 0 0 0 2px rgba(0, 210, 255, 0.1);
    }

    .packages-search-input::placeholder {
        color: #666;
    }

    .filter-select {
        padding: 10px 32px 10px 16px;
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        outline: none;
        cursor: pointer;
        transition: all 0.2s ease;
        min-width: 180px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23fff' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
    }

    .filter-select:hover {
        border-color: #3a3a3a;
        background: #1f1f1f;
    }

    .filter-select:focus {
        border-color: #00d2ff;
        box-shadow: 0 0 0 2px rgba(0, 210, 255, 0.1);
    }

    .filter-random-btn {
        padding: 10px 16px;
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 8px;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .filter-random-btn:hover {
        border-color: #00d2ff;
        background: rgba(0, 210, 255, 0.05);
        color: #00d2ff;
    }

    .filter-random-btn i {
        font-size: 18px;
    }

    .package-card-hidden {
        display: none !important;
    }

    .package-card {
        background: linear-gradient(135deg, #1a1a1a 0%, #242424 50%, #1a1a1a 100%);
        background-image: 
            radial-gradient(circle at 20% 50%, rgba(0, 210, 255, 0.03) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(52, 152, 219, 0.03) 0%, transparent 50%),
            linear-gradient(135deg, #1a1a1a 0%, #242424 50%, #1a1a1a 100%);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 18px;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.05);
        position: relative;
        overflow: hidden;
    }

    .package-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, #00d2ff, transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .package-card:hover {
        transform: translateY(-4px);
        border-color: rgba(0, 210, 255, 0.4);
        box-shadow: 0 8px 24px rgba(0, 210, 255, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
    }

    .package-card:hover::before {
        opacity: 1;
    }

    .package-images {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
        align-items: center;
        justify-content: center;
        flex-wrap: nowrap;
    }

    .package-image-item {
        width: 90px;
        height: 125px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
        transition: all 0.3s ease;
        background: #1a1a1a;
        position: relative;
    }

    .package-image-item:hover {
        transform: translateY(-3px) scale(1.05);
        border-color: rgba(0, 210, 255, 0.6);
        box-shadow: 0 4px 12px rgba(0, 210, 255, 0.3);
        z-index: 1;
    }

    .package-image-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .package-image-item.more-count {
        position: relative;
        border-color: rgba(255, 255, 255, 0.1);
        width: 90px;
        height: 125px;
        border-radius: 8px;
        overflow: hidden;
    }

    .package-image-item.more-count::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.6) 0%, rgba(0, 0, 0, 0.4) 100%);
        z-index: 1;
    }

    .package-image-item.more-count img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .package-image-item.more-count::after {
        content: '+ ' attr(data-count);
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9);
        z-index: 2;
    }

    .package-title {
        font-size: 18px;
        font-weight: 600;
        color: #fff;
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .package-title a {
        color: #fff;
        text-decoration: none;
        transition: color 0.3s;
    }

    .package-title a:hover {
        color: #00d2ff;
    }

    .package-info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .package-products-count {
        font-size: 13px;
        color: #999;
        font-weight: 500;
    }

    .package-discount-badge {
        display: inline-block;
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: #fff;
        padding: 4px 10px;
        border-radius: 5px;
        font-size: 11px;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(231, 76, 60, 0.4);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .package-price {
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .package-price-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .package-price-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
        flex: 1;
    }

    .package-price-current {
        font-size: 24px;
        font-weight: 700;
        color: #00d2ff;
        line-height: 1;
    }

    .package-price-old {
        font-size: 15px;
        color: #777;
        text-decoration: line-through;
        font-weight: 400;
    }

    .package-platform-icon {
        width: 44px;
        height: 44px;
        border-radius: 6px;
        background: linear-gradient(135deg, #1b2838, #2a475e);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(102, 192, 244, 0.4);
        box-shadow: 0 2px 6px rgba(102, 192, 244, 0.2);
        flex-shrink: 0;
    }

    .package-platform-icon i {
        font-size: 22px;
        color: #66c0f4;
    }

    .package-btn {
        margin-top: 12px;
        width: 100%;
        padding: 12px;
        font-size: 14px;
        font-weight: 600;
        border-radius: 8px;
        background: linear-gradient(135deg, #00d2ff, #3498db);
        border: none;
        color: #fff;
        transition: all 0.3s ease;
        box-shadow: 0 3px 10px rgba(0, 210, 255, 0.3);
        text-decoration: none;
        display: block;
        text-align: center;
    }

    .package-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 210, 255, 0.4);
        background: linear-gradient(135deg, #3498db, #00d2ff);
        color: #fff;
    }
  </style>

  <div class="container">
    <div class="fp-breadcrumb">
      <ul class="list-inline list-unstyled mb-0 list">
        <li><a href="<?= base_url() ?>" class="link">Anasayfa</a></li>
        <li><a href="#" class="link active">Paketler</a></li>
      </ul>
    </div>

    <div class="packages-header-section">
      <div class="packages-header-content">
        <div class="packages-title-section">
          <h1 class="packages-main-title">Paketler</h1>
          <p class="packages-subtitle">En iyi fırsatları keşfedin, özel paketleri kaçırmayın</p>
        </div>
        <div class="packages-filter-bar">
          <div class="packages-search-wrapper">
            <i class="ri-search-line packages-search-icon"></i>
            <input type="text" class="packages-search-input" placeholder="Paketlerde ara..." id="package-search">
          </div>
          <select id="sortSelect" class="filter-select" onchange="window.location.href='<?= base_url('paketler') ?>?sort=' + this.value">
            <option value="newest" <?= $sort_option == 'newest' ? 'selected' : '' ?>>En Yeni</option>
            <option value="price_low" <?= $sort_option == 'price_low' ? 'selected' : '' ?>>Fiyat: Düşükten Yükseğe</option>
            <option value="price_high" <?= $sort_option == 'price_high' ? 'selected' : '' ?>>Fiyat: Yüksekten Düşüğe</option>
            <option value="discount_high" <?= $sort_option == 'discount_high' ? 'selected' : '' ?>>En Yüksek İndirim</option>
          </select>
          <button type="button" onclick="rastgelePaket()" class="filter-random-btn">
            <i class="ri-shuffle-line"></i>
            <span>Rastgele Paket</span>
          </button>
        </div>
      </div>
    </div>

    <div class="row">
        <?php if (!empty($packages)): ?>
            <?php foreach ($packages as $package): ?>
                <?php 
                // Toplam fiyat hesapla
                $total_price = 0;
                foreach ($package->products as $product) {
                    $total_price += $product->price;
                }
                $discount_percent = $total_price > 0 ? (($total_price - $package->price) / $total_price) * 100 : 0;
                ?>
                <div class="col-12 col-md-6 col-lg-4 mb-4 js-package-item" data-name="<?= strtolower($package->name) ?>">
                    <div class="package-card">
                        <div class="package-images">
                            <?php 
                            $total_products = count($package->products);
                            if ($total_products <= 4) {
                                // 4 veya daha az ise hepsini göster
                                for ($i = 0; $i < $total_products; $i++): 
                            ?>
                                <div class="package-image-item">
                                    <img src="<?= base_url('assets/img/product/') . $package->products[$i]->img ?>" 
                                         alt="<?= $package->products[$i]->name ?>">
                                </div>
                            <?php 
                                endfor;
                            } else {
                                // 5+ ise ilk 3'ünü göster, 4. sini transparan "+X" butonu olarak göster
                                for ($i = 0; $i < 3; $i++): 
                            ?>
                                <div class="package-image-item">
                                    <img src="<?= base_url('assets/img/product/') . $package->products[$i]->img ?>" 
                                         alt="<?= $package->products[$i]->name ?>">
                                </div>
                            <?php 
                                endfor; 
                            ?>
                                <div class="package-image-item more-count" data-count="<?= $total_products - 4 ?>">
                                    <?php if (isset($package->products[3])): ?>
                                        <img src="<?= base_url('assets/img/product/') . $package->products[3]->img ?>" 
                                             alt="<?= $package->products[3]->name ?>">
                                    <?php endif; ?>
                                </div>
                            <?php } ?>
                        </div>

                        <h3 class="package-title">
                            <a href="<?= base_url('paket/' . $package->slug) ?>"><?= $package->name ?></a>
                        </h3>

                        <div class="package-info-row">
                            <span class="package-products-count">
                                <?= count($package->products) ?> Ürün
                            </span>
                            <?php if ($discount_percent > 0): ?>
                                <span class="package-discount-badge">%<?= number_format($discount_percent, 0) ?> İndirim</span>
                            <?php endif; ?>
                        </div>

                        <div class="package-price">
                            <div class="package-price-wrapper">
                                <div class="package-price-info">
                                    <div class="package-price-current"><?= number_format($package->price, 2) ?> ₺</div>
                                    <?php if ($total_price > $package->price): ?>
                                        <div class="package-price-old"><?= number_format($total_price, 2) ?> ₺</div>
                                    <?php endif; ?>
                                </div>
                                <div class="package-platform-icon" title="Steam">
                                    <i class="ri-steam-fill"></i>
                                </div>
                            </div>
                        </div>

                        <a href="<?= base_url('paket/' . $package->slug) ?>" class="package-btn">
                            Paketi İncele
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <p>Henüz paket bulunmamaktadır.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
  </div>
</section>

<script>
// Paket arama fonksiyonu
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('package-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const packageItems = document.querySelectorAll('.js-package-item');
            
            packageItems.forEach(function(item) {
                const packageName = item.getAttribute('data-name');
                if (packageName.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});

// Rastgele paket fonksiyonu
function rastgelePaket() {
    const gorunurPaketler = Array.from(document.querySelectorAll(".js-package-item")).filter(p => p.style.display !== 'none');
    if (gorunurPaketler.length === 0) return;
    const secilenPaket = gorunurPaketler[Math.floor(Math.random() * gorunurPaketler.length)];
    const paketLinki = secilenPaket.querySelector(".package-title a");
    if (paketLinki && paketLinki.href) {
        window.location.href = paketLinki.href;
    }
}
</script>

<?php $this->load->view('theme/future/includes/footer'); ?>

