<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Fatura</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/productHistory'); ?>">Satış Geçmişi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Fatura</li>
                </ol>
            </nav>

            <?php $user = $this->db->where('id', $shop->user_id)->get('user')->row(); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-userprofile">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <h2>FATURA NO: <?= $shop->id ?></h2>
                                <h2><?= $shop->date ?></h2>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <h4 class="title">Alıcı Bilgileri:</h4>
                                        <ul class="list-unstyled">
                                            <li><?= $user->name . " " . $user->surname ?></li>
                                            <li><?= $user->email ?></li>
                                            <li><?= $user->phone ?></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box text-right">
                                        <h4 class="title">Satıcı Bilgileri</h4>
                                        <ul class="list-unstyled">
                                            <?php if ($shop->seller_id == 0 || $user->isAdmin == 1) {
                                                $properties = $this->db->where('id', 1)->get('properties')->row(); ?>
                                            <li><?= $properties->name ?></li>
                                            <li><?= $properties->contact ?></li>
                                            <li><?= $properties->address ?></li>
                                        <?php }else{ ?>
                                            <li></li>
                                        <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">Ürünler</div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                        <th>Ürün</th>
                                                        <th>Fiyat</th>
                                                    </thead>
                                                    <tbody>
                                                        <?php $subtotal = 0; ?>
                                                        <?php if ($shop->type == 'deposit') {
                                                            $subtotal = $shop->price;
                                                        ?>
                                                        <tr>
                                                            <td>Bakiye Yüklemesi</td>
                                                            <td><?= $shop->price ?>₺</td>
                                                        </tr>
                                                    <?php }else{
                                                        $products = json_decode($shop->product, true);
                                                        foreach ($products as $product) { ?>
                                                        <?php $productDetail = $this->db->where('id', $product['product_id'])->get('product')->row();  ?>
                                                         <tr>
                                                            <td><?= $productDetail->name ?> (<?= $product['qty'] ?>)</td>
                                                            <td><?= $product['price'] * $product['qty'] ?>₺</td>
                                                            <?php $subtotal = $subtotal + $product['price'] * $product['qty']; ?>
                                                        </tr>   
                                                    <?php }} ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <th>Toplam</th>
                                                        <th><?= $shop->price + $shop->payment_commission?>₺</th>
                                                    </tfoot>
                                                    <tfoot>
                                                        <th>Ara Toplam</th>
                                                        <th><?= $subtotal ?>₺</th>
                                                    </tfoot>
                                                    <tfoot>
                                                        <th>Ödeme Komisyonu</th>
                                                        <th><?= $shop->payment_commission ?>₺</th>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <small>Bu faturanın hiçbir geçerliliği yoktur. Sadece satış takibi içindir.  </small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

