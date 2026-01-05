<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.7/jodit.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.7/jodit.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jodit/3.24.7/languages/tr.js"></script>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Mail Şablonları</h5>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('admin'); ?>">Panel</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Mail Şablonları</li>
                </ol>
            </nav>
            <div class="card">
                <div class="card-body">
                    <div class="text-right mb-3">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#addTemplate">
                            <i class="fa fa-plus"></i> Yeni Şablon
                        </button>
                        <a href="<?= base_url('admin/mail/bulk_mail') ?>" class="btn btn-success ml-2">
                            <i class="fa fa-envelope"></i> Toplu Mail Gönder
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered" id="templateTable">
                            <thead>
                            <tr>
                                <th>Durum</th>
                                <th>Kod</th>
                                <th>İsim</th>
                                <th>Konu</th>
                                <th>Oluşturma</th>
                                <th>İşlemler</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($templates as $template): ?>
                                <tr>
                                    <td>
                                        <?php if($template->is_active): ?>
                                            <?php if($template->send_mail): ?>
                                                <span class="badge badge-success">Gönderilecek</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Gönderilmeyecek</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Pasif Şablon</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><code><?= $template->code ?></code></td>
                                    <td><?= $template->name ?></td>
                                    <td><?= $template->subject ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($template->created_at)) ?></td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-sm btn-info edit-template"
                                           data-id="<?= $template->id ?>">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/mail/test_template/'.$template->id) ?>"
                                           class="btn btn-sm btn-warning">
                                            <i class="fa fa-envelope"></i>
                                        </a>
                                        <a href="<?= base_url('admin/mail/delete_template/'.$template->id) ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Emin misiniz?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </main>


<!-- Yeni Şablon Modal -->
<div class="modal fade" id="addTemplate" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Mail Şablonu</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= base_url('admin/mail/add_template') ?>" method="POST">
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Mail Başlığı:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Şablon Kodu:</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="code" required>
                                <option value="">Seçiniz...</option>
                                <option value="welcome_mail">Hoşgeldin Maili (welcome_mail)</option>
                                <option value="mail_verification">Mail Doğrulama (mail_verification)</option>
                                <option value="password_reset">Şifre Sıfırlama (password_reset)</option>
                                <option value="default">Varsayılan (default)</option>
                                <option value="guest_registration">Misafir Kayıt (guest_registration)</option>
                                <option value="new_order">Yeni Sipariş (new_order)</option>
                                <option value="order_delivery">Sipariş Teslimatı (order_delivery)</option>
                                <option value="cancel_delivery">Sipariş İptali (cancel_delivery)</option>
                                <option value="balance_success">Bakiye Yükleme Başarılı (balance_success)</option>
                                <option value="subscription_start">Abonelik Başlangıç (subscription_start)</option>
                                <option value="ticket_reply">Destek Talebi Yanıt (ticket_reply)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Mail Konusu:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="subject" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">İçerik:</label>
                        <div class="col-sm-10">
                            <!-- Jodit Classic için textarea -->
                            <textarea id="joditEditorAdd" class="form-control" name="content" rows="10"></textarea>
                            <div class="mt-2">
                                <strong>Kullanılabilir Değişkenler:</strong><br>
                                <!-- Genel değişkenler -->
                                <div id="commonVariables">
                                    <code>{name}</code> - Kullanıcı adı<br>
                                    <code>{surname}</code> - Kullanıcı soyadı<br>
                                    <code>{email}</code> - E-posta adresi<br>
                                    <code>{company_name}</code> - Site adı<br>
                                    <code>{company_logo}</code> - Site logosu<br>
                                    <code>{company_url}</code> - Site URL<br>
                                    <code>{support_email}</code> - Destek e-posta adresi<br>
                                </div>
                                <!-- Şablona özel değişkenler -->
                                <div id="templateSpecificVariables"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Mail Gönderimi:</label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="sendMail" name="send_mail" value="1" checked>
                                <label class="custom-control-label" for="sendMail">Bu maili gönder</label>
                            </div>
                            <small class="form-text text-muted">Bu seçenek işaretlenmezse, ilgili aksiyon gerçekleştiğinde mail gönderilmeyecektir.</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Kopya Gönderimi:</label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="sendCopy" name="send_copy" value="1">
                                <label class="custom-control-label" for="sendCopy">Bir kopyasını gönder</label>
                            </div>
                            <div id="copyEmailContainer" style="display: none;" class="mt-2">
                                <input type="email" class="form-control" name="copy_email" id="copyEmail" placeholder="Kopya gönderilecek mail adresi">
                                <small class="form-text text-muted">Bu adrese, gönderilen her mailin bir kopyası gönderilecektir.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
    </main>
</div>

<!-- Düzenle Şablon Modal -->
<div class="modal fade" id="editTemplateModal" tabindex="-1" role="dialog" aria-labelledby="editTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTemplateModalLabel">Şablonu Düzenle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Kapat">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editTemplateForm" method="POST">
                <div class="modal-body">
                    <!-- Form alanları burada dinamik olarak doldurulacak -->
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Şablon Adı:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" id="editTemplateName" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Şablon Kodu:</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="code" id="editTemplateCode" required>
                                <option value="">Seçiniz...</option>
                                <option value="welcome_mail">Hoşgeldin Maili (welcome_mail)</option>
                                <option value="mail_verification">Mail Doğrulama (mail_verification)</option>
                                <option value="password_reset">Şifre Sıfırlama (password_reset)</option>
                                <option value="default">Varsayılan (default)</option>
                                <option value="guest_registration">Misafir Kayıt (guest_registration)</option>
                                <option value="new_order">Yeni Sipariş (new_order)</option>
                                <option value="order_delivery">Sipariş Teslimatı (order_delivery)</option>
                                <option value="cancel_delivery">Sipariş İptali (cancel_delivery)</option>
                                <option value="balance_success">Bakiye Yükleme Başarılı (balance_success)</option>
                                <option value="subscription_start">Abonelik Başlangıç (subscription_start)</option>
                                <option value="ticket_reply">Destek Talebi Yanıt (ticket_reply)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Mail Konusu:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="subject" id="editTemplateSubject" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">İçerik:</label>
                        <div class="col-sm-10">
                            <!-- Jodit Classic için textarea -->
                            <textarea id="joditEditorEdit" class="form-control" name="content" rows="10"></textarea>
                            <div class="mt-2">
                                <strong>Kullanılabilir Değişkenler:</strong><br>
                                <!-- Genel değişkenler -->
                                <div id="editCommonVariables">
                                    <code>{name}</code> - Kullanıcı adı<br>
                                    <code>{surname}</code> - Kullanıcı soyadı<br>
                                    <code>{email}</code> - E-posta adresi<br>
                                    <code>{company_name}</code> - Site adı<br>
                                    <code>{company_logo}</code> - Site logosu<br>
                                    <code>{company_url}</code> - Site URL<br>
                                    <code>{support_email}</code> - Destek e-posta adresi<br>
                                </div>
                                <!-- Şablona özel değişkenler -->
                                <div id="editTemplateSpecificVariables"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Mail Gönderimi:</label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="editSendMail" name="send_mail" value="1">
                                <label class="custom-control-label" for="editSendMail">Bu maili gönder</label>
                            </div>
                            <small class="form-text text-muted">Bu seçenek işaretlenmezse, ilgili aksiyon gerçekleştiğinde mail gönderilmeyecektir.</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Kopya Gönderimi:</label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="editSendCopy" name="send_copy" value="1">
                                <label class="custom-control-label" for="editSendCopy">Bir kopyasını gönder</label>
                            </div>
                            <div id="editCopyEmailContainer" style="display: none;" class="mt-2">
                                <input type="email" class="form-control" name="copy_email" id="editCopyEmail" placeholder="Kopya gönderilecek mail adresi">
                                <small class="form-text text-muted">Bu adrese, gönderilen her mailin bir kopyası gönderilecektir.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Test Mail Modal -->
<div class="modal fade" id="testMailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Mail Gönder</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="mb-3">
                    <strong>Şablon: </strong>
                    <span id="preview_name"></span>
                </p>
                <form id="testMailForm">
                    <input type="hidden" id="template_id" name="template_id">
                    <div class="form-group">
                        <label for="test_email">Mail Adresi:</label>
                        <input type="email" class="form-control" id="test_email" name="test_email" required 
                               placeholder="Test mailinin gönderileceği adresi giriniz">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">İptal</button>
                <button type="button" class="btn btn-primary" id="sendTestMail">Gönder</button>
            </div>
        </div>
    </div>
</div>

<script>
    const SITE_URL = '<?= base_url(); ?>';

    // Jodit örneklerini tutacak değişkenler
    let joditAddEditor = null;
    let joditEditEditor = null;
    let bulkMailEditor = null;

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
            url: SITE_URL + 'admin/mail/upload_image',
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
        // Resim boyutlandırma ayarları
        imageeditor: {
            crop: true,
            resize: true
        },
        // Diğer ayarlar
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
        // DataTables ayarları
        $('#templateTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Turkish.json'
            },
            order: [[4, 'desc']],
            responsive: true,
            pageLength: 25
        });

        // "Yeni Şablon" Modal açılınca
        $('#addTemplate').on('shown.bs.modal', function () {
            if (!joditAddEditor) {
                joditAddEditor = new Jodit('#joditEditorAdd', editorConfig);
            }
        });

        // Yeni Şablon modal kapandığında içeriği sıfırla
        $('#addTemplate').on('hidden.bs.modal', function () {
            if (joditAddEditor) {
                joditAddEditor.value = '';
            }
            // Formu sıfırla
            $('#addTemplate form')[0].reset();
        });

        // "Düzenle" butonuna tıklanınca
        $(document).on('click', '.edit-template', function() {
            const templateId = $(this).data('id');

            $.ajax({
                url: SITE_URL + 'admin/mail/get_template/' + templateId,
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Form alanlarını doldur
                    $('#editTemplateName').val(data.name);
                    $('#editTemplateCode').val(data.code);
                    $('#editTemplateSubject').val(data.subject);
                    $('#editTemplateForm').attr('action', SITE_URL + 'admin/mail/edit_template/' + templateId);
                    $('#editSendMail').prop('checked', data.send_mail == 1);
                    $('#editSendCopy').prop('checked', data.send_copy == 1);
                    $('#editCopyEmail').val(data.copy_email);
                    
                    // Kopya mail alanını göster/gizle
                    if(data.send_copy == 1) {
                        $('#editCopyEmailContainer').show();
                        $('#editCopyEmail').attr('required', true);
                    } else {
                        $('#editCopyEmailContainer').hide();
                        $('#editCopyEmail').attr('required', false);
                    }

                    // Jodit editörünü başlat
                    if (!joditEditEditor) {
                        joditEditEditor = new Jodit('#joditEditorEdit', editorConfig);
                    }

                    // Editör hazır olduğunda içeriği yükle
                    setTimeout(() => {
                        joditEditEditor.value = data.content;
                    }, 100);

                    // Modalı aç
                    $('#editTemplateModal').modal('show');
                },
                error: function(xhr, status, error) {
                    alert('Şablon verisi alınamadı: ' + error);
                }
            });
        });

        // "Düzenle" modal kapandığında
        $('#editTemplateModal').on('hidden.bs.modal', function () {
            if (joditEditEditor) {
                joditEditEditor.value = '';
            }
            // Formu sıfırla
            $('#editTemplateModal form')[0].reset();
        });

        // Form validasyon
        function validateForm(formElement) {
            const name = $(formElement).find('input[name="name"]').val();
            const code = $(formElement).find('select[name="code"]').val();
            const subject = $(formElement).find('input[name="subject"]').val();
            let content;

            if (formElement.id === 'editTemplateForm') {
                content = joditEditEditor ? joditEditEditor.value : '';
            } else {
                content = joditAddEditor ? joditAddEditor.value : '';
            }

            if (!name || !code || !subject || !content) {
                alert('Lütfen tüm alanları doldurunuz.');
                return false;
            }
            return true;
        }

        // Form submitleri
        $('form').on('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });

        // Test mail butonuna tıklandığında
        $(document).on('click', '.btn-warning', function(e) {
            e.preventDefault();
            var templateId = $(this).attr('href').split('/').pop();
            $('#template_id').val(templateId);
            
            // Şablon adını al
            $.ajax({
                url: SITE_URL + 'admin/mail/get_template_name/' + templateId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#preview_name').text(response.name);
                        $('#testMailModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: response.message,
                            confirmButtonText: 'Tamam'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Şablon bilgileri alınırken bir hata oluştu.',
                        confirmButtonText: 'Tamam'
                    });
                }
            });
        });

        // Test mail gönderme
        $('#sendTestMail').click(function() {
            var templateId = $('#template_id').val();
            var testEmail = $('#test_email').val();

            if (!testEmail) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Uyarı!',
                    text: 'Lütfen mail adresi giriniz.',
                    confirmButtonText: 'Tamam'
                });
                return;
            }

            $.ajax({
                url: SITE_URL + 'admin/mail/test_template/' + templateId,
                type: 'POST',
                data: {
                    test_email: testEmail
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#sendTestMail').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Gönderiliyor...');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#testMailModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Başarılı!',
                            text: response.message,
                            confirmButtonText: 'Tamam',
                            timer: 2000,
                            timerProgressBar: true
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Hata!',
                            text: response.message,
                            confirmButtonText: 'Tamam'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hata!',
                        text: 'Mail gönderilirken bir hata oluştu.',
                        confirmButtonText: 'Tamam'
                    });
                },
                complete: function() {
                    $('#sendTestMail').prop('disabled', false).html('Gönder');
                }
            });
        });

        // Modal kapandığında formu sıfırla
        $('#testMailModal').on('hidden.bs.modal', function() {
            $('#testMailForm')[0].reset();
        });

        // Şablona özel değişkenleri tanımla
        const templateVariables = {
            'welcome_mail': [],
            'mail_verification': [
                { code: '{verification_link}', desc: 'Doğrulama linki' }
            ],
            'password_reset': [
                { code: '{reset_link}', desc: 'Sıfırlama linki' }
            ],
            'guest_registration': [
                { code: '{password}', desc: 'Oluşturulan şifre' }
            ],
            'new_order': [
                { code: '{order_no}', desc: 'Sipariş numarası' },
                { code: '{total}', desc: 'Toplam tutar' },
                { code: '{currency}', desc: 'Para birimi' }
            ],
            'order_delivery': [
                { code: '{order_no}', desc: 'Sipariş numarası' },
                { code: '{product_name}', desc: 'Ürün adı' },
                { code: '{product_price}', desc: 'Ürün fiyatı' },
                { code: '{product_code}', desc: 'Ürün kodu' }
            ],
            'cancel_delivery': [
                { code: '{order_no}', desc: 'Sipariş numarası' },
                { code: '{product_name}', desc: 'Ürün adı' },
                { code: '{product_price}', desc: 'Ürün fiyatı' }
            ],
            'balance_success': [
                { code: '{amount}', desc: 'İşlem tutarı' },
                { code: '{transaction_id}', desc: 'İşlem numarası' },
                { code: '{current_balance}', desc: 'Güncel bakiye' }
            ],
            'subscription_start': [
                { code: '{subscription_name}', desc: 'Abonelik adı' },
                { code: '{start_date}', desc: 'Başlangıç tarihi' },
                { code: '{end_date}', desc: 'Bitiş tarihi' },
                { code: '{amount}', desc: 'Abonelik tutarı' }
            ],
            'ticket_reply': [
                { code: '{ticket_id}', desc: 'Destek talebi numarası' },
                { code: '{ticket_subject}', desc: 'Destek talebi konusu' },
                { code: '{ticket_status}', desc: 'Destek talebi durumu' },
                { code: '{reply_content}', desc: 'Yanıt içeriği' }
            ]
        };

        // Şablon değiştiğinde değişkenleri güncelle (hem yeni hem düzenleme modalı için)
        function updateTemplateVariables(templateCode, isEdit = false) {
            const specificVars = templateVariables[templateCode] || [];
            const specificVarsHtml = specificVars.map(v => `<code>${v.code}</code> - ${v.desc}<br>`).join('');
            
            const targetDiv = isEdit ? '#editTemplateSpecificVariables' : '#templateSpecificVariables';
            
            if (specificVars.length > 0) {
                $(targetDiv).html('<br><strong>Şablona Özel Değişkenler:</strong><br>' + specificVarsHtml);
            } else {
                $(targetDiv).empty();
            }
        }

        // Yeni şablon modalında şablon seçimi değiştiğinde
        $('#addTemplate select[name="code"]').on('change', function() {
            updateTemplateVariables($(this).val(), false);
        });

        // Düzenleme modalında şablon seçimi değiştiğinde
        $('#editTemplateModal select[name="code"]').on('change', function() {
            updateTemplateVariables($(this).val(), true);
        });

        // Düzenleme modalı açıldığında seçili şablonun değişkenlerini göster
        $('#editTemplateModal').on('shown.bs.modal', function() {
            const selectedTemplate = $('#editTemplateCode').val();
            if (selectedTemplate) {
                updateTemplateVariables(selectedTemplate, true);
            }
        });

        // Sayfa yüklendiğinde varsayılan şablon için değişkenleri göster
        const defaultTemplate = $('#addTemplate select[name="code"]').val();
        if (defaultTemplate) {
            updateTemplateVariables(defaultTemplate, false);
        }

        // Kopya gönderimi switch'i değiştiğinde
        $('#sendCopy').change(function() {
            if($(this).is(':checked')) {
                $('#copyEmailContainer').slideDown();
                $('#copyEmail').attr('required', true);
            } else {
                $('#copyEmailContainer').slideUp();
                $('#copyEmail').attr('required', false);
            }
        });

        // Düzenleme modalında kopya gönderimi switch'i değiştiğinde
        $('#editSendCopy').change(function() {
            if($(this).is(':checked')) {
                $('#editCopyEmailContainer').slideDown();
                $('#editCopyEmail').attr('required', true);
            } else {
                $('#editCopyEmailContainer').slideUp();
                $('#editCopyEmail').attr('required', false);
            }
        });
    });
</script>


