# 🚀 Yerel Bilgisayarda Git Kurulumu (Otomatik Push İçin)

## 🎯 Neden Gerekli?

Şu anki durum:
- ❌ Ben bir dosyada değişiklik yapıyorum
- ❌ Değişiklik yerel bilgisayarınızda kalıyor
- ❌ GitHub'a gitmiyor
- ❌ Manuel yükleme gerekiyor

**Kurulumdan sonra:**
- ✅ Ben bir dosyada değişiklik yapıyorum
- ✅ Değişiklik otomatik GitHub'a push ediliyor
- ✅ Webhook tetikleniyor
- ✅ cPanel'de otomatik güncelleniyor

---

## 📝 ADIM ADIM KURULUM

### ADIM 1: Git Kurulumu (5 dakika)

1. **Git'i indirin:**
   - https://git-scm.com/download/win
   - "Download for Windows" butonuna tıklayın
   - İndirilen dosyayı çalıştırın
   - Kurulum sırasında tüm ayarları varsayılan bırakın (Next, Next, Install)

2. **Kurulumu kontrol edin:**
   - Windows PowerShell veya CMD açın
   - Şu komutu yazın:
     ```bash
     git --version
     ```
   - Versiyon numarası görünürse ✅ **Başarılı!**

---

### ADIM 2: Proje Klasöründe Git Başlatma (2 dakika)

1. **PowerShell'i açın:**
   - Windows tuşu + X
   - "Windows PowerShell" veya "Terminal" seçin

2. **Proje klasörüne gidin:**
   ```powershell
   cd C:\Users\GAMING\Downloads\public_html
   ```

3. **Git repository başlatın:**
   ```powershell
   git init
   ```

4. **GitHub'a bağlayın:**
   ```powershell
   git remote add origin https://github.com/chenrey1/site-vhsp.git
   ```

5. **İlk commit yapın (mevcut dosyalar için):**
   ```powershell
   git add .
   git commit -m "İlk commit - Mevcut dosyalar"
   git branch -M main
   git push -u origin main
   ```
   
   > ⚠️ **Not:** GitHub kullanıcı adı ve şifre soracak. Şifre yerine **Personal Access Token** kullanmanız gerekebilir (aşağıda anlatıldı).

---

### ADIM 3: GitHub Personal Access Token (5 dakika)

GitHub artık şifre kabul etmiyor, token gerekiyor:

1. **GitHub'a giriş yapın**
2. **Sağ üstte profil fotoğrafınıza tıklayın** → **Settings**
3. **Sol menüden "Developer settings" seçin**
4. **"Personal access tokens"** → **"Tokens (classic)"**
5. **"Generate new token"** → **"Generate new token (classic)"**
6. **Token ayarları:**
   - **Note:** `cPanel Auto Deploy` (istediğiniz isim)
   - **Expiration:** `90 days` (veya istediğiniz süre)
   - **Scopes:** `repo` işaretleyin (tüm repo yetkileri)
7. **"Generate token" butonuna tıklayın**
8. **Token'ı kopyalayın** (bir daha gösterilmeyecek!)

9. **PowerShell'de push yaparken:**
   - Username: `chenrey1` (GitHub kullanıcı adınız)
   - Password: **Token'ı yapıştırın** (şifre değil!)

---

### ADIM 4: Otomatik Push Script'i (Opsiyonel)

Her değişiklikten sonra otomatik push için bir script oluşturabiliriz. Ama şimdilik manuel push yeterli.

**Manuel push komutu:**
```powershell
cd C:\Users\GAMING\Downloads\public_html
git add .
git commit -m "Değişiklikler"
git push origin main
```

---

## ✅ KURULUM TAMAMLANDI!

Artık:
1. ✅ Ben bir dosyada değişiklik yaparım
2. ✅ Siz PowerShell'de `git add . && git commit -m "Değişiklik" && git push` yaparsınız
3. ✅ GitHub'a push edilir
4. ✅ Webhook tetiklenir
5. ✅ cPanel'de otomatik güncellenir

---

## 🎯 DAHA DA OTOMATİK YAPMAK İSTER MİSİNİZ?

Eğer "git add, commit, push" yapmak istemiyorsanız, bir script yazabilirim ki:
- Ben bir dosyada değişiklik yaptığımda
- Otomatik olarak algılansın
- Otomatik push edilsin

İsterseniz bunu da kurabiliriz!

---

## 📝 ÖZET

1. ✅ Git'i kurun
2. ✅ Proje klasöründe `git init` yapın
3. ✅ GitHub'a bağlayın
4. ✅ Personal Access Token oluşturun
5. ✅ İlk push'u yapın
6. ✅ Artık her değişiklikte `git add . && git commit -m "Mesaj" && git push` yapın

**Hepsi bu kadar! 🎉**

