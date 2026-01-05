# File Watcher Script - Dosya değişikliği algılandığında otomatik push

$folder = "C:\Users\GAMING\Downloads\public_html"
$gitPath = "C:\Users\GAMING\Downloads\public_html"

# Git kimlik ayarları
git -C $gitPath config user.name "ValoHesap" 2>$null
git -C $gitPath config user.email "valohesap@example.com" 2>$null

# File watcher oluştur
$watcher = New-Object System.IO.FileSystemWatcher
$watcher.Path = $folder
$watcher.Filter = "*.*"
$watcher.IncludeSubdirectories = $true
$watcher.EnableRaisingEvents = $true

# Değişiklik algılandığında
$action = {
    $path = $Event.SourceEventArgs.FullPath
    $changeType = $Event.SourceEventArgs.ChangeType
    
    # .git ve .vscode klasörlerini ignore et
    if ($path -match "\.git|\.vscode") { return }
    
    Write-Host "Değişiklik algılandı: $path" -ForegroundColor Green
    
    # 5 saniye bekle (birden fazla değişiklik için)
    Start-Sleep -Seconds 5
    
    # Git işlemleri
    Set-Location $gitPath
    git pull origin main --no-edit 2>$null
    git add .
    git commit -m "Otomatik güncelleme - $(Get-Date)" 2>$null
    git push origin main 2>$null
    
    Write-Host "✅ GitHub'a push edildi!" -ForegroundColor Green
}

# Event handler'ları kaydet
Register-ObjectEvent $watcher "Changed" -Action $action
Register-ObjectEvent $watcher "Created" -Action $action
Register-ObjectEvent $watcher "Deleted" -Action $action

Write-Host "👀 Dosya izleme başlatıldı. Çıkmak için Ctrl+C basın." -ForegroundColor Yellow

# Script'i çalışır tut
try {
    while ($true) {
        Start-Sleep -Seconds 1
    }
} finally {
    $watcher.Dispose()
}

