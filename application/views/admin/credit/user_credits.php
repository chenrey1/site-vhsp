<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title d-flex justify-content-between align-items-center">
                <h5>Kullanıcı Kredileri</h5>
                <a href="<?= base_url('admin/credit_management') ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-arrow-left"></i> Geri Dön</a>
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
                            <i class="fas fa-user"></i> Kullanıcı Bilgileri
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><?= $user->name . ' ' . $user->surname ?></h5>
                                    <p><strong>E-posta:</strong> <?= $user->email ?></p>
                                    <p><strong>Telefon:</strong> <?= $user->phone ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Bakiye:</strong> <?= number_format($user->balance, 2, ',', '.') ?> TL</p>
                                    <p><strong>Çekilebilir Bakiye:</strong> <?= number_format($user->balance2, 2, ',', '.') ?> TL</p>
                                    <p><strong>Kayıt Tarihi:</strong> <?= $user->date ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-credit-card"></i> Aktif Krediler
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="activeCreditsTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tutar</th>
                                            <th>İşlem Ücreti Oranı</th>
                                            <th>Net Tutar</th>
                                            <th>Kalan Tutar</th>
                                            <th>Son Ödeme Tarihi</th>
                                            <th>Son Ödeme</th>
                                            <th>Durum</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $active_credits_exist = false;
                                        if (!empty($credits)) {
                                            foreach ($credits as $credit) {
                                                if (in_array($credit->status, [1, 3, 4])) { // Aktif, Kısmi Ödeme, Gecikti
                                                    $active_credits_exist = true;
                                        ?>
                                                <tr>
                                                    <td><?= $credit->id ?></td>
                                                    <td><?= number_format($credit->amount, 2, ',', '.') ?> TL</td>
                                                    <td><?= number_format($credit->fee_percentage, 2, ',', '.') ?>%</td>
                                                    <td><?= number_format($credit->net_amount, 2, ',', '.') ?> TL</td>
                                                    <td><?= number_format($credit->remaining_amount, 2, ',', '.') ?> TL</td>
                                                    <td><?= date('d.m.Y', strtotime($credit->due_date)) ?></td>
                                                    <td>
                                                        <?php if ($credit->last_payment_date): ?>
                                                            <?= date('d.m.Y H:i', strtotime($credit->last_payment_date)) ?>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($credit->status == 1): ?>
                                                            <span class="badge badge-primary">Aktif</span>
                                                        <?php elseif ($credit->status == 3): ?>
                                                            <span class="badge badge-info">Kısmi Ödeme</span>
                                                        <?php elseif ($credit->status == 4): ?>
                                                            <span class="badge badge-danger">Gecikti</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-success payment-btn" data-toggle="modal" data-target="#paymentModal" data-id="<?= $credit->id ?>" data-amount="<?= $credit->remaining_amount ?>" data-userid="<?= $user->id ?>"><i class="fas fa-money-bill-wave"></i> Ödeme</button>
                                                    </td>
                                                </tr>
                                        <?php 
                                                }
                                            }
                                        }
                                        if (!$active_credits_exist) {
                                        ?>
                                            <tr>
                                                <td colspan="9" class="text-center">Aktif kredi bulunmamaktadır.</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-history"></i> Kredi Geçmişi
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="creditHistoryTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tutar</th>
                                            <th>İşlem Ücreti Oranı</th>
                                            <th>Oluşturulma Tarihi</th>
                                            <th>Son Ödeme Tarihi</th>
                                            <th>Ödenme Tarihi</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $history_exists = false;
                                        if (!empty($credits)) {
                                            foreach ($credits as $credit) {
                                                $history_exists = true;
                                        ?>
                                                <tr>
                                                    <td><?= $credit->id ?></td>
                                                    <td><?= number_format($credit->amount, 2, ',', '.') ?> TL</td>
                                                    <td><?= number_format($credit->fee_percentage, 2, ',', '.') ?>%</td>
                                                    <td><?= date('d.m.Y H:i', strtotime($credit->created_at)) ?></td>
                                                    <td><?= date('d.m.Y', strtotime($credit->due_date)) ?></td>
                                                    <td>
                                                        <?php if ($credit->status == 2): // Ödendi ?>
                                                            <?= date('d.m.Y H:i', strtotime($credit->last_payment_date)) ?>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($credit->status == 1): ?>
                                                            <span class="badge badge-primary">Aktif</span>
                                                        <?php elseif ($credit->status == 2): ?>
                                                            <span class="badge badge-success">Ödendi</span>
                                                        <?php elseif ($credit->status == 3): ?>
                                                            <span class="badge badge-info">Kısmi Ödeme</span>
                                                        <?php elseif ($credit->status == 4): ?>
                                                            <span class="badge badge-danger">Gecikti</span>
                                                        <?php elseif ($credit->status == 5): ?>
                                                            <span class="badge badge-warning">Hesap Askıda</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                        <?php 
                                            }
                                        }
                                        if (!$history_exists) {
                                        ?>
                                            <tr>
                                                <td colspan="8" class="text-center">Kredi geçmişi bulunmamaktadır.</td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-money-check"></i> Ödeme Geçmişi
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="paymentHistoryTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Kredi ID</th>
                                            <th>Kredi Tutarı</th>
                                            <th>Kalan Tutar</th>
                                            <th>Ödeme Sayısı</th>
                                            <th>Son Ödeme Tarihi</th>
                                            <th>Durum</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        if (!empty($credits)) {
                                            foreach ($credits as $credit) {
                                                // Her krediye ait ödeme sayısını hesapla
                                                $payment_count = 0;
                                                $credit_payments = [];
                                                
                                                if (!empty($payments)) {
                                                    foreach ($payments as $payment) {
                                                        if ($payment->credit_id == $credit->id) {
                                                            $payment_count++;
                                                            $credit_payments[] = $payment;
                                                        }
                                                    }
                                                }
                                        ?>
                                                <tr>
                                                    <td><?= $credit->id ?></td>
                                                    <td><?= number_format($credit->amount, 2, ',', '.') ?> TL</td>
                                                    <td><?= number_format($credit->remaining_amount, 2, ',', '.') ?> TL</td>
                                                    <td><?= $payment_count ?></td>
                                                    <td>
                                                        <?php if ($credit->last_payment_date): ?>
                                                            <?= date('d.m.Y H:i', strtotime($credit->last_payment_date)) ?>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Henüz Ödeme Yok</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($credit->status == 1): ?>
                                                            <span class="badge badge-primary">Aktif</span>
                                                        <?php elseif ($credit->status == 2): ?>
                                                            <span class="badge badge-success">Ödendi</span>
                                                        <?php elseif ($credit->status == 3): ?>
                                                            <span class="badge badge-info">Kısmi Ödeme</span>
                                                        <?php elseif ($credit->status == 4): ?>
                                                            <span class="badge badge-danger">Gecikti</span>
                                                        <?php elseif ($credit->status == 5): ?>
                                                            <span class="badge badge-warning">Hesap Askıda</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-info payment-history-btn" 
                                                            data-toggle="modal" 
                                                            data-target="#creditPaymentHistoryModal" 
                                                            data-credit-id="<?= $credit->id ?>"
                                                            data-credit-amount="<?= number_format($credit->amount, 2, ',', '.') ?>"
                                                            data-credit-remaining="<?= number_format($credit->remaining_amount, 2, ',', '.') ?>"
                                                            data-payments='<?= json_encode($credit_payments) ?>'>
                                                            <i class="fas fa-history"></i> Ödeme Geçmişi
                                                        </button>
                                                    </td>
                                                </tr>
                                        <?php 
                                            }
                                        } else {
                                        ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Kredi kaydı bulunmamaktadır.</td>
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

<!-- Ödeme Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Kredi Ödemesi Ekle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('admin/credit_management/add_payment') ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="credit_id" id="credit_id">
                    <input type="hidden" name="user_id" id="user_id">
                    
                    <div class="form-group">
                        <label for="payment_amount">Ödeme Tutarı (TL)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="payment_amount" name="amount" required>
                        <small class="text-muted">Kalan Borç: <span id="remaining_amount"></span> TL</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Ödeme Yöntemi</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="balance">Kullanıcı Bakiyesinden Düş</option>
                            <option value="bank_transfer">Banka Transferi</option>
                            <option value="other">Diğer</option>
                        </select>
                    </div>
                    
                    <div id="balance_info" class="form-group">
                        <div class="alert alert-info">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-wallet mr-2"></i> Mevcut Bakiye:</span>
                                <strong><?= number_format($user->balance, 2, ',', '.') ?> TL</strong>
                            </div>
                        </div>
                        <?php if ($user->balance <= 0): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i> Kullanıcının yeterli bakiyesi bulunmamaktadır.
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_type">Ödeme Tipi</label>
                        <select class="form-control" id="payment_type" name="payment_type" required>
                            <option value="partial">Kısmi Ödeme</option>
                            <option value="full">Tam Ödeme</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_final_payment" name="is_final_payment" value="1">
                            <label class="custom-control-label" for="is_final_payment">Son Ödeme</label>
                        </div>
                        <small class="text-muted">Bu ödemenin son ödeme olduğunu ve krediyi kapatacağını belirtir.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Ödeme Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Kredi Ödeme Geçmişi Modal -->
<div class="modal fade" id="creditPaymentHistoryModal" tabindex="-1" role="dialog" aria-labelledby="creditPaymentHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditPaymentHistoryModalLabel">Kredi Ödeme Geçmişi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="credit-info mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Kredi ID:</strong> <span id="modal-credit-id"></span>
                            </div>
                            <div class="info-item">
                                <strong>Kredi Tutarı:</strong> <span id="modal-credit-amount"></span> TL
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <strong>Kalan Tutar:</strong> <span id="modal-credit-remaining"></span> TL
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="creditPaymentsTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tutar</th>
                                <th>Ödeme Yöntemi</th>
                                <th>Ödeme Tipi</th>
                                <th>Son Ödeme</th>
                                <th>Tarih</th>
                                <th>Durum</th>
                            </tr>
                        </thead>
                        <tbody id="payment-history-body">
                            <!-- Ödemeler JavaScript ile doldurulacak -->
                        </tbody>
                    </table>
                </div>
                
                <div id="no-payments-message" class="alert alert-info mt-3" style="display: none;">
                    <i class="fas fa-info-circle mr-2"></i> Bu krediye ait ödeme kaydı bulunmamaktadır.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<!-- Ödeme Detay Modal -->
<div class="modal fade" id="paymentDetailModal" tabindex="-1" role="dialog" aria-labelledby="paymentDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentDetailModalLabel">Ödeme Detayları</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="payment-details">
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="detail-item">
                                <span class="detail-label">Ödeme ID:</span>
                                <span class="detail-value" id="detail-id"></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="detail-item">
                                <span class="detail-label">Kredi ID:</span>
                                <span class="detail-value" id="detail-credit-id"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="detail-item">
                                <span class="detail-label">Ödeme Tutarı:</span>
                                <span class="detail-value" id="detail-amount"></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="detail-item">
                                <span class="detail-label">Ödeme Tarihi:</span>
                                <span class="detail-value" id="detail-date"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="detail-item">
                                <span class="detail-label">Ödeme Yöntemi:</span>
                                <span class="detail-value" id="detail-method"></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="detail-item">
                                <span class="detail-label">Ödeme Tipi:</span>
                                <span class="detail-value" id="detail-type"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="detail-item">
                                <span class="detail-label">Son Ödeme:</span>
                                <span class="detail-value" id="detail-is-final"></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="detail-item">
                                <span class="detail-label">Durum:</span>
                                <span class="detail-value" id="detail-status"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle mr-2"></i> Bu ödeme detayları, kredi işlem kaydından otomatik olarak oluşturulmuştur.
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
        // En basit datatable yapılandırması ile başlat - hata azaltmak için
        var basicConfig = {
            "paginate": true,
            "ordering": true,
            "search": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json"
            }
        };
        
        // Tabloları teker teker ve hata kontrolüyle başlat
        initDataTable('#activeCreditsTable', basicConfig);
        initDataTable('#creditHistoryTable', basicConfig);
        initDataTable('#paymentHistoryTable', basicConfig);
        
        // DataTable başlatma fonksiyonu
        function initDataTable(selector, config) {
            try {
                // Tablo var mı kontrol et
                if ($(selector).length === 0) {
                    console.error("Tablo bulunamadı:", selector);
                    return;
                }
                
                // Tablo yapısını kontrol et ve düzelt
                validateTableStructure(selector);
                
                // DataTable başlat
                setTimeout(function() {
                    if ($.fn.DataTable.isDataTable(selector)) {
                        $(selector).DataTable().destroy();
                    }
                    $(selector).DataTable(config);
                    console.log("DataTable başarıyla başlatıldı:", selector);
                }, 100);
            } catch(e) {
                console.error("DataTable başlatma hatası (" + selector + "):", e);
            }
        }
        
        // Tablo yapısını doğrula ve düzelt
        function validateTableStructure(selector) {
            var $table = $(selector);
            
            // thead yoksa ekle
            if ($table.find('thead').length === 0) {
                console.warn("Tabloda thead eksik, ekleniyor:", selector);
                $table.prepend('<thead><tr></tr></thead>');
            }
            
            // tbody yoksa ekle
            if ($table.find('tbody').length === 0) {
                console.warn("Tabloda tbody eksik, ekleniyor:", selector);
                $table.append('<tbody></tbody>');
            }
            
            // Sütun sayılarını kontrol et
            var headerColCount = $table.find('thead tr:first th').length;
            var bodyRows = $table.find('tbody tr');
            
            bodyRows.each(function(i, row) {
                var cellCount = $(row).find('td').length;
                if (cellCount !== headerColCount && headerColCount > 0 && cellCount > 0) {
                    console.warn("Sütun sayısı uyuşmuyor. Başlık: " + headerColCount + ", Satır " + i + ": " + cellCount);
                    
                    // Eksik hücreler ekle (son sütuna)
                    if (cellCount < headerColCount) {
                        var diff = headerColCount - cellCount;
                        for (var j = 0; j < diff; j++) {
                            $(row).append('<td></td>');
                        }
                    }
                }
            });
        }
        
        // Modal içi DataTable başlatma
        function initModalDataTable() {
            try {
                validateTableStructure('#creditPaymentsTable');
                
                if ($.fn.DataTable.isDataTable('#creditPaymentsTable')) {
                    $('#creditPaymentsTable').DataTable().destroy();
                }
                
                $('#creditPaymentsTable').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json"
                    },
                    "paginate": true,
                    "ordering": true,
                    "search": true,
                    "pageLength": 5
                });
            } catch(e) {
                console.error("Modal DataTable başlatma hatası:", e);
            }
        }
        
        // Ödeme modalı açıldığında verileri doldur
        $('.payment-btn').click(function() {
            var creditId = $(this).data('id');
            var userId = $(this).data('userid');
            var remainingAmount = $(this).data('amount');
            var userBalance = <?= $user->balance ?>;
            
            $('#credit_id').val(creditId);
            $('#user_id').val(userId);
            $('#remaining_amount').text(remainingAmount.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $('#payment_amount').val(remainingAmount).attr('max', remainingAmount);
            
            // İlk yüklendiğinde bakiye kontrolü yap
            checkBalanceSufficiency(remainingAmount, userBalance);
            
            // Ödeme yöntemi değiştiğinde kontrol et
            $('#payment_method').change(function() {
                if ($(this).val() === 'balance') {
                    $('#balance_info').show();
                    checkBalanceSufficiency(parseFloat($('#payment_amount').val()), userBalance);
                } else {
                    $('#balance_info').hide();
                    $('.modal-footer button[type="submit"]').prop('disabled', false);
                }
            });
            
            // Tutar değiştiğinde bakiye kontrolü yap
            $('#payment_amount').on('input', function() {
                var enteredAmount = parseFloat($(this).val());
                
                if ($('#payment_method').val() === 'balance') {
                    checkBalanceSufficiency(enteredAmount, userBalance);
                }
                
                if (enteredAmount === remainingAmount) {
                    $('#payment_type').val('full');
                    $('#is_final_payment').prop('checked', true);
                } else {
                    $('#payment_type').val('partial');
                    $('#is_final_payment').prop('checked', false);
                }
            });
            
            // Tam ödeme seçilirse ödeme tutarını otomatik olarak kalan tutar yap
            $('#payment_type').change(function() {
                if ($(this).val() === 'full') {
                    $('#payment_amount').val(remainingAmount);
                    $('#is_final_payment').prop('checked', true);
                    
                    if ($('#payment_method').val() === 'balance') {
                        checkBalanceSufficiency(remainingAmount, userBalance);
                    }
                }
            });
        });
        
        // Bakiye yeterlilik kontrolü
        function checkBalanceSufficiency(paymentAmount, userBalance) {
            if (paymentAmount > userBalance) {
                $('#balance_info').append('<div class="alert alert-danger mt-2" id="insufficient_balance"><i class="fas fa-exclamation-circle mr-2"></i> Ödeme tutarı, kullanıcı bakiyesinden büyük olamaz.</div>');
                $('.modal-footer button[type="submit"]').prop('disabled', true);
            } else {
                $('#insufficient_balance').remove();
                $('.modal-footer button[type="submit"]').prop('disabled', false);
            }
        }
        
        // Kredi Ödeme Geçmişi modalını açma
        $('.payment-history-btn').click(function() {
            var creditId = $(this).data('credit-id');
            var creditAmount = $(this).data('credit-amount');
            var creditRemaining = $(this).data('credit-remaining');
            var payments = $(this).data('payments');
            
            // Modal içeriğini doldur
            $('#modal-credit-id').text(creditId);
            $('#modal-credit-amount').text(creditAmount);
            $('#modal-credit-remaining').text(creditRemaining);
            
            // Ödemeleri tabloya doldur
            var paymentHistoryHtml = '';
            if (payments && payments.length > 0) {
                $('#no-payments-message').hide();
                
                for (var i = 0; i < payments.length; i++) {
                    var payment = payments[i];
                    var methodBadge = '';
                    var typeBadge = '';
                    var isFinalBadge = '';
                    var statusBadge = '';
                    
                    // Ödeme yöntemi badge'i
                    if (payment.payment_method === 'balance') {
                        methodBadge = '<span class="badge badge-primary">Bakiye</span>';
                    } else if (payment.payment_method === 'bank_transfer') {
                        methodBadge = '<span class="badge badge-info">Banka Transferi</span>';
                    } else {
                        methodBadge = '<span class="badge badge-secondary">Diğer</span>';
                    }
                    
                    // Ödeme tipi badge'i
                    if (payment.payment_type === 'partial') {
                        typeBadge = '<span class="badge badge-info">Kısmi Ödeme</span>';
                    } else if (payment.payment_type === 'full') {
                        typeBadge = '<span class="badge badge-success">Tam Ödeme</span>';
                    }
                    
                    // Son ödeme badge'i
                    isFinalBadge = payment.is_final_payment == 1 ? 
                        '<span class="badge badge-success">Evet</span>' : 
                        '<span class="badge badge-secondary">Hayır</span>';
                    
                    // Durum badge'i
                    statusBadge = payment.status == 1 ? 
                        '<span class="badge badge-success">Onaylandı</span>' : 
                        '<span class="badge badge-danger">İptal Edildi</span>';
                    
                    // Ödeme tarihi
                    var paymentDate = new Date(payment.created_at);
                    var formattedDate = paymentDate.toLocaleDateString('tr-TR') + ' ' + 
                                        paymentDate.toLocaleTimeString('tr-TR', {hour: '2-digit', minute:'2-digit'});
                    
                    // Ödeme tutarı
                    var formattedAmount = parseFloat(payment.amount).toLocaleString('tr-TR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    
                    paymentHistoryHtml += '<tr>' +
                        '<td>' + payment.id + '</td>' +
                        '<td>' + formattedAmount + ' TL</td>' +
                        '<td>' + methodBadge + '</td>' +
                        '<td>' + typeBadge + '</td>' +
                        '<td>' + isFinalBadge + '</td>' +
                        '<td>' + formattedDate + '</td>' +
                        '<td>' + statusBadge + '</td>' +
                    '</tr>';
                }
                
                $('#payment-history-body').html(paymentHistoryHtml);
                
                // Modal içindeki tabloyu başlat
                setTimeout(function() {
                    initModalDataTable();
                }, 300);
            } else {
                $('#payment-history-body').html('');
                $('#no-payments-message').show();
            }
        });
        
        // Modal kapandığında DataTable'ı temizle
        $('#creditPaymentHistoryModal').on('hidden.bs.modal', function () {
            try {
                if ($.fn.DataTable.isDataTable('#creditPaymentsTable')) {
                    $('#creditPaymentsTable').DataTable().destroy();
                }
                $('#payment-history-body').html('');
            } catch(e) {
                console.error("Modal DataTable temizleme hatası:", e);
            }
        });
        
        // Ödeme detay modalını açma
        $('.payment-detail-btn').click(function() {
            // Data attribute'larından verileri al
            var id = $(this).data('id');
            var creditId = $(this).data('credit-id');
            var amount = $(this).data('amount');
            var method = $(this).data('method');
            var type = $(this).data('type');
            var isFinal = $(this).data('is-final');
            var date = $(this).data('date');
            var status = $(this).data('status');
            
            // Modal içeriğini doldur
            $('#detail-id').text(id);
            $('#detail-credit-id').text(creditId);
            $('#detail-amount').text(amount + ' TL');
            $('#detail-date').text(date);
            
            // Ödeme yöntemi formatı
            var methodText = "";
            if (method === 'balance') {
                methodText = '<span class="badge badge-primary">Bakiye</span>';
            } else if (method === 'bank_transfer') {
                methodText = '<span class="badge badge-info">Banka Transferi</span>';
            } else {
                methodText = '<span class="badge badge-secondary">Diğer</span>';
            }
            $('#detail-method').html(methodText);
            
            // Ödeme tipi formatı
            var typeText = "";
            if (type === 'partial') {
                typeText = '<span class="badge badge-info">Kısmi Ödeme</span>';
            } else if (type === 'full') {
                typeText = '<span class="badge badge-success">Tam Ödeme</span>';
            }
            $('#detail-type').html(typeText);
            
            // Son ödeme formatı
            var isFinalText = isFinal ? '<span class="badge badge-success">Evet</span>' : '<span class="badge badge-secondary">Hayır</span>';
            $('#detail-is-final').html(isFinalText);
            
            // Durum formatı
            var statusText = status == 1 ? '<span class="badge badge-success">Onaylandı</span>' : '<span class="badge badge-danger">İptal Edildi</span>';
            $('#detail-status').html(statusText);
        });
    });
</script>

<!-- CSS kodu custom.css'e taşındı -->

<!-- Hata Gizleme -->
<script>
    // Cloudflare Insights CORS hatalarını gizle (DataTable ile ilgisi yok)
    window.addEventListener('error', function(e) {
        if (e.target && (e.target.src || e.target.href)) {
            var url = e.target.src || e.target.href;
            if (url.includes('cloudflareinsights.com')) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Cloudflare Insights hatası gizlendi');
                return true;
            }
        }
    }, true);
</script> 