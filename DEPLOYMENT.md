# 🎰 Casino Portfolio - BERAT K | Deployment Guide

Bu belgede, BERAT K'nın casino portfolio web sitesinin cPanel hosting ortamına nasıl deploy edileceği adım adım açıklanmaktadır.

## 📋 Sistem Gereksinimleri

### Sunucu Gereksinimleri
- **PHP**: 8.0 veya üzeri (önerilen: 8.1+)
- **Web Server**: Apache 2.4+ (mod_rewrite aktif)
- **SQLite**: 3.x (PHP SQLite3 extension)
- **Disk Alanı**: Minimum 500MB
- **SSL Sertifikası**: Önerilen (güvenlik için)

### PHP Uzantıları
- `sqlite3` - Veritabanı işlemleri
- `pdo_sqlite` - PDO veritabanı sürücüsü
- `gd` - Görsel işleme
- `curl` - API istekleri
- `json` - JSON veri işleme
- `mbstring` - Çoklu karakter desteği
- `openssl` - Güvenlik işlemleri

## 🚀 cPanel Deployment Adımları

### 1. Dosya Yükleme

#### File Manager ile:
1. cPanel'e giriş yapın
2. **File Manager**'a tıklayın
3. `public_html` klasörüne gidin
4. Tüm proje dosyalarını bu klasöre yükleyin

#### FTP ile:
```bash
# FTP bilgilerinizi kullanarak
ftp your-domain.com
username: your-ftp-username
password: your-ftp-password

# Dosyaları public_html'e yükleyin
put -r casino-portfolio/* public_html/
```

### 2. Dosya İzinleri Ayarlama

cPanel File Manager'da aşağıdaki izinleri ayarlayın:

```bash
# Klasör izinleri (755)
/database/ -> 755
/uploads/ -> 755
/assets/ -> 755
/admin/ -> 755
/api/ -> 755
/cache/ -> 755
/backups/ -> 755

# Dosya izinleri (644)
*.php -> 644
*.html -> 644
*.css -> 644
*.js -> 644
.htaccess -> 644

# Özel izinler
/database/casino_portfolio.db -> 666 (yazılabilir)
/uploads/ -> 777 (yazılabilir)
/cache/ -> 777 (yazılabilir)
/backups/ -> 777 (yazılabilir)
```

### 3. PHP Ayarları

cPanel **PHP Selector** veya **MultiPHP Manager**'dan:

```ini
# Önerilen PHP ayarları
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
max_input_vars = 3000
display_errors = Off
log_errors = On
```

### 4. Veritabanı Kurulumu

Veritabanı SQLite olduğu için ek kurulum gerektirmez, ancak:

1. `/database/` klasörünün yazılabilir olduğundan emin olun
2. İlk erişimde otomatik olarak oluşturulacaktır
3. Örnek veri için `database/sample_data.sql` dosyasını kullanabilirsiniz

### 5. Konfigürasyon Ayarları

`includes/config.php` dosyasını düzenleyin:

```php
<?php
// Site URL'ini gerçek domain ile değiştirin
define('SITE_URL', 'https://your-domain.com');

// E-posta ayarları
define('ADMIN_EMAIL', 'admin@your-domain.com');
define('CONTACT_EMAIL', 'info@your-domain.com');

// SMTP ayarları (hosting sağlayıcınızdan alın)
define('SMTP_HOST', 'mail.your-domain.com');
define('SMTP_USERNAME', 'noreply@your-domain.com');
define('SMTP_PASSWORD', 'your-email-password');
define('SMTP_PORT', 587);

// Güvenlik anahtarları (yeni değerler üretin)
define('ENCRYPTION_KEY', 'generate-new-32-character-key');
define('CSRF_SECRET', 'generate-new-csrf-secret-key');
?>
```

### 6. SSL Sertifikası (Let's Encrypt)

cPanel'de SSL/TLS bölümünden:

1. **Let's Encrypt™** seçin
2. Domain adınızı seçin
3. Sertifikayı etkinleştirin
4. Auto-renewal'ı aktifleştirin

### 7. .htaccess Kontrolü

`.htaccess` dosyasının aktif olduğundan emin olun:

```apache
# HTTPS yönlendirmesi için (SSL varsa)
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Domain değiştirme
RewriteCond %{HTTP_HOST} !^www\. [NC]
RewriteRule ^(.*)$ https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## 🔧 Post-Deployment Yapılandırma

### 1. Admin Panel Erişimi

İlk kurulumda varsayılan admin bilgileri:
- **Kullanıcı Adı**: `admin`
- **Şifre**: `admin123`

⚠️ **Güvenlik**: İlk girişte mutlaka şifre değiştirin!

```
https://your-domain.com/admin/
```

### 2. Site Ayarları

Admin panelden ayarlayın:

1. **Genel Ayarlar**
   - Site adı ve açıklama
   - İletişim bilgileri
   - Saat dilimi

2. **Sosyal Medya**
   - YouTube, Twitch, Instagram linkleri
   - WhatsApp numarası

3. **SEO Ayarları**
   - Meta title ve description
   - Google Analytics kodu
   - Robots.txt içeriği

4. **E-posta Ayarları**
   - SMTP konfigürasyonu
   - Test e-postası gönderimi

### 3. İletişim Formu Testi

1. Ana sayfadaki iletişim formunu test edin
2. Admin panelden mesajın geldiğini kontrol edin
3. E-posta bildirimlerinin çalıştığını doğrulayın

### 4. Newsletter Sistemi

1. Newsletter abonelik formunu test edin
2. Doğrulama e-postalarının gönderildiğini kontrol edin
3. Abonelik iptal işlemini test edin

## 🔐 Güvenlik Kontrolleri

### 1. Dosya İzinleri Kontrolü

```bash
# Güvenlik için bu dosyalar public olmamalı
/.env -> 600
/includes/config.php -> 644
/database/ -> Klasör listelenemez olmalı
/admin/ -> İzinsiz erişim engellenmeli
```

### 2. Güvenlik Testleri

1. **SQL Injection**: Form alanlarında SQL kodları test edin
2. **XSS**: JavaScript kodları ile test edin
3. **CSRF**: Cross-site request forgery koruması
4. **Rate Limiting**: Çok fazla istek göndermeyi test edin

### 3. Backup Stratejisi

```php
// Otomatik yedekleme kurulumu
// Admin panel -> Ayarlar -> Bakım -> Veritabanı Yedeği
// Düzenli olarak (haftalık) yedek alın
```

## 📊 Performans Optimizasyonu

### 1. Önbellek Ayarları

```apache
# .htaccess dosyasında browser caching aktif
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 year"
</IfModule>
```

### 2. Görsel Optimizasyonu

1. Görselleri WebP formatına çevirin
2. Lazy loading kullanın
3. Uygun boyutlarda yükleyin

### 3. CDN Kurulumu (Opsiyonel)

Cloudflare gibi CDN servisleri:
1. DNS ayarlarını Cloudflare'e yönlendirin
2. SSL/TLS ayarlarını yapın
3. Caching rules'ları ayarlayın

## 🌐 Domain ve DNS Ayarları

### 1. DNS Kayıtları

```dns
# A Record
@ -> Server IP Address
www -> Server IP Address

# CNAME Records (opsiyonel)
admin -> your-domain.com
api -> your-domain.com

# MX Records (e-posta için)
@ -> mail.your-domain.com (Priority: 10)
```

### 2. Subdomain Kurulumu (Opsiyonel)

```apache
# Admin panel için subdomain
admin.your-domain.com -> public_html/admin/

# API için subdomain
api.your-domain.com -> public_html/api/
```

## 🔍 Monitoring ve Analytics

### 1. Google Analytics

```javascript
// Admin panel -> Ayarlar -> SEO -> Google Analytics ID
// G-XXXXXXXXXX formatında ID girin
```

### 2. Search Console

```html
<!-- Verification meta tag -->
<meta name="google-site-verification" content="your-verification-code">
```

### 3. Log Monitoring

```bash
# cPanel Error Logs'u düzenli kontrol edin
/logs/error_log
/logs/access_log
```

## 🐛 Troubleshooting

### Sık Karşılaşılan Sorunlar

#### 1. 500 Internal Server Error
```bash
# Çözümler:
1. .htaccess dosyasını kontrol edin
2. PHP error log'larını inceleyin
3. Dosya izinlerini kontrol edin
4. PHP versiyon uyumluluğunu kontrol edin
```

#### 2. Database Connection Error
```bash
# Çözümler:
1. /database/ klasörünün yazılabilir olduğunu kontrol edin
2. SQLite extension'ın aktif olduğunu kontrol edin
3. Dosya yollarını kontrol edin
```

#### 3. E-posta Gönderim Sorunları
```bash
# Çözümler:
1. SMTP ayarlarını kontrol edin
2. Hosting sağlayıcısının mail limitlerini kontrol edin
3. SPF ve DKIM kayıtlarını ayarlayın
```

#### 4. Upload Sorunları
```bash
# Çözümler:
1. /uploads/ klasörünün izinlerini 777 yapın
2. PHP upload_max_filesize ayarını kontrol edin
3. Apache LimitRequestBody direktifini kontrol edin
```

## 📞 Support ve Maintenance

### Güncellemeler
- Düzenli olarak güvenlik güncellemeleri kontrol edin
- PHP versiyonunu güncel tutun
- Backup'ları düzenli alın

### Support İletişim
- Hosting desteği için cPanel ticket sistemi
- Geliştirici desteği için proje dokümantasyonu

---

## ✅ Deployment Checklist

- [ ] Tüm dosyalar yüklendi
- [ ] Dosya izinleri ayarlandı
- [ ] PHP ayarları yapıldı
- [ ] SSL sertifikası kuruldu
- [ ] .htaccess kontrol edildi
- [ ] Admin panel erişimi test edildi
- [ ] İletişim formu test edildi
- [ ] Newsletter sistemi test edildi
- [ ] E-posta ayarları yapıldı
- [ ] Google Analytics kuruldu
- [ ] Güvenlik testleri yapıldı
- [ ] Backup stratejisi belirlendi
- [ ] Performans optimizasyonu yapıldı

**Deployment tamamlandı! 🎉**

> Casino portfolio web siteniz artık canlıya alınmıştır. BERAT K'nın profesyonel casino yayıncılığı brandını en iyi şekilde temsil etmek için tüm özellikler aktif ve hazırdır.

---

**Son Güncelleme**: Aralık 2024  
**Geliştirici**: Casino Yayıncısı - BERAT K  
**Versiyon**: 1.0