<?php
$user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
if ($properties->shop_active != 1) {
    $user->type = 1;
}
$allTimeShop = $this->db->where('seller_id', $user->id)->get('invoice')->result();
$allTimeSell = 0;
foreach ($allTimeShop as $ats) {
    $allTimeSell = $allTimeSell + $ats->price;
}
?>
            <?php if ($user->type != 2) {?>
                <div class="col-lg-9">
                    <div class="fp-card fp-card-client">
                        <div class="fp-cc-head">
                            <h1 class="title">Pazaryeri</h1>
                        </div>
                        <div class="fp-cc-body">
                            <h5 class="fw-medium mb-3">Pazaryeri Başvurusu</h5>
                            <form action="<?= base_url('client/appShop'); ?>" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label>Mağaza Adı</label>
                                    <input type="text" class="form-control" name="shopName" required="">
                                </div>
                                <div class="mb-3">
                                    <label>Logo</label>
                                    <input class="form-control" type="file" id="formFile" name="img">
                                </div>
                                <button type="submit" class="btn btn-primary w-100"><i class="ri-add-line icon icon-left"></i> Başvuru Yap</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="col-lg-9">
                    <div class="fp-card fp-card-client mb-16">
                        <div class="fp-cc-head border-bottom-0 d-flex align-items-center justify-content-between">
                            <h1 class="title">Pazaryeri</h1>
                            <a target="_blank" href="<?=base_url('magaza/') . sefLink($user->shop_name);?>" class="btn btn-opacity-primary btn-sm">Mağazama Git <i class="ri-arrow-right-up-line icon icon-right" style="font-size: 19px"></i></a>
                        </div>
                    </div>
                    <div class="row row-16">
                        <div class="col-md-6 col-lg-4">
                            <div class="fp-info-item fp-card">
                                <div class="content">
                                    <div class="key">Toplam Kazanç</div>
                                    <div class="value"><?= $allTimeSell ?> TL</div>
                                </div>
                                <div class="icon">
                                    <i class="ri-coin-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="fp-info-item fp-card">
                                <div class="content">
                                    <div class="key">Toplam Satış</div>
                                    <div class="value"><?= $this->db->where('seller_id', $user->id)->count_all_results('invoice'); ?></div>
                                </div>
                                <div class="icon">
                                    <i class="ri-line-chart-line"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="fp-info-item fp-card">
                                <div class="content">
                                    <div class="key">Satıştaki Ürün</div>
                                    <div class="value"><?= $this->db->where('seller_id', $user->id)->where('isActive', 1)->count_all_results('product'); ?></div>
                                </div>
                                <div class="icon">
                                    <i class="ri-box-3-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="fp-card fp-card-client">
                        <div class="fp-card-tabs">
                            <ul class="fp-tabs-nav-system list list-unstyled list-inline mb-0">
                                <li><a href="#" class="link active" id="urunler">Ürünler</a></li>
                                <li><a href="#" class="link" id="satis">Satış</a></li>
                                <li><a href="#" class="link" id="stok">Stok</a></li>
                                <li><a href="#" class="link" id="mesajlar">Mesajlar</a></li>
                            </ul>
                        </div>
                        <div class="fp-cc-body">
                            <div class="fp-tabs">
                                <div class="fp-tabs-content active" id="urunler-content">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-medium mb-0">Ürünlerim</h5>
                                        <a href="#modal-seller-add" data-bs-toggle="modal" data-bs-target="#modal-seller-add" class="btn btn-primary btn-sm"><i class="ri-add-line icon icon-left"></i> Ürün Ekle</a>
                                    </div>
                                    <div class="table-responsive fp-table-border">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th scope="col">Ürün</th>
                                                <th scope="col">Fiyat</th>
                                                <th scope="col">Satış Sayısı</th>
                                                <th scope="col">İncelenme Sayısı</th>
                                                <th scope="col">Durum</th>
                                                <th scope="col"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($myProducts as $mp) { ?>
                                                <?php $sell = $this->db->where('product_id', $mp->id)->count_all_results('invoice'); ?>
                                                <?php $see = $this->db->where('product_id', $mp->id)->count_all_results('category_review'); ?>
                                                <tr>
                                                    <td><?= $mp->name ?></td>
                                                    <td><?= $mp->price ?>₺</td>
                                                    <td><?= $sell ?></td>
                                                    <td><?= $see ?></td>
                                                    <td><?php if ($mp->isActive == 1) {
                                                            echo "Listeleniyor";
                                                        }else if ($mp->isActive == 2) {
                                                            echo "Uygun Bulunmadı";
                                                        }else if ($mp->isActive == 3) {
                                                            echo '<div class="spinner-border text-primary" role="status"></div>';
                                                        } ?></td>
                                                    <td>
                                                        <a href="javascript:void(0)" onclick="loadProductEditModal(this)" class="btn btn-opacity-success btn-sm"
                                                           product_id="<?= $mp->id ?>"
                                                           product_name="<?= $mp->name ?>"
                                                           product_price="<?= $mp->price ?>"
                                                           product_desc="<?= htmlspecialchars($mp->desc) ?>"
                                                           category_id="<?= $mp->category_id ?>"
                                                           product_img="<?= base_url('assets/img/product/').$mp->img ?>">Düzenle</a>
                                                        <a href="<?= base_url('client/deleteProduct/') . $mp->id ?>" class="btn btn-opacity-danger btn-sm">Sil</a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="fp-tabs-content" id="satis-content">
                                    <h5 class="fw-medium mb-3">Son Satışlarım</h5>
                                    <div class="table-responsive fp-table-border">
                                        <table class="table mb-0">
                                            <thead>
                                            <tr>
                                                <th scope="col">Ürün</th>
                                                <th scope="col">Fiyat</th>
                                                <th scope="col">Alıcı</th>
                                                <th scope="col">Verilen Stok</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($sellProduct as $sp) { ?>
                                                <tr>
                                                    <?php $product = $this->db->where('id', $sp->product_id)->get('product')->row(); ?>
                                                    <?php $shop = $this->db->where('id', $sp->shop_id)->get('shop')->row(); ?>
                                                    <?php $buyer = $this->db->where('id', $shop->user_id)->get('user')->row(); ?>
                                                    <td><?= $product->name ?></td>
                                                    <td><?= $buyer->name . " " . $buyer->surname ?></td>
                                                    <td><?= $sp->product ?></td>
                                                    <td><?= $sp->product ?></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="fp-tabs-content" id="stok-content">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-medium mb-0">Stoklarım</h5>
                                        <a href="#modal-seller-stock" data-bs-toggle="modal" data-bs-target="#modal-seller-stock" class="btn btn-primary btn-sm"><i class="ri-add-line icon icon-left"></i> Stok Ekle</a>
                                    </div>
                                    <div class="table-responsive fp-table-border">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th scope="col">Ürün</th>
                                                <th scope="col">Stok Bilgisi</th>
                                                <th scope="col">Durum</th>
                                                <th scope="col"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($stocks as $stock) { ?>
                                                <tr>
                                                    <td><?= $stock->name ?></td>
                                                    <td><?= $stock->product ?></td>
                                                    <td>Stokta Bekletiliyor</td>
                                                    <td><a href="<?= base_url('client/deleteStock/') . $stock->id ?>" class="btn btn-opacity-danger btn-sm">Sil</a></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="fp-tabs-content" id="mesajlar-content">
                                    <h5 class="fw-medium mb-3">Mesajlar</h5>
                                    <div class="table-responsive fp-table-border">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th scope="col">Gönderen</th>
                                                <th scope="col">Konu</th>
                                                <th scope="col">Tarih</th>
                                                <th scope="col">Durum</th>
                                                <th scope="col"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($sellerTickets as $ticket) { ?>
                                                <tr>
                                                    <?php $ouser_data = getTicketOtherUser($ticket, $this->session->userdata('info')['id']); ?>
                                                    <td><?= $ouser_data[1] ?></td>
                                                    <td><?= $ticket->title ?></td>
                                                    <td><?= $ticket->date ?></td>
                                                    <td><span class="badge bg-primary"><?php
                                                            if ($ticket->status == 0) {
                                                                echo "Kapandı";
                                                            }else if($ticket->status == 1){
                                                                echo "Cevap Verdiniz";
                                                            }else{
                                                                echo "Cevabınız Bekleniyor";
                                                            } ?></span></td>
                                                    <td><a href="<?= base_url('client/showTicket/') . $ticket->id ?>" class="btn btn-opacity-primary">Cevapla</a></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modal-seller-add" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Pazaryerine Ürün Ekle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= base_url('client/addProduct') ?>" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label for="">Ürün Adı</label>
                                        <input type="text" class="form-control" name="product_name" required="">
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Fiyat</label>
                                        <input type="number" class="form-control" name="product_price" required="">
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Açıklama</label>
                                        <textarea rows="3" class="form-control" name="product_desc"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Kategori</label>
                                        <select class="form-select" name="category_id">
                                            <option selected disabled value="">Ürün Kategorisini Seçiniz</option>
                                            <?php foreach ($categories as $c) { ?>
                                                <option value="<?= $c->id ?>"><?= $c->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Ürün Görseli</label>
                                        <input class="form-control" type="file" id="formFile" name="img">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100"><i class="ri-add-line icon icon-left"></i> Ekle</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modalPazarEdit" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Ürün Düzenle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= base_url('client/editProduct') ?>" method="POST" enctype="multipart/form-data">
                                    <div class="d-flex justify-content-center mb-3">
                                        <img src="" id="product_def_img" alt="Ürün Resmi" style="width: 200px;">
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Ürün Adı</label>
                                        <input type="text" class="form-control" name="product_name" required="">
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Fiyat</label>
                                        <input type="number" class="form-control" name="product_price" required="">
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Açıklama</label>
                                        <textarea rows="3" class="form-control" name="product_desc" id="editor"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Kategori</label>
                                        <select class="form-select" name="category_id">
                                            <option selected disabled value="">Ürün Kategorisini Seçiniz</option>
                                            <?php foreach ($categories as $c) { ?>
                                                <option value="<?= $c->id ?>"><?= $c->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Ürün Görseli</label>
                                        <input class="form-control" type="file" id="formFile" name="img">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 d-block">Gönder</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="modal-seller-stock" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Ürüne Stok Ekle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= base_url('client/addStock') ?>" method="POST">
                                    <div class="mb-3">
                                        <label for="inputProduct">Ürün</label>
                                        <select class="form-select" id="inputProduct" name="product_id" required>
                                            <option selected disabled value="">Stok Eklenecek Ürünü Seçiniz</option>
                                            <?php foreach ($myProducts as $mp) { ?>
                                                <option value="<?= $mp->id ?>"><?= $mp->name ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Ürün Kodu <small class="text-muted">(Örn: Steam Key: XXXX-XXXX-XXXX-XXXX)</small></label>
                                        <textarea rows="3" class="form-control" name="product_stock"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 d-block">Ekle</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>