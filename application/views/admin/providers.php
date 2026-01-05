<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid">
            <div class="page-title">
                <h5 class="mb-0">Tedarikçi Yönetimi</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Panel</a></li>
                    <li class="breadcrumb-item active">Tedarikçiler</li>
                </ol>
            </nav>

            <div class="card mb-3">
                <div class="card-header">
                    <button class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#addProviderModal">
                        <i class="fa fa-plus"></i> Yeni Tedarikçi Ekle
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tedarikçi Adı</th>
                                    <th>API Tipi</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($providers as $provider): ?>
                                <tr>
                                    <td><?= $provider->name ?></td>
                                    <td><?= $provider->type ?></td>
                                    <td>
                                        <?php if($provider->is_active): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Pasif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" onclick="editProvider(<?= $provider->id ?>)">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteProvider(<?= $provider->id ?>)">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tedarikçi Ekleme Modal -->
        <div class="modal fade" id="addProviderModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Yeni Tedarikçi Ekle</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="providerForm" action="<?= base_url('admin/product/addProvider') ?>" method="POST">
                            <div class="form-group">
                                <label>Tedarikçi Adı</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="form-group">
                                <label>API Tipi</label>
                                <select class="form-control" name="type" id="apiType" required>
                                    <option value="">Seçiniz</option>
                                    <option value="hyper">Hyper</option>
                                    <option value="orius">Orius</option>
                                </select>
                            </div>
                            <div id="hyperFields" style="display:none;">
                                <div class="form-group">
                                    <label>API Anahtarı</label>
                                    <input type="text" class="form-control" name="api_key">
                                </div>
                                <div class="form-group">
                                    <label>API Token</label>
                                    <input type="text" class="form-control" name="api_token">
                                </div>
                            </div>
                            <div id="oriusFields" style="display:none;">
                                <div class="form-group">
                                    <label>E-posta</label>
                                    <input type="email" class="form-control" name="api_email">
                                </div>
                                <div class="form-group">
                                    <label>Şifre</label>
                                    <input type="password" class="form-control" name="api_password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>API URL</label>
                                <input type="text" class="form-control" name="base_url">
                            </div>
                            <button type="submit" class="btn btn-primary">Kaydet</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

<script>
$('[data-target="#addProviderModal"]').on('click', function() {
    $('#providerForm')[0].reset();
    $("#providerForm").attr('action', '<?= base_url("admin/product/addProvider") ?>');
    $('#apiType').val('').trigger('change');
    $('#hyperFields').hide();
    $('#oriusFields').hide();
});
$('#apiType').change(function() {
    if($(this).val() == 'hyper') {
        $('#hyperFields').show();
        $('#oriusFields').hide();
    } else if($(this).val() == 'orius') {
        $('#hyperFields').hide();
        $('#oriusFields').show();
    } else {
        $('#hyperFields').hide();
        $('#oriusFields').hide();
    }
});

function editProvider(id) {
    $.get('<?= base_url("admin/product/getProvider/") ?>' + id, function(data) {
        var provider = JSON.parse(data);
        var apiDetails = JSON.parse(provider.api_details);
        
        $("#providerForm")[0].reset();
        $("#providerForm").attr('action', '<?= base_url("admin/product/updateProvider/") ?>' + id);
        $("input[name='name']").val(provider.name);
        $("#apiType").val(provider.type).trigger('change');
        
        if(provider.type == 'hyper') {
            $("input[name='api_key']").val(apiDetails.api_key);
            $("input[name='api_token']").val(apiDetails.api_token);
        } else if(provider.type == 'orius') {
            $("input[name='api_email']").val(apiDetails.api_email);
            $("input[name='api_password']").val(apiDetails.api_password);
        }
        
        $("input[name='base_url']").val(provider.base_url);
        $("#addProviderModal").modal('show');
    });
}

function deleteProvider(id) {
    if(confirm('Tedarikçiyi silmek istediğinize emin misiniz?')) {
        window.location.href = '<?= base_url("admin/product/deleteProvider/") ?>' + id;
    }
}
</script>
