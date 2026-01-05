            <div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">Sayfa Düzenle</h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url('admin/product/themeSettings') ?>">Tema</a></li>
                                <li class="breadcrumb-item"><a href="<?= base_url('admin/product/pages') ?>">Sayfalar</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Sayfa Düzenle (<?= $page->title ?>)</li>
                            </ol>
                        </nav>

                        <div class="row row-cols-lg-1">

                            <div class="col">
                                <div class="card">
                                    <div class="card-header"><?= $page->title ?></div>
                                    <div class="card-body p-3">
                                        <form action="<?= base_url('admin/product/edit/pages/pages/') . $page->id ?>" method="POST">
                                            <div class="form-group">
												<textarea id="editor" rows="10" class="form-control" name="content"><?= $page->content ?></textarea>
                                            </div>
                                            <div class="form-group">
                                                <textarea rows="10" class="form-control" name="meta" placeholder="Sayfa Meta Açıklaması (Sayfanın içeriğini Google'a bildirebileceğiniz alan)"><?= $page->meta ?></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-primary float-right"><i class="far fa-save"></i> Kaydet</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </main>

				<script>
					ClassicEditor
							.create( document.querySelector( '#editor' ) )
							.catch( error => {
								console.error( error );
							} );
				</script>
