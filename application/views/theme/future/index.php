<section class="fp-slider-area">
    <div class="blur-img">
        <img id="blurImage" src="assets/img/asdasda.jpg" alt="" class="image">
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="fp-swiper-home swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($slider as $s){ ?>
                            <div class="swiper-slide" data-blur='<img src="assets/img/sliderblur.jpg" />'>
                                <a href="<?= $s->buton_2_link ?>" class="fp-swiper-home-item">
                                    <img src="<?= base_url("assets/img/sliders/") . $s->img ?>" alt="" class="img-cover">
                                    <div class="content d-none">
                                        <h3 class="title"><?= $s->title ?></h3>
                                        <p><?= $s->description ?></p>
                                        <?php if($s->buton_2_text != "" && $s->buton_2_link != "") { ?>
                                            <span class="btn btn-primary rounded-pill">
                                                <?= ($s->buton_2_text) ?> 
                                                <i class="ri-arrow-right-line icon icon-right"></i>
                                            </span>
                                        <?php } ?>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="autoplay-progress">
                        <svg viewBox="0 0 48 48">
                            <circle cx="24" cy="24" r="20"></circle>
                        </svg>
                        <span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container">
  <div class="fp-categories">
    <div class="row">
      <div class="col-md-6 col-lg-3">
        <a href="kategori/valorant">
          <div class="fp-sc-item mb-2">
            <div class="img">
              <img src="assets/img/yatay-valo.jpeg" alt="" class="img-cover">
            </div>
            <div class="content">
              <div class="subtitle">Valorant Ürünleri</div>
              <div class="title">Kategoriyi İncele</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-lg-3">
        <a href="kategori/pubg-mobile">
          <div class="fp-sc-item mb-2">
            <div class="img">
              <img src="assets/img/yatay-pubg.jpeg" alt="" class="img-cover">
            </div>
            <div class="content">
              <div class="subtitle">PUBG Mobile Ürünleri</div>
              <div class="title">Kategoriyi İncele</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-lg-3">
        <a href="kategori/league-of-legends">
          <div class="fp-sc-item mb-2">
            <div class="img">
              <img src="assets/img/yatay-lol.jpeg" alt="" class="img-cover">
            </div>
            <div class="content">
              <div class="subtitle">League of Legends Ürünleri</div>
              <div class="title">Kategoriyi İncele</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-md-6 col-lg-3">
        <a href="kategori/steam">
          <div class="fp-sc-item mb-2">
            <div class="img">
              <img src="assets/img/steam slider son.png" alt="" class="img-cover">
            </div>
            <div class="content">
              <div class="subtitle">STEAM ÜRÜNLERİ</div>
              <div class="title">Kategoriyi incele</div>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
</section>

<section class="instagram-follow-section">
    <div class="container">
        <div class="instagram-follow-card">
            <div class="instagram-follow-content">
                <div class="instagram-icon">
                    <i class="ri-instagram-line"></i>
                </div>
                <div class="instagram-text">
                    <span>Kampanyalardan haberdar olmak için bizi takip edin!</span>
                </div>
                <div class="instagram-action">
                    <div class="instagram-link-badge">
                        <i class="ri-at-line"></i>
                        <span>instagram.com/valohesap_com/</span>
                    </div>
                    <a href="http://instagram.com/valohesap_com/" target="_blank" class="instagram-follow-btn">
                        <i class="ri-instagram-line"></i>
                        TAKIP ET
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
// Veritabanından sadece çok satanları çekiyoruz
// İster 24 yap (tavsiyem), ister limiti komple sil
$bestSellers = $this->db->where('is_best_seller', 1)->limit(24)->get('product')->result();
?>

<?php if (!empty($bestSellers)): ?>
<section class="index-products fp-section section-modern">
    <div class="container mb-4">
        <div style="text-align: center; width: 100%;">
            <div class="section-title-modern">
                <h2 class="title-text">Çok Satanlar</h2>
                <span class="title-accent"></span>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row row-products index-products-row">
            <?php foreach ($bestSellers as $p): ?>
                <?php $price = json_decode(calculatePrice($p->id, 1), true); ?>
                
                <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-4">
                    <div class="product-card-modern">
                        <?php if(isset($p->is_new) && $p->is_new == 1): ?>
                            <div class="product-card-modern__badge product-card-modern__badge--new">
                                <span>Yeni</span>
                            </div>
                        <?php endif; ?>
                        <div class="product-card-modern__badge product-card-modern__badge--bestseller">
                            <span>Çok Satan</span>
                        </div>
                        <?php if ($price['isDiscount'] == 1): ?>
                            <?php $discountPercent = round(($price['normalPrice'] - $price['price']) / $price['normalPrice'] * 100); ?>
                            <div class="product-card-modern__discount-badge">
                                %<?= $discountPercent ?>
                            </div>
                        <?php endif; ?>
                        
                        <a href="<?= base_url($p->slug) ?>" class="product-card-modern__image">
                            <img src="<?= base_url('assets/img/product/') . $p->img ?>" alt="<?= $p->name ?>">
                        </a>
                        
                        <div class="product-card-modern__content">
                            <h3 class="product-card-modern__title">
                                <a href="<?= base_url($p->slug) ?>"><?= $p->name ?></a>
                            </h3>
                            <div class="product-card-modern__price">
                                <?php if ($price['isDiscount'] == 1): ?>
                                    <span class="product-card-modern__price--current"><?= number_format($price['price'], 2) ?> ₺</span>
                                    <span class="product-card-modern__price--old"><?= number_format($price['normalPrice'], 2) ?> ₺</span>
                                <?php else: ?>
                                    <span class="product-card-modern__price--current"><?= number_format($price['price'], 2) ?> ₺</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= base_url($p->slug) ?>" class="product-card-modern__button">
                                <span>Sepete Ekle</span>
                                <i class="ri-shopping-basket-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
<?php 
$dealProducts = $this->db->where('is_deal', 1)->limit(24)->get('product')->result();
?>

<?php if (!empty($dealProducts)): ?>
<section class="index-products fp-section section-modern" style="background-color: var(--bg-secondary-color);">
    <div class="container mb-4">
        <div style="text-align: center; width: 100%;">
            <div class="section-title-modern section-title-modern--deal">
                <h2 class="title-text">Günün Fırsatları</h2>
                <span class="title-accent"></span>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row row-products index-products-row">
            <?php foreach ($dealProducts as $p): ?>
                <?php $price = json_decode(calculatePrice($p->id, 1), true); ?>
                
                <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-4">
                    <div class="product-card-modern product-card-modern--deal">
                        <?php if(isset($p->is_new) && $p->is_new == 1): ?>
                            <div class="product-card-modern__badge product-card-modern__badge--new">
                                <span>Yeni</span>
                            </div>
                        <?php endif; ?>
                        <div class="product-card-modern__badge product-card-modern__badge--deal">
                            <span>Fırsat</span>
                        </div>
                        <?php if ($price['isDiscount'] == 1): ?>
                            <?php $discountPercent = round(($price['normalPrice'] - $price['price']) / $price['normalPrice'] * 100); ?>
                            <div class="product-card-modern__discount-badge">
                                %<?= $discountPercent ?>
                            </div>
                        <?php endif; ?>
                        
                        <a href="<?= base_url($p->slug) ?>" class="product-card-modern__image">
                            <img src="<?= base_url('assets/img/product/') . $p->img ?>" alt="<?= $p->name ?>">
                        </a>
                        
                        <div class="product-card-modern__content">
                            <h3 class="product-card-modern__title">
                                <a href="<?= base_url($p->slug) ?>"><?= $p->name ?></a>
                            </h3>
                            <div class="product-card-modern__price">
                                <?php if ($price['isDiscount'] == 1): ?>
                                    <span class="product-card-modern__price--current"><?= number_format($price['price'], 2) ?> ₺</span>
                                    <span class="product-card-modern__price--old"><?= number_format($price['normalPrice'], 2) ?> ₺</span>
                                <?php else: ?>
                                    <span class="product-card-modern__price--current"><?= number_format($price['price'], 2) ?> ₺</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= base_url($p->slug) ?>" class="product-card-modern__button product-card-modern__button--deal">
                                <span>Sepete Ekle</span>
                                <i class="ri-shopping-basket-line"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="container">
    <div class="fp-categories">
        <div class="fp-swiper-categories swiper">
            <div class="swiper-wrapper">
                <?php foreach($editor_choice as $ec){ ?>
                    <div class="swiper-slide" onclick="window.location='<?= $ec->link ?>'">
                        <div class="fp-sc-item">
                            <img src="<?= base_url("assets/img/home_choice/") . $ec->img ?>" alt="Editörün Seçimi Ürün Linki" class="img">
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="fp-swiper-categories-prev fp-swiper-prev"><i class="ri-arrow-left-s-line"></i></div>
            <div class="fp-swiper-categories-next fp-swiper-next"><i class="ri-arrow-right-s-line"></i></div>
        </div>
    </div>
</section>

<?php
$altKategoriler = $products;
$anaGruplar = [];

foreach ($altKategoriler as $alt) {
    $kategori = $this->db->where('name', $alt['title'])->get('category')->row();
    if (!$kategori) continue;

    // Fiyatlara göre ucuzdan pahalıya sıralama
    usort($alt['products'], function ($a, $b) {
        $priceA = json_decode(calculatePrice($a->id, 1), true)['price'];
        $priceB = json_decode(calculatePrice($b->id, 1), true)['price'];
        return $priceA <=> $priceB;
    });

    $anaID = $kategori->mother_category_id != 0 ? $kategori->mother_category_id : $kategori->id;
    $anaKategori = $this->db->where('id', $anaID)->get('category')->row();

    if (!isset($anaGruplar[$anaID])) {
        $anaGruplar[$anaID] = [
            'anaKategori' => $anaKategori,
            'altSekmeler' => []
        ];
    }

    $anaGruplar[$anaID]['altSekmeler'][] = [
        'kategori' => $kategori,
        'urunler' => $alt['products']
    ];
}
?>

<?php $i = 0; ?>
<?php foreach ($anaGruplar as $anaID => $grup): ?>
<?php
    $kategoriAd = strtolower($grup['anaKategori']->name);
    $buttonClass = '';
    $linkClass = '';
    $icon = '';

    if (strpos($kategoriAd, 'valorant') !== false) {
        $buttonClass = 'btn-valorant';
        $linkClass = 'link-valorant';
        $icon = "/assets/future/img/valorant.png";
    } elseif (strpos($kategoriAd, 'steam') !== false) {
        $buttonClass = 'btn-steam';
        $linkClass = 'link-steam';
        $icon = "/assets/future/img/steams.png";
    } elseif (strpos($kategoriAd, 'league of legends') !== false || strpos($kategoriAd, 'lol') !== false) {
        $buttonClass = 'btn-lol';
        $linkClass = 'link-league-of-legends';
        $icon = "/assets/future/img/lol-tr.png";
    } elseif (strpos($kategoriAd, 'pubg') !== false) {
        $buttonClass = 'btn-pubg';
        $linkClass = 'link-pubg';
        $icon = "/assets/future/img/pubg-mobile.png";
    } else {
        $icon = base_url('assets/img/category/') . $grup['anaKategori']->img;
    }
?>
<section class="index-products fp-section fp-section-keys">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="product-section-head fp-section-head">
                    <div class="product-section-titles">
                        <ul class="nav nav-pills" id="tabMain-<?= $anaID ?>" role="tablist">
                            <?php foreach ($grup['altSekmeler'] as $j => $sekme): ?>
                            <?php
                                $uniqueTabId = "tab-{$anaID}-{$sekme['kategori']->id}";
                                $uniquePaneId = "tabpane-{$anaID}-{$sekme['kategori']->id}";
                            ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fp-spa-nav-link <?= $j == 0 ? 'active' : '' ?> <?= $buttonClass ?>"
                                        id="<?= $uniqueTabId ?>"
                                        data-bs-toggle="tab"
                                        data-bs-target="#<?= $uniquePaneId ?>"
                                        type="button"
                                        role="tab"
                                        aria-controls="<?= $uniquePaneId ?>"
                                        aria-selected="<?= $j == 0 ? 'true' : 'false' ?>">
                                    <div class="icon-area <?= $linkClass ?>">
                                        <img src="<?= $icon ?>" alt="<?= $grup['anaKategori']->name ?>">
                                    </div>
                                    <?= $sekme['kategori']->name ?>
                                </button>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="product-section-links">
                        <a class="btn btn-opacity-primary" href="<?= base_url('kategori/' . $grup['anaKategori']->slug) ?>">
                            Tümü <i class="fi fi-rs-angle-small-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="myTabContent-<?= $anaID ?>">
            <?php foreach ($grup['altSekmeler'] as $j => $sekme): ?>
            <?php
                $uniqueTabId = "tab-{$anaID}-{$sekme['kategori']->id}";
                $uniquePaneId = "tabpane-{$anaID}-{$sekme['kategori']->id}";
            ?>
            <div class="tab-pane fade <?= $j == 0 ? 'show active' : '' ?>"
                 id="<?= $uniquePaneId ?>"
                 role="tabpanel"
                 aria-labelledby="<?= $uniqueTabId ?>">
                <div class="row row-products index-products-row">
                    <?php foreach ($sekme['urunler'] as $p): ?>
                        <?php $price = json_decode(calculatePrice($p->id, 1), true); ?>
                        <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-4">
                            <div class="product-card-modern">
                                <?php if ($price['isDiscount'] == 1): ?>
                                    <?php $discountPercent = round(($price['normalPrice'] - $price['price']) / $price['normalPrice'] * 100); ?>
                                    <div class="product-card-modern__discount-badge">
                                        %<?= $discountPercent ?>
                                    </div>
                                <?php endif; ?>
                                <a href="<?= base_url($p->slug) ?>" class="product-card-modern__image">
                                    <img src="<?= base_url('assets/img/product/') . $p->img ?>" alt="<?= $p->name ?>">
                                </a>
                                <div class="product-card-modern__content">
                                    <h3 class="product-card-modern__title">
                                        <a href="<?= base_url($p->slug) ?>"><?= $p->name ?></a>
                                    </h3>
                                    <div class="product-card-modern__price">
                                        <?php if ($price['isDiscount'] == 1): ?>
                                            <span class="product-card-modern__price--current"><?= number_format($price['price'], 2) ?> ₺</span>
                                            <span class="product-card-modern__price--old"><?= number_format($price['normalPrice'], 2) ?> ₺</span>
                                        <?php else: ?>
                                            <span class="product-card-modern__price--current"><?= number_format($price['price'], 2) ?> ₺</span>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= base_url($p->slug) ?>" class="product-card-modern__button">
                                        <span>Sepete Ekle</span>
                                        <i class="ri-shopping-basket-line"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($i == 0): ?>
    <?php $homeCategory = $this->db->order_by('home_category.id', 'DESC')->limit(2)->select('home_category.*, category.name')->join('category', 'category.id = category_id', 'left')->get('home_category')->result(); ?>
    <div class="index-grid-img my-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="fp-home-slider-mini-grid">
                        <?php foreach($homeCategory as $hc): ?>
                            <div class="index-grid-img-area">
                                <a href="#">
                                    <img src="<?= base_url('assets/img/home_category/') . $hc->img ?>" alt="" class="img-cover">
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php $i++; ?>
<?php endforeach; ?>

<div style="border-top: 1px solid rgba(255, 255, 255, 0.08); margin: 30px 0 30px 0;"></div>
<section class="fp-testimonials">
  <div class="container">
    <div class="head-area">
      <div class="left">
      <div class="logo-container" style="margin-right:30px;">
    <a href="<?= base_url() ?>">
      <img src="<?= base_url('assets/future/img/vhsp-aqua.png') ?>" 
           class="logo-dark" 
           alt="ValoHesap Logo" 
           style="height:90px;">
      <img src="<?= base_url('assets/future/img/vhsp-siyah.png') ?>" 
           class="logo-light" 
           alt="ValoHesap Logo" 
           style="height:90px;">
    </a>
  </div>
      </div>
      <div class="right">
        <div class="content">
          <div class="stars">
            <i class="ri-star-fill"></i>
            <i class="ri-star-fill"></i>
            <i class="ri-star-fill"></i>
            <i class="ri-star-fill"></i>
            <i class="ri-star-fill"></i>
          </div>
          
<p>ValoHesap, müşteri deneyimine ve değerlendirmelere önem verir. Ürün alan kullanıcıların yorumları aşağıdadır:</p>
        </div>
      </div>
    </div>

    <div class="swiper mySwiper2">
      <div class="swiper-wrapper">

        <?php
$comments = [
    ["Valorant Yüksek Skin Mail Değişen", "maili falan her şeyi değiştim bir sıkıntı yok 60 skin olan ilanı aldım 85 skin civarı çıktı memnunum"],
    ["Valorant İstek Skin Garantili", "Kaos vandal için aldım yanında baya bir şey daha çıktı iş görür."],
    ["FC 26 Offline Hesap", "Kariyer modunu oynama fırsatım oldu hiç sıkıntı yok direkt giriyor teşekkürler fiyat süper."],
    ["Valorant Yüksek Skin Mail Değişen", "kendi üstüme aldım hesabı tertemizz."],
    ["Valorant Random VP", "2925VP ÇIKTI ALLAH RAZI OLSUN SİZDEN MUTLAKA BİR DAHA ALICAM"],
    ["Valorant Yüksek Skin Mail Değişen", "Bütün bilgileri değiştirdim sorunsuz vaat edilen her şey var 10/10"],
    ["Fortnite Mail Değişen Hesap", "hesapta nadir og skin var valla beklemiyodum bu kadarını dandik bp skinleri dolu olur sanıyordum"],
    ["Valorant Random VP", "Kod direkt düştü çalışıyor 1700vp geldi süper oldu"],
    ["Valorant Hesap", "YAĞMACI PHANTOM İÇİN ALDIM ÇIKTI VALLA HELAL OLSUN"],
    ["Fortnite Mail Değişen", "beyler direkt geldi valla güvenilir baya bi skin var hesapta"],
    ["ChatGPT Pro", "Cidden çok büyük kolaylık 200 dolar olan üyelik baya ucuza gelmiş oldu tavsiye ederim"],
    ["Steam Offline God of War", "valla gow serisini bitirmek istiyodum fiyatlar uçunca buradan aldım sorunsuz hesaba girdim hikaye akıyor"],
    ["Minecraft Bilgileri Değişen Hesap", "sunuculardan bansız tertemiz hesap agalarla sv açtık oynuyoz arada da hypixel takılıyoz :D cok uyguna geldi sag olun"],
    ["Valorant Random Hesap", "ilk defa aldm ejder vandal cıktı eyvallahhh."],
    ["Steam Offline Spider-Man", "spider man miles morales için aldım kurulumu falan çok basitmiş direkt oynamaya başladım eyvallah"],
    ["Steam Offline PES 2021", "piyasada pes 21 kalmamıştı ilaç gibi geldi valla yamayı da kurdum efsane oldu sağolun"],
    ["Valorant Random VP", "kod anında teslim edildi tşk"],
    ["Roblox Offsale Garantili Hesap", "Baya bi offsale çıktı içinden teşekkürler toplu alacağım"],
    ["FM 26 Offline Hesap", "yükledim çalışıyo valla fm26 gecelerimiz başlıyor sağolun in game editörde vardı hesapta"],
    ["Netflix", "Mail ve şifre ödedikten sonra direkt geldi izlemeye başladım bile sağ olun."],
    ["Valorant Random Hesap", "kuronami vandal bekliyodum yağmacı çıktı o da olur :D"],
    ["Valorant Random VP", "piyasadaki en ucuz ve kaliteli yerdir kendileri sürekli alım yapıyorum"], 
    ["Steam Offline RDR2 Hesabı", "Satın aldıktan sonra steam hesabını ilettiler sorunsuzca oynuyorum su an teşekkürler."],
    ["PUBG Random Hesap", "birkaç tane destansı kostüm var teşekkürler fiyat performans arkadaşlarla oynamak için almıştım"],
    ["Valorant Random Hesap", "beklentim düşüktü ama skinler beklediğimden çok çok iyi geldi."], 
    ["Valorant Random VP", "Ödeme yaptıktan hemen sonra mailime düştü teşekkürler"],
    ["Valorant Random VP", "3. alışverişim hala bi kusurlarını görmedim parasını çıkartıyor"],
    ["Valorant İstek Skin Garantili", "bu fiyata bu skinler çok iyi valla artık oyuna para yatırmam"],
    ["Valorant Hesap", "Diğer siteler gibi değiller ilgililer"],
    ["Valorant Random VP", "direkt geldi eyv"],
    ["Roblox Robux", "Robuxlar hesabıma yansıdı teşekkürler güvenilir."],
    ["Valorant İstek Skin Garantili", "destek ekibi falan baya hızlı her soruma yardımcı oldular"],
    ["Valorant Hesap", "full skinli geldi hesap eyv admin"],
    ["Valorant Random VP", "vp kodunu sorunsuzca aktif ettim teşekkürler"],
    ["Valorant Yüksek Skin Mail Değişen", "mail garantili olması güven veriyo"],
    ["Valorant Random Hesap", "fiyat performans ürünü cidden bu fiyata bu skinlerle oynamam inanılmaz"],
    ["Valorant Random VP", "gece 3 te aldım anında geldi çok teşekkürler 825vp çıktı"],
    ["Valorant Hesap", "Yayıncıda görmüştüm doğruymuş"],
    ["Valorant Random Hesap", "0 beklenti aldım champions 2021 vandal çıktı şansıma çok mutluyumm"],
    ["Valorant Random VP", "hızlı ve ucuz herhangi bir sorun yaşamadan kodu aktif ettim legit"]
];

          foreach ($comments as $c) {
        ?>
        <div class="swiper-slide">
          <div class="fp-testimonials-item">
            <div class="user">
              <img src="assets/img/profile-testimonials.png" alt="" class="img-profile">
              <div class="info">
                <div class="stars">
                  <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i>
                </div>
                <a href="#" class="product-link"><?= $c[0] ?></a>
              </div>
            </div>
            <div class="desc"><?= $c[1] ?></div>
          </div>
        </div>
        <?php } ?>

      </div>

      <div class="swiper-button-next fp-swiper-next fp-swiper-testimonials-next"><i class="ri-arrow-right-s-line"></i></div>
      <div class="swiper-button-prev fp-swiper-prev fp-swiper-testimonials-prev"><i class="ri-arrow-left-s-line"></i></div>
      <div class="swiper-paginationn"></div>
    </div>
  </div>
</section>

<div class="fp-footer-features">
  <div class="container">
    <div class="content-area">
      <div class="row align-items-center">
        <div class="col-12 col-md-6 col-lg-6 col-xl-3">
          <div class="fp-feature-item">
            <div class="icon">
              <img src="assets/img/delivery50429.png" alt="Hızlı Teslimat">
            </div>
            <div class="fp-fi-content">
              <h5 class="title mb-0">Hızlı Teslimat</h5>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-6 col-xl-3">
          <div class="fp-feature-item">
            <div class="icon">
              <img src="assets/img/secureshield39269.png" alt="Güvenli Alışveriş ">
            </div>
            <div class="fp-fi-content">
              <h5 class="title mb-0">Güvenli Alışveriş </h5>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-6 col-xl-3">
          <div class="fp-feature-item">
            <div class="icon">
              <img src="assets/img/offer85852.png" alt="Uygun Fiyatlandırma">
            </div>
            <div class="fp-fi-content">
              <h5 class="title mb-0">Uygun Fiyatlandırma</h5>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg-6 col-xl-3">
          <div class="fp-feature-item">
            <div class="icon">
              <img src="assets/img/customerservice6466.png" alt="7/24 Müşteri Hizmetleri">
            </div>
            <div class="fp-fi-content">
              <h5 class="title mb-0">7/24 Müşteri Hizmetleri</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (!empty($footerBlog)) { ?>
<div style="border-top: 1px solid rgba(255, 255, 255, 0.08); margin: 30px 0 30px 0;"></div>
<section class="fp-blog-section-home" style="background-color: var(--bg-secondary-color); padding: 50px 0;">
  <div class="container">
    <div class="fp-blog-section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <i class="ri-article-line" style="font-size: 28px; color: #fff;"></i>
        <h2 class="fp-blog-section-title" style="font-size: 28px; font-weight: 700; color: #fff; margin: 0; text-transform: uppercase; letter-spacing: 1px;">BLOG YAZILARI</h2>
      </div>
      <a href="<?= base_url('makale-listesi') ?>" class="fp-blog-view-all-btn" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);">
        <span>Tüm Yazıları Gör</span>
        <i class="ri-arrow-right-line"></i>
      </a>
    </div>
    
    <div class="row">
      <?php 
      $turkceAylar = [
        '01' => 'OCA', '02' => 'ŞUB', '03' => 'MAR', '04' => 'NIS',
        '05' => 'MAY', '06' => 'HAZ', '07' => 'TEM', '08' => 'AGU',
        '09' => 'EYL', '10' => 'EKI', '11' => 'KAS', '12' => 'ARA'
      ];
      
      foreach (array_slice($footerBlog, 0, 3) as $blog) { 
        $tarihObj = DateTime::createFromFormat('d.m.Y', $blog->date);
        if (!$tarihObj) {
          $tarihObj = DateTime::createFromFormat('Y-m-d', $blog->date);
        }
        $gun = $tarihObj ? $tarihObj->format('d') : date('d');
        $ay = $tarihObj ? $turkceAylar[$tarihObj->format('m')] : 'OCA';
        $kisaIcerik = strip_tags($blog->content);
        $kisaIcerik = mb_substr($kisaIcerik, 0, 80) . '...';
      ?>
        <div class="col-md-4 mb-3">
          <a href="<?= base_url('makale/') . $blog->slug ?>" class="fp-blog-card-home-link" style="display: block; text-decoration: none;">
            <div class="fp-blog-card-home" style="position: relative; border-radius: 12px; overflow: hidden; height: 280px; transition: all 0.3s ease; cursor: pointer;">
              <img src="<?= base_url('assets/img/blog/') . $blog->img ?>" alt="<?= htmlspecialchars($blog->title) ?>" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;">
              
              <!-- Gradient Overlay -->
              <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.5) 60%, transparent 100%); padding: 20px; height: 100%; display: flex; flex-direction: column; justify-content: flex-end;">
                <!-- Date Badge -->
                <div class="fp-blog-date-badge" style="position: absolute; top: 12px; left: 12px; background: rgba(24, 24, 24, 0.9); backdrop-filter: blur(10px); border-radius: 8px; padding: 6px 10px; text-align: center; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);">
                  <div style="font-size: 18px; font-weight: 700; color: #fff; line-height: 1;"><?= $gun ?></div>
                  <div style="font-size: 10px; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px;"><?= $ay ?></div>
                </div>
                
                <!-- Content -->
                <div>
                  <h3 style="font-size: 18px; font-weight: 600; color: #fff; margin: 0 0 8px 0; line-height: 1.3;">
                    <?= htmlspecialchars($blog->title) ?>
                  </h3>
                  <p style="font-size: 13px; color: #ccc; line-height: 1.5; margin: 0 0 10px 0;">
                    <?= $kisaIcerik ?>
                  </p>
                  <div style="display: inline-flex; align-items: center; gap: 6px; color: #3498db; font-weight: 600; font-size: 13px;">
                    <span>Devamını Oku</span>
                    <i class="ri-arrow-right-line"></i>
                  </div>
                </div>
              </div>
            </div>
          </a>
        </div>
      <?php } ?>
    </div>
  </div>
</section>

<style>
.fp-blog-section-home {
  background-color: var(--bg-secondary-color);
}

.fp-blog-view-all-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4) !important;
  background: linear-gradient(135deg, #2980b9 0%, #21618c 100%) !important;
}

.fp-blog-card-home-link {
  text-decoration: none !important;
}

.fp-blog-card-home:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
}

.fp-blog-card-home:hover img {
  transform: scale(1.08);
}

.fp-blog-card-home h3 {
  transition: color 0.3s ease;
}

.fp-blog-card-home:hover h3 {
  color: #3498db !important;
}

.fp-blog-card-home:hover .fp-blog-date-badge {
  background: rgba(52, 152, 219, 0.95) !important;
}

@media (max-width: 768px) {
  .fp-blog-section-header {
    flex-direction: column !important;
    align-items: flex-start !important;
    gap: 20px !important;
  }
  
  .fp-blog-section-title {
    font-size: 22px !important;
  }
  
  .fp-blog-view-all-btn {
    width: 100% !important;
    justify-content: center !important;
  }
  
  .fp-blog-card-home {
    height: 240px !important;
  }
}
</style>
<?php } ?>