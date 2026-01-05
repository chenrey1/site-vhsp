<footer class="py-4 bg-light mt-auto">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted"><a href="https://oritorius.com">Oritorius</a> &copy; Tüm Hakları Saklıdır</div>
        </div>
    </div>
</footer>

</div>
</div>


<!-- Trumbowyg -->
<script src="<?= base_url() ?>vendor/trumbowyg/trumbowyg.min.js"></script>
<script src="<?= base_url() ?>vendor/trumbowyg/langs/tr.min.js"></script>
<script src="<?= base_url() ?>vendor/trumbowyg/langs/gleox_tr.min.js"></script>

<script src="<?= base_url() ?>vendor/jquery-resizable/jquery-resizable.min.js"></script>
<script src="<?= base_url() ?>vendor/trumbowyg/plugins/resizimg/trumbowyg.resizimg.min.js"></script>
<script src="<?= base_url() ?>vendor/trumbowyg/plugins/pasteimage/gleox.pasteuploadimage.js"></script>
<script src="<?= base_url() ?>vendor/trumbowyg/plugins/pasteembed/trumbowyg.pasteembed.min.js"></script>
<script src="<?= base_url() ?>vendor/trumbowyg/plugins/upload/trumbowyg.upload.min.js"></script>
<script src="<?= base_url() ?>vendor/trumbowyg/plugins/colors/trumbowyg.colors.min.js"></script>
<script src="<?= base_url() ?>vendor/trumbowyg/plugins/history/trumbowyg.history.min.js"></script>
<script src="<?= base_url() ?>vendor/trumbowyg/plugins/fontfamily/trumbowyg.fontfamily.min.js"></script>
<script src="<?= base_url() ?>vendor/trumbowyg/plugins/fontsize/trumbowyg.fontsize.min.js"></script>
<script src="<?= base_url() ?>vendor/trumbowyg/plugins/dropdowntemplates/gleox.dropdowntemplates.js"></script>
<!-- Trumbowyg -->

<script src="<?= base_url() ?>assets/admin/js/main.js"></script>
<link href="<?= base_url() ?>assets/admin/css/dashboard.css" rel="stylesheet">

<!-- Arama Modal CSS -->
<style>
.search-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    overflow: hidden;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
}

.search-modal-content {
    position: relative;
    width: 60%;
    max-width: 800px;
    margin: 100px auto;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    max-height: 70vh;
    overflow: hidden;
    transform: translateY(-50px);
    opacity: 0;
    transition: all 0.3s ease;
}

.search-modal.show .search-modal-content {
    transform: translateY(0);
    opacity: 1;
}

.search-modal-header {
    padding: 15px;
    border-bottom: 1px solid #eaeaea;
}

.search-input-container {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: 15px;
    color: #6c757d;
    font-size: 16px;
    transition: all 0.3s ease;
}

/* Arama ikonu döndürme animasyonu */
.search-icon.searching {
    color: #0d6efd;
    animation: searchSpinner 1.2s infinite linear;
}

@keyframes searchSpinner {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

/* İnce progress bar animasyonu */
.search-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 2px;
    width: 0%;
    background: linear-gradient(90deg, #0d6efd, #6610f2);
    transition: width 0.3s ease;
    border-radius: 2px;
}

.search-progress.active {
    animation: progressAnimation 1.5s infinite ease-in-out;
}

@keyframes progressAnimation {
    0% {
        width: 0%;
        left: 0;
    }
    50% {
        width: 100%;
    }
    100% {
        width: 0%;
        left: 100%;
    }
}

.search-input {
    width: 100%;
    padding: 12px 50px 12px 40px;
    border: none;
    background-color: #f5f5f7;
    border-radius: 8px;
    font-size: 16px;
    outline: none;
    transition: all 0.2s;
}

.search-input:focus {
    background-color: #eaeaea;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.search-close-btn {
    position: absolute;
    right: 15px;
    background: none;
    border: none;
    cursor: pointer;
    color: #6c757d;
    font-size: 16px;
}

.search-modal-body {
    padding: 15px;
    overflow-y: auto;
    max-height: calc(70vh - 70px);
}

.search-categories {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.search-category h6 {
    color: #6c757d;
    margin-bottom: 10px;
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
}

.search-category-items {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.search-result-item {
    display: flex;
    align-items: center;
    padding: 10px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.search-result-item:hover {
    background-color: #f5f5f7;
}

.search-result-icon {
    width: 40px;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 8px;
    background-color: #eaeaea;
    margin-right: 15px;
    color: #6c757d;
}

.search-result-content {
    flex: 1;
}

.search-result-title {
    font-weight: 500;
    margin-bottom: 3px;
}

.search-result-details {
    font-size: 13px;
    color: #6c757d;
}

/* Son aramalar için eklenen stil */
.recent-searches {
    padding: 15px 0 5px;
    margin-bottom: 20px;
}

.recent-searches-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.recent-searches-title {
    font-size: 14px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
}

.clear-searches {
    font-size: 13px;
    color: #0d6efd;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}

.recent-search-items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.recent-search-tag {
    display: flex;
    align-items: center;
    background-color: #f0f2f5;
    border-radius: 50px;
    padding: 6px 12px;
    font-size: 13px;
    color: #495057;
    cursor: pointer;
    transition: all 0.2s;
}

.recent-search-tag:hover {
    background-color: #e2e6ea;
}

.recent-search-tag i {
    margin-right: 6px;
    font-size: 12px;
    color: #6c757d;
}

.search-shortcut {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 1px 5px;
    font-size: 12px;
    background-color: #f5f5f7;
    margin-left: 5px;
}

.sidebar-search {
    padding: 10px 15px;
}

.search-toggle {
    cursor: pointer;
    transition: all 0.2s;
    border-radius: 8px;
    overflow: hidden;
}

.search-toggle:hover {
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

.search-input-trigger {
    background-color: #f5f5f7;
    cursor: pointer;
    font-size: 14px;
}

.input-group-text {
    background-color: #f5f5f7;
    border: none;
}

.input-group-text kbd {
    display: inline-block;
    padding: 2px 4px;
    font-size: 11px;
    line-height: 1;
    color: #6c757d;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 3px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    margin: 0 2px;
}

/* Yeni eklenen stiller - Sade arama alanı için */
.sidebar-header {
    border-bottom: 1px solid #eee;
}

.logo-container {
    padding: 10px 0;
}

.logo-icon {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.logo-text {
    font-size: 18px;
    color: #333;
}

.simple-search {
    margin-bottom: 15px;
}

.search-box {
    background-color: #f5f5f7;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    padding: 8px 10px;
    margin-top: 12px;
}

.search-box:hover {
    background-color: #eaeaea;
}

.search-placeholder {
    font-size: 14px;
    color: #777;
}

.search-box kbd {
    display: inline-block;
    padding: 3px 5px;
    font-size: 11px;
    line-height: 1;
    color: #6c757d;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 3px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    margin: 0 2px;
}
</style>

<script type="text/javascript">
    function updateScript() {
        $('#updateButton').html('<img height="50" src="<?= base_url('assets/img/loading.gif') ?>" />');
        $.getJSON('<?= base_url() ?>admin/dashboard/update', function(result) {
            console.log(result);
            if (result) {
                $('#updateButton').html(result.message);
            }
        });
    }

    // Arama modalını açan fonksiyon
    function openSearchModal() {
        document.getElementById('searchModal').style.display = 'block';
        setTimeout(function() {
            document.getElementById('searchModal').classList.add('show');
            document.getElementById('searchInput').focus();
            
            // Son aramaları göster
            showRecentSearches();
        }, 10);
    }

    // Arama modalını kapatan fonksiyon
    function closeSearchModal() {
        document.getElementById('searchModal').classList.remove('show');
        setTimeout(function() {
            document.getElementById('searchModal').style.display = 'none';
        }, 300);
    }

    // Klavye kısayolu ile arama modalını açma (⌘+K veya Ctrl+K)
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            openSearchModal();
        }
        
        // ESC tuşu ile modalı kapatma
        if (e.key === 'Escape' && document.getElementById('searchModal').style.display === 'block') {
            closeSearchModal();
        }
    });

    // Sayfa yüklendiğinde tüm arama sonuçlarını temizle
    document.addEventListener('DOMContentLoaded', clearSearchResults);

    // Debounce fonksiyonu - Belirli bir süre içinde tekrar tetiklenen fonksiyonları tek sefere indirger
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            const context = this;
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                func.apply(context, args);
            }, wait);
        };
    }

    // Arama kutusunu dinle ve debounce uygula
    const searchInput = document.getElementById('searchInput');
    let lastSearchQuery = ''; // Son arama sorgusunu saklamak için

    searchInput.addEventListener('input', function() {
        // İçerik boşsa hemen temizle
        if (this.value.length < 2) {
            clearSearchResults();
            stopSearchAnimation();
            showRecentSearches();
            return;
        }
        
        // Arama animasyonunu başlat
        startSearchAnimation();
        
        // Son aramaları gizle
        hideRecentSearches();
    });

    // Debounce ile arama yapma - 500ms bekler
    searchInput.addEventListener('input', debounce(function() {
        const query = this.value.toLowerCase();
        
        // İçerik boşsa işlem yapma
        if (query.length < 2) return;
        
        // Sorgu son aranan ile aynıysa tekrar arama yapma
        if (query === lastSearchQuery) return;
        
        // Sorguyu sakla
        lastSearchQuery = query;
        
        // AJAX ile arama yap
        fetch(`<?= base_url('admin/API/search') ?>?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                // Arama animasyonunu durdur
                stopSearchAnimation();
                clearSearchResults();
                
                if (!data.status) {
                    showNoResults();
                    return;
                }
                
                // Aramayı geçmişe kaydet - artık sadece tamamlanmış aramalar kaydedilecek
                addToSearchHistory(query);
                
                // Sonuçları göster
                renderSearchResults(data.results);
                
                // Arama kategorilerini göster
                document.querySelector('.search-categories').style.display = 'flex';
            })
            .catch(error => {
                console.error('Arama hatası:', error);
                // Arama animasyonunu durdur
                stopSearchAnimation();
                showErrorMessage();
            });
    }, 500));

    // Minimal arama animasyonunu başlat
    function startSearchAnimation() {
        // İkon animasyonunu başlat
        const searchIcon = document.querySelector('.search-icon');
        searchIcon.classList.add('searching');
        
        // İnce progress bar animasyonunu başlat
        const inputContainer = document.querySelector('.search-input-container');
        
        // Eğer zaten varsa yeniden oluşturma
        let progressBar = document.querySelector('.search-progress');
        if (!progressBar) {
            progressBar = document.createElement('div');
            progressBar.className = 'search-progress';
            inputContainer.appendChild(progressBar);
        }
        
        // Animasyonu başlat
        setTimeout(() => {
            progressBar.classList.add('active');
        }, 10);
    }

    // Arama animasyonunu durdur
    function stopSearchAnimation() {
        // İkon animasyonunu durdur
        const searchIcon = document.querySelector('.search-icon');
        searchIcon.classList.remove('searching');
        
        // Progress bar animasyonunu durdur
        const progressBar = document.querySelector('.search-progress');
        if (progressBar) {
            progressBar.classList.remove('active');
        }
    }

    // Arama sonuçlarını render et
    function renderSearchResults(results) {
        // Kategori başlıklarını göster/gizle
        const productCategory = document.querySelector('.search-category:nth-child(1)');
        const userCategory = document.querySelector('.search-category:nth-child(2)');
        const pageCategory = document.querySelector('.search-category:nth-child(3)');
        
        // Varsayılan olarak tüm kategorileri gizle
        productCategory.style.display = 'none';
        userCategory.style.display = 'none';
        pageCategory.style.display = 'none';
        
        // Ürün sonuçları
        if (results.products && results.products.length > 0) {
            productCategory.style.display = 'block';
            const productsHtml = results.products.map(product => `
                <div class="search-result-item" onclick="window.location.href='<?= base_url('admin/product/detail/') ?>${product.id}'">
                    <div class="search-result-icon">
                        ${product.img ? 
                            `<img src="${product.img}" alt="${product.title}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">` : 
                            `<i class="${product.icon}"></i>`
                        }
                    </div>
                    <div class="search-result-content">
                        <div class="search-result-title">${product.title}</div>
                        <div class="search-result-details">${product.detail}</div>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('searchProductsResults').innerHTML = productsHtml;
        } else {
            document.getElementById('searchProductsResults').innerHTML = '';
        }
        
        // Kullanıcı sonuçları
        if (results.users && results.users.length > 0) {
            userCategory.style.display = 'block';
            const usersHtml = results.users.map(user => `
                <div class="search-result-item" onclick="window.location.href='${user.link}'">
                    <div class="search-result-icon">
                        <i class="${user.icon}"></i>
                    </div>
                    <div class="search-result-content">
                        <div class="search-result-title">${user.title}</div>
                        <div class="search-result-details">${user.detail}</div>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('searchUsersResults').innerHTML = usersHtml;
        } else {
            document.getElementById('searchUsersResults').innerHTML = '';
        }
        
        // Sayfa sonuçları
        if (results.pages && results.pages.length > 0) {
            pageCategory.style.display = 'block';
            const pagesHtml = results.pages.map(page => `
                <div class="search-result-item" onclick="window.location.href='${page.link}'">
                    <div class="search-result-icon">
                        <i class="${page.icon}"></i>
                    </div>
                    <div class="search-result-content">
                        <div class="search-result-title">${page.title}</div>
                        <div class="search-result-details">${page.detail}</div>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('searchPagesResults').innerHTML = pagesHtml;
        } else {
            document.getElementById('searchPagesResults').innerHTML = '';
        }
        
        // Hiçbir sonuç yoksa özel mesaj göster
        if ((!results.products || results.products.length === 0) && 
            (!results.users || results.users.length === 0) && 
            (!results.pages || results.pages.length === 0)) {
            document.getElementById('searchProductsResults').innerHTML = '<div class="text-muted text-center p-2">Hiç sonuç bulunamadı</div>';
            productCategory.style.display = 'block';
        }
    }

    // Hata mesajı göster
    function showErrorMessage() {
        const productCategory = document.querySelector('.search-category:nth-child(1)');
        productCategory.style.display = 'block';
        document.getElementById('searchProductsResults').innerHTML = '<div class="text-danger text-center p-2">Arama sırasında bir hata oluştu</div>';
        document.getElementById('searchUsersResults').innerHTML = '';
        document.getElementById('searchPagesResults').innerHTML = '';
    }

    // Sonuç bulunamadı mesajı göster
    function showNoResults() {
        const productCategory = document.querySelector('.search-category:nth-child(1)');
        productCategory.style.display = 'block';
        document.getElementById('searchProductsResults').innerHTML = '<div class="text-muted text-center p-2">Hiç sonuç bulunamadı</div>';
        document.getElementById('searchUsersResults').innerHTML = '';
        document.getElementById('searchPagesResults').innerHTML = '';
    }

    // Arama sonuçlarını temizleme
    function clearSearchResults() {
        document.getElementById('searchProductsResults').innerHTML = '';
        document.getElementById('searchUsersResults').innerHTML = '';
        document.getElementById('searchPagesResults').innerHTML = '';
        
        // Tüm kategorileri gizle
        const searchCategories = document.querySelector('.search-categories');
        if (searchCategories) {
            searchCategories.style.display = 'none';
        }
        
        const categories = document.querySelectorAll('.search-category');
        categories.forEach(category => {
            category.style.display = 'none';
        });
    }

    // Arama geçmişi işlemleri
    
    // Aramayı geçmişe ekle
    function addToSearchHistory(query) {
        if (!query || query.length < 2) return;
        
        // Geçmiş aramaları al
        let searchHistory = getSearchHistory();
        
        // Eğer aynı arama zaten varsa, onu kaldır (daha sonra en başa eklemek için)
        searchHistory = searchHistory.filter(item => item.toLowerCase() !== query.toLowerCase());
        
        // Yeni aramayı başa ekle
        searchHistory.unshift(query);
        
        // Geçmişi maksimum 10 arama ile sınırla
        if (searchHistory.length > 10) {
            searchHistory = searchHistory.slice(0, 10);
        }
        
        // Geçmişi kaydet
        localStorage.setItem('admin_search_history', JSON.stringify(searchHistory));
    }
    
    // Geçmiş aramaları al
    function getSearchHistory() {
        const history = localStorage.getItem('admin_search_history');
        return history ? JSON.parse(history) : [];
    }
    
    // Geçmiş aramaları temizle
    function clearSearchHistory() {
        localStorage.removeItem('admin_search_history');
        showRecentSearches(); // Güncelleme
    }
    
    // Son aramaları göster
    function showRecentSearches() {
        const searchHistory = getSearchHistory();
        
        // Son aramalar div'i
        let recentSearchesDiv = document.getElementById('recentSearches');
        
        // Eğer yoksa oluştur
        if (!recentSearchesDiv) {
            recentSearchesDiv = document.createElement('div');
            recentSearchesDiv.id = 'recentSearches';
            recentSearchesDiv.className = 'recent-searches';
            
            // Arama modalının body kısmına ekle
            const searchModalBody = document.querySelector('.search-modal-body');
            searchModalBody.insertBefore(recentSearchesDiv, searchModalBody.firstChild);
        }
        
        // İçerik olarak göster
        if (searchHistory.length > 0) {
            recentSearchesDiv.innerHTML = `
                <div class="recent-searches-header">
                    <div class="recent-searches-title">Son Aramalar</div>
                    <button class="clear-searches" onclick="clearSearchHistory()">Temizle</button>
                </div>
                <div class="recent-search-items">
                    ${searchHistory.map(query => `
                        <div class="recent-search-tag" onclick="performSearch('${query.replace(/'/g, "\\'")}')">
                            <i class="fas fa-history"></i>
                            ${query}
                        </div>
                    `).join('')}
                </div>
            `;
            recentSearchesDiv.style.display = 'block';
        } else {
            recentSearchesDiv.style.display = 'none';
        }
    }
    
    // Son aramalar bölümünü gizle
    function hideRecentSearches() {
        const recentSearchesDiv = document.getElementById('recentSearches');
        if (recentSearchesDiv) {
            recentSearchesDiv.style.display = 'none';
        }
    }
    
    // Son aramadan bir sorguyu gerçekleştir
    function performSearch(query) {
        const searchInput = document.getElementById('searchInput');
        searchInput.value = query;
        
        // Arama animasyonunu başlat
        startSearchAnimation();
        
        // Son aramaları gizle
        hideRecentSearches();
        
        // Doğrudan arama yap, debounce'u beklemeden
        fetch(`<?= base_url('admin/API/search') ?>?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                // Arama animasyonunu durdur
                stopSearchAnimation();
                clearSearchResults();
                
                if (!data.status) {
                    showNoResults();
                    return;
                }
                
                // Son arama sorgusunu güncelle (debounce mekanizmasının tekrar çalışmasını önlemek için)
                lastSearchQuery = query;
                
                // Sonuçları göster
                renderSearchResults(data.results);
                
                // Arama kategorilerini göster
                document.querySelector('.search-categories').style.display = 'flex';
            })
            .catch(error => {
                console.error('Arama hatası:', error);
                // Arama animasyonunu durdur
                stopSearchAnimation();
                showErrorMessage();
            });
    }
</script>

<div class="mobile-quick-menu d-md-none">
    <div class="quick-menu-wrapper">
        <div class="container-fluid px-1">
            <div class="row g-0 text-center">
                <div class="col">
                    <a href="<?= base_url('admin/dashboard') ?>" class="quick-link">
                        <i class="fas fa-home"></i>
                        <span>Ana Sayfa</span>
                    </a>
                </div>
                <div class="col">
                    <a href="<?= base_url('admin/products') ?>" class="quick-link">
                        <i class="fas fa-box"></i>
                        <span>Ürünler</span>
                    </a>
                </div>
                <div class="col">
                    <a href="<?= base_url('admin/productHistory') ?>" class="quick-link">
                        <div class="position-relative">
                            <i class="fas fa-chart-line"></i>
                            <?php 
                            $pending_count = $this->db->where('isActive', 1)->count_all_results('pending_product');
                            $return_count = $this->db->where('extras IS NOT NULL')
                                                   ->where('extras !=', '')
                                                   ->where('isActive', 1)
                                                   ->count_all_results('invoice');
                            $total_count = $pending_count + $return_count;
                            if($total_count > 0):
                            ?>
                            <span class="quick-badge sales-badge"><?= $total_count ?></span>
                            <?php endif; ?>
                        </div>
                        <span>Satışlar</span>
                    </a>
                </div>
                <div class="col">
                    <a href="<?= base_url('admin/listSupports') ?>" class="quick-link">
                        <div class="position-relative">
                            <i class="fas fa-bell"></i>
                            <?php 
                            $active_supports = $this->db->where('status', 1)->count_all_results('ticket');
                            if($active_supports > 0):
                            ?>
                            <span class="quick-badge"><?= $active_supports ?></span>
                            <?php endif; ?>
                        </div>
                        <span>Destek</span>
                    </a>
                </div>
                <div class="col">
                    <a href="javascript:void(0)" class="quick-link" onclick="toggleFullscreenMenu()">
                        <i class="fas fa-bars"></i>
                        <span>Menü</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tam Sayfa Menü -->
<div class="fullscreen-menu" id="fullscreenMenu">
    <div class="fullscreen-menu-header">
        <div class="d-flex justify-content-between align-items-center">
            <a href="javascript:void(0)" onclick="toggleFullscreenMenu()" class="back-button">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="menu-title">Menü</div>
            <div class="menu-actions">
                <a href="javascript:void(0)" onclick="openSearchModal()">
                    <i class="fas fa-search"></i>
                </a>
            </div>
        </div>
    </div>
    
    <div class="fullscreen-menu-body">
        <!-- Kullanıcı Bilgileri -->
        <?php $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row(); ?>
        <div class="user-info-bar">
            <div class="user-greeting">
                <div class="user-avatar">
                    <?= substr($user->name, 0, 1) ?>
                </div>
                <div class="greeting-text">
                    <p>Merhaba, <strong><?= $user->name ?></strong></p>
                </div>
            </div>
        </div>
        
        <!-- Menü Arama Kutusu -->
        <div class="menu-search-container">
            <div class="menu-search">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="menuSearchInput" placeholder="Menü ara..." onkeyup="searchMenuItems()">
                </div>
            </div>
        </div>

        <!-- Ana Panel Kategori -->
        <div class="menu-category">
            <div class="category-header">
                <h2>Ana Panel</h2>
            </div>
            <div class="category-content">
                <div class="icon-grid">
                    <a href="<?= base_url('admin/dashboard') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-tachometer-alt"></i>
                        </div>
                        <span>Gösterge Paneli</span>
                    </a>
                    
                    <?php if (isPermFunction('seeSellHistory') == true): ?>
                    <a href="<?= base_url('admin/productHistory') ?>" class="icon-item">
                        <div class="icon-circle success-bg">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span>Satış Geçmişi</span>
                        <?php if($total_count > 0): ?>
                        <div class="notification-badge"><?= $total_count ?></div>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeLogs') == true): ?>
                    <a href="<?= base_url('admin/listLogs') ?>" class="icon-item">
                        <div class="icon-circle info-bg">
                            <i class="fas fa-history"></i>
                        </div>
                        <span>Kayıt Geçmişi</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Ürün Yönetimi Kategori -->
        <div class="menu-category">
            <div class="category-header">
                <h2>Ürün Yönetimi</h2>
            </div>
            <div class="category-content">
                <div class="icon-grid">
                    <?php if (isPermFunction('seeProduct') == true): ?>
                    <a href="<?= base_url('admin/products') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-box"></i>
                        </div>
                        <span>Ürünler</span>
                    </a>
                    
                    <a href="<?= base_url('admin/product/addProduct') ?>" class="icon-item">
                        <div class="icon-circle success-bg">
                            <i class="fas fa-plus"></i>
                        </div>
                        <span>Ürün Ekle</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeStocks') == true): ?>
                    <a href="<?= base_url('admin/stock') ?>" class="icon-item">
                        <div class="icon-circle info-bg">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <span>Stok</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeCategory') == true): ?>
                    <a href="<?= base_url('admin/category') ?>" class="icon-item">
                        <div class="icon-circle warning-bg">
                            <i class="fas fa-th-large"></i>
                        </div>
                        <span>Kategoriler</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeCoupons') == true): ?>
                    <a href="<?= base_url('admin/coupons') ?>" class="icon-item">
                        <div class="icon-circle danger-bg">
                            <i class="fas fa-tags"></i>
                        </div>
                        <span>Kuponlar</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeProductComments') == true): ?>
                    <a href="<?= base_url('admin/comments') ?>" class="icon-item">
                        <div class="icon-circle purple-bg">
                            <i class="fas fa-comment"></i>
                        </div>
                        <span>Yorumlar</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- İçerik Yönetimi Kategori -->
        <div class="menu-category">
            <div class="category-header">
                <h2>İçerik Yönetimi</h2>
            </div>
            <div class="category-content">
                <div class="icon-grid">
                    <?php if (isPermFunction('seeNotification') == true): ?>
                    <a href="<?= base_url('admin/Notification/notificationList') ?>" class="icon-item">
                        <div class="icon-circle warning-bg">
                            <i class="fas fa-bell"></i>
                        </div>
                        <span>Bildirimler</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeBlogs') == true): ?>
                    <a href="<?= base_url('admin/blog') ?>" class="icon-item">
                        <div class="icon-circle info-bg">
                            <i class="fas fa-rss"></i>
                        </div>
                        <span>Blog</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seePages') == true): ?>
                    <a href="<?= base_url('admin/pages') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <span>Sayfalar</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Kullanıcı Yönetimi Kategori -->
        <div class="menu-category">
            <div class="category-header">
                <h2>Kullanıcı Yönetimi</h2>
            </div>
            <div class="category-content">
                <div class="icon-grid">
                    <?php if (isPermFunction('seeUsers') == true): ?>
                    <a href="<?= base_url('admin/users') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-users"></i>
                        </div>
                        <span>Üyeler</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeTickets') == true): ?>
                    <a href="<?= base_url('admin/listSupports') ?>" class="icon-item">
                        <div class="icon-circle info-bg">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <span>Destek Talepleri</span>
                        <?php if($active_supports > 0): ?>
                        <div class="notification-badge"><?= $active_supports ?></div>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeReferences') == true): ?>
                    <a href="<?= base_url('admin/referenceList') ?>" class="icon-item">
                        <div class="icon-circle success-bg">
                            <i class="fas fa-link"></i>
                        </div>
                        <span>Referanslar</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($user->role_id == 1): ?>
                    <a href="<?= base_url('admin/authList') ?>" class="icon-item">
                        <div class="icon-circle warning-bg">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <span>Yetkililer</span>
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('admin/dealer/dealerUsers') ?>" class="icon-item">
                        <div class="icon-circle purple-bg">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <span>Bayilik</span>
                        <?php 
                        $pending_applications = $this->db->where('status', 'pending')->count_all_results('dealer_applications');
                        if($pending_applications > 0): 
                        ?>
                        <div class="notification-badge"><?= $pending_applications ?></div>
                        <?php endif; ?>
                    </a>
                    
                    <a href="<?= base_url('admin/subscription/subList') ?>" class="icon-item">
                        <div class="icon-circle info-bg">
                            <i class="fas fa-stream"></i>
                        </div>
                        <span>Abonelikler</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Finansman Kategori -->
        <div class="menu-category">
            <div class="category-header">
                <h2>Finansman</h2>
            </div>
            <div class="category-content">
                <div class="icon-grid">
                    <?php if (isPermFunction('seePages') == true): ?>
                    <a href="<?= base_url('admin/finance/invoices') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <span>Faturalar</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeTransfer') == true): ?>
                    <a href="<?= base_url('admin/bankTransfer') ?>" class="icon-item">
                        <div class="icon-circle info-bg">
                            <i class="fas fa-money-check"></i>
                        </div>
                        <span>Havale Bildirimi</span>
                        <?php 
                        $bank_transfer = $this->db->where('isActive',1)->count_all_results('bank_transfer');
                        if($bank_transfer > 0): 
                        ?>
                        <div class="notification-badge"><?= $bank_transfer ?></div>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeRequests') == true): ?>
                    <a href="<?= base_url('admin/request') ?>" class="icon-item">
                        <div class="icon-circle success-bg">
                            <i class="fas fa-coins"></i>
                        </div>
                        <span>Çekim Talepleri</span>
                        <?php 
                        $pending_requests = $this->db->where('status',2)->count_all_results('request');
                        if($pending_requests > 0): 
                        ?>
                        <div class="notification-badge"><?= $pending_requests ?></div>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeProduct') == true): ?>
                    <a href="<?= base_url('admin/credit_management') ?>" class="icon-item">
                        <div class="icon-circle danger-bg">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <span>Kredi Yönetimi</span>
                        <?php 
                        $pending_credit_offers = $this->db->where('status', 1)->count_all_results('credit_offers');
                        $overdue_credits = $this->db->where('status', 4)->count_all_results('user_credits');
                        $total_credit_notifications = $pending_credit_offers + $overdue_credits;
                        if($total_credit_notifications > 0): 
                        ?>
                        <div class="notification-badge"><?= $total_credit_notifications ?></div>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Pazar Yeri Kategori -->
        <div class="menu-category">
            <div class="category-header">
                <h2>Pazar Yeri</h2>
            </div>
            <div class="category-content">
                <div class="icon-grid">
                    <?php if (isPermFunction('seeShops') == true): ?>
                    <a href="<?= base_url('admin/userShops') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-store"></i>
                        </div>
                        <span>Üye Mağazaları</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seePendingProducts') == true): ?>
                    <a href="<?= base_url('admin/pendingUserProductList') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-hourglass-start"></i>
                        </div>
                        <span>Onay Bekleyen</span>
                        <?php 
                        $pending_products = $this->db->where('isActive',3)->count_all_results('product');
                        if($pending_products > 0): 
                        ?>
                        <div class="notification-badge"><?= $pending_products ?></div>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeObjections') == true): ?>
                    <a href="<?= base_url('admin/pendingProductObjectionList') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <span>İtirazlar</span>
                        <?php 
                        $pending_objections = $this->db->where('status',2)->count_all_results('product_objections');
                        if($pending_objections > 0): 
                        ?>
                        <div class="notification-badge"><?= $pending_objections ?></div>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Yayıncı Kategori - Yeni Ekledik -->
        <div class="menu-category">
            <div class="category-header">
                <h2>Yayıncı</h2>
            </div>
            <div class="category-content">
                <div class="icon-grid">
                    <a href="<?= base_url('admin/streamers') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-broadcast-tower"></i>
                        </div>
                        <span>Yayıncılar</span>
                    </a>
                    
                    <a href="<?= base_url('admin/pendingStreamerList') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-hourglass-start"></i>
                        </div>
                        <span>Onay Bekleyen Yayıncılar</span>
                    </a>
                    
                    <a href="<?= base_url('admin/donations') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-donate"></i>
                        </div>
                        <span>Bağışlar</span>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Ayarlar Kategori -->
        <div class="menu-category">
            <div class="category-header">
                <h2>Ayarlar</h2>
            </div>
            <div class="category-content">
                <div class="icon-grid">
                    <?php if (isPermFunction('seeThemeSettings') == true): ?>
                    <a href="<?= base_url('admin/themeSettings') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-palette"></i>
                        </div>
                        <span>Tema</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (isPermFunction('seeSettings') == true): ?>
                    <a href="<?= base_url('admin/publicSettings') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <span>Genel Ayarlar</span>
                    </a>
                    
                    <a href="<?= base_url('admin/apiSettings') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-code"></i>
                        </div>
                        <span>API Ayarları</span>
                    </a>
                    <?php endif; ?>
                    
                    <!-- Mail Ayarları Grubu -->
                    <?php if (isPermFunction('seePages') == true): ?>
                    <a href="<?= base_url('admin/mail/templates') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <span>Mail Şablonları</span>
                    </a>
                    
                    <a href="<?= base_url('admin/mail/logs') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-history"></i>
                        </div>
                        <span>Geçmiş Gönderimler</span>
                    </a>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('login/logout') ?>" class="icon-item">
                        <div class="icon-circle primary-bg">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <span>Çıkış Yap</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Yeni Modern Tam Sayfa Menü Stilleri */
.fullscreen-menu {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #f8f9fa;
    z-index: 1040;
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    transition: transform 0.3s ease;
}

.fullscreen-menu.active {
    transform: translateX(0);
}

.fullscreen-menu-header {
    padding: 15px;
    background-color: #343a40;
    color: white;
    position: relative;
    z-index: 2;
}

.menu-title {
    font-size: 18px;
    font-weight: 600;
}

.back-button {
    color: white;
    font-size: 16px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s;
}

.menu-actions a {
    color: white;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.fullscreen-menu-body {
    flex: 1;
    overflow-y: auto;
    padding: 0 0 80px 0;
}

/* Kullanıcı Bilgi Alanı */
.user-info-bar {
    padding: 15px;
    background-color: #fff;
    border-bottom: 1px solid #eee;
}

.user-greeting {
    display: flex;
    align-items: center;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background-color: #3a56b5;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
    margin-right: 10px;
}

.greeting-text p {
    margin: 0;
    font-size: 14px;
    font-weight: 500;
}

/* Menü Arama Kutusu */
.menu-search-container {
    padding: 10px 15px;
    background-color: #fff;
    border-bottom: 1px solid #ddd;
}

.menu-search {
    position: relative;
}

.search-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input-wrapper i {
    position: absolute;
    left: 12px;
    color: #999;
    font-size: 14px;
}

#menuSearchInput {
    width: 100%;
    padding: 10px 12px 10px 35px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background-color: #f5f5f7;
    font-size: 14px;
}

#menuSearchInput:focus {
    outline: none;
    border-color: #3498db;
    background-color: #fff;
}

/* Kategori Stilleri */
.menu-category {
    background-color: #fff;
    margin-bottom: 8px;
}

.category-header {
    padding: 10px 15px;
    border-bottom: 1px solid #f2f2f2;
}

.category-header h2 {
    font-size: 14px;
    margin: 0;
    color: #666;
    font-weight: 600;
    text-transform: uppercase;
}

.category-content {
    padding: 10px 15px;
}

/* İkon Grid */
.icon-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

.icon-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
    color: #333;
    position: relative;
}

.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-bottom: 4px;
    font-size: 18px;
}

.icon-item span {
    font-size: 10px;
    text-align: center;
    font-weight: 500;
    max-width: 70px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.notification-badge {
    position: absolute;
    top: -4px;
    right: 4px;
    min-width: 18px;
    height: 18px;
    background-color: #e74c3c;
    color: white;
    border-radius: 9px;
    font-size: 10px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
}

/* İkon Renkleri */
.primary-bg,
.success-bg,
.info-bg,
.warning-bg,
.danger-bg,
.purple-bg {
    background-color: #3a56b5;
}

/* Bildirim rozetleri için rengi koruyalım */
.notification-badge {
    background-color: #e74c3c;
}

/* Avatar ve diğer renkli öğeler */
.user-avatar {
    background-color: #3a56b5;
}

/* Alt Menü Stilleri */
.mobile-quick-menu {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    z-index: 1050;
    background-color: #fff;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
}

.quick-menu-wrapper {
    padding: 8px 0;
}

.quick-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    color: #444;
    text-decoration: none;
    padding: 5px 0;
    position: relative;
}

.quick-link i {
    font-size: 18px;
    margin-bottom: 3px;
}

.quick-link span {
    font-size: 10px;
    font-weight: 500;
}

.quick-badge {
    position: absolute;
    top: 0;
    right: 50%;
    transform: translateX(8px);
    min-width: 16px;
    height: 16px;
    border-radius: 8px;
    background-color: #e74c3c;
    color: white;
    font-size: 10px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
}

/* Responsive */
@media (min-width: 768px) {
    .icon-grid {
        grid-template-columns: repeat(5, 1fr);
    }
}
</style>

<script>
// Tam sayfa menü aç/kapat
function toggleFullscreenMenu() {
    const fullscreenMenu = document.getElementById('fullscreenMenu');
    fullscreenMenu.classList.toggle('active');
}

// Menü öğelerini arama
function searchMenuItems() {
    const input = document.getElementById('menuSearchInput');
    const filter = input.value.toLowerCase();
    const categories = document.querySelectorAll('.menu-category');
    
    if (filter.length < 2) {
        // Filtreleme yok, tüm kategorileri göster
        categories.forEach(category => {
            category.style.display = '';
            
            // Tüm öğeleri göster
            const items = category.querySelectorAll('.icon-item');
            items.forEach(item => {
                item.style.display = '';
            });
        });
        return;
    }
    
    let totalResults = 0;
    
    // Kategorileri döngüye al
    categories.forEach(category => {
        const items = category.querySelectorAll('.icon-item');
        let categoryResults = 0;
        
        // Kategori içindeki öğeleri kontrol et
        items.forEach(item => {
            const text = item.querySelector('span').textContent.toLowerCase();
            if (text.includes(filter)) {
                item.style.display = '';
                categoryResults++;
                totalResults++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Eğer kategoride sonuç bulunduysa kategoriyi göster, yoksa gizle
        if (categoryResults > 0) {
            category.style.display = '';
        } else {
            category.style.display = 'none';
        }
    });
    
    // Hiç sonuç bulunamadıysa mesaj göster
    if (totalResults === 0) {
        categories.forEach(category => {
            category.style.display = 'none';
        });
        
        // Sonuç bulunamadı mesajı eklenebilir
    }
}
</script>
</body>

</html>
