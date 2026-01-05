<div class="col-lg-9">
    <!-- Alabileceği Abonelikler Bölümü -->
    <div class="fp-card fp-card-client mb-3">
        <div class="fp-cc-head">
            <h1 class="title">Alabileceğiniz Abonelikler</h1>
        </div>
    </div>
    <div class="alert alert-primary" role="alert">
        Eğer mevcut bir aboneliğe sahipseniz ve yeni bir abonelik satın alırsanız, mevcut aboneliğiniz iptal edilecektir. Ancak aynı abonelik planını satın alırsanız mevcut aboneliğinizin süresi uzatılacaktır.
    </div>
    <div class="row">
        <?php foreach ($available_subscriptions as $available_subscription) { ?>
            <div class="col-md-6">
                <div class="subscription-card">
                    <div class="head">
                        <div class="icon">
                            <i class="ri-vip-crown-line"></i>
                        </div>
                        <div class="content">
                            <div class="title"><?= $available_subscription->name ?></div>
                            <div class="price"><?=$available_subscription->price?> TL<span>/<?=$available_subscription->duration?> Gün</span></div>
                        </div>
                        <?php
                        if (!empty($active_subscription->subscription_id) && $available_subscription->id == $active_subscription->subscription_id) { ?>
                            <div class="active-badge">
                                Aktif Abonelik
                            </div>
                        <?php } ?>
                    </div>
                    <div class="body">
                        <h6><?=$available_subscription->description?></h6>
                        <ul>
                            <?php $subscription_features = $this->db->where('subscription_id', $available_subscription->id)->get('subscription_features')->result(); ?>
                            <?php foreach ($subscription_features as $feature) { ?>
                                <li><?= getFeature($feature->id) ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="p-20">
                        <a href="<?= base_url('client/buySubscription/') . $available_subscription->id; ?>" class="btn btn-primary w-100">Satın Al</a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="fp-card fp-card-client mt-4">
        <div class="fp-cc-head">
            <h1 class="title">Abonelik Kazançlarım</h1>
        </div>
        <div class="fp-cc-body">
            <div class="table-responsive fp-table-border">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Abonelik Adı</th>
                        <th scope="col">Abonelik Kazancı</th>
                        <th scope="col">Açıklama</th>
                        <th scope="col">İşlem Tarihi</th>
                        <th scope="col">Durum</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($subscription_earnings as $subscription_earning) {?>
                        <tr>
                            <td><?= $subscription_earning->subscription_name ?></td>
                            <td><?= $subscription_earning->amount ?> TL</td>
                            <td><?= $subscription_earning->description ?></td>
                            <td><?= format_date($subscription_earning->transaction_date) ?></td>
                            <td><?= $subscription_earning->status ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination-links mt-3 d-flex justify-content-end">
                <?= $pagination ?>
            </div>
        </div>
    </div>
    <div class="fp-card fp-card-client mt-4">
        <div class="fp-cc-head">
            <h1 class="title">Aboneliklerim</h1>
        </div>
        <div class="fp-cc-body">
            <div class="table-responsive fp-table-border">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Abonelik Adı</th>
                        <th scope="col">Bitiş Tarihi</th>
                        <th scope="col">Kalan Gün</th>
                        <th scope="col">Durum</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($my_subscriptions as $my_subscription) {
                        $subscription = $this->db->where('id', $my_subscription->subscription_id)->get('subscriptions')->row();
                        $today = date('Y-m-d');
                        $end_date = $my_subscription->end_date;
                        $diff = strtotime($end_date) - strtotime($today);
                        $days = floor($diff / (60 * 60 * 24));

                        if ($days < 0) {
                            $days = 0;
                        }

                        ?>
                        <tr>
                            <td><?= $subscription->name ?></td>
                            <td><?= format_date($my_subscription->end_date) ?></td>
                            <td><?= $days ?></td>
                            <td><?= ($days < 1) ? "<span class='text-danger'>Sonlandı</span>" : "<span class='text-success'>Aktif</span>" ?></td>
                            <td>
                                <?php if ($days < 1 || $my_subscription->auto_renew == 'passive' || $my_subscription->status == 'passive') { ?>
                                    <a class="btn btn-outline-secondary">Aboneliği Sonlandır</a>
                                    <a href="#subscriptionDetails" data-bs-toggle="modal" onclick="initSubscriptionDetails(<?=$my_subscription->id?>)" class="btn btn-primary">Detaylar</a>
                                <?php }else{ ?>
                                    <a href="#" id="cancelSubscription" class="btn btn-danger">Aboneliği Sonlandır</a>
                                    <a href="#subscriptionDetails" data-bs-toggle="modal" onclick="initSubscriptionDetails(<?=$my_subscription->id?>)" class="btn btn-primary">Detaylar</a>
                                    <script>
                                        // Abonelik iptali işlemi
                                        document.getElementById('cancelSubscription').addEventListener('click', function(event) {
                                            event.preventDefault();

                                            const cancelUrl = "<?= base_url('client/dashboard/cancelSubscription/') . $my_subscription->id; ?>";

                                            Swal.fire({
                                                title: 'Dikkat!',
                                                text: 'Mevcut aboneliniz iptal edilecek ve otomatik olarak yenilenmeyecek. Aboneliğinizin sonlanma tarihine kadar avantajlarınızı kullanmaya devam edebilirsiniz.',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonText: 'Onayla',
                                                cancelButtonText: 'Kapat'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.href = cancelUrl; // Kullanıcı onayladığında yönlendirme yapılır.
                                                }
                                            });
                                        });
                                    </script>
                                <?php } ?>

                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</section>

<!-- Subscription Details Modal -->
<div class="modal fade" id="subscriptionDetails" tabindex="-1" aria-labelledby="subscriptionDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subscriptionDetailsLabel">Abonelik Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Subscription details will be loaded here -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col">Abonelik Adı</th>
                            <th scope="col">Başlangıç Tarihi</th>
                            <th scope="col">Bitiş Tarihi</th>
                            <th scope="col">Toplam Gün</th>
                            <th scope="col">Kalan Gün</th>
                            <th scope="col">Abonelik Ücreti</th>
                            <th scope="col">Abonelik Kazancı</th>
                            <th scope="col">Otomatik Yenileme</th>
                            <th scope="col">Durum</th>
                        </tr>
                        </thead>
                        <tbody id="subscription-info">
                        <!-- Subscription details will be injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>


<script>
    function initSubscriptionDetails(subscriptionId) {
        $.ajax({
            url: "<?= base_url('client/dashboard/subscriptionDetail/'); ?>" + subscriptionId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#subscription-info').html(`
                <tr>
                    <td>${response.name}</td>
                    <td>${response.start_date}</td>
                    <td>${response.end_date}</td>
                    <td>${response.duration}</td>
                    <td>${response.remaining}</td>
                    <td>${response.price} TL</td>
                    <td>${response.user_earnings} TL</td>
                    <td>${response.auto_renew}</td>
                    <td>${response.status}</td>
                </tr>
            `);
            },
            error: function(error) {
                console.error('Error loading subscription details:', error);
            }
        });
    }
</script>