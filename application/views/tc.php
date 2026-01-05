    <div class="vt-login-page">
            <div class="alt">
                <div class="container">
                    <div class="row">
    
                        <div class="col-md-6 mx-auto">
                            <div class="vt-lp-card">
                                <div class="tab-content">

                                    <div class="tab-panel" id="giris" role="tabpanel">
                                        <h1 class="mt-3">Bilgileri Doldur</h1>
                                            <div id="formAlert" class="alert-warning mt-4"><?php if (!$this->session->userdata('clientFlashSession')): ?>
                                                    <?= $this->session->userdata('clientFlashSession'); ?>
                                                <?php endif ?>
                                            </div>
                                        <?php echo form_open(base_url('login/addTc'));?>
                                            <div class="form-group">
                                                <label for="">TC NO</label>
                                                <?php echo form_input('tc', '', ['class'=>'form-control','type' => 'text']) ?>
                                            </div>
                                            <div class="form-group">
                                                <label for="">Doğum Yılı (Sadece Yıl)</label>
                                                <?php echo form_input('birthday', '', ['class'=>'form-control']) ?>
                                            </div>
                                            <small>Bu bilgileri girmediğiniz taktirde sistemdeki bazı özelliklere erişeyemeceksiniz.</small>
                                             <?php echo form_submit('btn btn-gradient1 btn-block btn-primary', 'Kaydet', ['class'=>'btn btn-gradient1 btn-block btn-primary']); ?>
                                            <div class="button-divider"></div>
                                        </form>
                                    </div>

                                </div>

                            </div>
                        </div>
    
                    </div>
                </div>
            </div>
        </div>
    </main>
