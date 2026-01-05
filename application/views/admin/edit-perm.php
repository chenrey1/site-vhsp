            <div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">Yetki Ayarları</h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item ">Yetki Ayarları</li>
                                <li class="breadcrumb-item active" aria-current="page">Yetki Ayarları</li>
                            </ol>
                        </nav>

                        <div class="card card-referance">
                            <div class="card-body">
                                <form action="<?= base_url('admin/product/changePermission/') ?><?=(!empty($roles) ? $roles->id : NULL)?>" method="POST">
                                    <div class="row">
                                        <div class="col">
                                             <div class="form-group">
                                                <label for="nameOfAuth">Yetki Adı</label>
                                                <input type="text" class="form-control" id="nameOfAuth" value="<?=(!empty($roles) ? $roles->role : NULL)?>" name="authName" required>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth1" name="seeHome" <?= (!empty($roles) && isPerm($roles->id, 'seeHome') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth1">Admin Panel Ana Sayfasını Görebilir / Güncelleme Yapabilir / Bekleyen Ürünleri ve Geri Dönüşü Olan Ürünleri Gönderebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth2" name="seeProduct" <?= (!empty($roles) && isPerm($roles->id, 'seeProduct') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth2">Ürünleri Görebilir / Ekleyebilir / Düzenleyebilir / Silebilir / İndirim Tanımlayabilir / Fiyatlarını Değiştirebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth3" name="seeStocks" <?= (!empty($roles) && isPerm($roles->id, 'seeStocks') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth3">Stokları Görebilir / Ekleyebilir / Silebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth4" name="seeSellHistory" <?= (!empty($roles) && isPerm($roles->id, 'seeSellHistory') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth4">Geçmiş Satışları ve Detayını Görebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth5" name="seeTransfer" <?= (!empty($roles) && isPerm($roles->id, 'seeTransfer') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth5">Havale Bildirimlerini Görebilir / Onaylayabilir / Reddedebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth6" name="seeCategory" <?= (!empty($roles) && isPerm($roles->id, 'seeCategory') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth6">Kategorileri Görebilir / Ekleyebilir / Silebilir / Düzenleyebilir / Menü Görünümünü Değiştirebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth19" name="seeProductComments" <?= (!empty($roles) && isPerm($roles->id, 'seeProductComments') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth19">Ürün Yorumlarını Görebilir / Silebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth7" name="seeReferences" <?= (!empty($roles) && isPerm($roles->id, 'seeReferences') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth7">Referans Listesini Görebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth8" name="seeReferenceSettings" <?= (!empty($roles) && isPerm($roles->id, 'seeReferenceSettings') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth8">Referans Ayarlarını Görebilir / Güncelleyebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth9"name="seeBlogs" <?= (!empty($roles) && isPerm($roles->id, 'seeBlogs') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth9">Makaleleri Görebilir / Düzenleyebilir / Silebilir / Ekleyebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth10" name="seePages" <?= (!empty($roles) && isPerm($roles->id, 'seePages') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth10">Sayfaları Görebilir / Ekleyebilir / Silebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth11" name="seeUsers" <?= (!empty($roles) && isPerm($roles->id, 'seeUsers') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth11">Üyeleri Görebilir / Düzenleyebilir (Yetki Hariç) / Yasaklayabilir / Yasağı Kaldırabilir / Toplu Mail Gönderebilir / Kullanıcının Geçmiş Alımlarını Görebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth21" name="seeNotification" <?= (!empty($roles) && isPerm($roles->id, 'seeNotification') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth21">Site Bildirimlerini Görüntüleyebilir / Oluşturabilir / İptal Edebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth12" name="seeTickets" <?= (!empty($roles) && isPerm($roles->id, 'seeTickets') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth12">Destek Taleplerini Görebilir / Cevaplayabilir / Sonlandırabilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth13" name="seeLogs" <?= (!empty($roles) && isPerm($roles->id, 'seeLogs') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth13">Kayıt Geçmişini Görebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth14" name="seeShops" <?= (!empty($roles) && isPerm($roles->id, 'seeShops') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth14">Üye Mağazalarını Görebilir / Düzenleyebilir / Yasaklayabilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth15" name="seeRequests" <?= (!empty($roles) && isPerm($roles->id, 'seeRequests') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth15">Çekim Taleplerini Görebilir / Onaylayabilir / Reddedebilir</label>
                                            </div>
                                             <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth16" name="seePendingProducts" <?= (!empty($roles) && isPerm($roles->id, 'seePendingProducts') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth16">Onay Bekleyen Ürünleri Görebilir / Onaylayabilir / Reddedebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth17" name="seeObjections" <?= (!empty($roles) && isPerm($roles->id, 'seeObjections') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth17">Mevcut ve Geçmiş İtirazları Görebilir / Onaylayabilir / Reddedebilir</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth18" name="seeThemeSettings" <?= (!empty($roles) && isPerm($roles->id, 'seeThemeSettings') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth18">Tema Sekmesindeki Her Şeyi Düzenleyebilir (Sadece Admin İçin Tavsiye Edilir)</label>
                                            </div>
                                            <div class="custom-control custom-checkbox mr-sm-2">
                                                <input type="checkbox" class="custom-control-input" id="auth20" name="seeSettings" <?= (!empty($roles) && isPerm($roles->id, 'seeSettings') == true) ? 'checked=""' : NULL ?>>
                                                <label class="custom-control-label" for="auth20">Genel Ayarlar Sekmesindeki Her Şeyi Düzenleyebilir (Sadece Admin İçin Tavsiye Edilir)</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </main>
