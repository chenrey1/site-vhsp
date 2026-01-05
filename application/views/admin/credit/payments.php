<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title d-flex justify-content-between align-items-center">
                <h5>Kredi Ödemeleri</h5>
                <a href="<?= base_url('admin/credit_management') ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-arrow-left"></i> Kredi Yönetimine Dön</a>
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
                            <i class="fas fa-money-check-alt"></i> Kredi Ödemeleri Listesi
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="paymentsTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Kullanıcı</th>
                                            <th>Kredi ID</th>
                                            <th>Tutar</th>
                                            <th>Kalan Tutar</th>
                                            <th>Ödeme Yöntemi</th>
                                            <th>Ödeme Tipi</th>
                                            <th>Son Ödeme</th>
                                            <th>Tarih</th>
                                            <th>Durum</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($payments)): ?>
                                            <?php foreach ($payments as $payment): ?>
                                                <tr>
                                                    <td><?= $payment->id ?></td>
                                                    <td><?= $payment->name . ' ' . $payment->surname ?><br><small><?= $payment->email ?></small></td>
                                                    <td><?= $payment->credit_id ?></td>
                                                    <td><?= number_format($payment->amount, 2, ',', '.') ?> TL</td>
                                                    <td><?= number_format($payment->remaining_amount, 2, ',', '.') ?> TL</td>
                                                    <td>
                                                        <?php if ($payment->payment_method == 'balance'): ?>
                                                            <span class="badge badge-primary">Bakiye</span>
                                                        <?php elseif ($payment->payment_method == 'bank_transfer'): ?>
                                                            <span class="badge badge-info">Banka Transferi</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Diğer</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($payment->payment_type == 'partial'): ?>
                                                            <span class="badge badge-info">Kısmi Ödeme</span>
                                                        <?php elseif ($payment->payment_type == 'full'): ?>
                                                            <span class="badge badge-success">Tam Ödeme</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($payment->is_final_payment): ?>
                                                            <span class="badge badge-success">Evet</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">Hayır</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= date('d.m.Y H:i', strtotime($payment->created_at)) ?></td>
                                                    <td>
                                                        <?php if ($payment->status == 1): ?>
                                                            <span class="badge badge-success">Onaylandı</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-danger">İptal Edildi</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('admin/credit_management/user_credits/' . $payment->user_id) ?>" class="btn btn-sm btn-info" title="Kullanıcı Kredileri"><i class="fas fa-user"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="11" class="text-center">Henüz kredi ödemesi bulunmamaktadır.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-pie"></i> Ödeme Yöntemleri Dağılımı
                        </div>
                        <div class="card-body">
                            <canvas id="paymentMethodChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar"></i> Ödeme Tipleri Dağılımı
                        </div>
                        <div class="card-body">
                            <canvas id="paymentTypeChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar"></i> Aylık Ödeme Miktarları
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyPaymentsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // DataTable Ayarları
        $('#paymentsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json"
            },
            "order": [[8, "desc"]] // Tarih'e göre sırala
        });
        
        // İptal modalı açıldığında veriyi doldur
        $('.cancel-btn').click(function() {
            var paymentId = $(this).data('id');
            $('#payment_id').val(paymentId);
        });
        
        // Ödeme yöntemleri grafiği
        var methodCtx = document.getElementById('paymentMethodChart').getContext('2d');
        var methodChart = new Chart(methodCtx, {
            type: 'pie',
            data: {
                labels: ['Bakiye', 'Banka Transferi', 'Diğer'],
                datasets: [{
                    data: [
                        <?php 
                            $balance_count = 0;
                            $bank_count = 0;
                            $other_count = 0;
                            
                            if (!empty($payments)) {
                                foreach ($payments as $payment) {
                                    if ($payment->payment_method == 'balance') {
                                        $balance_count++;
                                    } else if ($payment->payment_method == 'bank_transfer') {
                                        $bank_count++;
                                    } else {
                                        $other_count++;
                                    }
                                }
                            }
                            
                            echo $balance_count . ', ' . $bank_count . ', ' . $other_count;
                        ?>
                    ],
                    backgroundColor: ['#4e73df', '#1cc88a', '#6f42c1']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Ödeme tipleri grafiği
        var typeCtx = document.getElementById('paymentTypeChart').getContext('2d');
        var typeChart = new Chart(typeCtx, {
            type: 'pie',
            data: {
                labels: ['Kısmi Ödeme', 'Tam Ödeme'],
                datasets: [{
                    data: [
                        <?php 
                            $partial_count = 0;
                            $full_count = 0;
                            
                            if (!empty($payments)) {
                                foreach ($payments as $payment) {
                                    if ($payment->payment_type == 'partial') {
                                        $partial_count++;
                                    } else if ($payment->payment_type == 'full') {
                                        $full_count++;
                                    }
                                }
                            }
                            
                            echo $partial_count . ', ' . $full_count;
                        ?>
                    ],
                    backgroundColor: ['#36b9cc', '#1cc88a']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Aylık ödeme miktarları grafiği
        var monthlyCtx = document.getElementById('monthlyPaymentsChart').getContext('2d');
        var monthlyChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
                datasets: [{
                    label: 'Ödeme Miktarı (TL)',
                    data: [
                        <?php 
                            $monthly_totals = array_fill(0, 12, 0);
                            
                            if (!empty($payments)) {
                                foreach ($payments as $payment) {
                                    if ($payment->status == 1) { // Sadece onaylı ödemeleri dahil et
                                        $month = date('n', strtotime($payment->created_at)) - 1; // 0-11 arası ay indeksi
                                        $monthly_totals[$month] += $payment->amount;
                                    }
                                }
                            }
                            
                            echo implode(', ', $monthly_totals);
                        ?>
                    ],
                    backgroundColor: '#4e73df'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script> 