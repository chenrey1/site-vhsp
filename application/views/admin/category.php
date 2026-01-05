<div id="layoutSidenav_content">
        <div class="container-fluid">
            <div class="page-title d-flex justify-content-between align-items-center">
                <h1 class="mb-0">Ürün Kategorileri</h1>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/product">Mağaza</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kategoriler</li>
                </ol>
            </nav>

            <div class="row row-cols-1 row-cols-lg-2">
                <!-- Kategori Listesi -->
                <div class="col">
                    <div class="card">
                        <div class="card-header">Kategoriler</div>
                        <div class="card-body p-3">
                            <div class="alert alert-danger">
                                <small>Dikkat! Bir kategori silmek ona ait ürünleri ve stoklarıda silecektir. <b>Bu işlem geri alınamaz.</b></small>
                            </div>

                            <ul class="list-group shadow-sm">
                                <?php foreach ($category as $c) { ?>
                                    <li class="list-group-item p-0">
                                        <!-- Ana Kategori -->
                                        <div class="d-flex justify-content-between align-items-center p-3 hover-bg-light">
                                            <div class="d-flex align-items-center gap-3">
                                                <!-- Alt kategori göstergesi -->
                                                <?php if(!empty($c->sub_categories)): ?>
                                                    <a href="javascript:void(0)"
                                                       class="text-secondary"
                                                       data-toggle="collapse"
                                                       data-target="#subCat<?= $c->id ?>"
                                                       aria-expanded="false"
                                                       style="width: 20px;">
                                                        <i class="fas fa-chevron-right transition-transform"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <div style="width: 20px;"></div>
                                                <?php endif; ?>

                                                <!-- Kategori adı -->
                                                <strong class="text-dark"><?= $c->name ?></strong>

                                                <!-- Rozetler -->
                                                <div class="d-flex gap-2">
                                                    <?php if($c->isMarketPlace == 1){ ?>
                                                        <span class="badge rounded-pill bg-warning">Pazaryeri Kategorisi</span>
                                                    <?php }else{ ?>
                                                        <span class="badge rounded-pill bg-warning">Yönetici Satışı</span>
                                                    <?php } ?>
                                                    <?php if($c->isMenu == 1): ?>
                                                        <span class="badge rounded-pill bg-success">Menüde</span>
                                                    <?php endif; ?>
                                                    <span class="badge rounded-pill bg-primary"><?= $c->product_count ?> Ürün</span>
                                                </div>
                                            </div>

                                            <div class="d-flex align-items-center gap-3">
                                                <!-- Sıralama butonları -->
                                                <div class="d-flex gap-1">
                                                    <button type="button" onclick="moveCategory(<?= $c->id ?>, 'up')" class="btn btn-link text-secondary p-1" style="min-width: 30px;">
                                                        <i class="fas fa-arrow-up"></i>
                                                    </button>
                                                    <button type="button" onclick="moveCategory(<?= $c->id ?>, 'down')" class="btn btn-link text-secondary p-1" style="min-width: 30px;">
                                                        <i class="fas fa-arrow-down"></i>
                                                    </button>
                                                </div>

                                                <!-- İşlem butonları -->
                                                <div class="d-flex gap-1">
                                                    <button type="button" onclick="editCategory(<?= $c->id ?>)" class="btn btn-link text-primary p-1" style="min-width: 30px;">
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                    <button type="button" onclick="deleteCategory(<?= $c->id ?>)" class="btn btn-link text-danger p-1" style="min-width: 30px;">
                                                        <i class="far fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Alt Kategoriler -->
                                        <?php if(!empty($c->sub_categories)): ?>
                                            <div class="collapse" id="subCat<?= $c->id ?>">
                                                <?php foreach($c->sub_categories as $sub): ?>
                                                    <div class="list-group-item border-0" style="background-color: #f8f9fa;">
                                                        <div class="d-flex justify-content-between align-items-center ps-5">
                                                            <div class="d-flex align-items-center gap-3">
                                                                <span class="text-dark"><?= $sub->name ?></span>

                                                                <!-- Rozetler -->
                                                                <div class="d-flex gap-2">
                                                                    <?php if($sub->isMarketPlace == 1){ ?>
                                                                        <span class="badge rounded-pill bg-warning">Pazaryeri Kategorisi</span>
                                                                    <?php }else{ ?>
                                                                        <span class="badge rounded-pill bg-warning">Yönetici Satışı</span>
                                                                    <?php } ?>
                                                                    <?php if($sub->isMenu == 1): ?>
                                                                        <span class="badge rounded-pill bg-success">Menüde</span>
                                                                    <?php endif; ?>
                                                                    <span class="badge rounded-pill bg-primary"><?= $sub->product_count ?> Ürün</span>
                                                                </div>
                                                            </div>

                                                            <div class="d-flex align-items-center gap-3">
                                                                <!-- Sıralama butonları -->
                                                                <div class="d-flex gap-1">
                                                                    <button type="button" onclick="moveCategory(<?= $sub->id ?>, 'up')" class="btn btn-link text-secondary p-1" style="min-width: 30px;">
                                                                        <i class="fas fa-arrow-up"></i>
                                                                    </button>
                                                                    <button type="button" onclick="moveCategory(<?= $sub->id ?>, 'down')" class="btn btn-link text-secondary p-1" style="min-width: 30px;">
                                                                        <i class="fas fa-arrow-down"></i>
                                                                    </button>
                                                                </div>

                                                                <!-- İşlem butonları -->
                                                                <div class="d-flex gap-1">
                                                                    <button type="button" onclick="editCategory(<?= $sub->id ?>)" class="btn btn-link text-primary p-1" style="min-width: 30px;">
                                                                        <i class="fa fa-edit"></i>
                                                                    </button>
                                                                    <button type="button" onclick="deleteCategory(<?= $sub->id ?>)" class="btn btn-link text-danger p-1" style="min-width: 30px;">
                                                                        <i class="far fa-trash-alt"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Kategori Ekleme Formu -->
                <div class="col">
                    <div class="card">
                        <div class="card-header">Kategori Ekle</div>
                        <div class="card-body">
                            <form action="<?= base_url('admin/product/insert/category/category/category') ?>" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Kategori Adı" name="name" onchange="doSlug()" required id="category">
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Kategori Linki (Örn. minecraft-premium)" name="slug" required id="slug">
                                </div>
                                <div class="form-group">
                                    <select id="select1" class="custom-select" name="mother_category_id">
                                        <option value="0" selected="">Ana Kategori</option>
                                        <?php foreach ($categories as $c) { ?>
                                            <option value="<?= $c->id ?>"><?= $c->name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select id="select2" class="custom-select" name="isMenu">
                                        <option value="1">Menüde Gözüksün</option>
                                        <option value="0">Menüde Gözükmesin</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select id="select3" class="custom-select" name="isMarketPlace">
                                        <option value="1">Pazaryeri Kategorisi</option>
                                        <option value="0">Sadece Yönetici Satışı</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="inputW1" name="description" placeholder="Örn: Minecraft 2011 yılında çıkan ve günlük milyonlarca oyuncusu bulunan..." required="required">
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFileLangHTML" name="img">
                                    <label class="custom-file-label" for="customFileLangHTML" data-browse="Seç">Resim Eklemek İçin Tıkla</label>
                                </div>
                                <hr>
                                <button class="btn btn-primary float-right"><i class="fa fa-plus"></i> Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Düzenleme Modalı -->
        <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Düzenleme Ekranı</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editCategoryForm" action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="editName">Kategori Adı</label>
                                <input type="text" class="form-control" id="editName" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="editSlug">Slug</label>
                                <input type="text" class="form-control" id="editSlug" name="slug" required>
                            </div>
                            <div class="form-group">
                                <label for="editDescription">Kategori Açıklaması</label>
                                <input type="text" class="form-control" id="editDescription" name="description" required>
                            </div>
                            <div class="form-group">
                                <label>Mevcut Resim</label>
                                <div id="currentImageContainer" class="mb-2">
                                    <img id="currentImage" src="" alt="Kategori Resmi" style="max-width: 100px; display: none;" class="img-thumbnail">
                                    <p id="noImageText" class="text-muted" style="display: none;">Yüklenmiş bir resim bulunamadı.</p>
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="editImage" name="img">
                                    <label class="custom-file-label" for="editImage" data-browse="Seç">Yeni Resim Seç</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Durum</label>
                                <select class="custom-select" name="isMenu" id="editIsMenu">
                                    <option value="1">Menüde Gözüksün</option>
                                    <option value="0">Menüde Gözükmesin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Pazaryeri Durumu</label>
                                <select class="custom-select" name="isMarketPlace" id="editIsMarketPlace">
                                    <option value="1">Pazaryeri Kategorisi</option>
                                    <option value="0">Sadece Yönetici Satışı</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Ana Kategori Seçimi</label>
                                <select class="custom-select" name="mother_category_id" id="editMotherCategory">
                                    <option value="0">Ana Kategori</option>
                                    <?php foreach ($category as $cat): ?>
                                        <option value="<?= $cat->id ?>"><?= $cat->name ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <hr>
                            <div class="text-right">
                                <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Değiştir</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <style>
        .hover-bg-light:hover {
            background-color: #f8f9fa;
        }

        .transition-transform {
            transition: transform 0.2s;
        }

        [aria-expanded="true"] .fa-chevron-right {
            transform: rotate(90deg);
        }

        .gap-1 { gap: 0.25rem; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 1rem; }

        .btn-link {
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .btn-link:hover {
            opacity: 0.7;
        }
    </style>

    <script>

        function editCategory(id) {
            // Ajax ile kategori bilgilerini çek
            $.ajax({
                url: '<?= base_url(); ?>admin/product/getCategoryData/' + id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Form action URL'sini güncelle
                    $('#editCategoryForm').attr('action', '<?= base_url("admin/product/edit/category/category/"); ?>' + id + '/category');

                    // Form alanlarını doldur
                    $('#editName').val(data.name);
                    $('#editSlug').val(data.slug);
                    $('#editDescription').val(data.description);
                    $('#editIsMenu').val(data.isMenu);
                    $('#editIsMarketPlace').val(data.isMarketPlace);
                    $('#editMotherCategory').val(data.mother_category_id);

                    // Resim kontrolü ve gösterimi
                    if (data.img) {
                        $('#currentImage').attr('src', '<?= base_url("assets/img/category/"); ?>' + data.img).show();
                        $('#noImageText').hide();
                    } else {
                        $('#currentImage').hide();
                        $('#noImageText').show();
                    }

                    // Kendisini ana kategori seçeneklerinden kaldır
                    $('#editMotherCategory option').show();
                    $('#editMotherCategory option[value="' + id + '"]').hide();

                    // Dosya seçimi etiketini güncelle
                    $('.custom-file-label').html('Yeni Resim Seç');

                    // Modalı göster
                    $('#editCategoryModal').modal('show');
                },
                error: function() {
                    alert('Kategori bilgileri alınırken bir hata oluştu');
                }
            });
        }

        // Dosya seçildiğinde etiketin güncellenmesi
        $(document).on('change', '.custom-file-input', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName || 'Yeni Resim Seç');
        });


        $(document).ready(function() {
            // Collapse animasyonu
            $('.collapse').on('show.bs.collapse', function () {
                $(this).closest('.list-group-item').find('.fa-chevron-right').css('transform', 'rotate(90deg)');
            });

            $('.collapse').on('hide.bs.collapse', function () {
                $(this).closest('.list-group-item').find('.fa-chevron-right').css('transform', 'rotate(0deg)');
            });
        });

        // Silme fonksiyonu
        function deleteCategory(id) {
            if(confirm('Bu kategoriyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!')) {
                window.location.href = '<?= base_url("admin/product/deleteCategory/"); ?>' + id;
            }
        }

        // Sıralama fonksiyonu
        function moveCategory(categoryId, direction) {
            $('button').prop('disabled', true);

            $.ajax({
                url: '<?= base_url(); ?>admin/product/ajaxMoveCategory',
                type: 'POST',
                data: {
                    category_id: categoryId,
                    direction: direction,
                    '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        window.location.reload();
                    } else {
                        alert(response.message || 'Bir hata oluştu');
                        $('button').prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Ajax Error:', error);
                    alert('İşlem sırasında bir hata oluştu');
                    $('button').prop('disabled', false);
                }
            });

            return false;
        }

        function doSlug() {
            var title = $("#category").val();
            title = str(title);
            $('input#slug').val(title);
        }

        function str(str) {
            str = str.replace(/^\s+|\s+$/g, ''); // trim
            str = str.toLowerCase();

            // remove accents, swap ñ for n, etc
            var from = "ãàáäâẽèéëêìíïîıõòóöôùúüûñç·/_,:;";
            var to = "aaaaaeeeeeiiiiiooooouuuunc-------";
            for (var i = 0, l = from.length; i < l; i++) {
                str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
            }

            str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

            return str;
        };
    </script>
</div>