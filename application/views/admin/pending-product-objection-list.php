<div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">İtirazlar</h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin">Ana Sayfa</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Bekleyen İtirazlar</li>
                            </ol>
                        </nav>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered border dataTable table-product">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Ürün</th>
                                                <th>Stok</th>
                                                <th>Satıcı</th>
                                                <th>İtiraz Eden</th>
                                                <th>İtiraz</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pendingObjections as $p) { ?>
                                                <tr>
                                                <td><img src="<?= base_url(); ?>assets/img/product/<?= $p->product_img ?>" alt=""> <?= $p->product_name ?> <?= ($p->product_active == 2) ? "<small class='text-danger'>(Deaktif)</small>" : NULL ?></td>
                                                <td class="text-primary">
                                                    <?= $p->stock_value ?>
                                                </td>
                                                <td><?php if ($p->seller_id == 0) {
                                                    echo "Yönetici";
                                                }else if($p->seller_id != 0) {
                                                    $seller = $this->db->where('id', $p->seller_id)->get('user')->row();
                                                    if ($seller->isAdmin == 1) {
                                                        echo "Yönetici";
                                                    }else{
                                                        echo "({$seller->shop_name})" . $seller->name . " " . $seller->surname;
                                                    }
                                                } ?></td>
                                                <td><?php 
                                                    $buyer = $this->db->where('id', $p->seller_id)->get('user')->row();
                                                    echo ($buyer->type == 2 ? "({$seller->shop_name})" : "") . $buyer->name . " " . $buyer->surname;
                                                ?></td>
                                                <td><?= $p->objection ?></td>
                                                <td class="text-center"> 
                                                    <a href="<?= base_url('admin/product/changeProductObjection/1/') . $p->id ?>" class="text-success">Onayla</a>
                                                    <a href="<?= base_url('admin/product/changeProductObjection/0/') . $p->id ?>" class="text-danger">Reddet</a>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </main>

                

               
