<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Mail Logları</h5>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('admin'); ?>">Panel</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Mail Logları</li>
                </ol>
            </nav>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="logsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Durum</th>
                                    <th>Şablon</th>
                                    <th>Alıcı</th>
                                    <th>Mail Adresi</th>
                                    <th>Konu</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($logs as $log): ?>
                                    <tr>
                                        <td><?= $log->id ?></td>
                                        <td>
                                            <?php if($log->status == 'success'): ?>
                                                <span class="badge badge-success">Başarılı</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Başarısız</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $log->template_name ?></td>
                                        <td><?= ($log->user_name) ? $log->user_name . ' ' . $log->user_surname : '-' ?></td>
                                        <td><?= $log->to_email ?></td>
                                        <td><?= $log->subject ?></td>
                                        <td><?= date('d.m.Y H:i', strtotime($log->created_at)) ?></td>
                                        <td>
                                            <a href="javascript:void(0);" class="btn btn-sm btn-info view-mail" data-id="<?= $log->id ?>">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="<?= base_url('admin/mail/delete_log/'.$log->id) ?>" 
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

<!-- Mail Detay Modal -->
<div class="modal fade" id="mailDetailModal" tabindex="-1" role="dialog" aria-labelledby="mailDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mailDetailModalLabel">Mail Detayı</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Şablon:</strong></div>
                    <div class="col-md-9" id="templateName"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Alıcı:</strong></div>
                    <div class="col-md-9" id="recipientName"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Mail Adresi:</strong></div>
                    <div class="col-md-9" id="recipientEmail"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Konu:</strong></div>
                    <div class="col-md-9" id="mailSubject"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Durum:</strong></div>
                    <div class="col-md-9" id="mailStatus"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Gönderim Tarihi:</strong></div>
                    <div class="col-md-9" id="mailDate"></div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Mail İçeriği:</strong>
                        <div class="border p-3 mt-2" id="mailContent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#logsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.21/i18n/Turkish.json'
        },
        order: [[0, 'desc']],
        responsive: true,
        pageLength: 25
    });

    // Mail detay görüntüleme
    $('.view-mail').on('click', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '<?= base_url("admin/mail/get_log_details/") ?>' + id,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    var data = response.data;
                    $('#templateName').text(data.template_name);
                    $('#recipientName').text(data.user_name ? data.user_name + ' ' + data.user_surname : '-');
                    $('#recipientEmail').text(data.to_email);
                    $('#mailSubject').text(data.subject);
                    $('#mailStatus').html(data.status == 'success' ? 
                        '<span class="badge badge-success">Başarılı</span>' : 
                        '<span class="badge badge-danger">Başarısız</span>');
                    $('#mailDate').text(formatDate(data.created_at));
                    $('#mailContent').html(data.content);
                    $('#mailDetailModal').modal('show');
                } else {
                    alert('Mail detayı yüklenirken bir hata oluştu.');
                }
            },
            error: function() {
                alert('Mail detayı yüklenirken bir hata oluştu.');
            }
        });
    });

    // Tarih formatı için yardımcı fonksiyon
    function formatDate(dateString) {
        var date = new Date(dateString);
        return ('0' + date.getDate()).slice(-2) + '.' +
               ('0' + (date.getMonth() + 1)).slice(-2) + '.' +
               date.getFullYear() + ' ' +
               ('0' + date.getHours()).slice(-2) + ':' +
               ('0' + date.getMinutes()).slice(-2);
    }
});
</script>