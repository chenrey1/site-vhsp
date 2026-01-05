<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Yetki Listesi</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Abone Listesi</li>
                </ol>
            </nav>

            <div class="blog-add-btn d-flex justify-content-between align-items-center mb-3">
                <div class="bad-1"></div>
                <div class="bad-2">
                    <a href="<?= base_url('admin/subscription/addSubscription') ?>" class="btn btn-primary"> Yeni Abonelik Oluştur</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered border dataTable table-product">
                            <thead class="thead-light">
                            <tr>
                                <th>Abonelik Adı</th>
                                <th>Bu aboneliğe sahip kişi sayısı</th>
                                <th>Abonelik Süresi (Gün)</th>
                                <th>Abonelik Ücreti</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($subscriptions as $subscription) { ?>
                                <?php
                                //user_subscription tablosundaki end_date tarihi dolmayan abonelikleri çek
                                $this->db->where('subscription_id', $subscription->id);
                                $this->db->where('end_date >', date('Y-m-d H:i:s'));
                                $this->db->from('user_subscriptions');
                                $active_subscription = $this->db->count_all_results();
                                ?>
                                <tr>
                                    <td><?= $subscription->name ?></td>
                                    <td><?= $active_subscription ?></td>
                                    <td><?= $subscription->duration ?></td>
                                    <td><?= $subscription->price ?>₺</td>
                                    <td>
                                        <a href="<?= base_url('admin/subscription/edit_subscription/' . $subscription->id); ?>"><i class="fa fa-edit"></i></a>
                                        <a href="<?= base_url('admin/subscription/delete_subscription/' . $subscription->id); ?>"><i class="far fa-trash-alt text-danger"></i></a>
                                        <a href="<?= base_url('admin/subscription/subList/' . $subscription->id); ?>"><i class="fa fa-eye"></i></a>
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
