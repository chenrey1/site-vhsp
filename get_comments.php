<?php
// Yorumları çekmek için basit script
require_once('index.php'); // CodeIgniter'ı başlat

// CodeIgniter instance'ını al
$CI =& get_instance();
$CI->load->database();

// Tüm yorumları çek (kullanıcı ve ürün bilgileriyle birlikte)
$comments = $CI->db->select('pc.*, u.name as user_name, u.surname as user_surname, p.name as product_name')
    ->from('product_comments pc')
    ->join('user u', 'u.id = pc.user_id', 'left')
    ->join('product p', 'p.id = pc.product_id', 'left')
    ->order_by('pc.id', 'DESC')
    ->get()
    ->result();

echo "=== TÜM MÜŞTERİ YORUMLARI ===\n\n";
echo "Toplam Yorum Sayısı: " . count($comments) . "\n\n";
echo str_repeat("=", 80) . "\n\n";

foreach ($comments as $comment) {
    echo "Yorum ID: " . $comment->id . "\n";
    echo "Yorum: " . $comment->comment . "\n";
    echo "Kullanıcı: " . ($comment->user_name ? $comment->user_name . " " . $comment->user_surname : "Bilinmiyor") . "\n";
    echo "Ürün: " . ($comment->product_name ? $comment->product_name : "Ürün bulunamadı") . "\n";
    echo "Yıldız: " . $comment->star . "/5\n";
    echo "Tarih: " . $comment->date . "\n";
    echo "Durum: " . ($comment->isActive == 1 ? "Onaylı" : "Beklemede") . "\n";
    echo str_repeat("-", 80) . "\n\n";
}

// "Asil vandal" içeren yorumları özel olarak göster
echo "\n\n=== 'ASIL VANDAL' İÇEREN YORUMLAR ===\n\n";
$asil_comments = $CI->db->select('pc.*, u.name as user_name, u.surname as user_surname, p.name as product_name')
    ->from('product_comments pc')
    ->join('user u', 'u.id = pc.user_id', 'left')
    ->join('product p', 'p.id = pc.product_id', 'left')
    ->like('pc.comment', 'asil', 'both')
    ->or_like('pc.comment', 'vandal', 'both')
    ->order_by('pc.id', 'DESC')
    ->get()
    ->result();

foreach ($asil_comments as $comment) {
    echo "Yorum ID: " . $comment->id . "\n";
    echo "Yorum: " . $comment->comment . "\n";
    echo "Kullanıcı: " . ($comment->user_name ? $comment->user_name . " " . $comment->user_surname : "Bilinmiyor") . "\n";
    echo "Ürün: " . ($comment->product_name ? $comment->product_name : "Ürün bulunamadı") . "\n";
    echo "Yıldız: " . $comment->star . "/5\n";
    echo "Tarih: " . $comment->date . "\n";
    echo str_repeat("-", 80) . "\n\n";
}

