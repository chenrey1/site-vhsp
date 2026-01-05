<section class="fp-section-page">
    <div class="container">

        <div class="fp-card fp-auth-card">
            <div class="fp-card-body">

                <div class="text-center">
                    <h1 class="title">Yeni Şifre</h1>
                    <p class="text">Hemen yeni şifrenizi oluşturun</p>
                </div>

                <form action="<?= base_url('home/setNewPassword'); ?>" method="POST">

                    <div class="fp-input mb-3">
                        <div class="icon"><i class="ri-lock-line"></i></div>
                        <input type="password" class="form-control" placeholder="Yeni Şifre" id="pp1" name="newPassword" required>
                    </div>

                    <div class="fp-input mb-3">
                        <div class="icon"><i class="ri-lock-line"></i></div>
                        <input type="password" class="form-control" placeholder="Yeni Şifre Tekrar" id="pp1" name="reNewPassword" required>
                    </div>

                    <input type="hidden" name="hash" value="<?= $hash ?>">
                    <button type="submit" class="btn btn-primary w-100">Yenile</button>
                </form>
                
            </div>
        </div>

    </div>
</section>