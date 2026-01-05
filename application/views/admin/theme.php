
            <div id="layoutSidenav_content">


                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">Tema</h5>
                        </div>

                        <div class="theme-area">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <?php $properties = $this->db->where('id', 1)->get("properties")->row(); ?>
                                            <img src="<?= base_url('assets/img/theme/') . $properties->theme . '.png' ?>" alt="">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6><?= $properties->theme ?></h6>
                                                <a href="#" class="btn btn-success btn-sm">Aktif</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php $themes = scandir("application/views/theme/"); ?>
                                <?php foreach ($themes as $theme) { ?>
                                    <?php if ($theme != $properties->theme && $theme != "." && $theme != ".."): ?>
                                        <div class="col-lg-3">
                                            <div class="card">
                                                <div class="card-body">
                                                    <?php $path = base_url('assets/img/theme/') . $theme . ".png" ?>
                                                      <img src="<?= $path; ?>" alt="">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h6><?= $theme ?></h6>
                                                        <a href="<?= base_url('admin/product/changeTheme/') . $theme ?>" class="btn btn-outline-danger btn-sm">Aktif Et</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif ?>
                                <?php } ?>

                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="nav flex-column nav-pills nav-theme" id="theme-settings" role="tablist" aria-orientation="vertical">
                                            <a class="nav-link active" id="id-urunler" data-toggle="pill" href="#nav-urunler" role="tab" aria-controls="nav-urunler" aria-selected="true">Ürünler</a>
                                            <a class="nav-link" id="id-kategori" data-toggle="pill" href="#nav-kategori" role="tab" aria-controls="nav-kategori" aria-selected="false">Kategoriler</a>
                                            <a class="nav-link" id="id-slider" data-toggle="pill" href="#nav-slider" role="tab" aria-controls="nav-slider" aria-selected="false">Slider</a>
                                            <a class="nav-link" id="id-neden" data-toggle="pill" href="#nav-neden" role="tab" aria-controls="nav-neden" aria-selected="false">Neden Biz</a>
                                            <a class="nav-link" id="id-editor" data-toggle="pill" href="#nav-editor" role="tab" aria-controls="nav-editor" aria-selected="false">Editörün Seçimi</a>
                                            <a class="nav-link" id="id-one-cikanlar" data-toggle="pill" href="#nav-one-cikanlar" role="tab" aria-controls="nav-editor" aria-selected="false">Öne Çıkanlar</a>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="tab-content" id="theme-settings">

                                            <div class="tab-pane fade show active" id="nav-urunler" role="tabpanel">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <orius>
                                                        <h5>Ürünler</h5>
                                                        <p class="mb-0">Ana sayfada gösterilecek ürün kategorilerini sırasıyla buradan seçebilirsiniz.</p>
                                                    </orius>
                                                    <button type="submit" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#modalUrunler"><i class="fa fa-plus"></i></button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table border table-ppb tabledit">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Kategori</th>
                                                                <th>Ürün Sayısı</th>
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        	<?php foreach ($homeProducts as $hp) { ?>
                                                            <tr>
                                                                <td><?php 

                                                                if ($hp->type == "lastProduct") {
                                                                	echo $type = "Son Çıkanlar";
                                                                }else if($hp->type == "bestSell") {
                                                                	echo $type = "Çok Satanlar";
                                                                }else{
                                                                	$productCategory = $this->db->where('id', $hp->type)->get('category')->row();
                                                                	echo $type = $productCategory->name;
                                                                }
                                                                ?></td>
                                                                <td><span class="badge badge-primary"><?= $hp->amount ?></span></td>
                                                                <td>
                                                                    <a href="#modalUrunlerDuzenle<?= $hp->id ?>" class="text-success" data-toggle="modal"><i class="fa fa-edit"></i></a>
                                                                    <a href="<?= base_url('admin/product/delete/themeSettings/home_products/') . $hp->id ?>" class="text-danger"><i class="fa fa-trash-alt"></i></a>
                                                                </td>
                                                            </tr>
                                                            <div class="modal fade" id="modalUrunlerDuzenle<?= $hp->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
											                    <div class="modal-dialog modal-dialog-centered" role="document">
											                        <div class="modal-content">
											                            <div class="modal-header">
											                                <h6 class="modal-title">Değiştir (<?= $type ?>)</h6>
											                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
											                                    <span aria-hidden="true">&times;</span>
											                                </button>
											                            </div>
											                            <div class="modal-body">
											                                <form action="<?= base_url('admin/product/edit/themeSettings/home_products/') . $hp->id ?>"  method="post">
											                                    <div class="form-group">
											                                        <label for="inputWTT">Kategori</label>
											                                        <select name="type" id="inputWTT" class="custom-select">
											                                            <option value="<?= $hp->type ?>" selected><?= $type ?></option>
											                                            <option value="lastProduct">Son Çıkanlar</option>
                                           						 						<option value="bestSell">Çok Satanlar</option>
											                                            <?php foreach ($categories as $c) { ?>
											                                                <option value="<?= $c->id ?>"><?= $c->name ?> (Kategori)</option>
											                                            <?php } ?>
											                                        </select>
											                                    </div>
											                                    <div class="form-group">
											                                        <label for="inputWT">Gösterilecek Sayı</label>
											                                        <input type="input" class="form-control" id="inputWT" name="amount" min="1" value="<?= $hp->amount ?>">
											                                    </div>
											                                    <div class="float-right">
											                                        <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
											                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Değiştir</button>
											                                    </div>
											                                </form>
											                            </div>
											                        </div>
											                    </div>
											                </div>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="nav-kategori" role="tabpanel">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">Kategoriler</h5>
                                                    <button type="submit" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#modalKategori"><i class="fa fa-plus"></i></button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table border table-bordered table-why tabledit">
                                                        <thead class="thead-light">
                                                        <tr>
                                                            <th>Görsel</th>
                                                            <th>Kategori / Ürün</th>
                                                            <th>Link</th>
                                                            <th width="5%"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php foreach ($homeCategory as $hc) { ?>
                                                            <tr>
                                                                <td><img src="<?= base_url('assets/img/home_category/') . $hc->img ?>" alt="image"></td>
                                                                <td><?= $hc->name ?></td>
                                                                <td><small><a href="<?= $hc->link ?>"><?= $hc->link ?></a></small></td>
                                                                <td>
                                                                    <a href="#modalCategory<?=$hc->id?>" class="text-success" data-toggle="modal"><i class="fa fa-edit"></i></a>
                                                                    <a href="<?= base_url('admin/product/delete/themeSettings/home_category/') . $hc->id ?>" class="text-danger"><i class="far fa-trash-alt"></i></a>
                                                                </td>
                                                            </tr>
                                                            <div class="modal fade" id="modalCategory<?=$hc->id?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h6 class="modal-title">Düzenleme Ekranı</h6>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form action="<?= base_url('admin/product/edit/themeSettings/home_category/') . $hc->id . '/home_category'?>"  method="post" enctype="multipart/form-data">
                                                                                <div class="form-group">
                                                                                    <label for="inputWTT">Kategori</label>
                                                                                    <select name="category_id" id="inputWTT" class="custom-select">
                                                                                        <option value="<?= $hc->category_id ?>" selected><?= $hc->name ?></option>
                                                                                        <?php foreach ($categories as $c) { ?>
                                                                                            <option value="<?= $c->id ?>"><?= $c->name ?></option>
                                                                                        <?php } ?>
                                                                                    </select>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="inputWT">Link</label>
                                                                                    <input type="text" class="form-control" id="inputWT" name="link" value="<?= $hc->link ?>" required="required">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <input type="file" name="img">
                                                                                </div>
                                                                                <div class="float-right">
                                                                                    <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                                                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Değiştir</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="nav-slider" role="tabpanel">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">Slider</h5>
                                                    <button type="submit" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#modalSlider"><i class="fa fa-plus"></i></button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table border table-bordered table-why tabledit">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Görsel</th>
                                                                <th>Başlık</th>
                                                                <th>Etiket</th>
                                                                <th>Açıklama</th>
                                                                <th width="5%"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($slider as $s) { ?>
                                                            <tr>
                                                                <td><img src="<?= base_url('assets/img/sliders/') . $s->img ?>" alt="image"></td>
                                                                <td><?= $s->title ?></td>
                                                                <td>#<?= $s->tag ?></td>
                                                                <td><?= $s->description ?></td>
                                                                <td>
                                                                    <a href="#modalSlider<?=$s->id?>" class="text-success" data-toggle="modal"><i class="fa fa-edit"></i></a>
                                                                    <a href="<?= base_url('admin/product/delete/themeSettings/slider/') . $s->id ?>" class="text-danger"><i class="far fa-trash-alt"></i></a>
                                                                </td>
                                                            </tr>
                                                            <div class="modal fade" id="modalSlider<?=$s->id?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h6 class="modal-title">Düzenleme Ekranı</h6>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form action="<?= base_url('admin/product/edit/themeSettings/slider/') . $s->id . '/sliders'?>"  method="post" enctype="multipart/form-data">
                                                                                <div class="form-group">
                                                                                    <label for="inputWT">Başlık</label>
                                                                                    <input type="text" class="form-control" id="inputWT" name="title" value="<?= $s->title ?>" required="required">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="inputWT">Etiket</label>
                                                                                    <input type="text" class="form-control" id="inputWT" name="tag" value="<?= $s->tag ?>" required="required">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="inputWT">Buton Yazısı (Boş Bırakabilirsiniz)</label>
                                                                                    <input type="text" class="form-control" id="inputWT" value="<?= $s->buton_2_text ?>" name="buton_2_text">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="inputWT">Buton Linki (Boş Bırakabilirsiniz)</label>
                                                                                    <input type="text" class="form-control" id="inputWT" value="<?= $s->buton_2_link ?>" name="buton_2_link">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="inputWT">Açıklama</label>
                                                                                    <input type="text" class="form-control" id="inputWT" name="description" value="<?= $s->description ?>" required="required">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <input type="file" name="img">
                                                                                </div>
                                                                                <div class="float-right">
                                                                                    <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                                                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Değiştir</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="nav-neden" role="tabpanel">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">Neden Biz?</h2>
                                                    <button type="submit" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#modalWhy"><i class="fa fa-plus"></i></button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table border table-bordered table-why tabledit">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Görsel</th>
                                                                <th class="text-left">Başlık</th>
                                                                <th class="text-left">Açıklama</th>
                                                                <th width="5%"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($why as $w) { ?>
                                                            <tr>
                                                                <td><img src="<?= base_url('assets/img/why/') . $w->img ?>" alt="image"></td>
                                                                <td class="text-left"><?= $w->title ?></td>
                                                                <td class="text-left"><?= $w->desc ?></td>
                                                                <td>
                                                                    <a href="#modalWhy<?=$w->id?>" class="text-success" data-toggle="modal"><i class="fa fa-edit"></i></a>
                                                                    <a href="<?= base_url('admin/product/delete/themeSettings/why/') . $w->id ?>" class="text-danger"><i class="far fa-trash-alt"></i></a>
                                                                </td>
                                                            </tr>
                                                            <div class="modal fade" id="modalWhy<?=$w->id?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h6 class="modal-title">Düzenleme Ekranı</h6>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form action="<?= base_url('admin/product/edit/themeSettings/why/') . $w->id . '/why'?>"  method="post" enctype="multipart/form-data">
                                                                                <div class="form-group">
                                                                                    <label for="inputWT">Başlık</label>
                                                                                    <input type="text" class="form-control" id="inputWT" name="title" value="<?= $w->title ?>" required="required">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label for="inputWT">Açıklama</label>
                                                                                    <input type="text" class="form-control" id="inputWT" name="desc" value="<?= $w->desc ?>" required="required">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <input type="file" name="img">
                                                                                </div>
                                                                                <div class="float-right">
                                                                                    <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                                                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Değiştir</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="nav-editor" role="tabpanel">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">Editörün Seçimi</h5>
                                                    <button type="submit" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#modalEditor"><i class="fa fa-plus"></i></button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table border table-bordered table-why tabledit">
                                                        <thead class="thead-light">
                                                        <tr>
                                                            <th>Görsel</th>
                                                            <th>Yazı</th>
                                                            <th>Link</th>
                                                            <th width="5%"></th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php foreach ($homeChoice as $hc) { ?>
                                                            <tr>
                                                                <td><img src="<?= base_url('assets/img/home_choice/') . $hc->img ?>" alt="image"></td>
                                                                <td><small><?= $hc->text ?></small></td>
                                                                <td><small><a href="<?= $hc->link ?>"><?= $hc->link ?></a></small></td>
                                                                <td>
                                                                    <a href="#modalEditor<?=$hc->id?>" class="text-success" data-toggle="modal"><i class="fa fa-edit"></i></a>
                                                                    <a href="<?= base_url('admin/product/delete/themeSettings/home_choice/') . $hc->id ?>" class="text-danger"><i class="far fa-trash-alt"></i></a>
                                                                </td>
                                                            </tr>
                                                            <div class="modal fade" id="modalEditor<?=$hc->id?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h6 class="modal-title">Düzenleme Ekranı</h6>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form action="<?= base_url('admin/product/edit/themeSettings/home_choice/') . $hc->id . '/home_choice'?>"  method="post" enctype="multipart/form-data">
                                                                                <div class="form-group">
                                                                                    <label for="inputWT">Başlık</label>
                                                                                    <input type="text" class="form-control" id="inputWT" name="text" value="<?= $hc->text ?>" required="required">
                                                                                </div>
                                                                               <div class="form-group">
                                                                                    <label for="inputWT">Link</label>
                                                                                    <input type="text" class="form-control" id="inputWT" name="link" required="required" value="<?= $hc->link ?>">
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <div class="custom-file ca8">
                                                                                        <input type="file" class="custom-file-input" id="cr8" name="img">
                                                                                        <label class="custom-file-label" for="cr8" data-browse="Seç">Görseli Seçmek İçin Tıkla</label>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="float-right">
                                                                                    <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                                                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Değiştir</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <div class="tab-pane fade" id="nav-one-cikanlar" role="tabpanel">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h5 class="mb-0">Öne Çıkanlar</h5>
                                                    <button type="submit" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#modalStory"><i class="fa fa-plus"></i></button>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table border table-bordered table-why tabledit">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th>Görsel</th>
                                                                <th width="5%"></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($story as $s) { ?>
                                                            <tr>
                                                                <td><img src="<?= base_url('assets/img/story/') . $s->img ?>" alt="image"></td>
                                                                <td>
                                                                    <a href="#modalStory<?=$s->id?>" class="text-success" data-toggle="modal"><i class="fa fa-edit"></i></a>
                                                                    <a href="<?= base_url('admin/product/delete/themeSettings/story/') . $s->id ?>" class="text-danger"><i class="far fa-trash-alt"></i></a>
                                                                </td>
                                                            </tr>
                                                            <div class="modal fade" id="modalStory<?=$s->id?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h6 class="modal-title">Düzenleme Ekranı</h6>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form action="<?= base_url('admin/product/edit/themeSettings/story/') . $s->id . '/story'?>"  method="post" enctype="multipart/form-data">
                                                                                <div class="form-group">
                                                                                    <input type="file" name="img">
                                                                                </div>
                                                                                <div class="float-right">
                                                                                    <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                                                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Değiştir</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>          

                    </div>
                </main>

                <div class="modal fade" id="modalSlider" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Slider Ekle</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= base_url('admin/product/insert/themeSettings/slider/sliders') ?>"  method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="inputWT">Başlık</label>
                                        <input type="text" class="form-control" id="inputWT" name="title" required="required">
                                    </div>
									<div class="form-group">
										<label for="inputWT">Etiket</label>
										<input type="text" class="form-control" id="inputWT" name="tag" required="required">
									</div>
									<div class="form-group">
										<label for="inputWT">Buton Yazısı (Boş Bırakabilirsiniz)</label>
										<input type="text" class="form-control" id="inputWT" name="buton_2_text">
									</div>
									<div class="form-group">
										<label for="inputWT">Buton Linki (Boş Bırakabilirsiniz)</label>
										<input type="text" class="form-control" id="inputWT" name="buton_2_link">
									</div>
                                    <div class="form-group">
                                        <label for="inputWD">Açıklama</label>
                                        <textarea rows="3" class="form-control" id="inputWD" name="description" required="required"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-file ca1">
                                            <input type="file" class="custom-file-input" id="cr1" name="img" required>
                                            <label class="custom-file-label" for="cr1" data-browse="Seç">Görseli Seçmek İçin Tıkla</label>
                                        </div>
                                    </div>
                                    <div class="float-right">
                                        <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Ekle</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalStory" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Öne Çıkanlar Ekle</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= base_url('admin/product/insert/themeSettings/story/story') ?>"  method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <div class="custom-file ca1">
                                            <input type="file" class="custom-file-input" id="cr1" name="img" required>
                                            <label class="custom-file-label" for="cr1" data-browse="Seç">Görseli Seçmek İçin Tıkla</label>
                                        </div>
                                    </div>
                                    <div class="float-right">
                                        <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Ekle</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalKategori" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Kategori Ekle</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= base_url('admin/product/insert/themeSettings/home_category/home_category') ?>"  method="post" enctype="multipart/form-data">
                                     <div class="form-group">
                                        <label for="inputWTT">Kategori</label>
                                        <select name="category_id" id="inputWTT" class="custom-select">
                                            <?php foreach ($categories as $c) { ?>
                                                <option value="<?= $c->id ?>"><?= $c->name ?> (Kategori)</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputWT">Link</label>
                                        <input type="text" class="form-control" id="inputWT" name="link" required="required">
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-file ca2">
                                            <input type="file" class="custom-file-input" id="cr2" name="img" required>
                                            <label class="custom-file-label" for="cr2" data-browse="Seç">Görseli Seçmek İçin Tıkla</label>
                                        </div>
                                    </div>
                                    <div class="float-right">
                                        <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Ekle</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalUrunler" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Ürün Kategorileri</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= base_url('admin/product/addHomeProduct') ?>"  method="post">
                                    <div class="form-group">
                                        <label for="inputWTT">Kategori</label>
                                        <select name="homeProduct" id="inputWTT" class="custom-select">
                                            <option value="lastProduct" selected>Son Çıkanlar</option>
                                            <option value="bestSell">Çok Satanlar</option>
                                            <?php foreach ($categories as $c) { ?>
                                                <option value="<?= $c->id ?>"><?= $c->name ?> (Kategori)</option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                      <div class="form-group">
                                        <label for="inputWT">Gösterilecek Sayı</label>
                                        <input type="input" class="form-control" id="inputWT" name="number" min="1" value="4">
                                    </div>
                                    <div class="float-right">
                                        <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Ekle</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalWhy" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Neden Biz?</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= base_url('admin/product/insert/themeSettings/why/why') ?>"  method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="inputWT">Başlık</label>
                                        <input type="text" class="form-control" id="inputWT" name="title">
                                    </div>
                                    <div class="form-group">
                                        <label for="inputWD">Açıklama</label>
                                        <textarea rows="3" class="form-control" id="inputWD" name="desc"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-file ca3">
                                            <input type="file" class="custom-file-input" id="cr3" name="img" required>
                                            <label class="custom-file-label" for="cr3" data-browse="Seç">Görseli Seçmek İçin Tıkla</label>
                                        </div>
                                    </div>
                                    <div class="float-right">
                                        <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Ekle</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalEditor" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Editörün Seçimi Ekle</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= base_url('admin/product/insert/themeSettings/home_choice/home_choice'); ?>" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="inputWT">Başlık</label>
                                        <input type="text" class="form-control" id="inputWT" name="text" required="required">
                                    </div>
                                    <div class="form-group">
                                        <label for="inputWT">Link</label>
                                        <input type="text" class="form-control" id="inputWT" name="link" required="required">
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-file ca8">
                                            <input type="file" class="custom-file-input" id="cr8" name="img" required>
                                            <label class="custom-file-label" for="cr8" data-browse="Seç">Görseli Seçmek İçin Tıkla</label>
                                        </div>
                                    </div>
                                    <div class="float-right">
                                        <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Ekle</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    document.querySelector('.ca1 .custom-file-input').addEventListener('change',function(e){
                        var fileName = document.getElementById("cr1").files[0].name;
                        var label = document.querySelector(".ca1 .custom-file-label");
                        label.innerText = fileName;
                    });


                    document.querySelector('.ca2 .custom-file-input').addEventListener('change',function(e){
                        var fileName = document.getElementById("cr2").files[0].name;
                        var label = document.querySelector(".ca2 .custom-file-label");
                        label.innerText = fileName;
                    });

                    document.querySelector('.ca3 .custom-file-input').addEventListener('change',function(e){
                        var fileName = document.getElementById("cr3").files[0].name;
                        var label = document.querySelector(".ca3 .custom-file-label");
                        label.innerText = fileName;
                    });

                     document.querySelector('.ca8 .custom-file-input').addEventListener('change',function(e){
                        var fileName = document.getElementById("cr8").files[0].name;
                        var label = document.querySelector(".ca8 .custom-file-label");
                        label.innerText = fileName;
                    });

                    document.querySelector('.ca5 .custom-file-input').addEventListener('change',function(e){
                        var fileName = document.getElementById("cr5").files[0].name;
                        var label = document.querySelector(".ca5 .custom-file-label");
                        label.innerText = fileName;
                    });

                    document.querySelector('.ca6 .custom-file-input').addEventListener('change',function(e){
                        var fileName = document.getElementById("cr6").files[0].name;
                        var label = document.querySelector(".ca6 .custom-file-label");
                        label.innerText = fileName;
                    });

                    document.querySelector('.ca7 .custom-file-input').addEventListener('change',function(e){
                        var fileName = document.getElementById("cr7").files[0].name;
                        var label = document.querySelector(".ca7 .custom-file-label");
                        label.innerText = fileName;
                    });
                </script>