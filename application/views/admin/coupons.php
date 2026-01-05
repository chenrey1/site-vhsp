<div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">Kuponlar</h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/products">Mağaza</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Kuponlar</li>
                            </ol>
                        </nav>

                        <div class="page-btn">
                            <div class="btns">
                                <a href="javascript:void(0)" speedy-init-url="<?= base_url('admin/API/getCoupon/create') ?>" speedy-modal="couponEdit" speedy-action="<?= base_url('admin/product/createCoupon/') ?>" class="btn btn-success btn-sm">Kupon Oluştur</a>     
                                <!--<a href="#" class="btn btn-info btn-sm"><i class="fa fa-paper-plane"></i> SMS Gönder</a> -->         
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered border dataTable table-product">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Kupon</th>
                                                <th>Durum</th>
                                                <th>Alt Limit</th>
                                                <th>İndirim Tutarı</th>
                                                <th>Kullanan/Toplam Kullanıcı</th>
                                                <th>Başlangıç</th>
                                                <th>Bitiş</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
											$allUsers = $this->db->get('user')->result();
											foreach ($coupons as $c) {
												if (isset($c->users) && $c->users == "all") {
													$c->users = $allUsers;
													$c->isAllUsers = true;
												} else {
													$c->users = json_decode($c->users ?? "[]", true);
													$c->isAllUsers = false;
												}
                                                $c->used_by = json_decode($c->used_by ?? "[]", true);
                                                $c->products = json_decode($c->products ?? "[]", true);
                                                $c->categories = json_decode($c->categories ?? "[]", true);
                                            ?>
                                                <tr>
                                                    <td>
                                                        <?= $c->coupon ?>
                                                    </td>
                                                    <td>
                                                        <?= ($c->status=="active" && strtotime($c->end_at)>time()) ? "Aktif" : "Deaktif" ?>
                                                    </td>
                                                    <td>
                                                        <?= $c->min_amount ?>
                                                    </td>
                                                    <td>
                                                        <?= ($c->type=="amount") ? $c->amount."₺" : "%".$c->amount ?>
                                                    </td>
                                                    <td>
                                                        <?= count($c->used_by)."/".count($c->users).( $c->isAllUsers ? " (Tüm Kullanıcılar)" : "" ) ?>
                                                    </td>
                                                    <td>
                                                        <?= $c->start_at ?>
                                                    </td>
                                                    <td>
                                                        <?= $c->end_at ?>
                                                    </td>
                                                    <td>
                                                        <a href="javascript:void(0)" class="couponEdit" speedy-init-url="<?= base_url('admin/API/getCoupon/'.$c->id) ?>" speedy-modal="couponEdit" speedy-action="<?= base_url('admin/product/editCoupon/'.$c->id) ?>"><i class="fa fa-edit"></i></a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </main>

<div class="modal fade" id="couponEdit" tabindex="-1" role="dialog" aria-labelledby="couponEdit" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Kupon Düzenleme</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient-name" class="col-form-label">Kupon Kodu:</label>
                        <input type="text" class="form-control" name="coupon" required speedy-init-by="coupon">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Durum</label>
                        <select class="custom-select" id="inputProduct" name="status" required speedy-init-by="status">
                            <option value="active">Aktif</option>
                            <option value="deactive">DeAktif</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Kategoriler</label>
                        <select class="form-control selectpicker" data-live-search="true" multiple data-actions-box="true" name="categories[]" speedy-init-by="categories" speedy-init-with="categories" speedy-init-value="id" speedy-init-text="{{@name}}">
                            <option disabled selected>Kategorileri Seçin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Ürünler</label>
                        <select class="form-control selectpicker" data-live-search="true" multiple data-actions-box="true" name="products[]" speedy-init-by="products" speedy-init-with="products" speedy-init-value="id" speedy-init-text="{{@name}}">
                            <option disabled selected>Ürünleri Seçin</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="message-text" class="col-form-label">İndirim Tipi</label>
                        <select class="custom-select" id="inputProduct" name="type" required speedy-init-by="type">
                            <option value="amount">Tutar</option>
                            <option value="rate">Yüzde</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Tutar/Yüzde:</label>
                        <input type="number" class="form-control" value="" name="amount" min="0"  step="0.01" required="" speedy-init-by="amount">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Alt Limit:</label>
                        <input type="number" class="form-control" value="" name="min_amount" min="0"  step="0.01" required="" speedy-init-by="min_amount">
                    </div>


                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Başlangıç Tarihi:</label>
                        <input type="datetime-local" class="form-control" value="" name="start_at" required="" speedy-init-by="start_at">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Bitiş Tarihi:</label>
                        <input type="datetime-local" class="form-control" value="" name="end_at" required="" speedy-init-by="end_at">
                    </div>

					<div>
						<div class="form-group">
							<div class="custom-control custom-checkbox">
								<input type="checkbox" class="custom-control-input" id="customCheck1" name="only_users" value="1" speedy-init-by="only_users">
								<label class="custom-control-label" for="customCheck1">Kuponu sadece şu kullanıcılar kullanabilsin</label>
							</div>
						</div>
						<div class="form-group" display-for="{@only_users}=1">
							<label for="message-text" class="col-form-label">Tanımlı Kullanıcılar:</label>
							<select class="form-control selectpicker" data-live-search="true" multiple data-actions-box="true" name="users[]" speedy-init-by="users" speedy-init-with="users" speedy-init-value="id" speedy-init-text="{{@name}} {{@surname}} | {{@email}}">
								<option disabled selected>Tanımlı Kullanıcıları Seçin</option>
							</select>
						</div>
					</div>

                    <div class="form-group">
                        <label for="message-text" class="col-form-label">Kuponu Kullanan Kullanıcılar:</label>
                        <select class="form-control selectpicker" data-live-search="true" multiple data-actions-box="true" name="used_by[]" speedy-init-by="used_by" speedy-init-with="used_by" speedy-init-value="id" speedy-init-text="{{@name}} {{@surname}} | {{@email}}">
                            <option disabled selected>Tanımlı Kullanıcıları Seçin</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Güncelle</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        speedy_init();
    });
    $(document).on("onSpeedyInit", {}, function(event,element) {
        element.html('<i class="fas fa-sync fa-spin"></i>');
        element.prop( "disabled", true );
    });
    $(document).on("onSpeedyInitComplete", {}, function(event,element) {
        element.html('<i class="fa fa-edit"></i>');
        element.prop( "disabled", false );
    });
</script>

               
