<section class="fp-section-page">
    <div class="container">

        <div class="fp-section-page-head">
            <h1 class="title mb-0">Blog</h1>
        </div>


        <div class="row">

            <?php foreach ($blogs as $blog) { ?>
                <div class="col-lg-4">
                    <div class="fp-blog-card fp-card">
                        <a href="<?= base_url('makale/') . $blog->slug; ?>"><div class="img"><img src="<?= base_url('assets/img/blog/') . $blog->img ?>" alt="<?= $blog->title ?>" class="img-aspect"></div></a>
                        <div class="content">
                            <a class="title" href="<?= base_url('makale/') . $blog->slug; ?>"><?= strip_tags($blog->title) ?></a>
                            <p><?= strip_tags(substr($blog->content, 0, 200)); ?></p>
                            <div class="flex">
                                <div class="date"><i class="ri-calendar-2-line"></i> <?= $blog->date ?></div>
                                <a href="<?= base_url('makale/') . $blog->slug; ?>" class="link">Devamını Oku</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>


    </div>
</section>