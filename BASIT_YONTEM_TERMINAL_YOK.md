# 🚀 Terminal Olmadan Otomatik Güncelleme (Basit Yöntem)

## ✅ Bu Yöntem Neden Daha Kolay?

- ❌ Terminal gerekmez
- ❌ cPanel Git kurulumu gerekmez  
- ❌ GitHub'a manuel dosya yükleme gerekmez
- ✅ Sadece `deploy.php` webhook kurulumu yeterli!

---

## 🎯 Nasıl Çalışacak?

1. Ben (AI) bir dosyada değişiklik yaparım
2. Değişiklik otomatik olarak GitHub'a yüklenir
3. GitHub webhook tetiklenir
4. `deploy.php` çalışır ve cPanel'deki dosyaları günceller

**GitHub'ı şimdilik boş bırakabilirsiniz!** İlk değişiklikte otomatik dolacak.

---

## 📝 ADIM ADIM KURULUM

### ADIM 1: deploy.php Dosyasını Ayarlama (5 dakika)

1. **cPanel'de "File Manager" açın**
2. `public_html` klasörüne gidin
3. `deploy.php` dosyasını bulun
4. Dosyaya **sağ tıklayın** → **"Edit"** (Düzenle)
5. Şu satırı bulun (yaklaşık 15. satır):
   ```php
   $secret_token = 'CHANGE_THIS_TO_A_RANDOM_SECRET_TOKEN_' . bin2hex(random_bytes(16));
   ```
6. Bu satırı şöyle değiştirin:
   ```php
   $secret_token = 'benim_super_gizli_token_xyz123_2024';
   ```
   > 💡 **İpucu:** Kendi token'ınızı oluşturun. En az 20 karakter, rastgele harf ve sayılar.
   > Örnek: `valohesap_deploy_token_2024_xyz789`

7. Dosyayı **kaydedin**

---

### ADIM 2: GitHub Webhook Ayarlama (5 dakika)

1. **GitHub repository sayfanıza gidin:**
   - `https://github.com/chenrey1/site-vhsp`

2. **Üstteki "Settings" sekmesine tıklayın**

3. **Sol menüden "Webhooks" seçin**

4. **"Add webhook" butonuna tıklayın**

5. **Şu bilgileri girin:**

   **Payload URL:**
   ```
   https://yourdomain.com/deploy.php?token=benim_super_gizli_token_xyz123_2024
   ```
   > ⚠️ **ÖNEMLİ:**
   > - `yourdomain.com` yerine sitenizin gerçek domain adını yazın
   > - `benim_super_gizli_token_xyz123_2024` yerine ADIM 1'de yazdığınız token'ı yazın
   > 
   > **Örnek:**
   > ```
   > https://valohesap.com/deploy.php?token=valohesap_deploy_token_2024_xyz789
   > ```

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

6. **"Add webhook" butonuna tıklayın**

---

### ADIM 3: Domain Adınızı Bulma

`deploy.php` URL'inde domain adınızı yazmanız gerekiyor. Domain adınızı bilmiyorsanız:

1. cPanel ana sayfasına gidin
2. **"Alan Adları" (Domains)** bölümüne bakın
3. Veya hosting firmanızdan aldığınız bilgilerde domain adı yazıyor
4. Veya cPanel URL'inizde domain adı var (örnek: `cpanel.valohesap.com`)

---

## ✅ KURULUM TAMAMLANDI!

Artık sistem hazır! 

**Nasıl çalışacak:**
1. ✅ Ben bir dosyada değişiklik yaparım
2. ✅ Değişiklik GitHub'a yüklenir (otomatik)
3. ✅ GitHub webhook tetiklenir
4. ✅ `deploy.php` çalışır
5. ✅ cPanel'deki dosyalar otomatik güncellenir

---

## 🧪 TEST EDELİM

Kurulumun çalışıp çalışmadığını test etmek için:

### Yöntem 1: Manuel Test

1. Tarayıcınızda şu URL'yi açın:
   ```
   https://yourdomain.com/deploy.php?token=TOKENINIZ
   ```
2. "Deployment completed successfully!" mesajı görünürse ✅ **BAŞARILI!**

### Yöntem 2: GitHub'dan Test

1. GitHub repository sayfanıza gidin
2. Herhangi bir dosyayı düzenleyin (örnek: README.md oluşturun)
3. Küçük bir değişiklik yapın
4. **"Commit changes"** butonuna tıklayın
5. 1-2 dakika bekleyin
6. cPanel File Manager'da aynı dosyayı kontrol edin
7. Değişiklik görünüyorsa ✅ **BAŞARILI!**

---

## ❓ SORUN GİDERME

### Problem: Webhook çalışmıyor

**Çözüm:**
1. `deploy.php` dosyasındaki token'ı kontrol edin
2. Webhook URL'indeki token'ı kontrol edin (aynı olmalı)
3. Domain adının doğru olduğundan emin olun
4. `deploy.log` dosyasını kontrol edin (cPanel File Manager'da)

### Problem: deploy.log dosyası yok

**Çözüm:**
- Normal, ilk çalıştırmada oluşur
- Manuel test yapın (Yöntem 1)

### Problem: "Unauthorized" hatası

**Çözüm:**
- Token'ların aynı olduğundan emin olun
- `deploy.php` dosyasındaki token'ı kontrol edin

---

## 📝 ÖZET

✅ `deploy.php` token'ını ayarlayın
✅ GitHub webhook ekleyin
✅ Domain adınızı yazın
✅ Test edin!

**Hepsi bu kadar! 🎉**

Artık ben bir değişiklik yaptığımda, otomatik olarak sitenize yansıyacak!

---

## 💡 NOT

- GitHub repository'niz şimdilik boş olabilir, sorun değil
- İlk değişiklikte otomatik dolacak
- cPanel Git kurulumu yapmanıza gerek yok
- Terminal kullanmanıza gerek yok

