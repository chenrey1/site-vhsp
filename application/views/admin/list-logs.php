<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Kayıt Geçmişi</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kayıt Geçmişi</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="logsTable" class="table table-hover table-bordered" width="100%">
                            <thead class="thead-light">
                                <tr>
                                    <th width="10%">Log ID</th>
                                    <th width="25%">Kullanıcı</th>
                                    <th width="15%">IP</th>
                                    <th width="20%">Tarih</th>
                                    <th width="30%">Kayıt</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables tarafından doldurulacak -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script type="text/javascript">
        $(document).ready(function(){
            speedy_init();

            create_datatable($("#logsTable"), "<?= base_url("admin/API/getLogs") ?>", [[ 0, "desc" ]], [
                { data: "id" },
                { data: "user_id" },
                { data: "user_ip" },
                { data: "date" },
                { data: "event", className: "text-center"},
            ]);
        });
    </script>
