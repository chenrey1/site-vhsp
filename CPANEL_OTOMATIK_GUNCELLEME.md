# 🚀 cPanel Otomatik Güncelleme - Sadece cPanel Kullanıcıları İçin

## 📖 Bu Rehber Ne İçin?

Bu rehber, **sadece cPanel'den dosya düzenleyen** kullanıcılar için hazırlandı. Yerel bilgisayarınızda Git kurmanıza gerek yok!

---

## 🎯 Ne Yapacağız?

**Hedef:** Ben (AI) bir dosyada değişiklik yaptığımda, bu değişiklik otomatik olarak cPanel'deki sitenize yansısın.

**Nasıl Çalışacak:**
1. Ben bir dosyayı düzenlerim
2. Değişiklikler GitHub'a yüklenir (otomatik)
3. cPanel'deki Git sistemi bunu algılar
4. Dosyalar otomatik güncellenir

---

## 📝 ADIM ADIM KURULUM (Çok Basit!)

### ADIM 1: GitHub Hesabı Oluşturun (5 dakika)

1. **https://github.com** adresine gidin
2. **Sign up** (Kayıt ol) butonuna tıklayın
3. Email, şifre ve kullanıcı adı girin
4. Hesabınızı oluşturun

> 💡 **Neden GitHub?** 
> - Ücretsiz
> - cPanel ile otomatik çalışır
> - Dosyalarınızı güvenle saklar

---

### ADIM 2: GitHub'da Yeni Repository Oluşturun (2 dakika)

1. GitHub'a giriş yapın
2. Sağ üstteki **"+"** işaretine tıklayın
3. **"New repository"** seçin
4. Şunları doldurun:
   - **Repository name**: `valohesap-site` (istediğiniz ismi verebilirsiniz)
   - **Description**: "ValoHesap.com website" (opsiyonel)
   - **Public** veya **Private** seçin (Private önerilir)
   - **"Add a README file"** işaretini KALDIRIN (boş repo istiyoruz)
5. **"Create repository"** butonuna tıklayın

---

### ADIM 3: cPanel'de Git Version Control Kurulumu (10 dakika)

#### 3.1. cPanel'e Giriş Yapın

1. Hosting firmanızın cPanel linkine gidin
2. Kullanıcı adı ve şifrenizle giriş yapın

#### 3.2. Git Version Control Bulun

1. cPanel ana sayfasında **"Git Version Control"** yazısını arayın
   - Eğer göremiyorsanız, arama kutusuna "git" yazın
2. **"Git Version Control"** ikonuna tıklayın

> ⚠️ **Not:** Bazı cPanel'lerde bu özellik yoktur. O zaman **ADIM 4**'e geçin.

#### 3.3. Yeni Repository Oluşturun

1. **"Create"** butonuna tıklayın
2. Şu bilgileri girin:

   **Repository URL:**
   ```
   https://github.com/KULLANICI_ADINIZ/valohesap-site.git
   ```
   > ⚠️ **ÖNEMLİ:** `KULLANICI_ADINIZ` yerine GitHub kullanıcı adınızı yazın!
   > Örnek: `https://github.com/ahmet123/valohesap-site.git`

   **Repository Root:**
   ```
   public_html
   ```
   > Bu, sitenizin ana klasörüdür. Genellikle `public_html` olur.

   **Branch:**
   ```
   main
   ```
   > GitHub'da varsayılan branch genellikle `main`'dir.

   **Auto Pull:**
   ```
   ✅ İŞARETLEYİN (ÇOK ÖNEMLİ!)
   ```
   > Bu işaretli olursa, GitHub'daki değişiklikler otomatik gelir.

3. **"Create"** butonuna tıklayın

#### 3.4. İlk Dosyaları Yükleyin

cPanel Git sistemi, GitHub'dan dosyaları çekmeye çalışacak ama GitHub boş olduğu için hata verebilir. Bu normal!

**Şimdi ne yapmalıyız?**
- cPanel'deki mevcut dosyalarınızı GitHub'a yüklememiz gerekiyor
- Bunu **ADIM 4**'te yapacağız

---

### ADIM 4: Mevcut Dosyaları GitHub'a Yükleme (15 dakika)

Bu adımda, cPanel'deki dosyalarınızı GitHub'a yükleyeceğiz.

#### Seçenek A: cPanel Terminal Kullanarak (Önerilen)

1. cPanel'de **"Terminal"** veya **"SSH Access"** bölümünü bulun
2. Terminal'i açın
3. Şu komutları sırayla yazın (her satırdan sonra Enter'a basın):

```bash
cd ~/public_html
git init
git add .
git commit -m "İlk yükleme"
git branch -M main
git remote add origin https://github.com/KULLANICI_ADINIZ/valohesap-site.git
git push -u origin main
```

> ⚠️ **ÖNEMLİ:** 
> - `KULLANICI_ADINIZ` yerine GitHub kullanıcı adınızı yazın
> - GitHub kullanıcı adı ve şifre soracak, girin

#### Seçenek B: cPanel File Manager Kullanarak (Daha Kolay)

Eğer Terminal kullanmak istemiyorsanız:

1. cPanel'de **"File Manager"** açın
2. `public_html` klasörüne gidin
3. Tüm dosyaları seçin (Ctrl+A)
4. **"Compress"** (Sıkıştır) butonuna tıklayın
5. **ZIP** formatını seçin
6. ZIP dosyasını bilgisayarınıza indirin
7. ZIP'i açın
8. GitHub repository sayfanıza gidin
9. **"uploading an existing file"** linkine tıklayın
10. Tüm dosyaları sürükleyip bırakın
11. **"Commit changes"** butonuna tıklayın

> ⚠️ **DİKKAT:** 
> - `application/config/database.php` ve `application/config/config.php` dosyalarını YÜKLEMEYİN!
> - Bu dosyalar hassas bilgiler içerir
> - Zaten `.gitignore` dosyasında var, yüklenmeyecek

---

### ADIM 5: deploy.php Dosyasını Ayarlama (5 dakika)

1. cPanel File Manager'da `deploy.php` dosyasını bulun
2. Dosyaya sağ tıklayın → **"Edit"** (Düzenle)
3. Şu satırı bulun:
   ```php
   $secret_token = 'CHANGE_THIS_TO_A_RANDOM_SECRET_TOKEN_' . bin2hex(random_bytes(16));
   ```
4. Bu satırı şöyle değiştirin:
   ```php
   $secret_token = 'benim_super_gizli_token_12345_xyz';
   ```
   > 💡 **İpucu:** Kendi token'ınızı oluşturun. En az 20 karakter, rastgele harf ve sayılar.

5. Dosyayı kaydedin

---

### ADIM 6: GitHub Webhook Ayarlama (5 dakika)

Bu adım, GitHub'daki değişikliklerin otomatik olarak cPanel'e gelmesini sağlar.

1. GitHub repository sayfanıza gidin
2. Üstteki **"Settings"** sekmesine tıklayın
3. Sol menüden **"Webhooks"** seçin
4. **"Add webhook"** butonuna tıklayın
5. Şu bilgileri girin:

   **Payload URL:**
   ```
   https://yourdomain.com/deploy.php?token=benim_super_gizli_token_12345_xyz
   ```
   > ⚠️ **ÖNEMLİ:**
   > - `yourdomain.com` yerine sitenizin gerçek domain adını yazın
   > - `benim_super_gizli_token_12345_xyz` yerine ADIM 5'te yazdığınız token'ı yazın

   **Content type:**
   ```
   application/json
   ```

   **Which events would you like to trigger this webhook?**
   ```
   Just the push event
   ```

   **Active:**
   ```
   ✅ İşaretli olsun
   ```

6. **"Add webhook"** butonuna tıklayın

---

## ✅ KURULUM TAMAMLANDI!

Artık sistem hazır! Nasıl çalışacak:

1. ✅ Ben (AI) bir dosyada değişiklik yaparım
2. ✅ Değişiklik GitHub'a yüklenir
3. ✅ GitHub webhook tetiklenir
4. ✅ `deploy.php` çalışır
5. ✅ cPanel'deki dosyalar otomatik güncellenir

---

## 🧪 TEST EDELİM

Kurulumun çalışıp çalışmadığını test etmek için:

1. GitHub repository sayfanıza gidin
2. Herhangi bir dosyayı düzenleyin (örnek: README.md)
3. Küçük bir değişiklik yapın (örnek: bir satır ekleyin)
4. **"Commit changes"** butonuna tıklayın
5. 1-2 dakika bekleyin
6. cPanel File Manager'da aynı dosyayı kontrol edin
7. Değişiklik görünüyorsa ✅ **BAŞARILI!**

---

## ❓ SIK SORULAN SORULAR

### S: "Keep all" ne demek?

**C:** Cursor editöründe, yeni dosyalar eklerken bir uyarı çıkabilir. **"Keep all"** tıklayın, sorun olmaz. Yeni dosyalar eklenecek, mevcut dosyalar değişmeyecek.

---

### S: Yerel bilgisayarımda Git kurmam gerekiyor mu?

**C:** Hayır! Sadece cPanel'den çalışıyorsanız, yerel bilgisayarınızda Git kurmanıza gerek yok. Tüm işlemler cPanel ve GitHub üzerinden yapılır.

---

### S: Terminal kullanmak zorunda mıyım?

**C:** Hayır! ADIM 4'te **Seçenek B**'yi kullanabilirsiniz. File Manager ile ZIP indirip GitHub'a yükleyebilirsiniz.

---

### S: Git Version Control cPanel'de yok, ne yapmalıyım?

**C:** O zaman şu yöntemi kullanın:
1. GitHub'da repository oluşturun (ADIM 2)
2. Dosyaları GitHub'a yükleyin (ADIM 4 - Seçenek B)
3. `deploy.php` dosyasını kullanın (ADIM 5-6)
4. Her değişiklikte `deploy.php` URL'ini tarayıcıdan açın:
   ```
   https://yourdomain.com/deploy.php?token=TOKENINIZ
   ```

---

### S: Hassas dosyalar (database.php) GitHub'a gider mi?

**C:** Hayır! `.gitignore` dosyasında bu dosyalar var. GitHub'a yüklenmezler. Sunucuda kalırlar.

---

### S: Webhook çalışmıyor, ne yapmalıyım?

**C:** 
1. `deploy.php` dosyasındaki token'ı kontrol edin
2. Webhook URL'indeki token'ı kontrol edin (aynı olmalı)
3. `deploy.log` dosyasını kontrol edin (hata mesajları orada)
4. Domain adının doğru olduğundan emin olun

---

### S: Manuel güncelleme yapabilir miyim?

**C:** Evet! cPanel Terminal'den:
```bash
cd ~/public_html
git pull origin main
```

---

## 🆘 YARDIM GEREKİRSE

Sorun yaşarsanız:
1. `deploy.log` dosyasını kontrol edin (cPanel File Manager'da)
2. cPanel error log'larını kontrol edin
3. GitHub repository ayarlarını kontrol edin

---

## 📝 ÖZET

✅ GitHub hesabı oluşturun
✅ Repository oluşturun
✅ cPanel'de Git Version Control kurun
✅ Dosyaları GitHub'a yükleyin
✅ `deploy.php` token'ını ayarlayın
✅ GitHub webhook ekleyin
✅ Test edin!

**Hepsi bu kadar! 🎉**

Artık ben bir değişiklik yaptığımda, otomatik olarak sitenize yansıyacak!

