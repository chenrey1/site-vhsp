<?php
function prepare_mail_content($template, $data) {
    // CI instance'ını al
    $CI =& get_instance();

    // Properties tablosundan site bilgilerini al
    $site_info = $CI->db->where('id', 1)->get('properties')->row();

    // Varsayılan değerleri tanımla
    $defaults = [
        'year' => date('Y'),
        'site_name' => $site_info->name ?? 'Site Adı',
        'copyright' => $site_info->name ?? 'Site Adı',
        'site_url' => base_url() ?? '',
        'site_email' => $site_info->email ?? '',
        'site_phone' => $site_info->phone ?? '',
    ];

    // Gelen veriyi varsayılan değerlerle birleştir
    // Gelen veri öncelikli olacak şekilde birleştirme yapılıyor
    $data = array_merge($defaults, $data);

    // Değişken değiştirme için dizileri hazırla
    $search = [];
    $replace = [];

    foreach ($data as $key => $value) {
        $search[] = '{' . $key . '}';
        $replace[] = $value;
    }

    // Değişkenleri değiştir ve sonucu döndür
    return str_replace($search, $replace, $template);
}

function sendDeliveryNotification($email, $data) {
    $CI =& get_instance();
    $CI->load->library('mailer');

    // Mail verilerini hazırla
    $orderData = [
        'name' => $data['name'],
        'surname' => $data['surname'],
        'email' => $data['email'],
        'order_id' => $data['order_id'],
        'product_name' => $data['product_name'],
        'product_price' => number_format($data['product_price'], 2),
        'product_code' => $data['product_code'],
        'date' => date('d.m.Y H:i')
    ];

    return $CI->mailer->send(
        $email,
        'order_delivery',
        $orderData,
        2
    );
}

function sendCancelNotification($email, $data) {
    $CI =& get_instance();
    $CI->load->library('mailer');

    $mailData = [
        'name' => $data['name'],
        'surname' => $data['surname'],
        'email' => $data['email'],
        'order_id' => $data['order_id'],
        'product_name' => $data['product_name'],
        'product_price' => number_format($data['product_price'], 2),
        'date' => date('d.m.Y H:i')
    ];

    return $CI->mailer->send(
        $email,
        'cancel_delivery',
        $mailData,
        1
    );
}

function sendWelcomeMail($email, $data) {
    $CI =& get_instance();
    $CI->load->library('mailer');

    // Mail verilerini hazırla
    $mailData = [
        'name' => $data['name'],
        'surname' => $data['surname'],
        'email' => $data['email'],
        'company_name' => isset($data['company_name']) ? $data['company_name'] : null,
        'company_logo' => isset($data['company_logo']) ? $data['company_logo'] : null,
        'company_url' => isset($data['company_url']) ? $data['company_url'] : base_url(),
        'support_email' => isset($data['support_email']) ? $data['support_email'] : null
    ];

    // Hoşgeldin mailini gönder (yüksek öncelikli)
    return $CI->mailer->send(
        $email,
        'welcome_mail',
        $mailData,
        2 // Yüksek öncelik
    );
}