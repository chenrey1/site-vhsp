<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Çekim Talepleri</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Çekim Talepleri</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover border dataTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Ad</th>
                                    <th>Soyad</th>
                                    <th>E-Posta</th>
                                    <th>Telefon</th>
                                    <th>Çekim Miktarı</th>
                                    <th>IBAN</th>
                                    <th>Banka</th>
                                    <th>Hesap Sahibi</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($request as $r) { ?>
                                    <tr>
                                        <td><?= $r->name ?></td>
                                        <td><?= $r->surname ?></td>
                                        <td><?= $r->email ?></td>
                                        <td><?= $r->phone ?></td>
                                        <td><?= $r->amount ?>₺</td>
                                        <td><?= $r->bank_iban ?></td>
                                        <td><?= $r->bank_name ?></td>
                                        <td><?= $r->bank_owner ?></td>
                                        <td>
                                            <a href="<?= base_url('admin/product/changeRequest/1/') . $r->id ?>" class="text-success">Onayla</a>
                                            <a href="<?= base_url('admin/product/changeRequest/0/') . $r->id ?>" class="text-danger">Reddet</a>
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

<script type="text/javascript">
    
</script>