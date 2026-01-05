# 🚀 Cursor SFTP Extension Kurulumu (GitHub'sız Otomatik Yükleme)

## 🎯 Bu Yöntem Neden Daha İyi?

✅ **GitHub'a gerek yok!**
✅ **Webhook'a gerek yok!**
✅ **Direkt cPanel'e yüklenir!**
✅ **Ben değişiklik yaptığımda otomatik yüklenir!**

---

## 📝 ADIM ADIM KURULUM

### ADIM 1: SFTP Extension Kurulumu (2 dakika)

1. **Cursor'da Extensions'a gidin:**
   - Sol menüden Extensions ikonuna tıklayın (veya Ctrl+Shift+X)

2. **"SFTP" yazın ve arayın**

3. **"SFTP" extension'ını bulun** (Natizyskunk tarafından)
   - Veya "FTP-Sync" extension'ını kullanabilirsiniz

4. **"Install" butonuna tıklayın**

---

### ADIM 2: SFTP Ayarları (5 dakika)

1. **Proje klasörünüzde `.vscode` klasörü oluşturun** (eğer yoksa)
   - `C:\Users\GAMING\Downloads\public_html\.vscode`

2. **`.vscode` klasöründe `sftp.json` dosyası oluşturun**

3. **Aşağıdaki ayarları yapıştırın:**

```json
{
    "name": "ValoHesap cPanel",
    "host": "valohesap.com",
    "protocol": "sftp",
    "port": 22,
    "username": "valohesa",
    "password": "CPANEL_SIFRENIZ",
    "remotePath": "/home/valohesa/public_html",
    "uploadOnSave": true,
    "useTempFile": false,
    "openSsh": false,
    "ignore": [
        ".vscode",
        ".git",
        ".DS_Store",
        "node_modules",
        "*.log",
        ".gitignore"
    ]
}
```

> ⚠️ **ÖNEMLİ:** 
> - `host`: cPanel host adresiniz (veya IP)
> - `username`: cPanel kullanıcı adınız
> - `password`: cPanel şifreniz
> - `remotePath`: Genellikle `/home/KULLANICI_ADI/public_html`

---

### ADIM 3: SFTP Bilgilerini Bulma

cPanel SFTP bilgilerinizi bulmak için:

1. **cPanel'e giriş yapın**
2. **"FTP Accounts" bölümüne gidin**
3. **Veya "File Manager" → sağ üstte "Connect" butonuna tıklayın**
4. **SFTP bilgilerini göreceksiniz:**
   - Host: `valohesap.com` veya `ftp.valohesap.com`
   - Port: `22` (SFTP) veya `21` (FTP)
   - Username: cPanel kullanıcı adınız
   - Password: cPanel şifreniz

---

### ADIM 4: Test Edin

1. **Herhangi bir dosyayı düzenleyin**
2. **Ctrl+S ile kaydedin**
3. **Otomatik olarak cPanel'e yüklenecek!**

---

## 🔐 Güvenlik İpucu

Şifreyi direkt dosyaya yazmak güvenli değil. Alternatif:

1. **SSH Key kullanın** (daha güvenli)
2. **Veya şifreyi environment variable olarak saklayın**

---

## ✅ KURULUM TAMAMLANDI!

Artık:
1. ✅ Ben bir dosyada değişiklik yaparım
2. ✅ Siz Ctrl+S ile kaydedersiniz
3. ✅ Otomatik olarak cPanel'e yüklenir!
4. ✅ Hiç GitHub'a gerek yok!

---

## 🎯 ÖZET

1. ✅ SFTP extension kurun
2. ✅ `.vscode/sftp.json` dosyası oluşturun
3. ✅ cPanel bilgilerinizi girin
4. ✅ `uploadOnSave: true` yapın
5. ✅ Artık her kaydettiğinizde otomatik yüklenir!

**Hepsi bu kadar! 🎉**

