<?php 
// PHP tarafında Türkçe karakterleri İngilizce karakterlere dönüştüren fonksiyon
function tr_to_en($text) {
    $search = array('ç','Ç','ğ','Ğ','ı','İ','ö','Ö','ş','Ş','ü','Ü');
    $replace = array('c','C','g','G','i','I','o','O','s','S','u','U');
    return strtoupper(str_replace($search, $replace, $text));
}
?>

            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <div class="page-title">
                            <h5 class="mb-0">Satış Geçmişi</h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Ana Sayfa</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Satış Geçmişi</li>
                            </ol>
                        </nav>

                        <div class="row g-4">
                            <!-- Verilemeyen Ürünler -->
                            <div class="col-lg-8 col-md-12">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-white">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start">
                                            <div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="sh-header-icon sh-warning mr-2">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                    </div>
                                                    <h6 class="mb-0">Verilemeyen Ürünler</h6>
                                                </div>
                                                <small class="text-muted d-block">Bonus alan abonelerin ürünleri bonus tutarı düşülerek iade edilecektir.</small>
                                            </div>
                                            <div class="d-flex flex-column flex-sm-row mt-3 mt-md-0">
                                                <a href="<?= base_url('admin/stock') ?>" class="btn btn-outline-secondary btn-sm mr-0 mr-sm-2 mb-2 mb-sm-0">
                                                    <i class="fas fa-box mr-1"></i> Stok Kontrolü
                                                </a>
                                                <a href="<?= base_url('admin/product/confirmAllPendingTransfer') ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-check mr-1"></i> Tümünü Gönder
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <?php if (!empty($pending)): ?>
                                            <div class="sh-pending-list">
                                                <?php foreach ($pending as $p): ?>
                                                <div class="sh-pending-item">
                                                    <div class="d-flex flex-column flex-md-row align-items-start">
                                                        <div class="sh-avatar-sm bg-light rounded-circle mb-3 mb-md-0 mr-md-3 d-flex align-items-center justify-content-center">
                                                            <span class="text-dark"><?= mb_substr(tr_to_en($p->user_name), 0, 1, 'UTF-8') ?></span>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start">
                                                                <div class="w-100 w-md-auto">
                                                                    <div class="d-flex flex-wrap align-items-center mb-1">
                                                                        <h6 class="mb-0 mr-2">
                                                                            <a href="<?= base_url('admin/product/userShopHistory/') . $p->user_id ?>" class="text-primary">
                                                                                <?= $p->user_name . " " . $p->user_surname ?>
                                                                            </a>
                                                                        </h6>
                                                                        <?php
                                                                            // Alışveriş sayısını hesapla
                                                                            $order_count = $this->db->where('user_id', $p->user_id)
                                                                                                    ->where('status', 0)
                                                                                                    ->count_all_results('shop');
                                                                        ?>
                                                                        <span class="sh-badge sh-badge-soft-primary"><?= $order_count ?>. Alışveriş</span>
                                                                    </div>
                                                                    <div class="sh-product-info mb-2">
                                                                        <span class="text-muted text-truncate d-inline-block w-100"><?= $p->product_name ?></span>
                                                                    </div>
                                                                    <div class="d-flex flex-wrap align-items-center text-muted small">
                                                                        <span class="mr-3 mb-2">
                                                                            <i class="far fa-calendar-alt mr-1"></i>
                                                                            <?= date('d.m.Y H:i', strtotime($p->date)) ?>
                                                                        </span>
                                                                        <span class="mr-3 mb-2">
                                                                            <i class="far fa-money-bill-alt mr-1"></i>
                                                                            <?= number_format($p->price, 2) ?>₺
                                                                        </span>
                                                                        <?php
                                                                            $waiting_time = time() - strtotime($p->date);
                                                                            $waiting_hours = floor($waiting_time / 3600);
                                                                            $waiting_minutes = floor(($waiting_time % 3600) / 60);
                                                                            
                                                                            $badge_class = $waiting_hours >= 24 ? 'sh-badge-soft-danger' : 
                                                                                        ($waiting_hours >= 12 ? 'sh-badge-soft-warning' : 
                                                                                        'sh-badge-soft-success');
                                                                        ?>
                                                                        <span class="sh-badge <?= $badge_class ?> mb-2 sh-waiting-badge">
                                                                            <i class="far fa-clock mr-1"></i>
                                                                            <?= $waiting_hours ?> saat <?= $waiting_minutes ?> dakika bekliyor
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex flex-column align-items-start align-items-md-end mt-3 mt-md-0 sh-action-buttons">
                                                                    <div class="d-flex flex-wrap mb-2">
                                                                        <a href="<?= base_url('admin/product/deletePendingTransfer/') . $p->id ?>" class="btn sh-btn-soft-danger btn-sm mr-2 mb-2 mb-md-0">
                                                                            <i class="fas fa-times mr-1"></i> İptal
                                                                        </a>
                                                                        <?php if (!$p->api_pending): ?>
                                                                        <a href="<?= base_url('admin/product/confirmPendingTransfer/') . $p->id ?>" class="btn sh-btn-soft-success btn-sm">
                                                                            <i class="fas fa-check mr-1"></i> Gönder
                                                                        </a>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <a href="#" class="btn btn-outline-secondary btn-sm w-100 sh-manual-stock-btn" data-id="<?= $p->id ?>">
                                                                        <i class="fas fa-keyboard mr-1"></i> Manuel Stok Gönder
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-4">
                                                <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                                <p class="mb-0">Verilemeyen ürün bulunmuyor.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Geri Dönüşü Olan Ürünler -->
                            <div class="col-lg-4 col-md-12">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-white">
                                        <div class="d-flex align-items-center">
                                            <div class="sh-header-icon sh-info mr-2">
                                                <i class="fas fa-undo"></i>
                                            </div>
                                            <h6 class="mb-0">Geri Dönüşü Olan Ürünler</h6>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <?php if (!empty($invoices)): ?>
                                            <div class="sh-return-list">
                                                <?php foreach ($invoices as $invoice): ?>
                                                <div class="sh-return-item">
                                                    <div class="d-flex flex-column flex-md-row">
                                                        <div class="sh-avatar-sm bg-light rounded-circle mb-3 mb-md-0 mr-md-3 d-flex align-items-center justify-content-center">
                                                            <span class="text-dark"><?= mb_substr(tr_to_en($invoice->user_name), 0, 1, 'UTF-8') ?></span>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center mb-2">
                                                                <h6 class="mb-0">
                                                                    <a href="<?= base_url('admin/product/userShopHistory/') . $invoice->user_id ?>" class="text-primary">
                                                                        <?= $invoice->user_name . " " . $invoice->user_surname ?>
                                                                    </a>
                                                                </h6>
                                                            </div>
                                                            <div class="sh-product-info mb-3">
                                                                <span class="text-muted text-truncate d-inline-block w-100"><?= $invoice->product_name ?></span>
                                                                <div class="d-flex flex-wrap align-items-center mt-1">
                                                                    <span class="text-muted small mr-3 mb-2">
                                                                        <i class="far fa-calendar-alt mr-1"></i>
                                                                        <?= date('d.m.Y H:i', strtotime($invoice->date)) ?>
                                                                    </span>
                                                                    <span class="text-muted small mb-2">
                                                                        <i class="far fa-money-bill-alt mr-1"></i>
                                                                        <?= number_format($invoice->price, 2) ?>₺
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <?php if (!empty($invoice->extras)): 
                                                                $extras = json_decode($invoice->extras, true);
                                                                if ($extras):
                                                            ?>
                                                            <div class="sh-return-details p-3 bg-light rounded mb-3">
                                                                <?php foreach ($extras as $key => $value): ?>
                                                                <div class="d-flex justify-content-between align-items-center mb-2 last-child-no-margin">
                                                                    <span class="text-muted small"><?= $key ?></span>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="sh-code-wrapper d-flex align-items-center bg-white px-2 py-1 rounded">
                                                                            <code class="mr-2"><?= $value ?></code>
                                                                            <button type="button" class="sh-copy-btn" data-clipboard-text="<?= $value ?>">
                                                                                <i class="fas fa-copy"></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                            <?php 
                                                                endif;
                                                            endif; 
                                                            ?>
                                                            <div class="d-flex flex-wrap justify-content-end">
                                                                <a href="<?= base_url('admin/product/cancelGiveProduct/') . $invoice->id ?>" class="btn sh-btn-soft-danger btn-sm mr-2 mb-2 mb-md-0">
                                                                    <i class="fas fa-times mr-1"></i> İptal
                                                                </a>
                                                                <a href="<?= base_url('admin/product/confirmGiveProduct/') . $invoice->id ?>" class="btn sh-btn-soft-success btn-sm">
                                                                    <i class="fas fa-check mr-1"></i> Onayla
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-4">
                                                <i class="fas fa-check-circle text-success fa-2x mb-2"></i>
                                                <p class="mb-0">Geri dönüşü olan ürün bulunmuyor.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Tüm Satışlar -->
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-white">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="sh-header-icon sh-primary mr-2">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </div>
                                                <h6 class="mb-0">Tüm Satışlar</h6>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="text-muted mr-2">Filtrele:</span>
                                                <select id="dateFilterSelect" class="form-control form-control-sm mr-2" style="width: auto;">
                                                    <option value="today">Bugün</option>
                                                    <option value="week">Son 1 Hafta</option>
                                                    <option value="month" selected>Son 1 Ay</option>
                                                    <option value="year">Son 1 Yıl</option>
                                                    <option value="custom">Özel Tarih</option>
                                                </select>
                                                <div id="customDateInputs" class="d-none d-flex align-items-center">
                                                    <input type="date" id="startDate" class="form-control form-control-sm mr-2">
                                                    <input type="date" id="endDate" class="form-control form-control-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3 d-flex justify-content-end">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-primary btn-sm active mr-2" data-filter="all">
                                                    <i class="fas fa-list mr-1"></i> Tümü
                                                </button>
                                                <button type="button" class="btn btn-light btn-sm mr-2" data-filter="product">
                                                    <i class="fas fa-shopping-cart mr-1"></i> Ürün Satışları
                                                </button>
                                                <button type="button" class="btn btn-light btn-sm" data-filter="deposit">
                                                    <i class="fas fa-wallet mr-1"></i> Bakiye Yüklemeleri
                                                </button>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table id="salesTable" class="table table-hover table-bordered" width="100%">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>Sipariş No</th>
                                                        <th>Müşteri</th>
                                                        <th>İşlem</th>
                                                        <th>Alım Şekli</th>
                                                        <th>Tutar</th>
                                                        <th>Tarih</th>
                                                        <th>Durum</th>
                                                        <th>İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- DataTables tarafından doldurulacak -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                <!-- İşlem Detayları Modal -->
                <div class="modal fade" id="transactionModal" tabindex="-1" role="dialog" aria-labelledby="transactionModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="transactionModalLabel">İşlem Detayları</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- İşlem detayları burada yüklenecek -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                $(document).ready(function() {
                    // Tarih değişkenlerini tanımlama
                    let startDate = '';
                    let endDate = '';
                    let currentFilter = 'all';
                    let searchTerm = '';
                    
                    // Tarih aralığını başlangıçta ayarla (son 1 ay)
                    function initializeDateRange() {
                        const today = new Date();
                        const monthAgo = new Date(today);
                        monthAgo.setMonth(today.getMonth() - 1);
                        
                        startDate = monthAgo.toISOString().split('T')[0];
                        endDate = today.toISOString().split('T')[0];
                        
                        // Özel tarih seçildiğinde inputları doldur
                        $('#startDate').val(startDate);
                        $('#endDate').val(endDate);
                    }
                    
                    // Tarih aralığını güncelleme fonksiyonu
                    function updateDateRange() {
                        const selectedValue = $('#dateFilterSelect').val();
                        const today = new Date();
                        
                        switch(selectedValue) {
                            case 'today':
                                startDate = today.toISOString().split('T')[0];
                                endDate = today.toISOString().split('T')[0];
                                $('#customDateInputs').addClass('d-none');
                                break;
                            case 'week':
                                const weekAgo = new Date(today);
                                weekAgo.setDate(today.getDate() - 7);
                                startDate = weekAgo.toISOString().split('T')[0];
                                endDate = today.toISOString().split('T')[0];
                                $('#customDateInputs').addClass('d-none');
                                break;
                            case 'month':
                                const monthAgo = new Date(today);
                                monthAgo.setMonth(today.getMonth() - 1);
                                startDate = monthAgo.toISOString().split('T')[0];
                                endDate = today.toISOString().split('T')[0];
                                $('#customDateInputs').addClass('d-none');
                                break;
                            case 'year':
                                const yearAgo = new Date(today);
                                yearAgo.setFullYear(today.getFullYear() - 1);
                                startDate = yearAgo.toISOString().split('T')[0];
                                endDate = today.toISOString().split('T')[0];
                                $('#customDateInputs').addClass('d-none');
                                break;
                            case 'custom':
                                $('#customDateInputs').removeClass('d-none');
                                if ($('#startDate').val() && $('#endDate').val()) {
                                    startDate = $('#startDate').val();
                                    endDate = $('#endDate').val();
                                }
                                break;
                        }
                        
                        // DataTable'ı yeniden yükle
                        salesTable.ajax.reload();
                    }
                    
                    // Tarih değişkenlerini başlangıçta ayarla
                    initializeDateRange();
                    
                    // DataTable tanımlama
                    var salesTable = $('#salesTable').DataTable({
                        "processing": true,
                        "serverSide": true,
                        "ajax": {
                            "url": "<?= base_url('admin/API/getSalesData') ?>",
                            "type": "POST",
                            "data": function(d) {
                                // DataTables'ın kendi parametrelerini koruyun
                                return {
                                    // Standart DataTables parametreleri
                                    draw: d.draw,
                                    start: d.start,
                                    length: d.length,
                                    'order[0][column]': d.order[0].column,
                                    'order[0][dir]': d.order[0].dir,
                                    'search[value]': d.search.value,
                                    
                                    // Özel parametrelerimiz
                                    filter: currentFilter,
                                    startDate: startDate,
                                    endDate: endDate
                                };
                            }
                        },
                        "columns": [
                            { "data": "id" },
                            { 
                                "data": null,
                                "render": function(data, type, row) {
                                    if (row.customer === 'Misafir') {
                                        return 'Misafir';
                                    } else {
                                        return '<a href="<?= base_url('admin/product/userShopHistory/') ?>' + row.user_id + '" class="text-primary">' + row.customer + '</a>';
                                    }
                                }
                            },
                            { "data": "type_text" },
                            { "data": "payment_method" },
                            { 
                                "data": "amount",
                                "render": function(data, type, row) {
                                    return parseFloat(data).toFixed(2) + '₺';
                                }
                            },
                            { 
                                "data": "date",
                                "render": function(data, type, row) {
                                    const date = new Date(data);
                                    return date.toLocaleDateString('tr-TR', {
                                        day: '2-digit',
                                        month: '2-digit',
                                        year: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    });
                                }
                            },
                            { 
                                "data": "status_text",
                                "render": function(data, type, row) {
                                    let badgeClass = 'badge-secondary';
                                    
                                    switch(data) {
                                        case 'Tamamlandı':
                                            badgeClass = 'badge-success';
                                            break;
                                        case 'Beklemede':
                                            badgeClass = 'badge-warning';
                                            break;
                                        case 'İptal Edildi':
                                            badgeClass = 'badge-danger';
                                            break;
                                    }
                                    
                                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                                }
                            },
                            { 
                                "data": null,
                                "orderable": false,
                                "render": function(data, type, row) {
                                    return '<div class="action-buttons">' +
                                        '<button type="button" class="btn btn-soft-primary btn-sm view-details mr-1" data-id="' + row.id + '">' +
                                        '<i class="fas fa-eye"></i>' +
                                        '</button>' +
                                        '<a href="<?= base_url("admin/product/invoice/") ?>' + row.id + '" class="btn btn-soft-info btn-sm">' +
                                        '<i class="fas fa-file-invoice"></i>' +
                                        '</a>' +
                                        '</div>';
                                }
                            }
                        ],
                        "order": [[ 0, "desc" ]],
                        "pageLength": 25,
                        "language": {
                            "url": "<?= base_url('assets/plugins/datatables/Turkish.json') ?>"
                        },
                        "responsive": true,
                        "drawCallback": function() {
                            // Detay görüntüleme butonlarına olay dinleyicileri ekle
                            $('.view-details').off('click').on('click', function() {
                                const id = $(this).data('id');
                                
                                $.ajax({
                                    url: '<?= base_url("admin/product/getTransactionDetails/") ?>' + id,
                                    type: 'GET',
                                    success: function(response) {
                                        $('#transactionModal .modal-body').html(response);
                                        $('#transactionModal').modal('show');
                                    },
                                    error: function() {
                                        alert('İşlem detayları yüklenirken bir hata oluştu.');
                                    }
                                });
                            });
                        }
                    });
                    
                    // Debug bilgilerini ekleyelim
                    salesTable.on('xhr', function() {
                        var ajaxData = salesTable.ajax.params();
                        console.log('DataTables Ajax Parametreleri:', ajaxData);
                    });
                    
                    // Tarih filtresi değişim olayı
                    $('#dateFilterSelect').on('change', updateDateRange);
                    
                    // Özel tarih değişim olayları
                    $('#startDate, #endDate').on('change', function() {
                        if ($('#dateFilterSelect').val() === 'custom') {
                            startDate = $('#startDate').val();
                            endDate = $('#endDate').val();
                            salesTable.ajax.reload();
                        }
                    });
                    
                    // Filtre butonları olay dinleyicileri - YENİ SEÇİCİ
                    $('.btn-group .btn').on('click', function() {
                        $('.btn-group .btn').removeClass('active').addClass('btn-light');
                        $(this).addClass('active').removeClass('btn-light');
                        currentFilter = $(this).data('filter');
                        salesTable.ajax.reload();
                    });
                });
                </script>

                <!-- Modal işlevselliği -->
                <script>
                // Modal işlevselliği ve diğer yardımcı fonksiyonlar
                $(document).ready(function() {
                    // Manuel stok butonuna tıklandığında
                    $('.sh-manual-stock-btn').click(function(e) {
                        e.preventDefault();
                        const pendingId = $(this).data('id');
                        $('#pendingId').val(pendingId);
                        $('#manualStockForm').attr('action', '<?= base_url("admin/product/addManualStock/") ?>' + pendingId);
                        $('#manualStockModal').modal('show');
                    });
                    
                    // Kopyalama butonu işlevselliği
                    document.querySelectorAll('.sh-copy-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            const textToCopy = this.getAttribute('data-clipboard-text');
                            
                            navigator.clipboard.writeText(textToCopy).then(() => {
                                // Kopyalama başarılı olduğunda
                                this.classList.add('copied');
                                const icon = this.querySelector('i');
                                icon.classList.remove('fa-copy');
                                icon.classList.add('fa-check');
                                
                                // 1.5 saniye sonra eski haline döndür
                                setTimeout(() => {
                                    this.classList.remove('copied');
                                    icon.classList.remove('fa-check');
                                    icon.classList.add('fa-copy');
                                }, 1500);
                            }).catch(err => {
                                console.error('Kopyalama hatası:', err);
                            });
                        });
                    });
                    
                    // Modal kapatma işlevleri
                    $(document).on('click', '[data-dismiss="modal"]', function() {
                        $(this).closest('.modal').modal('hide');
                    });
                    
                    $(document).on('click', '.modal', function(e) {
                        if ($(e.target).hasClass('modal')) {
                            $(this).modal('hide');
                        }
                    });
                });
                </script>

                <!-- Manuel Stok Modal -->
                <div class="modal fade" id="manualStockModal" tabindex="-1" role="dialog" aria-labelledby="manualStockModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="manualStockModalLabel">Manuel Stok Gönder</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="" method="POST" id="manualStockForm">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="stockCode">Stok Kodu</label>
                                        <textarea class="form-control" id="stockCode" name="stock_code" rows="3" placeholder="Stok kodunu girin"></textarea>
                                    </div>
                                    <input type="hidden" name="pending_id" id="pendingId">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-primary">Gönder</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                // Türkçe karakterleri İngilizce karakterlere dönüştüren fonksiyon
                function tr_to_en(text) {
                    var trMap = {
                        'çÇ': 'c',
                        'ğĞ': 'g',
                        'şŞ': 's',
                        'üÜ': 'u',
                        'ıİ': 'i',
                        'öÖ': 'o'
                    };
                    
                    for (var key in trMap) {
                        text = text.replace(new RegExp('[' + key + ']', 'g'), trMap[key]);
                    }
                    
                    return text.toUpperCase();
                }
                </script>
