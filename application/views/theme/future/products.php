<section class="fp-section-page fp-products-page">
  <style>
    /* Modern Filtre Bar - Gamesepet Style */
    .category-filter-bar {
        background: #181818 !important;
        border: 1px solid #282828 !important;
        border-radius: 12px !important;
        padding: 12px 16px !important;
        margin-bottom: 24px !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
        display: flex !important;
        align-items: center !important;
        gap: 16px !important;
        flex-wrap: wrap !important;
    }

    .filter-bar-header {
        margin-bottom: 0 !important;
        padding-bottom: 0 !important;
        border-bottom: none !important;
        flex-shrink: 1 !important;
        min-width: 0 !important;
        max-width: 200px !important;
    }

    .category-title-section {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        min-width: 0 !important;
        width: 100% !important;
    }

    .category-icon {
        width: 24px !important;
        height: 24px !important;
        object-fit: contain !important;
        border-radius: 6px !important;
        flex-shrink: 0 !important;
    }

    .category-title {
        font-size: 16px !important;
        font-weight: 600 !important;
        color: #fff !important;
        margin: 0 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        max-width: 100% !important;
        flex: 1 !important;
        min-width: 0 !important;
    }

    .filter-bar-controls {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        flex-wrap: wrap !important;
        flex: 1 !important;
        min-width: 0 !important;
    }

    .filter-control-group {
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }

    .search-input-wrapper {
        position: relative !important;
        flex: 1 !important;
        min-width: 180px !important;
        max-width: 250px !important;
    }

    .search-icon {
        position: absolute !important;
        left: 12px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        color: #888 !important;
        font-size: 18px !important;
        pointer-events: none !important;
    }

    .filter-input,
    .filter-select {
        width: 100% !important;
        padding: 8px 12px !important;
        padding-left: 36px !important;
        background: #1a1a1a !important;
        border: 1px solid #2a2a2a !important;
        border-radius: 8px !important;
        color: #fff !important;
        font-size: 13px !important;
        outline: none !important;
        transition: all 0.3s ease !important;
        font-family: inherit !important;
        box-sizing: border-box !important;
        height: 38px !important;
    }

    .filter-search {
        padding-left: 36px !important;
    }

    .filter-price {
        width: 85px !important;
        padding: 8px 28px 8px 12px !important;
        position: relative !important;
    }

    .price-input-wrapper {
        position: relative !important;
    }

    .price-symbol {
        position: absolute !important;
        right: 10px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        color: #888 !important;
        font-size: 13px !important;
        pointer-events: none !important;
    }

    .filter-input:focus,
    .filter-select:focus {
        border-color: #3498db !important;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1) !important;
    }

    .filter-input::placeholder {
        color: #666 !important;
    }

    .price-separator {
        color: #666 !important;
        font-weight: 500 !important;
    }

    .filter-select {
        padding: 10px 14px !important;
        padding-right: 36px !important;
        cursor: pointer !important;
        appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23fff' d='M6 9L1 4h10z'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
    }

    .filter-random-btn {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        padding: 8px 14px !important;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%) !important;
        color: #fff !important;
        border: none !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        white-space: nowrap !important;
        box-shadow: 0 2px 6px rgba(52, 152, 219, 0.3) !important;
        height: 38px !important;
    }

    .filter-random-btn:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4) !important;
        background: linear-gradient(135deg, #2980b9 0%, #21618c 100%) !important;
    }

    .filter-random-btn i {
        font-size: 18px !important;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .category-filter-bar {
            padding: 16px;
        }

        .filter-bar-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-control-group {
            width: 100%;
        }

        .search-input-wrapper {
            max-width: 100%;
        }

        .price-range-group {
            justify-content: space-between;
        }

        .filter-price {
            flex: 1;
        }

        .filter-select,
        .filter-random-btn {
            width: 100%;
        }

        .filter-random-btn {
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .category-title {
            font-size: 18px;
        }

        .filter-input,
        .filter-select,
        .filter-random-btn {
            font-size: 13px;
            padding: 8px 12px;
        }
    }

    /* KART TASARIMI (SENİN ORİJİNAL KODUN - DOKUNMADIM) */
    .product-card-vertical {
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
      position: relative;
      background: #181818;
      border: 1px solid #282828;
      border-radius: 12px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    /* HOVER (ÜZERİNE GELİNCE) */
    .product-card-vertical:hover {
      transform: translateY(-8px); 
      border-color: #00d2ff !important; 
      box-shadow: 0 0 20px rgba(0, 210, 255, 0.4), 
                  0 0 40px rgba(0, 210, 255, 0.2); 
      z-index: 100;
      background: #1c1c1c !important;
    }

    .product-card-vertical img {
      transition: all 0.4s ease;
    }
    .product-card-vertical:hover img {
      filter: brightness(1.1) contrast(1.1);
    }

    /* KÖŞE BADGE ANİMASYONU */
    @keyframes rgb_glow_border {
      0% { border-color: #ff0000; box-shadow: 0 0 5px rgba(255, 0, 0, 0.5); }
      33% { border-color: #00ff00; box-shadow: 0 0 5px rgba(0, 255, 0, 0.5); }
      66% { border-color: #0000ff; box-shadow: 0 0 5px rgba(0, 0, 255, 0.5); }
      100% { border-color: #ff0000; box-shadow: 0 0 5px rgba(255, 0, 0, 0.5); }
    }
    .rgb-corner-badge {
      position: relative;
      z-index: 10;
      background: rgba(0, 0, 0, 0.8);
      color: #fff;
      padding: 3px 8px;
      font-size: 9px;
      font-weight: 800;
      text-transform: uppercase;
      border-radius: 4px;
      border: 1px solid #ff0000;
      animation: rgb_glow_border 3s linear infinite;
    }

    /* YENİ SADE PREMIUM BUTON (RGB YOK, KÜÇÜK BOYUT) */
    .premium-btn {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: #fff;
        font-size: 11px; /* Küçük yazı */
        font-weight: 700;
        padding: 8px 12px; /* Küçük boyut */
        border: none;
        border-radius: 6px;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    }
    .premium-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 10px rgba(52, 152, 219, 0.4);
        filter: brightness(1.1);
    }
  </style>

  <div class="container">
    <div class="fp-breadcrumb">
      <ul class="list-inline list-unstyled mb-0 list">
        <li><a href="<?= base_url('tum-kategoriler') ?>" class="link">Tüm Kategoriler</a></li>
        <li><a href="#" class="link active"><?= $categories->name ?></a></li>
      </ul>
    </div>

    <div class="row">
      <div class="col-lg-3">
        <div class="fp-products-category-info">
          <img src="<?= base_url('assets/img/category/') . $categories->img ?>" alt="" class="img-products">
          <div class="content">
            <h1 class="title"><?= $categories->name ?></h1>
            <p><?= $categories->description ?></p>
          </div>
        </div>

        <?php
        $alt_kategori_id = $categories->id;
        $current_url = $_SERVER['REQUEST_URI'];
        $ana_kategori = $this->db->select('mother_category_id')->where('id', $alt_kategori_id)->get('category')->row();

        if ($ana_kategori && $ana_kategori->mother_category_id != 0) {
          $alt_kategoriler = $this->db->where('mother_category_id', $ana_kategori->mother_category_id)->where('isActive', 1)->get('category')->result();
          if (!empty($alt_kategoriler)) {
            echo "<ul class='list-unstyled fp-list-alt-kategoriler'>";
            foreach ($alt_kategoriler as $kategori) {
              $link = base_url('kategori/' . $kategori->slug);
              $active_class = (strpos($current_url, $kategori->slug) !== false) ? 'active' : '';
              echo "<li><a href='{$link}' class='{$active_class}'>{$kategori->name}</a></li>";
            }
            echo "</ul>";
          }
        }
        ?>
      </div>

<div class="col-lg-9">
    <div class="category-filter-bar">
        <div class="filter-bar-header">
            <div class="category-title-section">
                <img src="<?= base_url('assets/img/category/') . $categories->img ?>" alt="<?= $categories->name ?>" class="category-icon">
                <h2 class="category-title"><?= $categories->name ?></h2>
            </div>
        </div>
        <div class="filter-bar-controls">
            <div class="filter-control-group">
                <div class="search-input-wrapper">
                    <i class="ri-search-line search-icon"></i>
                    <input type="text" id="ilanAraInput" onkeyup="ilanlariFiltrele()" placeholder="Ara..." class="filter-input filter-search">
                </div>
            </div>

            <div class="filter-control-group price-range-group">
                <div class="price-input-wrapper">
                    <input type="number" id="minFiyat" onkeyup="ilanlariFiltrele()" placeholder="Min" class="filter-input filter-price">
                    <span class="price-symbol">₺</span>
                </div>
                <span class="price-separator">-</span>
                <div class="price-input-wrapper">
                    <input type="number" id="maxFiyat" onkeyup="ilanlariFiltrele()" placeholder="Max" class="filter-input filter-price">
                    <span class="price-symbol">₺</span>
                </div>
            </div>

            <div class="filter-control-group">
                <select id="fiyatSiralaSelect" onchange="ilanlariSirala()" class="filter-select">
                    <option value="varsayilan">Sıralama: Varsayılan</option>
                    <option value="yeni">En Yeni İlanlar</option>
                    <option value="artan">Fiyat: Düşükten Yükseğe</option>
                    <option value="azalan">Fiyat: Yüksekten Düşüğe</option>
                    <option value="a-z">Alfabetik (A-Z)</option>
                    <option value="z-a">Alfabetik (Z-A)</option>
                </select>
            </div>

            <button type="button" onclick="sansliyim()" class="filter-random-btn">
                <i class="ri-shuffle-line"></i>
                <span>Rastgele İlan</span>
            </button>
        </div>
    </div> <div class="row row-products mr-0 ml-0 px-2">
          <?php if(!empty($subCategories)): foreach ($subCategories as $subCategory) { ?>
            <div class="col-6 col-md-4 col-lg-3">
              <a href="<?= base_url('kategori/' . $subCategory->slug) ?>" class="fp-categories-item">
                <div class="img"><img src="<?= base_url('assets/img/category/') . $subCategory->img ?>"></div>
                <div class="name"><?= $subCategory->name ?></div>
              </a>
            </div>
          <?php } endif; ?>

          <?php if (!empty($products)) {
            foreach ($products as $key => $product) { 
              $price = json_decode(calculatePrice($product->id, 1), true);
            ?>
<div class="col-6 col-md-4 col-lg-3 mb-4 px-2 js-item" data-id="<?= $product->id ?>" data-price="<?= $price['price'] ?>">
                <div class="product-card-modern w-100">
                  <?php if(isset($product->is_new) && $product->is_new == 1): ?>
                    <div class="product-card-modern__badge product-card-modern__badge--new">
                      <span>Yeni</span>
                    </div>
                  <?php endif; ?>
                  
                  <?php if((isset($product->is_bestseller) && $product->is_bestseller == 1) || (isset($product->is_best_seller) && $product->is_best_seller == 1)): ?>
                    <div class="product-card-modern__badge product-card-modern__badge--bestseller">
                      <span>Çok Satan</span>
                    </div>
                  <?php endif; ?>
                  
                  <?php if(isset($product->is_deal) && $product->is_deal == 1): ?>
                    <div class="product-card-modern__badge product-card-modern__badge--deal">
                      <span>Fırsat</span>
                    </div>
                  <?php endif; ?>
                  
                  <?php if ($price['isDiscount'] == 1): ?>
                    <?php $discountPercent = round(($price['normalPrice'] - $price['price']) / $price['normalPrice'] * 100); ?>
                    <div class="product-card-modern__discount-badge">
                      %<?= $discountPercent ?>
                    </div>
                  <?php endif; ?>
                  
                  <a href="<?= base_url($product->slug) ?>" class="product-card-modern__image">
                    <img src="<?= base_url('assets/img/product/') . $product->img ?>" alt="<?= $product->name ?>">
                  </a>
                  
                  <div class="product-card-modern__content">
                    <h3 class="product-card-modern__title">
                      <a href="<?= base_url($product->slug) ?>"><?= $product->name ?></a>
                    </h3>
                    <div class="product-card-modern__price">
                      <?php if ($price['isDiscount'] == 1): ?>
                        <span class="product-card-modern__price--current"><?= number_format($price['price'], 2) ?> ₺</span>
                        <span class="product-card-modern__price--old"><?= number_format($price['normalPrice'], 2) ?> ₺</span>
                      <?php else: ?>
                        <span class="product-card-modern__price--current"><?= number_format($price['price'], 2) ?> ₺</span>
                      <?php endif; ?>
                    </div>
                    <a href="<?= base_url($product->slug) ?>" class="product-card-modern__button">
                      <span>Sepete Ekle</span>
                      <i class="ri-shopping-basket-line"></i>
                    </a>
                  </div>
                </div>
              </div>
            <?php } 
          } ?>
        </div>

        <?php $history = $this->db->where('isActive', 1)->limit(10)->get('product_comments')->result(); ?>
        <div class="fp-card fp-card-comments mt-4" style="background: #181818; border: 1px solid #282828; border-radius: 12px; padding: 15px;">
            <h4 class="title" style="color:#fff; font-size:16px; margin-bottom:15px;">Müşteri Yorumları <span class="fw-normal" style="font-size:12px;">(<?= $this->db->where('isActive', 1)->count_all_results('product_comments'); ?>)</span></h4>
            <?php if (!empty($history)) { foreach ($history as $comment) { ?>
                <div class="fp-comment-item mb-3 pb-2 border-bottom border-secondary" style="border-color: #282828 !important;">
                  <p class="text" style="color: #ccc; font-size: 12px; margin-bottom: 0;"><?= $comment->comment ?></p>
                </div>
            <?php } } ?>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
let orijinalSira = [];

document.addEventListener("DOMContentLoaded", function() {
    let kapsayici = document.querySelector(".row-products");
    if(kapsayici) {
        // Artık "js-item" arıyoruz, product-item belasından kurtulduk
        orijinalSira = Array.from(kapsayici.querySelectorAll(".js-item"));
    }
});

// 1. FİLTRELEME
function ilanlariFiltrele() {
    let searchInput = document.getElementById("ilanAraInput").value.toLocaleLowerCase('tr').trim();
    let minPrice = parseFloat(document.getElementById("minFiyat").value) || 0;
    let maxPrice = parseFloat(document.getElementById("maxFiyat").value) || Infinity;
    
    let ilanlar = document.querySelectorAll(".js-item"); // js-item oldu

    ilanlar.forEach(ilan => {
        let metin = ilan.innerText.toLocaleLowerCase('tr');
        let ilanFiyati = parseFloat(ilan.getAttribute("data-price")) || 0;

        let textMatch = metin.includes(searchInput);
        let priceMatch = (ilanFiyati >= minPrice && ilanFiyati <= maxPrice);

        if (textMatch && priceMatch) {
            // BURASI ÇOK ÖNEMLİ: flex yaparsan kayıyor, block yapıyoruz
            ilan.style.setProperty("display", "block", "important"); 
        } else {
            ilan.style.setProperty("display", "none", "important");
        }
    });
}

// 2. SIRALAMA
function ilanlariSirala() {
    let secim = document.getElementById("fiyatSiralaSelect").value;
    let kapsayici = document.querySelector(".row-products");
    let ilanlar = Array.from(kapsayici.querySelectorAll(".js-item")); // js-item oldu

    if (secim === "varsayilan") {
        kapsayici.innerHTML = "";
        orijinalSira.forEach(ilan => kapsayici.appendChild(ilan));
    } else {
        ilanlar.sort((a, b) => {
            if (secim === "yeni") {
                return parseInt(b.getAttribute("data-id")) - parseInt(a.getAttribute("data-id"));
            } else if (secim === "a-z" || secim === "z-a") {
                // Alfabetik sıralama
                let titleA = a.querySelector(".product-card-modern__title a");
                let titleB = b.querySelector(".product-card-modern__title a");
                let isimA = titleA ? titleA.textContent.trim().toLowerCase() : "";
                let isimB = titleB ? titleB.textContent.trim().toLowerCase() : "";
                
                if (secim === "a-z") {
                    return isimA.localeCompare(isimB, 'tr');
                } else {
                    return isimB.localeCompare(isimA, 'tr');
                }
            } else {
                // Fiyat sıralama
                let pA = parseFloat(a.getAttribute("data-price")) || 0;
                let pB = parseFloat(b.getAttribute("data-price")) || 0;
                return (secim === "artan") ? pA - pB : pB - pA;
            }
        });
        kapsayici.innerHTML = "";
        ilanlar.forEach(ilan => kapsayici.appendChild(ilan));
    }
    ilanlariFiltrele(); 
}

// 3. RASTGELE İLAN
function sansliyim() {
    let gorunurIlanlar = Array.from(document.querySelectorAll(".js-item")).filter(i => i.style.display !== 'none');
    if (gorunurIlanlar.length === 0) return;
    let secilenIlan = gorunurIlanlar[Math.floor(Math.random() * gorunurIlanlar.length)];
    let ilanLinki = secilenIlan.querySelector("a").href;
    if (ilanLinki) window.location.href = ilanLinki;
}
</script>