
            <div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid">

                        <div class="page-title">
                            <h5 class="mb-0">Yazı Düzenle <small><?= $blog->title ?></small></h5>
                        </div>

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="<?= base_url(); ?>admin/blog">Blog</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Yazı Düzenle</li>
                                <li class="breadcrumb-item active" aria-current="page"><?= $blog->title ?></li>
                            </ol>
                        </nav>

                        <div class="card">
                            <div class="card-body">
                                <form action="<?= base_url('admin/product/edit/blog/blog/' . $blog->id . '/blog') ?>" method="POST" enctype="multipart/form-data">
                                    <div class="form-group row">
                                        <label for="inputPName" class="col-sm-2 col-form-label">Başlık:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="title" id="title" value="<?= $blog->title ?>" onchange="doSlug()" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputPName" class="col-sm-2 col-form-label">Yazı Linki:</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" name="slug" id="slug" value="<?= $blog->slug ?>" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputPImg" class="col-sm-2 col-form-label">Başlık Görseli:</label>
                                        <div class="col-sm-10">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="customFileLangHTML" name="img">
                                                <label class="custom-file-label" for="customFileLangHTML" data-browse="Seç">Başlık Görselini Seçmek İçin Tıkla</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputPText" class="col-sm-2 col-form-label">Yazı: <small>(HTML)</small></label>
                                        <div class="col-sm-10">
                                            <textarea id="editor" rows="10" class="form-control" name="content"><?= $blog->content ?></textarea>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary float-right"><i class="fa fa-plus"></i> Gönder</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </main>

                <script>
                    ClassicEditor
                            .create( document.querySelector( '#editor' ) )
                            .catch( error => {
                                console.error( error );
                            } );
                </script>

<script>
    function doSlug()
    {
        var title = $("#title").val();
        title = str(title);
        $('#slug').val(title);
    }

    function str(str) {
        str = str.replace(/^\s+|\s+$/g, ''); // trim
        str = str.toLowerCase();

        // remove accents, swap ñ for n, etc
        var from = "ãàáäâẽèéëêìíïîıõòóöôùúüûñç·/_,:;şğ";
        var to   = "aaaaaeeeeeiiiiiooooouuuunc------sg";
        for (var i=0, l=from.length ; i<l ; i++) {
            str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                .replace(/\s+/g, '-') // collapse whitespace and replace by -
                .replace(/-+/g, '-'); // collapse dashes

        return str;
    };
</script>