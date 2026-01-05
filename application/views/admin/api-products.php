<div id="layoutSidenav_content">

	<main>
		<div class="container-fluid">

			<div class="page-title">
				<h5 class="mb-0">API Ürünleri</h5>
			</div>

			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
					<li class="breadcrumb-item active" aria-current="page">API Ürünleri</li>
				</ol>
			</nav>


			<div class="card mb-4">
				<div class="card-body">
					<h6>Kar Payı Belirle (%)</h6>
					<p class="text-muted">
						Belirlediğiniz kar payı tüm ürünlere uygulanacaktır.
					</p>
					<div class="row">
						<div class="col-lg-4">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" style="border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important" id="basic-addon1">%</span>
								</div>
								<input type="text" class="form-control" style="border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important" placeholder="Kar Payını Yazınız" aria-label="Kar Payı" aria-describedby="basic-addon1" name="dividend">
							</div>
						</div>
					</div>
					<div class="text-danger">
						<i class="fas fa-info-circle"></i> Kar payı belirlediğinizde ürün fiyatları otomatik olarak değişecektir.
					</div>
				</div>
			</div>


			<h5 class="mb-3">Ürünler</h5>

			<?php
				foreach ($categories as $category) {
					if (count($category->products) == 0) continue;
			?>
			<div>
				<h6>
					<?= $category->name; ?>
					<div class="custom-control custom-checkbox d-inline float-right">
						<input type="checkbox" class="custom-control-input" id="customCheckAll<?= $category->id ?>" check-all-category="<?= $category->id ?>">
						<label class="custom-control-label" for="customCheckAll<?= $category->id ?>">Tümünü Seç</label>
					</div>
				</h6>
				<hr>
				<div class="api-product-item-list">
					<?php
						$i = 0;
						foreach ($category->products as $product) {
					?>
						<div class="api-product-item">
							<div class="img">
								<img src="<?= $product->img ?>" alt="">
							</div>
							<label class="check" for="customCheck<?= $i ?>">
								<div class="custom-control custom-checkbox">
									<input type="checkbox" class="custom-control-input" id="customCheck<?= $i ?>" name="products[]" value="<?= $product->id ?>" category-id="<?= $category->id ?>">
									<label class="custom-control-label" for="customCheck<?= $i ?>">Seç</label>
								</div>
							</label>
							<input type="hidden" name="product_default_prices[<?= $product->id ?>]" value="<?= $product->price ?>">
							<input type="hidden" name="product_prices[<?= $product->id ?>]" value="<?= $product->price ?>">
							<input type="hidden" name="product_categories[<?= $product->id ?>]" value="<?= $category->id ?>">
							<div class="content">
								<div class="name"><?= $product->name ?></div>
								<div class="category mb-0">Kategori: <?= $category->name ?></div>
								<div class="category">Ürün Tedarikçisi: <?= $product->api_provider ?? "Yok" ?></div>
								<div class="price">
									<span id="def_price" class="d-inline" style="text-decoration: line-through;"><?= $product->price ?></span>
									<p id="new_price" class="d-inline"><?= $product->price ?></p>
								TL</div>
							</div>
						</div>
					<?php
							$i++;
						}
					?>
				</div>
			</div>
			<?php
				}
			?>

			<div class="card">
				<div class="card-body">
					<a href="javascript:void(0)" class="btn btn-primary" onclick="initProducts()" id="submit_button"><i class="far fa-save"></i> Kaydet</a>
				</div>
			</div>

		</div>
	</main>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script type="text/javascript">
		//check-all-category
		$('[check-all-category]').on("change", function () {
			var category_id = $(this).attr("check-all-category");
			if ($(this).is(":checked")) {
				$("input[category-id='" + category_id + "']").prop("checked", true);
			} else {
				$("input[category-id='" + category_id + "']").prop("checked", false);
			}
		});
		$('input[name="dividend"]').on("keyup", function () {
			var dividend = $(this).val();
			if (dividend == "") {
				$(".api-product-item").each(function () {
					var product_def_price = $(this).find("[name^='product_default_prices']").val();
					$(this).find(".price #new_price").html(product_def_price);
					$(this).find("[name^='product_prices']").val(product_def_price);
				});
			} else {
				var dividend = parseFloat(dividend);

				$(".api-product-item").each(function () {
					var product_def_price = $(this).find("[name^='product_default_prices']").val();
					var new_price = parseFloat(product_def_price) + (parseFloat(product_def_price) * (dividend / 100));
					$(this).find("[name^='product_prices']").val(new_price);
					$(this).find(".price #new_price").html(new_price);
				});
			}
		});
		function showSubmitSpinner() {
			var submit_button = document.getElementById("submit_button");
			submit_button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Yükleniyor...';
			submit_button.disabled = true;
		}

		function hideSubmitSpinner() {
			var submit_button = document.getElementById("submit_button");
			submit_button.innerHTML = '<i class="far fa-save"></i> Kaydet';
			submit_button.disabled = false;
		}

		function initProducts() {
			Swal.fire({
				title: 'Emin Misin?',
				text: "Bu işlem sisteminize yeni ürünler ekleyecektir. Emin misiniz?",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				cancelButtonText: "Hayır",
				confirmButtonText: 'Evet'
			}).then((result) => {
				//if confirm
				if (result.isConfirmed) {
					showSubmitSpinner();
					//get selected products
					var products = [];
					$("input[name='products[]']:checked").each(function() {
						products.push($(this).val());
					});

					if (products.length == 0) {
						Swal.fire({
							title: 'Hata!',
							text: "Lütfen en az bir ürün seçiniz.",
							icon: 'error',
							confirmButtonText: 'Tamam'
						})
						return;
					}

					var product_prices = {};
					var active_categories = [];
					$.each(products, function(index, value) {
						product_prices[value] = $("input[name='product_prices[" + value + "]']").val();
						//if not exists
						if (!active_categories.includes($("input[name='product_categories[" + value + "]']").val())) {
							active_categories.push($("input[name='product_categories[" + value + "]']").val());
						}
					});
					//send request
					$.ajax({
						url: "<?= base_url('admin/API/InitProductsFromAPI'); ?>",
						type: "POST",
						data: {
							products: products,
							product_prices: product_prices,
							active_categories: active_categories
						},
						dataType: "json",
						success: function(result) {
							if (result.success) {
								Swal.fire({
									title: 'Başarılı!',
									text: result.message,
									icon: 'success',
									confirmButtonText: 'Tamam'
								}).then((result) => {
									if (result.isConfirmed) {
										hideSubmitSpinner();
										location.reload();
									}
								})
							} else {
								Swal.fire({
									title: 'Hata!',
									text: result.message,
									icon: 'error',
									confirmButtonText: 'Tamam'
								}).then((result) => {
									if (result.isConfirmed) {
										hideSubmitSpinner();
										location.reload();
									}
								})
							}
						},
						error: function() {
							Swal.fire({
								title: 'Hata!',
								text: "Sistemsel bir hata oluştu. Lütfen sayfayı yenileyip tekrar deneyiniz.",
								icon: 'error',
								confirmButtonText: 'Tamam'
							}).then((result) => {
								if (result.isConfirmed) {
									hideSubmitSpinner();
									location.reload();
								}
							})
						}
					});

				}
			})
		}
	</script>
