<?php if ($transaction->type == 'deposit'): ?>
    <!-- Bakiye Yükleme Detayları -->
    <div class="transaction-modal-details">
        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-3">İşlem Bilgileri</h6>
                <table class="table table-sm transaction-modal-table">
                    <tr>
                        <th>İşlem No:</th>
                        <td>#<?= $transaction->id ?></td>
                    </tr>
                    <tr>
                        <th>İşlem Türü:</th>
                        <td><span class="badge badge-info"><?= $transaction->type_text ?></span></td>
                    </tr>
                    <tr>
                        <th>Tutar:</th>
                        <td class="text-primary font-weight-bold"><?= number_format($transaction->price, 2) ?> TL</td>
                    </tr>
                    <tr>
                        <th>Ödeme Yöntemi:</th>
                        <td><?= $transaction->payment_method ?></td>
                    </tr>
                    <tr>
                        <th>Durum:</th>
                        <td>
                            <?php
                            $badge_class = $transaction->status == 0 ? 'badge-success' : 
                                         ($transaction->status == 1 ? 'badge-warning' : 
                                         ($transaction->status == 2 ? 'badge-danger' : 'badge-secondary'));
                            ?>
                            <span class="badge <?= $badge_class ?>"><?= $transaction->status_text ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tarih:</th>
                        <td>
                            <?= date('d.m.Y H:i', strtotime($transaction->date)) ?>
                            <small class="text-muted ml-2">(<?= $transaction->time_passed ?>)</small>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="mb-3">Kullanıcı Bilgileri</h6>
                <table class="table table-sm transaction-modal-table">
                    <tr>
                        <th>Ad Soyad:</th>
                        <td><?= $transaction->user_name ?></td>
                    </tr>
                    <tr>
                        <th>E-posta:</th>
                        <td><?= $transaction->user_email ?></td>
                    </tr>
                    <tr>
                        <th>Telefon:</th>
                        <td><?= $transaction->user_phone ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- Ürün Alımı Detayları -->
    <div class="transaction-modal-details">
        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-3">İşlem Bilgileri</h6>
                <table class="table table-sm transaction-modal-table">
                    <tr>
                        <th>İşlem No:</th>
                        <td>#<?= $transaction->id ?></td>
                    </tr>
                    <tr>
                        <th>İşlem Türü:</th>
                        <td><span class="badge badge-info"><?= $transaction->type_text ?></span></td>
                    </tr>
                    <tr>
                        <th>Tutar:</th>
                        <td class="text-primary font-weight-bold"><?= number_format($transaction->price, 2) ?> TL</td>
                    </tr>
                    <tr>
                        <th>Ödeme Yöntemi:</th>
                        <td><?= $transaction->payment_method ?></td>
                    </tr>
                    <tr>
                        <th>Durum:</th>
                        <td>
                            <?php
                            $badge_class = $transaction->status == 0 ? 'badge-success' : 
                                         ($transaction->status == 1 ? 'badge-warning' : 
                                         ($transaction->status == 2 ? 'badge-danger' : 'badge-secondary'));
                            ?>
                            <span class="badge <?= $badge_class ?>"><?= $transaction->status_text ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Tarih:</th>
                        <td>
                            <?= date('d.m.Y H:i', strtotime($transaction->date)) ?>
                            <small class="text-muted ml-2">(<?= $transaction->time_passed ?>)</small>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="mb-3">Ürün Bilgileri</h6>
                <table class="table table-sm transaction-modal-table">
                    <tr>
                        <th>Ürün:</th>
                        <td>
                            <?php if ($transaction->product_img): ?>
                                <img src="<?= base_url('assets/img/product/' . $transaction->product_img) ?>" 
                                     alt="<?= $transaction->product_name ?>" 
                                     class="img-thumbnail mr-2" 
                                     style="height: 40px;">
                            <?php endif; ?>
                            <?= $transaction->product_name ?>
                        </td>
                    </tr>
                    <?php if ($transaction->has_code): ?>
                        <tr>
                            <th>Ürün Kodu:</th>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-sm" value="<?= $transaction->code ?>" readonly>
                                    <div class="input-group-append">
                                        <button class="btn btn-sm btn-outline-secondary transaction-modal-copy-btn" data-clipboard-text="<?= $transaction->code ?>">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if ($transaction->extras): ?>
                        <?php foreach ($transaction->extras as $key => $value): ?>
                            <tr>
                                <th><?= $key ?>:</th>
                                <td>
                                    <div class="input-group">
                                        <input type="text" class="form-control form-control-sm" value="<?= $value ?>" readonly>
                                        <div class="input-group-append">
                                            <button class="btn btn-sm btn-outline-secondary transaction-modal-copy-btn" data-clipboard-text="<?= $value ?>">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-6">
                <h6 class="mb-3">Kullanıcı Bilgileri</h6>
                <table class="table table-sm transaction-modal-table">
                    <tr>
                        <th>Ad Soyad:</th>
                        <td><?= $transaction->user_name ?></td>
                    </tr>
                    <tr>
                        <th>E-posta:</th>
                        <td><?= $transaction->user_email ?></td>
                    </tr>
                    <tr>
                        <th>Telefon:</th>
                        <td><?= $transaction->user_phone ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
.transaction-modal-details {
    padding: 15px;
}

.transaction-modal-details table {
    margin-bottom: 0;
}

.transaction-modal-details th {
    width: 35%;
    font-weight: 600;
}

.transaction-modal-details td {
    width: 65%;
}

.transaction-modal-details .badge {
    padding: 5px 10px;
}

.transaction-modal-copy-btn {
    padding: 0.25rem 0.5rem;
}

.transaction-modal-copy-btn i {
    font-size: 0.875rem;
}

.transaction-modal-copy-btn.copied {
    background-color: #28a745;
    border-color: #28a745;
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kopyalama işlevi için
    document.querySelectorAll('.transaction-modal-copy-btn').forEach(button => {
        button.addEventListener('click', function() {
            const text = this.dataset.clipboardText;
            navigator.clipboard.writeText(text).then(() => {
                // Başarılı kopyalama animasyonu
                this.classList.add('copied');
                const icon = this.querySelector('i');
                icon.classList.remove('fa-copy');
                icon.classList.add('fa-check');
                
                // 1.5 saniye sonra eski haline döndür
                setTimeout(() => {
                    this.classList.remove('copied');
                    icon.classList.remove('fa-check');
                    icon.classList.add('fa-copy');
                }, 1500);
            }).catch(err => {
                console.error('Kopyalama hatası:', err);
            });
        });
    });
});
</script> 