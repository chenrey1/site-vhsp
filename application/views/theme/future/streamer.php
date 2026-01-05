<section class="fp-section-page">

    <div class="container">

        <div class="row">



            <div class="col-lg-3">

                <div class="fp-streamer-item mb-24">

                    <div class="img">

                        <img src="assets/img/slider-1.jpg" alt="" class="img-profile">

                        <div class="icon"><i class="ri-twitch-fill"></i></div>

                    </div>

                    <div class="content">

                        <div class="streamer-name"><?= mb_strtoupper($streamer->streamer_title) ?></div>

                        <div class="link"><?= $streamer->streamer_stream_url ?><?= $streamer->streamer_stream_url ?></div>



                        <ul class="list-social mb-0 list-unstyled list-inline">

                            <?php foreach ($streamer->streamer_social as $key => $value) { ?>

                                <li><a class="<?=$key?>" href="<?= $value ?>"><i class="ri-<?=$key?>-line"></i></a></li>

                            <?php } ?>

                        </ul>

                    </div>

                </div>

            </div>



            <div class="col-lg-9">

                <div class="fp-card fp-card-client">

                    <div class="fp-cc-head">

                        <h1 class="title">Bağış Yap</h1>

                    </div>

                    <div class="fp-cc-body">



                        <form action="<?= base_url("donation/".$streamer->streamer_slug) ?>" method="POST">

                            <div class="mb-3">

                                <label>Gönderen Kullanıcı Adı</label>

                                <input type="text" name="donor" class="form-control" placeholder="Örn. Bağışçı1">

                            </div>



                            <div class="mb-3">

                                <label>Bağış Tutarı</label>

                                <input type="number" name="amount" class="form-control" placeholder="Örn. 5">

                            </div>



                            <div class="mb-3">

                                <label>Bağış Mesajı</label>

                                <textarea name="message" rows="5" class="form-control" placeholder="Mesaj"></textarea>

                            </div>



                            <div class="form-check">

                                <input id="hide_in_screen" class="form-check-input" type="checkbox" name="hide_in_screen" value="yes">

                                <label class="form-check-label" for="hide_in_screen">

                                    Ekranda gözükmesin

                                </label>

                            </div>



                            <div id="formAlert" class="text-danger my-2">

                                Dikkat! Yapılan bağışlar iade edilemez.

                            </div>



                            <button class="btn btn-twitch w-100 d-flex">Gönder <i class="ri-arrow-right-line icon icon-right"></i></button>



                        </form>



                    </div>

                </div>

            </div>



        </div>

    </div>

</section>