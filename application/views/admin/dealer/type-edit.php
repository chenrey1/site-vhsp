<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Bayilik Tipi Düzenle</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dealer'); ?>">Bayilik Tipleri</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bayilik Tipi Düzenle</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-edit mr-1"></i>
                            Bayilik Tipi Düzenle: <?= $dealer_type->name ?>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('admin/dealer/updateDealerType/'.$dealer_type->id) ?>" method="post">
                                <?php $csrf = array(
                                    'name' => $this->security->get_csrf_token_name(),
                                    'hash' => $this->security->get_csrf_hash()
                                ); ?>
                                <input type="hidden" name="<?= $csrf['name']; ?>" value="<?= $csrf['hash']; ?>" />
                                
                                <div class="form-group">
                                    <label for="name">Bayilik Adı</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= $dealer_type->name ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="min_purchase_amount">Minimum Alım Miktarı (TL)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="min_purchase_amount" name="min_purchase_amount" value="<?= $dealer_type->min_purchase_amount ?>" required>
                                    <small class="form-text text-muted">Bu bayilik seviyesi için gereken minimum alım miktarı</small>
                                </div>
                                <div class="form-group">
                                    <label for="discount_percentage">Varsayılan İndirim Yüzdesi (%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="discount_percentage" name="discount_percentage" value="<?= $dealer_type->discount_percentage ?>" required>
                                    <small class="form-text text-muted">Bu bayilik için varsayılan indirim oranı</small>
                                </div>
                                <div class="form-group">
                                    <label for="upgrade_condition">Yükseltme Koşulu (TL)</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="upgrade_condition" name="upgrade_condition" value="<?= $dealer_type->upgrade_condition ?>" required>
                                    <small class="form-text text-muted">Bir üst bayiliğe geçiş için gereken alım miktarı</small>
                                </div>
                                <div class="form-group">
                                    <label for="description">Açıklama</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?= $dealer_type->description ?></textarea>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="auto_upgrade" name="auto_upgrade" <?= $dealer_type->auto_upgrade ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="auto_upgrade">Otomatik Yükseltme</label>
                                    </div>
                                    <small class="form-text text-muted">Alım koşulunu sağlayan bayiler otomatik olarak yükseltilsin mi?</small>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Güncelle</button>
                                    <a href="<?= base_url('admin/dealer') ?>" class="btn btn-secondary">İptal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-info-circle mr-1"></i>
                            Bayilik Bilgileri
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-6">Oluşturulma Tarihi:</dt>
                                <dd class="col-sm-6"><?= date('d.m.Y H:i', strtotime($dealer_type->created_at)) ?></dd>
                                
                                <dt class="col-sm-6">Son Güncelleme:</dt>
                                <dd class="col-sm-6"><?= $dealer_type->updated_at ? date('d.m.Y H:i', strtotime($dealer_type->updated_at)) : '-' ?></dd>
                                
                                <dt class="col-sm-6">Durum:</dt>
                                <dd class="col-sm-6">
                                    <?php if ($dealer_type->status == 1): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Pasif</span>
                                    <?php endif; ?>
                                </dd>
                            </dl>
                            <hr>
                            <div class="mt-3">
                                <h6>Hızlı Erişim</h6>
                                <div class="list-group">
                                    <a href="<?= base_url('admin/dealer/dealerUsers/'.$dealer_type->id) ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-users mr-2"></i>Bayilik Kullanıcıları
                                    </a>
                                    <a href="<?= base_url('admin/dealer/productPricing/'.$dealer_type->id) ?>" class="list-group-item list-group-item-action">
                                        <i class="fas fa-tag mr-2"></i>Ürün Fiyatlandırma
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>