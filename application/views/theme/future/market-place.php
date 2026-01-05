<section class="fp-section-page pt-4">

    <div class="container">



        <div class="d-flex align-items-center justify-content-between mb-4">

            <h1 class="title mb-0 text-nowrap" style="font-size:26px">İlan Pazarı Tüm İlanlar</h1>

            <!--<select class="form-select" style="max-width: 200px">

                <option value="" selected>Varsayılan Sıralama</option>

                <option value="">Ucuzdan Pahalıya</option>

                <option value="">Pahalıdan Ucuza</option>

                <option value="">En Çok Satılan</option>

            </select>-->

        </div>



        <div class="row row-products">



            <?php foreach ($products as $p) { ?>

                <div class="col-6 col-md-4 col-lg-3 col-xl-2">

                <div class="fp-product-item">

                    <a class="img" href="<?= base_url($p->slug) ?>"><img src="<?= base_url('assets/img/product/') . $p->img ?>" alt="Ürün Resmi" class="img-product img-aspect"></a>

                    <div class="content">

                        <a class="product-name" href="<?= base_url($p->slug) ?>"><?= $p->name ?></a>

                        <div class="price">

                            <?php 

                                $price = json_decode(calculatePrice($p->id, 1), true);

                                $total = ($price['price'] - $price['normalPrice']) / $price['normalPrice'] * 100;

                            ?>

                            <?php if ($price['isDiscount'] == 1) { ?>

                            <div class="price-new" style="color: #3498db; font-size: 16px; font-weight: 700;"><?= $price['price'] ?> TL</div>

                            <div class="price-old" style="color: #888; font-size: 12px; text-decoration: line-through;"><?= $price['normalPrice'] ?> TL</div>

                            <?php }else{ ?>

                            <div class="price-new" style="color: #3498db; font-size: 16px; font-weight: 700;"><?= $price['price'] ?> TL</div>

                            <?php } ?>

                        </div>

                        <?php if($p->seller_id != 0){

                            $seller = $this->db->where('id', $p->seller_id)->get('user')->row();

                        }

                        ?>

                        <?php if($p->seller_id > 2 && !empty($seller)){ ?>

                            <a class="seller" href="<?=base_url('magaza/' . $seller->shop_slug)?> ">

                                <img src="<?= base_url('assets/img/shop/') . $seller->shop_img ?>" alt="Satıcı Resmi" class="img-seller">

                                <div class="seller-content">

                                    <div class="key">Satıcı</div>

                                    <div class="value"><?= $seller->shop_name ?></div>

                                </div>

                                <i class="ri-arrow-right-s-line icon"></i>

                            </a>

                        <?php } ?>

                    </div>

                </div>

            </div>

        <?php } ?>



        <div class="pagination">

            <?= $this->pagination->create_links(); ?>

        </div>





        </div>



    </div>

</section>