# 🚀 cPanel Otomatik Deployment Kurulum Rehberi

Bu rehber, yaptığınız değişikliklerin otomatik olarak cPanel sunucunuza yansıması için gerekli adımları içerir.

## 📋 Gereksinimler

1. cPanel erişimi
2. Git repository (GitHub, GitLab, Bitbucket vb.)
3. SSH erişimi (opsiyonel ama önerilir)

---

## 🔧 Kurulum Adımları

### 1. Git Repository Oluşturma

#### Yerel Bilgisayarınızda:

```bash
# Proje klasörüne gidin
cd C:\Users\GAMING\Downloads\public_html

# Git repository başlatın
git init

# Tüm dosyaları ekleyin
git add .

# İlk commit
git commit -m "Initial commit"

# GitHub/GitLab'de yeni bir repository oluşturun, sonra:
git remote add origin https://github.com/KULLANICI_ADI/REPO_ADI.git
# veya
git remote add origin https://gitlab.com/KULLANICI_ADI/REPO_ADI.git

# Dosyaları yükleyin
git branch -M main
git push -u origin main
```

### 2. cPanel'de Git Repository Kurulumu

#### Yöntem A: cPanel Git Version Control (Önerilen)

1. **cPanel'e giriş yapın**
2. **"Git Version Control"** bölümünü bulun
3. **"Create"** butonuna tıklayın
4. Aşağıdaki bilgileri girin:
   - **Repository URL**: `https://github.com/KULLANICI_ADI/REPO_ADI.git`
   - **Repository Root**: `public_html` (veya sitenizin root klasörü)
   - **Branch**: `main` veya `master`
   - **Auto Pull**: ✅ **İşaretleyin** (Bu önemli!)
5. **Create** butonuna tıklayın

#### Yöntem B: SSH ile Manuel Kurulum

1. **cPanel'de Terminal/SSH** erişimini açın
2. SSH ile bağlanın:
   ```bash
   ssh kullanici@sunucu.com
   ```
3. Proje klasörüne gidin:
   ```bash
   cd ~/public_html
   ```
4. Git repository'yi klonlayın:
   ```bash
   git clone https://github.com/KULLANICI_ADI/REPO_ADI.git .
   ```
   (Eğer zaten dosyalar varsa, önce yedek alın)

### 3. Webhook Script Kurulumu

1. **`deploy.php` dosyasını düzenleyin:**
   - Dosyayı açın
   - `$secret_token` değişkenini değiştirin (güçlü bir token oluşturun)
   - Örnek: `$secret_token = 'my_super_secret_token_12345';`

2. **Dosyayı cPanel'e yükleyin:**
   - `deploy.php` dosyasını `public_html` klasörüne yükleyin
   - Dosya izinlerini kontrol edin (644 veya 755)

3. **GitHub/GitLab Webhook Ayarları:**
   - Repository'nize gidin
   - **Settings** > **Webhooks** > **Add webhook**
   - **Payload URL**: `https://yourdomain.com/deploy.php?token=YOUR_SECRET_TOKEN`
   - **Content type**: `application/json`
   - **Events**: `Just the push event` seçin
   - **Active**: ✅ İşaretleyin
   - **Add webhook** butonuna tıklayın

### 4. Güvenlik Ayarları

1. **`.gitignore` dosyasını kontrol edin:**
   - Hassas dosyaların (database.php, config.php) Git'e eklenmediğinden emin olun
   - Bu dosyalar sunucuda manuel olarak tutulmalı

2. **Sunucuda Hassas Dosyaları Oluşturun:**
   ```bash
   # SSH ile bağlanın ve şu dosyaları oluşturun:
   # application/config/database.php
   # application/config/config.php
   ```
   Bu dosyalar `.gitignore`'da olduğu için Git'e eklenmeyecek.

---

## 🔄 Kullanım

### Otomatik Deployment

Artık her `git push` yaptığınızda:

1. **GitHub/GitLab webhook** tetiklenir
2. **`deploy.php`** script'i çalışır
3. **Git pull** otomatik olarak yapılır
4. **Composer install** çalışır (varsa)
5. **Cache temizlenir**

### Manuel Deployment

Eğer otomatik çalışmazsa, manuel olarak:

1. **cPanel Terminal'den:**
   ```bash
   cd ~/public_html
   git pull origin main
   ```

2. **veya Webhook URL'ini tarayıcıdan çağırın:**
   ```
   https://yourdomain.com/deploy.php?token=YOUR_SECRET_TOKEN
   ```

---

## 🛠️ Sorun Giderme

### Problem: Webhook çalışmıyor

**Çözüm:**
- `deploy.php` dosyasının izinlerini kontrol edin
- Token'ın doğru olduğundan emin olun
- `deploy.log` dosyasını kontrol edin
- cPanel'de PHP versiyonunu kontrol edin (7.4+ olmalı)

### Problem: Git pull hata veriyor

**Çözüm:**
- SSH ile bağlanıp manuel `git pull` deneyin
- Git credentials'ları kontrol edin
- Repository URL'inin doğru olduğundan emin olun

### Problem: Dosyalar güncellenmiyor

**Çözüm:**
- Cache'i temizleyin
- Dosya izinlerini kontrol edin
- `.gitignore` dosyasının doğru olduğundan emin olun

---

## 📝 Notlar

1. **İlk kurulumda** hassas dosyaları (database.php, config.php) manuel olarak sunucuya yüklemelisiniz
2. **Her deployment'tan sonra** siteyi test edin
3. **Backup almayı unutmayın** önemli değişikliklerden önce
4. **`deploy.log`** dosyasını düzenli kontrol edin

---

## 🔐 Güvenlik İpuçları

1. ✅ Secret token'ı güçlü tutun (en az 32 karakter)
2. ✅ `deploy.log` dosyasına erişimi engelleyin (`.htaccess` ile yapıldı)
3. ✅ `.git` klasörüne erişimi engelleyin
4. ✅ Hassas dosyaları `.gitignore`'a ekleyin
5. ✅ Webhook URL'ini sadece güvendiğiniz kişilerle paylaşın

---

## 📞 Destek

Sorun yaşarsanız:
1. `deploy.log` dosyasını kontrol edin
2. cPanel error log'larını kontrol edin
3. Git repository'nizin public/private durumunu kontrol edin

---

**Başarılar! 🎉**

