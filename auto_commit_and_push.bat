@echo off
REM Otomatik Commit ve Push Script
REM Bu script'i Cursor'da task olarak ayarlayabilirsiniz

cd /d "C:\Users\GAMING\Downloads\public_html"

REM Git kimlik ayarları
git config user.name "ValoHesap" 2>nul
git config user.email "valohesap@example.com" 2>nul

REM Önce pull yap
git pull origin main --no-edit 2>nul

REM Değişiklikleri ekle ve commit et
git add .
git commit -m "Otomatik güncelleme - %date% %time%" 2>nul

REM Push et (post-commit hook otomatik çalışacak)
git push origin main

