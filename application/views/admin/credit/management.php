<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5>Kredi Yönetimi</h5>
            </div>

            <?php if ($this->session->flashdata('success')) { ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php } ?>

            <?php if ($this->session->flashdata('error')) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php } ?>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-plus-circle"></i> Yeni Kredi Teklifi Oluştur
                        </div>
                        <div class="card-body">
                            <form id="creditOfferForm" action="javascript:void(0);" method="POST">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="dealerId">Kullanıcı Seçin</label>
                                            <select id="dealerId" class="form-control" name="user_id" required>
                                                <option value="">Kullanıcı seçin</option>
                                                <?php foreach ($dealers as $dealer): ?>
                                                    <option value="<?= $dealer->user_id ?>"><?= $dealer->name . ' ' . $dealer->surname ?> (<?= $dealer->email ?>)</option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <!-- Kredi durum uyarıları -->
                                        <div id="creditStatusAlerts" class="mt-2">
                                            <div id="activeCreditAlert" class="alert alert-danger" style="display: none;">
                                                <i class="fas fa-exclamation-triangle"></i> Kullanıcının aktif kredisi bulunmaktadır. Yeni kredi teklifi verilemez.
                                            </div>
                                            <div id="pendingOfferAlert" class="alert alert-warning" style="display: none;">
                                                <i class="fas fa-info-circle"></i> Kullanıcının bekleyen kredi teklifi bulunmaktadır. Yeni teklif oluşturduğunuzda, eski teklif silinecektir.
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="amount">Kredi Tutarı (TL)</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="fee_percentage">İşlem Ücreti Oranı (%)</label>
                                            <input type="number" step="0.01" min="0" class="form-control" id="fee_percentage" name="fee_percentage" value="0">
                                            <small class="text-muted">0 girerseniz işlem ücreti alınmayacaktır</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="description">Açıklama</label>
                                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="term_days">Vade (Gün)</label>
                                            <input type="number" min="1" class="form-control" id="term_days" name="term_days" value="30" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="valid_days">Teklif Geçerlilik Süresi (Gün)</label>
                                            <input type="number" min="1" class="form-control" id="valid_days" name="valid_days" value="7">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" id="createOfferBtn">Kredi Teklifi Oluştur</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-list"></i> Kredi Teklifleri
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="creditOffersTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Kullanıcı</th>
                                            <th>Tutar</th>
                                            <th>İşlem Ücreti</th>
                                            <th>Vade</th>
                                            <th>Geçerlilik</th>
                                            <th>Oluşturulma</th>
                                            <th>Durum</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($offers)): ?>
                                            <?php foreach ($offers as $offer): ?>
                                                <tr>
                                                    <td><?= $offer->id ?></td>
                                                    <td><?= $offer->name . ' ' . $offer->surname ?><br><small><?= $offer->email ?></small></td>
                                                    <td><?= number_format($offer->amount, 2, ',', '.') ?> TL</td>
                                                    <td><?= number_format($offer->fee_percentage, 2, ',', '.') ?>%</td>
                                                    <td><?= $offer->term_days ?> gün</td>
                                                    <td><?= date('d.m.Y', strtotime($offer->offer_valid_until)) ?></td>
                                                    <td><?= date('d.m.Y H:i', strtotime($offer->created_at)) ?></td>
                                                    <td>
                                                        <?php 
                                                        $statusClass = '';
                                                        $statusText = '';
                                                        
                                                        switch($offer->status) {
                                                            case 0:
                                                                $statusClass = 'badge-secondary';
                                                                $statusText = 'Beklemede';
                                                                break;
                                                            case 1:
                                                                $statusClass = 'badge-primary';
                                                                $statusText = 'Aktif';
                                                                break;
                                                            case 2:
                                                                $statusClass = 'badge-info';
                                                                $statusText = 'Kısmen Kabul Edildi';
                                                                break;
                                                            case 3:
                                                                $statusClass = 'badge-success';
                                                                $statusText = 'Onaylandı';
                                                                break;
                                                            case 4:
                                                                $statusClass = 'badge-danger';
                                                                $statusText = 'Reddedildi';
                                                                break;
                                                            default:
                                                                $statusClass = 'badge-warning';
                                                                $statusText = 'Bilinmiyor';
                                                        }
                                                        ?>
                                                        <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('admin/credit_management/user_credits/' . $offer->user_id) ?>" class="btn btn-sm btn-info" title="Kullanıcı Kredileri"><i class="fas fa-user"></i></a>
                                                        <?php if ($offer->status != 2 && $offer->status != 3): // Teklif kabul edilmemiş ise silme butonunu göster ?>
                                                            <a href="<?= base_url('admin/credit_management/delete_offer/' . $offer->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu teklifi silmek istediğinizden emin misiniz?');" title="Teklifi İptal Et"><i class="fas fa-trash"></i></a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="9" class="text-center">Henüz kredi teklifi bulunmamaktadır.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<!-- Kredi Teklifi Onay Modalı -->
<div class="modal fade" id="confirmOfferModal" tabindex="-1" role="dialog" aria-labelledby="confirmOfferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmOfferModalLabel">Kredi Teklifi Onay</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <!-- Genel Bilgiler -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Kredi Bilgileri</h6>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th>Kredi Tutarı:</th>
                                    <td id="modalAmount">-</td>
                                </tr>
                                <tr>
                                    <th>İşlem Ücreti Oranı:</th>
                                    <td id="modalFeePercentage">-</td>
                                </tr>
                                <tr>
                                    <th>İşlem Ücreti:</th>
                                    <td id="modalFeeAmount">-</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th>Vade:</th>
                                    <td id="modalTermDays">-</td>
                                </tr>
                                <tr>
                                    <th>Hesaba Aktarılacak Net Tutar:</th>
                                    <td id="modalNetAmount">-</td>
                                </tr>
                                <tr>
                                    <th>Geçerlilik Süresi:</th>
                                    <td id="modalValidDays">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Bekleyen Teklif Uyarısı -->
                    <div id="modalPendingOfferAlert" class="row mb-3" style="display: none;">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i> <strong>Dikkat:</strong> Kullanıcının bekleyen kredi teklifi bulunmaktadır. Bu teklifi onaylarsanız, eski teklif silinecektir.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kullanıcı Bilgileri -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">Kullanıcı Bilgileri</h6>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th>Ad Soyad:</th>
                                    <td id="modalUserName">-</td>
                                </tr>
                                <tr>
                                    <th>E-posta:</th>
                                    <td id="modalUserEmail">-</td>
                                </tr>
                                <tr>
                                    <th>Bayi Tipi:</th>
                                    <td id="modalUserType">-</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th>Toplam Ödemeler:</th>
                                    <td id="modalTotalPayments">-</td>
                                </tr>
                                <tr>
                                    <th>Aktif Krediler:</th>
                                    <td id="modalActiveCredits">-</td>
                                </tr>
                                <tr>
                                    <th>Zamanında Ödeme Oranı:</th>
                                    <td id="modalOnTimePayments">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Kredi Puanı -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="border-bottom pb-2">
                                Kredi Puanı 
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#scoreInfoModal" class="ml-1" title="Kredi puanı nasıl hesaplanır?">
                                    <i class="fas fa-question-circle text-info"></i>
                                </a>
                            </h6>
                        </div>
                        <div class="col-md-6">
                            <div class="progress" style="height: 30px;">
                                <div id="modalCreditScore" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div id="modalCreditRating" class="text-center p-2 rounded" style="background-color: #f8f9fa;">
                                Kredi puanı hesaplanıyor...
                            </div>
                            <div id="modalCreditAdvice" class="text-muted mt-2 small">
                                Kullanıcının kredi geçmişi değerlendiriliyor...
                            </div>
                        </div>
                    </div>
                    
                    <!-- Açıklama Alanı -->
                    <div class="row">
                        <div class="col-12">
                            <div id="modalDescription" class="alert alert-info">
                                -
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <form id="finalOfferForm" action="<?= base_url('admin/credit_management/create_offer') ?>" method="POST">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
                    <input type="hidden" name="user_id" id="final_user_id">
                    <input type="hidden" name="amount" id="final_amount">
                    <input type="hidden" name="fee_percentage" id="final_fee_percentage">
                    <input type="hidden" name="term_days" id="final_term_days">
                    <input type="hidden" name="valid_days" id="final_valid_days">
                    <input type="hidden" name="description" id="final_description">
                    <button type="submit" class="btn btn-primary">Teklifi Oluştur</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Kredi Puanı Hesaplama Bilgi Modalı -->
<div class="modal fade" id="scoreInfoModal" tabindex="-1" role="dialog" aria-labelledby="scoreInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scoreInfoModalLabel">Kredi Puanı Nasıl Hesaplanır?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row mb-3">
                        <div class="col-12">
                            <p>Kredi puanı, kullanıcının kredi kullanım alışkanlıklarına ve ödeme geçmişine dayanarak hesaplanan 0-100 arasında bir değerdir. Bu puan, kullanıcının kredi riskini değerlendirmek için kullanılır.</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Yeni Kullanıcılar İçin</h6>
                                </div>
                                <div class="card-body">
                                    <p>Hiç kredi kullanmamış kullanıcılara otomatik olarak <strong>75 puan</strong> verilir. Bu puan, kullanıcının ilk kredi deneyiminde bir başlangıç ​​noktası olarak kullanılır.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Kredi Kullanmış Kullanıcılar İçin</h6>
                                </div>
                                <div class="card-body">
                                    <p>Kredi kullanmış kullanıcıların puanı aşağıdaki faktörlere göre hesaplanır:</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Faktör</th>
                                        <th>Açıklama</th>
                                        <th>Etki</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Baz Puan</strong></td>
                                        <td>Her kullanıcı için başlangıç puanı</td>
                                        <td><span class="badge badge-success">+30 puan</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Zamanında Ödeme</strong></td>
                                        <td>Vadesi gelmeden yapılan ödemeler yüzdesi</td>
                                        <td><span class="badge badge-success">+40 puana kadar</span><br><small>Zamanında ödeme oranınızın %0.4 katı</small></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Kredi Geçmişi</strong></td>
                                        <td>Toplam kullanılmış kredi sayısı</td>
                                        <td><span class="badge badge-success">+10 puana kadar</span><br><small>Her kredi için +2 puan (maksimum 5 kredi)</small></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Aktif Krediler</strong></td>
                                        <td>Şu anda ödemesi devam eden kredi sayısı</td>
                                        <td><span class="badge badge-danger">-15 puana kadar</span><br><small>Birden fazla aktif kredi için her biri -5 puan</small></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Vadesi Geçmiş Krediler</strong></td>
                                        <td>Ödenmemiş/vade tarihi geçmiş krediler</td>
                                        <td><span class="badge badge-danger">-25 puana kadar</span><br><small>Her vadesi geçmiş kredi için -10 puan</small></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i> Kredi puanınızı yükseltmek için, ödemelerinizi zamanında yapmanız ve aktif kredi sayınızı düşük tutmanız önerilir.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="border-bottom pb-2 mt-3">Puan Değerlendirme Aralıkları</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Puan Aralığı</th>
                                            <th>Değerlendirme</th>
                                            <th>Risk Seviyesi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr style="background-color: rgba(39, 174, 96, 0.2)">
                                            <td>85-100</td>
                                            <td>Çok İyi</td>
                                            <td>Çok Düşük Risk</td>
                                        </tr>
                                        <tr style="background-color: rgba(46, 204, 113, 0.2)">
                                            <td>70-84</td>
                                            <td>İyi</td>
                                            <td>Düşük Risk</td>
                                        </tr>
                                        <tr style="background-color: rgba(243, 156, 18, 0.2)">
                                            <td>50-69</td>
                                            <td>Orta</td>
                                            <td>Orta Risk</td>
                                        </tr>
                                        <tr style="background-color: rgba(230, 126, 34, 0.2)">
                                            <td>30-49</td>
                                            <td>Riskli</td>
                                            <td>Yüksek Risk</td>
                                        </tr>
                                        <tr style="background-color: rgba(231, 76, 60, 0.2)">
                                            <td>0-29</td>
                                            <td>Çok Riskli</td>
                                            <td>Çok Yüksek Risk</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // DataTable Ayarları
        try {
            $('#creditOffersTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json"
                },
                "order": [[6, "desc"]], // Oluşturulma tarihine göre sırala
                "columnDefs": [
                    { "orderable": false, "targets": 8 } // İşlemler sütununu sıralamadan çıkar
                ]
            });
        } catch(e) {
            console.error("DataTable başlatma hatası:", e);
        }
        
        // Kullanıcı seçildiğinde kredi durumunu kontrol et
        $("#dealerId").on('change', function() {
            var userId = $(this).val();
            
            // Uyarı alanlarını temizle
            $("#activeCreditAlert").hide();
            $("#pendingOfferAlert").hide();
            
            if (!userId) {
                return;
            }
            
            // Kullanıcının kredi durumunu kontrol et
            $.ajax({
                url: '<?= base_url('admin/credit_management/get_user_credit_status') ?>',
                type: 'POST',
                data: {
                    user_id: userId,
                    <?php echo $this->security->get_csrf_token_name(); ?>: "<?php echo $this->security->get_csrf_hash(); ?>"
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        // Aktif kredi kontrolü
                        if (response.has_active_credit) {
                            $("#activeCreditAlert").show();
                            $("#createOfferBtn").prop("disabled", true).addClass("disabled");
                        } else {
                            $("#activeCreditAlert").hide();
                            $("#createOfferBtn").prop("disabled", false).removeClass("disabled");
                        }
                        
                        // Bekleyen teklif kontrolü
                        if (response.has_pending_offers) {
                            $("#pendingOfferAlert").show();
                        } else {
                            $("#pendingOfferAlert").hide();
                        }
                    }
                },
                error: function() {
                    console.error("Kullanıcı kredi durumu alınamadı");
                }
            });
        });
        
        // Kredi teklifi formunu yakalayıp onay modalını göster
        $("#creditOfferForm").on('submit', function(e) {
            e.preventDefault();
            
            // Form verilerini al
            var userId = $("#dealerId").val();
            var amount = parseFloat($("#amount").val());
            var feePercentage = parseFloat($("#fee_percentage").val());
            var description = $("#description").val();
            var termDays = parseInt($("#term_days").val());
            var validDays = parseInt($("#valid_days").val());
            
            // Form validasyonu
            if (!userId || isNaN(amount) || amount <= 0 || isNaN(termDays) || termDays <= 0) {
                alert("Lütfen tüm zorunlu alanları doldurun");
                return;
            }
            
            // Aktif kredi kontrolü - buton disabled olsa da son bir kontrol daha
            if ($("#activeCreditAlert").is(":visible")) {
                alert("Kullanıcının aktif kredisi bulunmaktadır. Yeni kredi teklifi verilemez.");
                return;
            }
            
            // Seçilen kullanıcının bilgilerini al
            var userOption = $("#dealerId option:selected");
            var userName = userOption.text().split(' (')[0];
            var userEmail = userOption.text().match(/\((.*?)\)/)[1];
            
            // Modalda gösterilecek hesaplamaları yap
            var feeAmount = (amount * feePercentage / 100).toFixed(2);
            var netAmount = (parseFloat(amount) - parseFloat(feeAmount)).toFixed(2);
            
            // Modal içeriğini doldur
            $("#modalAmount").text(amount.toFixed(2) + " TL");
            $("#modalFeePercentage").text("%" + feePercentage);
            $("#modalFeeAmount").text(feeAmount + " TL");
            $("#modalTermDays").text(termDays + " gün");
            $("#modalNetAmount").text(netAmount + " TL");
            $("#modalValidDays").text(validDays + " gün");
            
            $("#modalUserName").text(userName);
            $("#modalUserEmail").text(userEmail);
            $("#modalDescription").text(description || "Açıklama eklenmedi");
            
            // Bekleyen teklif uyarısını modala aktar
            if ($("#pendingOfferAlert").is(":visible")) {
                $("#modalPendingOfferAlert").show();
            } else {
                $("#modalPendingOfferAlert").hide();
            }
            
            // Kullanıcı bilgilerini AJAX ile al
            $.ajax({
                url: '<?= base_url('admin/credit_management/get_user_credit_data') ?>',
                type: 'POST',
                data: {
                    user_id: userId,
                    <?php echo $this->security->get_csrf_token_name(); ?>: "<?php echo $this->security->get_csrf_hash(); ?>"
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        var data = response.data;
                        
                        // Kullanıcı bilgilerini güncelle
                        $("#modalUserType").text(data.dealer_type || "-");
                        $("#modalTotalPayments").text(data.total_payments || "0 TL");
                        $("#modalActiveCredits").text(data.active_credits || "0");
                        $("#modalOnTimePayments").text(data.on_time_payment_rate || "-%");
                        
                        // Kredi puanını ve değerlendirmeyi güncelle
                        var creditScore = data.credit_score || 0;
                        var scoreColor = getCreditScoreColor(creditScore, data.is_new_user);
                        var scoreRating = getCreditScoreRating(creditScore, data.is_new_user);
                        
                        $("#modalCreditScore")
                            .css("width", creditScore + "%")
                            .attr("aria-valuenow", creditScore)
                            .text(creditScore)
                            .css("background-color", scoreColor);
                        
                        $("#modalCreditRating")
                            .text(scoreRating.title)
                            .css("background-color", scoreColor)
                            .css("color", "#fff");
                        
                        $("#modalCreditAdvice").text(scoreRating.advice);
                    } else {
                        // Hata durumunda varsayılan değerler
                        $("#modalUserType").text("-");
                        $("#modalTotalPayments").text("Veri yok");
                        $("#modalActiveCredits").text("Veri yok");
                        $("#modalOnTimePayments").text("Veri yok");
                        
                        $("#modalCreditScore")
                            .css("width", "0%")
                            .attr("aria-valuenow", 0)
                            .text(0)
                            .css("background-color", "#6c757d");
                        
                        $("#modalCreditRating")
                            .text("Değerlendirilemedi")
                            .css("background-color", "#6c757d")
                            .css("color", "#fff");
                        
                        $("#modalCreditAdvice").text("Kullanıcı geçmişi değerlendirilemedi.");
                    }
                },
                error: function() {
                    // Hata durumunda varsayılan değerler göster
                    $("#modalUserType").text("Veri alınamadı");
                    $("#modalTotalPayments").text("Veri alınamadı");
                    $("#modalActiveCredits").text("Veri alınamadı");
                    $("#modalOnTimePayments").text("Veri alınamadı");
                    
                    $("#modalCreditScore")
                        .css("width", "0%")
                        .attr("aria-valuenow", 0)
                        .text(0)
                        .css("background-color", "#dc3545");
                    
                    $("#modalCreditRating")
                        .text("Hata")
                        .css("background-color", "#dc3545")
                        .css("color", "#fff");
                    
                    $("#modalCreditAdvice").text("Kullanıcı verilerine erişilemiyor.");
                }
            });
            
            // Gizli form alanlarını doldur
            $("#final_user_id").val(userId);
            $("#final_amount").val(amount);
            $("#final_fee_percentage").val(feePercentage);
            $("#final_term_days").val(termDays);
            $("#final_valid_days").val(validDays);
            $("#final_description").val(description);
            
            // Modalı göster
            $("#confirmOfferModal").modal('show');
        });
        
        // Kredi puanına göre renk döndüren fonksiyon
        function getCreditScoreColor(score, isNewUser = false) {
            if (isNewUser) {
                return "#3498db"; // Mavi (yeni kullanıcı)
            }
            
            if (score >= 85) return "#27ae60"; // Yeşil
            if (score >= 70) return "#2ecc71"; // Açık yeşil
            if (score >= 50) return "#f39c12"; // Turuncu
            if (score >= 30) return "#e67e22"; // Koyu turuncu
            return "#e74c3c"; // Kırmızı
        }
        
        // Kredi puanı değerlendirme fonksiyonu
        function getCreditScoreRating(score, isNewUser = false) {
            let rating = {
                title: "",
                advice: "",
                color: ""
            };
            
            if (isNewUser) {
                rating.title = "Yeni Kullanıcı";
                rating.advice = "Kredi geçmişi bulunmamaktadır. İlk kredi kullanımınızda düşük miktarlar önerilir.";
                rating.color = "#3498db";
                return rating;
            }
            
            if (score >= 85) {
                rating.title = "Çok İyi";
                rating.advice = "Mükemmel kredi geçmişi. Yüksek limitli krediler için uygun.";
                rating.color = "#27ae60";
            } else if (score >= 70) {
                rating.title = "İyi";
                rating.advice = "İyi bir kredi geçmişi. Makul limitli krediler için uygun.";
                rating.color = "#2ecc71";
            } else if (score >= 50) {
                rating.title = "Orta";
                rating.advice = "Orta seviyede kredi geçmişi. Dikkatli değerlendirme gerekiyor.";
                rating.color = "#f39c12";
            } else if (score >= 30) {
                rating.title = "Riskli";
                rating.advice = "Riskli kredi geçmişi. Düşük limitli krediler önerilir.";
                rating.color = "#e67e22";
            } else {
                rating.title = "Çok Riskli";
                rating.advice = "Çok riskli kredi geçmişi. Kredi vermeden önce iyi değerlendirin.";
                rating.color = "#e74c3c";
            }
            
            return rating;
        }
    });
</script>

<!-- Kredi Tablo CSS ve Responsive Düzenlemeler -->
<style>
    @media (max-width: 767px) {
        table.dataTable>thead>tr>th:nth-child(4), 
        table.dataTable>tbody>tr>td:nth-child(4),
        table.dataTable>thead>tr>th:nth-child(5), 
        table.dataTable>tbody>tr>td:nth-child(5),
        table.dataTable>thead>tr>th:nth-child(6), 
        table.dataTable>tbody>tr>td:nth-child(6) {
            display: none;
        }
    }
</style> 