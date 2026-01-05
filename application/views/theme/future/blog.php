<section class="fp-section-blog-page">
    <div class="container">

        <div class="fp-breadcrumb">
            <ul class="list-inline list-unstyled mb-0 list text-center">
                <li><a href="<?= base_url('makale-listesi') ?>" class="link">Blog</a></li>
                <li><a href="#" class="link active"><?= $blog->title ?></a></li>
            </ul>
        </div>

        <h1 class="blog-title"><?= $blog->title ?></h1>

        <div class="info-list">
            <div class="text"><i class="ri-calendar-2-line"></i> <?= $blog->date ?></div>
            <div class="text"><i class="ri-edit-line"></i> Yayınlayan: Yönetici</div>
        </div>

        <div class="img-cover">
            <img src="<?php echo base_url('assets/img/blog/') . $blog->img ?>" alt="<?= $blog->title ?>" class="img-blog">
        </div>

        <div class="fp-blog-content">
            <?= $blog->content ?>
        </div>

    </div>
</section>

<style>
    .fp-blogs-section {
        margin-top: 0 !important;
    }
</style>