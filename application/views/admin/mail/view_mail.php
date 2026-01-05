<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Şablon:</strong></div>
                        <div class="col-md-9"><?= $mail->template_name ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Kullanıcı:</strong></div>
                        <div class="col-md-9"><?= ($mail->user_name) ? $mail->user_name . ' ' . $mail->user_surname : 'Sistem' ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Alıcı:</strong></div>
                        <div class="col-md-9"><?= $mail->to_email ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Konu:</strong></div>
                        <div class="col-md-9"><?= $mail->subject ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Durum:</strong></div>
                        <div class="col-md-9">
                            <?php if($mail->status == 'success'): ?>
                                <span class="badge badge-success">Başarılı</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Başarısız</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>Tarih:</strong></div>
                        <div class="col-md-9"><?= date('d.m.Y H:i', strtotime($mail->created_at)) ?></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <strong>İçerik:</strong>
                            <div class="border p-3 mt-2">
                                <?= $mail->content ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 