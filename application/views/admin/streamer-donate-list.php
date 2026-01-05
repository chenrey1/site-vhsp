<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Bağışlar</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Bağışlar</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered border dataTable table-product">
                            <thead class="thead-light">
                            <tr>
                                <th>Yayıncı Adı</th>
                                <th>Bağışçı Adı</th>
                                <th>Görünen Bağışçı</th>
								<th>Mesaj</th>
								<th>Miktar</th>
								<th>Ekranda Gizli</th>
								<th>Tarih</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($donates as $donate) {
                                $streamer = $this->db->where('id', $donate->streamer)->get('user')->row();
								$streamer->streamer_info = json_decode($streamer->streamer_info, false);
								$donor = $this->db->where('id', $donate->user)->get('user')->row();
                                ?>
                                <tr>
                                    <td>
										<a href="<?= base_url('admin/users').'?edit_user='.$streamer->id ?>">
											<img src="<?= $streamer->streamer_info->streamlabs->thumbnail ?>" alt=""> <?= mb_strtoupper($streamer->streamer_title) ?>
										</a>
									</td>
									<td>
										<a href="<?= base_url('admin/users').'?edit_user='.$donor->id ?>">
											<?= mb_strtoupper($donor->name." ".$donor->surname) ?>
										</a>
									</td>
									<td>
										<?= $donate->donor ?>
									</td>
									<td>
										<?= $donate->message ?>
									</td>
									<td>
										<?= $donate->amount ?>
									</td>
									<td><?= ($donate->hide == 1) ? "Evet" : "Hayır" ?></td>
									<td>
										<?= $donate->date ?>
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
