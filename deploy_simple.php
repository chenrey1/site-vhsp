<?php
/**
 * cPanel Otomatik Deployment Script
 * 
 * Bu script, GitHub'dan gelen değişiklikleri otomatik olarak çeker.
 */

// ÖNEMLİ: Bu token'ı değiştirin! Kendi token'ınızı oluşturun (en az 20 karakter)
$secret_token = 'valohesap_deploy_token_2024_xyz789';

// Git repository path
$git_path = __DIR__;

// Log dosyası
$log_file = __DIR__ . '/deploy.log';

// Log yazma fonksiyonu
function writeLog($message) {
    global $log_file;
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] $message\n";
    file_put_contents($log_file, $log_entry, FILE_APPEND);
    echo $log_entry;
}

// Token kontrolü
$provided_token = isset($_GET['token']) ? $_GET['token'] : '';
if (empty($provided_token) || $provided_token !== $secret_token) {
    http_response_code(403);
    writeLog("ERROR: Unauthorized access from IP: " . $_SERVER['REMOTE_ADDR']);
    die('Unauthorized');
}

writeLog("Deployment started...");

// Git pull komutları
$commands = array(
    'cd ' . escapeshellarg($git_path),
    'git fetch origin 2>&1',
    'git reset --hard origin/main 2>&1 || git reset --hard origin/master 2>&1',
    'git pull origin main 2>&1 || git pull origin master 2>&1',
);

foreach ($commands as $command) {
    writeLog("Executing: $command");
    exec($command, $output, $return_var);
    if ($return_var !== 0) {
        writeLog("WARNING: " . implode("\n", $output));
    } else {
        writeLog("SUCCESS");
    }
    $output = array();
}

// Cache temizleme
if (is_dir($git_path . '/application/cache')) {
    $cache_files = glob($git_path . '/application/cache/*');
    foreach ($cache_files as $file) {
        if (is_file($file) && basename($file) !== 'index.html') {
            unlink($file);
        }
    }
    writeLog("Cache cleared");
}

writeLog("Deployment completed!");
echo "Deployment completed successfully!";

