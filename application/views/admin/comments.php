
 <div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Yorumlar</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/product">Mağaza</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Müşteri Yorumları</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover border dataTable table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th width="40%">Yorum</th>
                                    <th>Gönderen</th>
                                    <th>Ürün</th>
                                    <th>Tarih</th>
                                    <th width="1%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($comments as $c) { ?>
                                    <tr>
                                    <td><?= $c->comment ?></td>
                                    <td><?php $user = $this->db->where('id', $c->user_id)->get('user')->row(); echo $user->name ?></td>
                                    <td><?php $product = $this->db->where('id', $c->product_id)->get('product')->row(); ?> <?= ($product) ? $product->name : "Ürün kaldırılmış veya bulunamadı" ?></td>
                                    <td><?= $c->date ?></td>
                                    <td>
                                        <?php if ($c->isActive == 0) { ?>
                                        <a class="btn btn-link btn-sm text-success" href="<?= base_url('admin/product/confirmComment/'. $c->id) ?>"><i class="fas fa-check"></i></a>
                                    <?php }else ?>
                                        <a class="btn btn-link btn-sm text-danger" href="<?= base_url('admin/product/deleteComment/'. $c->id) ?>"><i class="far fa-trash-alt"></i></a>
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
