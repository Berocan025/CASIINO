# 🎰 Casino Portfolio Website

**Geliştirici: BERAT K**

Profesyonel casino yayıncısı portföy sitesi - Modern tasarım, güvenli admin paneli ve kapsamlı içerik yönetim sistemi.

## 📋 Proje Hakkında

Bu proje, casino yayıncılığı ve dijital pazarlama hizmetleri sunan profesyoneller için tasarlanmış tam fonksiyonel bir portföy sitesidir. Koyu tema (#021526) ve casino atmosferi ile modern bir deneyim sunar.

### 🎯 Temel Özellikler

- **Responsive Tasarım**: Mobil, tablet ve desktop uyumlu
- **Güvenli Admin Paneli**: CSRF koruması, rate limiting, session yönetimi
- **SEO Optimizasyonu**: Meta tagları, sitemap, robots.txt
- **Portföy Yönetimi**: Proje showcase ve başarı hikayeleri
- **İletişim Sistemi**: Otomatik mail gönderimi ve form yönetimi
- **Galeri Sistemi**: Fotoğraf ve video galerisi
- **Sosyal Medya Entegrasyonu**: Instagram, YouTube, Telegram, Twitter

## 🛠️ Teknik Özellikler

### Backend
- **PHP 8.0+**: Modern PHP standartları
- **SQLite**: Hafif ve hızlı veritabanı
- **PDO**: Güvenli veritabanı işlemleri
- **MVC Yapısı**: Düzenli kod organizasyonu

### Frontend
- **Bootstrap 5**: Responsive CSS framework
- **Font Awesome**: İkon kütüphanesi
- **JavaScript ES6**: Modern JavaScript
- **CSS3**: Animasyonlar ve geçişler
- **Google Fonts**: Poppins font ailesi

### Güvenlik
- **XSS Koruması**: Input sanitizasyonu
- **CSRF Koruması**: Token tabanlı güvenlik
- **SQL Injection Koruması**: Prepared statements
- **Rate Limiting**: Spam ve brute force koruması
- **Session Güvenliği**: Güvenli oturum yönetimi

## 📁 Dizin Yapısı

```
/
├── admin/                  # Admin paneli
│   ├── includes/          # Admin include dosyaları
│   ├── pages/             # Admin sayfa yönetimi
│   ├── services/          # Hizmet yönetimi
│   ├── portfolio/         # Portföy yönetimi
│   ├── gallery/           # Galeri yönetimi
│   ├── messages/          # Mesaj yönetimi
│   ├── settings/          # Ayarlar
│   └── dashboard.php      # Ana dashboard
├── api/                   # API endpoints
│   ├── contact-form.php   # İletişim formu
│   └── newsletter.php     # Newsletter aboneliği
├── assets/                # Statik dosyalar
│   ├── css/              # CSS dosyaları
│   ├── js/               # JavaScript dosyaları
│   ├── img/              # Resim dosyaları
│   └── fonts/            # Font dosyaları
├── database/              # Veritabanı
│   ├── database.sqlite    # SQLite veritabanı
│   ├── schema.sql         # Veritabanı şeması
│   └── sample-data.sql    # Örnek veriler
├── includes/              # PHP include dosyaları
│   ├── config.php         # Ana konfigürasyon
│   ├── database.php       # Veritabanı sınıfı
│   ├── security.php       # Güvenlik fonksiyonları
│   ├── functions.php      # Yardımcı fonksiyonlar
│   ├── header.php         # Site başlığı
│   └── footer.php         # Site alt bilgisi
├── pages/                 # Site sayfaları
│   ├── about.php          # Hakkımda
│   ├── services.php       # Hizmetler
│   ├── portfolio.php      # Portföy
│   ├── gallery.php        # Galeri
│   └── contact.php        # İletişim
├── uploads/               # Yüklenen dosyalar
│   ├── images/           # Resim dosyaları
│   └── videos/           # Video dosyaları
├── .htaccess             # Apache ayarları
├── index.php             # Ana sayfa
├── robots.txt            # SEO robots
└── sitemap.xml           # Site haritası
```

## 🚀 Kurulum

### Gereksinimler

- PHP 8.0 veya üzeri
- Apache/Nginx web sunucusu
- SQLite3 extension
- PDO extension
- GD extension (resim işlemleri için)
- mod_rewrite (Apache için)

### Kurulum Adımları

1. **Depoyu klonlayın:**
```bash
git clone https://github.com/username/casino-portfolio.git
cd casino-portfolio
```

2. **Veritabanını başlatın:**
```bash
php init_database.php
```

3. **Web sunucusunu başlatın:**
```bash
php -S localhost:8000
```

4. **Tarayıcıda açın:** `http://localhost:8000`

## 🔐 Admin Paneli

Admin paneline erişim için:
- **URL**: `/admin/login.php`
- **Kullanıcı Adı**: `admin`
- **Şifre**: `admin123`

### Admin Özellikleri:
- ✅ **Sayfa Yönetimi**: İçerik sayfalarını düzenle
- ✅ **Hizmet Yönetimi**: Hizmetleri ekle/düzenle/sil (drag & drop sıralama)
- ✅ **Portföy Yönetimi**: Projeleri resim upload ile yönet
- ✅ **Galeri Yönetimi**: Görselleri yönet (drag & drop)
- ✅ **Mesaj Yönetimi**: İletişim formundan gelen mesajları görüntüle
- ✅ **Ayarlar**: Site ayarlarını düzenle
- ✅ **Güvenlik**: CSRF koruması, rate limiting, input sanitization
- ✅ **Responsive**: Mobil uyumlu admin paneli

### Adım Adım Kurulum

1. **Dosyaları Sunucuya Yükleyin**
   ```bash
   # cPanel File Manager veya FTP ile dosyaları public_html dizinine yükleyin
   ```

2. **Dizin İzinlerini Ayarlayın**
   ```bash
   chmod 755 uploads/
   chmod 755 database/
   chmod 644 .htaccess
   ```

3. **Veritabanını İlk Çalıştırma**
   - İlk sayfa ziyaretinde veritabanı otomatik oluşturulacak
   - Örnek veriler otomatik yüklenecek

4. **Admin Hesabı**
   - **Kullanıcı Adı**: admin
   - **Şifre**: admin123
   - **URL**: `yourdomain.com/admin`

5. **Site Ayarlarını Yapın**
   - Admin paneline giriş yapın
   - Ayarlar bölümünden site bilgilerini güncelleyin
   - Logo ve favicon yükleyin

## ⚙️ Konfigürasyon

### Veritabanı Ayarları

`includes/config.php` dosyasında veritabanı yolu:
```php
define('DB_PATH', SITE_ROOT . '/database/database.sqlite');
```

### Mail Ayarları

```php
define('MAIL_FROM', 'noreply@yourdomain.com');
define('MAIL_FROM_NAME', 'Casino Yayıncısı - BERAT K');
```

### Güvenlik Ayarları

```php
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 300); // 5 dakika
define('UPLOAD_MAX_SIZE', 10 * 1024 * 1024); // 10MB
```

## 🎨 Tasarım Özellikleri

### Renk Paleti

- **Ana Renk**: #021526 (Koyu Lacivert)
- **İkincil Renk**: #6f42c1 (Mor)
- **Vurgu Rengi**: #e91e63 (Pembe)
- **Gradyanlar**: Mor/Pembe geçişleri
- **Neon Efektler**: Hover animasyonları

### Responsive Breakpoints

- **Mobile**: < 576px
- **Tablet**: 576px - 991px
- **Desktop**: 992px - 1399px
- **Large Desktop**: > 1400px

## 📊 Admin Panel Özellikleri

### Dashboard

- Site istatistikleri
- Son mesajlar
- Hızlı erişim butonları
- Sistem bilgileri

### İçerik Yönetimi

- **Sayfa Yönetimi**: Tüm sayfa içerikleri
- **Hizmet Yönetimi**: Hizmet ekleme/düzenleme
- **Portföy Yönetimi**: Proje showcase
- **Galeri Yönetimi**: Fotoğraf/video galeri
- **Mesaj Yönetimi**: İletişim formundan gelen mesajlar

### Ayarlar

- Site genel ayarları
- SEO ayarları
- Sosyal medya linkleri
- İletişim bilgileri
- Mail ayarları

## 🔒 Güvenlik Özellikleri

### Input Güvenliği

- XSS koruması (htmlspecialchars)
- SQL Injection koruması (PDO prepared statements)
- CSRF token doğrulaması
- File upload güvenliği

### Session Güvenliği

- HttpOnly cookies
- Secure cookies (HTTPS)
- Session regeneration
- Timeout kontrolü

### Rate Limiting

- Login denemesi sınırlaması
- Form gönderimi sınırlaması
- IP tabanlı engellemeler

## 📈 SEO Özellikleri

### Meta Tags

- Dinamik title ve description
- Open Graph tags
- Twitter Card tags
- Canonical URLs

### Sitemap

- Otomatik sitemap.xml üretimi
- Google Search Console uyumlu
- Sayfa öncelik belirleme

### URL Yapısı

- SEO dostu URLs
- .htaccess yönlendirmeleri
- Clean URLs

## 🎯 Kullanım Senaryoları

### Casino Yayıncısı için

- Canlı yayın programları
- Bonus değerlendirmeleri
- Oyun stratejileri
- Takipçi istatistikleri

### Dijital Pazarlama için

- Sosyal medya başarıları
- Reklam kampanya sonuçları
- Müşteri testimonialları
- İletişim bilgileri

## 🔧 Özelleştirme

### Renk Değiştirme

`assets/css/style.css` dosyasında CSS variables:

```css
:root {
    --primary-color: #021526;
    --secondary-color: #6f42c1;
    --accent-color: #e91e63;
}
```

### Logo Değiştirme

Admin panel > Ayarlar > Genel Ayarlar > Logo yükleme

### İçerik Değiştirme

Admin panel üzerinden tüm içerikler düzenlenebilir.

## 📱 API Endpoints

### Contact Form

```
POST /api/contact-form.php
```

**Parameters:**
- name (required)
- email (required)
- phone (optional)
- subject (required)
- message (required)
- csrf_token (required)

### Newsletter

```
POST /api/newsletter.php
```

**Parameters:**
- email (required)
- csrf_token (required)

## 🐛 Hata Ayıklama

### Debug Mode

`includes/config.php` dosyasında:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Log Dosyaları

- PHP error log: Server error logs
- Security events: Custom security logging
- Activity log: User activity tracking

## 🔄 Güncelleme

### Veritabanı Güncellemeleri

Yeni tablolar için `database/schema.sql` dosyasını güncelleyin.

### CSS/JS Güncellemeleri

Cache busting için dosya versiyonlarını artırın.

## 📞 Destek

### Geliştirici İletişim

- **Ad**: BERAT K
- **E-mail**: info@casinoportfolio.com
- **Web**: Casino Portfolio Website

### Dokümantasyon

Bu README dosyası projenin temel kullanımını kapsar. Detaylı dokümantasyon için admin paneli help bölümünü ziyaret edin.

## 📄 Lisans

Bu proje BERAT K tarafından geliştirilmiş olup, tüm hakları saklıdır.

## 🚀 Performans

### Optimizasyon

- CSS/JS minification
- Image compression
- Lazy loading
- Browser caching
- GZIP compression

### Sayfa Hızı

- Google PageSpeed optimize
- Core Web Vitals uyumlu
- Mobile-first approach

## 🌟 Öne Çıkan Özellikler

- ✅ Tam responsive tasarım
- ✅ Güvenli admin paneli
- ✅ SEO optimize
- ✅ Sosyal medya entegrasyonu
- ✅ İletişim form sistemi
- ✅ Newsletter sistemi (e-posta onaylı)
- ✅ PWA desteği (mobil uygulama deneyimi)
- ✅ Service Worker (offline kullanım)
- ✅ Push notifications
- ✅ Background sync
- ✅ GDPR uyumlu abonelik iptali
- ✅ Galeri yönetimi
- ✅ Portföy showcase
- ✅ Casino teması
- ✅ cPanel uyumlu
- ✅ SQLite veritabanı

## 🔗 **Önemli Linkler**

- **Ana Sayfa:** `index.php`
- **Admin Panel:** `admin/login.php` (admin/admin123)
- **API Endpoints:** `/api/contact-form.php`, `/api/newsletter.php`
- **Newsletter Onay:** `newsletter-confirm.php`
- **Abonelik İptal:** `unsubscribe.php`
- **PWA Manifest:** `manifest.json`
- **Service Worker:** `sw.js`
- **Offline Sayfa:** `offline.html`
- **Veritabanı:** `database/casino_portfolio.db`

## 🎯 **Son Eklenen Özellikler**

### PWA (Progressive Web App)
- 📱 **Mobil Uygulama Deneyimi:** Ana ekrana eklenebilir ikon
- 🔄 **Service Worker:** Otomatik önbellekleme ve offline kullanım
- 📵 **Offline Sayfa:** İnternet olmadan bile temel sayfalara erişim
- 🔔 **Push Notifications:** Canlı yayın ve içerik bildirimleri
- ⚡ **Background Sync:** Çevrimdışı form gönderimlerini senkronize etme

### Newsletter Sistemi
- 📧 **E-posta Onaylı Abonelik:** Güvenli newsletter sistemi
- ✉️ **HTML E-posta Şablonları:** Profesyonel e-posta tasarımları
- 🎯 **GDPR Uyumlu:** Kolay abonelik iptal sistemi
- 📊 **Admin Paneli Entegrasyonu:** Abone yönetimi

### Güvenlik & Performance
- 🛡️ **Rate Limiting:** Form spam koruması
- 🔒 **CSRF Protection:** Cross-site request forgery koruması
- ⚡ **Caching Strategy:** Akıllı önbellekleme sistemi
- 📈 **Performance Monitoring:** Yavaş API çağrı takibi

---

## 🎰 **BERAT K - Casino Yayıncısı için özel tasarlanmıştır**

5+ yıllık casino deneyimi ile profesyonel dijital pazarlama hizmetleri.

**Hizmetlerimiz:**
- 📺 YouTube Yayın Yönetimi
- 💬 Telegram Kanal Yönetimi
- 📱 Sosyal Medya Pazarlaması
- 🎯 Meta Ads Kampanyaları
- 📧 SMS & E-posta Pazarlaması
- ⚙️ Panel Optimizasyonu

**© 2024 Casino Yayıncısı - BERAT K | Tüm hakları saklıdır.**

*Bu proje profesyonel casino yayıncılığı ve dijital pazarlama hizmetleri için geliştirilmiştir.*
