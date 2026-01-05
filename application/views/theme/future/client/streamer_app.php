<div class="col-lg-9">
    <div class="fp-card fp-card-client mb-16">
        <div class="fp-cc-head">
            <h1 class="title">Yayıncı Başvurusu</h1>
        </div>
        <div class="fp-cc-body">
            <form class="multiStep" method="POST" action="<?= base_url("client/streamer_app/".$next_page) ?>">
                <!-- step one -->
                <div class="step" <?= ($page == 1) ? 'style="display: block;"' : '' ?>>
                    <h5 class="fw-medium mb-3">Yayıncı Ayarları</h5>
                    <div class="mb-3">
                        <label>Yayın Adresi</label>
                        <input type="text" class="form-control" placeholder="Örn: https://twitch.tv/nickname" name="stream_url" value="<?= $_POST["stream_url"] ?? "" ?>" <?= !$only_notify_system ? 'required' : '' ?>>
                    </div>
                    <div class="mb-3">
                        <label>Bağış Sayfası Başlığı</label>
                        <input type="text" class="form-control" placeholder="Örn: XXXX" name="donate_title" value="<?= $_POST["donate_title"] ?? "" ?>" <?= !$only_notify_system ? 'required' : '' ?>>
                    </div>
                    <div class="mb-3">
                        <label>Bağış Adresi</label>
                        <div class="input-group">
                            <span class="input-group-text"><?= $url_without_https ?></span>
                            <input type="text" class="form-control" placeholder="nickname" name="donate_url" value="<?= $_POST["donate_url"] ?? "" ?>" <?= !$only_notify_system ? 'required' : '' ?>>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label>Minimum Bağış Tutarı</label>
                        <input type="number" class="form-control" name="min_donate" value="<?= $_POST["min_donate"] ?? "" ?>" <?= !$only_notify_system ? 'required' : '' ?>>
                    </div>
                </div>
                <!-- step two -->
                <div class="step" <?= ($page == 2) ? 'style="display: block;"' : '' ?>>
                    <h5 class="fw-medium mb-3">Sosyal Medya</h5>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-twitch-line"></i></span>
                            <input type="text" class="form-control" placeholder="https://twitch.com/nickname" name="social[twitch]" value="<?= $_POST["social"]["twitch"] ?? "" ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-youtube-line"></i></span>
                            <input type="text" class="form-control" placeholder="https://youtube.com/nickname" name="social[youtube]" value="<?= $_POST["social"]["youtube"] ?? "" ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-discord-line"></i></span>
                            <input type="text" class="form-control" placeholder="https://discord.com/nickname" name="social[discord]" value="<?= $_POST["social"]["discord"] ?? "" ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-facebook-line"></i></span>
                            <input type="text" class="form-control" placeholder="https://facebook.com/nickname" name="social[facebook]" value="<?= $_POST["social"]["facebook"] ?? "" ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-instagram-line"></i></span>
                            <input type="text" class="form-control" placeholder="https://instagram.com/nickname" name="social[instagram]" value="<?= $_POST["social"]["instagram"] ?? "" ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-twitter-line"></i></span>
                            <input type="text" class="form-control" placeholder="https://twitter.com/nickname" name="social[twitter]" value="<?= $_POST["social"]["twitter"] ?? "" ?>">
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri-tiktok-line"></i></span>
                            <input type="text" class="form-control" placeholder="https://www.tiktok.com/@nickname" name="social[tiktok]" value="<?= $_POST["social"]["tiktok"] ?? "" ?>">
                        </div>
                    </div>
                </div>
                <!-- step three -->
                <div class="step" <?= ($page == 3) ? 'style="display: block;"' : '' ?>>
                    <h5 class="fw-medium mb-3">Bildirim Sistemi</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" id="inlineCheckbox1" value="streamlabs" name="notify_system" <?= ($page == 3) ? 'required' : '' ?>>
                                <label class="form-check-label" for="inlineCheckbox1">
                                    <img src="<?= base_url('assets/' . $properties->theme) ?>/img/streamlabs.svg" alt="#" class="img-fluid">
                                </label>
                            </div>
                        </div>
                    </div>
                    <?php if ($only_notify_system) { ?>
                        <input type="hidden" name="only_notify_system" value="yes">
                    <?php } ?>
                    <br>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <button type="submit" class="btn btn-secondary" id="prevBtn" <?= ($page == 1) ? 'style="display: none;"' : 'style="display: flex;"' ?> onclick="streamerPrevBtn(event)"><i class="ri-arrow-left-s-line icon icon-left"></i> Önceki</button>
                    <span></span>
                    <button type="submit" class="btn btn-primary" id="nextBtn"><?= ($page != 3) ? 'Sonraki' : 'Başvur' ?> <i class="ri-arrow-right-s-line icon icon-right"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>
</section>

<script>
    function streamerPrevBtn(e) {
        e = e || window.event;
        e.preventDefault();
        document.querySelector('.multiStep').setAttribute('action', '<?= base_url("client/streamer_app/".($page-1)) ?>');
        document.querySelector('.multiStep').submit();
        return true;
    }
</script>