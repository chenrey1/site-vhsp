<div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">Ürünler</h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin">Ana Sayfa</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Onay Bekleyen Ürünler</li>
                            </ol>
                        </nav>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered border dataTable table-product">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Ürün Adı</th>
                                                <th>Fiyat</th>
                                                <th>Stok</th>
                                                <th>Kategori</th>
                                                <th>Satıcı</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($pendingProducts as $p) { ?>
                                                <tr>
                                                <td><img src="<?= base_url(); ?>assets/img/product/<?= $p->img ?>" alt=""> <?= $p->name ?> <?= ($p->isActive == 2) ? "<small class='text-danger'>(Deaktif)</small>" : NULL ?></td>
                                                <td><?= ($p->discount > 0) ? "<s class='text-danger'><small>" . $p->price . "₺</small></s><br> " . $p->discount : $p->price ?>₺</td>
                                                <td class="text-primary"><?php if ($p->isStock == 1) {
                                                    $stok = $this->db->where('isActive', 1)->where('product_id', $p->id)->count_all_results('stock'); 
                                                    echo $stok;
                                                }else{
                                                  echo "Stok Gerektirmeyen Ürün";  
                                                } ?>
                                                </td>
                                                <td><?php $category = $this->db->where('id', $p->category_id)->get('category')->row(); ?> <?= ($category) ? $category->name : "Kategori Bulunamadı" ?> </td>
                                                <td><?php if ($p->seller_id == 0) {
                                                    echo "Yönetici";
                                                }else if($p->seller_id != 0) {
                                                    $seller = $this->db->where('id', $p->seller_id)->get('user')->row();
                                                    if ($seller->isAdmin == 1) {
                                                        echo "Yönetici";
                                                    }else{
                                                        echo $seller->name . " " . $seller->surname;
                                                    }
                                                } ?></td>
                                                <td class="text-center"> 
                                                    <a href="<?= base_url('admin/product/changeUserProduct/1/') . $p->id ?>" class="text-success">Onayla</a>
                                                    <a href="<?= base_url('admin/product/changeUserProduct/2/') . $p->id ?>" class="text-danger">Reddet</a>
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

                

               
