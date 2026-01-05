<?php
// Kullanıcı ve site ayarları
$user = $this->db->where('id', $this->session->userdata('info')['id'])->get('user')->row();
$properties = isset($properties) ? $properties : $this->db->where('id', 1)->get('properties')->row();
?>
<div class="col-lg-9">
    <div class="fp-card fp-card-client">
        <div class="fp-cc-head">
            <h1 class="title">
                <div class="icon"><i class="ri-gift-fill"></i></div> Çekiliş Ödüllerim
            </h1>
        </div>
        <div class="fp-cc-body">
            <?php if (empty($won_rewards)): ?>
                <div class="alert alert-info">Henüz çekilişten kazandığınız bir ödül yok.</div>
            <?php else: ?>

                <div class="row g-3">
                    <?php foreach ($won_rewards as $reward): ?>
                        <div class="fp-order-item-new fp-card">
                            <div class="head-area">
                                <div class="top border-bottom-0 p-0 m-0">
                                    <div class="name">
                                        <div class="imgs icon">
                                            <i class="ri-gift-fill text-primary"></i>
                                        </div>
                                        <div class="area text-start">
                                            <div class="text-alt text-muted small">
                                                <?= isset($reward->created_at) ? date('d.m.Y', strtotime($reward->created_at)) : '-' ?>
                                            </div>
                                            <div class="text-alt">Çekiliş Hediyesi</div>
                                            <div class="product-name">Çekiliş:
                                                <strong><?= isset($reward->draw_name) ? htmlspecialchars($reward->draw_name) : '-' ?></strong>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!empty($reward->product_id) && !empty($reward->product_name)): ?>
                                        <div class="price">
                                            <a href="<?= isset($reward->product_slug) ? '/' . $reward->product_slug : '#' ?>" target="_blank">
                                                <?= htmlspecialchars($reward->product_name) ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($reward->reward_type == 'bakiye' && isset($reward->amount) && $reward->amount > 0): ?>
                                        <div class="price">Bakiye</div>
                                    <?php endif; ?>

                                    <?php if (empty($reward->product_id) && ($reward->reward_type != 'bakiye' || empty($reward->amount))): ?>
                                        <div class="price">Ödül detayları bulunamadı</div>
                                    <?php endif; ?>
                                    <span class="badge bg-<?= $reward->is_delivered ? 'success' : 'warning' ?> ms-1">
                                        <?= $reward->is_delivered ? 'Teslim Edildi' : 'Teslim Bekliyor' ?>
                                    </span>
                                    <div class="right">
                                        <div class="icon-right d-flex align-items-center justify-content-end" style="gap: 6px">
                                            Detay <i class="ri-arrow-down-s-line"></i></div>
                                    </div>
                                </div>

                            </div>
                            <div class="body-area">
                                <div class="epin-area">
                                    <h5 class="mb-0">Ödül Bilgisi</h5>
                                    <div class="product-info-box-area">
                                        <div class="product-info-box position-relative">
                                            <?php if ($reward->reward_type == 'bakiye' && isset($reward->amount) && $reward->amount > 0): ?>
                                                <input type="text" class="form-control" value="<?= number_format($reward->amount, 2, ',', '.') ?> ₺ + bakiye eklendi. " readonly>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($reward->product_id) && !empty($reward->product_name)): ?>
                                                <?php if($reward->is_delivered == 1 && !empty($reward->delivery_info)): ?>
                                                <input type="text" class="form-control" value="<?= nl2br(htmlspecialchars($reward->delivery_info)) ?>" readonly>
                                                <a href="#" class="copy"><i class="ri-file-copy-line"></i></a>
                                                <?php else: ?>
                                                    <input type="text" class="form-control" value="<?= htmlspecialchars($reward->product_name) ?> kazandınız. Teslimat işlemleri yönetici tarafından yürütülecektir." readonly>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</section>

<script>
    $(document).ready(function() {
        $(".copy").off("click").on("click", function(e) {
            e.preventDefault();
            var inputVal = $(this).closest(".product-info-box").find("input.form-control").val();

            if (inputVal) {
                navigator.clipboard.writeText(inputVal).then(() => {
                    alert("Kopyalandı!");
                }).catch(err => {
                    console.error("Kopyalama başarısız: ", err);
                });
            }
        });
    });

</script>
