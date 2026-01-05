<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Üye Mağazaları</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Üye Mağazaları</li>
                </ol>
            </nav>

           <!-- <div class="d-flex justify-content-between align-items-center mb-3">
                <span></span>
                <span><a href="<?= base_url('admin/sendMail') ?>" class="btn btn-success">Toplu Mail Gönder</a></span>
            </div> -->

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover border dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Kullanıcı ID</th>
                                    <th>Mağaza Resmi</th>
                                    <th>Mağaza Adı</th>
                                    <th>Komisyon</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $u) { ?>
                                    <tr>
                                        <td><a href="<?= base_url('admin/users').'?edit_user='.$u->id ?>"><?= $u->id ?></a></td>
                                        <td><img src="<?= base_url(); ?>assets/img/shop/<?= $u->shop_img ?>" alt="" style="width: 60px;height: auto;object-fit: cover;"></td>
                                        <td><?= $u->shop_name ?></td>
                                        <td><?= $u->shop_com ?></td>
                                        <td>
                                            <?php if ($u->isActive == 1) { ?>
                                                <a href="<?= base_url('admin/product/setActive/0/') . $u->id ?>" class="btn btn-outline-danger btn-sm">Yasakla</a>
                                            <?php }else{ ?>
                                                <a href="<?= base_url('admin/product/setActive/1/') . $u->id ?>" class="btn btn-outline-success btn-sm">Yasağı Kaldır</a>
                                            <?php } ?>
                                            <a href="#shopEdit<?= $u->id ?>" data-toggle="modal" class="btn btn-outline-info btn-sm"><i class="fa fa-edit"></i></a>
                                        </td>
                                    </tr>
        
                                    <div class="modal fade" id="shopEdit<?= $u->id  ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title" id="exampleModalLabel">Mağaza Düzenleme</h6>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                <form method="POST" action="<?= base_url('admin/product/edit/userShops/user/') . $u->id?> ">
                                                    <div class="form-group">
                                                        <label for="message-text" class="col-form-label">Mağaza Adı:</label>
                                                        <input type="text" class="form-control" value="<?= $u->shop_name ?>" name="shop_name" required="">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="message-text" class="col-form-label">Mağaza Komisyon:</label>
                                                        <input type="number" class="form-control" value="<?= $u->shop_com ?>" name="shop_com" min="0"  step="0.01" required="">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary">Güncelle</button>
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

<script type="text/javascript">
    $( document ).ready(function() {
        var $_GET = JSON.parse('{' + window.location.search.slice(1).split('&').map(x => { y = x.split('='); return '"' + y[0] + '":' + y[1]}).join(',')  + '}');
        if ($_GET["edit_shop"]) {
            $('#shopEdit'+$_GET["edit_shop"]).modal('show');
        }
    });
</script>