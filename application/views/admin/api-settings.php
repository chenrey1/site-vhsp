<div id="layoutSidenav_content">

	<main>
		<div class="container-fluid">

			<div class="page-title">
				<h5>Ayarlar</h5>
			</div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="<?= base_url('admin/publicSettings') ?>">Genel Ayarlar</a></li>
					<li class="breadcrumb-item active" aria-current="page">API Ayarları</li>
				</ol>
			</nav>

			<div class="card">
				<div class="card-header card-header-nav">
					<ul class="nav nav-pills" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="pill" href="#sms" role="tab" aria-selected="true">
								<i class="fas fa-sms"></i>
								SMS Ayarları
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="pill" href="#billing" role="tab" aria-selected="false">
								<i class="fas fa-university"></i>
								Fatura Ayarları
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="pill" href="#pinabi" role="tab" aria-selected="false">
								<i class="fas fa-wallet"></i>
								PinAbi Ayarları
							</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="pill" href="#turkpin" role="tab" aria-selected="false">
								<i class="fas fa-wallet"></i>
								Turkpin Ayarları
							</a>
						</li>
						<!-- Stremlabs -->
						<li class="nav-item">
							<a class="nav-link" data-toggle="pill" href="#streamers" role="tab" aria-selected="false">
								<i class="fab fa-twitch"></i>
								Yayıncı Ayarları
							</a>
						</li>
					</ul>
				</div>
				<div class="card-body">
					<div class="tab-content" id="pills-tabContent">

						<div class="tab-pane fade show active" id="sms" role="tabpanel">
							<div class="row">
								<div class="col-12 col-lg-12">
									<form action="<?= base_url('admin/product/editAPISettings/sms') ?>" method="POST" enctype="multipart/form-data">
										<div class="form-group">
											<label for="inputSLogo">SMS Sağlayıcınızı Seçiniz</small></label>
											<div class="row">
												<div class="col-3">
													<div class="custom-control custom-radio">
														<input type="radio" id="sms1" name="sms[provider]" class="custom-control-input" value="netgsm" <?php if($settings["sms"]->provider == "netgsm") {echo "checked";} ?>>
														<label class="custom-control-label" for="sms1">NetGSM</label>
													</div>
												</div>
												<div class="col-3">
													<div class="custom-control custom-radio">
														<input type="radio" id="sms2" name="sms[provider]" class="custom-control-input" value="disabled" <?php if($settings["sms"]->provider == "disabled") {echo "checked";} ?>>
														<label class="custom-control-label" for="sms2">DeAktif</label>
													</div>
												</div>
											</div>
										</div>

										<!-- NetGSM -->
										<div class="form-group" display-for="{@sms[provider]}=netgsm">
											<label for="nameofsite">Kullanıcı Adı</label>
											<input type="text" class="form-control" id="nameofsite" value="<?= $settings["sms"]->username ?? "" ?>" name="sms[username]" required>
										</div>
										<div class="form-group" display-for="{@sms[provider]}=netgsm">
											<label for="inputSFace">Kullanıcı Şifre</label>
											<input type="text" class="form-control" id="inputSFace" value="<?= $settings["sms"]->password ?? "" ?>" name="sms[password]" required>
										</div>
										<div class="form-group" display-for="{@sms[provider]}=netgsm">
											<label for="inputSTw">SMS Başlık (Sistemde Tanımlı Mesaj Başlığı)</label>
											<input type="text" class="form-control" id="inputSTw" value="<?= $settings["sms"]->header ?? "" ?>" name="sms[header]" required>
										</div>
										<!-- NetGSM -->

										<label for="textarea">
											Yeni Sipariş
										</label>
										<div class="form-row">
											<div class="col-md-3">
												<div class="custom-control custom-checkbox">
													<input type="hidden" name="sms[neworder_enabled]" value="off">
													<input type="checkbox" name="sms[neworder_enabled]" class="custom-control-input" id="neworder_enabled" <?= ($settings["sms"]->neworder_enabled == 1) ? "checked" : NULL; ?>>
													<label class="custom-control-label" for="neworder_enabled"> Yeni Siparişlerde SMS Gönder</label>
												</div>
											</div>

											<div class="col-md-9">
												<textarea class="form-control" id="textarea" name="sms[neworder_message]" rows="1" required><?= $settings["sms"]->neworder_message ?? "Merhaba {User}, siparişin teslim edildi. Siparişini hesabına girip görebilirsin. ".base_url() ?></textarea>
											</div>
										</div>
										<br>
										<div class="text-center">
											<button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Kaydet</button>
										</div>
									</form>
								</div>
							</div>
						</div>

						<div class="tab-pane fade" id="billing" role="tabpanel">
							<div class="row">
								<div class="col-12 col-lg-12">
									<form action="<?= base_url('admin/product/editAPISettings/billing') ?>" method="POST" enctype="multipart/form-data">
										<div class="form-group">
											<label for="inputSLogo">Fatura Sağlayıcınızı Seçiniz</small></label>
											<div class="row">
												<div class="col-3">
													<div class="custom-control custom-radio">
														<input type="radio" id="billing1" name="billing[provider]" class="custom-control-input" value="tiko" <?php if($settings["billing"]->provider == "tiko") {echo "checked";} ?>>
														<label class="custom-control-label" for="billing1">Tiko</label>
													</div>
												</div>
												<div class="col-3">
													<div class="custom-control custom-radio">
														<input type="radio" id="billing2" name="billing[provider]" class="custom-control-input" value="disabled" <?php if($settings["billing"]->provider == "disabled") {echo "checked";} ?>>
														<label class="custom-control-label" for="billing2">DeAktif</label>
													</div>
												</div>
											</div>
										</div>

										<!-- Tiko -->
										<div class="form-group" display-for="{@billing[provider]}=tiko">
											<label for="auth_token">Authorization Token</label>
											<input type="text" class="form-control" id="auth_token" value="<?= $settings["billing"]->auth_token ?? "" ?>" name="billing[auth_token]" required>
										</div>
										<!-- Tiko -->
										<br>
										<div class="text-center">
											<button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Kaydet</button>
										</div>
									</form>
								</div>
							</div>
						</div>

						<div class="tab-pane fade" id="pinabi" role="tabpanel">
							<div class="row">
								<div class="col-12 col-lg-12">
									<form action="<?= base_url('admin/product/editAPISettings/pinabi') ?>" method="POST" enctype="multipart/form-data">
										<div class="form-group">
											<label for="apiUser">API Username</label>
											<input type="text" class="form-control" id="apiUser" value="<?= $settings["pinabi"]->apiUser ?? "" ?>" name="pinabi[apiUser]" required>
										</div>
										<div class="form-group">
											<label for="secretKey">API Secret Key</label>
											<input type="text" class="form-control" id="secretKey" value="<?= $settings["pinabi"]->secretKey ?? "" ?>" name="pinabi[secretKey]" required>
										</div>
										<div class="form-group">
											<label for="Authorization">API Basic Auth Key</label>
											<input type="text" class="form-control" id="Authorization" value="<?= $settings["pinabi"]->Authorization ?? "" ?>" name="pinabi[Authorization]" required>
										</div>
										<br>
										<div class="text-center">
											<button type="submit" class="btn btn-primary"><i class="far fa-save"></i> Kaydet</button>
										</div>
									</form>
								</div>
							</div>
						</div>

						<div class="tab-pane fade" id="turkpin" role="tabpanel">
							<form action="<?= base_url('admin/product/edit/publicSettings/properties/1') ?>" method="POST">
								<div class="form-group">
									<label for="inputExample1">Türkpin Kullanıcı Adı</label>
									<input type="text" class="form-control" value="<?= $properties->turkpin_username ?>" id="inputExample1" name="turkpin_username">
								</div>
								<div class="form-group">
									<label for="inputExample2">Türkpin Şifresi</label>
									<input type="text" class="form-control" value="<?= $properties->turkpin_password ?>" id="inputExample2" name="turkpin_password">
								</div>
								<button type="submit" class="btn btn-primary float-right"><i class="far fa-save"></i> Kaydet</button>
							</form>
						</div>

						<div class="tab-pane fade" id="streamers" role="tabpanel">
							<div class="alert alert-danger">
								Yayıncı sisteminin düzgün çalışabilmesi için <a href="https://streamlabs.com/tr-tr/login" target="_blank">Streamlabs</a> üzerinden hesap oluşturulması gerekmektedir. Streamlabs hesabı oluşturulduktan sonra <a href="https://streamlabs.com/dashboard#/settings/oauth-clients" target="_blank">API Anahtarı</a> oluşturulmalıdır. API Anahtarı oluşturulurken REDIRECT URI alanına "<u><?= base_url("client/streamer_app/5") ?></u>" yazmanız gerekmektedir. Oluşturulan API Anahtarı bilgileri aşağıdaki alana girilmelidir. Detaylı bilgi için <a href="https://dev.streamlabs.com/docs/register-your-application" target="_blank">bu sayfayı</a> ziyaret edebilirsiniz.
							</div>
							<form action="<?= base_url('admin/product/edit/publicSettings/properties/1') ?>" method="POST">

								<div class="form-group">
									<label for="streamlabs_client_id">StreamLabs Client ID</label>
									<input type="text" class="form-control" value="<?= $properties->streamlabs_client_id ?>" name="streamlabs_client_id" id="streamlabs_client_id" required="">
								</div>
								<div class="form-group">
									<label for="streamlabs_client_secret">StreamLabs Client Secret</label>
									<input type="text" class="form-control" value="<?= $properties->streamlabs_client_secret ?>" name="streamlabs_client_secret" id="streamlabs_client_secret" required="">
								</div>
								<button type="submit" class="btn btn-primary float-right"><i class="far fa-save"></i> Kaydet</button>
							</form>
						</div>
					</div>
				</div>
			</div>

		</div>
	</main>
