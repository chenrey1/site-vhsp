<div class="col-lg-9">
    <div class="fp-wallet-container">
        <!-- Ana Başlık -->
        <div class="fp-card-balance fp-card-client mb-3">
            <div class="fp-cc-head">
                <h1 class="title">Cüzdanım</h1>
            </div>
        </div>
        
        <?php if($has_credit_offers && !$has_active_credit): // Aktif teklifleri olan ve aktif cari hesabı olmayan kullanıcılar için ?>
        <div class="pre-approved-credit-offer mb-4">
            <div class="pre-approved-credit-card">
                <div class="credit-card-header">
                    <div class="credit-icon">
                        <i class="ri-bank-line"></i>
                    </div>
                    <div class="credit-title">
                        <h4>Cari Hesap Teklifiniz</h4>
                        <p>Size özel hazırlanmış cari hesap limiti</p>
                    </div>
                </div>
                <div class="credit-card-body">
                    <?php $offer = $credit_offers[0]; // İlk teklifi göster ?>
                    <div class="credit-limit">
                        <span class="credit-limit-label">Teklif Edilen Limit</span>
                        <span class="credit-limit-amount"><?= number_format($offer->amount, 2) ?> ₺</span>
                    </div>
                    <div class="credit-features">
                        <div class="credit-feature-item">
                            <i class="ri-timer-line"></i>
                            <span>Anında Alışveriş İmkanı</span>
                        </div>
                        <div class="credit-feature-item">
                            <i class="ri-percent-line"></i>
                            <span><?= $offer->fee_percentage > 0 ? '%'.$offer->fee_percentage.' İşlem Ücreti' : 'İşlem Ücreti Yok' ?></span>
                        </div>
                        <div class="credit-feature-item">
                            <i class="ri-calendar-check-line"></i>
                            <span><?= $offer->term_days ?> Gün Ödeme Vadesi</span>
                        </div>
                        <div class="credit-feature-item">
                            <i class="ri-time-line"></i>
                            <span>Son Geçerlilik: <?= date('d.m.Y', strtotime($offer->offer_valid_until)) ?></span>
                        </div>
                    </div>
                    <div class="credit-cta">
                        <a href="#" class="btn btn-credit-primary wallet-tab-item" data-target="kredi-gecmisim">Cari Hesabımı Aç</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Aktif Cari Hesap Bilgileri (Varsa) -->
        <?php if($has_active_credit): // Aktif cari hesabı olan kullanıcılar için ?>
        <div class="active-credit-container mb-4">
            <div class="active-credit-card">
                <div class="active-credit-header">
                    <div class="credit-icon warning">
                        <i class="ri-alarm-warning-line"></i>
                    </div>
                    <div class="credit-title">
                        <h4>Cari Hesap Ödemeniz Yaklaşıyor</h4>
                        <p>Lütfen son ödeme tarihine dikkat ediniz</p>
                    </div>
                    <div class="credit-urgency-badge">
                        <span>Önemli</span>
                    </div>
                </div>
                <div class="active-credit-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <div class="credit-amount-box">
                                <div class="credit-amount-label">Toplam Cari Hesap Borcu</div>
                                <div class="credit-amount-value"><?= number_format($active_credit->remaining_amount, 2) ?> ₺</div>
                                <div class="credit-payment-date">
                                    <i class="ri-calendar-check-line"></i>
                                    <span>Son Ödeme: <strong><?= date('d.m.Y', strtotime($active_credit->due_date)) ?></strong></span>
                                </div>
                                <div class="credit-progress-bar">
                                    <div class="progress-label">
                                        <span>Ödeme Vadesi</span>
                                        <span><?= round((time() - strtotime($active_credit->created_at)) / (strtotime($active_credit->due_date) - strtotime($active_credit->created_at)) * 100) ?>%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-warning" role="progressbar" 
                                            style="width: <?= round((time() - strtotime($active_credit->created_at)) / (strtotime($active_credit->due_date) - strtotime($active_credit->created_at)) * 100) ?>%" 
                                            aria-valuenow="<?= round((time() - strtotime($active_credit->created_at)) / (strtotime($active_credit->due_date) - strtotime($active_credit->created_at)) * 100) ?>" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="credit-countdown-container">
                                <div class="countdown-label">Ödeme için kalan süre</div>
                                <div class="countdown-timer" id="credit-countdown" data-due-date="<?= $active_credit->due_date ?>">
                                    <div class="countdown-item">
                                        <div class="countdown-value" id="days">00</div>
                                        <div class="countdown-unit">Gün</div>
                                    </div>
                                    <div class="countdown-separator">:</div>
                                    <div class="countdown-item">
                                        <div class="countdown-value" id="hours">00</div>
                                        <div class="countdown-unit">Saat</div>
                                    </div>
                                    <div class="countdown-separator">:</div>
                                    <div class="countdown-item">
                                        <div class="countdown-value" id="minutes">00</div>
                                        <div class="countdown-unit">Dakika</div>
                                    </div>
                                    <div class="countdown-separator">:</div>
                                    <div class="countdown-item">
                                        <div class="countdown-value" id="seconds">00</div>
                                        <div class="countdown-unit">Saniye</div>
                                    </div>
                                </div>
                                <div class="credit-payment-action">
                                    <div class="payment-tip">Hesabınızın kısıtlanmaması için ödemenizi son tarihe kadar yapınız.</div>
                                    <a href="#" class="btn btn-primary credit-pay-btn" data-bs-toggle="modal" data-bs-target="#creditPaymentModal">
                                        <i class="ri-bank-card-line me-2"></i> Cari Hesap Borcunu Öde
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Bakiye Özeti Kartları -->
        <div class="fp-balance-cards mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="fp-balance-card available-balance">
                        <div class="balance-icon">
                            <i class="ri-money-dollar-circle-line"></i>
                        </div>
                        <div class="balance-info">
                            <div class="balance-label">Kullanılabilir Bakiye</div>
                            <div class="balance-amount"><?= number_format($user->balance, 2) ?> ₺</div>
                            <div class="balance-actions">
                                <a href="#" id="quick-deposit" class="balance-action-btn"><i class="ri-add-line"></i> Yükle</a>
                                <a href="#" id="quick-transfer" class="balance-action-btn"><i class="ri-arrow-right-line"></i> Transfer</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="fp-balance-card withdrawable-balance">
                        <div class="balance-icon">
                            <i class="ri-bank-card-line"></i>
                        </div>
                        <div class="balance-info">
                            <div class="balance-label">Çekilebilir Bakiye</div>
                            <div class="balance-amount"><?= number_format($user->balance2, 2) ?> ₺</div>
                            <div class="balance-actions">
                                <a href="#" id="quick-withdraw" class="balance-action-btn"><i class="ri-arrow-left-line"></i> Çek</a>
                                <a href="#" id="quick-history" class="balance-action-btn"><i class="ri-history-line"></i> Geçmiş</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- İşlem Sekmeleri -->
        <div class="fp-card-balance wallet-tabs-card">
            <div class="fp-card-balance-header tab-header">
                <ul class="wallet-tabs">
                    <li class="wallet-tab-item active" data-target="bakiye-ekle">
                        <i class="ri-add-line"></i> 
                        <span>Bakiye Yükle</span>
                    </li>
                    <?php if(getSetting('enable_balance_transfer') == '1'): ?>
                    <li class="wallet-tab-item" data-target="bakiye-transferi">
                        <i class="ri-exchange-funds-line"></i>
                        <span>Bakiye Transferi</span>
                    </li>
                    <?php endif; ?>
                    <li class="wallet-tab-item" data-target="bakiye-cekimi">
                        <i class="ri-bank-card-line"></i>
                        <span>Bakiye Çekimi</span>
                    </li>
                    <?php if(getSetting('enable_balance_exchange') == '1'): ?>
                    <li class="wallet-tab-item" data-target="bakiyeler-arasi">
                        <i class="ri-arrow-left-right-line"></i>
                        <span>Bakiyeler Arası</span>
                    </li>
                    <?php endif; ?>
                    <li class="wallet-tab-item" data-target="bakiye-gecmisi">
                        <i class="ri-history-line"></i>
                        <span>Bakiye Geçmişi</span>
                    </li>
                    <?php if(getSetting('enable_credit_operations') == '1'): ?>
                    <li class="wallet-tab-item" data-target="kredi-gecmisim">
                        <i class="ri-bank-line"></i>
                        <span>Cari Hesap İşlemlerim</span>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="fp-card-balance-body tab-content-wrapper">
                <!-- Bakiye Yükleme Sekmesi -->
                <div class="tab-content active" id="bakiye-ekle-content">
                    <div class="payment-methods-section mb-4">
                        <h4 class="section-title">Ödeme Yöntemi Seçin</h4>
                        <div class="payment-methods">
                            <?php 
                            // PaymentFactory'yi kullanarak aktif ödeme yöntemlerini getirelim
                            $CI =& get_instance();
                            $CI->load->library('PaymentFactory');
                            $payment_methods = $CI->paymentfactory->getActivePaymentMethods();
                            $default_method_id = $CI->paymentfactory->getDefaultPaymentMethodId();
                            $selected_method_id = $default_method_id;
                            
                            // En az bir aktif ödeme yöntemi var mı?
                            $has_active_payment = false;
                            
                            foreach($payment_methods as $key => $method): 
                                $is_active = ($key === 0 || $method->id === $default_method_id);
                                $has_active_payment = true;
                                
                                // İlk veya varsayılan metodu seçili yap
                                if ($is_active) {
                                    $selected_method_id = $method->id;
                                }
                                
                                // Ödeme metodunun komisyon oranını hesapla
                                $commission_rate = $CI->paymentfactory->getCommissionRate($method->id, $user->id);
                            ?>
                                <div class="payment-method-card <?= $is_active ? 'active' : '' ?>" data-method-id="<?= $method->id ?>" data-commission="<?= $commission_rate ?>">
                                    <?php if (!empty($method->icon) && file_exists(FCPATH . 'assets/img/payments/' . $method->icon)): ?>
                                        <img src="<?= base_url('assets/img/payments/' . $method->icon) ?>" alt="<?= $method->payment_name ?>">
                                    <?php else: ?>
                                        <i class="ri-bank-card-line method-icon"></i>
                                    <?php endif; ?>
                                    <span class="method-name"><?= $method->payment_name ?></span>
                                    <div class="method-info">
                                        <?php if($commission_rate === 0): ?>
                                            <span class="text-success">Komisyon Yok</span>
                                        <?php else: ?>
                                            +%<?= $commission_rate ?> Komisyon
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="payment-method-card" id="havale-eft-method">
                                <i class="ri-bank-line method-icon"></i>
                                <span class="method-name">Havale / EFT</span>
                                <div class="method-info">Ücretsiz</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kredi Kartı ile Ödeme Formu -->
                    <div class="payment-form" id="card-payment-form">
                        <div class="row">
                            <div class="col-lg-7">
                                <form action="<?= base_url('payment'); ?>" method="POST" class="card-form">
                                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
                                    <input type="hidden" name="payment_method" id="selected_payment_method" value="<?= $selected_method_id ?>">
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label">Yüklenecek Miktar</label>
                                        <div class="input-with-icon">
                                            <i class="ri-money-dollar-circle-line input-icon-left"></i>
                                            <input type="text" class="form-control price" name="amount" placeholder="Yüklemek istediğiniz tutarı girin" onchange="calculateAmount()">
                                            <span class="input-icon-right">TL</span>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                                        <i class="ri-shield-check-line me-2"></i> Güvenli Ödeme Yap
                                    </button>
                                </form>
                            </div>
                            <div class="col-lg-5">
                                <div class="payment-summary">
                                    <h5 class="summary-title">Ödeme Özeti</h5>
                                    <div class="summary-item">
                                        <span class="item-label">Yükleme Tutarı</span>
                                        <span class="item-value" id="base-amount">0.00 TL</span>
                                    </div>
                                    <div class="summary-item">
                                        <span class="item-label">Komisyon (<span id="commission-rate-display">%0</span>)</span>
                                        <span class="item-value" id="commission-amount">0.00 TL</span>
                                    </div>
                                    <div class="summary-total">
                                        <span class="total-label">Toplam Ödenecek</span>
                                        <span class="total-value" id="total-amount">0.00 TL</span>
                                    </div>
                                    <div class="payment-security">
                                        <i class="ri-shield-check-line"></i>
                                        <span>Güvenli SSL Bağlantısı ile ödemeleriniz korunmaktadır.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Havale/EFT Formu -->
                    <div class="payment-form" id="bank-transfer-form" style="display: none;">
                        <div class="row">
                            <div class="col-lg-6">
                                <?php echo form_open('client/balance/addTransfer'); ?>
                                <h5 class="form-title mb-3">Havale/EFT Bildirim Formu</h5>
                                <div class="form-group mb-3">
                                    <label class="form-label">Banka Seçin</label>
                                    <select class="form-select" name="bank" required>
                                        <option value="">Banka seçiniz</option>
                                        <?php foreach ($banks as $b) { ?>
                                            <option value="<?= $b->id ?>"><?= $b->bank_name ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Gönderen Adı Soyadı</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">İşlem Tarihi</label>
                                    <input type="date" class="form-control" name="date" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Yatırılan Tutar</label>
                                    <div class="input-with-icon">
                                        <i class="ri-money-dollar-circle-line input-icon-left"></i>
                                        <input type="number" class="form-control" name="price" required>
                                        <span class="input-icon-right">TL</span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ri-check-double-line me-2"></i> Bildirimi Gönder
                                </button>
                                <?php echo form_close(); ?>
                            </div>
                            <div class="col-lg-6">
                                <div class="bank-accounts">
                                    <h5 class="form-title mb-3">Banka Hesap Bilgilerimiz</h5>
                                    <div class="bank-accounts-container">
                                        <?php foreach ($banks as $bank) { ?>
                                            <div class="bank-account-card">
                                                <div class="bank-name"><?= $bank->bank_name ?></div>
                                                <div class="bank-info">
                                                    <div class="info-row">
                                                        <span class="info-label">IBAN:</span>
                                                        <span class="info-value"><?= $bank->iban ?></span>
                                                        <button class="copy-btn" data-copy="<?= $bank->iban ?>">
                                                            <i class="ri-file-copy-line"></i>
                                                        </button>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="info-label">Hesap Sahibi:</span>
                                                        <span class="info-value"><?= $bank->owner_name ?></span>
                                                    </div>
                                                </div>
                                                <div class="bank-note">
                                                    <i class="ri-information-line"></i> 
                                                    <span>Ödeme açıklamasına adınızı ve soyadınızı yazmayı unutmayın.</span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bakiye Transferi Sekmesi -->
                <div class="tab-content" id="bakiye-transferi-content">
                    <div class="row">
                        <div class="col-lg-7">
                            <?php if($has_active_credit): ?>
                            <!-- Aktif kredi uyarısı -->
                            <div class="alert alert-warning mb-4" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="ri-error-warning-line me-2 fs-5"></i>
                                    <div>
                                        <h5 class="mb-1">Cari Hesap Borcu Uyarısı</h5>
                                        <p class="mb-0">Aktif bir cari hesap borcunuz bulunduğu için bakiye transferi yapamazsınız. Lütfen önce mevcut cari hesap borcunuzu ödeyin.</p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <form action="<?= base_url('client/balance/transferBalance'); ?>" method="POST" class="transfer-form" <?php echo $has_active_credit ? 'style="opacity: 0.6; pointer-events: none;"' : ''; ?>>
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
                                <h4 class="section-title mb-3">Bakiye Transferi</h4>
                                <div class="form-group mb-3">
                                    <label class="form-label">Alıcı E-posta Adresi</label>
                                    <div class="input-with-icon">
                                        <i class="ri-mail-line input-icon-left"></i>
                                        <input type="email" class="form-control" name="recipient_email" required placeholder="ornek@mail.com" <?php echo $has_active_credit ? 'disabled' : ''; ?>>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Transfer Tutarı</label>
                                    <div class="input-with-icon">
                                        <i class="ri-money-dollar-circle-line input-icon-left"></i>
                                        <input type="number" class="form-control" name="amount" required min="1" placeholder="Minimum 1 TL" <?php echo $has_active_credit ? 'disabled' : ''; ?>>
                                        <span class="input-icon-right">TL</span>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Açıklama (İsteğe bağlı)</label>
                                    <textarea class="form-control" name="description" rows="3" placeholder="Transfer için açıklama ekleyin" <?php echo $has_active_credit ? 'disabled' : ''; ?>></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" <?php echo $has_active_credit ? 'disabled' : ''; ?>>
                                    <i class="ri-send-plane-line me-2"></i> Transferi Gerçekleştir
                                </button>
                            </form>
                        </div>
                        <div class="col-lg-5">
                            <div class="info-card transfer-info">
                                <div class="info-card-header">
                                    <i class="ri-information-line"></i>
                                    <h5>Transfer Bilgileri</h5>
                                </div>
                                <div class="info-card-body">
                                    <ul class="info-list">
                                        <li><i class="ri-check-line"></i> Minimum transfer tutarı 1 TL'dir.</li>
                                        <li><i class="ri-check-line"></i> Transfer işlemi anında gerçekleşir.</li>
                                        <li><i class="ri-check-line"></i> Transfer edilen bakiye geri alınamaz.</li>
                                        <li><i class="ri-check-line"></i> Sadece sistemde kayıtlı kullanıcılara transfer yapabilirsiniz.</li>
                                        <li><i class="ri-check-line"></i> Alıcının e-posta adresinin doğru olduğundan emin olun.</li>
                                        <?php if($has_active_credit): ?>
                                        <li><i class="ri-close-line text-danger"></i> <strong class="text-danger">Aktif cari hesap borcu olan kullanıcılar bakiye transferi yapamaz.</strong></li>
                                        <?php endif; ?>
                                    </ul>
                                    <div class="balance-reminder">
                                        <span>Mevcut Kullanılabilir Bakiyeniz:</span>
                                        <strong><?= number_format($user->balance, 2) ?> TL</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bakiye Çekimi Sekmesi -->
                <div class="tab-content" id="bakiye-cekimi-content">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="section-title mb-0">Bakiye Çekme</h4>
                                <a href="#modal-banka-hesabi" data-bs-toggle="modal" data-bs-target="#modal-banka-hesabi" class="btn btn-primary btn-sm">
                                    <i class="ri-bank-line icon icon-left"></i> Banka Hesabım
                                </a>
                            </div>
                            
                            <?php if (empty($user->bank_name) || empty($user->bank_owner) || empty($user->bank_iban)): ?>
                            <div class="alert alert-warning mb-4">
                                <i class="ri-alert-line me-2"></i>
                                Lütfen çekim talebi vermeden önce banka hesap bilgilerinizi ekleyin.
                            </div>
                            <?php else: ?>
                            <div class="bank-info-card mb-4">
                                <div class="bank-info-header">
                                    <?php
                                    $bankClass = '';
                                    
                                    switch($user->bank_name) {
                                        case 'İş Bankası':
                                            $bankClass = 'is-bank';
                                            break;
                                        case 'Akbank':
                                            $bankClass = 'akbank';
                                            break;
                                        case 'DenizBank':
                                            $bankClass = 'denizbank';
                                            break;
                                        case 'Garanti BBVA':
                                            $bankClass = 'garanti';
                                            break;
                                        case 'QNB Finansbank':
                                            $bankClass = 'finansbank';
                                            break;
                                        case 'Yapı Kredi Bankası':
                                            $bankClass = 'yapikredi';
                                            break;
                                        case 'Ziraat Bankası':
                                            $bankClass = 'ziraat';
                                            break;
                                        case 'Papara':
                                            $bankClass = 'papara';
                                            break;
                                        case 'İninal':
                                            $bankClass = 'ininal';
                                            break;
                                        case 'PEP':
                                            $bankClass = 'pep';
                                            break;
                                        default:
                                            $bankClass = 'default';
                                    }
                                    ?>
                                    
                                    <div class="bank-card <?= $bankClass ?>">
                                        <div class="bank-card-inner">
                                            <div class="bank-card-front">
                                                <div class="bank-card-header">
                                                    <div class="bank-name-wrapper">
                                                        <i class="ri-bank-line bank-icon"></i>
                                                        <span class="bank-name"><?= $user->bank_name ?></span>
                                                    </div>
                                                    
                                                    <div class="chip-icon">
                                                        <div class="chip"></div>
                                                    </div>
                                                </div>
                                                <div class="bank-card-body">
                                                    <div class="iban-number">
                                                        <?php 
                                                        // IBAN'ı 4'lü gruplar halinde gösterme
                                                        $formatted_iban = chunk_split(str_replace(' ', '', $user->bank_iban), 4, ' ');
                                                        echo $formatted_iban;
                                                        ?>
                                                    </div>
                                                    <div class="account-holder">
                                                        <?= $user->bank_owner ?>
                                                    </div>
                                                </div>
                                                <div class="bank-card-footer">
                                                    <span class="bank-label">Kayıtlı Banka Hesabınız</span>
                                                    <div class="card-network-logo">
                                                        <i class="ri-bank-card-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <form action="<?= base_url('client/balance/withdrawBalance'); ?>" method="POST" class="withdraw-form">
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Çekilecek Tutar</label>
                                    <div class="input-with-icon">
                                        <i class="ri-money-dollar-circle-line input-icon-left"></i>
                                        <input type="number" class="form-control" name="amount" required min="<?= $this->db->where('id', 1)->get('properties')->row()->min_draw ?>" max="<?= $user->balance2 ?>" placeholder="Minimum <?= $this->db->where('id', 1)->get('properties')->row()->min_draw ?> TL">
                                        <span class="input-icon-right">TL</span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ri-bank-card-line me-2"></i> Bakiye Çekme Talebi Oluştur
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-5 mt-2">
                            <div class="info-card withdraw-info">
                                <div class="info-card-header">
                                    <i class="ri-information-line"></i>
                                    <h5>Çekim Bilgileri</h5>
                                </div>
                                <div class="info-card-body">
                                    <ul class="info-list">
                                        <li><i class="ri-check-line"></i> Minimum çekim tutarı <?= $this->db->where('id', 1)->get('properties')->row()->min_draw ?> TL'dir.</li>
                                        <li><i class="ri-check-line"></i> Çekim işlemleri 24-48 saat içerisinde işleme alınır.</li>
                                        <li><i class="ri-check-line"></i> Hesap bilgilerinin doğruluğundan emin olunuz.</li>
                                        <li><i class="ri-check-line"></i> Çekilebilir bakiyeniz kadar çekim yapabilirsiniz.</li>
                                    </ul>
                                    <div class="balance-reminder">
                                        <span>Mevcut Çekilebilir Bakiyeniz:</span>
                                        <strong><?= number_format($user->balance2, 2) ?> TL</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Banka Hesabı Modal -->
                <div class="modal fade" id="modal-banka-hesabi" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Banka Hesabım</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="<?= base_url('client/balance/changeBank'); ?>" method="POST">
                                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
                                    <div class="mb-3">
                                        <label for="">Banka</label>
                                        <select class="form-select" name="bank_name" required>
                                            <option <?= $user->bank_name == "" ? "selected" : "" ?> value="">Banka Seçiniz</option>
                                            <option <?= $user->bank_name == "İş Bankası" ? "selected" : "" ?> value="İş Bankası">İş Bankası</option>
                                            <option <?= $user->bank_name == "Akbank" ? "selected" : "" ?> value="Akbank">Akbank</option>
                                            <option <?= $user->bank_name == "DenizBank" ? "selected" : "" ?> value="DenizBank">DenizBank</option>
                                            <option <?= $user->bank_name == "Garanti BBVA" ? "selected" : "" ?> value="Garanti BBVA">Garanti BBVA</option>
                                            <option <?= $user->bank_name == "QNB Finansbank" ? "selected" : "" ?> value="QNB Finansbank">QNB Finansbank</option>
                                            <option <?= $user->bank_name == "Yapı Kredi Bankası" ? "selected" : "" ?> value="Yapı Kredi Bankası">Yapı Kredi Bankası</option>
                                            <option <?= $user->bank_name == "Ziraat Bankası" ? "selected" : "" ?> value="Ziraat Bankası">Ziraat Bankası</option>
                                            <option <?= $user->bank_name == "Diğer" ? "selected" : "" ?> value="Diğer">Diğer</option>
                                            <option <?= $user->bank_name == "Papara" ? "selected" : "" ?> value="Papara">Papara</option>
                                            <option <?= $user->bank_name == "İninal" ? "selected" : "" ?> value="İninal">İninal</option>
                                            <option <?= $user->bank_name == "PEP" ? "selected" : "" ?> value="PEP">PEP</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="">Hesap Sahibi</label>
                                        <input type="text" class="form-control" name="bank_owner" value="<?= $user->bank_owner ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="">IBAN</label>
                                        <input type="text" class="form-control" name="bank_iban" value="<?= $user->bank_iban ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 d-block">Kaydet</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bakiyeler Arası Transfer Sekmesi -->
                <div class="tab-content" id="bakiyeler-arasi-content">
                    <div class="row">
                        <div class="col-lg-7">
                            <?php if($has_active_credit): ?>
                            <!-- Aktif kredi uyarısı -->
                            <div class="alert alert-warning mb-4" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="ri-error-warning-line me-2 fs-5"></i>
                                    <div>
                                        <h5 class="mb-1">Cari Hesap Borcu Uyarısı</h5>
                                        <p class="mb-0">Aktif bir cari hesap borcunuz bulunduğu için harcanabilir bakiyeden çekilebilir bakiyeye transfer yapamazsınız. Lütfen önce mevcut cari hesap borcunuzu ödeyin.</p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <form action="<?= base_url('client/balance/transferBetweenBalances'); ?>" method="POST" class="transfer-between-form">
                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
                                <h4 class="section-title mb-3">Bakiyeler Arası Transfer</h4>
                                
                                <!-- Görsel Transfer Yönü Seçimi -->
                                <div class="form-group mb-4">
                                    <label class="form-label">Transfer Yönü</label>
                                    <div class="transfer-direction-selector">
                                        <div class="direction-option" id="normal-to-withdraw-option" <?php echo $has_active_credit ? 'style="opacity: 0.6; pointer-events: none;"' : ''; ?>>
                                            <input type="radio" name="transfer_direction" id="normal_to_withdrawable" value="normal_to_withdrawable" <?php echo $has_active_credit ? 'disabled' : 'checked'; ?>>
                                            <label for="normal_to_withdrawable" class="direction-card">
                                                <div class="direction-card-inner">
                                                    <div class="balance-icons">
                                                        <div class="balance-icon-wrapper from">
                                                            <div class="balance-icon-circle">
                                                                <i class="ri-money-dollar-circle-line"></i>
                                                            </div>
                                                            <span>Kullanılabilir Bakiye</span>
                                                        </div>
                                                        <div class="direction-arrow">
                                                            <div class="arrow-body"></div>
                                                            <div class="arrow-head"></div>
                                                            <div class="commission-badge">%<?= getSetting('usable2withdraw_commission') ?? '5' ?> Komisyon</div>
                                                        </div>
                                                        <div class="balance-icon-wrapper to">
                                                            <div class="balance-icon-circle">
                                                                <i class="ri-bank-card-line"></i>
                                                            </div>
                                                            <span>Çekilebilir Bakiye</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        
                                        <div class="direction-option" id="withdraw-to-normal-option">
                                            <input type="radio" name="transfer_direction" id="withdrawable_to_normal" value="withdrawable_to_normal" <?php echo $has_active_credit ? 'checked' : ''; ?>>
                                            <label for="withdrawable_to_normal" class="direction-card">
                                                <div class="direction-card-inner">
                                                    <div class="balance-icons">
                                                        <div class="balance-icon-wrapper from">
                                                            <div class="balance-icon-circle">
                                                                <i class="ri-bank-card-line"></i>
                                                            </div>
                                                            <span>Çekilebilir Bakiye</span>
                                                        </div>
                                                        <div class="direction-arrow reverse">
                                                            <div class="arrow-body"></div>
                                                            <div class="arrow-head"></div>
                                                            <div class="commission-badge free">Ücretsiz</div>
                                                        </div>
                                                        <div class="balance-icon-wrapper to">
                                                            <div class="balance-icon-circle">
                                                                <i class="ri-money-dollar-circle-line"></i>
                                                            </div>
                                                            <span>Kullanılabilir Bakiye</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <input type="hidden" name="transfer_direction" id="transfer-direction" value="<?php echo $has_active_credit ? 'withdrawable_to_normal' : 'normal_to_withdrawable'; ?>">
                                </div>
                                
                                <!-- Transfer Görselleştirme -->
                                <div class="transfer-visualization mb-4">
                                    <div class="balance-visualization" id="normal-balance-vis">
                                        <div class="balance-vis-label">Kullanılabilir Bakiye</div>
                                        <div class="balance-vis-amount"><?= number_format($user->balance, 2) ?> TL</div>
                                        <div class="balance-vis-new-amount" id="normal-new-balance" style="display: none;">
                                            <div class="old-balance-strikethrough"><?= number_format($user->balance, 2) ?> TL</div>
                                            <div class="new-balance-value"><span></span></div>
                                        </div>
                                    </div>
                                    <div class="transfer-vis-arrow">
                                        <i class="ri-arrow-right-line"></i>
                                    </div>
                                    <div class="balance-visualization" id="withdrawable-balance-vis">
                                        <div class="balance-vis-label">Çekilebilir Bakiye</div>
                                        <div class="balance-vis-amount"><?= number_format($user->balance2, 2) ?> TL</div>
                                        <div class="balance-vis-new-amount" id="withdrawable-new-balance" style="display: none;">
                                            <div class="old-balance-strikethrough"><?= number_format($user->balance2, 2) ?> TL</div>
                                            <div class="new-balance-value"><span></span></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label">Transfer Tutarı</label>
                                    <div class="input-with-icon">
                                        <i class="ri-money-dollar-circle-line input-icon-left"></i>
                                        <input type="number" class="form-control" id="transfer-amount" name="amount" required min="10" placeholder="Minimum 10 TL">
                                        <span class="input-icon-right">TL</span>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="ri-arrow-left-right-line me-2"></i> Transferi Gerçekleştir
                                </button>
                            </form>
                        </div>
                        <div class="col-lg-5">
                            <div class="info-card transfer-between-info">
                                <div class="info-card-header">
                                    <i class="ri-information-line"></i>
                                    <h5>Transfer Bilgileri</h5>
                                </div>
                                <div class="info-card-body">
                                    <div id="normal-to-withdrawable-info">
                                        <div class="transfer-summary mb-4">
                                            <h6 class="summary-subtitle">Transfer Özeti</h6>
                                            <div class="summary-item">
                                                <span class="item-label">Transfer Tutarı</span>
                                                <span class="item-value" id="n2w-amount">0.00 TL</span>
                                            </div>
                                            <div class="summary-item">
                                                <span class="item-label">Komisyon (%<?= getSetting('usable2withdraw_commission') ?: '5' ?>)</span>
                                                <span class="item-value" id="n2w-commission">0.00 TL</span>
                                            </div>
                                            <div class="summary-total">
                                                <span class="total-label">Net Alacak</span>
                                                <span class="total-value" id="n2w-total">0.00 TL</span>
                                            </div>
                                        </div>
                                        <ul class="info-list">
                                            <li><i class="ri-check-line"></i> Kullanılabilir bakiyenizden çekilebilir bakiyenize transfer yapmak için %<?= getSetting('usable2withdraw_commission') ?: '5' ?> komisyon uygulanır.</li>
                                            <li><i class="ri-check-line"></i> Minimum transfer tutarı 10 TL'dir.</li>
                                            <li><i class="ri-check-line"></i> Transfer işlemi anında gerçekleşir.</li>
                                            <li><i class="ri-check-line"></i> Çekilebilir bakiyenize aktarılan tutar, banka hesabınıza çekilebilir.</li>
                                        </ul>
                                    </div>
                                    <div id="withdrawable-to-normal-info" style="display: none;">
                                        <div class="transfer-summary mb-4">
                                            <h6 class="summary-subtitle">Transfer Özeti</h6>
                                            <div class="summary-item">
                                                <span class="item-label">Transfer Tutarı</span>
                                                <span class="item-value" id="w2n-amount">0.00 TL</span>
                                            </div>
                                            <div class="summary-total">
                                                <span class="total-label">Net Alacak</span>
                                                <span class="total-value" id="w2n-total">0.00 TL</span>
                                            </div>
                                        </div>
                                        <ul class="info-list">
                                            <li><i class="ri-check-line"></i> Çekilebilir bakiyenizden kullanılabilir bakiyenize transfer ücretsizdir.</li>
                                            <li><i class="ri-check-line"></i> Minimum transfer tutarı 10 TL'dir.</li>
                                            <li><i class="ri-check-line"></i> Transfer işlemi anında gerçekleşir.</li>
                                            <li><i class="ri-check-line"></i> Kullanılabilir bakiyenize aktarılan tutar, site içi alışverişlerinizde kullanılabilir.</li>
                                        </ul>
                                    </div>
                                    <div class="balance-reminder">
                                        <span>Mevcut Kullanılabilir Bakiyeniz:</span>
                                        <strong><?= number_format($user->balance, 2) ?> TL</strong>
                                    </div>
                                    <div class="balance-reminder mt-2">
                                        <span>Mevcut Çekilebilir Bakiyeniz:</span>
                                        <strong><?= number_format($user->balance2, 2) ?> TL</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Bakiye Geçmişi Sekmesi -->
                <div class="tab-content" id="bakiye-gecmisi-content">
                    <div class="financial-history-container">
                        <div class="financial-history-header d-flex justify-content-between align-items-center">
                            <h4 class="section-title">Finansal İşlem Geçmişi</h4>
                            <div class="balance-type-filter">
                                <div class="btn-group" role="group" aria-label="Bakiye Tipi Filtresi">
                                    <button type="button" class="btn btn-sm btn-primary active" data-balance-type="spendable">Kullanılabilir Bakiye</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-balance-type="withdrawable">Çekilebilir Bakiye</button>
                                </div>
                            </div>
                        </div>
                    
                        <!-- Gelişmiş Filtreleme Paneli -->
                        <div class="advanced-filter-panel mb-4">
                            <div class="filter-header" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false">
                                <div class="filter-header-title">
                                    <i class="ri-filter-3-line"></i>
                                    <span>Gelişmiş Filtreler</span>
                                </div>
                                <div class="filter-toggle-icon">
                                    <i class="ri-arrow-down-s-line"></i>
                                </div>
                            </div>
                            <div class="collapse" id="filterCollapse">
                                <div class="filter-body pt-3">
                                    <form action="" method="GET" class="filter-form" id="transaction-filter-form">
                                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">İşlem Tipi</label>
                                                    <select class="form-select" name="transaction_type" id="transaction-type-filter">
                                                        <option value="">Tümü</option>
                                                        <option value="deposit">Bakiye Yükleme</option>
                                                        <option value="withdrawal">Bakiye Çekimi</option>
                                                        <option value="transfer_in">Gelen Transfer</option>
                                                        <option value="transfer_out">Giden Transfer</option>
                                                        <option value="balance_transfer">Bakiyeler Arası</option>
                                                        <option value="purchase">Satın Alma</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Başlangıç Tarihi</label>
                                                    <div class="input-with-icon">
                                                        <i class="ri-calendar-line input-icon-left"></i>
                                                        <input type="date" class="form-control" name="start_date" id="start-date-filter">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Bitiş Tarihi</label>
                                                    <div class="input-with-icon">
                                                        <i class="ri-calendar-line input-icon-left"></i>
                                                        <input type="date" class="form-control" name="end_date" id="end-date-filter">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">İşlem Durumu</label>
                                                    <select class="form-select" name="status" id="status-filter">
                                                        <option value="">Tümü</option>
                                                        <option value="0">Beklemede</option>
                                                        <option value="1">Onaylandı</option>
                                                        <option value="2">Reddedildi</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Tutar Aralığı (Min)</label>
                                                    <div class="input-with-icon">
                                                        <i class="ri-money-dollar-circle-line input-icon-left"></i>
                                                        <input type="number" class="form-control" name="min_amount" id="min-amount-filter" placeholder="Min. tutar">
                                                        <span class="input-icon-right">₺</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="form-label">Tutar Aralığı (Max)</label>
                                                    <div class="input-with-icon">
                                                        <i class="ri-money-dollar-circle-line input-icon-left"></i>
                                                        <input type="number" class="form-control" name="max_amount" id="max-amount-filter" placeholder="Max. tutar">
                                                        <span class="input-icon-right">₺</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 d-flex justify-content-end">
                                                <button type="button" class="btn btn-outline-secondary me-2" id="reset-filters">
                                                    <i class="ri-refresh-line me-1"></i> Filtreleri Sıfırla
                                                </button>
                                                <button type="button" class="btn btn-primary" id="apply-filters">
                                                    <i class="ri-filter-3-line me-1"></i> Uygula
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hızlı Filtre Etiketleri -->
                        <div class="quick-filter-tags mb-3">
                            <div class="filter-tag active" data-filter="all">
                                <i class="ri-exchange-dollar-line"></i> Tüm İşlemler
                            </div>
                            <div class="filter-tag" data-filter="deposit">
                                <i class="ri-add-circle-line"></i> Yüklemeler
                            </div>
                            <div class="filter-tag" data-filter="withdrawal">
                                <i class="ri-bank-card-line"></i> Çekimler
                            </div>
                            <div class="filter-tag" data-filter="transfer">
                                <i class="ri-exchange-funds-line"></i> Transferler
                            </div>
                            <div class="filter-tag" data-filter="purchase">
                                <i class="ri-shopping-cart-line"></i> Satın Almalar
                            </div>
                            <div class="filter-tag" data-filter="pending">
                                <i class="ri-time-line"></i> Bekleyenler
                            </div>
                        </div>
                        
                        <!-- İşlem Geçmişi Tablosu -->
                        <div class="transaction-history-table-wrapper">
                            <div class="transaction-history-table-container">
                                <table class="table transaction-history-table" id="transaction-history-table">
                                    <thead>
                                        <tr>
                                            <th width="5%"></th>
                                            <th width="20%">İşlem Tipi</th>
                                            <th width="15%">Tarih</th>
                                            <th width="15%">Tutar</th>
                                            <th width="15%">Bakiye</th>
                                            <th width="15%">Durum</th>
                                            <th width="10%">İşlem</th>
                                        </tr>
                                    </thead>
                                    <tbody id="transaction-history-body">
                                        <?php 
                                        if (!empty($all_transactions)){
                                            foreach($all_transactions as $transaction){ 
                                                // İşlem ikonunu belirleme
                                                $icon_class = '';
                                                switch($transaction->transaction_type) {
                                                    case 'deposit':
                                                        $icon_class = 'ri-add-circle-line transaction-icon deposit';
                                                        break;
                                                    case 'withdrawal':
                                                        $icon_class = 'ri-bank-card-line transaction-icon withdrawal';
                                                        break;
                                                    case 'transfer_in':
                                                        $icon_class = 'ri-arrow-left-circle-line transaction-icon transfer-in';
                                                        break;
                                                    case 'transfer_out':
                                                        $icon_class = 'ri-arrow-right-circle-line transaction-icon transfer-out';
                                                        break;
                                                    case 'balance_transfer':
                                                        $icon_class = 'ri-exchange-funds-line transaction-icon balance-transfer';
                                                        break;
                                                    case 'purchase':
                                                        $icon_class = 'ri-shopping-cart-line transaction-icon purchase';
                                                        break;
                                                    default:
                                                        $icon_class = 'ri-question-line transaction-icon other';
                                                }
                                        ?>
                                            <tr data-type="<?= $transaction->transaction_type ?>" data-date="<?= date('Y-m-d', strtotime($transaction->created_at)) ?>" data-status="<?= $transaction->status ?>" data-amount="<?= $transaction->amount ?>" data-balance-type="<?= $transaction->balance_type ?>">
                                                <td>
                                                    <i class="<?= $icon_class ?>"></i>
                                                </td>
                                                <td>
                                                    <div class="transaction-info">
                                                        <?php 
                                                        switch($transaction->transaction_type) {
                                                            case 'deposit':
                                                                echo '<div class="transaction-type">Bakiye Yükleme</div>';
                                                                break;
                                                            case 'withdrawal':
                                                                echo '<div class="transaction-type">Bakiye Çekimi</div>';
                                                                break;
                                                            case 'transfer_in':
                                                                echo '<div class="transaction-type">Gelen Transfer</div>';
                                                                break;
                                                            case 'transfer_out':
                                                                echo '<div class="transaction-type">Giden Transfer</div>';
                                                                break;
                                                            case 'balance_transfer':
                                                                echo '<div class="transaction-type">Bakiyeler Arası</div>';
                                                                break;
                                                            case 'purchase':
                                                                echo '<div class="transaction-type">Satın Alma</div>';
                                                                break;
                                                            default:
                                                                echo '<div class="transaction-type">Diğer İşlem</div>';
                                                        }
                                                        ?>
                                                        <div class="transaction-metadata">
                                                            <span class="balance-type-indicator"><?= $transaction->balance_type == 'spendable' ? 'Kullanılabilir' : 'Çekilebilir' ?></span>
                                                            <?php if(!empty($transaction->description)): ?>
                                                            <span class="transaction-description-preview"><?= mb_substr($transaction->description, 0, 20) ?><?= (mb_strlen($transaction->description) > 20) ? '...' : '' ?></span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="transaction-date">
                                                        <div class="date"><?= date('d.m.Y', strtotime($transaction->created_at)) ?></div>
                                                        <div class="time"><?= date('H:i', strtotime($transaction->created_at)) ?></div>
                                                    </div>
                                                </td>
                                                <td class="amount <?= $transaction->amount >= 0 ? 'positive' : 'negative' ?>">
                                                    <?= $transaction->amount >= 0 ? '+' : '' ?><?= number_format($transaction->amount, 2) ?> ₺
                                                </td>
                                                <td class="balance-after">
                                                    <?php if($transaction->status == 0): ?>
                                                        <span class="pending-balance-badge">
                                                            <i class="ri-time-line"></i> İşlem beklemede
                                                        </span>
                                                    <?php else: ?>
                                                        <?= number_format(isset($transaction->balance_after_transaction) ? $transaction->balance_after_transaction : 0, 2) ?> ₺
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                    if($transaction->status == 0): 
                                                        $estimated_completion = date('d.m.Y', strtotime($transaction->created_at . ' +1 day'));
                                                    ?>
                                                        <div class="status-container">
                                                            <span class="status-badge pending">Beklemede</span>
                                                            <div class="estimated-completion">Tahmini: <?= $estimated_completion ?></div>
                                                        </div>
                                                    <?php elseif($transaction->status == 1): ?>
                                                        <span class="status-badge completed">Onaylandı</span>
                                                    <?php else: ?>
                                                        <span class="status-badge rejected">Reddedildi</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary transaction-detail-btn" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#transactionDetailModal"
                                                            data-id="<?= $transaction->id ?>"
                                                            data-type="<?= $transaction->transaction_type ?>"
                                                            data-date="<?= date('d.m.Y H:i', strtotime($transaction->created_at)) ?>"
                                                            data-description="<?= $transaction->description ?>"
                                                            data-amount="<?= number_format($transaction->amount, 2) ?>"
                                                            data-balance-before="<?= number_format(isset($transaction->balance_before) ? $transaction->balance_before : 0, 2) ?>"
                                                            data-balance-after="<?= number_format(isset($transaction->balance_after_transaction) ? $transaction->balance_after_transaction : 0, 2) ?>"
                                                            data-status="<?= $transaction->status ?>"
                                                            data-payment-method="<?= isset($transaction->payment_method) ? $transaction->payment_method : '' ?>">
                                                        <i class="ri-information-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php 
                                            }
                                        } else {
                                        ?>
                                            <tr id="no-transactions-row">
                                                <td colspan="7" class="empty-table">
                                                    <div class="empty-state">
                                                        <div class="empty-state-icon">
                                                            <i class="ri-history-line"></i>
                                                        </div>
                                                        <h4>İşlem Geçmişi Bulunamadı</h4>
                                                        <p>Henüz finansal işlem geçmişiniz bulunmuyor. İlk işleminizi gerçekleştirdiğinizde burada görüntülenecektir.</p>
                                                        <a href="#" class="btn btn-primary wallet-tab-trigger" data-target="bakiye-ekle">Bakiye Ekle</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if (count($all_transactions) > 0): ?>
                            <div class="transaction-pagination mt-3">
                                <div class="transaction-stats">
                                    Toplam <span id="total-records"><?= count($all_transactions) ?></span> işlem
                                </div>
                                <nav aria-label="İşlem sayfaları">
                                    <ul class="pagination justify-content-end" id="transaction-pagination">
                                        <!-- Sayfalandırma dinamik olarak JS ile oluşturulacak -->
                                    </ul>
                                </nav>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Cari Hesap İşlemlerim Sekmesi -->
                <div class="tab-content" id="kredi-gecmisim-content">
                    <div class="container-fluid p-0">
                        <?php 
                        // Aktif cari hesap limitleri
                        if($has_credit_offers && !$has_active_credit): 
                            $offer = $credit_offers[0]; // İlk teklifi göster
                        ?>
                        <div class="row mb-4">
                            <div class="col-lg-7">
                                <div class="pre-approved-credit-card">
                                    <div class="credit-card-header">
                                        <div class="credit-icon">
                                            <i class="ri-bank-line"></i>
                                        </div>
                                        <div class="credit-title">
                                            <h4>Cari Hesap Teklifiniz</h4>
                                            <p>Size özel hazırlanmış cari hesap limitiniz</p>
                                        </div>
                                    </div>
                                    <div class="credit-card-body">
                                        <div class="credit-limit">
                                            <span class="credit-limit-label">Teklif Edilen Limit</span>
                                            <span class="credit-limit-amount"><?= number_format($offer->amount, 2) ?> ₺</span>
                                        </div>
                                        <div class="credit-features">
                                            <div class="credit-feature-item">
                                                <i class="ri-timer-line"></i>
                                                <span>Anında Alışveriş İmkanı</span>
                                            </div>
                                            <div class="credit-feature-item">
                                                <i class="ri-percent-line"></i>
                                                <span><?= $offer->fee_percentage > 0 ? '%'.$offer->fee_percentage.' İşlem Ücreti' : 'İşlem Ücreti Yok' ?></span>
                                            </div>
                                            <div class="credit-feature-item">
                                                <i class="ri-calendar-check-line"></i>
                                                <span><?= $offer->term_days ?> Gün Ödeme Vadesi</span>
                                            </div>
                                            <div class="credit-feature-item">
                                                <i class="ri-time-line"></i>
                                                <span>Son Geçerlilik: <?= date('d.m.Y', strtotime($offer->offer_valid_until)) ?></span>
                                            </div>
                                        </div>
                                        <div class="credit-cta">
                                            <a href="#" class="btn btn-credit-primary" id="show-credit-form" data-offer-id="<?= $offer->id ?>">Cari Hesabımı Açmak İstiyorum</a>
                                        </div>
                                    </div>
                                </div>
                                
                                <form id="credit-form" style="display: none;" action="<?= base_url('client/balance/acceptCreditOffer'); ?>" method="POST" class="credit-application-form mt-4">
                                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
                                    <h4 class="section-title mb-3">Cari Hesap Açılış Formu</h4>
                                    <div class="form-group mb-3">
                                        <label class="form-label">Talep Ettiğiniz Limit</label>
                                        <div class="input-with-icon">
                                            <i class="ri-money-dollar-circle-line input-icon-left"></i>
                                            <input type="number" class="form-control" id="credit-amount" name="amount" required min="<?= $offer->amount * 0.1 ?>" max="<?= $offer->amount ?>" value="<?= $offer->amount ?>" placeholder="Minimum <?= number_format($offer->amount * 0.1, 2) ?> TL" onchange="calculateCreditDetails()">
                                            <span class="input-icon-right">TL</span>
                                        </div>
                                    </div>
                                    <input type="hidden" id="credit-term" name="credit_term" value="<?= $offer->term_days ?>">
                                    <input type="hidden" name="offer_id" value="<?= $offer->id ?>">
                                    <div class="form-group mb-3">
                                        <div class="vade-info-box">
                                            <div class="vade-info-icon"><i class="ri-calendar-check-line"></i></div>
                                            <div class="vade-info-text">
                                                <span class="vade-info-label">Ödeme Vadesi</span>
                                                <span class="vade-info-value"><?= $offer->term_days ?> Gün</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="credit-terms-checkbox mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="accept-terms" name="accept_terms" required>
                                            <label class="form-check-label" for="accept-terms">
                                                Cari hesap sözleşmesini okudum ve kabul ediyorum.
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                                        <i class="ri-bank-line me-2"></i> Cari Hesabı Onayla ve Aç
                                    </button>
                                </form>
                            </div>
                            
                            <div class="col-lg-5">
                                <div class="credit-details-card" id="credit-details">
                                    <div class="credit-details-header">
                                        <h5><i class="ri-information-line me-2"></i> Cari Hesap Detayları</h5>
                                    </div>
                                    <div class="credit-details-body">
                                        <div class="credit-details-item">
                                            <span class="details-label">Talep Edilen Limit:</span>
                                            <span class="details-value" id="requested-amount"><?= number_format($offer->amount, 2) ?> TL</span>
                                        </div>
                                        <div class="credit-details-item">
                                            <span class="details-label">İşlem Ücreti (<?= $offer->fee_percentage ?>%):</span>
                                            <span class="details-value" id="processing-fee"><?= number_format($offer->amount * $offer->fee_percentage / 100, 2) ?> TL</span>
                                        </div>
                                        <div class="credit-details-item highlight">
                                            <span class="details-label">Kullanılabilir Net Limit:</span>
                                            <span class="details-value" id="net-amount"><?= number_format($offer->amount - ($offer->amount * $offer->fee_percentage / 100), 2) ?> TL</span>
                                        </div>
                                        <div class="credit-details-item">
                                            <span class="details-label">Geri Ödeme Tutarı:</span>
                                            <span class="details-value" id="total-payment"><?= number_format($offer->amount, 2) ?> TL</span>
                                        </div>
                                        <div class="credit-details-item">
                                            <span class="details-label">Ödeme Vadesi:</span>
                                            <span class="details-value"><?= $offer->term_days ?> Gün</span>
                                        </div>
                                        <div class="credit-details-item">
                                            <span class="details-label">Son Ödeme Tarihi:</span>
                                            <span class="details-value" id="payment-date"><?= date('d.m.Y', strtotime('+' . $offer->term_days . ' days')) ?></span>
                                        </div>
                                    </div>
                                    <div class="credit-details-footer">
                                        <div class="credit-note">
                                            <i class="ri-information-line"></i>
                                            <p>Cari hesap tutarını son ödeme tarihine kadar ödemeniz gerekmektedir.</p>
                                        </div>
                                        <a href="<?= base_url('sayfa/cari-hesap-sozlesmesi') ?>" class="btn btn-sm btn-outline-primary w-100 mt-2" id="view-credit-terms">
                                            <i class="ri-file-text-line me-1"></i> Cari Hesap Sözleşmesini İncele
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($has_active_credit): // Aktif kredisi olan kullanıcılar için cari hesap bilgileri ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="active-credit-detail-card">
                                    <div class="card-header">
                                        <h4 class="section-title mb-0">Aktif Cari Hesap Detayları</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="credit-info-list">
                                                    <div class="credit-info-item">
                                                        <span class="info-label">Toplam Limit:</span>
                                                        <span class="info-value"><?= number_format($active_credit->amount, 2) ?> ₺</span>
                                                    </div>
                                                    <div class="credit-info-item">
                                                        <span class="info-label">Kalan Borç:</span>
                                                        <span class="info-value"><?= number_format($active_credit->remaining_amount, 2) ?> ₺</span>
                                                    </div>
                                                    <div class="credit-info-item">
                                                        <span class="info-label">Açılış Tarihi:</span>
                                                        <span class="info-value"><?= date('d.m.Y', strtotime($active_credit->created_at)) ?></span>
                                                    </div>
                                                    <div class="credit-info-item">
                                                        <span class="info-label">Ödeme Vadesi (Gün):</span>
                                                        <span class="info-value"><?= $active_credit->term_days ?> Gün</span>
                                                    </div>
                                                    <div class="credit-info-item">
                                                        <span class="info-label">Son Ödeme Tarihi:</span>
                                                        <span class="info-value"><?= date('d.m.Y', strtotime($active_credit->due_date)) ?></span>
                                                    </div>
                                                    <div class="credit-info-item">
                                                        <span class="info-label">Ödeme Durumu:</span>
                                                        <?php
                                                        $status_class = '';
                                                        $status_text = '';
                                                        switch($active_credit->status){
                                                            case 1:
                                                                $status_class = 'pending';
                                                                $status_text = 'Ödeme Bekleniyor';
                                                                break;
                                                            case 2:
                                                                $status_class = 'completed';
                                                                $status_text = 'Ödendi';
                                                                break;
                                                            case 3:
                                                                $status_class = 'partial';
                                                                $status_text = 'Kısmi Ödendi';
                                                                break;
                                                            case 4:
                                                                $status_class = 'overdue';
                                                                $status_text = 'Vadesi Geçmiş';
                                                                break;
                                                            default:
                                                                $status_class = 'pending';
                                                                $status_text = 'Beklemede';
                                                        }
                                                        ?>
                                                        <span class="info-value status-badge <?= $status_class ?>"><?= $status_text ?></span>
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-4">
                                                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#creditPaymentModal">
                                                        <i class="ri-bank-card-line me-2"></i> Cari Hesap Borcunu Öde
                                                    </a>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-6">
                                                <div class="credit-payment-instruction mb-4">
                                                    <div class="instruction-header">
                                                        <i class="ri-information-line"></i>
                                                        <span>Ödeme Bilgileri</span>
                                                    </div>
                                                    <div class="instruction-body">
                                                        <p>Cari hesap borcunuzu son ödeme tarihine kadar ödemeniz gerekmektedir. Ödeme yapmak için cüzdana bakiye eklemelisiniz.</p>
                                                        <ul>
                                                        </ul>
                                                    </div>
                                                </div>
                                                
                                                <?php if(count($credit_payments) > 0): ?>
                                                <div class="payment-history">
                                                    <h6>Ödeme Geçmişi</h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                    <th>Tarih</th>
                                                                    <th>Tutar</th>
                                                                    <th>Ödeme Yöntemi</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach($credit_payments as $payment): ?>
                                                                <tr>
                                                                    <td><?= date('d.m.Y', strtotime($payment->created_at)) ?></td>
                                                                    <td><?= number_format($payment->amount, 2) ?> ₺</td>
                                                                    <td><?= $payment->payment_method ?></td>
                                                                </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if(!$has_active_credit && !$has_credit_offers): // Ne aktif cari hesap ne de limit varsa ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="no-credit-info-card">
                                    <div class="card-body d-flex flex-column align-items-center text-center p-5">
                                        <div class="no-credit-icon mb-4">
                                            <div class="icon-circle">
                                                <i class="ri-bank-line"></i>
                                            </div>
                                        </div>
                                        <h4 class="mb-3">Cari Hesap Limiti Bulunamadı</h4>
                                        <div class="credit-info-content mb-4">
                                            <p class="mb-3">Şu an için size özel hazırlanmış bir cari hesap limiti bulunmamaktadır. Cari hesap limitleri, alışveriş geçmişinize ve ödeme alışkanlıklarınıza göre sistem tarafından otomatik olarak oluşturulur.</p>
                                            <div class="credit-info-steps">
                                                <div class="credit-step">
                                                    <div class="step-icon"><i class="ri-shopping-cart-line"></i></div>
                                                    <div class="step-text">Alışveriş Yapın</div>
                                                </div>
                                                <div class="step-arrow"><i class="ri-arrow-right-line"></i></div>
                                                <div class="credit-step">
                                                    <div class="step-icon"><i class="ri-time-line"></i></div>
                                                    <div class="step-text">Düzenli Ödeyin</div>
                                                </div>
                                                <div class="step-arrow"><i class="ri-arrow-right-line"></i></div>
                                                <div class="credit-step">
                                                    <div class="step-icon"><i class="ri-bank-card-line"></i></div>
                                                    <div class="step-text">Limit Kazanın</div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="<?= base_url('client/ticket') ?>" class="btn btn-primary">
                                            <i class="ri-customer-service-2-line me-2"></i> Bizimle İletişime Geçin
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Cari Hesap Geçmişim Tablosu -->
                        <?php if(count($credit_history) > 0): ?>
                        <div class="row">
                            <div class="col-12">
                                <div id="credit-history" class="mt-4">
                                    <h4 class="section-title mb-3">Cari Hesap Geçmişim</h4>
                                    <div class="table-responsive">
                                        <table class="table custom-table">
                                            <thead>
                                                <tr>
                                                    <th>Tarih</th>
                                                    <th>Tutar</th>
                                                    <th>Vade (Gün)</th>
                                                    <th>Durum</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($credit_history as $credit): ?>
                                                <tr>
                                                    <td><?= date('d.m.Y', strtotime($credit->created_at)) ?></td>
                                                    <td><?= number_format($credit->amount, 2) ?> ₺</td>
                                                    <td><?= $credit->term_days ?> Gün</td>
                                                    <td>
                                                        <?php if($credit->status == 2): ?>
                                                        <span class="status-badge completed">Ödendi</span>
                                                        <?php elseif($credit->status == 4): ?>
                                                        <span class="status-badge overdue">Vadesi Geçmiş</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="row">
                            <div class="col-12">
                                <div id="no-credit-history" class="mt-4">
                                    <div class="alert alert-info">
                                        <i class="ri-information-line me-2"></i>
                                        Henüz cari hesap geçmişiniz bulunmamaktadır.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<!-- Kredi Ödeme Modal -->
<div class="modal fade" id="creditPaymentModal" tabindex="-1" aria-labelledby="creditPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="creditPaymentModalLabel">Cari Hesap Ödemesi Yap</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">
        <form action="<?= base_url('client/balance/payCreditDebt'); ?>" method="POST" id="creditPaymentForm">
          <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>" />
          <input type="hidden" name="credit_id" value="<?= $has_active_credit ? $active_credit->id : '' ?>">
          
          <div class="payment-summary mb-4">
            <div class="summary-item">
              <span class="item-label">Toplam Cari Hesap Tutarı:</span>
              <span class="item-value"><?= $has_active_credit ? number_format($active_credit->amount, 2) : '0.00' ?> TL</span>
            </div>
            <div class="summary-item">
              <span class="item-label">Önceki Ödemeler:</span>
              <span class="item-value"><?= $has_active_credit ? number_format($active_credit->amount - $active_credit->remaining_amount, 2) : '0.00' ?> TL</span>
            </div>
            <div class="summary-item">
              <span class="item-label">Kalan Borç:</span>
              <span class="item-value"><?= $has_active_credit ? number_format($active_credit->remaining_amount, 2) : '0.00' ?> TL</span>
            </div>
            <div class="summary-item">
              <span class="item-label">Son Ödeme Tarihi:</span>
              <span class="item-value"><?= $has_active_credit ? date('d.m.Y', strtotime($active_credit->due_date)) : '-' ?></span>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="paymentAmount" class="form-label">Ödenecek Tutar</label>
            <div class="input-group">
              <span class="input-group-text"><i class="ri-money-dollar-circle-line"></i></span>
              <input type="number" class="form-control" id="paymentAmount" name="amount" min="1" 
                     max="<?= $has_active_credit ? $active_credit->remaining_amount : '0' ?>" 
                     value="<?= $has_active_credit ? min($active_credit->remaining_amount, $user->balance) : '0' ?>" required>
              <span class="input-group-text">TL</span>
            </div>
            <div class="form-text">Mevcut kullanılabilir bakiyeniz: <strong><?= number_format($user->balance, 2) ?> TL</strong></div>
          </div>
          
          <?php if($has_active_credit && $user->balance < $active_credit->remaining_amount): ?>
          <div class="alert alert-info">
            <i class="ri-information-line me-2"></i>
            Bakiyeniz toplam borcu ödemek için yetersiz. Dilediğiniz miktarda kısmi ödeme yapabilirsiniz.
          </div>
          <?php endif; ?>
          
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="confirmPayment" required>
            <label class="form-check-label" for="confirmPayment">
              Kullanılabilir bakiyemden bu tutarın düşüleceğini onaylıyorum.
            </label>
          </div>
          
          <div class="d-grid">
            <button type="submit" class="btn btn-primary" <?= ($has_active_credit && $user->balance <= 0) ? 'disabled' : '' ?>>
              Ödemeyi Tamamla
            </button>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
      </div>
    </div>
  </div>
</div>

<!-- İşlem Detay Modal -->
<div class="modal fade" id="transactionDetailModal" tabindex="-1" aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="transactionDetailModalLabel">İşlem Detayları</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
      </div>
      <div class="modal-body">
        <div class="transaction-detail-header mb-3">
          <div class="transaction-type-badge"></div>
          <div class="transaction-date"></div>
        </div>
        
        <div class="transaction-detail-item">
          <div class="detail-label">İşlem No:</div>
          <div class="detail-value" id="detail-id"></div>
        </div>
        
        <div class="transaction-detail-item">
          <div class="detail-label">Açıklama:</div>
          <div class="detail-value" id="detail-description"></div>
        </div>
        
        <div class="transaction-detail-item">
          <div class="detail-label">Tutar:</div>
          <div class="detail-value" id="detail-amount"></div>
        </div>
        
        <div class="transaction-detail-item">
          <div class="detail-label">İşlem Öncesi Bakiye:</div>
          <div class="detail-value" id="detail-balance-before"></div>
        </div>
        
        <div class="transaction-detail-item">
          <div class="detail-label">İşlem Sonrası Bakiye:</div>
          <div class="detail-value" id="detail-balance-after"></div>
        </div>
        
        <div class="transaction-detail-item">
          <div class="detail-label">Ödeme Yöntemi:</div>
          <div class="detail-value" id="detail-payment-method"></div>
        </div>
        
        <div class="transaction-detail-item">
          <div class="detail-label">Durum:</div>
          <div class="detail-value" id="detail-status"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
      </div>
    </div>
  </div>
</div>

<script>
  // PHP'den JS'ye değişken aktarımı
  var usable2withdrawCommission = <?= getSetting('usable2withdraw_commission') ?: '5' ?>;
  var userNormalBalance = <?= $user->balance ?: 0 ?>;
  var userWithdrawableBalance = <?= $user->balance2 ?: 0 ?>;
  // İşlem Ücreti
  <?php $jsCommissionRate = (float)getCommission($user->id); ?>
  window.commissionRate = <?= $jsCommissionRate > 0 ? $jsCommissionRate : 5 ?>;
</script>

<script src="<?= base_url('assets/future/js/balance.js') ?>"></script>

<!-- JavaScript - URL Tab Parametresini İşleme -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // URL'den tab parametresini al
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    
    if (tabParam) {
        // Tab linkini bul ve tıkla
        const tabLinks = document.querySelectorAll('.wallet-tab-item');
        
        tabLinks.forEach(tabLink => {
            if (tabLink.getAttribute('data-target') === tabParam) {
                // Aktif sekme sınıfını kaldır
                document.querySelector('.wallet-tab-item.active').classList.remove('active');
                
                // İlgili sekmeyi aktif yap
                tabLink.classList.add('active');
                
                // Tüm içerikleri gizle
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // İlgili içeriği göster
                document.getElementById(tabParam + '-content').classList.add('active');
            }
        });
    }
});
</script>