 <div id="layoutSidenav_content">


                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">Havale Bildirimleri</h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>">Ana Sayfa</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Havale Bildirimleri</li>
                            </ol>
                        </nav>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover border dataTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Gönderen</th>
                                                <th>Banka</th>
                                                <th>Tarih</th>
                                                <th>Ücret</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($transfers as $t) { ?>
                                                <tr>
                                                <td><?php $user = $this->db->where('id', $t->user_id)->get('user')->row(); echo $user->name; ?></td>
                                                <td><?php $banka = $this->db->where('id', $t->banks_id)->get('banks')->row(); echo $banka->bank_name; ?></td>
                                                <td><?= $t->date ?></td>
                                                <td><?= $t->amount ?>₺</td>
                                                <td>
                                                        <a href="<?= base_url('admin/product/cancelTransfer/') . $t->id ?>" class="btn btn-outline-danger btn-sm">Reddet</button>
                                                     <a href="<?= base_url('admin/product/confirmTransfer/') . $t->id . "/" . $t->user_id . "/" . $t->amount ?>" class="btn btn-outline-success btn-sm ml-2">Onayla</button>
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
