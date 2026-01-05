<?php
    $categories = $this->db->where('isActive', 1)->get('category')->result(); // Aktif Kategoriler
    $products = $this->db->where('isActive', 1)->get('product')->result(); // Aktif Ürünler
    $blogs = $this->db->get('blog')->result();
    $pages = $this->db->get('pages')->result();
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc><?php echo base_url(); ?></loc>
        <priority>1.0</priority>
        <changefreq>always</changefreq>
    </url>
<?php foreach ($blogs as $b) { ?>
    <url>
        <loc><?= base_url('makale/' . $b->slug) ?></loc>
        <priority>1</priority>
        <changefreq>daily</changefreq>
    </url>
<?php } ?>
<?php foreach ($products as $p) { ?>
    <url>
        <loc><?= base_url($p->slug) ?></loc>
        <priority>0.85</priority>
        <changefreq>daily</changefreq>
    </url>
<?php } ?>
<?php foreach ($categories as $c) { ?>
    <url>
        <loc><?= base_url('kategori/' . $c->slug) ?></loc>
        <priority>0.70</priority>
        <changefreq>daily</changefreq>
    </url>
<?php } ?>
</urlset>