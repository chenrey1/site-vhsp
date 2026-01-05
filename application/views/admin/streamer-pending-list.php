<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Yayıncılar</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Onay Bekleyen Yayıncılar</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered border dataTable table-product">
                            <thead class="thead-light">
                            <tr>
                                <th>Yayıncı Adı</th>
                                <th>Kullanıcı</th>
                                <th>Site Bağlantısı</th>
                                <th>Yayın Linki</th>
                                <th>Minimum Bağış</th>
                                <th>Sosyal Medya Hesapları</th>
                                <th width="5%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($users as $streamer) {
                                $streamer->streamer_info = json_decode($streamer->streamer_info, false);
                                ?>
                                <tr>
                                    <td><img src="<?= $streamer->streamer_info->streamlabs->thumbnail ?>" alt=""> <?= mb_strtoupper($streamer->streamer_title) ?></td>
                                    <td><a href="<?= base_url("admin/users?edit_user=".$streamer->id) ?>"><?= $streamer->name." ".$streamer->surname ?></a></td>
                                    <td><?= $url_without_https.$streamer->streamer_slug ?></td>
                                    <td><?= $streamer->streamer_stream_url ?></td>
                                    <td><?= $streamer->streamer_min_donate ?>₺</td>
                                    <td>
                                        <a href="#socialAccounts" data-toggle="modal" onclick="initSocialModal(this)" data-social-info="<?= htmlspecialchars($streamer->streamer_social, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary"><i class="fas fa-eye"></i> Göster</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="<?= base_url('admin/product/changeStreamer/1/') . $streamer->id ?>" class="text-success">Onayla</a>
                                        <a href="<?= base_url('admin/product/changeStreamer/3/') . $streamer->id ?>" class="text-danger">Reddet</a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <div class="modal fade" id="socialAccounts" tabindex="-1" role="dialog" aria-labelledby="userEdit" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Sosyal Medya Hesapları</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="buttons"></div>

                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-dark">Kapat</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


<script>
    function initSocialModal(element) {
        var social_info = $(element).data('social-info');

        $('#socialAccounts .modal-body #buttons').html('');
        //foreach social_info key value
        $.each(social_info, function (key, value) {
            //if value is not empty
            if (value != "") {
                //add button
                $('#socialAccounts .modal-body #buttons').append('<a href="button" class="btn btn-secondary btn-block"><i class="fab fa-'+key+'"></i> ' + value + '</a>');
            }
        });
    }
</script>