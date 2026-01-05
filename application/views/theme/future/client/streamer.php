<div class="col-lg-9">
    <div class="fp-card fp-card-client mb-16">
        <div class="fp-cc-head border-bottom-0">
            <h1 class="title">Yayıncı Paneli</h1>
        </div>
    </div>
    <div class="row row-16">
        <div class="col-md-6 col-lg-4">
            <div class="fp-info-item fp-card">
                <div class="content">
                    <div class="key">Toplam Bağış Tutarı</div>
                    <div class="value"><?= $this->db->select_sum("amount")->where('streamer', $user->id)->get('streamer_donations')->row()->amount ?? 0; ?> TL</div>
                </div>
                <div class="icon">
                    <i class="ri-coin-line"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="fp-info-item fp-card">
                <div class="content">
                    <div class="key">Toplam Bağış Sayısı</div>
                    <div class="value"><?= $this->db->where('streamer', $user->id)->count_all_results('streamer_donations'); ?></div>
                </div>
                <div class="icon">
                    <i class="ri-hand-coin-line"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="fp-info-item fp-card">
                <div class="content">
                    <div class="key">Toplam Bağışçı</div>
                    <div class="value"><?= $this->db->where('streamer', $user->id)->group_by('user')->count_all_results('streamer_donations'); ?></div>
                </div>
                <div class="icon">
                    <i class="ri-user-smile-line"></i>
                </div>
            </div>
        </div>
    </div>
    <?php
    $user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
    ?>
    <div class="fp-card fp-card-client">
        <div class="fp-card-tabs">
            <ul class="fp-tabs-nav-system list list-unstyled list-inline mb-0">
                <li><a href="<?= base_url('client/streamer_donations') ?>" class="link active" onclick="window.location.href='<?= base_url('client/streamer_donations') ?>'">Gelen Bağış Listesi</a></li>
            </ul>
        </div>
    </div>
</div>
</div>
</div>
</section>