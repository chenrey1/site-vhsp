<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5>Bildirim Yönetimi</h5>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bildirim Yönetimi</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-end align-items-center mb-3">
                <a href="<?= base_url('admin/notification/newNotification') ?>" class="btn btn-success"><i class="fas fa-plus"></i> Yeni Bildirim Oluştur</a>
            </div>


            <div class="card">
                <div class="card-header card-header-nav">
                    <ul class="nav nav-pills" id="pills-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="pill" href="#productInvoices" role="tab" aria-selected="true">
                                <i class="fas fa-bell"></i>
                                Aktif Bildirimler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#depositInvoices" role="tab" aria-selected="false">
                                <i class="fas fa-bell-slash"></i>
                                Sonlanan Bildirimler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="pill" href="#systemInvoices" role="tab" aria-selected="false">
                                <i class="fas fa-cog"></i>
                                Sistem Bildirimleri
                            </a>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-gray" data-toggle="pill" role="tab" aria-selected="false" disabled="">
                                <i class="fas fa-dollar-sign"></i>
                                Satın Alınan Bildirimler (Yakında)
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="pills-tabContent">

                        <div class="tab-pane fade show active" id="productInvoices" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover border dataTable">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width:5%">Bildirim Numarası</th>
                                        <th>Bildirim Adı</th>
                                        <th>Başlangıç Tarihi</th>
                                        <th>Sonlanma Tarihi</th>
                                        <th>Görüntülenme Sayısı</th>
                                        <th>Kalan Süre</th>
                                        <th>İşlemler</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($notifications as $notification) { ?>
                                        <tr>
                                            <td>#<?= $notification->id ?></td>
                                            <td><?= $notification->name ?></td>
                                            <td><?= format_date($notification->start_at) ?></td>
                                            <td><?= format_date($notification->end_up) ?></td>
                                            <td><?= $notification->views ?> / <?= $notification->maximum_views ?></td>
                                            <td><?= calculate_remaining_time($notification->start_at, $notification->end_up); ?></td>
                                            <td>
                                                <a href="<?=base_url('admin/notification/cancelNotification/' . $notification->id)?>"><button class="btn btn-danger btn-sm">Bildirimi İptal Et</button></a>
                                                <a href="<?=base_url('admin/notification/statistics/' . $notification->id)?>"><button class="btn btn-primary btn-sm">İstatikleri Görüntüle</button></a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="depositInvoices" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover border dataTable">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width:5%">Bildirim Numarası</th>
                                        <th>Bildirim Adı</th>
                                        <th>Oluşturma Tarihi</th>
                                        <th>Başlangıç Tarihi</th>
                                        <th>Sonlanma Tarihi</th>
                                        <th>Görüntülenme Sayısı</th>
                                        <th>Sonlanma Sebebi</th>
                                        <th>İşlemler</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($endedNotifications as $endedNotification) { ?>
                                        <tr>
                                            <td>#<?= $endedNotification->id ?></td>
                                            <td><?= $endedNotification->name ?></td>
                                            <td><?= format_date($endedNotification->created_at) ?></td>
                                            <td><?= format_date($endedNotification->start_at) ?></td>
                                            <td><?= format_date($endedNotification->end_up) ?></td>
                                            <td><?= $endedNotification->views ?> / <?= $endedNotification->maximum_views ?></td>
                                            <td><?= $endedNotification->cancel_reason ?></td>
                                            <td><a href="<?=base_url('admin/notification/statistics/' . $endedNotification->id)?>"><button class="btn btn-primary btn-sm">İstatikleri Görüntüle</button></a></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="systemInvoices" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover border dataTable">
                                    <thead class="thead-light">
                                    <tr>
                                        <th style="width:5%">Bildirim Numarası</th>
                                        <th>Bildirim Adı</th>
                                        <th>Bildirim İçeriği</th>
                                        <th>Gönderim Tarihi</th>
                                        <th>Görüntülenme Tarihi</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($systemNotifications as $systemNotification) { ?>
                                        <tr>
                                            <td>#<?= $systemNotification->id ?></td>
                                            <td><?= $systemNotification->title ?></td>
                                            <td><?= $systemNotification->contents ?></td>
                                            <td><?= format_date($systemNotification->created_at) ?></td>
                                            <td><?= ($systemNotification->seen_date == NULL) ? 'Henüz Görüntülenmedi' : format_date($systemNotification->seen_date); ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </main>