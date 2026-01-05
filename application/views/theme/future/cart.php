<?php
$amount = 0;
if (!empty($this->session->userdata('info')['id'])) {
  $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
}
?>
<?php foreach ($this->advanced_cart->contents() as $items) {
  $amount = $amount + $items['price'] * $items['qty'];
}
?>
<section class="fp-section-page fp-products-page">
  <div class="container">
    <div class="fp-section-page-head">
      <h1 class="title mb-0">Sepet</h1>
    </div>
    <div class="row">
      <div class="col-lg-12 col-xl-9">
        <?php if (empty($this->session->userdata('info')) && $properties->isGuest == 1) { ?>
          <div class="fp-card mb-3">
            <div class="fp-card-body">
              <h5 class="mb-3 text-uppercase">Son Bir Adım Kaldı</h5>
              <?php echo form_open(base_url('login/regGuest')); ?>
              <div class="fp-input mb-3">
                <div class="icon"><i class="ri-mail-line"></i></div>
                <input type="email" id="email" name="email" class="form-control" placeholder="E-Posta Adresi" required>
              </div>
              <div class="row">
                <div class="col-12 col-md-6">
                  <div class="fp-input mb-3">
                    <div class="icon"><i class="ri-user-line"></i></div>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Ad" required>
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="fp-input mb-3">
                    <div class="icon"><i class="ri-user-star-line"></i></div>
                    <input type="text" id="surname" name="surname" class="form-control" placeholder="Soyad" required>
                  </div>
                </div>
              </div>
              <div class="fp-input mb-3">
                <div class="icon"><i class="ri-phone-line"></i></div>
                <input type="text" id="phone" name="phone" class="form-control" placeholder="Telefon Numarası" required>
              </div>
              <?php if ($properties->isConfirmTc == 1): ?>
                <div class="fp-input mb-3">
                  <div class="icon"><i class="ri-shield-user-line"></i></div>
                  <input minlength="11" maxlength="11" placeholder="TC Kimlik NO" class="form-control" required>
                </div>
                <div class="fp-input mb-3">
                  <div class="icon"><i class="ri-calendar-2-line"></i></div>
                  <input type="text" id="birthday" name="birthday" class="form-control" minlength="4" maxlength="4"
                    placeholder="Telefon Numarası" required>
                </div>
              <?php endif ?>
              <small id="passwordNote" class="d-block">Not: Bilgilerinizi doldurduğunuzda size bir şifre göndereceğiz.
                Bundan sonraki siparişlerinizde mail adresinizi ve şifrenizi kullanabilirsiniz.</small>
            </div>
          </div>
        <?php } ?>
        <div class="fp-card fp-cart-card">
          <div class="fp-card-body">
            <div class="fp-cart-grid">
              <div class="left">
                <div class="column">
                  <div class="title">Ürün</div>
                </div>
                <div class="column price">
                  <div class="title">Fiyat</div>
                </div>
              </div>
              <div class="right">
                <div class="column qty">
                  <div class="title">Adet</div>
                </div>
                <div class="column total">
                  <div class="title">Toplam</div>
                </div>
                <div class="column"></div>
              </div>
            </div>
            <?php foreach ($this->advanced_cart->contents() as $items) { ?>
              <?php 
                // Paket kontrolü
                $isPackage = false;
                $package = null;
                if (isset($items['extras']['type']) && $items['extras']['type'] == 'package' && isset($items['extras']['package_id'])) {
                    $isPackage = true;
                    $package = $this->db->where('id', $items['extras']['package_id'])->get('packages')->row();
                }
                
                if ($isPackage && $package) {
                    // Paket gösterimi
              ?>
              <div class="fp-cart-item">
                <div class="left">
                  <div class="context">
                    <div class="img">
                      <?php 
                        // Paketteki ilk 3 ürünün görselini göster
                        $package_products = $this->db->select('product.*')
                            ->from('package_products')
                            ->join('product', 'product.id = package_products.product_id')
                            ->where('package_products.package_id', $package->id)
                            ->limit(3)
                            ->get()
                            ->result();
                        if (!empty($package_products)) {
                            echo '<div style="display: flex; gap: 4px;">';
                            foreach ($package_products as $pp) {
                                echo '<img src="' . base_url('assets/img/product/') . $pp->img . '" alt="' . $pp->name . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">';
                            }
                            echo '</div>';
                        } else {
                            echo '<div style="width: 50px; height: 50px; background: #eee; border-radius: 4px; display: flex; align-items: center; justify-content: center;"><i class="ri-gift-line" style="font-size: 24px; color: #999;"></i></div>';
                        }
                      ?>
                    </div>
                    <div class="content">
                      <a class="product-name" href="<?= base_url('paket/' . $package->slug) ?>"><?= $package->name ?> (Paket)</a>
                    </div>
                  </div>
                  <div class="price">
                    <div class="price-new"><?= number_format($package->price, 2) ?> TL</div>
                    <?php if ($package->discount_percent > 0): 
                      // Toplam ürün fiyatını hesapla
                      $total_products_price = 0;
                      $all_products = $this->db->select('product.*')
                          ->from('package_products')
                          ->join('product', 'product.id = package_products.product_id')
                          ->where('package_products.package_id', $package->id)
                          ->get()
                          ->result();
                      foreach ($all_products as $p) {
                          $total_products_price += $p->price * 1; // quantity = 1 varsayıyoruz
                      }
                      $original_price = $total_products_price;
                    ?>
                      <div class="price-old"><?= number_format($original_price, 2) ?> TL</div>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="right">
                  <div class="fp-quantity">
                    <input type="number" class="form-control" min="1" value="<?= $items['qty'] ?>"
                      style="pointer-events: none">
                  </div>
                  <div class="price">
                    <div class="text-total">Toplam</div>
                    <div class="price-new"><?= number_format($package->price * $items['qty'], 2) ?> TL</div>
                    <?php if ($package->discount_percent > 0 && isset($original_price)): ?>
                      <div class="price-old"><?= number_format($original_price * $items['qty'], 2) ?> TL</div>
                    <?php endif; ?>
                  </div>
                  <a href="<?= base_url('home/removeCart/' . $items['rowid']); ?>" class="btn btn-opacity-danger"><i
                      class="ri-delete-bin-line icon"></i></a>
                </div>
              </div>
              <?php } else {
                    // Normal ürün gösterimi
                    $product = $this->db->where('id', $items['product_id'])->get('product')->row();
                    if (!$product) continue;
              ?>
              <div class="fp-cart-item">
                <div class="left">
                  <div class="context">
                    <div class="img"><img src="<?= base_url('assets/img/product/') . $product->img; ?>"
                        alt="<?= $product->name ?>" class="img-product"></div>
                    <div class="content">
                      <a class="product-name" href="<?= base_url($product->slug) ?>"><?= $product->name ?></a>
                    </div>
                  </div>
                  <div class="price">
                    <?php
                    $price = json_decode(calculatePrice($product->id, 1), true);
                    $total = ($price['price'] - $price['normalPrice']) / $price['normalPrice'] * 100;
                    ?>
                    <?php if ($price['isDiscount'] == 1) { ?>
                      <div class="price-new"><?= $price['price'] ?> TL</div>
                      <div class="price-old"><?= $price['normalPrice'] ?> TL</div>
                    <?php } else { ?>
                      <div class="price-new"><?= $price['normalPrice'] ?> TL</div>
                    <?php } ?>
                  </div>
                </div>
                <div class="right">
                  <div class="fp-quantity">
                    <input type="number" class="form-control" min="1" value="<?= $items['qty'] ?>"
                      style="pointer-events: none">
                  </div>
                  <div class="price">
                    <div class="text-total">Toplam</div>
                    <?php if ($price['isDiscount'] == 1) { ?>
                      <div class="price-new"><?= $price['price'] * $items['qty'] ?> TL</div>
                      <div class="price-old"><?= $price['normalPrice'] * $items['qty'] ?> TL</div>
                    <?php } else { ?>
                      <div class="price-new"><?= $price['normalPrice'] * $items['qty'] ?> TL</div>
                    <?php } ?>
                  </div>
                  <a href="<?= base_url('home/removeCart/' . $items['rowid']); ?>" class="btn btn-opacity-danger"><i
                      class="ri-delete-bin-line icon"></i></a>
                </div>
              </div>
              <?php } ?>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="col-lg-12 col-xl-3">
        <div class="fp-cart-summary fp-card">
          <div class="fp-card-body">
            <div class="title">Sepet Özeti</div>
            <?php
            $totalSavings = 0;
            $hasDealerDiscount = false;

            foreach ($this->advanced_cart->contents() as $item) {
              // Paket kontrolü - paketler için dealer discount hesaplaması yapmıyoruz
              $isPackageItem = isset($item['extras']['type']) && $item['extras']['type'] == 'package';
              
              if (!$isPackageItem) {
                $productPrice = json_decode(calculatePrice($item['product_id'], 1), true);
                if (isset($productPrice['dealerDiscount']) && $productPrice['dealerDiscount'] > 0) {
                  // Ürünün normal fiyatını veya indirimli fiyatını tespit et
                  $product = $this->db->where('id', $item['product_id'])->get('product')->row();
                  $basePrice = ($product->discount > 0) ? $product->discount : $product->price;

                  // Bayilik indirimli fiyat ile baz fiyat arasındaki farkı hesapla
                  $itemSaving = ($basePrice - $productPrice['price']) * $item['qty'];
                  $totalSavings += $itemSaving;
                  $hasDealerDiscount = true;
                }
              }
            }
            ?>

            <div class="total">
              <div class="key">Ödenecek Tutar</div>
              <div class="value"><?= $amount; ?> TL</div>
            </div>

            <?php if ($hasDealerDiscount && $totalSavings > 0) { ?>
              <div class="total" style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 10px;">
                <div class="key" style="color: #2ecc71;">Toplam Bayi Kazancınız</div>
                <div class="value" style="color: #2ecc71; font-weight: 600;"><?= number_format($totalSavings, 2); ?> TL
                </div>
              </div>
            <?php } ?>
            <div class="input-coupon">
              <?php
              $coupon_code = ($this->advanced_cart->has_discount() && $this->advanced_cart->has_cart_extra("coupon_code")) ? $this->advanced_cart->get_cart_extra("coupon_code") : false;
              ?>
              <input type="text" class="form-control" placeholder="Kupon Kodu Ekleyin" name="coupon"
                value="<?= $coupon_code ?? "" ?>" style="<?= ($coupon_code != false) ? "border-right: 0;" : "" ?>">
              <?php if ($coupon_code != false) { ?>
                <a href="javascript:cancelCoupon()" class="input-group-text" style="background-color: #fff;">
                  <i class="far fa-times-circle" style="color:red;" data-toggle="tooltip" data-placement="top"
                    title="Kuponu kaldır"></i>
                </a>
              <?php } ?>
              <a href="javascript:useCoupon()" class="btn btn-opacity-primary">Uygula</a>
            </div>
            <div class="mb-3" id="couponCodeError"></div>
            <div class="btn-area">
              <?php if (count($this->advanced_cart->contents()) > 0) { ?>
                <?php if (!empty($this->session->userdata('info'))) { ?>
                  <button type="button" class="btn btn-primary w-100" onclick="openPaymentPopup()">
                    <div class="left">Ödeme Yöntemini Seçin</div> <i class="ri-bank-card-line"></i>
                  </button>
                <?php } else { ?>
                  <button type="button" class="btn btn-primary w-100" onclick="openPaymentPopup()">
                    <div class="left">Ödeme Yöntemini Seçin</div> <i class="ri-bank-card-line"></i>
                  </button>
                <?php }
              } else { ?>
                <a href="<?= base_url(''); ?>" class="btn btn-danger mb-0">
                  <div class="left">Sepetiniz Boş Ürün Ekleyin</span></div>
                </a>
              <?php } ?>
              <?php echo form_close(); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Ödeme Yöntemi Seçim Popup -->
<div id="paymentPopupOverlay" onclick="if(event.target.id === 'paymentPopupOverlay') closePaymentPopup();"
  style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(7px); z-index: 9999; align-items: center; justify-content: center; animation: fadeIn 0.2s;">
  <div id="paymentPopupBox" class="payment-popup-box"
    style="padding: 0; border-radius: 16px; max-width: 480px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.3); position: relative; animation: slideUp 0.3s;">
    <div style="padding: 24px; border-bottom: 1px solid #e0e0e0; position: relative;" class="payment-popup-header">
      <h4 style="margin: 0; font-size: 18px; font-weight: 600;" class="payment-popup-title">Ödeme Yöntemi Seç</h4>
              <button onclick="closePaymentPopup()"
         style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 24px; cursor: pointer; opacity: 0.6; transition: all 0.2s; padding: 0; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%;" class="payment-popup-close"
         onmouseover="this.style.background='#e0e0e0'; this.style.opacity='1';"
         onmouseout="this.style.background='none'; this.style.opacity='0.6';">&times;</button>
    </div>

    <div style="padding: 24px; display: flex; flex-direction: column; gap: 12px;">
      <?php if (!empty($this->session->userdata('info'))) { ?>
        <?php if ($amount <= $user->balance) { ?>
          <a href="<?= base_url('client/buyOnBalance'); ?>" class="payment-method-btn"
            style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: var(--primary-color); color: white; border-radius: 12px; text-decoration: none; transition: all 0.2s; border: 2px solid var(--primary-color);"
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';"
            onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
            <div>
              <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                <i class="ri-wallet-3-fill" style="font-size: 20px;"></i>
                <strong style="font-size: 15px;">Bakiye ile Öde</strong>
              </div>
              <small style="font-size: 12px; opacity: 0.9;">Kalan: <?= number_format($user->balance - $amount, 2); ?>
                TL</small>
            </div>
            <div style="text-align: right; font-size: 18px; font-weight: 700;">
              <?= $amount; ?> TL
            </div>
          </a>
        <?php } else { ?>
          <div
            style="padding: 12px; background: rgba(220, 53, 69, 0.1); color: #dc3545; border-radius: 8px; text-align: center; font-size: 13px; border: 1px solid rgba(220, 53, 69, 0.2);">
            <i class="ri-error-warning-line" style="margin-right: 6px;"></i>
            <strong>Bakiye Yetersiz</strong> - Eksik: <?= number_format($amount - $user->balance, 2); ?> TL
          </div>
          <a href="<?= base_url('client/balance'); ?>" class="payment-method-btn"
            style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 14px; background: var(--warning-color, #ffc107); color: #000; border-radius: 12px; text-decoration: none; font-weight: 600; transition: all 0.2s;"
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';"
            onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
            <i class="ri-add-circle-line" style="font-size: 20px;"></i>
            Bakiye Yükle
          </a>
        <?php } ?>
      <?php } ?>

      <div class="payment-divider" style="height: 1px; background: #e0e0e0; margin: 8px 0;"></div>

      <a href="<?= base_url('payment/buyOnCart'); ?>" class="payment-method-btn payment-card-btn"
        style="display: flex; align-items: center; justify-content: space-between; padding: 16px; background: #ffffff; color: #333; border-radius: 12px; text-decoration: none; transition: all 0.2s; border: 1px solid #e0e0e0;"
        onmouseover="this.style.borderColor='#007bff'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)';"
        onmouseout="this.style.borderColor='#e0e0e0'; this.style.transform='none'; this.style.boxShadow='none';">
        <div>
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
            <i class="ri-bank-card-fill" style="font-size: 20px; color: #007bff;"></i>
            <strong style="font-size: 15px;" class="payment-card-text">Kart ile Öde</strong>
          </div>
          <small style="font-size: 12px; opacity: 0.7;" class="payment-card-desc">Komisyon dahil</small>
        </div>
        <div style="text-align: right;">
          <div style="font-size: 18px; font-weight: 700; color: #007bff;">
            <?= number_format($amount + (($amount * getCommission()) / 100), 2); ?> TL
          </div>
          <small style="font-size: 11px; opacity: 0.6;">(+<?= number_format(($amount * getCommission()) / 100, 2); ?>
            TL)</small>
        </div>
      </a>
    </div>
  </div>
</div>

<style>
  /* Gündüz modu (varsayılan) */
  .payment-popup-box {
    background: #ffffff;
    color: #333;
    border: 1px solid #e0e0e0;
  }
  
  .payment-popup-header {
    border-bottom-color: #e0e0e0;
  }
  
  .payment-popup-title {
    color: #333;
  }
  
  .payment-popup-close {
    color: #333;
  }

  /* Gece modu */
  body.dark-mode .payment-popup-box,
  body.theme-dark .payment-popup-box,
  [data-theme="dark"] .payment-popup-box {
    background: #1e1e1e;
    color: #ffffff;
    border-color: #3a3a3a;
  }
  
  body.dark-mode .payment-popup-header,
  body.theme-dark .payment-popup-header,
  [data-theme="dark"] .payment-popup-header {
    border-bottom-color: #3a3a3a;
  }
  
  body.dark-mode .payment-popup-title,
  body.theme-dark .payment-popup-title,
  [data-theme="dark"] .payment-popup-title {
    color: #ffffff;
  }
  
  body.dark-mode .payment-popup-close,
  body.theme-dark .payment-popup-close,
  [data-theme="dark"] .payment-popup-close {
    color: #ffffff;
  }
  
  /* Gece modu için buton ve divider renkleri */
  body.dark-mode .payment-divider,
  body.theme-dark .payment-divider,
  [data-theme="dark"] .payment-divider {
    background: #3a3a3a;
  }
  
  body.dark-mode .payment-card-btn,
  body.theme-dark .payment-card-btn,
  [data-theme="dark"] .payment-card-btn {
    background: #2a2a2a !important;
    color: #ffffff !important;
    border-color: #3a3a3a !important;
  }
  
  body.dark-mode .payment-card-text,
  body.theme-dark .payment-card-text,
  [data-theme="dark"] .payment-card-text {
    color: #ffffff !important;
  }
  
  body.dark-mode .payment-card-desc,
  body.theme-dark .payment-card-desc,
  [data-theme="dark"] .payment-card-desc {
    color: #b0b0b0 !important;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
    }

    to {
      opacity: 1;
    }
  }

  @keyframes slideUp {
    from {
      transform: translateY(20px);
      opacity: 0;
    }

    to {
      transform: translateY(0);
      opacity: 1;
    }
  }
</style>

<script src="<?= base_url("assets/base.js") ?>"></script>
<script type="text/javascript">
  // Popup açma/kapama fonksiyonları
  function openPaymentPopup() {
    document.getElementById('paymentPopupOverlay').style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Sayfa scroll'unu kapat
  }

  function closePaymentPopup() {
    document.getElementById('paymentPopupOverlay').style.display = 'none';
    document.body.style.overflow = 'auto'; // Sayfa scroll'unu aç
  }

  function useCoupon() {
    $.post(
      "<?= base_url('API/useCoupon'); ?>",
      { "coupon": $('[name=coupon]').val() },
      function (data) {
        if (data.status == "success") {
          window.location.reload();
        } else {
          $('#couponCodeError').html(data.message);
          $('#couponCodeError').addClass("btn btn-danger");
          setTimeout(function () {
            $('#couponCodeError').html("");
            $('#couponCodeError').removeClass("btn btn-danger");
          }, 2500);
        }
      },
      "json"
    );
  }
  function cancelCoupon() {
    $.post(
      "<?= base_url('API/cancelCoupon'); ?>",
      {},
      function (data) {
        if (data.status == "success") {
          window.location.reload();
        }
      },
      "json"
    );
  }
</script>
