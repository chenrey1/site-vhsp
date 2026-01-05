<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Bayilik Tipleri</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bayilik Tipleri</li>
                </ol>
            </nav>

            <!-- Bayilik Yükseltme Görsel Akış Diyagramı -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-route mr-1"></i>
                    Bayilik Yükseltme Akışı
                </div>
                <div class="card-body">
                    <?php if (isset($dealer_types) && !empty($dealer_types)): ?>
                        <div class="dealer-flow-diagram">
                            <div class="row">
                                <?php 
                                // Aktif bayilikleri filtrele
                                $active_dealer_types = array_filter($dealer_types, function($dt) {
                                    return $dt->status == 1;
                                });
                                
                                // Bayilik tiplerini en düşük alım miktarına göre sırala
                                usort($active_dealer_types, function($a, $b) {
                                    return $a->min_purchase_amount - $b->min_purchase_amount;
                                });
                                
                                $total_types = count($active_dealer_types);
                                ?>
                                
                                <?php foreach ($active_dealer_types as $index => $type): ?>
                                    <div class="col-md-<?= ceil(12 / $total_types) ?> text-center">
                                        <div class="dealer-box <?= $type->status == 1 ? 'active' : 'inactive' ?>">
                                            <div class="dealer-level">
                                                <span class="badge badge-primary">Seviye <?= $index + 1 ?></span>
                                            </div>
                                            <div class="dealer-icon">
                                                <i class="fas <?= $index == 0 ? 'fa-hand-holding-usd' : ($index == $total_types - 1 ? 'fa-crown' : 'fa-award') ?> fa-2x"></i>
                                            </div>
                                            <h5 class="dealer-name"><?= $type->name ?></h5>
                                            <div class="dealer-info">
                                                <p><i class="fas fa-shopping-cart"></i> Min: <?= number_format($type->min_purchase_amount, 2, ',', '.') ?> TL</p>
                                                <p><i class="fas fa-tags"></i> İndirim: %<?= number_format($type->discount_percentage, 2, ',', '.') ?></p>
                                                <p><i class="fas fa-users"></i> Kullanıcı: <?= $type->user_count ?></p>
                                                <?php if ($index < $total_types - 1): ?>
                                                    <p><i class="fas fa-level-up-alt"></i> Yükseltme: <?= number_format($type->upgrade_condition, 2, ',', '.') ?> TL</p>
                                                <?php else: ?>
                                                    <p><i class="fas fa-check-circle"></i> En Yüksek Seviye</p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="dealer-actions">
                                                <a href="<?= base_url('admin/dealer/editDealerType/'.$type->id) ?>" class="btn btn-sm btn-info">
                                                    <i class="fa fa-edit"></i> Düzenle
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <?php if ($index < $total_types - 1): ?>
                                            <div class="dealer-arrow">
                                                <i class="fas fa-long-arrow-alt-right"></i>
                                                <p class="upgrade-text"><?= number_format($type->upgrade_condition, 2, ',', '.') ?> TL alım</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- CSS custom.css dosyasına taşındı -->
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i> Henüz bayilik tipi eklenmemiş. Yeni bayilik tipleri ekleyerek görsel akış diyagramını görüntüleyebilirsiniz.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="page-btn">
                <div class="btns">
                    <a href="#addDealerTypeModal" data-toggle="modal" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Yeni Bayilik Tipi Ekle</a>
                    <a href="<?= base_url('admin/dealer/settings') ?>" class="btn btn-outline-primary btn-sm"><i class="fa fa-cogs"></i> Bayilik Ayarları</a>
                </div>
            </div>

            <!-- Bayilik Tipleri Listesi -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table mr-1"></i>
                    Bayilik Tipleri
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Bayilik Adı</th>
                                    <th>Min. Alım Miktarı</th>
                                    <th>İndirim Yüzdesi</th>
                                    <th>Yükseltme Koşulu</th>
                                    <th>Kullanıcı Sayısı</th>
                                    <th>Otomatik Yükseltme</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($dealer_types) && !empty($dealer_types)): ?>
                                    <?php foreach ($dealer_types as $type): ?>
                                        <?php if ($type->status == 0) continue; // Silinen bayilikleri listede gösterme ?>
                                        <tr>
                                            <td><?= $type->name ?></td>
                                            <td><?= number_format($type->min_purchase_amount, 2, ',', '.') ?> TL</td>
                                            <td>%<?= number_format($type->discount_percentage, 2, ',', '.') ?></td>
                                            <td><?= number_format($type->upgrade_condition, 2, ',', '.') ?> TL</td>
                                            <td>
                                                <span class="badge badge-info"><?= $type->user_count ?> Kullanıcı</span>
                                                <?php if ($type->user_count > 0): ?>
                                                    <a href="<?= base_url('admin/dealer/dealerUsers/'.$type->id) ?>" class="btn btn-sm btn-link p-0 ml-1">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($type->auto_upgrade == 1): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Pasif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($type->status == 1): ?>
                                                    <span class="badge badge-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Pasif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/dealer/editDealerType/'.$type->id) ?>" class="btn btn-sm btn-info">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <?php if ($type->status == 1): ?>
                                                    <a href="<?= base_url('admin/dealer/deleteDealerType/'.$type->id) ?>" class="btn btn-sm btn-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="#" class="btn btn-sm btn-secondary" disabled>
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= base_url('admin/dealer/dealerUsers/'.$type->id) ?>" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-users"></i>
                                                </a>
                                                <a href="<?= base_url('admin/dealer/productPricing/'.$type->id) ?>" class="btn btn-sm btn-warning">
                                                    <i class="fa fa-tag"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Henüz bayilik tipi bulunmuyor.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bayilik Sistemi Hakkında Bilgi Kartı -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle mr-1"></i>
                    Bayilik Sistemi Hakkında
                </div>
                <div class="card-body">
                    <p>Bayilik sistemi ile müşterilerinize özel fiyatlar ve indirimler sunabilirsiniz.</p>
                    <p>Özellikler:</p>
                    <ul>
                        <li><strong>Bayilik Tipleri:</strong> Farklı bayilik seviyeleri tanımlayabilirsiniz (Bronz, Gümüş, Altın vb.)</li>
                        <li><strong>Otomatik Yükseltme:</strong> Belirli alım miktarına ulaşan bayileri otomatik olarak bir üst seviyeye yükseltebilirsiniz</li>
                        <li><strong>Özel Fiyatlandırma:</strong> Her bayilik tipi için ürünlere özel fiyat veya indirim oranı tanımlayabilirsiniz</li>
                    </ul>
                    <p>Bayilik sistemini etkin kullanmak için:</p>
                    <ol>
                        <li>Önce bayilik tipleri oluşturun</li>
                        <li>Bayilik tiplerine özel fiyatlar tanımlayın</li>
                        <li>Kullanıcıları bayiliklere atayın</li>
                        <li>Periyodik olarak bayilik seviyelerini kontrol edin</li>
                    </ol>
                </div>
            </div>
        </div>
    </main>

    <!-- Yeni Bayilik Tipi Ekleme Modal -->
    <div class="modal fade" id="addDealerTypeModal" tabindex="-1" role="dialog" aria-labelledby="addDealerTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDealerTypeModalLabel">Yeni Bayilik Tipi Ekle</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="<?= base_url('admin/dealer/addDealerType') ?>" method="post">
                        <div class="form-group">
                            <label for="name">Bayilik Adı</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="min_purchase_amount">Minimum Alım Miktarı (TL)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="min_purchase_amount" name="min_purchase_amount" required>
                            <small class="form-text text-muted">Bu bayilik seviyesi için gereken minimum alım miktarı</small>
                        </div>
                        <div class="form-group">
                            <label for="discount_percentage">Varsayılan İndirim Yüzdesi (%)</label>
                            <input type="number" step="0.01" min="0" max="100" class="form-control" id="discount_percentage" name="discount_percentage" required>
                            <small class="form-text text-muted">Bu bayilik için varsayılan indirim oranı</small>
                        </div>
                        <div class="form-group">
                            <label for="upgrade_condition">Yükseltme Koşulu (TL)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="upgrade_condition" name="upgrade_condition" required>
                            <small class="form-text text-muted">Bir üst bayiliğe geçiş için gereken alım miktarı</small>
                        </div>
                        <div class="form-group">
                            <label for="description">Açıklama</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="auto_upgrade" name="auto_upgrade" checked>
                                <label class="custom-control-label" for="auto_upgrade">Otomatik Yükseltme</label>
                            </div>
                            <small class="form-text text-muted">Alım koşulunu sağlayan bayiler otomatik olarak yükseltilsin mi?</small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                            <button type="submit" class="btn btn-primary">Ekle</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bayilik Kullanıcılarını Taşıma Modal -->
    <?php if ($this->session->flashdata('show_migration_modal')): ?>
    <?php $dealer_type = $this->session->flashdata('dealer_type_to_delete'); ?>
    <?php $users_count = $this->session->flashdata('dealer_users_count'); ?>
    <?php $is_last_dealer_type = $this->session->flashdata('is_last_dealer_type'); ?>
    <div class="modal fade" id="migrateDealerUsersModal" tabindex="-1" role="dialog" aria-labelledby="migrateDealerUsersModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="migrateDealerUsersModalLabel">Dikkat: Bayilik Kullanıcılarını Taşı</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong><?= $dealer_type->name ?></strong> bayilik tipinde <strong><?= $users_count ?> kullanıcı</strong> bulunmaktadır.
                    </div>
                    <p>Silinen bayilik planına ait kullanıcılar için bir işlem seçin:</p>
                    <form action="<?= base_url('admin/dealer/deleteDealerType/'.$dealer_type->id) ?>" method="post">
                        <?php $csrf = array(
                            'name' => $this->security->get_csrf_token_name(),
                            'hash' => $this->security->get_csrf_hash()
                        ); ?>
                        <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>" />
                        
                        <?php if ($is_last_dealer_type): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Bu, sistemdeki son aktif bayilik tipidir.
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="convert_to_normal_radio" name="action_type" value="normal" class="custom-control-input" checked>
                                <label class="custom-control-label" for="convert_to_normal_radio">
                                    <strong>Tüm kullanıcıları normal üyeliğe çevir</strong>
                                </label>
                                <p class="text-muted small mt-1">Kullanıcılar bayilik avantajlarını kaybedecek ve normal üye olarak devam edecekler.</p>
                            </div>
                        </div>
                        <input type="hidden" name="convert_to_normal" value="1">
                        <?php else: ?>
                        <div class="form-group">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="migrate_radio" name="action_type" value="migrate" class="custom-control-input" checked>
                                <label class="custom-control-label" for="migrate_radio">
                                    <strong>Başka bayiliğe taşı</strong>
                                </label>
                                <p class="text-muted small mt-1">Kullanıcılar seçtiğiniz başka bir bayilik planına taşınacak.</p>
                            </div>
                            
                            <div class="custom-control custom-radio mt-3">
                                <input type="radio" id="convert_radio" name="action_type" value="normal" class="custom-control-input">
                                <label class="custom-control-label" for="convert_radio">
                                    <strong>Normal üyeliğe çevir</strong>
                                </label>
                                <p class="text-muted small mt-1">Kullanıcılar bayilik avantajlarını kaybedecek ve normal üye olarak devam edecekler.</p>
                            </div>
                        </div>
                        
                        <input type="hidden" name="convert_to_normal" value="0">
                        
                        <div id="dealer_selection_area" class="form-group mt-3">
                            <label for="new_dealer_type_id">Kullanıcıları Taşınacak Bayilik:</label>
                            <select class="form-control" id="new_dealer_type_id" name="new_dealer_type_id" required>
                                <option value="">-- Bayilik Seçin --</option>
                                <?php foreach ($dealer_types as $type): ?>
                                    <?php if ($type->id != $dealer_type->id && $type->status == 1): ?>
                                        <option value="<?= $type->id ?>"><?= $type->name ?> (Min: <?= number_format($type->min_purchase_amount, 2, ',', '.') ?> TL)</option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="description">Açıklama:</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="İşlem açıklaması (opsiyonel)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">İşlemi Uygula</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            $('#migrateDealerUsersModal').modal('show');
            
            // Radio butonlarına göre form alanlarını göster/gizle
            $('input[name="action_type"]').change(function() {
                if ($(this).val() === 'migrate') {
                    $('#dealer_selection_area').show();
                    $('#new_dealer_type_id').prop('required', true);
                    $('input[name="convert_to_normal"]').val('0');
                } else {
                    $('#dealer_selection_area').hide();
                    $('#new_dealer_type_id').prop('required', false);
                    $('input[name="convert_to_normal"]').val('1');
                }
            });
            
            // Form submit
            $('form').submit(function() {
                var actionType = $('input[name="action_type"]:checked').val();
                if (actionType === 'normal') {
                    $('#new_dealer_type_id').prop('required', false);
                    $('#new_dealer_type_id').val('');
                }
            });
        });
    </script>
    <?php endif; ?>