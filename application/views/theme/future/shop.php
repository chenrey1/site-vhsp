<section class="fp-section-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="fp-card fp-card-marketplace-user">
                    <div class="fp-card-body">
                        <img src="<?= base_url('assets/img/shop/') . $seller->shop_img ?>" alt="" class="img-profile">
                        <h1 class="title"><?= $seller->shop_name ?></h1>
                        <div class="fp-stars justify-content-center">
                            <?= getSellerStar($seller->id, '<i class="ri-star-fill"></i>', '<i class="ri-star-line"></i>'); ?>
                        </div>
                        <div class="infos">
                            <div class="fp-info-item fp-card">
                                <div class="content">
                                    <div class="key">Başarılı Satış</div>
                                    <div class="value"><?= $this->db->where('seller_id', $seller->id)->count_all_results('invoice'); ?></div>
                                </div>
                                <div class="icon">
                                    <i class="ri-shield-check-line"></i>
                                </div>
                            </div>
                            <div class="fp-info-item fp-card">
                                <div class="content">
                                    <div class="key">Ürün Sayısı</div>
                                    <div class="value"><?= $this->db->where('seller_id', $seller->id)->where('isActive', 1)->count_all_results('product'); ?></div>
                                </div>
                                <div class="icon">
                                    <i class="ri-box-3-line"></i>
                                </div>
                            </div>
                            <div class="fp-info-item fp-card">
                                <div class="content">
                                    <div class="key">İade Sayısı</div>
                                    <div class="value"><?= $this->db->where('seller_id', $seller->id)->where('isActive', 2)->count_all_results('invoice'); ?></div>
                                </div>
                                <div class="icon">
                                    <i class="ri-refund-2-line text-danger"></i>
                                </div>
                            </div>
                            <div class="fp-info-item fp-card mb-0">
                                <div class="content">
                                    <div class="key">Başarı Oranı</div>
                                    <div class="value">%<?= ($this->db->where('seller_id', $seller->id)->count_all_results('invoice') == 0) ? '100' : (substr(($this->db->where('seller_id', $seller->id)->count_all_results('invoice') - $this->db->where('seller_id', $seller->id)->where('isActive', 2)->count_all_results('invoice')) / $this->db->where('seller_id', $seller->id)->count_all_results('invoice') * 100, 0, 4)) ?></div>
                                </div>
                                <div class="icon">
                                    <i class="ri-checkbox-circle-line text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="fp-card fp-card-client mb-16">
                    <div class="fp-cc-head border-bottom-0">
                        <h1 class="title">İlanlar</h1>
                    </div>
                </div>
                <?php $products = $this->db->where('isActive', 1)->where('seller_id', $seller->id)->order_by('id', 'DESC')->get('product')->result(); ?>
                <div class="row row-products">
                    <?php foreach ($products as $p) { ?>
                        <?php $price = json_decode(calculatePrice($p->id, 1), true); ?>
                        <div class="col-6 col-md-6 col-lg-4 col-xl-3">
                            <div class="fp-product-item">
                                <a class="img" href="<?= base_url($p->slug) ?>"><img src="<?= base_url('assets/img/product/') . $p->img ?>" alt="" class="img-product img-aspect"></a>
                                <div class="content">
                                    <a class="product-name" href="<?= base_url($p->slug) ?>"><?= $p->name ?></a>
                                    <div class="price">
                                        <?php if ($price['isDiscount'] == 1) { ?>
                                            <div class="price-new"><?= $price['normalPrice'] ?> TL</div>
                                            <div class="price-old"><?= $price['price'] ?> TL</div>
                                        <?php }else{ ?>
                                            <div class="price-new"><?= $price['price'] ?> TL</div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>