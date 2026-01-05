<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5>Fatura Listesi</h5>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Fatura Listesi</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-header card-header-nav">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#productInvoices" role="tab" aria-selected="true">
                                <i class="fas fa-list-alt"></i>
                                Ürün Alım Faturaları
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#depositInvoices" role="tab" aria-selected="false">
                                <i class="fas fa-wallet"></i>
                                Bakiye Komisyon Faturaları
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">

                        <div class="tab-pane fade show active" id="productInvoices" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover border dataTable">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width:5%">Fatura No</th>
                                        <th>Alıcı</th>
                                        <th>Ödenen Tutar</th>
                                        <th>Ödeme Komisyonu</th>
                                        <th width="10%">Faturulandırılacak Tutar</th>
                                        <th>Tarih</th>
                                        <th>Durum</th>
                                        <th width="10%">Fatura Sağlayıcı</th>
                                        <th>İşlemler</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($invoices as $invoice) { ?>
                                        <?php $shop = $this->db->where('id', $invoice->shop_id)->get('shop')->row(); ?>
                                        <?php $user  = $this->db->where('id', $shop->user_id)->get('user')->row(); ?>
                                        <tr>
                                            <td><?= $invoice->id ?></td>
                                            <td><?= $user->name . " " . $user->surname ?></td>
                                            <td><?= $invoice->price ?>₺</td>
                                            <td><?= $invoice->payment_commission ?>₺</td>
                                            <td><?= $invoice->price + $invoice->payment_commission ?>₺</td>
                                            <td><?= $invoice->date ?></td>
                                            <td><?php
                                                if ($invoice->invoice_status == "invoiced") {
                                                    echo "<span class='text-success'>Fatura Oluşturuldu</span>";
                                                }else if($invoice->invoice_status == "in_system" && $invoice->invoice_provider != "disabled"){
                                                    echo "<span class='text-danger'>Ürün Teslimatı Bekleniyor</span>";
                                                }else{
                                                    echo "<span class='text-info'>Fatura Bilgisi Bulunamadı</span>";
                                                }
                                                ?>
                                            </td>
                                            <td><?=$invoice->invoice_provider?></td>
                                            <td><a href="<?=base_url('admin/finance/createInvoicebyINV/' . $invoice->id)?>"><button class="btn btn-<?=( $invoice->invoice_status == "in_system" ? "primary" : "secondary"); ?>">Fatura Oluştur</button></a></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="depositInvoices" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover border dataTable">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width:5%">Fatura No</th>
                                        <th>Alıcı</th>
                                        <th>Yatırılan Bakiye</th>
                                        <th>Ödeme Komisyonu</th>
                                        <th>Faturalandırılacak Tutar</th>
                                        <th>Tarih</th>
                                        <th>Durum</th>
                                        <th width="10%">Fatura Sağlayıcı</th>
                                        <th>İşlemler</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($balanceShops as $balanceShop) { ?>
                                        <?php $shop = $this->db->where('id', $balanceShop->id)->get('shop')->row(); ?>
                                        <?php $user  = $this->db->where('id', $shop->user_id)->get('user')->row(); ?>
                                        <tr>
                                            <td><?= $balanceShop->id ?></td>
                                            <td><?= $user->name . " " . $user->surname ?></td>
                                            <td><?= $balanceShop->price ?>₺</td>
                                            <td><?= $balanceShop->payment_commission ?>₺</td>
                                            <td><?= $balanceShop->payment_commission ?>₺</td>
                                            <td><?= $balanceShop->date ?></td>
                                            <td><?php
                                                if ($balanceShop->invoice_status == "invoiced") {
                                                    echo "<span class='text-success'>Fatura Oluşturuldu</span>";
                                                }else{
                                                    echo "<span class='text-danger'>Fatura Bulunamadı</span>";
                                                }
                                                ?>
                                            </td>
                                            <td><?=$balanceShop->invoice_provider?></td>
                                            <td><a href="<?=base_url('admin/finance/createInvoicebyShop/' . $shop->id)?>"><button class="btn btn-<?=( $balanceShop->invoice_status == "in_system" ? "primary" : "secondary"); ?>">Fatura Oluştur</button></a></td>
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
    </main>