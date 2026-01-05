<?php
$user_id = $this->session->userdata('info')['id'];

$shops = $this->db->order_by('id', 'DESC')
    ->where([
        'status' => 0,
        'type !=' => 'deposit',
        'user_id' => $user_id,
        'price >' => 0
    ])
    ->get('shop')
    ->result();

$pending_products = $this->db->where('isActive', 1)
    ->where('user_id', $user_id)
    ->get('pending_product')
    ->result();

$pending_products_no_stock = $this->db->select('invoice.*, product.name')
    ->from('invoice')
    ->where('invoice.isActive', 1)
    ->where('invoice.seller_id', 0)
    ->join('shop', 'shop.id = invoice.shop_id', 'inner')
    ->where('shop.user_id', $user_id) // Shop tablosundan user_id kontrolü
    ->join('product', 'product.id = invoice.product_id', 'left')
    ->get()
    ->result();

$pending_products_merged = array_merge($pending_products, $pending_products_no_stock);
?>

<div class="col-lg-9">
    <div class="fp-card fp-card-client">
        <div class="fp-cc-head">
            <h1 class="title">Siparişlerim</h1>
        </div>

        <div class="fp-cc-body">
        <?php if (!empty($pending_products_merged)): ?>
                <div class="fp-order-item fp-card">
                    <div class="head">
                        <div class="name">
                            <div class="imgs">#</div>
                            <div class="area text-start">
                                <div class="title-mini">Bekleyen Siparişler</div>
                                <div class="text">Toplam: <?= count($pending_products_merged) ?></div>
                            </div>
                        </div>
                        <div class="icon-right">
                            <i class="ri-arrow-down-s-line"></i>
                        </div>
                    </div>
                    <div class="body">
                        <?php foreach($pending_products_merged as $pending_product):
                            $productInf = $this->db->where('id', $pending_product->product_id)->get('product')->row();
                            ?>
                            <div class="fp-order-alt-item">
                                <div class="area area-product text-start">
                                    <img src="<?= base_url('assets/img/product/') . $productInf->img; ?>" alt="" class="img-product">
                                    <div class="text"><?= $productInf->name ?></div>
                                </div>
                                <div class="area">
                                    <div class="title-mini">Fiyat</div>
                                    <div class="text"><?= $pending_product->price ?> TL</div>
                                </div>
                                <div class="area">
                                    <div class="title-mini">Tarih</div>
                                    <div class="text"><?= $pending_product->date ?></div>
                                </div>
                                <div class="area">
                                    <div class="title-mini">Durum</div>
                                    <div class="text"><span class="badge bg-warning">Teslimat Bekleniyor</span></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
        <?php endif; ?>
            <?php foreach($shops as $shop):
                $products1 = $this->db->where('shop_id', $shop->id)->get('invoice')->result();
                $products2 = $this->db->where('isActive', 1)->where('shop_id', $shop->id)->get('pending_product')->result();
                $mergedProducts = array_merge($products1, $products2);

                if (!empty($mergedProducts)):
                    $firstProduct = $this->db->where('id', $mergedProducts[0]->product_id)->get('product')->row();
                    ?>
                    <div class="fp-order-item fp-card">
                        <div class="head">
                            <div class="name">
                                <div class="imgs">
                                    <img src="<?= base_url('assets/img/product/') . $firstProduct->img; ?>" alt="" class="img-product">
                                    <?php if (count($mergedProducts) > 1): ?>
                                        <div class="more">+<?=count($mergedProducts) - 1;?></div>
                                    <?php endif ?>
                                </div>
                                <div class="area text-start">
                                    <div class="title-mini">Sipariş</div>
                                    <div class="text">#<?=$shop->id?></div>
                                </div>
                            </div>
                            <div class="area">
                                <div class="title-mini">Fiyat</div>
                                <div class="text"><?= $shop->price ?> TL</div>
                            </div>
                            <div class="area date">
                                <div class="title-mini">Tarih</div>
                                <div class="text"><?= $shop->date ?></div>
                            </div>
                            <div class="icon-right">
                                <i class="ri-arrow-down-s-line"></i>
                            </div>
                        </div>
                        <div class="body">
                            <?php foreach ($mergedProducts as $product):
                                $productInf = $this->db->where('id', $product->product_id)->get('product')->row();
                                $product_comment = $this->db->where(['user_id' => $user_id, 'product_id' => $productInf->id])->get('product_comments')->row();
                                ?>
                                <div class="fp-order-alt-item">
                                    <div class="area area-product text-start">
                                        <img src="<?= base_url('assets/img/product/') . $productInf->img; ?>" alt="" class="img-product">
                                        <div class="text"><a target="_blank" href="<?=base_url($productInf->slug); ?>"><?= $productInf->name ?></a></div>
                                    </div>
                                    <div class="area">
                                        <div class="title-mini">Fiyat</div>
                                        <div class="text"><?= $product->price ?> TL</div>
                                    </div>
                                    <div class="area">
                                        <div class="title-mini">Durum</div>
                                        <?php if($product->isActive == 0): ?>
                                            <div class="text"><span class="badge bg-success">Teslim Edildi</span></div>
                                        <?php elseif($product->isActive == 1): ?>
                                            <div class="text"><span class="badge bg-warning">Teslimat Bekleniyor</span></div>
                                        <?php else: ?>
                                            <div class="text"><span class="badge bg-danger">İptal Edildi</span></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="actions">
                                        <?php if ($product->isActive == 1 || $productInf->isStock == 0): ?>
                                            <button class="btn btn-secondary btn-sm"><i class="ri-information-line icon icon-left"></i> Görüntüle</button>
                                            <?php if (!empty($product_comment) || $product->isActive != 0): ?>
                                                <button class="btn btn-secondary mb-0 btn-sm"><i class="ri-chat-new-line icon icon-left"></i> Yorum Yap</button>
                                            <?php else: ?>
                                                <a href="#modalProductComment" class="btn btn-success mb-0 btn-sm" data-bs-toggle="modal" data-bs-target="#modalProductComment" data-product-id="<?= $product->id ?>"><i class="ri-chat-new-line icon icon-left"></i> Yorum Yap</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <a href="#modalProductInfo" class="btn btn-primary btn-sm view-order" data-orderid="<?= $product->id ?>" data-bs-toggle="modal" data-bs-target="#modalProductInfo"><i class="ri-information-line icon icon-left"></i> Görüntüle</a>
                                            <?php if (!empty($product_comment)): ?>
                                                <button class="btn btn-secondary mb-0 btn-sm"><i class="ri-chat-new-line icon icon-left"></i> Yorum Yap</button>
                                            <?php else: ?>
                                                <a href="#modalProductComment" class="btn btn-success mb-0 btn-sm" data-bs-toggle="modal" data-bs-target="#modalProductComment" data-product-id="<?= $product->id ?>"><i class="ri-chat-new-line icon icon-left"></i> Yorum Yap</a>
                                            <?php endif; ?>
                                            <?php 
                                            $existing_objection = $this->db->where('invoice_id', $product->id)->get('product_objections')->row();
                                            if ($product->seller_id > 2 && strtotime($product->last_refund) > time() && !$existing_objection): ?>
                                                <a href="#modalProductObjection" class="btn btn-warning mb-0 btn-sm" data-bs-toggle="modal" data-bs-target="#modalProductObjection" data-invoice-id="<?= $product->id ?>">
                                                    <i class="ri-error-warning-line icon icon-left"></i> İtiraz Et
                                                </a>
                                            <?php elseif($existing_objection): ?>
                                                <button class="btn btn-secondary mb-0 btn-sm" disabled>
                                                    <i class="ri-error-warning-line icon icon-left"></i> İtiraz Edildi
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php
                endif;
            endforeach;
            ?>
        </div>
    </div>
</div>
</section>
<div class="modal fade" id="modalProductInfo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fs-5" id="staticBackdropLabel">Ürün Bilgisi</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                Ürün Bilgisi Alanı
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalProductComment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fs-5" id="staticBackdropLabel">Yorum Yap</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="commentForm" action="<?= base_url('client/dashboard/addStars/') ?>" method="POST">
                    <input type="hidden" name="product_id" id="product_id" value="">
                    <div class="mb-3">
                        <label>Puan</label>
                        <select name="stars" id="stars" class="form-select">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Yorum</label>
                        <textarea name="comment" rows="3" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Gönder</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalProductObjection" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title fs-5">Ürün İtirazı</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="objectionForm" action="<?= base_url('client/dashboard/productObjection/') ?>" method="POST">
                    <input type="hidden" name="invoice_id" id="invoice_id" value="">
                    <div class="mb-3">
                        <label>İtiraz Nedeni</label>
                        <textarea name="objection_text" rows="3" class="form-control" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">İtiraz Gönder</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('#modalProductComment').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var productId = button.data('product-id');
        $('#product_id').val(productId);
        $('#commentForm').attr('action', '<?= base_url('client/dashboard/addStars/') ?>' + productId);
    });
</script>
<script>
    $(document).ready(function () {
        $('.view-order').click(function () {
            var orderId = $(this).data('orderid');
            $.ajax({
                url: '<?=base_url('client/dashboard/getOrderDetails/');?>' + orderId,
                type: 'GET',
                success: function (data) {
                    console.log(data);
                    $('#orderDetailsContent').html(data);
                    $('#modalProductInfo').modal('show');
                }
            });
        });
    });
</script>
<script>
    $('#modalProductObjection').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var invoiceId = button.data('invoice-id');
        $('#invoice_id').val(invoiceId);
        $('#objectionForm').attr('action', '<?= base_url('client/dashboard/productObjection/') ?>' + invoiceId);
    });
</script>