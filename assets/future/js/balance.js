/* Balance Sayfası JavaScript Kodları */

// Bakiye Yükleme Hesaplamaları
function calculateAmount() {
    let amount = parseFloat($('.price').val()) || 0;
    
    // Seçilen ödeme yönteminin komisyon oranını al
    let commissionRate = 0; 
    
    // Eğer aktif bir ödeme yöntemi kartı varsa, onun komisyon oranını kullan
    let activeCard = $('.payment-method-card.active');
    
    if (activeCard.length > 0 && activeCard.attr('id') !== 'havale-eft-method') {
        // Komisyon oranını data-commission attribute'undan doğrudan al
        commissionRate = parseFloat(activeCard.data('commission'));
    }
    
    // Komisyon tutarını doğru hesaplama (önce hesaplama, sonra yuvarlama)
    let commission = (amount * commissionRate) / 100;
    let total = amount + commission;
    
    // 2 ondalığa yuvarlama (round kullanarak)
    total = Math.round(total * 100) / 100;
    
    $('#base-amount').text(amount.toFixed(2) + ' TL');
    $('#commission-amount').text(commission.toFixed(2) + ' TL');
    $('#total-amount').text(total.toFixed(2) + ' TL');
    
    // Komisyon oranı görüntüleme alanını güncelle
    if (commissionRate === 0) {
        $('#commission-rate-display').text('0');
    } else {
        $('#commission-rate-display').text('%' + commissionRate);
    }
}

// Sayaç Fonksiyonu
function updateCountdown() {
    const dueDate = new Date($('#credit-countdown').data('due-date'));
    const now = new Date();
    
    // Kalan süreyi hesapla
    let diff = dueDate - now;
    
    // Zaman geçmişse
    if (diff <= 0) {
        $('#days').text('00');
        $('#hours').text('00');
        $('#minutes').text('00');
        $('#seconds').text('00');
        return;
    }
    
    // Gün, saat, dakika, saniye hesaplamaları
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    diff -= days * (1000 * 60 * 60 * 24);
    
    const hours = Math.floor(diff / (1000 * 60 * 60));
    diff -= hours * (1000 * 60 * 60);
    
    const minutes = Math.floor(diff / (1000 * 60));
    diff -= minutes * (1000 * 60);
    
    const seconds = Math.floor(diff / 1000);
    
    // Sayaç değerlerini güncelle
    $('#days').text(days < 10 ? `0${days}` : days);
    $('#hours').text(hours < 10 ? `0${hours}` : hours);
    $('#minutes').text(minutes < 10 ? `0${minutes}` : minutes);
    $('#seconds').text(seconds < 10 ? `0${seconds}` : seconds);
}

// Bakiyeler Arası Transfer Hesaplamaları
function calculateTransferDetails() {
    // Normal bakiyeden çekilebilir bakiyeye transfer
    const amount = parseFloat($('#transfer-amount').val()) || 0;
    
    // Komisyon oranını PHP'den gelen değerden al (getSetting fonksiyonu üzerinden)
    // Bu değişken balance.php dosyasında <?= getSetting('usable2withdraw_commission') ?: 5 ?> ile tanımlanıyor
    let commissionRate = 0;
    
    if (typeof usable2withdrawCommission !== 'undefined' && !isNaN(usable2withdrawCommission)) {
        commissionRate = usable2withdrawCommission / 100;
    }
    
    // Transfer yönünü al
    const direction = $('input[name="transfer_direction"]:checked').val();
    
    // Bakiye değerlerini al
    let normalBalance = 0;
    let withdrawableBalance = 0;
    
    if (typeof userNormalBalance !== 'undefined' && !isNaN(userNormalBalance)) {
        normalBalance = userNormalBalance;
    }
    
    if (typeof userWithdrawableBalance !== 'undefined' && !isNaN(userWithdrawableBalance)) {
        withdrawableBalance = userWithdrawableBalance;
    }
    
    // Hata mesajı gösterimi için konteyner element
    let errorContainer = $('#transfer-error-message');
    if (!errorContainer.length) {
        // Eğer hata mesajı konteyner yoksa oluştur
        $('.transfer-between-form .form-group:last').after('<div id="transfer-error-message" class="alert alert-danger mt-3" style="display: none;"></div>');
        errorContainer = $('#transfer-error-message');
    }
    
    // Bakiye kontrolü
    if (amount > 0) {
        if (direction === 'normal_to_withdrawable' && amount > normalBalance) {
            // Kullanılabilir bakiye yetersiz
            errorContainer.html('<i class="ri-error-warning-line me-2"></i> Kullanılabilir bakiyeniz yetersiz! Maksimum ' + normalBalance.toFixed(2) + ' TL transfer yapabilirsiniz.').show();
            // Submit butonunu devre dışı bırak
            $('.transfer-between-form button[type="submit"]').prop('disabled', true);
            // Görselleştirmeleri gizle
            $('.balance-vis-new-amount').hide();
            $('.balance-vis-amount').show();
            return;
        } else if (direction === 'withdrawable_to_normal' && amount > withdrawableBalance) {
            // Çekilebilir bakiye yetersiz
            errorContainer.html('<i class="ri-error-warning-line me-2"></i> Çekilebilir bakiyeniz yetersiz! Maksimum ' + withdrawableBalance.toFixed(2) + ' TL transfer yapabilirsiniz.').show();
            // Submit butonunu devre dışı bırak
            $('.transfer-between-form button[type="submit"]').prop('disabled', true);
            // Görselleştirmeleri gizle
            $('.balance-vis-new-amount').hide();
            $('.balance-vis-amount').show();
            return;
        } else {
            // Hata mesajını gizle
            errorContainer.hide();
            // Submit butonunu aktif et
            $('.transfer-between-form button[type="submit"]').prop('disabled', false);
        }
    }
    
    // Normal -> Çekilebilir
    const commission = amount * commissionRate;
    const n2wTotal = amount - commission;
    
    $('#n2w-amount').text(amount.toFixed(2) + ' TL');
    $('#n2w-commission').text(commission.toFixed(2) + ' TL');
    $('#n2w-total').text(n2wTotal.toFixed(2) + ' TL');
    
    // Çekilebilir -> Normal
    $('#w2n-amount').text(amount.toFixed(2) + ' TL');
    $('#w2n-total').text(amount.toFixed(2) + ' TL');
    
    // Bakiye görselleştirmelerini güncelle
    updateBalanceVisualizations(amount, n2wTotal);
}

// Bakiye transferi görselleştirmelerini güncelle
function updateBalanceVisualizations(amount, netAmount) {
    const direction = $('input[name="transfer_direction"]:checked').val();
    
    // Değişkenleri güvenli bir şekilde al
    let normalBalance = 0;
    let withdrawableBalance = 0;
    
    // Sayfa yüklenirken tanımlanan global değişkenleri kullan (eğer tanımlandıysa)
    if (typeof userNormalBalance !== 'undefined' && !isNaN(userNormalBalance)) {
        normalBalance = userNormalBalance;
    }
    
    if (typeof userWithdrawableBalance !== 'undefined' && !isNaN(userWithdrawableBalance)) {
        withdrawableBalance = userWithdrawableBalance;
    }
    
    // Başlangıçta tüm yeni bakiye gösterimlerini gizle
    $('.balance-vis-new-amount').hide();
    $('.balance-vis-amount').show();
    
    if (amount <= 0) {
        return; // Tutar 0 veya daha az ise hesaplama yapma
    }
    
    if (direction === 'normal_to_withdrawable') {
        // Normal -> Çekilebilir yönünde
        const newNormalBalance = normalBalance - amount;
        const newWithdrawableBalance = withdrawableBalance + netAmount;
        
        // Yeni bakiye değerlerini güncelle ve göster
        $('#normal-new-balance .new-balance-value span').text(newNormalBalance.toFixed(2) + ' TL');
        $('#withdrawable-new-balance .new-balance-value span').text(newWithdrawableBalance.toFixed(2) + ' TL');
        
        // Mevcut bakiye değerlerini gizle ve yeni bakiye değerlerini göster
        $('.balance-vis-amount').hide();
        $('.balance-vis-new-amount').show();
        
        // Ok yönünü sağa ayarla
        $('.transfer-vis-arrow i').removeClass('ri-arrow-left-line').addClass('ri-arrow-right-line');
        
    } else {
        // Çekilebilir -> Normal yönünde
        const newWithdrawableBalance = withdrawableBalance - amount;
        const newNormalBalance = normalBalance + amount; // Bu yönde komisyon yok
        
        // Yeni bakiye değerlerini güncelle ve göster
        $('#withdrawable-new-balance .new-balance-value span').text(newWithdrawableBalance.toFixed(2) + ' TL');
        $('#normal-new-balance .new-balance-value span').text(newNormalBalance.toFixed(2) + ' TL');
        
        // Mevcut bakiye değerlerini gizle ve yeni bakiye değerlerini göster
        $('.balance-vis-amount').hide();
        $('.balance-vis-new-amount').show();
        
        // Ok yönünü sola ayarla
        $('.transfer-vis-arrow i').removeClass('ri-arrow-right-line').addClass('ri-arrow-left-line');
    }
}

// Kredi Detayları Hesaplama
function calculateCreditDetails() {
    const amount = parseFloat($('#credit-amount').val()) || 0;
    const term = parseInt($('#credit-term').val()) || 30;
    const interestRate = 0.02; // %2 işlem ücreti
    
    const interest = amount * interestRate;
    const totalAmount = amount + interest;
    
    $('#credit-interest').text(interest.toFixed(2) + ' TL');
    $('#credit-total').text(totalAmount.toFixed(2) + ' TL');
}

// Filtre İşlemleri
function applyTransactionFilters() {
    const typeFilter = $('#transaction-type-filter').val();
    const startDateFilter = $('#start-date-filter').val();
    const endDateFilter = $('#end-date-filter').val();
    const statusFilter = $('#status-filter').val();
    
    // Tüm satırları göster
    $('#transaction-history-body tr').show();
    $('#no-transactions-row').hide();
    
    // Filtreleri uygula
    $('#transaction-history-body tr').each(function() {
        let show = true;
        
        // İşlem tipi filtresi
        if (typeFilter && $(this).data('type') !== typeFilter) {
            show = false;
        }
        
        // Tarih filtresi
        if (startDateFilter) {
            const rowDate = new Date($(this).data('date'));
            const startDate = new Date(startDateFilter);
            
            if (rowDate < startDate) {
                show = false;
            }
        }
        
        if (endDateFilter) {
            const rowDate = new Date($(this).data('date'));
            const endDate = new Date(endDateFilter);
            endDate.setDate(endDate.getDate() + 1); // Bitiş tarihini dahil etmek için
            
            if (rowDate > endDate) {
                show = false;
            }
        }
        
        // Durum filtresi
        if (statusFilter && $(this).data('status').toString() !== statusFilter) {
            show = false;
        }
        
        if (show) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    
    // Hiç sonuç yoksa boş mesajı göster
    if ($('#transaction-history-body tr:visible').length === 0) {
        $('#no-transactions-row').show();
    }
}

// Banka Hesabı Kopyalama
function copyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    document.body.removeChild(textarea);
    
    // Bildirim
    Swal.fire({
        title: 'Kopyalandı!',
        text: 'Bilgi panoya kopyalandı.',
        icon: 'success',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
}

// Bakiyeler arası transfer yönü değişim olayı için gerekli fonksiyon
function updateTransferDirection(direction) {
    if (direction === 'normal_to_withdrawable') {
        $('#normal-to-withdrawable-info').show();
        $('#withdrawable-to-normal-info').hide();
        $('#transfer-direction').val('normal_to_withdrawable');
        
        // Görsel olarak ok yönünü değiştir (sağa)
        $('.transfer-vis-arrow i').removeClass('ri-arrow-left-line').addClass('ri-arrow-right-line');
    } else {
        $('#normal-to-withdrawable-info').hide();
        $('#withdrawable-to-normal-info').show();
        $('#transfer-direction').val('withdrawable_to_normal');
        
        // Görsel olarak ok yönünü değiştir (sola)
        $('.transfer-vis-arrow i').removeClass('ri-arrow-right-line').addClass('ri-arrow-left-line');
    }
}

// Sayfanın Yüklenmesi Sırasında İşlemler
$(document).ready(function() {
    // Sayaç güncelleme
    if ($('#credit-countdown').length) {
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }
    
    // Sayfa yüklendiğinde varsayılan ödeme yöntemini ayarla
    var defaultMethodId = $('#selected_payment_method').val();
    
    // Tüm ödeme yöntemlerinin aktifliğini kaldır
    $('.payment-method-card').removeClass('active');
    
    // Varsayılan ödeme yöntemini aktif yap
    const defaultCard = $('.payment-method-card[data-method-id="' + defaultMethodId + '"]');
    defaultCard.addClass('active');
    
    // Varsayılan ödeme yönteminin komisyon oranını al ve göster
    if (defaultCard.length > 0) {
        let defaultCommissionRate = parseFloat(defaultCard.data('commission'));
        
        // Komisyon oranı görüntüleme alanını güncelle
        if (defaultCommissionRate === 0) {
            $('#commission-rate-display').text('0');
        } else {
            $('#commission-rate-display').text('%' + defaultCommissionRate);
        }
    }
    
    // Sayfa yüklendiğinde varsayılan komisyon oranını hesapla
    calculateAmount();
    
    // Sayfa yüklendiğinde varsayılan transfer yönünü ayarla
    const defaultDirection = $('input[name="transfer_direction"]:checked').val() || 'normal_to_withdrawable';
    updateTransferDirection(defaultDirection);
    
    // Sayfa yüklendiğinde varsayılan hesaplamaları yap
    calculateTransferDetails();
    
    // Transfer tutarı için değişim olayı ekle (input event)
    $('#transfer-amount').on('input', function() {
        calculateTransferDetails();
    });
    
    // Transfer yönü değişimi için olay ekle
    $('input[name="transfer_direction"]').on('change', function() {
        const direction = $(this).val();
        updateTransferDirection(direction);
        calculateTransferDetails();
    });
    
    // Ödeme yöntemi seçimi
    $(document).on('click', '.payment-method-card', function(e) {
        e.preventDefault();
        
        if ($(this).attr('id') === 'havale-eft-method') {
            // Havale/EFT seçildiğinde
            $('.payment-method-card').removeClass('active');
            $(this).addClass('active');
            
            // Kredi kartı formunu gizle
            $('#card-payment-form').hide();
            // Havale formunu göster
            $('#bank-transfer-form').show();
        } else {
            // Diğer ödeme metotları seçildiğinde
            $('.payment-method-card').removeClass('active');
            $(this).addClass('active');
            
            // Kredi kartı formunu göster
            $('#card-payment-form').show();
            // Havale formunu gizle
            $('#bank-transfer-form').hide();
            
            // Seçilen ödeme metodunu input'a ata
            $('#selected_payment_method').val($(this).data('method-id'));
            
            // Komisyon oranını güncelle
            let commissionRate = $(this).data('commission');
            $('#commission-rate-display').text('%' + commissionRate);
            
            // Tutarı yeniden hesapla
            calculateAmount();
        }
    });
    
    // Tutar değiştiğinde hesapla
    $('.price').on('input', function() {
        calculateAmount();
    });
    
    // Kredi tutarı değişince hesapla
    $('#credit-amount').on('input', calculateCreditDetails);
    
    // Cüzdan sekmeleri geçişi
    $('.wallet-tab-item').on('click', function() {
        const targetId = $(this).data('target');
        $('.wallet-tab-item').removeClass('active');
        $(this).addClass('active');
        
        $('.tab-content').removeClass('active');
        $(`#${targetId}-content`).addClass('active');
    });
    
    // Filtre uygulama
    $('#apply-filters').on('click', applyTransactionFilters);
    
    // Filtreleri sıfırlama
    $('#reset-filters').on('click', function() {
        $('#transaction-type-filter').val('');
        $('#start-date-filter').val('');
        $('#end-date-filter').val('');
        $('#status-filter').val('');
        
        applyTransactionFilters();
    });
    
    // IBAN Kopyalama
    $('.copy-btn').on('click', function() {
        const textToCopy = $(this).data('copy');
        copyToClipboard(textToCopy);
    });
    
    // Kredi formunu gösterme
    $('#show-credit-form').on('click', function(e) {
        e.preventDefault();
        $('.pre-approved-credit-card').fadeOut(300, function() {
            $('#credit-form').fadeIn(300);
        });
    });
    
    // İşlem detayları modalı
    $('.transaction-detail-btn').on('click', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        const date = $(this).data('date');
        const description = $(this).data('description') || 'Açıklama bulunmuyor.';
        const amount = $(this).data('amount');
        const balanceBefore = $(this).data('balance-before');
        const balanceAfter = $(this).data('balance-after');
        const status = $(this).data('status');
        const paymentMethod = $(this).data('payment-method') || '-';
        
        // İşlem tipini belirleme
        let typeBadge = '';
        switch(type) {
            case 'deposit':
                typeBadge = '<span class="transaction-badge deposit">Bakiye Yükleme</span>';
                break;
            case 'withdrawal':
                typeBadge = '<span class="transaction-badge withdrawal">Bakiye Çekimi</span>';
                break;
            case 'transfer_in':
                typeBadge = '<span class="transaction-badge transfer-in">Gelen Transfer</span>';
                break;
            case 'transfer_out':
                typeBadge = '<span class="transaction-badge transfer-out">Giden Transfer</span>';
                break;
            case 'balance_transfer':
                typeBadge = '<span class="transaction-badge balance-transfer">Bakiyeler Arası</span>';
                break;
            case 'purchase':
                typeBadge = '<span class="transaction-badge purchase">Satın Alma</span>';
                break;
            case 'refund':
                typeBadge = '<span class="transaction-badge refund">İade</span>';
                break;
            case 'bonus_cancel':
                typeBadge = '<span class="transaction-badge bonus-cancel">Bonus İptal</span>';
                break;
            default:
                typeBadge = '<span class="transaction-badge other">Diğer İşlem</span>';
        }
        
        // Durum belirteci
        let statusBadge = '';
        if (status == 0) {
            statusBadge = '<span class="status-badge pending">Beklemede</span>';
        } else if (status == 1) {
            statusBadge = '<span class="status-badge completed">Onaylandı</span>';
        } else {
            statusBadge = '<span class="status-badge rejected">Reddedildi</span>';
        }
        
        // Modal içeriğini güncelle - ID'leri düzeltildi, transaction-date seçicisi daha spesifik hale getirildi
        $('#detail-id').text(id);
        $('.transaction-type-badge').html(typeBadge);
        $('#transactionDetailModal .transaction-date').text(date);
        $('#detail-description').text(description);
        $('#detail-amount').text(amount + ' TL');
        $('#detail-balance-before').text(balanceBefore + ' TL');
        $('#detail-balance-after').text(balanceAfter + ' TL');
        $('#detail-status').html(statusBadge);
        $('#detail-payment-method').text(paymentMethod);
    });
    
    // Hızlı butonlar
    $('#quick-deposit').on('click', function(e) {
        e.preventDefault();
        $('.wallet-tab-item[data-target="bakiye-ekle"]').click();
    });
    
    $('#quick-transfer').on('click', function(e) {
        e.preventDefault();
        $('.wallet-tab-item[data-target="bakiye-transferi"]').click();
    });
    
    $('#quick-withdraw').on('click', function(e) {
        e.preventDefault();
        $('.wallet-tab-item[data-target="bakiye-cekimi"]').click();
    });
    
    $('#quick-history').on('click', function(e) {
        e.preventDefault();
        $('.wallet-tab-item[data-target="bakiye-gecmisi"]').click();
    });
    
    // Bakiye tipi filtreleme
    const balanceTypeButtons = $('.balance-type-filter button');
    
    // Sayfa yüklendiğinde varsayılan filtreyi uygula (aktif butonun değerini al)
    const defaultBalanceType = $('.balance-type-filter button.active').data('balance-type');
    filterTransactionsByBalanceType(defaultBalanceType);
    
    // Butonlara tıklama olayları ekle
    balanceTypeButtons.on('click', function() {
        // Aktif butonu değiştir
        balanceTypeButtons.removeClass('active');
        balanceTypeButtons.addClass('btn-outline-primary').removeClass('btn-primary');
        
        $(this).addClass('active');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        
        // Seçilen bakiye tipine göre filtreleme yap
        const balanceType = $(this).data('balance-type');
        filterTransactionsByBalanceType(balanceType);
    });
});

// Gelişmiş Filtreler Paneli
document.addEventListener('DOMContentLoaded', function() {
    // Filter Panel Toggle
    const filterHeader = document.querySelector('.filter-header');
    if (filterHeader) {
        filterHeader.addEventListener('click', function() {
            const targetId = this.getAttribute('data-bs-target');
            const targetCollapse = document.querySelector(targetId);
            
            // Bootstrap 5 ile collapse işlemi
            const bsCollapse = new bootstrap.Collapse(targetCollapse, {
                toggle: true
            });
            
            // Açılıp kapanma durumuna göre ok simgesini döndür
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            this.setAttribute('aria-expanded', !isExpanded);
            
            // Ok simgesini döndür
            const toggleIcon = this.querySelector('.filter-toggle-icon i');
            if (isExpanded) {
                toggleIcon.style.transform = 'rotate(0deg)';
            } else {
                toggleIcon.style.transform = 'rotate(180deg)';
            }
        });
    }
    
    // Hızlı Filtre Etiketleri
    const filterTags = document.querySelectorAll('.filter-tag');
    if (filterTags.length > 0) {
        filterTags.forEach(tag => {
            tag.addEventListener('click', function() {
                // Aktif etiketi değiştir
                filterTags.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                const filterValue = this.getAttribute('data-filter');
                // filterTransactions fonksiyonu ile karıştırmamak için
                applyQuickFilter(filterValue);
            });
        });
    }
    
    // Filtreleri Uygula Butonu
    const applyFiltersBtn = document.getElementById('apply-filters');
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            applyAdvancedFilters();
        });
    }
    
    // Filtreleri Sıfırla Butonu
    const resetFiltersBtn = document.getElementById('reset-filters');
    if (resetFiltersBtn) {
        resetFiltersBtn.addEventListener('click', function() {
            resetFilters();
        });
    }
    
    // İşlem Geçmişi Sayfalama
    initPagination();
});

// Gelişmiş filtreleri uygula
function applyAdvancedFilters() {
    const transactionType = document.getElementById('transaction-type-filter').value;
    const startDate = document.getElementById('start-date-filter').value;
    const endDate = document.getElementById('end-date-filter').value;
    const status = document.getElementById('status-filter').value;
    const minAmount = document.getElementById('min-amount-filter').value;
    const maxAmount = document.getElementById('max-amount-filter').value;
    
    const rows = document.querySelectorAll('#transaction-history-body tr');
    
    rows.forEach(row => {
        let show = true;
        
        // İşlem tipi filtresi
        if (transactionType && row.getAttribute('data-type') !== transactionType) {
            show = false;
        }
        
        // Başlangıç tarihi filtresi
        if (startDate && new Date(row.getAttribute('data-date')) < new Date(startDate)) {
            show = false;
        }
        
        // Bitiş tarihi filtresi
        if (endDate && new Date(row.getAttribute('data-date')) > new Date(endDate)) {
            show = false;
        }
        
        // Durum filtresi
        if (status && row.getAttribute('data-status') !== status) {
            show = false;
        }
        
        // Tutar aralığı (min)
        if (minAmount && minAmount.trim() !== '') {
            const rowAmount = parseFloat(row.getAttribute('data-amount').replace(/[^\d.-]/g, ''));
            if (rowAmount < parseFloat(minAmount)) {
                show = false;
            }
        }
        
        // Tutar aralığı (max)
        if (maxAmount && maxAmount.trim() !== '') {
            const rowAmount = parseFloat(row.getAttribute('data-amount').replace(/[^\d.-]/g, ''));
            if (rowAmount > parseFloat(maxAmount)) {
                show = false;
            }
        }
        
        row.style.display = show ? '' : 'none';
    });
    
    // No records mesajını göster/gizle
    toggleNoRecordsMessage();
    
    // Sayfalamayı güncelle
    updatePagination();
    
    // Accordion'u kapat
    try {
        const filterCollapse = document.getElementById('filterCollapse');
        if (filterCollapse) {
            const bsCollapse = new bootstrap.Collapse(filterCollapse);
            bsCollapse.hide();
        }
    } catch (error) {
        // Filtre panelini kapatırken hata
        // Alternatif yöntem - jQuery ile
        $('#filterCollapse').collapse('hide');
    }
}

// Filtreleri sıfırla
function resetFilters() {
    // Form elemanlarını sıfırla
    document.getElementById('transaction-type-filter').value = '';
    document.getElementById('start-date-filter').value = '';
    document.getElementById('end-date-filter').value = '';
    document.getElementById('status-filter').value = '';
    document.getElementById('min-amount-filter').value = '';
    document.getElementById('max-amount-filter').value = '';
    
    // Tüm satırları göster
    const rows = document.querySelectorAll('#transaction-history-body tr');
    rows.forEach(row => {
        row.style.display = '';
    });
    
    // Hızlı filtre etiketlerini sıfırla
    const filterTags = document.querySelectorAll('.filter-tag');
    filterTags.forEach(tag => tag.classList.remove('active'));
    document.querySelector('.filter-tag[data-filter="all"]').classList.add('active');
    
    // No records mesajını güncelle
    toggleNoRecordsMessage();
    
    // Sayfalamayı güncelle
    updatePagination();
}

// Boş durum mesajını kontrol et
function toggleNoRecordsMessage() {
    const rows = document.querySelectorAll('#transaction-history-body tr:not([style*="display: none"])');
    const noRecordsRow = document.getElementById('no-transactions-row');
    
    if (rows.length === 0 && !noRecordsRow) {
        // Boş durum mesajı yoksa oluştur
        const tbody = document.getElementById('transaction-history-body');
        const newRow = document.createElement('tr');
        newRow.id = 'no-transactions-row';
        newRow.innerHTML = `
            <td colspan="7" class="empty-table">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="ri-filter-off-line"></i>
                    </div>
                    <h4>Sonuç Bulunamadı</h4>
                    <p>Seçtiğiniz filtrelerle eşleşen işlem bulunamadı. Lütfen filtreleri değiştirerek tekrar deneyin.</p>
                    <button class="btn btn-outline-primary" onclick="resetFilters()" style="display: inline-flex; align-items: center;">
                        <i class="ri-refresh-line" style="font-size: 20px; margin-right: 8px;"></i> Filtreleri Sıfırla
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(newRow);
    } else if (rows.length > 0 && noRecordsRow) {
        // Boş durum mesajı varsa kaldır
        noRecordsRow.remove();
    }
}

// Sayfalama başlat
function initPagination() {
    const table = document.getElementById('transaction-history-table');
    const paginationContainer = document.getElementById('transaction-pagination');
    
    if (!table || !paginationContainer) return;
    
    // Her sayfada gösterilecek kayıt sayısı
    const pageSize = 10;
    
    // Toplam kayıt sayısı
    const rows = table.querySelectorAll('tbody tr:not([style*="display: none"]):not(#no-transactions-row)');
    const totalRecords = rows.length;
    
    // Toplam sayfa sayısı
    const totalPages = Math.ceil(totalRecords / pageSize);
    
    // Sayfalama butonlarını oluştur
    createPaginationButtons(totalPages, 1);
    
    // İlk sayfayı göster
    showPage(1, pageSize);
    
    // Toplam kayıt sayısını güncelle
    const totalRecordsElement = document.getElementById('total-records');
    if (totalRecordsElement) {
        totalRecordsElement.textContent = totalRecords;
    }
}

// Sayfalama butonlarını oluştur
function createPaginationButtons(totalPages, currentPage) {
    const paginationContainer = document.getElementById('transaction-pagination');
    if (!paginationContainer) return;
    
    // Sayfalama butonlarını temizle
    paginationContainer.innerHTML = '';
    
    // Önceki sayfa butonu
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `
        <a class="page-link" href="#" aria-label="Previous" ${currentPage > 1 ? `onclick="changePage(${currentPage - 1}); return false;"` : ''}>
            <span aria-hidden="true">&laquo;</span>
        </a>
    `;
    paginationContainer.appendChild(prevLi);
    
    // Sayfa butonları
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    
    // En az 5 sayfa göster
    if (endPage - startPage < 4) {
        startPage = Math.max(1, endPage - 4);
    }
    
    for (let i = startPage; i <= endPage; i++) {
        const pageLi = document.createElement('li');
        pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
        pageLi.innerHTML = `
            <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
        `;
        paginationContainer.appendChild(pageLi);
    }
    
    // Sonraki sayfa butonu
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
    nextLi.innerHTML = `
        <a class="page-link" href="#" aria-label="Next" ${currentPage < totalPages ? `onclick="changePage(${currentPage + 1}); return false;"` : ''}>
            <span aria-hidden="true">&raquo;</span>
        </a>
    `;
    paginationContainer.appendChild(nextLi);
}

// Sayfa değiştir
function changePage(page) {
    const pageSize = 10;
    showPage(page, pageSize);
    
    // Sayfalama butonlarını güncelle
    const rows = document.querySelectorAll('#transaction-history-body tr:not([style*="display: none"]):not(#no-transactions-row)');
    const totalRecords = rows.length;
    const totalPages = Math.ceil(totalRecords / pageSize);
    
    createPaginationButtons(totalPages, page);
    
    // Sayfanın başına dön
    const tableContainer = document.querySelector('.transaction-history-table-wrapper');
    if (tableContainer) {
        tableContainer.scrollIntoView({ behavior: 'smooth' });
    }
}

// Belirli bir sayfayı göster
function showPage(page, pageSize) {
    const rows = document.querySelectorAll('#transaction-history-body tr:not([style*="display: none"]):not(#no-transactions-row)');
    
    const startIndex = (page - 1) * pageSize;
    const endIndex = startIndex + pageSize;
    
    rows.forEach((row, index) => {
        if (index >= startIndex && index < endIndex) {
            row.classList.remove('d-none');
        } else {
            row.classList.add('d-none');
        }
    });
}

// Hızlı filtre uygula
function applyQuickFilter(filterValue) {
    const rows = document.querySelectorAll('#transaction-history-body tr');
    
    if (filterValue === 'all') {
        // Tüm satırları göster
        rows.forEach(row => row.style.display = '');
    } else if (filterValue === 'deposit') {
        // Yükleme işlemlerini göster
        rows.forEach(row => {
            row.style.display = row.getAttribute('data-type') === 'deposit' ? '' : 'none';
        });
    } else if (filterValue === 'withdrawal') {
        // Çekim işlemlerini göster
        rows.forEach(row => {
            row.style.display = row.getAttribute('data-type') === 'withdrawal' ? '' : 'none';
        });
    } else if (filterValue === 'purchase') {
        // Satın alma işlemlerini göster
        rows.forEach(row => {
            row.style.display = row.getAttribute('data-type') === 'purchase' ? '' : 'none';
        });
    } else if (filterValue === 'transfer') {
        // Transfer işlemlerini göster
        rows.forEach(row => {
            row.style.display = (
                row.getAttribute('data-type') === 'transfer_in' || 
                row.getAttribute('data-type') === 'transfer_out' || 
                row.getAttribute('data-type') === 'balance_transfer'
            ) ? '' : 'none';
        });
    } else if (filterValue === 'pending') {
        // Bekleyen işlemleri göster
        rows.forEach(row => {
            row.style.display = row.getAttribute('data-status') === '0' ? '' : 'none';
        });
    }
    
    // No records mesajını kontrol et
    toggleNoRecordsMessage();
    
    // Sayfalamayı güncelle
    updatePagination();
}

// Bakiye tipine göre işlemleri filtrele
function filterTransactionsByBalanceType(balanceType) {
    const transactionRows = $('.transaction-history-table tbody tr');
    
    if (balanceType === 'all') {
        // Tüm işlemleri göster
        transactionRows.show();
    } else {
        // Önce tüm satırları gizle
        transactionRows.hide();
        
        // Seçilen bakiye tipine ait işlemleri göster
        $(`.transaction-history-table tbody tr[data-balance-type="${balanceType}"]`).show();
    }
    
    // No records mesajını kontrol et
    toggleNoRecordsMessage();
    
    // Filtreleme sonrası sayfalama bilgisini güncelle
    updatePagination();
}

// Sayfalama bilgisini güncelle - Bu fonksiyonu document ready bloğunun dışına taşıdık
function updatePagination() {
    // Görünür olan satırların sayısını hesapla
    const visibleRows = $('.transaction-history-table tbody tr:visible').length;
    
    // Sayfalama bilgisi mevcut ise güncelle
    if ($('.transaction-pagination').length) {
        if (visibleRows === 0) {
            $('.transaction-pagination').hide();
            $('.no-transactions-message').show();
        } else {
            $('.transaction-pagination').show();
            $('.no-transactions-message').hide();
            
            // Burada sayfalama mantığı uygulanacak
            // ...
        }
    }
    
    // Table içindeki görünür satır sayısını güncelle
    const totalRecordsElement = document.getElementById('total-records');
    if (totalRecordsElement) {
        totalRecordsElement.textContent = visibleRows;
    }
    
    // Sayfalama yeniden oluşturulmalı
    if (typeof initPagination === 'function') {
        initPagination();
    }
} 