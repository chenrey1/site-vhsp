<!-- Jodit ve SweetAlert2 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.7/jodit.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.7/jodit.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.7/languages/tr.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4/bootstrap-4.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Toplu Mail Gönderimi</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/mail/templates'); ?>">Mail Şablonları</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Toplu Mail Gönderimi</li>
                </ol>
            </nav>

            <!-- İstatistik Kartları -->
            <div class="row mb-2">
                <div class="col-xl-3 col-md-6">
                    <div class="bulk-mail-stats-card">
                        <div class="bulk-mail-stats-icon bulk-mail-bg-soft-primary">
                            <i class="fas fa-users bulk-mail-text-primary"></i>
                        </div>
                        <div class="bulk-mail-stats-info">
                            <h4><?= number_format($stats['total_users']) ?></h4>
                            <p>Aktif Kullanıcı</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="bulk-mail-stats-card">
                        <div class="bulk-mail-stats-icon bulk-mail-bg-soft-success">
                            <i class="fas fa-star bulk-mail-text-success"></i>
                        </div>
                        <div class="bulk-mail-stats-info">
                            <h4><?= number_format($stats['total_subscribers']) ?></h4>
                            <p>Aktif Abone</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="bulk-mail-stats-card">
                        <div class="bulk-mail-stats-icon bulk-mail-bg-soft-info">
                            <i class="fas fa-user-plus bulk-mail-text-info"></i>
                        </div>
                        <div class="bulk-mail-stats-info">
                            <h4><?= number_format($stats['new_users_7days']) ?></h4>
                            <p>Yeni Kullanıcı (7 Gün)</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="bulk-mail-stats-card">
                        <div class="bulk-mail-stats-icon bulk-mail-bg-soft-warning">
                            <i class="fas fa-clock bulk-mail-text-warning"></i>
                        </div>
                        <div class="bulk-mail-stats-info">
                            <h4><?= number_format($stats['inactive_users_30days']) ?></h4>
                            <p>İnaktif Kullanıcı (30 Gün)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mail Gönderim Formu -->
            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('admin/mail/send_bulk_mail') ?>" method="POST" id="bulkMailForm">
                        <!-- Mail Başlığı -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Mail Başlığı</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="subject" required 
                                    placeholder="Etkileyici bir başlık girin...">
                            </div>
                        </div>

                        <!-- Öncelik Seçimi -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Öncelik:</label>
                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="bulk-mail-priority-card" data-priority="1">
                                            <div class="card-body text-center">
                                                <input type="radio" name="priority" value="1" class="d-none priority-radio">
                                                <div class="bulk-mail-priority-icon">
                                                    <i class="fas fa-bolt fa-2x"></i>
                                                </div>
                                                <h5 class="card-title">Yüksek Öncelik</h5>
                                                <p class="card-text">
                                                    Diğer tüm mail isteklerinin önüne geçer. Acil bilgilendirmeler için kullanın.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bulk-mail-priority-card active" data-priority="2">
                                            <div class="card-body text-center">
                                                <input type="radio" name="priority" value="2" class="d-none priority-radio" checked>
                                                <div class="bulk-mail-priority-icon">
                                                    <i class="fas fa-clock fa-2x"></i>
                                                </div>
                                                <h5 class="card-title">Orta Öncelik</h5>
                                                <p class="card-text">
                                                    Normal bilgilendirme ve pazarlama mailleri için standart öncelik.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="bulk-mail-priority-card" data-priority="3">
                                            <div class="card-body text-center">
                                                <input type="radio" name="priority" value="3" class="d-none priority-radio">
                                                <div class="bulk-mail-priority-icon">
                                                    <i class="fas fa-hourglass-half fa-2x"></i>
                                                </div>
                                                <h5 class="card-title">Düşük Öncelik</h5>
                                                <p class="card-text">
                                                    Acil olmayan toplu mailler ve kampanya duyuruları için.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mail İçeriği -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">İçerik</label>
                            <div class="col-sm-10">
                                <textarea id="mailContent" class="form-control" name="content" rows="10"></textarea>
                            </div>
                        </div>

                        <!-- Alıcı Seçimi -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Alıcılar:</label>
                            <div class="col-sm-10">
                                <div class="bulk-mail-recipient-options">
                                    <div class="row">
                                        <!-- Temel Seçenekler -->
                                        <div class="col-md-6">
                                            <div class="bulk-mail-recipient-card mb-3">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" id="allUsers" name="recipient_type" value="all" checked>
                                                    <label class="custom-control-label" for="allUsers">
                                                        <i class="fas fa-globe mr-1"></i>
                                                        Tüm Aktif Kullanıcılar
                                                    </label>
                                                    <small class="text-muted d-block mt-1">
                                                        Mail almak isteyen ve "Bu maili gönder" seçeneği işaretli olan tüm aktif kullanıcılara gönderilecektir.
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="bulk-mail-recipient-card mb-3">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" id="allSubscribers" name="recipient_type" value="all_subscribers">
                                                    <label class="custom-control-label" for="allSubscribers">
                                                        <i class="fas fa-star mr-1"></i>
                                                        Tüm Aktif Aboneler
                                                    </label>
                                                    <small class="text-muted d-block mt-1">
                                                        Aktif aboneliği bulunan tüm kullanıcılara gönderilecektir.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="bulk-mail-recipient-card mb-3">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" id="specificSubscribers" name="recipient_type" value="specific_subscribers">
                                                    <label class="custom-control-label" for="specificSubscribers">
                                                        <i class="fas fa-tags mr-1"></i>
                                                        Belirli Abonelikler
                                                    </label>
                                                </div>
                                                <div class="specific-subscribers-options mt-2" style="display: none;">
                                                    <select class="form-control select2" name="subscription_ids[]" multiple>
                                                        <?php foreach($subscriptions as $subscription): ?>
                                                            <option value="<?= $subscription->id ?>"><?= $subscription->name ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Özel Seçenekler -->
                                        <div class="col-md-6">
                                            <div class="bulk-mail-recipient-card mb-3">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" id="inactiveUsers" name="recipient_type" value="inactive">
                                                    <label class="custom-control-label" for="inactiveUsers">
                                                        <i class="fas fa-user-clock mr-1"></i>
                                                        İnaktif Kullanıcılar
                                                    </label>
                                                </div>
                                                <div class="inactive-options mt-2" style="display: none;">
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="inactive_days" placeholder="Gün sayısı">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">gün</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="bulk-mail-recipient-card mb-3">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" id="newUsers" name="recipient_type" value="new">
                                                    <label class="custom-control-label" for="newUsers">
                                                        <i class="fas fa-user-plus mr-1"></i>
                                                        Yeni Kullanıcılar
                                                    </label>
                                                </div>
                                                <div class="new-users-options mt-2" style="display: none;">
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="new_user_days" placeholder="Gün sayısı">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">gün</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="bulk-mail-recipient-card mb-3">
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" id="referralUsers" name="recipient_type" value="referral">
                                                    <label class="custom-control-label" for="referralUsers">
                                                        <i class="fas fa-link mr-1"></i>
                                                        Referans Kodu ile Üye Olanlar
                                                    </label>
                                                </div>
                                                <div class="referral-options mt-2" style="display: none;">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="referral_code" placeholder="Referans kodunu giriniz">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Alıcı Sayısı -->
                                    <div class="bulk-mail-recipient-count mt-4">
                                        <div class="alert alert-info" id="recipientCount">
                                            <i class="fas fa-info-circle mr-2"></i>
                                            <span>Tüm aktif kullanıcılara gönderilecek.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gönderim Butonları -->
                        <div class="form-group row mt-4">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-primary" id="sendMailBtn">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Mail Gönder
                                </button>
                                <a href="<?= base_url('admin/mail/templates') ?>" class="btn btn-secondary ml-2">
                                    <i class="fas fa-times mr-2"></i>
                                    İptal
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

<script>
    // Jodit editör ayarları
    const editorConfig = {
        height: 350,
        language: 'tr',
        toolbarButtonSize: 'middle',
        toolbarAdaptive: false,
        buttons: [
            'source', '|',
            'bold', 'italic', 'underline', '|',
            'ul', 'ol', '|',
            'link', '|',
            'image', '|',
            'align', '|',
            'undo', 'redo', '|',
            'fullsize'
        ],
        uploader: {
            url: '<?= base_url("admin/mail/upload_image") ?>',
            format: 'json',
            method: 'POST',
            paramName: 'upload',
            withCredentials: false,
            sendAsFormData: true,
            headers: {},
            isSuccess: function (resp) {
                return !resp.error;
            },
            getMessage: function (resp) {
                return resp.message;
            },
            process: function (resp) {
                return {
                    files: resp.files,
                    path: resp.path,
                    baseurl: resp.baseurl,
                    error: resp.error,
                    message: resp.message
                };
            },
            defaultHandlerSuccess: function (data) {
                if (data.files && data.files.length) {
                    for (let file of data.files) {
                        this.selection.insertImage(file);
                    }
                }
            }
        },
        imageeditor: {
            crop: true,
            resize: true
        },
        showCharsCounter: false,
        showWordsCounter: false,
        showXPathInStatusbar: false,
        width: 'auto',
        allowResizeY: false,
        removeButtons: ['about', 'print', 'file'],
        disablePlugins: ['xpath'],
        beautifyHTML: false
    };

    $(document).ready(function() {
        // Jodit editörünü başlat
        const editor = new Jodit('#mailContent', editorConfig);

        // Alıcı kartlarına tıklama işlevi
        $('.bulk-mail-recipient-card').click(function(e) {
            // Eğer tıklanan yer select2 veya input alanı ise işlemi durdur
            if ($(e.target).closest('.select2').length || 
                $(e.target).closest('.input-group').length || 
                $(e.target).closest('input[type="number"]').length) {
                return;
            }
            
            // Radio butonu seç
            const radio = $(this).find('input[type="radio"]');
            radio.prop('checked', true);
            
            // Change event'ı tetikle
            radio.trigger('change');
        });

        // Alıcı sayısını güncelle
        function updateRecipientCount() {
            const formData = $('#bulkMailForm').serialize();
            
            $.ajax({
                url: '<?= base_url("admin/mail/get_recipient_count") ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success') {
                        $('#recipientCount').removeClass('alert-danger').addClass('alert-info')
                            .html(`<i class="fas fa-info-circle mr-1"></i> ${response.message}`);
                        $('#sendMailBtn').prop('disabled', false);
                    } else {
                        $('#recipientCount').removeClass('alert-info').addClass('alert-danger')
                            .html(`<i class="fas fa-exclamation-circle mr-1"></i> ${response.message}`);
                        $('#sendMailBtn').prop('disabled', true);
                    }
                },
                error: function() {
                    $('#recipientCount').removeClass('alert-info').addClass('alert-danger')
                        .html('<i class="fas fa-exclamation-circle mr-1"></i> Alıcı sayısı hesaplanırken bir hata oluştu.');
                    $('#sendMailBtn').prop('disabled', true);
                }
            });
        }

        // Debounce fonksiyonu
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Debounce ile güncelleme fonksiyonu
        const debouncedUpdate = debounce(updateRecipientCount, 300);

        // Alıcı seçenekleri için dinamik gösterme/gizleme
        $('input[name="recipient_type"]').change(function() {
            // Tüm seçenek alanlarını gizle
            $('.specific-subscribers-options, .referral-options, .inactive-options, .new-users-options, .no-purchase-options').hide();
            
            // Seçilen seçeneğe göre ilgili alanı göster
            switch($(this).val()) {
                case 'specific_subscribers':
                    $('.specific-subscribers-options').show();
                    break;
                case 'referral':
                    $('.referral-options').show();
                    break;
                case 'inactive':
                    $('.inactive-options').show();
                    break;
                case 'new':
                    $('.new-users-options').show();
                    break;
                case 'no_purchase':
                    $('.no-purchase-options').show();
                    break;
            }

            // Alıcı sayısını güncelle
            debouncedUpdate();
        });

        // Ürün almama periyodu seçeneği için
        $('input[name="no_purchase_period"]').change(function() {
            if($(this).val() === 'recent') {
                $('.no-purchase-days').show();
            } else {
                $('.no-purchase-days').hide();
            }
            debouncedUpdate();
        });

        // Diğer input değişikliklerini dinle
        $('input[name="inactive_days"], input[name="new_user_days"], input[name="no_purchase_days"], input[name="referral_code"], select[name="subscription_ids[]"]').on('change keyup', function() {
            debouncedUpdate();
        });

        // Sayfa yüklendiğinde alıcı sayısını hesapla
        updateRecipientCount();

        // Form gönderimini yakala
        $('#bulkMailForm').on('submit', function(e) {
            e.preventDefault();
            
            if(!editor.value.trim()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Hata!',
                    text: 'Mail içeriği boş olamaz.',
                    confirmButtonText: 'Tamam'
                });
                return;
            }

            Swal.fire({
                title: 'Emin misiniz?',
                text: 'Mail gönderimi başlatılacak. Bu işlem geri alınamaz!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Evet, Gönder',
                cancelButtonText: 'İptal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        // Öncelik kartları için tıklama işlevi
        $('.bulk-mail-priority-card').click(function() {
            // Aktif sınıfını kaldır
            $('.bulk-mail-priority-card').removeClass('active');
            // Tıklanan kartı aktif yap
            $(this).addClass('active');
            // Radio butonunu seç
            $(this).find('.priority-radio').prop('checked', true);
        });
    });
</script> 