<?php
/**
 * cPanel Otomatik Deployment Script
 * 
 * Bu script, Git repository'den gelen değişiklikleri otomatik olarak çeker.
 * Güvenlik için bir secret token kullanır.
 * 
 * Kullanım:
 * 1. Bu dosyayı public_html klasörüne yükleyin
 * 2. GitHub/GitLab webhook URL'ine ekleyin: https://yourdomain.com/deploy.php?token=YOUR_SECRET_TOKEN
 * 3. Secret token'ı aşağıdaki $secret_token değişkenine ekleyin
 */

// Güvenlik: Secret token belirleyin (en az 32 karakter, rastgele)
// ÖNEMLİ: Bu token'ı mutlaka değiştirin! Güçlü bir token oluşturun.
// Örnek güçlü token oluşturma: php -r "echo bin2hex(random_bytes(32));"
   $secret_token = 'valohesap_deploy_token_2024_xyz789';

// Git repository path (cPanel'de genellikle public_html klasörü)
$git_path = __DIR__;

// Log dosyası
$log_file = __DIR__ . '/deploy.log';

// Fonksiyon: Log yazma
function writeLog($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    echo $log_entry;
}

// Güvenlik kontrolü: Token kontrolü
$provided_token = isset($_GET['token']) ? $_GET['token'] : '';
if (empty($provided_token) || $provided_token !== $secret_token) {
    http_response_code(403);
    writeLog("ERROR: Unauthorized access attempt from IP: " . $_SERVER['REMOTE_ADDR']);
    die('Unauthorized');
}

// GitHub/GitLab webhook payload kontrolü (opsiyonel)
$payload = file_get_contents('php://input');
if (!empty($payload)) {
    $data = json_decode($payload, true);
    // GitHub webhook signature kontrolü (daha güvenli)
    if (isset($_SERVER['HTTP_X_HUB_SIGNATURE_256'])) {
        $signature = $_SERVER['HTTP_X_HUB_SIGNATURE_256'];
        $expected_signature = 'sha256=' . hash_hmac('sha256', $payload, $secret_token);
        if (!hash_equals($expected_signature, $signature)) {
            http_response_code(403);
            writeLog("ERROR: Invalid signature");
            die('Invalid signature');
        }
    }
}

writeLog("Deployment started...");

// GitHub repository URL (değiştirmeniz gerekebilir)
$github_repo = 'https://github.com/chenrey1/site-vhsp.git';

// Eğer Git repository değilse, önce başlat
if (!is_dir($git_path . '/.git')) {
    writeLog("Git repository not found. Initializing...");
    
    $init_commands = array(
        'cd ' . escapeshellarg($git_path),
        'git init',
        'git remote add origin ' . escapeshellarg($github_repo) . ' 2>&1 || git remote set-url origin ' . escapeshellarg($github_repo) . ' 2>&1',
    );
    
    foreach ($init_commands as $cmd) {
        writeLog("Executing: $cmd");
        exec($cmd, $init_output, $init_return);
        if ($init_return !== 0) {
            writeLog("WARNING: " . implode("\n", $init_output));
        } else {
            writeLog("SUCCESS");
        }
    }
}

// Git pull komutu
$commands = array(
    'cd ' . escapeshellarg($git_path),
    'git fetch origin 2>&1',
    'git reset --hard origin/main 2>&1 || git reset --hard origin/master 2>&1',
    'git pull origin main 2>&1 || git pull origin master 2>&1',
);

$output = array();
$return_var = 0;

foreach ($commands as $command) {
    writeLog("Executing: $command");
    exec($command, $output, $return_var);
    
    if ($return_var !== 0) {
        writeLog("ERROR: Command failed with return code $return_var");
        writeLog("Output: " . implode("\n", $output));
    } else {
        writeLog("SUCCESS: " . implode("\n", $output));
    }
    $output = array();
}

// Composer install (eğer composer.json varsa)
if (file_exists($git_path . '/composer.json')) {
    writeLog("Running composer install...");
    exec('cd ' . escapeshellarg($git_path) . ' && composer install --no-dev --optimize-autoloader 2>&1', $composer_output, $composer_return);
    if ($composer_return === 0) {
        writeLog("Composer install completed successfully");
    } else {
        writeLog("WARNING: Composer install had issues: " . implode("\n", $composer_output));
    }
}

// Cache temizleme (CodeIgniter için)
if (is_dir($git_path . '/application/cache')) {
    writeLog("Clearing cache...");
    $cache_files = glob($git_path . '/application/cache/*');
    foreach ($cache_files as $file) {
        if (is_file($file) && basename($file) !== 'index.html') {
            unlink($file);
        }
    }
    writeLog("Cache cleared");
}

writeLog("Deployment completed successfully!");
echo "Deployment completed successfully!";

