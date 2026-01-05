<section class="fp-section-page">
    <div class="container">

        <div class="fp-card fp-auth-card">
            <div class="fp-card-body">

                <div class="auth-login-form">
                    <div class="text-center">
                        <h1 class="title">Giriş Yap</h1>
                        <p class="text">Bilgilerinizle hemen giriş yapın</p>
                    </div>

                    <?php echo form_open(base_url('login/loginClient'));?>
                    <div class="fp-input mb-3">
                        <div class="icon"><i class="ri-mail-line"></i></div>
                        <input type="email" class="form-control" placeholder="E-Posta Adresi" id="loginemail" name="mail" required>
                    </div>

                    <div class="fp-input mb-3">
                        <div class="icon"><i class="ri-lock-line"></i></div>
                        <input type="password" class="form-control" placeholder="Şifre" id="loginpassword" name="password" required>
                    </div>

                    <div class="text-end my-2">
                        <a href="<?= base_url('sifremi-unuttum') ?>" class="link">Şifremi Unuttum</a>
                    </div>

                    <?php echo form_submit('btn btn-primary w-100', 'Giriş Yap', ['class'=>'btn btn-primary w-100']); ?>
                    <?php echo form_close();?>

                    <div class="text-alt">Henüz hesabın yok mu?</div>
                    <a href="#" class="btn btn-opacity-primary w-100 show-register-form">Kayıt Ol</a>
                </div>

                <div class="auth-register-form" style="display: none">
                    <div class="text-center">
                        <h1 class="title">Kayıt Ol</h1>
                        <p class="text">Bilgilerinizle hemen kayıt olun</p>
                    </div>

                    <?php echo form_open(base_url('login/regUser'));?>
                    <div class="row">

                        <div class="col-lg-6">
                            <div class="fp-input mb-3">
                                <div class="icon"><i class="ri-user-line"></i></div>
                                <input type="text" class="form-control" placeholder="Ad" name="name" id="name">
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="fp-input mb-3">
                                <div class="icon"><i class="ri-user-star-line"></i></div>
                                <input type="text" class="form-control" placeholder="Soyad" name="surname" id="surname">
                            </div>
                        </div>

                    </div>

                    <div class="fp-input mb-3">
                        <div class="icon"><i class="ri-mail-line"></i></div>
                        <input type="text" class="form-control" id="email" name="email" placeholder="E-Posta Adresi">
                    </div>

                    <?php if ($properties->isConfirmTc == 1): ?>
                        <div class="fp-input mb-3">
                            <div class="icon"><i class="ri-shield-user-line"></i></div>
                            <input type="text" id="tc" name="tc" class="form-control" placeholder="TC Kimlik Numarası" minlength="11" maxlength="11" required="">
                        </div>

                        <div class="fp-input mb-3">
                            <div class="icon"><i class="ri-calendar-2-line"></i></div>
                            <input type="text" id="birthday" name="birthday" class="form-control" placeholder="Doğum Yılı" minlength="4" maxlength="4" required="">
                        </div>
                    <?php endif ?>

                    <div class="fp-input mb-3">
                        <div class="icon"><i class="ri-phone-line"></i></div>
                        <input type="text" class="form-control" id="phone" name="phone" placeholder="Telefon Numarası">
                    </div>

                    <div class="fp-input mb-3">
                        <div class="icon"><i class="ri-lock-line"></i></div>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Şifre">
                    </div>

                    <div class="fp-input mb-3">
                        <div class="icon"><i class="ri-link"></i></div>
                        <input type="text" class="form-control" id="ref_code" name="ref_code" placeholder="Referans Kodu" <?php if (!empty($ref_code)) { ?>value="<?= $ref_code ?>" readonly<?php } ?>>
                    </div>

                    <div class="my-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="confirm" name="confirm" required="" checked="">
                            <label class="form-check-label fs-15" for="confirm">
                                <a href="#modalContract" data-bs-toggle="modal" data-bs-target="#modalContract">Sözleşmeyi</a> okudum ve onaylıyorum.
                            </label>
                        </div>
                    </div>

                    <div class="modal fade" id="modalContract" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Şartlar ve Gizlilik Sözleşmesi</h5>
                                    <a type="button" class="btn-close" data-dismiss="modal" aria-label="Close">x</a>
                                </div>
                                <div class="modal-body">
                                    <?= $properties->contract ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <?php echo form_submit('btn btn-primary w-100', 'Kayıt Ol', ['class'=>'btn btn-primary w-100']); ?>
                    </div>

                    <div class="text-alt">Hesabın var mı?</div>
                    <a href="#" class="btn btn-opacity-primary w-100 show-login-form">Giriş Yap</a>
                    <?php echo form_close();?>
                </div>

            </div>
        </div>

    </div>
</section>