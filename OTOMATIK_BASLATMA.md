# 🚀 Otomatik Başlatma Kurulumu

## 🎯 Nasıl Çalışacak?

Windows açıldığında:
1. ✅ Script otomatik başlar
2. ✅ Dosya değişikliklerini izler
3. ✅ Ben bir dosyada değişiklik yaparım
4. ✅ Otomatik algılanır
5. ✅ Otomatik commit + push edilir
6. ✅ Webhook tetiklenir
7. ✅ cPanel'de otomatik güncellenir

**Hiçbir şey yapmanıza gerek yok!** 🎉

---

## 📝 KURULUM (2 Dakika)

### ADIM 1: Windows Startup Klasörünü Açın

1. **Windows tuşu + R** basın
2. Şunu yazın: `shell:startup`
3. **Enter** basın
4. Startup klasörü açılacak

---

### ADIM 2: Script'i Kopyalayın

1. `start_auto_push.bat` dosyasını bulun
2. **Kopyalayın** (Ctrl+C)
3. **Startup klasörüne yapıştırın** (Ctrl+V)

---

### ADIM 3: Test Edin

1. **Bilgisayarı yeniden başlatın** (veya şimdi test için)
2. **Task Manager'ı açın** (Ctrl+Shift+Esc)
3. **Processes** sekmesine gidin
4. **PowerShell** process'ini arayın
5. `watch_and_push.ps1` çalışıyor olmalı

---

## ✅ TAMAMLANDI!

Artık:
- ✅ Windows açıldığında script otomatik başlar
- ✅ Ben bir dosyada değişiklik yaparım
- ✅ Otomatik algılanır ve push edilir
- ✅ Hiçbir şey yapmanıza gerek yok!

---

## 🔄 Script'i Durdurmak İsterseniz

1. **Task Manager** açın (Ctrl+Shift+Esc)
2. **Processes** sekmesine gidin
3. **PowerShell** process'ini bulun
4. **End Task** yapın

---

## 🎯 ÖZET

1. ✅ `shell:startup` komutu ile Startup klasörünü açın
2. ✅ `start_auto_push.bat` dosyasını kopyalayın
3. ✅ Startup klasörüne yapıştırın
4. ✅ Bilgisayarı yeniden başlatın
5. ✅ Artık tam otomatik! 🚀

