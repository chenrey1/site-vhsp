@echo off
REM Otomatik Git Push Script
REM Ben bir dosyada değişiklik yaptığımda, bu script'i çalıştırın

cd /d "C:\Users\GAMING\Downloads\public_html"

REM Git kimlik ayarları (ilk çalıştırmada)
git config user.name "ValoHesap" 2>nul
git config user.email "valohesap@example.com" 2>nul

REM Git repository kontrolü
if not exist ".git" (
    echo Git repository başlatılıyor...
    git init
    git remote add origin https://github.com/chenrey1/site-vhsp.git 2>nul
    git branch -M main
)

REM Önce GitHub'dan değişiklikleri çek (pull)
git pull origin main --allow-unrelated-histories --no-edit 2>nul

REM Değişiklikleri ekle ve commit et
git add .
git commit -m "Otomatik güncelleme - %date% %time%" 2>nul

REM Push et
git push origin main

echo.
echo ✅ Değişiklikler GitHub'a yüklendi!
echo 🔄 Webhook tetikleniyor, cPanel'de güncellenecek...
pause

