# 🚀 Otomatik Push Kurulumu

## 🎯 Nasıl Çalışacak?

Ben bir dosyada değişiklik yaptığımda:
1. ✅ Dosya kaydedilir
2. ✅ Otomatik commit edilir
3. ✅ Otomatik GitHub'a push edilir
4. ✅ Webhook tetiklenir
5. ✅ cPanel'de otomatik güncellenir

**Hiçbir şey yapmanıza gerek yok!** 🎉

---

## 📝 KURULUM

### Yöntem 1: Git Hook (En Basit)

Git hook zaten kuruldu! Artık:
1. Ben bir dosyada değişiklik yaparım
2. Siz `auto_commit_and_push.bat` dosyasına çift tıklayın
3. Otomatik commit + push edilir

---

### Yöntem 2: File Watcher (Tam Otomatik)

1. **PowerShell'i Yönetici olarak açın**

2. **Şu komutu çalıştırın:**
   ```powershell
   cd C:\Users\GAMING\Downloads\public_html
   .\watch_and_push.ps1
   ```

3. **Script çalışır durumda kalacak**
   - Ben bir dosyada değişiklik yaparım
   - Otomatik algılanır
   - Otomatik commit + push edilir
   - Hiçbir şey yapmanıza gerek yok!

4. **Çıkmak için:** Ctrl+C

---

### Yöntem 3: Cursor Task (Yarı Otomatik)

1. **Cursor'da Command Palette açın:** Ctrl+Shift+P
2. **"Tasks: Run Task" yazın**
3. **"Auto Push to GitHub" seçin**
4. **Her değişiklikten sonra bu task'ı çalıştırın**

---

## ✅ ÖNERİLEN YÖNTEM

**Yöntem 1 (Git Hook)** - En basit:
- Ben değişiklik yaparım
- Siz `auto_commit_and_push.bat` dosyasına çift tıklayın
- Otomatik push edilir

**Yöntem 2 (File Watcher)** - Tam otomatik:
- Script'i bir kez başlatın
- Artık hiçbir şey yapmanıza gerek yok!

---

## 🎯 HANGİSİNİ SEÇMELİYİM?

- **Basit ve kontrol edilebilir:** Yöntem 1
- **Tam otomatik:** Yöntem 2

**Hangisini tercih edersiniz?** 🤔

