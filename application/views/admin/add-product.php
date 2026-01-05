
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Ürün Ekle</h5>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="
							<?= base_url(); ?>admin/products">Mağaza </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="
							<?= base_url(); ?>admin/products">Ürünler </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Ürün Ekle</li>
                </ol>
            </nav>
            <div class="card">
                <div class="card-body">
                    <form action="
						<?= base_url(); ?>admin/product/insert/products/product/product" method="POST" enctype="multipart/form-data">
                        <div class="form-group row">
                            <label for="inputPName" class="col-sm-2 col-form-label">Ürün Adı:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="name" id="productName" onchange="doSlug()" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPName" class="col-sm-2 col-form-label">Slug:</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="slug" id="productSlug" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPPrice" class="col-sm-2 col-form-label">Fiyat:</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="inputPPrice" aria-label="Ürün Fiyatı" aria-describedby="basic-addon1" name="price" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon1">₺</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPCate" class="col-sm-2 col-form-label">Kategori:</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="inputPCate" name="category_id" required>
                                    <option selected disabled value="">Ürün Kategorisini Seçiniz</option> <?php foreach ($category as $c) { ?> <option value="
													<?= $c->id ?>"> <?= $c->name ?> </option> <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPCate" class="col-sm-2 col-form-label">Stok Durumu:</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="inputPCate" name="isStock" required>
                                    <option selected disabled value="">Ürün Stok Bilgisini Girin</option>
                                    <option value="1">Stoklu Ürün</option>
                                    <option value="0">Stoksuz Ürün</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="inputPImg" class="col-sm-2 col-form-label">Ürün Görseli:</label>
                            <div class="col-sm-10">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFileLangHTML" name="img" required>
                                    <label class="custom-file-label" for="customFileLangHTML" data-browse="Seç">Ürün Görselini Seçmek İçin Tıkla</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">
                                <strong>Geri Bildirim Modülü:</strong>
                            </label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="text[]" placeholder="Örneğin: PUBG ID">
                            </div>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="text[]" placeholder="Örneğin: ZYNGA Kullanıcı Adı">
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="text[]" placeholder="Örneğin: ZYNGA Şifresi">
                            </div>
                        </div>
                        <div id="fields-ids-area"></div>
                        <div class="form-group row">
                            <label for="inputPText" class="col-sm-2 col-form-label">Ürün Bilgisi: <small>(HTML)</small>
                            </label>
                            <div class="col-sm-10">
                                <div class="form-group mb-4" style="border: 1px solid #444; padding: 15px; border-radius: 5px; background: #1a1a1a;">
                                    
    <label style="color: #00ff00; font-weight: bold; margin-bottom: 10px; display: block;">🔥 ÖNE ÇIKARMA SEÇENEĞİ</label>
    <div class="form-check custom-checkbox">
        <input type="checkbox" class="form-check-input" name="is_best_seller" value="1" id="bestSellerCheck" style="width: 18px; height: 18px; cursor: pointer;">
        <label class="form-check-label" for="bestSellerCheck" style="color: #fff; cursor: pointer; padding-left: 10px;">
            Bu ürünü ana sayfada "Çok Satanlar" listesinde göster.
        </label>
    </div>
</div>
<textarea id="editor" name="desc"><?= isset($product->desc) ? $product->desc : '' ?></textarea>                            </div>
<div class="form-check mt-3 mb-3">
    <input class="form-check-input" type="checkbox" name="is_best_seller" value="1" id="checkBest">
    <label class="form-check-label" for="checkBest" style="color: #fff; font-weight: bold;">
        🔥 Bu Ürünü Çok Satanlar Listesine Ekle
    </label>
</div>
                        </div>
                        <div class="form-group row">
                            <label for="product_provider" class="col-sm-2 col-form-label">Ürün Tedarikçisi:</label>
                            <div class="col-sm-10">
                                <select class="custom-select" id="product_provider" name="product_provider" onchange="getCategories(this)">
                                    <option selected value="null">Tedarikçi Kullanma</option>
                                    <option value="turkpin">Turkpin</option>
                                    <option value="pinabi">Pinabi</option> <?php foreach ($product_providers as $provider) { ?> <option value="
																	<?= $provider->id ?>"> <?= $provider->name ?> </option> <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="text-danger" id="provider_error"></div>
                        <div id="product_provider_area"></div>
                        <button class="btn btn-primary float-right" id="submit_button">
                            <i class="fa fa-plus"></i> Ekle </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

<script>

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

	function getCategories(e) {
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
                    content += `<option value="${val.id}">${val.name}</option>`;
                });
                
                content += `
                        </select>
                    </div>
                </div>
                `;
                
                product_provider_area.innerHTML = content;
            } else {
                sendToast("Hata", response.message);
            }
            hideSubmitSpinner();
        });

	}

	function getProducts(e) {
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
                    let dataFieldsAttr = '';
                    if(val.required_fields) {
                        dataFieldsAttr = ` data-fields='${JSON.stringify(val.required_fields)}'`;
                    }
                    content += `
                        <option value="${val.id}"${dataFieldsAttr}>${val.name} (${val.price} TL)</option>
                    `;
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
		submit_button.innerHTML = '<i class="fa fa-plus"></i> Ekle';
		submit_button.disabled = false;
	}

	function appendHTML(el, str) {
		var div = document.createElement('div');
		div.innerHTML = str;
		while (div.children.length > 0) {
			el.appendChild(div.children[0]);
		}
	}

    $('#productSelected').on('change', function() {
        $('#fields-ids-area').empty();
        $('input[name="text[]"]').val('');

        var selectedProductId = $(this).val();
        if (!selectedProductId) {
            return;
        }

        var requiredFields = $('option[value="'+selectedProductId+'"]').data("fields");

        if (requiredFields && requiredFields.length > 0) {
            requiredFields.forEach(function(field, index) {
                var $textInput = $('input[name="text[]"]').eq(index); 
                if ($textInput.length > 0) {
                    $textInput.val(field.name);
                }

                var $hiddenInput = $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', 'field_ids[]')
                    .val(field.id);

                $('#fields-ids-area').append($hiddenInput);
            });
        }
    });
</script>
<link href="<?= base_url('assets/future/css/summernote-lite.css') ?>" rel="stylesheet">

<script src="<?= base_url('assets/future/js/summernote-lite.js') ?>"></script>

<script src="<?= base_url('assets/future/js/lang/summernote-tr-TR.js') ?>"></script>
<script>
    // Sayfadaki diğer JS hataları bu kodu durdurmasın diye 'try' içine alıyoruz
    try {
        $(document).ready(function() {
            var $editor = $('#editor');
            
            // Eğer editör kütüphanesi yüklendiyse başlat
            if ($.fn.summernote) {
                $editor.summernote({
                    height: 300,
                    lang: 'tr-TR',
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview']]
                    ]
                });

                // Veritabanından gelen veriyi zorla görsel hale getir
                var hamVeri = $editor.val();
                if (hamVeri.includes('<')) {
                    $editor.summernote('code', hamVeri);
                }
            }
        });
    } catch (e) {
        console.warn("Summernote başlatılırken bir hata oluştu ama görmezden gelindi:", e);
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
</style>
<style>
    /* Editör içindeki yazı alanını siteyle aynı yap (Kar beyazı görünsün) */
    .note-editable {
        background-color: #181818 !important; 
        color: #ffffff !important;
        -webkit-font-smoothing: antialiased;
    }

    /* Beyaz seçilen yazıları solukluktan kurtar ve parlat */
    .note-editable span[style*="color:#ffffff"],
    .note-editable span[style*="color: rgb(255, 255, 255)"] {
        color: #ffffff !important;
        text-shadow: 0 0 1px rgba(255,255,255,0.3);
    }
</style>
<style>
    /* 1. Dropdown menüyü beyaz yap ve görünür kıl */
    .note-dropdown-menu {
        background-color: #ffffff !important;
        border: 1px solid #a9a9a9 !important;
        min-width: 340px !important;
        padding: 10px !important;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3) !important;
        z-index: 999999 !important; /* Her şeyin üstünde dursun */
    }

    /* 2. O siyah/boş kutuların içindeki renkleri geri getir */
    .note-color-btn {
        width: 22px !important;
        height: 22px !important;
        padding: 0 !important;
        margin: 2px !important;
        border: 1px solid #ccc !important;
        cursor: pointer;
        /* KRİTİK NOKTA: Rengi HTML'den çekmesi için bunu inherit yapıyoruz */
        background-color: inherit; 
    }

    /* Üzerine gelince belirginleşsin */
    .note-color-btn:hover {
        border: 2px solid #000 !important;
        transform: scale(1.2);
    }

    /* 3. Renk başlıklarını (Yazı Rengi vs.) okunur yap */
    .note-palette-title {
        color: #333 !important;
        font-size: 14px !important;
        font-weight: bold !important;
        margin: 5px 0 !important;
        border-bottom: 1px solid #eee;
        display: block !important;
    }

    /* 4. Reset butonunu düzelt */
    .note-color-reset {
        padding: 5px !important;
        background: #f1f1f1 !important;
        color: #333 !important;
        margin-top: 5px !important;
        display: block !important;
        text-align: center;
        border-radius: 4px;
        cursor: pointer;
    }
    
    /* 5. Satırların yan yana düzgün durmasını sağla */
    .note-color-row {
        display: flex !important;
        flex-wrap: nowrap !important;
    }
</style>