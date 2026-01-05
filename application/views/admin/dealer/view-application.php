<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Sayfa Başlık ve Navigasyon -->
<div class="container-fluid">
    <h1 class="mt-4">Bayilik Başvurusu Detayı</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Ana Sayfa</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dealer') ?>">Bayilik Yönetimi</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dealer/applications') ?>">Bayilik Başvuruları</a></li>
        <li class="breadcrumb-item active">Başvuru #<?= $application->id ?></li>
    </ol>

    <!-- Başvuru Durumu ve Butonlar -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="me-3">
                        <?php if ($application->status == 'pending'): ?>
                            <span class="badge bg-warning p-2"><i class="fas fa-clock fs-5"></i></span>
                        <?php elseif ($application->status == 'approved'): ?>
                            <span class="badge bg-success p-2"><i class="fas fa-check fs-5"></i></span>
                        <?php elseif ($application->status == 'rejected'): ?>
                            <span class="badge bg-danger p-2"><i class="fas fa-times fs-5"></i></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h5 class="card-title mb-1">
                            <?php 
                            if ($application->status == 'pending') echo 'Bekleyen Başvuru';
                            else if ($application->status == 'approved') echo 'Onaylanmış Başvuru';
                            else if ($application->status == 'rejected') echo 'Reddedilmiş Başvuru';
                            ?>
                        </h5>
                        <p class="card-text mb-0">Başvuru Tarihi: <?= date('d.m.Y H:i', strtotime($application->created_at)) ?></p>
                        <?php if (!empty($application->updated_at)): ?>
                            <p class="card-text mb-0 small">Son Güncelleme: <?= date('d.m.Y H:i', strtotime($application->updated_at)) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <?php if ($application->status == 'pending'): ?>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="fas fa-check me-2"></i> Onayla
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times me-2"></i> Reddet
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Firma Bilgileri -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-building me-1"></i>
                    Firma Bilgileri
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th width="150">Firma Adı:</th>
                                <td><strong><?= $application->company_name ?></strong></td>
                            </tr>
                            <tr>
                                <th>Web Sitesi:</th>
                                <td><?= !empty($application->website) ? '<a href="' . $application->website . '" target="_blank">' . $application->website . '</a>' : '<span class="text-muted">Belirtilmemiş</span>' ?></td>
                            </tr>
                            <tr>
                                <th>Vergi Numarası:</th>
                                <td><?= !empty($application->tax_number) ? $application->tax_number : '<span class="text-muted">Belirtilmemiş</span>' ?></td>
                            </tr>
                            <tr>
                                <th>Vergi Dairesi:</th>
                                <td><?= !empty($application->tax_office) ? $application->tax_office : '<span class="text-muted">Belirtilmemiş</span>' ?></td>
                            </tr>
                            <tr>
                                <th>Tahmini Ciro:</th>
                                <td><?= !empty($application->estimated_revenue) ? number_format($application->estimated_revenue, 2, ',', '.') . ' TL' : '<span class="text-muted">Belirtilmemiş</span>' ?></td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-3">
                        <h6 class="fw-bold">Adres:</h6>
                        <p><?= nl2br($application->address) ?></p>
                    </div>

                    <?php if (!empty($application->description)): ?>
                        <div class="mt-3">
                            <h6 class="fw-bold">Ek Açıklamalar:</h6>
                            <p><?= nl2br($application->description) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Müşteri ve Durum Bilgileri -->
        <div class="col-md-6 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Müşteri Bilgileri
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 20px;">
                            <?= strtoupper(substr($application->name, 0, 1) . substr($application->surname, 0, 1)) ?>
                        </div>
                        <div>
                            <h5 class="mb-0"><?= $application->name . ' ' . $application->surname ?></h5>
                            <p class="text-muted mb-0"><?= $application->email ?></p>
                            <p class="text-muted mb-0"><?= $application->phone ?></p>
                        </div>
                    </div>

                    <?php if ($user_dealer_info): ?>
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i> Mevcut Bayilik Bilgisi</h6>
                            <p class="mb-1">Bu kullanıcı halihazırda <strong><?= $user_dealer_info->dealer_name ?></strong> bayisi olarak tanımlı.</p>
                            <p class="mb-0">Toplam Alım: <strong><?= number_format($user_dealer_info->total_purchase, 2, ',', '.') ?> TL</strong></p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-3">
                        <h6 class="fw-bold">Admin Notu:</h6>
                        <?php if (!empty($application->admin_notes)): ?>
                            <div class="p-3 bg-light rounded">
                                <?= nl2br($application->admin_notes) ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">Admin notu bulunmuyor.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Kullanıcı Aktiviteleri -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Kullanıcı Aktiviteleri
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <a href="<?= base_url('admin/user/userDetail/' . $application->user_id) ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user me-1"></i> Kullanıcı Detayları
                        </a>
                        <a href="<?= base_url('admin/dealer/userHistory/' . $application->user_id) ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-history me-1"></i> Bayilik Geçmişi
                        </a>
                        <a href="<?= base_url('admin/order/userOrders/' . $application->user_id) ?>" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-shopping-cart me-1"></i> Siparişleri
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Onaylama Modal -->
<?php if ($application->status == 'pending'): ?>
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel">Bayilik Başvurusunu Onayla</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <form action="<?= base_url('admin/dealer/approveApplication') ?>" method="post">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    <input type="hidden" name="application_id" value="<?= $application->id ?>">
                    
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong><?= $application->company_name ?></strong> firması için bayilik başvurusunu onaylamak üzeresiniz.
                        </div>
                        
                        <div class="mb-3">
                            <label for="dealer_type_id" class="form-label">Bayilik Tipi</label>
                            <select class="form-select" id="dealer_type_id" name="dealer_type_id" required>
                                <option value="">Seçiniz</option>
                                <?php foreach ($dealer_types as $type): ?>
                                    <option value="<?= $type->id ?>"><?= $type->name ?> (Min: <?= number_format($type->min_purchase_amount, 2, ',', '.') ?> TL - İndirim: %<?= $type->discount_percentage ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Admin Notları</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" placeholder="Bu notlar sadece admin panelinde görünecektir."><?= !empty($application->admin_notes) ? $application->admin_notes : '' ?></textarea>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-success">Onayla</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Reddetme Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Bayilik Başvurusunu Reddet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
                </div>
                <form action="<?= base_url('admin/dealer/rejectApplication') ?>" method="post">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">
                    <input type="hidden" name="application_id" value="<?= $application->id ?>">
                    
                    <div class="modal-body">
                        <div class="alert alert-danger">
                            <strong><?= $application->company_name ?></strong> firması için bayilik başvurusunu reddetmek üzeresiniz.
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Ret Sebebi</label>
                            <textarea class="form-control" id="admin_notes" name="admin_notes" rows="3" placeholder="Başvuruyu reddetme sebebiniz..." required><?= !empty($application->admin_notes) ? $application->admin_notes : '' ?></textarea>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-danger">Reddet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?> 