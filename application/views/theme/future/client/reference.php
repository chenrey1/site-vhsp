<div class="col-lg-9">
    <div class="fp-card fp-card-client">
        <div class="fp-cc-head">
            <h1 class="title">Referanslar</h1>
        </div>
        <div class="fp-cc-body">
            <?php if (!$refcode) { ?>
                <form action="<?= base_url('client/createRefcode') ?>">
                    <p>Henüz bir referans kodun yok, hemen referans kodu oluştur ve arkadaşlarınla paylaşmaya başla!</p>
                    <button type="submit" class="btn btn-primary"><i class="ri-link icon icon-left"></i> Referans Kodu Oluştur</button>
                </form>
            <?php } else { ?>
                <p><strong class="fw-medium">Referans Kodunuz:</strong> <?= $refcode ?></p>
                <p class="mb-0"><strong class="fw-medium">Referans Linkiniz:</strong> <?= base_url("hesap")."?ref_code=".$refcode ?></p>
            <?php } ?>
        </div>
    </div>
</div>
</div>
</div>
</section>