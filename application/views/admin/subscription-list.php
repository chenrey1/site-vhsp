<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Abone Listesi</h5>
                <!--<a class="btn btn-primary" data-toggle="modal" data-target="#userAddSub" href="#userAddSub">Kullanıcıya Abonelik Ekle</a>-->
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Aboneler</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered border dataTable table-product">
                            <thead class="thead-light">
                            <tr>
                                <th>Abonelik Adı</th>
                                <th>Kullanıcı</th>
                                <th>Kalan Gün</th>
                                <th>Abonelik İçin Harcadığı Tutar (TOPLAM)</th>
                                <th>Abonelikten Kazandığı Tutar (TOPLAM)</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($subscriptions as $subscription) {
                                $subscription->total_earned = round($this->db->select_sum('amount')->where('user_id', $subscription->user_id)->get('user_savings')->row()->amount, 2);
                                $subscription->total_spent = round($this->db->select_sum('price')->where('user_id', $subscription->user_id)->get('user_subscriptions')->row()->price, 2);
                                ?>
                                <tr>
                                    <td><a href="<?= base_url('admin/subscription/subList/' . $subscription->subscription_id); ?>"><?= $subscription->subscription_name ?></a></td>
                                    <td><?= $subscription->name . " " .$subscription->surname . ' ('.$subscription->email.')' ?></td>
                                    <td><?= $this->M_Subscription->calculateRemainingDay($subscription->end_date); ?></td>
                                    <td><?= $subscription->total_spent ?>₺</td>
                                    <td><?= $subscription->total_earned ?>₺</td>
                                    <td class="text-center">
                                        <a href="#subscriptionHistory" data-toggle="modal" onclick="initSubscriptionHistory(<?=$subscription->user_id?>)" class="btn btn-primary"> Geçmişi İncele</a>
                                        <a href="<?= base_url('admin/subscription/ended_subscription/') . $subscription->id ?>" class="btn btn-danger mt-1">Aboneliği Sonlandır</a>
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

    <div class="modal fade" id="userAddSub" tabindex="-1" role="dialog" aria-labelledby="userAddSub" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Kullanıcıya Abonelik Ekle</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label>Kullanıcı Adı</label>
                    <div class="d-block mb-3">
                        <select class="selectpicker" data-live-search="true">
                            <option data-tokens="ketchup mustard">oritorius</option>
                            <option data-tokens="mustard">oritorius 2</option>
                            <option data-tokens="frosting">oritorius 3</option>
                        </select>
                        <label class="text-muted small">Kullanıcıyı bulmak için en az 3 kelime yazınız</label>
                    </div>

                    <label>Abonelik Türü Seçiniz</label>
                    <div class="d-block">
                        <select class="selectpicker" data-live-search="true">
                            <option data-tokens="ketchup mustard">VIP</option>
                            <option data-tokens="mustard">VIP 2</option>
                            <option data-tokens="frosting">VIP 3</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-primary w-100">Ekle</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dropdown.bootstrap-select, .dropdown.bootstrap-select .dropdown-toggle {
            width: 100% !important;
        }
        .dropdown.bootstrap-select .dropdown-toggle {
            background-color: #fff !important;
            border: 1px solid #ced4da !important;
        }
    </style>

    <div class="modal fade" id="subscriptionHistory" tabindex="-1" role="dialog" aria-labelledby="subscriptionHistoryLabel" aria-hidden="true">
        <div class="modal-dialog subscription-modal-dialog" role="document">
            <div class="modal-content subscription-modal-content">
                <div class="modal-header subscription-modal-header">
                    <h6 class="modal-title" id="subscriptionHistoryLabel">Geçmiş Abonelikler ve Kazanımlar</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body subscription-modal-body">
                    <!-- Sekme Başlıkları -->
                    <ul class="nav nav-tabs subscription-nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active subscription-nav-link" id="subscriptions-tab" data-toggle="tab" href="#subscriptions" role="tab" aria-controls="subscriptions" aria-selected="true">Geçmiş Abonelikler</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link subscription-nav-link" id="achievements-tab" data-toggle="tab" href="#achievements" role="tab" aria-controls="achievements" aria-selected="false">Geçmiş Kazanımlar</a>
                        </li>
                    </ul>
                    <!-- Sekme İçerikleri -->
                    <div class="tab-content subscription-tab-content" id="myTabContent">
                        <div class="tab-pane fade show active subscription-tab-pane" id="subscriptions" role="tabpanel" aria-labelledby="subscriptions-tab">
                            <div class="p-3">
                                <!-- Geçmiş Abonelikler İçeriği -->
                                <div class="table-responsive subscription-table-responsive">
                                    <table class="table table-bordered subscription-table">
                                        <thead>
                                        <tr>
                                            <th scope="col">Abonelik Adı</th>
                                            <th scope="col">Başlangıç Tarihi</th>
                                            <th scope="col">Bitiş Tarihi</th>
                                            <th scope="col">Toplam Gün</th>
                                            <th scope="col">Abonelik Ücreti</th>
                                        </tr>
                                        </thead>
                                        <tbody id="subscriptionsTableBody">
                                        <!-- Dinamik İçerik -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade subscription-tab-pane" id="achievements" role="tabpanel" aria-labelledby="achievements-tab">
                            <div class="p-3">

                                <p><strong>Bakiye İadesi:</strong> Bir kullanıcının yaptığı ödeme karşılığında belirlenen tutar kadar aldığı ödeme miktarıdır.</p>
                                <p><strong>Ödeme Komisyon Kazancı:</strong> Kullanıcının normal komisyon oranı yerine abonelik için sunulan komisyon oranından faydalanarak elde ettiği kazanç miktarıdır.</p>

                                <!-- Geçmiş Kazanımlar İçeriği -->
                                <div class="table-responsive subscription-table-responsive">
                                    <table class="table table-bordered subscription-table">
                                        <thead>
                                        <tr>
                                            <th scope="col">Abonelik Adı</th>
                                            <th scope="col">Kazanım Tutarı</th>
                                            <th scope="col">Kazanım Açıklaması</th>
                                            <th scope="col">Tarih</th>
                                            <th scope="col">Satın Alım Numarası</th>
                                            <th scope="col">Durum</th>
                                        </tr>
                                        </thead>
                                        <tbody id="achievementsTableBody">
                                        <!-- Dinamik İçerik -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer subscription-modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-secondary">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function initSubscriptionHistory(userId) {
            $.ajax({
                url: `get_history/${userId}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);

                    // Geçmiş Abonelikler Tablosunu Oluştur
                    let subscriptionsHtml = '';
                    response.past_subscriptions.forEach(subscription => {
                        subscriptionsHtml += `
                    <tr>
                        <td>${subscription.subscription_name}</td>
                        <td>${formatDate(subscription.start_date)}</td>
                        <td>${formatDate(subscription.end_date)}</td>
                        <td>${subscription.duration}</td>
                        <td>${subscription.price}₺</td>
                    </tr>
                `;
                    });
                    $('#subscriptionsTableBody').html(subscriptionsHtml);

                    // Geçmiş Kazanımlar Listesini Oluştur
                    let achievementsHtml = '';
                    response.achievements.forEach(achievement => {
                        achievementsHtml += `
                    <tr>
                        <td>${achievement.subscription_name}</td>
                        <td>${achievement.amount}₺</td>
                        <td>${achievement.reason}</td>
                        <td>${formatDate(achievement.transaction_date)}</td>
                        <td><a href="${achievement.shop_link}" target="_blank">${achievement.shop_id}</a></td>
                        <td>${achievement.status}</td>
                    </tr>
                `;
                    });
                    $('#achievementsTableBody').html(achievementsHtml);

                    // Modal'i göster
                    $('#subscriptionHistory').modal('show');
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return new Date(dateString).toLocaleDateString('tr-TR', options);
        }

    </script>

