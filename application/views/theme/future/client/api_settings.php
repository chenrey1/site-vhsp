
            <div class="col-lg-9">
                <div class="fp-card fp-card-client mb-16">
                    <div class="fp-cc-head border-bottom-0">
                        <h1 class="title">API Ayarları</h1>
                    </div>
                </div>

                <div class="fp-card fp-card-client">
                    <div class="fp-cc-body">
                        <form action="<?= base_url('client/updateApiSettings') ?>" method="POST">
                            <div class="mb-4">
                                <label class="mb-2">İzin Verilen IP Adresleri</label>
                                <textarea 
                                    class="form-control" 
                                    name="allowed_ips" 
                                    rows="4" 
                                    placeholder="Her satıra bir IP adresi yazın. Örnek:&#10;192.168.1.1&#10;10.0.0.1"
                                ><?= str_replace(',', "\n", $user->allowed_ips) ?></textarea>
                                <small class="text-muted">API'ye erişim sağlayacak IP adreslerini her satıra bir tane olacak şekilde yazın.</small>
                            </div>

                            <div class="mb-4">
                                <label class="mb-2">API Kullanım Bilgileri</label>
                                <div class="alert alert-info mb-0">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="fw-medium" style="width: 150px">API Endpoint:</div>
                                        <div><?= base_url('api/v1') ?></div>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="fw-medium" style="width: 150px">Dökümanlar:</div>
                                        <div><a href="<?= base_url('api/docs') ?>" target="_blank">API Dökümanları</a></div>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="fw-medium" style="width: 150px">Kimlik Doğrulama:</div>
                                        <div>Basic Authentication</div>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="fw-medium" style="width: 150px">Kullanıcı Adı:</div>
                                        <div><?= $user->email ?></div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="fw-medium" style="width: 150px">Şifre:</div>
                                        <div>Hesap şifreniz</div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line icon icon-left"></i> Kaydet
                            </button>
                        </form>
                    </div>
                </div>
            </div>
