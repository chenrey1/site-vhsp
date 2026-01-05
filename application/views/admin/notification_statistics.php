<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Bildirim İstatistikleri</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/Notification/notificationList'); ?>">Bildirim Yönetimi</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?=$notification->name ?> İncelemesi</li>
                </ol>
            </nav>

            <?php
            //get notification_management
            $notificationManagement = $this->db->where('id', $notification->id)->get('notification_management')->row();
            ?>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-8">
                            <canvas id="notificationChart"></canvas>
                            <div class="row mt-3">
                                <div class="col-12 col-lg-4">
                                    <div class="sf-box bg-light">
                                        <div class="sf-box-left">
                                            <small>Bildirim Durumu</small>
                                            <h5><?php
                                                //control isActive
                                                if($notificationManagement->isActive == 'Active') {
                                                    echo 'Devam Ediyor';
                                                }else{
                                                    echo 'Sonlandırıldı';
                                                }?></h5>
                                            <small>
                                                <?php
                                                if ($notificationManagement->isActive == 'Passive') {
                                                    echo 'İptal Nedeni: ' . $notificationManagement->cancel_reason;
                                                } else {
                                                    echo 'Veri toplamaya devam ediyoruz.';

                                                }?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="sf-box bg-light">
                                        <div class="sf-box-left">
                                            <small>Kalan Süre</small>
                                            <h5><?php
                                                $exp_date = new DateTime($notification->end_up);
                                                $now = new DateTime();
                                                $interval = $now->diff($exp_date);
                                                //if remaining hours is 0 or isActive == 'passive' then show 'Süre Doldu'
                                                if($interval->format('%h') == 0 || $notificationManagement->isActive == 'Passive'){
                                                    echo 'Süre Doldu veya İptal Edildi';
                                                }else{
                                                    echo $interval->format('%a gün %h saat');
                                                }
                                                ?></h5>
                                            <small>Bitiş Tarihi: <?= format_date($notificationManagement->end_up) ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-lg-4">
                                    <div class="sf-box bg-light">
                                        <div class="sf-box-left">
                                            <small>Hedef Kitleye Ulaşım Oranı <span class="font-weight-bold">(%<?= ($notificationManagement->views / $notificationManagement->maximum_views) * 100; ?>)</span></small>
                                            <h5><?=$notificationManagement->maximum_views?> Gönderim <br> <?= $notificationManagement->views ?> Görüntülenme</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4 border-left">
                            <div class="form-group">
                                <label>Bildirim Takip Adı</label>
                                <p class="font-weight-bold" id="notification_name_span"><?= $notification->name ?></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Bildirim Başlığı</label>
                                <p class="font-weight-bold" id="notification_title_span"><?=$notification->title?></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Bildirim İçeriği</label>
                                <p class="font-weight-bold" id="notification_contents_span"><?=$notification->contents?></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Bildirim Yönlendirme Linki</label>
                                <p class="font-weight-bold" id="notification_link_span"><?= $notification->link ?></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Bildirim Başlangıç Tarihi</label>
                                <p class="font-weight-bold" id="start_at_span"><?=format_date($notification->start_at)?></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Bildirim Bitiş Tarihi</label>
                                <p class="font-weight-bold" id="end_up_span"><?=format_date($notification->end_up)?></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Hedeflenen Grup</label>
                                <p class="font-weight-bold" id="target_group_span"><?=$notification->target_group?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script>
       $(document).ready(function(){
            // AJAX isteği gönderme
            $.ajax({
                url: '<?php echo base_url("admin/notification/get_daily_notification_counts/") . $notification->id; ?>',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    var dates = [];
                    var notificationCounts = [];

                    // Verileri tabloya ekleme ve grafik için diziye ekleme
                    data.forEach(function(item) {
                        // Tarihi formatla
                        var date = new Date(item.date);
                        var formattedDate = formatDate(date);

                        dates.push(formattedDate);
                        notificationCounts.push(item.notification_count);

                        $('#notificationTable tbody').append('<tr><td>' + formattedDate + '</td><td>' + item.notification_count + '</td></tr>');
                    });

                    // Verileri grafik olarak gösterme
                    var ctx = document.getElementById('notificationChart').getContext('2d');
                    var myChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: dates,
                            datasets: [{
                                label: 'Bildirim Okunma Sayısı (Günlük)',
                                data: notificationCounts,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            });
        });

        // Tarih biçimini dönüştürme işlevi
        function formatDate(date) {
            var options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('tr-TR', options);
        }


    </script>

