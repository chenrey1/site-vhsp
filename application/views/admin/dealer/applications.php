<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!-- CSS custom.css dosyasına taşındı -->
<div id="layoutSidenav_content">    
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Bayilik Başvuruları</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dealer') ?>">Bayilik Yönetimi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bayilik Başvuruları</li>
                </ol>
            </nav>

            <div class="page-btn">
                <div class="btns">
                    <?php if ($counts['pending'] > 0): ?>
                        <div class="alert alert-warning py-2 px-3 mb-0 d-inline-block">
                            <i class="fas fa-exclamation-circle mr-1"></i> <?= $counts['pending'] ?> bekleyen başvuru
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Filtre Kartları -->
            <div class="card mb-4 shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center">
                                <span class="bg-primary-soft p-2 rounded mr-2">
                                    <i class="fas fa-filter text-primary"></i>
                                </span>
                                <span class="font-weight-bold">Başvuruları Filtrele</span>
                            </div>
                        </div>
                        <?php if ($counts['pending'] > 0): ?>
                        <div>
                            <div class="bg-warning-soft text-warning px-3 py-1 rounded">
                                <i class="fas fa-exclamation-circle mr-1"></i> 
                                <span class="font-weight-bold"><?= $counts['pending'] ?></span> bekleyen başvuru
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= base_url('admin/dealer/applications') ?>" class="btn <?= empty($current_status) ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm mr-2 mb-2 px-3 rounded-pill">
                            <i class="fas fa-list-ul mr-1"></i> Tümü
                        </a>
                        <a href="<?= base_url('admin/dealer/applications/pending') ?>" class="btn <?= $current_status === 'pending' ? 'btn-warning' : 'btn-outline-warning' ?> btn-sm mr-2 mb-2 px-3 rounded-pill">
                            <i class="fas fa-clock mr-1"></i> Bekleyen
                        </a>
                        <a href="<?= base_url('admin/dealer/applications/approved') ?>" class="btn <?= $current_status === 'approved' ? 'btn-success' : 'btn-outline-success' ?> btn-sm mr-2 mb-2 px-3 rounded-pill">
                            <i class="fas fa-check-circle mr-1"></i> Onaylanan
                        </a>
                        <a href="<?= base_url('admin/dealer/applications/rejected') ?>" class="btn <?= $current_status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' ?> btn-sm mr-2 mb-2 px-3 rounded-pill">
                            <i class="fas fa-times-circle mr-1"></i> Reddedilen
                        </a>
                    </div>
                    
                    <!-- İstatistik Özeti -->
                    <div class="row mt-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-0 rounded-lg shadow-sm overflow-hidden">
                                <div class="card-body px-3 py-2 position-relative">
                                    <div class="text-right mb-1">
                                        <i class="fas fa-clipboard-list fa-lg text-primary"></i>
                                    </div>
                                    <h3 class="h3 font-weight-bold mb-0"><?= $counts['all'] ?></h3>
                                    <div class="text-uppercase font-weight-bold text-primary small">Toplam Başvuru</div>
                                    <div class="progress mt-2" style="height: 3px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-0 rounded-lg shadow-sm overflow-hidden">
                                <div class="card-body px-3 py-2 position-relative">
                                    <div class="text-right mb-1">
                                        <i class="fas fa-clock fa-lg text-warning"></i>
                                    </div>
                                    <h3 class="h3 font-weight-bold mb-0"><?= $counts['pending'] ?></h3>
                                    <div class="text-uppercase font-weight-bold text-warning small">Bekleyen</div>
                                    <div class="progress mt-2" style="height: 3px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?= ($counts['all'] > 0) ? ($counts['pending'] / $counts['all'] * 100) : 0 ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-0 rounded-lg shadow-sm overflow-hidden">
                                <div class="card-body px-3 py-2 position-relative">
                                    <div class="text-right mb-1">
                                        <i class="fas fa-check-circle fa-lg text-success"></i>
                                    </div>
                                    <h3 class="h3 font-weight-bold mb-0"><?= $counts['approved'] ?></h3>
                                    <div class="text-uppercase font-weight-bold text-success small">Onaylanan</div>
                                    <div class="progress mt-2" style="height: 3px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= ($counts['all'] > 0) ? ($counts['approved'] / $counts['all'] * 100) : 0 ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card border-0 rounded-lg shadow-sm overflow-hidden">
                                <div class="card-body px-3 py-2 position-relative">
                                    <div class="text-right mb-1">
                                        <i class="fas fa-times-circle fa-lg text-danger"></i>
                                    </div>
                                    <h3 class="h3 font-weight-bold mb-0"><?= $counts['rejected'] ?></h3>
                                    <div class="text-uppercase font-weight-bold text-danger small">Reddedilen</div>
                                    <div class="progress mt-2" style="height: 3px;">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?= ($counts['all'] > 0) ? ($counts['rejected'] / $counts['all'] * 100) : 0 ?>%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Başvuru Listesi -->
            <div class="card mb-4 shadow-sm border-0 rounded-lg">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center">
                                <span class="bg-primary-soft p-2 rounded mr-2">
                                    <i class="fas fa-clipboard-list text-primary"></i>
                                </span>
                                <span class="font-weight-bold">Bayilik Başvuru Listesi</span>
                                <?php if (!empty($current_status)) { ?>
                                    <?php 
                                    $status_text = '';
                                    $status_class = '';
                                    $bg_class = '';
                                    
                                    if ($current_status === 'pending') {
                                        $status_text = 'Bekleyen';
                                        $status_class = 'warning';
                                        $bg_class = 'bg-warning-soft';
                                    } else if ($current_status === 'approved') {
                                        $status_text = 'Onaylanan';
                                        $status_class = 'success';
                                        $bg_class = 'bg-success-soft';
                                    } else if ($current_status === 'rejected') {
                                        $status_text = 'Reddedilen';
                                        $status_class = 'danger';
                                        $bg_class = 'bg-danger-soft';
                                    }
                                    ?>
                                    <span class="<?= $bg_class ?> text-<?= $status_class ?> px-2 py-1 rounded ml-2"><?= $status_text ?></span>
                                <?php } ?>
                            </div>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3" id="refreshTable">
                                <i class="fas fa-sync-alt mr-1"></i> Yenile
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover" id="applicationsTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="60" class="rounded-left">ID</th>
                                    <th>Firma</th>
                                    <th>Müşteri</th>
                                    <th>Tahmini Ciro</th>
                                    <th width="100">Durum</th>
                                    <th width="150">Tarih</th>
                                    <th width="100" class="text-center rounded-right">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($applications)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle mr-2"></i> Henüz başvuru bulunmuyor.
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($applications as $application): ?>
                                        <tr>
                                            <td class="align-middle"><?= $application->id ?></td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" style="width: 36px; height: 36px; font-size: 16px;">
                                                        <?= strtoupper(substr($application->company_name, 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <strong><?= $application->company_name ?></strong>
                                                        <?php if (!empty($application->website)): ?>
                                                            <div>
                                                                <a href="<?= $application->website ?>" target="_blank" class="small text-primary">
                                                                    <i class="fas fa-external-link-alt mr-1"></i> <?= preg_replace('#^https?://#', '', $application->website) ?>
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mr-2" style="width: 32px; height: 32px; font-size: 12px;">
                                                        <?= strtoupper(substr($application->name, 0, 1) . substr($application->surname, 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <div><?= $application->name . ' ' . $application->surname ?></div>
                                                        <div class="small text-muted"><?= $application->email ?></div>
                                                        <div class="small text-muted"><?= $application->phone ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <?php if (!empty($application->estimated_revenue)): ?>
                                                    <span class="badge badge-success px-2 py-1">
                                                        <i class="fas fa-chart-line mr-1"></i> <?= number_format($application->estimated_revenue, 2, ',', '.') ?> TL
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary px-2 py-1">Belirtilmemiş</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php if ($application->status == 'pending'): ?>
                                                    <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> Bekleyen</span>
                                                <?php elseif ($application->status == 'approved'): ?>
                                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Onaylandı</span>
                                                <?php elseif ($application->status == 'rejected'): ?>
                                                    <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Reddedildi</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <div><?= date('d.m.Y', strtotime($application->created_at)) ?></div>
                                                <div class="small text-muted"><?= date('H:i', strtotime($application->created_at)) ?></div>
                                                <?php if (!empty($application->updated_at)): ?>
                                                    <div class="small text-muted mt-1">Güncellenme: <?= date('d.m.Y H:i', strtotime($application->updated_at)) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <?php if ($application->status == 'pending'): ?>
                                                        <button type="button" class="btn btn-success btn-sm" title="Onayla" data-toggle="modal" data-target="#approveModal<?= $application->id ?>">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        
                                                        <button type="button" class="btn btn-danger btn-sm" title="Reddet" data-toggle="modal" data-target="#rejectModal<?= $application->id ?>">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="badge <?= ($application->status == 'approved') ? 'badge-success' : 'badge-danger' ?> px-2 py-1">
                                                            <?= ($application->status == 'approved') ? 'Onaylandı' : 'Reddedildi' ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Modal Tanımları -->
                                        <?php if ($application->status == 'pending'): ?>
                                            <!-- Onaylama Modal -->
                                            <div class="modal fade" id="approveModal<?= $application->id ?>" tabindex="-1" aria-labelledby="approveModalLabel<?= $application->id ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title" id="approveModalLabel<?= $application->id ?>">
                                                                <i class="fas fa-check-circle mr-2"></i>Bayilik Başvurusunu Onayla
                                                            </h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form action="<?= base_url('admin/dealer/approveApplication') ?>" method="post">
                                                            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                                                            <input type="hidden" name="application_id" value="<?= $application->id ?>">
                                                            
                                                            <div class="modal-body">
                                                                <div class="alert alert-info">
                                                                    <strong><?= $application->company_name ?></strong> firması için bayilik başvurusunu onaylamak üzeresiniz.
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="dealer_type_id<?= $application->id ?>" class="form-label">Bayilik Tipi <span class="text-danger">*</span></label>
                                                                        <select class="form-control" id="dealer_type_id<?= $application->id ?>" name="dealer_type_id" required>
                                                                            <option value="">Seçiniz</option>
                                                                            <?php foreach ($dealer_types as $type): ?>
                                                                                <option value="<?= $type->id ?>"><?= $type->name ?> (Min: <?= number_format($type->min_purchase_amount, 2, ',', '.') ?> TL - İndirim: %<?= $type->discount_percentage ?>)</option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                            </div>
                                                            
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                    <i class="fas fa-times mr-1"></i>İptal
                                                                </button>
                                                                <button type="submit" class="btn btn-success">
                                                                    <i class="fas fa-check mr-1"></i>Onayla
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Reddetme Modal -->
                                            <div class="modal fade" id="rejectModal<?= $application->id ?>" tabindex="-1" aria-labelledby="rejectModalLabel<?= $application->id ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title" id="rejectModalLabel<?= $application->id ?>">
                                                                <i class="fas fa-times-circle mr-2"></i>Bayilik Başvurusunu Reddet
                                                            </h5>
                                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Kapat">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form action="<?= base_url('admin/dealer/rejectApplication') ?>" method="post">
                                                            <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                                                            <input type="hidden" name="application_id" value="<?= $application->id ?>">
                                                            
                                                            <div class="modal-body">
                                                                <div class="alert alert-danger">
                                                                    <strong><?= $application->company_name ?></strong> firması için bayilik başvurusunu reddetmek üzeresiniz.
                                                                </div>
                                                                
                                                                <div class="mb-3">
                                                                    <label for="admin_notes<?= $application->id ?>_reject" class="form-label">Ret Sebebi <span class="text-danger">*</span></label>
                                                                        <textarea class="form-control" id="admin_notes<?= $application->id ?>_reject" name="admin_notes" rows="3" placeholder="Başvuruyu reddetme sebebiniz..." required><?= !empty($application->admin_notes) ? $application->admin_notes : '' ?></textarea>
                                                                    </div>
                                                            </div>
                                                            
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                                    <i class="fas fa-times mr-1"></i>İptal
                                                                </button>
                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="fas fa-times mr-1"></i>Reddet
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

<script>
$(document).ready(function() {
    // DataTable ayarları
    $('#applicationsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Turkish.json'
        },
        order: [[5, 'desc']], // Tarihe göre sırala (5. sütun)
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [6] } // İşlemler sütununu sıralamaya dahil etme
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tümü"]]
    });
    
    // Tablo yenileme butonu
    $('#refreshTable').click(function() {
        location.reload();
    });
    
    // Modal açıldığında ilk input alanına odaklan
    $('.modal').on('shown.bs.modal', function () {
        $(this).find('select, textarea').first().focus();
    });
});
</script> 