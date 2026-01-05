<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Yetki Listesi</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Yetki Listesi</li>
                </ol>
            </nav>

            <div class="blog-add-btn d-flex justify-content-between align-items-center mb-3">
                <div class="bad-1"></div>
                <div class="bad-2">
                    <a href="<?= base_url('admin/editPermission') ?>" class="btn btn-primary"> Yeni Yetki Oluştur</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered border dataTable table-product">
                            <thead class="thead-light">
                            <tr>
                                <th>Yetki Adı</th>
                                <th>Bu yetkiye sahip kişi sayısı</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($roles as $role) { ?>
                                <tr>
                                    <td><?= $role->role ?></td>
                                    <td><?= $this->db->where('role_id', $role->id)->count_all_results('user');  ?></td>
                                    <td>
                                    <?php if ($role->id != 1): ?>
                                        <a href="<?= base_url('admin/editPermission').'?auth='.$role->id ?>"><i class="fa fa-edit"></i></a>
                                        <a href="<?= base_url('admin/product/deletePerm/') . $role->id ?>"><i class="far fa-trash-alt text-danger"></i></a>
                                        <?php endif ?>
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
