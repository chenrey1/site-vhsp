
            <div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5>Ürün Bilgisi</h5>
                            <h3 class="text-muted"><?= $product->name ?></h3>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/products">Mağaza</a></li>
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/products">Ürünler</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Ürün Bilgisi (<?= $product->name ?>)</li>
                            </ol>
                        </nav>

                        <div class="row">

                            <div class="col-12 col-md-10 col-lg-6 mx-auto">
                                <div class="card">
                                    <div class="card-header">Ürün Bilgileri</div>
                                    <div class="card-body">
                                        <a href="#modalPicture" class="btn btn-primary btn-pmc" data-toggle="modal"><i class="fa fa-pen"></i></a>
                                        <img src="<?= base_url('assets/img/product/') . $product->img ?>" class="product-info-img">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Ürün Adı:
                                                <strong><?= $product->name ?></strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Fiyat:
                                                <strong><?= $product->price ?>₺</strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Stok:
                                                <strong class="text-primary"><?php $stok = $this->db->where('product_id', $product->id)->count_all_results('stock'); echo $stok; ?></strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Kategori:
                                                <strong><?php $category = $this->db->where('id', $product->category_id)->get('category')->row(); echo $category->name;  ?></strong>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-10 col-lg-6 mx-auto">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        Düzenle
                                        <a href="#" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#modalProductDel"><i class="far fa-trash-alt"></i> Sil</a>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?= base_url() ?>admin/product/edit/products/product/<?= $product->id ?>/product" method="POST">
                                            <div class="form-group">
                                                <label for="inputPName">Ürün Adı:</label>
                                                <input type="text" class="form-control" id="inputPName" value="<?= $product->name ?>" name="name" onchange="doSlug()" required>
                                            </div>
											<div class="form-group row">
												<label for="inputPName" class="col-sm-2 col-form-label">Slug:</label>
												<div class="col-sm-10">
													<input type="text" class="form-control" value="<?= $product->slug ?>" name="slug" id="productSlug" required>
												</div>
											</div>
                                            <div class="form-group">
                                                <label for="inputPPrice">Fiyat:</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" aria-label="Ürün Fiyatı" aria-describedby="basic-addon1" value="<?= $product->price ?>" name="price" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon1">₺</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputPDiscount">İndirimli Fiyat (TL):</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" aria-label="İndirimli Fiyat" aria-describedby="basic-addon2" value="<?= $product->discount ?>" name="discount" placeholder="0 veya boş bırakın">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="basic-addon2">₺</span>
                                                    </div>
                                                </div>
                                                <small class="text-muted">İndirim yapmak istemiyorsanız 0 yazın veya boş bırakın. İndirimli fiyat, normal fiyattan düşük olmalıdır.</small>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputPCate">Kategori:</label>
                                                <select class="custom-select" id="inputPCate" name="category_id" required>
                                                    <option value="<?= $category->id ?>" selected><?= $category->name ?></option>
                                                    <?php foreach ($categories as $a) { ?>
                                                        <option value="<?= $a->id ?>"><?= $a->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputPText">Ürün Bilgisi:</label>
                                                <textarea name="desc" class="form-control" id="editor" required><?= ($product->desc) ? $product->desc : "Bu Ürün İçin Açıklama Girilmedi." ?></textarea>
                                            </div>
											<div class="form-group">
												<label for="inputPCate">Stok Durumu:</label>
												<select class="custom-select" name="isStock" required disabled="">
													<option value="<?= $product->isStock ?>" selected>
														<?php if ($product->isStock == 1) {
														echo "Stoklu Ürün"; ?>
													<?php }else {
														echo "Stoksuz Ürün"; ?>
													<?php } ?>
												</select>
                                                <small>Değiştirildiğinde sistemsel hatalara sebep olabileceğinden bu özellik devre dışı bırakıldı.</small>
											</div>
                                            <div class="form-group">
                                                <label for="inputPCate">Ürün Durumu:</label>
                                                <select class="custom-select" name="isActive" required>
                                                    <option value="<?= $product->isActive ?>" selected>
                                                    <?php if ($product->isActive == 1) {
                                                        echo "Aktif"; ?>
                                                        <option value="2">DeAktif</option>
                                                    <?php }else { 
                                                        echo "Deaktif"; ?>
                                                        <option value="1">Aktif</option>
                                                    <?php }

                                                        ?>
                                                </select>
                                            </div>

											<div class="form-group row">
												<label for="product_provider" class="col-sm-2 col-form-label">Ürün Tedarikçisi:</label>
												<div class="col-sm-10">
													<select class="custom-select" id="product_provider" name="product_provider" onchange="getCategories(this, <?= $product->game_code ?>, <?= $product->product_code ?>)">
														<option selected value="null">Tedarikçi Kullanma</option>
														<option value="turkpin">Turkpin</option>
														<option value="pinabi">Pinabi</option>

                                                        <?php foreach ($product_providers as $provider) { ?>
                                                            <option value="<?= $provider->id ?>"><?= $provider->name ?></option>
                                                        <?php } ?>
													</select>
												</div>
											</div>
											<div id="product_provider_area"></div>

                                            <button class="btn btn-primary float-right" id="submit_button"><i class="far fa-save"></i> Güncelle</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </main>

                <div class="modal fade" id="modalPicture" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Ürün Görselini Değiştir</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= base_url('admin/product/edit/products/product/') . $product->id . '/product' ?>" method="post" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFileLangHTML" name="img" required>
                                            <label class="custom-file-label" for="customFileLangHTML" data-browse="Seç">Görseli Seçmek İçin Tıkla</label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block"><i class="far fa-save"></i> Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalProductDel" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Dikkat</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="">
                                    <p><strong><?= $product->name ?></strong> ürününü silmek ona bağlı tüm stoklarıda siler. Devam etmek istediğine emin misin?</p>
                                    <div class="float-right">
                                        <button type="button" class="btn btn-link btn-sm" data-dismiss="modal">İptal</button>
                                        <a href="<?= base_url() ?>admin/product/disableProduct/<?= $product->id ?>" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i> Sil</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

<script>
$(document).ready(function() {
    // 1. Summernote Başlatma
    var $editor = $('#editor');
    
    $editor.summernote({
        placeholder: 'Ürün açıklamasını buraya yazın...',
        tabsize: 2,
        height: 300,
        lang: 'tr-TR',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']], // İşte o renk paleti!
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    // 2. DÜZENLEME MODU FIX (HTML kodlarını görselleştirir)
    // Kutunun içindeki veriyi al ve Summernote formatına zorla çevir
    var currentContent = $editor.val();
    if (currentContent.length > 0) {
        $editor.summernote('code', currentContent);
    }
});
</script>
<script>
	function doSlug() {
		var title = $("#productName").val();
		console.log(title);
		title = str(title);
		$('#productSlug').val(title);
	}

	function str(str) {
		str = str.replace(/^\s+|\s+$/g, ''); // trim
		str = str.toLowerCase();

		// remove accents, swap ñ for n, etc
		var from = "ãàáäâẽèéëêìíïîıõòóöôùúüûñç·/_,:;şğ";
		var to   = "aaaaaeeeeeiiiiiooooouuuunc------sg";
		for (var i=0, l=from.length ; i<l ; i++) {
			str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
		}

		str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
			.replace(/\s+/g, '-') // collapse whitespace and replace by -
			.replace(/-+/g, '-'); // collapse dashes

		return str;
	}

	$("#product_provider").val("<?= $product->product_provider ?>");

    function getCategories(e, game_code, product_code) {
        console.log(e)
        console.log(e.value)
        var product_provider_area = document.querySelector('div[id="product_provider_area"]');
        product_provider_area.innerHTML = "";
        if (e.value == "null") {
            hideSubmitSpinner();
            return;
        }

        showSubmitSpinner();
        $.getJSON({
            url: "<?= base_url() ?>admin/API/getProviderCategories",
            type: "POST",
            data: {provider_id: e.value}
        }, function(response) {
            $('#provider_error').html(response.message);
            if (response.status) {
                var product_provider_area = document.querySelector('div[id="product_provider_area"]');
                var content = `
                <div class="form-group row">
                    <label for="game_code" class="col-sm-2 col-form-label">Ürün Kategorisi:</label>
                    <div class="col-sm-10">
                        <select class="custom-select" id="game_code" name="game_code" onchange="getProducts(this)">
                            <option selected disabled value="">Kategori Seçin</option>
                `;

                $.each(response.categories, function(key, val) {
                    if (val.id == game_code) {
                        content += `<option value="${val.id}" selected>${val.name}</option>`;
                    } else {
                        content += `<option value="${val.id}">${val.name}</option>`;
                    }
                });

                content += `
                        </select>
                    </div>
                </div>
                `;

                product_provider_area.innerHTML = content;
                if (game_code != null) {
                    getProducts({
                        value: game_code
                    }, product_code);
                }
            } else {
                sendToast("Hata", response.message);
            }
            hideSubmitSpinner();
        });
    }

    function getProducts(e, product_code) {
        var product_provider_area = document.querySelector('div[id="product_provider_area"]');
        if (product_provider_area.querySelectorAll("#product_area").length > 0) {
            product_provider_area.querySelector("#product_area").remove();
        }

        showSubmitSpinner();
        $.getJSON({
            url: "<?= base_url() ?>admin/API/getProviderProducts",
            type: "POST",
            data: {
                provider_id: $('#product_provider').val(),
                category_id: e.value
            }
        }, function(response) {
            $('#provider_error').html(response.message);
            if (response.status) {
                var content = `
                <div class="form-group row" id="product_area">
                    <label for="productSelected" class="col-sm-2 col-form-label">Ürün:</label>
                    <div class="col-sm-10">
                        <select class="custom-select" id="productSelected" name="product_code">
                            <option selected disabled value="">Ürün Seçin</option>
                `;

                $.each(response.products, function(key, val) {
                    if (val.id == product_code) {
                        content += `
                            <option value="${val.id}" selected>${val.name} (${val.price} TL)</option>
						`;
                    } else {
                        content += `
                            <option value="${val.id}">${val.name} (${val.price} TL)</option>
						`;
                    }
                });

                content += `
                        </select>
                    </div>
                </div>
                `;

                appendHTML(product_provider_area, content);
            } else {
                sendToast("Hata", response.message);
            }
            hideSubmitSpinner();
        });
    }

	function showSubmitSpinner() {
		var submit_button = document.getElementById("submit_button");
		submit_button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Yükleniyor...';
		submit_button.disabled = true;
	}

	function hideSubmitSpinner() {
		var submit_button = document.getElementById("submit_button");
		submit_button.innerHTML = '<i class="far fa-save"></i> Güncelle';
		submit_button.disabled = false;
	}

	function appendHTML(el, str) {
		var div = document.createElement('div');
		div.innerHTML = str;
		while (div.children.length > 0) {
			el.appendChild(div.children[0]);
		}
	}
</script>
<style>
    /* Yazı yazılan alanın rengini siteyle birebir aynı yapıyoruz */
    .note-editable {
        background-color: #181818 !important; 
        color: #ffffff !important;           /* Yazılar varsayılan beyaz */
        font-family: 'Open Sans', sans-serif;
    }

    /* Editörün dış çerçevesini sitenin koyu temasına uyduralım */
    .note-editor.note-frame {
        border: 1px solid #333 !important;
        background-color: #181818 !important;
    }

    /* Üstteki araç çubuğu (Toolbar) biraz daha belirgin kalsın */
    .note-toolbar {
        background-color: #222222 !important;
        border-bottom: 1px solid #333 !important;
    }

    /* Butonları biraz koyulaştır ki sırıtmmasın */
    .note-btn {
        background-color: #333 !important;
        color: #ddd !important;
        border: 1px solid #444 !important;
    }

    /* Butonların üzerine gelince (hover) rengi */
    .note-btn:hover {
        background-color: #444 !important;
        color: #fff !important;
    }
</style>
<style>
    /* 1. Sitenin açıklama alanındaki tüm yazılara tam opaklık veriyoruz */
    .product-description-area, 
    .product-description-area * {
        opacity: 1 !important; /* Eğer şeffaflık varsa iptal eder */
        -webkit-font-smoothing: antialiased; /* Yazıyı keskinleştirir */
    }

    /* 2. Özel renk atanmış (span ve font) etiketlerini sitenin gri renginden kurtar */
    .product-description-area span[style*="color"],
    .product-description-area font[color] {
        display: inline-block; /* Bazı CSS çakışmalarını önler */
    }

    /* 3. Özellikle BEYAZ seçtiğin yerleri zorla parlat */
    .product-description-area span[style*="color:#ffffff"],
    .product-description-area span[style*="color:rgb(255, 255, 255)"],
    .product-description-area span[style*="color: white"],
    .product-description-area font[color="#ffffff"] {
        color: #ffffff !important;
        text-shadow: 0 0 1px rgba(255,255,255,0.2); /* Hafif bir parlama efekti ekler */
    }
</style>
<style>
    /* fp-product-context içindeki tüm metinleri bembeyaz yapmaya zorla */
    .fp-product-context, 
    .fp-product-context p, 
    .fp-product-context span, 
    .fp-product-context font {
        color: #ffffff !important; /* Griyi beyazla değiştir */
        opacity: 1 !important;    /* Solukluğu kaldır */
        filter: none !important;  /* Filtreleri temizle */
    }

    /* Eğer sistem 'style' etiketlerini sildiyse (xss="removed"), biz yine de beyaz yapıyoruz */
    [xss="removed"] {
        color: #ffffff !important;
        opacity: 1 !important;
    }

    /* Seçtiğin diğer renklerin (sarı vs.) bozulmaması için */
    .fp-product-context [style*="color"]:not([style*="ffffff"]) {
        color: inherit !important;
    }
    /* Bu kod xss="removed" olsa bile her şeyi bembeyaz parlatır */
.fp-product-context, 
.fp-product-context p, 
.fp-product-context span, 
.fp-product-context font,
[xss="removed"] {
    color: #ffffff !important; 
    opacity: 1 !important;
    filter: none !important;
    -webkit-font-smoothing: antialiased;
}

/* Diğer renk verdiğin yazılar (Sarı vs.) varsa onları korumak için */
.fp-product-context span[style*="color"]:not([style*="ffffff"]) {
    color: inherit !important;
}
</style>