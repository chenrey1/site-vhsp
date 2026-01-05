<?php $this->load->view('admin/includes/header'); ?>
<?php $this->load->view('admin/includes/sidebar'); ?>
<div id="layoutSidenav_content">
    <main>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title">Ürün Teslimatları</h4>
                        <p class="card-category">Çekiliş ürün ödüllerinin teslimat yönetimi</p>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link <?= $tab == 'active' ? 'active' : '' ?>" href="/admin/draw/index">
                                    <i class="fas fa-play-circle"></i> Aktif Çekilişler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $tab == 'finished' ? 'active' : '' ?>" href="/admin/draw/finished">
                                    <i class="fas fa-check-circle"></i> Sonlanmış Çekilişler
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $tab == 'deliveries' ? 'active' : '' ?>" href="/admin/draw/deliveries">
                                    <i class="fas fa-truck"></i> Teslimatlar
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active">
                                <?php if(empty($draws)): ?>
                                    <div class="alert alert-info mt-3">
                                        Tamamlanmış ürün ödüllü çekiliş bulunmuyor.
                                    </div>
                                <?php else: ?>
                                    <?php foreach($draws as $draw): ?>
                                        <div class="card mt-4">
                                            <div class="card-header">
                                                <h5><?= $draw->name ?> - <?= date('d.m.Y H:i', strtotime($draw->end_time)) ?> tarihinde tamamlandı</h5>
                                            </div>
                                            
                                            <div class="card-body">
                                                <?php if(empty($draw->winners)): ?>
                                                    <div class="alert alert-warning">
                                                        Bu çekilişte ürün kazanan bulunamadı.
                                                    </div>
                                                <?php else: ?>
                                                    <div class="table-responsive">
                                                        <table class="table">
                                                            <thead>
                                                                <tr>
                                                                    <th>Kullanıcı</th>
                                                                    <th>E-posta</th>
                                                                    <th>Kazanılan Ürün</th>
                                                                    <th>Teslimat Durumu</th>
                                                                    <th>Teslimat Bilgisi</th>
                                                                    <th>İşlemler</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach($draw->winners as $winner): ?>
                                                                    <tr>
                                                                        <td><?= $winner->user_name ?></td>
                                                                        <td><?= $winner->email ?></td>
                                                                        <td><?= $winner->product_name ?></td>
                                                                        <td>
                                                                            <?php if($winner->is_delivered): ?>
                                                                                <span class="badge badge-success">Teslim Edildi</span>
                                                                                <br>
                                                                                <small><?= date('d.m.Y H:i', strtotime($winner->delivery_date)) ?></small>
                                                                            <?php else: ?>
                                                                                <span class="badge badge-warning">Beklemede</span>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#deliveryModal<?= $winner->id ?>">
                                                                                <?= $winner->delivery_info ? 'Teslimat Bilgisini Düzenle' : 'Teslimat Bilgisi Ekle' ?>
                                                                            </button>
                                                                            
                                                                            <!-- Teslimat Bilgisi Modal -->
                                                                            <div class="modal fade" id="deliveryModal<?= $winner->id ?>" tabindex="-1" role="dialog" aria-labelledby="deliveryModalLabel<?= $winner->id ?>" aria-hidden="true">
                                                                                <div class="modal-dialog" role="document">
                                                                                    <div class="modal-content">
                                                                                        <form action="/admin/draw/update_delivery" method="post">
                                                                                            <div class="modal-header">
                                                                                                <h5 class="modal-title" id="deliveryModalLabel<?= $winner->id ?>">Teslimat Bilgisi - <?= $winner->user_name ?></h5>
                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                    <span aria-hidden="true">&times;</span>
                                                                                                </button>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                                <input type="hidden" name="winner_id" value="<?= $winner->id ?>">
                                                                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                                                                
                                                                                                <div class="form-group">
                                                                                                    <label for="delivery_info<?= $winner->id ?>">Teslimat Bilgisi</label>
                                                                                                    <textarea class="form-control" id="delivery_info<?= $winner->id ?>" name="delivery_info" rows="5" placeholder="Kargo takip numarası, teslimat adresi vb."><?= $winner->delivery_info ?? '' ?></textarea>
                                                                                                    <small class="form-text text-muted">Bu bilgi, teslimat tamamlandı olarak işaretlenirse kullanıcıya gösterilecektir.</small>
                                                                                                </div>
                                                                                                
                                                                                                <div class="form-check">
                                                                                                    <label class="form-check-label">
                                                                                                        <input class="form-check-input" type="checkbox" name="is_delivered" value="1" <?= $winner->is_delivered ? 'checked' : '' ?>>
                                                                                                        Teslimat tamamlandı olarak işaretle
                                                                                                        <span class="form-check-sign">
                                                                                                            <span class="check"></span>
                                                                                                        </span>
                                                                                                    </label>
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
                                                                        </td>
                                                                        <td>
                                                                            <?php if($winner->delivery_info): ?>
                                                                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#viewDeliveryModal<?= $winner->id ?>">
                                                                                    Görüntüle
                                                                                </button>
                                                                                
                                                                                <!-- Teslimat Bilgisi Görüntüleme Modal -->
                                                                                <div class="modal fade" id="viewDeliveryModal<?= $winner->id ?>" tabindex="-1" role="dialog" aria-labelledby="viewDeliveryModalLabel<?= $winner->id ?>" aria-hidden="true">
                                                                                    <div class="modal-dialog" role="document">
                                                                                        <div class="modal-content">
                                                                                            <div class="modal-header">
                                                                                                <h5 class="modal-title" id="viewDeliveryModalLabel<?= $winner->id ?>">Teslimat Bilgisi - <?= $winner->user_name ?></h5>
                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                    <span aria-hidden="true">&times;</span>
                                                                                                </button>
                                                                                            </div>
                                                                                            <div class="modal-body">
                                                                                                <p><?= nl2br($winner->delivery_info) ?></p>
                                                                                            </div>
                                                                                            <div class="modal-footer">
                                                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('admin/includes/footer'); ?> 
