<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Bayilik Kullanıcıları</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dealer'); ?>">Bayilik Tipleri</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bayilik Kullanıcıları</li>
                </ol>
            </nav>

            <div class="page-btn">
                <div class="btns">
                    <a href="#addDealerUserModal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Kullanıcıya Bayilik Ata</a>
                    <a href="<?= base_url('admin/dealer/checkUpgrades') ?>" class="btn btn-info btn-sm"><i class="fa fa-sync"></i> Otomatik Yükseltmeleri Kontrol Et</a>
                    
                    <!-- Bayilik filtreleme dropdown -->
                    <div class="btn-group ml-2">
                        <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Bayilik Tipine Göre Filtrele
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?= base_url('admin/dealer/dealerUsers') ?>">Tüm Bayiler</a>
                            <div class="dropdown-divider"></div>
                            <?php foreach ($dealer_types as $type): ?>
                                <a class="dropdown-item" href="<?= base_url('admin/dealer/dealerUsers/'.$type->id) ?>"><?= $type->name ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bayilik Kullanıcıları Listesi -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-users mr-1"></i>
                    <?php if (isset($dealer_type)): ?>
                        <?= $dealer_type->name ?> Bayilik Kullanıcıları
                    <?php else: ?>
                        Tüm Bayilik Kullanıcıları
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Kullanıcı</th>
                                    <th>Bayilik Tipi</th>
                                    <th>Başlangıç Tarihi</th>
                                    <th>Toplam Alım</th>
                                    <th>Durum</th>
                                    <th>Oto. Yükseltme</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($dealer_users) && !empty($dealer_users)): ?>
                                    <?php foreach ($dealer_users as $user): ?>
                                        <tr>
                                            <td><?= $user->name ?> <?= $user->surname ?> (<?= $user->email ?>)</td>
                                            <td><?= $user->dealer_name ?></td>
                                            <td><?= date('d.m.Y H:i', strtotime($user->start_date)) ?></td>
                                            <td><?= number_format($user->total_purchase, 2, ',', '.') ?> TL</td>
                                            <td>
                                                <?php if ($user->active_status == 1): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Pasif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($user->auto_upgrade == 1): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Pasif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="#editDealerUserModal<?= $user->id ?>" data-toggle="modal" class="btn btn-sm btn-info">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('admin/dealer/toggleDealerStatus/'.$user->user_id) ?>" class="btn btn-sm <?= $user->active_status ? 'btn-warning' : 'btn-success' ?>">
                                                    <i class="fa <?= $user->active_status ? 'fa-ban' : 'fa-check' ?>"></i>
                                                </a>
                                                <a href="<?= base_url('admin/dealer/userHistory/'.$user->user_id) ?>" class="btn btn-sm btn-secondary">
                                                    <i class="fa fa-history"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        
                                        <!-- Edit Dealer User Modal -->
                                        <div class="modal fade" id="editDealerUserModal<?= $user->id ?>" tabindex="-1" role="dialog" aria-labelledby="editDealerUserModalLabel<?= $user->id ?>" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editDealerUserModalLabel<?= $user->id ?>">Bayilik Düzenle: <?= $user->name ?> <?= $user->surname ?></h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form action="<?= base_url('admin/dealer/assignDealer') ?>" method="post">
                                                            <input type="hidden" name="user_id" value="<?= $user->user_id ?>">
                                                            
                                                            <div class="form-group">
                                                                <label for="dealer_type_id<?= $user->id ?>">Bayilik Tipi</label>
                                                                <select class="form-control" id="dealer_type_id<?= $user->id ?>" name="dealer_type_id" required>
                                                                    <?php foreach ($dealer_types as $type): ?>
                                                                        <option value="<?= $type->id ?>" <?= ($type->id == $user->dealer_type_id) ? 'selected' : '' ?>><?= $type->name ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="form-group">
                                                                <label for="description<?= $user->id ?>">Değişiklik Açıklaması</label>
                                                                <textarea class="form-control" id="description<?= $user->id ?>" name="description" rows="2" placeholder="Bayilik değişikliği için açıklama"></textarea>
                                                            </div>
                                                            
                                                            <!-- Süreli Bayilik Ataması - Mevcut bayiler için -->
                                                            <div class="form-group">
                                                                <div class="custom-control custom-switch">
                                                                    <input type="checkbox" class="custom-control-input enable-timed-dealer" id="enable_timed_dealer<?= $user->id ?>" name="enable_timed_dealer" value="1">
                                                                    <label class="custom-control-label" for="enable_timed_dealer<?= $user->id ?>">Süreli bayilik ataması yap</label>
                                                                </div>
                                                                <small class="form-text text-muted">Bu seçenek ile belirli süre sonra otomatik bayilik değişimi yapılabilir.</small>
                                                            </div>
                                                            
                                                            <div class="timed-dealer-settings" style="display:none;">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="dealer_period<?= $user->id ?>">Süre (Gün)</label>
                                                                            <input type="number" class="form-control" id="dealer_period<?= $user->id ?>" name="dealer_period" min="1" value="30">
                                                                            <small class="form-text text-muted">Kaç gün sonra bayilik tipi değiştirilecek</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="final_dealer_type_id<?= $user->id ?>">Son Bayilik Tipi</label>
                                                                            <select class="form-control" id="final_dealer_type_id<?= $user->id ?>" name="final_dealer_type_id">
                                                                                <option value="">Seçiniz</option>
                                                                                <?php foreach ($dealer_types as $type): ?>
                                                                                    <option value="<?= $type->id ?>"><?= $type->name ?></option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                            <small class="form-text text-muted">Süre sonunda geçilecek bayilik tipi</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                                                                <button type="submit" class="btn btn-primary">Kaydet</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Henüz bayilik atanmış kullanıcı bulunmuyor.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Dealer User Modal -->
    <div class="modal fade" id="addDealerUserModal" tabindex="-1" role="dialog" aria-labelledby="addDealerUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDealerUserModalLabel">Kullanıcıya Bayilik Ata</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('admin/dealer/assignDealer') ?>" method="post">
                        <div class="form-group">
                            <label for="user_id">Kullanıcı</label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">-- Kullanıcı Seçin --</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user->id ?>"><?= $user->name ?> <?= $user->surname ?> (<?= $user->email ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="dealer_type_id">Bayilik Tipi</label>
                            <select class="form-control" id="dealer_type_id" name="dealer_type_id" required>
                                <?php foreach ($dealer_types as $type): ?>
                                    <option value="<?= $type->id ?>"><?= $type->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control" id="description" name="description" rows="2" placeholder="Bayilik ataması için açıklama"></textarea>
                        </div>
                        
                        <!-- Süreli Bayilik Ataması - Yeni bayilik atamaları için -->
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input enable-timed-dealer" id="enable_timed_dealer_new" name="enable_timed_dealer" value="1">
                                <label class="custom-control-label" for="enable_timed_dealer_new">Süreli bayilik ataması yap</label>
                            </div>
                            <small class="form-text text-muted">Bu seçenek ile belirli süre sonra otomatik bayilik değişimi yapılabilir.</small>
                        </div>
                        
                        <div class="timed-dealer-settings" style="display:none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dealer_period">Süre (Gün)</label>
                                        <input type="number" class="form-control" id="dealer_period" name="dealer_period" min="1" value="30">
                                        <small class="form-text text-muted">Kaç gün sonra bayilik tipi değiştirilecek</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="final_dealer_type_id">Son Bayilik Tipi</label>
                                        <select class="form-control" id="final_dealer_type_id" name="final_dealer_type_id">
                                            <option value="">Seçiniz</option>
                                            <?php foreach ($dealer_types as $type): ?>
                                                <option value="<?= $type->id ?>"><?= $type->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="form-text text-muted">Süre sonunda geçilecek bayilik tipi</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-primary">Kaydet</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    $(document).ready(function() {
        // DataTable
        $('#dealerUsersTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Turkish.json"
            }
        });
        
        // Süreli bayilik ayarları görünürlüğü
        $('.enable-timed-dealer').change(function() {
            var settingsDiv = $(this).closest('.form-group').next('.timed-dealer-settings');
            
            if($(this).is(':checked')) {
                settingsDiv.slideDown();
            } else {
                settingsDiv.slideUp();
            }
        });
    });
</script>