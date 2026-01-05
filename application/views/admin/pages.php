    <div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid">

                        <div class="page-title d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Sayfalar</h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url('admin/product/themeSettings') ?>">Tema</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Sayfalar</li>
                            </ol>
                        </nav>

                        <div class="row row-cols-lg-2">
                            <div class="col">
                                <div class="card">
                                    <div class="card-header">Sayfalar</div>
                                    <div class="card-body p-3">
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($pages as $page) { ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?= $page->title ?>
                                                <span>
                                                    <a href="<?= base_url('admin/product/editPage/' . $page->id) ?>" class="btn btn-link btn-sm"><i class="far fa-edit"></i></a>
                                                    <a href="<?= base_url('admin/product/delete/pages/pages/' . $page->id) ?>" class="btn btn-link btn-sm text-danger"><i class="far fa-trash-alt"></i></a>
                                                </span>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col">
                                <div class="card">
                                    <div class="card-header">Sayfa Ekle</div>
                                    <div class="card-body">
                                        <form action="<?= base_url('admin/product/insert/pages/pages') ?>" method="POST">
                                            <div class="form-group">
                                                <input type="text" class="form-control" placeholder="Sayfa Adı" name="title" id="title" onchange="doSlug()" required>
                                            </div>
											<div class="form-group">
												<textarea id="editor" rows="10" class="form-control" name="content"></textarea>
											</div>
											<div class="form-group">
												<input type="text" class="form-control slug" placeholder="Sayfa Linki" name="slug" id="slug" required>
											</div>
                                            <div class="form-group">
                                                <textarea rows="10" class="form-control" name="meta" placeholder="Sayfa Meta Açıklaması (Sayfanın içeriğini Google'a bildirebileceğiniz alan)"></textarea>
                                            </div>
                                            <button class="btn btn-primary float-right"><i class="fa fa-plus"></i> Ekle</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </main>

		<script>
			function doSlug()
			{
				var title = $("#title").val();
				title =	str(title);
				$('input#slug').val(title);
			}

			function str(str) {
				str = str.replace(/^\s+|\s+$/g, ''); // trim
				str = str.toLowerCase();

				// remove accents, swap ñ for n, etc
				var from = "ãàáäâẽèéëêìíïîıõòóöôùúüûñç·/_,:;";
				var to   = "aaaaaeeeeeiiiiiooooouuuunc-------";
				for (var i=0, l=from.length ; i<l ; i++) {
					str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
				}

				str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
						.replace(/\s+/g, '-') // collapse whitespace and replace by -
						.replace(/-+/g, '-'); // collapse dashes

				return str;
			};
		</script>

        <script>
$(document).ready(function() {
    $('#editor').summernote({
        placeholder: 'Ürün açıklamasını buraya yazın...',
        tabsize: 2,
        height: 300,
        lang: 'tr-TR', // Türkçe dil desteği
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
});
        </script>

