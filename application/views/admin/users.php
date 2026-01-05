<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Üyeler</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Üyeler</li>
                </ol>
            </nav>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover border userTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>Durum</th>
                                    <th>Ad</th>
                                    <th>Soyad</th>
                                    <th>E-Posta</th>
                                    <th>Telefon</th>
                                    <th>TC NO</th>
                                    <th>Kayıt Tarihi</th>
                                    <th>Bakiye</th>
                                    <th>İndirim Miktarı</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
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
        create_datatable($(".userTable"), "<?= base_url("admin/API/getUsers") ?>", [[ 6, "desc" ]], [
            { data: "type" },
            { data: "name" },
            { data: "surname" },
            { data: "email" },
            { data: "phone" },
            { data: "tc" },
            { data: "date" },
            { data: "balance" },
            { data: "discount" },
            { data: "extra_row1" },
            { data: "extra_row3" }
        ]).ajax.reload(null, false);
    });
</script>
