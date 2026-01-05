<section class="fp-section-page">
    <div class="container">

        <div class="fp-card fp-auth-card">
            <div class="fp-card-body">

                <div class="text-center">
                    <h1 class="title">Şifremi Unuttum</h1>
                    <p class="text">E-Posta adresinizi girin, yeni şifrenizi oluşturun</p>
                </div>

                <form action="<?= base_url('home/reNewPassword') ?>" method="POST">
                    <div class="fp-input mb-3">
                        <div class="icon"><i class="ri-mail-line"></i></div>
                        <input type="email" class="form-control" placeholder="E-Posta Adresi" id="pp1" name="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Bağlantı Gönder</button>
                </form>

            </div>
        </div>

    </div>
</section>