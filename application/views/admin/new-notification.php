<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Stok Ekle</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/Notification/notificationList'); ?>">Bildirim Yönetimi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Yeni Bildirim Oluştur</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-lg-8">
                            <form action="<?= base_url('admin/Notification/createNotification') ?>" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="nameofnt">Bildirim Adı <small class="text-muted">Bu alan sadece bildirim takibi içindir. Kullanıcılar göremez.</small></label>
                                    <input type="text" class="form-control" id="nameofnt" value="<?=date("dmY") . "BILDIRIM" ?>" name="notification_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="titleofnt">Bildirim Başlığı</label>
                                    <input type="text" class="form-control" id="titleofnt" name="notification_title" required>
                                </div>
                                <div class="form-group">
                                    <label for="contensofnt">Bildirim İçeriği</label>
                                    <input type="text" class="form-control" id="contensofnt" name="notification_contents" required>
                                </div>
                                <div class="form-group">
                                    <label for="linkofnt">Yönlendirme Linki</label>
                                    <input type="text" class="form-control" id="linkofnt" name="notification_link" required>
                                </div>
                                <!--<div class="form-group">
                                    <label for="start_at">Başlangıç Tarihi <small class="text-muted">Bildirimini şu anda gönderebilir ya da daha sonraya planlayabilirsin.</small></label>
                                    <input type="datetime-local" class="form-control" id="start_at" name="start_at" required>
                                </div>-->
                                <div class="form-group">
                                    <label for="end_up">Bitiş Tarihi <small class="text-muted">Bildirimin ne zaman sonlanacağını seçmelisin.</small></label>
                                    <input type="datetime-local" class="form-control" id="end_up" name="end_up" required>
                                </div>
                                <div class="form-group">
                                    <label for="target_group">Hedeflenen Grup</label>
                                    <select class="custom-select" id="target_group" name="target_group" required>
                                        <option selected>Herkese Gönder</option>
                                    </select>
                                    <small class="text-muted">Bu bildirim şu anda varsayılan olarak herkese gönderilecek. Ancak ilerleyen güncellemelerde eğer istek olursa son satın alanlar, son kayıt olanlar ya da hiç ürün almayanlar gibi seçimleri yapabilirsin.</small>
                                </div>
                                <div class="form-group">
                                    <label>Bildirim Görseli</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="customFileLangHTML" name="img">
                                        <label class="custom-file-label" for="customFileLangHTML" data-browse="Seç">Bir görsel seç</label>
                                    </div>
                                </div>
                                <div><button type="submit" class="btn btn-primary"><i class="far fa-paper-plane"></i> Bildirim Gönder</button></div>
                            </form>
                        </div>
                        <div class="col-12 col-lg-4 border-left">
                            <div class="form-group">
                                <label>Bildirimde Gözükecek Görsel</label>
                                <p><img style="max-width: 220px;" id="img_span" src="" alt=""></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Bildirim Takip Adı</label>
                                <p class="font-weight-bold" id="notification_name_span"><?=date("dmY") . "BILDIRIM" ?></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Bildirim Başlığı</label>
                                <p class="font-weight-bold" id="notification_title_span"></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Bildirim İçeriği</label>
                                <p class="font-weight-bold" id="notification_contents_span"></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Bildirim Yönlendirme Linki</label>
                                <p class="font-weight-bold" id="notification_link_span"></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Bildirim Başlangıç Tarihi</label>
                                <p class="font-weight-bold" id="start_at_span"><?=date('d-m-Y H:i:s')?></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Bildirim Bitiş Tarihi</label>
                                <p class="font-weight-bold" id="end_up_span"></p>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Hedeflenen Grup</label>
                                <p class="font-weight-bold" id="target_group_span">Herkese Gönder</p>
                            </div>
                            <hr>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Form elemanlarını seç
        var end_up_input = document.getElementById('end_up');
        var notification_name_input = document.getElementById('nameofnt');
        var notification_title_input = document.getElementById('titleofnt');
        var notification_contents_input = document.getElementById('contensofnt');
        var notification_link_input = document.getElementById('linkofnt');
        var target_group_input = document.getElementById('target_group');
        var img_input = document.getElementById('customFileLangHTML');

        // Span alanlarını seç
        var end_up_span = document.getElementById('end_up_span');
        var notification_name_span = document.getElementById('notification_name_span');
        var notification_title_span = document.getElementById('notification_title_span');
        var notification_contents_span = document.getElementById('notification_contents_span');
        var notification_link_span = document.getElementById('notification_link_span');
        var target_group_span = document.getElementById('target_group_span');
        var img_span = document.getElementById('img_span');

        // Değişiklikleri dinle ve span alanlarına yaz
        end_up_input.addEventListener('input', function() {
            end_up_span.textContent = end_up_input.value;
        });

        notification_name_input.addEventListener('input', function() {
            notification_name_span.textContent = notification_name_input.value;
        });

        notification_title_input.addEventListener('input', function() {
            notification_title_span.textContent = notification_title_input.value;
        });

        notification_contents_input.addEventListener('input', function() {
            notification_contents_span.textContent = notification_contents_input.value;
        });

        notification_link_input.addEventListener('input', function() {
            notification_link_span.textContent = notification_link_input.value;
        });

        target_group_input.addEventListener('change', function() {
            target_group_span.textContent = target_group_input.options[target_group_input.selectedIndex].text;
        });

        img_input.addEventListener('change', function() {
            if (img_input.files && img_input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    img_span.src = e.target.result;
                }
                reader.readAsDataURL(img_input.files[0]);
            }
            else {
                img_span.src = "";
            }
            img_span.textContent = img_input.files[0].name;
        });
    </script>
