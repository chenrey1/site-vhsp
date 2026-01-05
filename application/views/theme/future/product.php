<?php $category = $this->db->where('id', $product->category_id)->get('category')->row();?>
<section class="fp-section-page fp-products-page">
    <div class="container">
        <div class="fp-breadcrumb">
            <ul class="list-inline list-unstyled mb-0 list">
                <li><a href="<?= base_url('tum-kategoriler'); ?>" class="link">Tüm Kategoriler</a></li>
                <li><a href="<?= base_url('kategori/') . $category->slug ?>" class="link"><?=$category->name?></a></li>
                <li><a href="#" class="link active"><?=$product->name?></a></li>
            </ul>
        </div>
        <div class="product-detail-box" style="overflow: visible; position: relative;">
            <div class="product-detail-inner-box" style="overflow: visible; position: relative;">
                <div class="row" style="align-items: flex-start; display: flex; flex-wrap: wrap; position: relative;">
                    <div class="col-lg-9">
                        <div class="fp-card fp-product-card fp-product-card-left" style="border-radius:10px; border:none;">
                            <div class="fp-card-body">
                             <div class="row">
                            <div class="col-lg-4">
                                <div class="img-cover"><img src="<?= base_url('assets/img/product/') . $product->img; ?>" alt="" class="img-product img-aspect"></div>
                            </div>
                            <div class="col-lg-8">
                                <div class="content">
                                    <div class="flex-top">
                                      <div class="product-info-title-area">
<?php
// Ürünün kategorisini getir (örnek)
$kategori = $this->db->where('id', $product->category_id)->get('category')->row();

$kategoriSlug = strtolower($kategori->slug);
$icon = '';
$linkClass = '';

if (strpos($kategoriSlug, 'valorant') !== false) {
    $icon = '/assets/future/img/valorant.png';
    $linkClass = 'link-valorant';
} elseif (strpos($kategoriSlug, 'steam') !== false) {
    $icon = '/assets/future/img/steams.png';
    $linkClass = 'link-steam';
} elseif (strpos($kategoriSlug, 'league-of-legends') !== false || strpos($kategoriSlug, 'lol') !== false) {
    $icon = '/assets/future/img/lol-tr.png';
    $linkClass = 'link-league-of-legends';
} elseif (strpos($kategoriSlug, 'pubg') !== false) {
    $icon = '/assets/future/img/pubg-mobile.png';
    $linkClass = 'link-pubg-mobile';
} elseif (strpos($kategoriSlug, 'fortnite') !== false) {
    $icon = base_url('/assets/future/img/fortnite.png');
    $linkClass = 'link-fortnite';
}
else {
    $icon = base_url('assets/img/category/' . $kategori->img);
}

function close_tags_and_fix_strong($html) {
    if (empty($html)) {
        return $html;
    }
    
    // <strong> -> <span style="font-weight:bold;">
    $html = str_ireplace('<strong', '<span style="font-weight:bold;"', $html);
    $html = str_ireplace('</strong>', '</span>', $html);

    // Gereksiz attribute'ları temizle
    $html = preg_replace('/\s+xss="removed"/i', '', $html);
    $html = preg_replace('/\s+data-path-to-node="[^"]*"/i', '', $html);
    $html = preg_replace('/\s+data-index-in-node="[^"]*"/i', '', $html);

    // <p> içindeki <p> etiketlerini düzelt (geçersiz HTML - içerideki <p> etiketlerini kaldır)
    // Önce <p> içindeki <p> etiketlerini bul ve içeriğini koru
    $html = preg_replace_callback('/<p([^>]*)>(.*?)<p([^>]*)>(.*?)<\/p>(.*?)<\/p>/is', function($matches) {
        // İç <p> etiketini kaldır, içeriğini koru
        return '<p' . $matches[1] . '>' . $matches[2] . $matches[4] . $matches[5] . '</p>';
    }, $html);
    
    // Boş <p> etiketlerini kaldır
    $html = preg_replace('/<p[^>]*>\s*<\/p>/i', '', $html);
    $html = preg_replace('/<p[^>]*><br\s*\/?><\/p>/i', '', $html);
    $html = preg_replace('/<p[^>]*><span[^>]*>\s*<\/span><\/p>/i', '', $html);
    $html = preg_replace('/<p[^>]*><span[^>]*><span[^>]*>\s*<\/span><\/span><\/p>/i', '', $html);

    // Kapanmayan etiketleri kapat (sadece gerekli olanları)
    $tags = ['span', 'ul', 'li', 'p', 'div', 'font'];
    foreach ($tags as $tag) {
        $openCount = substr_count($html, '<' . $tag);
        $closeCount = substr_count($html, '</' . $tag . '>');
        if ($openCount > $closeCount) {
            $html .= str_repeat('</' . $tag . '>', $openCount - $closeCount);
        }
    }

    return $html;
}


?>
                             
<div class="icon-area <?= $linkClass ?>">
    <img src="<?= $icon ?>" alt="<?= $kategori->name ?>">
</div>
                                        <div class="ctx">
                                           <h1 class="product-name"><?= $product->name ?></h1>
                                                 <?php if ($product->isStock == 0 || $stock > 0 || $properties->isStock == 1){ ?>

                                                    <?php }else{ ?>
                                            <div class="alert alert-warning mt-2">
                                            <i class="ri-information-line"></i> Bu ürün şu anda tedarik aşamasındadır.
                                          </div>
                                                <?php } ?>
                                        </div>
                                      </div>
                                      <div class="code-area">#22</div>
                                    </div>
                                    <p class="text"><i class="fi fi-rr-info"></i> Ürün hakkında bilgi için açıklamayı kontrol ediniz.</p>
                                    <div class="text d-none">
                                        <p><?=substr(close_tags_and_fix_strong($product->desc), 0, 200) . "..."; ?></p>
                                    </div>
                                    <a href="#urun-hakkinda" class="link-more d-none">Devamını Oku <i class="ri-arrow-down-s-line"></i></a>
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
                                            <i class="ri-key-2-line fs-4 me-2"></i>
                                            <span>Ürün Tipi: <?= (stripos($product->name, 'hesap') !== false ? 'Hesap' : 'Epin') ?></span>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="action-area">
                                        <div class="price">
                                            <i class="fi fi-sr-coins me-2" style="font-size: 32px"></i>
                                            <?php
                                            $price = json_decode(calculatePrice($product->id, 1), true);
                                            $total = ($price['price'] - $price['normalPrice']) / $price['normalPrice'] * 100;
                                            ?>
                                            <?php if ($price['isDiscount'] == 1) { ?>
                                                <div class="price-new" style="color: #3498db; font-size: 24px; font-weight: 700;"><?= $price['price'] ?> TL</div>
                                                <div class="price-old" style="color: #888; font-size: 16px; text-decoration: line-through;"><?= $price['normalPrice'] ?> TL</div>
                                            <?php }else{ ?>
                                                <div class="price-new" style="color: #3498db; font-size: 24px; font-weight: 700;"><?= $price['price'] ?> TL</div>
                                            <?php } ?>
                                        </div>
                                        <?php $inf = json_decode($product->text); ?>
                                        <?php $a = 1; ?>
                                        <?php foreach ($inf as $i) {
                                            if (!empty($i)) { ?>
                                                <input type="text" class="form-control mb-2" id="extras<?=$a?>" name="<?= $i ?>" placeholder="<?= $i ?>" required style="min-height:50px">
                                                <div id="extras<?=$a?>Feedback" class="invalid-feedback"><?= $i ?> alanını boş bırakamazsınız!</div>
                                                <?php $a++; } } ?>
                                        <div class="grid">
                                            <div class="fp-quantity">
                                                <a href="#" class="fp-quantity-btn minus"><i class="ri-subtract-line"></i></a>
                                                <input type="number" class="form-control" min="1" name="amount" id="amount" value="1">
                                                <a href="#" class="fp-quantity-btn plus"><i class="ri-add-line"></i></a>
                                            </div>
                                            <?php if (!empty($this->session->userdata('info')) || $properties->isGuest == 1) { ?>
                                                <?php if ($product->isStock == 0 || $stock > 0 || $properties->isStock == 1){ ?>
                                                    <a id="addItem" onclick="addItem();" class="btn btn-primary"><i class="ri-shopping-cart-2-line icon icon-left"></i> Sepete Ekle</a>
                                                <?php }else{ ?>
                                                    <a class="btn btn-primary">Stok Bulunamadı</a>
                                                <?php } ?>
                                            <?php }else{ ?>
                                                <a href="<?= base_url('hesap') ?>" class="btn btn-warning"><i class="ri-shopping-cart-2-line icon icon-left"></i> Giriş Yapmalısın</a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                            </div>
                        </div>
                                <div class="fp-card" id="urun-hakkinda">
            <div class="fp-card-body">
                <ul class="fp-tabs-nav fp-tabs-nav-system list-inline list-unstyled">
                    <li><a href="#" class="link active" id="urun-hakkinda">Ürün Hakkında</a></li>
                    <li><a href="#" class="link" id="yorumlar">Yorumlar</a></li>
                </ul>
                <div class="fp-tabs">
                    <div class="fp-tabs-content active" id="urun-hakkinda-content">
                        <div class="fp-product-context">
                            <?php if (!empty($product->desc)): ?>
                                <?= close_tags_and_fix_strong($product->desc); ?>
                            <?php else: ?>
                                <p>Bu ürün için açıklama bulunmamaktadır.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="fp-tabs-content" id="yorumlar-content">
                        <?php if (!empty($comments)) { ?>
                            <div class="fp-comments-total">
                                <div class="text">Toplam Puan <span class="fw-medium">(<?= calculateAverageRating($comments); ?>)</span></div>
                                <div class="fp-stars">
                                    <?php
                                    $averageStars = calculateAverageRating($comments);
                                    $filledStars = floor($averageStars);
                                    $emptyStars = 5 - $filledStars;
                                    for ($i = 0; $i < $filledStars; $i++) {
                                        echo '<i class="ri-star-fill"></i>';
                                    }
                                    for ($i = 0; $i < $emptyStars; $i++) {
                                        echo '<i class="ri-star-line"></i>';
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php foreach ($history as $comment) { ?>
                                <?php $user = $this->db->where('id', $comment->user_id)->get('user')->row(); ?>
                                <div class="fp-comment-item">
                                    <div class="user">
                                        <div class="name"><?= $user->name . " " . $user->surname ?></div>
                                        <div class="fp-stars">
                                            <?php
                                            $filledStars = $comment->star;
                                            $emptyStars = 5 - $filledStars;
                                            for ($i = 0; $i < $filledStars; $i++) {
                                                echo '<i class="ri-star-fill"></i>';
                                            }
                                            for ($i = 0; $i < $emptyStars; $i++) {
                                                echo '<i class="ri-star-line"></i>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="content">
                                        <p class="text"><?= $comment->comment ?></p>
                                        <div class="date"><?= $comment->date ?></div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="alert alert-info mb-0">Henüz yorum bulunmamaktadır.</div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
                    </div>
                    
                    <div class="col-lg-3" style="position: -webkit-sticky; position: sticky; top: 20px; align-self: flex-start; max-height: calc(100vh - 40px); overflow-y: auto; height: fit-content; z-index: 10;">
                    <?php
                    $seller = ($product->seller_id == 0) ? convertToObject([
                        "isAdmin" => 1
                    ]) : $this->db->where(['id' => $product->seller_id, "type" => 2])->get('user')->row();
                    ?>
                    <?php if (empty($seller) || $seller->isAdmin == 1) {
                        /*$seller = $this->db->where('isAdmin', 1)->get('user')->row();
                        $seller->shop_name = $properties->name;
                        $seller->shop_img = "assets/img/site/".$properties->img;
                        $seller->success_invoice = $this->db->count_all_results('invoice');
                        $seller->link = base_url('tum-kategoriler');
                        $seller->message = base_url('client/ticket');*/
                    } else {
                        /*
                        $seller->shop_img = "assets/img/shop/".$seller->shop_img;
                        $seller->success_invoice = $this->db->where('seller_id', $seller->id)->count_all_results('invoice');
                        $seller->link = base_url('magaza/') . $seller->shop_slug;
                        $seller->message = "#modal-seller-message";*/
                        ?>
                        <div class="fp-seller-card mb-3">
                            <img src="<?= base_url('assets/img/shop/') . $seller->shop_img ?>" alt="" class="img-profile">
                            <h4 class="name"><?=$seller->shop_name?></h4>
                            <div class="info"><i class="ri-store-line icon"></i> <?= $this->db->where('seller_id', $seller->id)->count_all_results('invoice'); ?> Başarılı Satış</div>
                            <a href="<?= base_url('magaza/') . $seller->shop_slug ?>" class="btn btn-opacity-primary"><i class="ri-grid-fill icon icon-left"></i> Tüm İlanlar</a>
                            <a href="#modal-seller-message" data-bs-target="#modal-seller-message" data-bs-toggle="modal" class="btn btn-opacity-success"><i class="ri-question-answer-line icon icon-left"></i> Mesaj Gönder</a>
                        </div>
                        <div class="modal fade" id="modal-seller-message" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Satıcıya Mesaj Gönder</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="<?= base_url('client/addSupport')."?shop=".$seller->shop_slug; ?>" method="POST">
                                            <div class="mb-3">
                                                <label for="">Konu</label>
                                                <input type="text" class="form-control" name="title" required="">
                                            </div>
                                            <div class="mb-3">
                                                <label for="">Mesaj</label>
                                                <textarea rows="3" class="form-control" name="message"></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100 d-block">Gönder</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
<?php
$this->load->helper('text'); // character_limiter kullanabilmek için
?>
  <?php if (!empty($packages)): ?>
    <!-- Paket Önerileri -->
    <?php foreach ($packages as $package): ?>
      <?php 
      // Toplam fiyat hesapla
      $total_price = 0;
      foreach ($package->products as $pp) {
          $total_price += $pp->price;
      }
      $discount_percent = $total_price > 0 ? (($total_price - $package->price) / $total_price) * 100 : 0;
      ?>
      <div class="fp-card fp-card-client fp-card-client-left mb-3" style="border-radius:10px; border:none; background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);">
        <div class="fp-cc-head" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); padding: 15px; border-radius: 10px 10px 0 0;">
          <div class="d-flex justify-content-between align-items-center">
            <h4 class="mt-0 mb-0" style="color: #fff; font-size: 16px; font-weight: 600;">
              <i class="ri-gift-line"></i> <?= $package->name ?>
            </h4>
            <?php if ($discount_percent > 0): ?>
              <span class="badge badge-danger" style="background: #e74c3c; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                %<?= number_format($discount_percent, 0) ?> İndirim
              </span>
            <?php endif; ?>
          </div>
        </div>
        <div class="fp-cc-body" style="padding: 15px;">
          <!-- Paket içindeki ürün görselleri -->
          <div class="d-flex gap-2 mb-3" style="flex-wrap: wrap;">
            <?php 
            $display_count = min(count($package->products), 3);
            for ($i = 0; $i < $display_count; $i++): 
            ?>
              <div style="position: relative;">
                <img src="<?= base_url('assets/img/product/') . $package->products[$i]->img ?>" 
                     alt="<?= $package->products[$i]->name ?>" 
                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px; border: 2px solid #282828;">
                <?php if ($package->products[$i]->id == $product->id): ?>
                  <div style="position: absolute; bottom: -5px; right: -5px; background: #3498db; color: #fff; padding: 2px 6px; border-radius: 10px; font-size: 9px; font-weight: 600;">
                    Bu ürün
                  </div>
                <?php endif; ?>
              </div>
            <?php endfor; ?>
            <?php if (count($package->products) > 3): ?>
              <div style="width: 60px; height: 60px; background: #282828; border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 12px; font-weight: 600; border: 2px solid #282828;">
                +<?= count($package->products) - 3 ?>
              </div>
            <?php endif; ?>
          </div>
          
          <!-- Fiyat bilgisi -->
          <div class="mb-3">
            <?php if ($total_price > $package->price): ?>
              <div style="color: #888; font-size: 12px; text-decoration: line-through; margin-bottom: 5px;">
                <?= number_format($total_price, 2) ?> ₺
              </div>
            <?php endif; ?>
            <div style="color: #3498db; font-size: 20px; font-weight: 700;">
              <?= number_format($package->price, 2) ?> ₺
            </div>
            <?php if ($total_price > $package->price): ?>
              <div style="color: #27ae60; font-size: 11px; margin-top: 3px;">
                <?= count($package->products) ?> ürün al, <?= number_format($total_price - $package->price, 2) ?> ₺ kazan!
              </div>
            <?php endif; ?>
          </div>
          
          <!-- Paketi Al butonu -->
          <a href="<?= base_url('paket/' . $package->slug) ?>" class="btn btn-primary w-100" style="font-size: 13px; padding: 8px;">
            <i class="ri-gift-line"></i> Paketi Al
          </a>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
  
  <!-- Benzer Ürünler -->
  <div class="fp-card fp-card-client fp-card-client-left" style="border-radius:10px; border:none;">
    <div class="fp-cc-head">
      <center>
        <h4 class="mt-2">Benzer Ürünler</h4>
      </center>
    </div>
    <div class="fp-cc-body" style="max-height: 400px; overflow-y: auto; overflow-x: hidden;">
      <?php
        $products = $this->db
          ->limit(4)
          ->where('category_id', $category->id)
          ->where('isActive', 1)
          ->where('id !=', $product->id) // Mevcut ürünü hariç tut
          ->order_by('id', 'DESC')
          ->get('product')
          ->result();

        foreach($products as $p){
          $price = json_decode(calculatePrice($p->id, 1), true);
      ?>
        <a href="<?= base_url($p->slug) ?>">
          <div class="fp-product-horizontal-mini">
            <div class="img">
              <img src="<?= base_url('assets/img/product/') . $p->img ?>" alt="<?= $p->name ?>" class="img-product">
            </div>
            <div class="content">
              <div class="product-name"><?= character_limiter(strip_tags($p->name), 50) ?></div>
              <div class="price">
                <?php if ($price['isDiscount'] == 1): ?>
                  <div class="price-new" style="color: #3498db; font-size: 16px; font-weight: 700;"><?= $price['price'] ?> TL</div>
                  <div class="price-old" style="color: #888; font-size: 12px; text-decoration: line-through;"><?= $price['normalPrice'] ?> TL</div>
                <?php else: ?>
                  <div class="price-new" style="color: #3498db; font-size: 16px; font-weight: 700;"><?= $price['price'] ?> TL</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </a>
      <?php } ?>
    </div>
  </div>
</div>



    </div>
</section>
<section class="fp-section d-none">
    <div class="container">
        <div class="fp-section-head">
            <h4 class="title mb-0">İlgini Çekebilir</h4>
            <a href="<?=base_url('kategori/') . $category->slug?>" class="btn btn-white rounded-pill">Tüm Ürünler <i class="ri-arrow-right-s-line icon icon-right"></i></a>
        </div>
        <?php $products = $this->db->limit(8)->where('category_id', $category->id)->where('isActive', 1)->get('product')->result(); ?>
        <?php foreach($products as $p){ ?>
            <div class="fp-product-horizontal">
                <div class="left">
                    <div class="img"><img src="<?= base_url('assets/img/') . $p->img ?>" alt="Ürün Resmi" class="img-product"></div>
                    <div class="content">
                        <a class="product-name" href="<?= base_url($p->slug) ?>"><?= $p->name ?></a>
                        <p class="text mb-0"><i class="ri-information-fill"></i> Ürün hakkında detaylı bilgi için ürün sayfasını inceleyebilirsiniz.</p>
                    </div>
                    <?php $price = json_decode(calculatePrice($p->id, 1), true); ?>
                    <div class="price">
                        <?php if ($price['isDiscount'] == 1) { ?>
                            <div class="price-new" style="color: #3498db; font-size: 16px; font-weight: 700;"><?= $price['price'] ?> TL</div>
                            <div class="price-old" style="color: #888; font-size: 12px; text-decoration: line-through;"><?= $price['normalPrice'] ?> TL</div>
                        <?php }else{ ?>
                            <div class="price-new" style="color: #3498db; font-size: 16px; font-weight: 700;"><?= $price['price'] ?> TL</div>
                        <?php } ?>
                    </div>
                </div>
                <div class="price">
                    <?php if ($price['isDiscount'] == 1) { ?>
                        <div class="price-new" style="color: #3498db; font-size: 16px; font-weight: 700;"><?= $price['price'] ?> TL</div>
                        <div class="price-old" style="color: #888; font-size: 12px; text-decoration: line-through;"><?= $price['normalPrice'] ?> TL</div>
                    <?php }else{ ?>
                        <div class="price-new" style="color: #3498db; font-size: 16px; font-weight: 700;"><?= $price['price'] ?> TL</div>
                    <?php } ?>
                </div>
                <div class="right">
                    <div class="fp-quantity">
                        <a href="#" class="fp-quantity-btn minus"><i class="ri-subtract-line"></i></a>
                        <input type="number" class="form-control" min="1" value="1" name="amounts" id="amounts">
                        <a href="#" class="fp-quantity-btn plus"><i class="ri-add-line"></i></a>
                    </div>
                    <?php if (!empty($this->session->userdata('info')) || $properties->isGuest == 1) { ?>
                        <?php if ($product->isStock == 1 && $properties->isStock == 1){ ?>
                            <a href="#" class="btn btn-primary" id="addItem<?=$p->id?>" onclick="addItem(<?=$p->id?>)"><i class="ri-shopping-cart-2-line icon icon-left"></i> Sepete Ekle</a>
                        <?php }else{ ?>
                            <a href="<?=base_url($p->slug)?>" class="btn btn-primary"> Ürünü İncele</a>
                        <?php } ?>
                    <?php }else{ ?>
                        <a href="<?= base_url('hesap') ?>" class="btn btn-warning"><i class="ri-shopping-cart-2-line icon icon-left"></i> Giriş Yapmalısın</a>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
<script type="text/javascript">
function addItem() {
    // 1. Validasyonlar (Buralar aynı)
    var extras = {
        name1: $("#extras1").attr("name"),
        number1: $("#extras1").val(),
        name2: $("#extras2").attr("name"),
        number2: $("#extras2").val(),
        name3: $("#extras3").attr("name"),
        number3: $("#extras3").val()
    };
    
    var is_valid = true;
    if ($("#extras1").prop("required") && extras.number1.length === 0) {
        $("#extras1").addClass("is-invalid"); is_valid = false;
    } else { $("#extras1").removeClass("is-invalid"); }
    
    if ($("#extras2").prop("required") && extras.number2.length === 0) {
        $("#extras2").addClass("is-invalid"); is_valid = false;
    } else { $("#extras2").removeClass("is-invalid"); }
    
    if ($("#extras3").prop("required") && extras.number3.length === 0) {
        $("#extras3").addClass("is-invalid"); is_valid = false;
    } else { $("#extras3").removeClass("is-invalid"); }
    
    if (!is_valid) return;
    
    // Eklenecek adeti al (Boşsa 1 say)
    var addedAmount = parseInt($("#amount").val()) || 1;
    var amount = $("#amount").val();
    
    // 2. GÖNDERME İŞLEMİ
    $.post({
        url: "https://valohesap.com/home/addToCartItem",
        type: "POST",
        data: {
            id: <?= $product->id ?>,
            extras: extras,
            amount: amount
        },
        success: function (response) {
            // A) Buton Yazısı Efekti
            $("#addItem").html("Sepete Eklendi");
            setTimeout(function () {
                $("#addItem").html("Sepete Ekle");
            }, 500);

            // B) SİHİRLİ MATEMATİK KISMI (Düzeltildi)
            
            // 1. Adım: Mevcut sayıyı sayfadan doğru şekilde oku
            var currentText = "";
            
            // Önce masaüstü ikonundaki sayıya bak
            if ($('#basket-button .number').length > 0) {
                currentText = $('#basket-button .number').text();
            } 
            // Eğer o yoksa veya boşsa herhangi bir .number'a bak
            else if ($('.number').length > 0) {
                currentText = $('.number').first().text();
            }

            // 2. Adım: Yazının içindeki SADECE sayıları al (Regex Temizliği)
            // "Sepet (9)" -> "9" olur. " 9 " -> "9" olur.
            var currentTotal = parseInt(currentText.replace(/[^0-9]/g, '')) || 0;

            // 3. Adım: Topla ve Yazdır
            var newTotal = currentTotal + addedAmount;

            // Ekrana bas
            $('.number').html(newTotal);
            $('#MobileNavbarCart').html('Sepet (' + newTotal + ')');
            
            console.log("Eski Sayı: " + currentTotal + " | Eklenen: " + addedAmount + " | Yeni: " + newTotal);
        }
    });
}


function addItems(productId) {
    var extras = {
        name1: $("#extras1").attr("name"),
        number1: $("#extras1").val(),
        name2: $("#extras2").attr("name"),
        number2: $("#extras2").val(),
        name3: $("#extras3").attr("name"),
        number3: $("#extras3").val()
    };
    
    var amounts = $("#amounts").val();
    
    $.post({
        url: "<?= base_url('home/addToCartItem'); ?>",
        type: "POST",
        data: {
            "id": productId, 
            "extras": extras, 
            "amount": amounts
        },
        success: function() {
            $('#addItems' + productId).html('Sepete Eklendi');
            setTimeout(function () {
                $('#addItems' + productId).html('<i class="ri-shopping-cart-2-line icon-left"></i>Sepete Ekle');
            }, 500);
            
            // Sepet sayısını güncelle
            $.post("<?= base_url('API/getCartAmount'); ?>", {}, function(amount) {
                $('.number').html(amount);
                $('#MobileNavbarCart').html('Sepet (' + amount + ')');
            });
        }
    });
}

// Sticky Sidebar Fix - Sayfa yüklendiğinde ve resize'da çalıştır
function fixStickySidebar() {
    var $sidebar = $('.product-detail-box .col-lg-3');
    if ($sidebar.length > 0) {
        // Parent container'ları kontrol et
        var $productBox = $sidebar.closest('.product-detail-box');
        var $innerBox = $sidebar.closest('.product-detail-inner-box');
        var $row = $sidebar.closest('.row');
        
        // Parent container'lara gerekli style'ları ekle
        if ($productBox.length > 0) {
            $productBox.css({
                'overflow': 'visible',
                'position': 'relative'
            });
        }
        
        if ($innerBox.length > 0) {
            $innerBox.css({
                'overflow': 'visible',
                'position': 'relative'
            });
        }
        
        if ($row.length > 0) {
            $row.css({
                'align-items': 'flex-start',
                'display': 'flex',
                'flex-wrap': 'wrap',
                'position': 'relative'
            });
        }
        
        // Sticky positioning'i zorla
        $sidebar.css({
            'position': '-webkit-sticky',
            'position': 'sticky',
            'top': '20px',
            'align-self': 'flex-start',
            'max-height': 'calc(100vh - 40px)',
            'overflow-y': 'auto',
            'height': 'fit-content',
            'z-index': '10'
        });
    }
}

$(document).ready(function() {
    fixStickySidebar();
});

// Resize ve scroll event'lerinde de çalıştır
$(window).on('resize scroll', function() {
    fixStickySidebar();
});
</script> 
