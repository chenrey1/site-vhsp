<?php if ($result) {
	foreach ($result as $r) { ?>
    <a class="search-item" href="<?= base_url($r->slug); ?>">
        <div class="img"><img src="<?= base_url('assets/img/product/') . $r->img ?>" alt="" class="img-product"></div>
        <div class="content">
            <div class="product-name"><?= $r->name ?></div>
            <div class="price"><?= $r->price ?> TL</div>
        </div>
        <i class="ri-arrow-right-line icon"></i>
    </a>
<?php }

}else{
	echo "Sonuç Bulunamadı.";
} ?>