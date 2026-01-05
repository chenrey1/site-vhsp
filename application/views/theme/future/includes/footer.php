<section class="fp-blogs-section d-none">
    <div class="container">
        <div class="fp-section-head">
            <h4 class="title mb-0">Blog</h4>
            <a href="<?= base_url("makale-listesi") ?>" class="btn btn-white rounded-pill">Tüm Yazılar <i class="ri-arrow-right-s-line icon icon-right"></i></a>
        </div>
        <div class="row justify-content-center">
            <?php foreach ($footerBlog as $b) { ?>
                <div class="col-md-6 col-lg-4">
                    <div class="fp-blog-item">
                        <img src="<?= base_url('assets/img/blog/' . $b->img) ?>" alt="" class="img-cover img-aspect">
                        <div class="date">
                            <?php $dateObject = DateTime::createFromFormat('d.m.Y', $b->date); ?>
                            <div class="day"><?= $dateObject->format('d'); ?></div>
                            <div class="month"><?= $dateObject->format('m'); ?></div>
                        </div>
                        <div class="content">
                            <a href="<?= base_url('makale/') . $b->slug ?>" class="title"><?= $b->title ?></a>
                            <a href="<?= base_url('makale/') . $b->slug ?>" class="btn btn-link text-white rounded-pill btn-sm">Tümünü Oku <i class="ri-arrow-right-line icon icon-right"></i></a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>
<footer class="fp-footer modern-footer">
  <div class="footer-top">
    <div class="container">
      <div class="footer-body">
        <div class="container">
          <div class="row">
            <div class="col-lg-4">
              <div class="footer-content"> 
              
<img src="https://www.valohesap.com/assets/future/img/vhsp-aqua.png" 
     alt="ValoHesap Logo" 
     class="img-logo floating-element logo-dark-active" 
     style="height:60px; display:block;">

<img src="https://www.valohesap.com/assets/future/img/vhsp-siyah.png" 
     alt="ValoHesap Logo" 
     class="img-logo floating-element logo-light-active" 
     style="height:60px; display:none;">

<style>
    /* Light mode: Siyah logo görünür, Aqua logo gizli */
    html[data-theme="light"] .logo-dark-active {
        display: none !important;
    }
    
    html[data-theme="light"] .logo-light-active {
        display: block !important;
    }
    
    /* Dark mode: Aqua logo görünür, Siyah logo gizli */
    html[data-theme="dark"] .logo-light-active {
        display: none !important;
    }
    
    html[data-theme="dark"] .logo-dark-active {
        display: block !important;
    }
</style>
         
         </div> 
         
         <div class="footer-contact">
         
                <p class="text-desc"> <?= $properties->description ?> </p>
                <div class="follow-area">
                  <div class="title d-none">Bizi Takip Edin</div>
                  <ul class="list-social mb-0 list-unstyled list-inline"> 
										<li>
											<a target="_blank" href="#" class="fb">
												<i class="ri-facebook-line"></i>
											</a>
										</li>
										<li>
											<a target="_blank" href="" class="ig">
												<i class="ri-instagram-line"></i>
											</a>
										</li>
										<li>
											<a target="_blank" href="" class="tw">
												<i class="ri-twitter-x-line"></i>
											</a>
										</li>
                    			<li>
											<a target="_blank" href="" class="tw">
												<i class="ri-discord-fill"></i>
											</a>
										</li>
                     </ul>
                </div>
              </div>
            </div>
            <div class="col-lg-8">
              <div class="row">
                <div class="col-lg-4 col-md-4 mb-4 mb-lg-0 col-12 d-none">
                  <div class="footer-content">
                    <h4 class="footer-title">Blog</h4>
                    <ul class="list-unstyled footer-list mb-0"> <?php foreach ($footerBlog as $b) { ?> <li>
                        <a href="
													<?= base_url('makale/' . $b->slug); ?>"> <?= $b->title ?> </a>
                      </li> <?php } ?> </ul>
                  </div>
                </div>
                   <div class="col-lg-4 col-md-4 mb-4 mb-lg-0 col-12">
                  <div class="footer-content">
                    <h4 class="footer-title">
                      <i class="fi fi-sr-bolt footer-title-icon"></i>Hızlı Erişim</h4>
                    <ul class="list-unstyled footer-list mb-0">  <li>
                        <a href="
													/"> Anasayfa </a>
                      </li>  <li>
                        <a href="
													/sayfa/hakkimizda"> Hakkımızda </a>
                      </li>  <li>
                        <a href="
													/tum-kategoriler"> Ürünlerimiz </a>
                      </li> 
                      <li>
                        <a href="
													/paketler"> Paketler </a>
                      </li> 
                       <li>
                        <a href="
													/blog"> Blog </a>
                      </li> 
                          <li>
                        <a href="
													/#"> İletişim </a>
                      </li> 
                     </ul>
                  </div>
                </div>
                <div class="col-lg-4 col-md-4 mb-4 mb-lg-0 col-12">
                  <div class="footer-content">
                    <h4 class="footer-title">
                      <i class="ri-pages-line footer-title-icon"></i>Sayfalar</h4>
                    <ul class="list-unstyled footer-list mb-0"> <?php foreach ($footerPage as $p) { ?> <li>
                        <a href="
													<?= base_url('sayfa/' . $p->slug); ?>"> <?= $p->title ?> </a>
                      </li> <?php } ?> </ul>
                  </div>
                </div>
             
            <div class="col-lg-4 col-md-4 mb-4 mb-lg-0 col-12">
             <div class="footer-content">
               <h4 class="footer-title">
                <i class="fi fi-sr-phone-call footer-title-icon"></i>İletişim</h4>
             <ul class="footer-contact">
                  <li class="d-none">
                    <div class="contact-icon">
                      <i class="ri-map-pin-line"></i>
                    </div>
                    <div class="contact-text">
                      <p>ASDAS ASDASD ASDASD, <br>34000 İstanbul, Türkiye </p>
                    </div>
                  </li>
                  <li>
                    
                    <div class="contact-icon">
                      <i class="ri-mail-line"></i>
                    </div>
                    <div class="contact-text">
                      <p>E-posta: <a href="mailto:destek@valohesap.com">
                          <br>destek@valohesap.com </a>
                      </p>
                    </div>
                  </li>
                </ul>
             </div>
           </div>
           <!--
                <div class="col-6 col-lg-4">
                  <div class="footer-content">
                    <h4 class="footer-title">Son Eklenen Ürünler</h4>
                    <ul class="list-unstyled footer-list mb-0"> <?php foreach ($footerProduct as $fp) { ?> <li>
                        <a href="
													<?= base_url($fp->slug); ?>"> <?= $fp->name ?> </a>
                      </li> <?php } ?> </ul>
                  </div>
                </div>
              </div>
            </div>
            -->
          </div>
        </div>
      </div>
      <div class="fp-footer-features d-none">
        <div class="row"> <?php $why = $this->db->get('why')->result(); ?> <?php foreach ($why as $w) { ?> <div class="col-12 col-md-6 col-lg-6 col-xl-3">
            <div class="fp-feature-item">
              <div class="icon">
                <img src="
									<?= base_url('assets/img/why/') . $w->img ?>" alt="
									<?= $w->title ?>">
              </div>
              <div class="fp-fi-content">
                <h5 class="title"> <?= $w->title ?> </h5>
                <p> <?= $w->desc ?> </p>
              </div>
            </div>
          </div> <?php } ?> </div>
      </div>
    </div>
  </div>

</footer>
<div class="footer-secondary d-none">
  <div class="container">
    <h4 class="footer-heading text-center">Son Eklenen Ürünler</h4>
    <div class="row">
      <?php
        $lastProducts = $this->db
            ->order_by('id', 'DESC')
            ->limit(6)
            ->get('product')
            ->result();

        foreach ($lastProducts as $p): ?>
          <div class="col-lg-2 col-md-4 col-6 mb-3">
            <a href="<?= base_url($p->slug) ?>" class="product-link">
              <div class="product-item"><?= mb_substr($p->name, 0, 40) ?><?= strlen($p->name) > 40 ? '...' : '' ?></div>
            </a>
          </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div class="footer-bottom">
  <div class="container">
    <div class="row align-items-center">
      <div class="col-md-6">
        <p class="copyright-text">© 2025 ValoHesap. Tüm hakları saklıdır ve tescillidir.</p>
      </div>
      <div class="col-md-6">
        <div class="payment-methods">
          <img class="paytr" src="/assets/img/paytr.png" alt="Paytr">
          <img src="/assets/img/shopier.png" alt="Shopier">
          <img src="/assets/img/payhesap.png" alt="Payhesap">
          <img src="/assets/img/mastercard.svg" alt="Mastercard">
          <img src="/assets/img/visa.svg" alt="Visa">
          <img src="/assets/img/american-express.svg" alt="American Express">
        </div>
      </div>
    </div>
  </div>
</div>

<div id="support-buttons-container">
    <a href="#" id="custom-whatsapp-button" target="_blank" rel="noopener noreferrer">
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp">
        <span>WhatsApp</span>
    </a>
    <a href="#" id="custom-tawk-button">
        <i class="ri-customer-service-2-line"></i>
        <span>Canlı Destek</span>
    </a>
</div>
<script src="<?= base_url('assets/' . $properties->theme) ?>/js/bootstrap.min.js"></script>
<script src="<?= base_url('assets/' . $properties->theme) ?>/js/swiper-bundle.min.js"></script>
<script src="<?= base_url('assets/' . $properties->theme) ?>/js/sweetalert2@11.js"></script>
<script src="<?= base_url('assets/' . $properties->theme) ?>/js/main.js?v=21321321321"></script>

<script>
    // Tawk.to widget'ını script yüklenmeden ÖNCE gizle
    window.Tawk_API = window.Tawk_API || {};
    window.Tawk_LoadStart = new Date();
    
    // Widget'ı varsayılan olarak gizle
    window.Tawk_API.hideWidget = function() {
        if (typeof Tawk_API !== 'undefined' && Tawk_API.hideWidget) {
            Tawk_API.hideWidget();
        }
        hideAllTawkElements();
    };
    
    // Widget yüklendiğinde hemen gizle
    window.Tawk_API.onLoad = function(){
        if (typeof Tawk_API !== 'undefined') {
            try {
                Tawk_API.hideWidget();
            } catch(e) {}
        }
        hideAllTawkElements();
    };
    
    function hideAllTawkElements() {
        // Tüm Tawk elementlerini bul ve gizle
        var selectors = [
            '#tawkchat-container',
            '#tawkchat-minimal-wrapper',
            '.tawk-chat-container',
            'iframe[src*="tawk.to"]',
            '.tawk-button',
            '[class*="tawk"]',
            '[id*="tawk"]',
            'div[data-tawk-widget]',
            'div[class*="tawkchat"]',
            'div[id*="tawkchat"]'
        ];
        
        selectors.forEach(function(selector) {
            try {
                var elements = document.querySelectorAll(selector);
                elements.forEach(function(el) {
                    if (el.id !== 'custom-tawk-button' && !el.closest('#support-buttons-container')) {
                        el.style.display = 'none';
                        el.style.visibility = 'hidden';
                        el.style.opacity = '0';
                        el.style.position = 'absolute';
                        el.style.left = '-9999px';
                        el.style.width = '0';
                        el.style.height = '0';
                    }
                });
            } catch(e) {}
        });
    }
</script>

<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/69589b1bb8624c19854d6f4f/1je11roq1';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

<script>
    // MutationObserver ile Tawk.to elementlerini anında yakala ve gizle
    var observer = new MutationObserver(function(mutations) {
        hideAllTawkElements();
        if (typeof Tawk_API !== 'undefined' && Tawk_API.hideWidget) {
            Tawk_API.hideWidget();
        }
    });
    
    // Observer'ı başlat (sayfa yüklenmeden önce)
    if (document.body) {
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // Canlı Destek butonuna tıklayınca Tawk.to'yu aç
    document.addEventListener('DOMContentLoaded', function() {
        // Observer'ı body'ye bağla
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
        
        // Widget'ı hemen gizle
        hideAllTawkElements();
        
        // Sürekli kontrol et (her 100ms - daha sık)
        var hideInterval = setInterval(hideAllTawkElements, 100);
        
        // Widget yüklendikten sonra da birkaç kez gizle
        setTimeout(function() {
            if (typeof Tawk_API !== 'undefined' && Tawk_API.hideWidget) {
                Tawk_API.hideWidget();
            }
            hideAllTawkElements();
        }, 500);
        
        setTimeout(function() {
            if (typeof Tawk_API !== 'undefined' && Tawk_API.hideWidget) {
                Tawk_API.hideWidget();
            }
            hideAllTawkElements();
        }, 1000);
        
        setTimeout(function() {
            if (typeof Tawk_API !== 'undefined' && Tawk_API.hideWidget) {
                Tawk_API.hideWidget();
            }
            hideAllTawkElements();
        }, 2000);
        
        // Buton click handler
        var tawkButton = document.getElementById('custom-tawk-button');
        if (tawkButton) {
            tawkButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (typeof Tawk_API !== 'undefined' && Tawk_API.maximize) {
                    Tawk_API.maximize();
                }
            });
        }
        
    });
</script>
<script type="text/javascript">
    function search_form() {
        var value = $('#searchInput').val();
        if (value.length > 0) {
            $.ajax({
                url: "<?= base_url('home/getSearchFormProducts'); ?>",
                type: "POST",
                data: {words: $('#searchInput').val()},
                success: function(data) {
                    $("#serch-results").html(data);
                    $('#serch-results').removeClass('d-none');
                    $('#serch-results').slideDown('slow');
                }
            });
        }else{
            $('#serch-results').addClass('d-none');
        }
    }
    function disable_form()
    {
        $('#serch-results').slideUp(800, function(){
            $('#serch-results').addClass('d-none');
        }).delay(800).fadeIn(400);
    }
</script>
</body>
</html>
