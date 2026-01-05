<section class="fp-section-page">
    <div class="container">
        <div class="fp-section-page-head">
            <h1 class="title mb-0">Yayıncılar</h1>
        </div>

        <div class="row">
            <?php foreach ($streamers as $key => $value) { 
                $value->streamer_info = json_decode($value->streamer_info, false);
            ?>
            <div class="col-lg-3">
                <div class="fp-streamer-item mb-24">
                    <div class="img">
                        <img src="<?= $value->streamer_info->streamlabs->thumbnail ?>" alt="" class="img-profile">
                        <div class="icon"><i class="ri-twitch-fill"></i></div>
                    </div>
                    <div class="content">
                        <div class="streamer-name"><?= mb_strtoupper($value->streamer_title) ?></div>
                        <div class="link"><?= $value->streamer_stream_url ?><?= $value->streamer_stream_url ?></div>
                        <a href="<?= base_url("yayinci/").$value->streamer_slug ?>" class="btn btn-twitch btn-sm">Bağış Yap</a>
                    </div>
                </div>
            </div>
            <?php } ?>

        </div>

    </div>
</section>
