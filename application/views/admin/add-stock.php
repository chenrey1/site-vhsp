            <div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">Stok Ekle</h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/product">Mağaza</a></li>
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/product">Ürünler</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Stok Ekle</li>
                            </ol>
                        </nav>

                        <div class="card">
                            <div class="card-body">
                                <form action="<?= base_url(); ?>admin/product/addStock" method="POST">
                                    <div class="form-group row">
                                        <label for="inputSP" class="col-sm-2 col-form-label">Eklenecek Ürün:</label>
                                        <div class="col-sm-10">
                                            <select class="custom-select" id="inputSP" name="product_id" required>
                                                <option selected disabled>Stok Eklenecek Ürünü Seçiniz</option>
                                                <?php foreach ($products as $p) { 
                                                    if ($p->isStock == 1) {
                                                ?>
                                                <option value="<?= $p->id ?>"><?= $p->name ?></option>
                                                <?php } } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputSInfo" class="col-sm-2 col-form-label">Ürün Bilgisi:</label>
                                        <div class="col-sm-10">
                                            <textarea name="product" id="inputSInfo" rows="5" class="form-control" placeholder="Müşteri ürünü aldığında gidecek bilgi (Örn. account@mail.com;pass1234). Çoklu eklemek için satır atlayın." required></textarea>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary float-right"><i class="fa fa-plus"></i> Ekle</button>
                                </form>
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        <ul class="list-group list-group-horizontal mb-4">
                            <li class="list-group-item text-info">Kontrol Bekliyor</li>
                            <li class="list-group-item text-success">Hesap Sorunsuz</li>
                            <li class="list-group-item text-danger">Giriş Yapılamadı</li>
                        </ul> 

                        <div class="card" id="stockCard">
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
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </main>