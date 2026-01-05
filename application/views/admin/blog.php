
<div id="layoutSidenav_content">

    <main>
        <div class="container-fluid">

            <div class="page-title">
                <h5 class="mb-0">Blog</h5>
            </div>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin'); ?>">Ana Sayfa</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Blog</li>
                </ol>
            </nav>

            <div class="blog-add-btn d-flex justify-content-between align-items-center mb-3">
                <div class="bad-1"></div>
                <div class="bad-2">
                    <a href="<?= base_url('admin/addBlog') ?>" class="btn btn-success"><i class="fa fa-plus"></i> Yazı Oluştur</a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered border dataTable table-blog">
                            <thead class="thead-light">
                                <tr>
                                    <th>Başlık</th>
                                    <th>İçerik</th>
                                    <th>Tarih</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blogs as $blog) { ?>
                                    <tr>
                                    <td><img src="<?= base_url('assets/img/blog/') . $blog->img ?>" alt=""> <?= $blog->title ?></td>
                                    <td><?= strip_tags(substr($blog->content, 0, 40)); ?>...</td>
                                    <td><?= $blog->date ?></td>
                                    <td>
                                        <a href="<?= base_url('admin/product/editBlog/') . $blog->id ?>" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                                        <a href="<?= base_url('admin/product/delete/blog/blog/') . $blog->id ?>" class="btn btn-outline-danger"><i class="fa fa-trash-alt"></i></a>
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
