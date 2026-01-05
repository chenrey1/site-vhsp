<div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">Referans Listesi</h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/referenceList">Mağaza</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Referans Listesi</li>
                            </ol>
                        </nav>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered border dataTable table-product">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Üye</th>
                                                <th>Şu Üyenin Referansıyla Kaydoldu</th>
                                                <th>Davet Ettiği Üye Sayısı</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 0; ?>
                                            <?php foreach ($user_references as $ref) { ?>
                                                <tr>
                                                    <td><?= $ref->name." ".$ref->surname."(".$ref->email.")" ?></td>
                                                    <td><?php
                                                        if ($ref->referrer) {
                                                           echo $ref->referrer->name." ".$ref->referrer->surname."(".$ref->referrer->email.")"; 
                                                        } else {
                                                            echo "-";
                                                        } ?>        
                                                    </td>
                                                    <td><?= count($ref->refs) ?></td>
                                                    <td><button class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modalReferences<?=$i?>"><i class="fas fa-eye"></i></button></td>
                                                </tr>
                                                <div class="modal fade" id="modalReferences<?=$i?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h6 class="modal-title">Referanslar</h6>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <?php foreach ($ref->refs as $r) {
                                                                    $regUser = $this->db->where('id', $r->buyer_id)->get('user')->row();
                                                                     echo $regUser->name." ".$regUser->surname."(".$regUser->email.") | " . $regUser->date; 
                                                                     echo "<br>";
                                                                } ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php $i++; } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </main>
