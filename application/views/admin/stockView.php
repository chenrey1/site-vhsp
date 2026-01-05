        <div class="card-header">Stok</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table dataTable border table-stock">
                    <thead class="thead-light">
                        <tr>
                            <th>Stok Bilgisi</th>
                            <th>Ürün</th>
                            <th>Durum</th>
                            <th></th>
                            <th>Kontrol Et</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($stocks as $stock) { ?>
                            <tr class="align-items-center">
                            <td <?php if ($stock->checked == 0) {
                                echo "style='border-left: solid 4px #dc3545;'";
                            }else if($stock->checked == 1) {
                                echo "style='border-left: solid 4px #28a745;'";
                            }else if($stock->checked == 2) {
                                echo "style='border-left: solid 4px #17a2b8;'";
                            } ?>><?= $stock->product ?></td>
                            <td><?php 
                            $product_name = $this->db->where('id', $stock->product_id)->get('product')->row(); 
                            
                            if ($product_name) {
                                echo $product_name->name;
                            }else {
                                echo "Ürün Bulunamadı";
                            }

                            ?></td>
                            <td>
                                <?php if ($stock->isActive == 0) { ?>
                                <span class="text-info">Teslim Edildi</span>
                                <?php }else{ ?>
                                <span class="text-success">Stokta Bekletiliyor</span>
                                <?php } ?>
                            </td>
                            <td><a href="<?= base_url('admin/product/delete/stock/stock/') . $stock->id ?>" class="text-danger"><i class="far fa-trash-alt"></i></a></td>
                            <td>
                                <a class="text-info">
                                    <img height="50" onclick="controlAccount(<?= $stock->id ?>)" src="<?= base_url('assets/img/icons/mc.png'); ?>">
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
