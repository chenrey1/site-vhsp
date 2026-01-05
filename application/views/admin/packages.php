<?php /* CACHE BUST: <?= time() ?> */ ?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Paketler</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/product">Mağaza</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Paketler</li>
                </ol>
            </nav>

            <div class="page-btn">
                <div class="btns">
                    <a href="<?= base_url(); ?>admin/product/addPackage" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Paket Ekle</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered border dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Paket Adı</th>
                                    <th>Fiyat</th>
                                    <th>İndirim %</th>
                                    <th>Ürün Sayısı</th>
                                    <th>Durum</th>
                                    <th>Sıra</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($packages as $p) { 
                                    // Paket içindeki ürünleri getir
                                    $package_products = $this->db->select('p.name, p.img')
                                        ->from('package_products pp')
                                        ->join('product p', 'p.id = pp.product_id', 'left')
                                        ->where('pp.package_id', $p->id)
                                        ->order_by('pp.sort_order', 'ASC')
                                        ->get()
                                        ->result();
                                ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($package_products)) { ?>
                                                    <div class="d-flex" style="gap: 5px; margin-right: 10px;">
                                                        <?php 
                                                        $display_count = min(count($package_products), 3);
                                                        for ($i = 0; $i < $display_count; $i++) { 
                                                        ?>
                                                            <img src="<?= base_url('assets/img/product/') . $package_products[$i]->img ?>" 
                                                                 alt="<?= $package_products[$i]->name ?>" 
                                                                 style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                        <?php } ?>
                                                        <?php if (count($package_products) > 3) { ?>
                                                            <div style="width: 40px; height: 40px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #666;">
                                                                +<?= count($package_products) - 3 ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                                <strong><?= $p->name ?></strong>
                                                <?php if ($p->isActive == 0) { ?>
                                                    <small class="text-danger ml-2">(Pasif)</small>
                                                <?php } ?>
                                            </div>
                                        </td>
                                        <td><?= number_format($p->price, 2, ',', '.') ?>₺</td>
                                        <td>%<?= number_format($p->discount_percent, 2, ',', '.') ?></td>
                                        <td><?= $p->total_products ?> ürün</td>
                                        <td>
                                            <?php if ($p->isActive == 1) { ?>
                                                <span class="badge badge-success">Aktif</span>
                                            <?php } else { ?>
                                                <span class="badge badge-secondary">Pasif</span>
                                            <?php } ?>
                                        </td>
                                        <td><?= $p->sort_order ?></td>
                                        <td>
                                            <a href="<?= base_url('admin/product/editPackage/') . $p->id ?>" class="btn btn-link btn-sm text-primary">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('admin/product/deletePackage/') . $p->id ?>" 
                                               class="btn btn-link btn-sm text-danger" 
                                               onclick="return confirm('Bu paketi silmek istediğinize emin misiniz?');">
                                                <i class="far fa-trash-alt"></i>
                                            </a>
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
</div>
