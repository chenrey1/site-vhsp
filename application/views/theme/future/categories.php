<?php
$motherCategories = $this->db
    ->where('mother_category_id', 0)
    ->where('isActive', 1)
    ->get('category')
    ->result();

// Alfabetik sırala
usort($motherCategories, function($a, $b) {
    setlocale(LC_COLLATE, 'tr_TR.UTF-8');
    return strcoll($a->name, $b->name);
});
?>

<section class="fp-section-page">
    <div class="container">
        <div class="fp-section-page-head">
            <h1 class="title mb-0">Tüm Kategoriler</h1>
        </div>

    <style>
    .categories-filter-bar {
        background: #181818 !important;
        border: 1px solid #282828 !important;
        border-radius: 12px !important;
        padding: 12px 16px !important;
        margin-bottom: 24px !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        flex-wrap: wrap !important;
    }

    .categories-search-wrapper {
        position: relative !important;
        flex: 1 !important;
        min-width: 200px !important;
    }

    .categories-search-icon {
        position: absolute !important;
        left: 12px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        color: #888 !important;
        font-size: 18px !important;
        pointer-events: none !important;
        z-index: 2 !important;
    }

    .categories-search-input {
        width: 100% !important;
        padding: 8px 12px 8px 36px !important;
        background: #1a1a1a !important;
        border: 1px solid #2a2a2a !important;
        border-radius: 8px !important;
        color: #fff !important;
        font-size: 14px !important;
        outline: none !important;
        transition: all 0.3s ease !important;
        height: 38px !important;
        box-sizing: border-box !important;
    }

    .categories-search-input:focus {
        border-color: #3498db !important;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1) !important;
    }

    .categories-search-input::placeholder {
        color: #666 !important;
    }

    .categories-sort-select {
        padding: 8px 32px 8px 12px !important;
        background: #1a1a1a !important;
        border: 1px solid #2a2a2a !important;
        border-radius: 8px !important;
        color: #fff !important;
        font-size: 14px !important;
        cursor: pointer !important;
        outline: none !important;
        appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23fff' d='M6 9L1 4h10z'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        height: 38px !important;
        min-width: 150px !important;
        transition: all 0.3s ease !important;
    }

    .categories-sort-select:focus {
        border-color: #3498db !important;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1) !important;
    }

    @media (max-width: 768px) {
        .categories-filter-bar {
            flex-direction: column !important;
            align-items: stretch !important;
        }

        .categories-search-wrapper {
            width: 100% !important;
        }

        .categories-sort-select {
            width: 100% !important;
        }
    }
    </style>

    <div class="categories-filter-bar">
        <div class="categories-search-wrapper">
            <i class="ri-search-line categories-search-icon"></i>
            <input type="text" class="categories-search-input" placeholder="Kategori ara..." id="category-filter">
        </div>
        <select id="categorySortSelect" onchange="kategoriSiralaFonksiyonu()" class="categories-sort-select">
            <option value="varsayilan">Sıralama: Varsayılan</option>
            <option value="a-z">Alfabetik (A-Z)</option>
            <option value="z-a">Alfabetik (Z-A)</option>
        </select>
    </div>
        <div class="row row-products">
            <?php foreach ($motherCategories as $cat) { ?>
                <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                    <a href="<?= base_url('kategori/') . $cat->slug ?>" class="fp-categories-item">
                        <div class="img">
                            <img src="<?= base_url('assets/img/category/') . $cat->img ?>" alt="<?= $cat->name ?>">
                        </div>
                        <div class="name"><?= $cat->name ?></div>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <script>
let orijinalKategoriSira = [];

document.addEventListener("DOMContentLoaded", function() {
    let kapsayici = document.querySelector(".row-products");
    if(kapsayici) {
        orijinalKategoriSira = Array.from(kapsayici.children);
    }
});

function kategoriSiralaFonksiyonu() {
    var secim = document.getElementById("categorySortSelect").value;
    var kapsayici = document.querySelector(".row-products");
    var kategoriler = Array.from(kapsayici.children);

    if (secim === "varsayilan") {
        kapsayici.innerHTML = "";
        orijinalKategoriSira.forEach(function(kat) {
            kapsayici.appendChild(kat);
        });
    } else {
        kategoriler.sort(function(a, b) {
            var isimA = a.querySelector(".name").textContent.toLowerCase();
            var isimB = b.querySelector(".name").textContent.toLowerCase();
            
            if (secim === "a-z") {
                return isimA.localeCompare(isimB, 'tr');
            } else if (secim === "z-a") {
                return isimB.localeCompare(isimA, 'tr');
            }
            return 0;
        });

        kapsayici.innerHTML = "";
        kategoriler.forEach(function(kat) {
            kapsayici.appendChild(kat);
        });
    }
}
</script>
</section>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const categoryFilter = document.getElementById("category-filter");
    const categoryCols = document.querySelectorAll(".fp-categories-item");

    categoryFilter.addEventListener("input", function () {
        const searchKeyword = categoryFilter.value.toLowerCase().trim();

        // Kategori filtreleme
        categoryCols.forEach(function (item) {
            const categoryName = item.querySelector(".name").textContent.toLowerCase();
            const col = item.closest("div"); // col kapsayıcı

            if (categoryName.includes(searchKeyword)) {
                col.style.display = "";
            } else {
                col.style.display = "none";
            }
        });
    });
});
</script>
