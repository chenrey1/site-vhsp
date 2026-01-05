<!DOCTYPE html>
<html lang="tr">
<head>
    <title><?= $SITE_NAME ?> API Dokümantasyonu</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        /* RapiDocs Dark Tema CSS */

        :root {
            --bg-color: #121212;
            --text-color: #f2f2f2;
            --border-color: #221f1f;
            --header-bg: #181818;
            --header-text-color: #f2f2f2;
            --sidebar-bg: #181818;
            --sidebar-text-color: #f2f2f2;
            --link-color: #0089FF;
            --primary-color: #0089FF;
            --primary-text-color: #ffffff;
            --secondary-bg: #1f1f1f;
            --method-get-color: #19DB91;
            --method-post-color: #0089FF;
            --method-put-color: #FFA500;
            --method-delete-color: #dc3545;
            --code-bg: #262626;
        }

        /* Genel Stiller */
        rapi-doc {
            font-family: "Euclid Circular A", sans-serif;
            /*background-color: var(--bg-color);*/
            color: var(--text-color);
        }

        /* Başlık Stili */
        rapi-doc::part(section-header) {
            background-color: var(--header-bg);
            color: var(--header-text-color);
            border-bottom: 1px solid var(--border-color);
            padding: 20px;
        }

        /* Kenar Çubuğu Stili */
        rapi-doc::part(section-sidebar) {
            background-color: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
        }

        rapi-doc::part(section-sidebar-item) {
            color: var(--sidebar-text-color);
            font-size: 15px;
            padding: 10px 15px;
        }

        rapi-doc::part(section-sidebar-item):hover {
            background-color: #262626;
        }

        /* API Endpoint Stili */
        rapi-doc::part(section-endpoint) {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 20px;
            background-color: var(--header-bg);
        }

        rapi-doc::part(section-endpoint-head) {
            background-color: var(--secondary-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 15px;
        }

        /* HTTP Metod Renkleri */
        rapi-doc::part(method-get) { color: var(--method-get-color); }
        rapi-doc::part(method-post) { color: var(--method-post-color); }
        rapi-doc::part(method-put) { color: var(--method-put-color); }
        rapi-doc::part(method-delete) { color: var(--method-delete-color); }

        /* Buton Stili */
        rapi-doc::part(button) {
            background-color: var(--primary-color);
            color: var(--primary-text-color);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-size: 15px;
            cursor: pointer;
            transition: 0.2s;
        }

        rapi-doc::part(button):hover {
            opacity: 0.9;
        }

        /* Form Elemanları */
        rapi-doc::part(input) {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 15px;
            color: var(--text-color);
            background-color: var(--secondary-bg);
        }

        rapi-doc::part(textarea) {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 15px;
            color: var(--text-color);
            background-color: var(--secondary-bg);
        }

        /* Kod Blokları */
        rapi-doc::part(code) {
            background-color: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 2px 5px;
            font-family: monospace;
        }

        /* Tablo Stili */
        rapi-doc::part(table) {
            border-collapse: collapse;
            width: 100%;
        }

        rapi-doc::part(tr) {
            border-bottom: 1px solid var(--border-color);
        }

        rapi-doc::part(th) {
            background-color: var(--secondary-bg);
            padding: 10px;
            text-align: left;
            font-weight: 500;
        }

        rapi-doc::part(td) {
            padding: 10px;
        }

        /* Scroll Bar */
        rapi-doc::-webkit-scrollbar {
            width: 8px;
        }

        rapi-doc::-webkit-scrollbar-track {
            background: var(--secondary-bg);
        }

        rapi-doc::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 4px;
        }

        rapi-doc::-webkit-scrollbar-thumb:hover {
            background: #0069c3;
        }

        /* Ek Dark Tema Ayarları */
        rapi-doc::part(section-main-content) {
            /*background-color: var(--bg-color);*/
        }

        rapi-doc::part(section-navbar) {
            background-color: var(--header-bg);
            border-bottom: 1px solid var(--border-color);
        }

        rapi-doc::part(section-overview) {
            background-color: var(--header-bg);
            color: var(--text-color);
        }

        rapi-doc::part(section-auth) {
            color: var(--text-color);
            border-top: 1px solid var(--primary-color);
            margin-top: 0;
        }

        rapi-doc::part(section-models) {
            background-color: var(--header-bg);
            color: var(--text-color);
        }

        rapi-doc::part(label) {
            color: var(--text-color);
        }

        rapi-doc::part(anchor) {
            color: var(--link-color);
        }

        rapi-doc::part(anchor):hover {
            text-decoration: underline;
        }

        rapi-doc::part(pre) {
            background-color: var(--code-bg);
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 2px 5px;
            font-family: monospace;
        }
    </style>
    <script type="module" src="https://unpkg.com/rapidoc/dist/rapidoc-min.js"></script>
</head>
<body>
<rapi-doc
    spec-url="<?= base_url("api/docs/openapi") ?>"
    theme="dark"
    show-header="false"
    render-style="read"
    allow-authentication="true"
    allow-server-selection="false"
    heading-text="<?= $SITE_NAME ?> API Dokümantasyonu"
    primary-color="#0089FF"
    text-color="#f2f2f2"
    nav-bg-color="#181818"
    nav-text-color="#f2f2f2"
    nav-hover-bg-color="#262626"
    nav-hover-text-color="#ffffff"
    nav-accent-color="#0089FF"
    lang="tr"
>
    <img
        slot="nav-logo"
        src="<?= $logo_url ?>"
    />
    <div slot="overview">
        <h2><?= $SITE_NAME ?> API'ye Hoş Geldiniz</h2>
        
        <h3>API Hakkında</h3>
        <p>
            Bu API, <?= $SITE_NAME ?> platformunda aşağıdaki işlemleri gerçekleştirmenizi sağlar:
        </p>
        <ul>
            <li>Kullanıcı bakiyesi sorgulama</li>
            <li>Ürün listeleme ve detay görüntüleme</li>
            <li>Kategori listeleme ve detay görüntüleme</li>
            <li>Ürün satın alma</li>
        </ul>

        <h3>Kimlik Doğrulama</h3>
        <p>
            Tüm API çağrıları için Basic Authentication kullanılmaktadır. Her istek için
            Authorization header'ında e-posta adresinizi ve şifrenizi base64 ile kodlanmış
            olarak göndermeniz gerekmektedir.
        </p>
        <pre><code>Authorization: Basic base64(email:password)</code></pre>

        <h3>Örnek PHP Kullanımı</h3>
        <pre><code>
$email = 'kullanici@email.com';
$password = 'sifre';
$credentials = base64_encode($email . ':' . $password);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "<?= base_url('api/v1') ?>/<ENDPOINT>");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Basic ' . $credentials
));
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);
        </code></pre>

        <h3>Hata Kodları</h3>
        <table>
            <tr>
                <th>Kod</th>
                <th>Açıklama</th>
            </tr>
            <tr>
                <td>400</td>
                <td>Geçersiz istek (Eksik veya hatalı parametre)</td>
            </tr>
            <tr>
                <td>401</td>
                <td>Kimlik doğrulama bilgileri eksik veya geçersiz</td>
            </tr>
            <tr>
                <td>403</td>
                <td>IP adresi izin verilmeyen listede</td>
            </tr>
            <tr>
                <td>404</td>
                <td>İstenen kaynak bulunamadı</td>
            </tr>
            <tr>
                <td>500</td>
                <td>Sunucu hatası</td>
            </tr>
        </table>

        <h3>IP Kısıtlaması</h3>
        <p>
            Güvenlik nedeniyle, API'ye erişim sadece önceden tanımlanmış IP adreslerinden yapılabilir. 
            IP adresinizi hesabınıza giriş yaptıktan sonra API Ayarları sayfasından ekleyebilirsiniz.
        </p>

        <h3>Yanıt Formatı</h3>
        <p>
            Tüm API yanıtları JSON formatında döner ve aşağıdaki genel yapıya sahiptir:
        </p>
        <pre><code>{
    "status": true|false,
    "data|message": "...",
    ... diğer alanlar ...
}</code></pre>

        <h3>Callback URL Kullanımı</h3>
        <p>
            Satın alma işlemlerinde, siparişin durumu tamamlandığında bilgilendirilmek için callback URL kullanabilirsiniz.
            Sipariş durumu "completed" olduğunda, sistemimiz belirttiğiniz callback URL'ine bir POST isteği gönderir.
        </p>

        <h4>Callback İsteği Örneği</h4>
        <pre><code>
// Callback URL'ine gönderilen POST verisi
{
    "order_id": "ORD123456",
    "status": "completed",
    "delivery_details": "Ürün detayları...",
    "timestamp": "2024-01-01 12:00:00"
}
        </code></pre>

        <h4>Callback Kullanım Örneği (PHP)</h4>
        <pre><code>
// Satın alma isteği
$email = 'kullanici@email.com';
$password = 'sifre';
$credentials = base64_encode($email . ':' . $password);

$data = array(
    'product_id' => 1,
    'quantity' => 1,
    'callback_url' => 'https://sizin-siteniz.com/callback',
    'required_fields' => array(
        'username' => 'oyuncu123',
        'server' => 'EU'
    )
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "<?= base_url('api/v1') ?>/purchase");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Basic ' . $credentials,
    'Content-Type: application/json'
));
$response = curl_exec($ch);
curl_close($ch);

// Callback'i karşılayan endpoint örneği
// https://sizin-siteniz.com/callback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $callback_data = json_decode(file_get_contents('php://input'), true);
    
    if ($callback_data['status'] === 'completed') {
        // Sipariş tamamlandı, gerekli işlemleri yapın
        $order_id = $callback_data['order_id'];
        $delivery_details = $callback_data['delivery_details'];
        
        // Veritabanına kaydet, kullanıcıyı bilgilendir vs.
    }
}
        </code></pre>

        <h4>Önemli Notlar</h4>
        <ul>
            <li>Callback URL'iniz HTTPS protokolünü desteklemelidir</li>
            <li>Callback isteği yalnızca sipariş "completed" durumuna geçtiğinde bir kez gönderilir</li>
            <li>Callback URL opsiyoneldir, belirtilmezse sadece API yanıtı ile sipariş durumunu öğrenebilirsiniz</li>
        </ul>

        <h3>Yardım ve Destek</h3>
        <p>
            Herhangi bir sorunuz veya sorununuz varsa, lütfen <a href="mailto:<?= $SUPPORT_MAIL ?>"><?= $SUPPORT_MAIL ?></a> adresine e-posta gönderin.
        </p>
    </div>
</rapi-doc>
</body>
</html>