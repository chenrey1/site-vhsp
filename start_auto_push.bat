@echo off
REM Windows başlangıcında otomatik başlatmak için
REM Bu dosyayı Windows Startup klasörüne kopyalayın

cd /d "C:\Users\GAMING\Downloads\public_html"

REM PowerShell script'ini arka planda başlat
start /min powershell -ExecutionPolicy Bypass -WindowStyle Hidden -File "C:\Users\GAMING\Downloads\public_html\watch_and_push.ps1"

