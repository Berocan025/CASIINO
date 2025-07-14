-- Sample Data for Casino Portfolio Website
-- Geliştirici: BERAT K

-- Insert default admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role, status) VALUES 
('admin', 'admin@casinoportfolio.com', '$2y$12$H3t7Ot1yOAx7o4/7mMszSuC97EE1QgZxVUQ7NCsBE1x8WW234i/zi', 'BERAT K', 'admin', 'active');

-- Insert default pages
INSERT INTO pages (title, slug, content, meta_title, meta_description, status) VALUES 
('Ana Sayfa', 'home', '<h1>Casino Yayıncısı Portföy Sitesi</h1><p>Profesyonel casino yayıncılığı ve pazarlama hizmetleri.</p>', 'Casino Yayıncısı - BERAT K', 'Profesyonel casino yayıncılığı, sosyal medya yönetimi ve pazarlama hizmetleri', 'active'),
('Hakkımda', 'about', '<h1>Hakkımda</h1><p>Deneyimli casino yayıncısı ve dijital pazarlama uzmanı BERAT K.</p>', 'Hakkımda - BERAT K', 'Casino yayıncısı BERAT K hakkında bilgiler ve deneyimleri', 'active'),
('Hizmetler', 'services', '<h1>Hizmetlerim</h1><p>Profesyonel casino yayıncılığı ve pazarlama hizmetleri.</p>', 'Hizmetler - Casino Yayıncısı', 'YouTube canlı yayın, Telegram yönetimi, sosyal medya ve pazarlama hizmetleri', 'active'),
('Portföy', 'portfolio', '<h1>Portföyüm</h1><p>Başarıyla tamamladığım projeler ve aktif kampanyalar.</p>', 'Portföy - Başarılı Projeler', 'Casino yayıncılığı ve pazarlama projelerinin portföyü', 'active'),
('Galeri', 'gallery', '<h1>Galeri</h1><p>Canlı yayın görüntüleri, ekran görüntüleri ve proje fotoğrafları.</p>', 'Galeri - Fotoğraf ve Videolar', 'Casino yayınları ve projelerden fotoğraf ve video galerisi', 'active'),
('İletişim', 'contact', '<h1>İletişim</h1><p>Profesyonel casino yayıncılığı hizmetleri için benimle iletişime geçin.</p>', 'İletişim - BERAT K', 'Casino yayıncısı BERAT K ile iletişim bilgileri ve form', 'active');

-- Insert sample services
INSERT INTO services (title, slug, short_description, description, price, category, features, status) VALUES 
('YouTube Canlı Yayınları', 'youtube-canli-yayin', 'Haftada 3 profesyonel canlı yayın hizmeti', '<p>Profesyonel ekipman ve deneyimle haftada 3 adet kaliteli canlı yayın gerçekleştiriyorum.</p><p>Yayın içerikleri: Casino oyunları, bonus değerlendirmeleri, strateji paylaşımları.</p>', '₺2,500/Hafta', 'Yayıncılık', '["HD kalite", "Profesyonel ses", "Etkileşimli yayın", "Düzenli program"]', 'active'),
('Telegram Kanal Yönetimi', 'telegram-kanal-yonetimi', 'Kitle manipülasyonu ve kanal büyütme', '<p>Telegram kanalınızı profesyonelce yönetir, organik büyüme sağlarım.</p><p>Günlük aktif paylaşım, follower artırma, engagement yükseltme.</p>', '₺1,800/Ay', 'Sosyal Medya', '["Günlük içerik", "Organik büyüme", "Engagement artışı", "Analytics raporu"]', 'active'),
('Sosyal Medya Yönetimi', 'sosyal-medya-yonetimi', 'Instagram, Facebook ve TikTok yönetimi', '<p>Tüm sosyal medya hesaplarınızı profesyonelce yönetir, görsel ve yazılı içerikler üretirim.</p>', '₺3,000/Ay', 'Sosyal Medya', '["Günlük post", "Story yönetimi", "Hashtag optimizasyonu", "Follower analizi"]', 'active'),
('Meta Reklamları', 'meta-reklamlari', 'Facebook ve Instagram reklam kampanyaları', '<p>Hedef kitle analizleri ile etkili reklam kampanyaları oluşturup yönetirim.</p>', '₺2,000 + %15 reklam bütçesi', 'Reklam', '["Hedef kitle analizi", "Kreatif tasarım", "A/B test", "ROI optimizasyonu"]', 'active'),
('SMS ve Mail Kampanyaları', 'sms-mail-kampanyalari', 'Toplu SMS ve e-mail pazarlama', '<p>Etkili SMS ve e-mail kampanyaları ile müşteri geri dönüşümü sağlarım.</p>', '₺1,200/Kampanya', 'Pazarlama', '["Toplu gönderim", "Kişiselleştirme", "Açılma oranı raporu", "Click tracking"]', 'active'),
('Panel Güçlendirme', 'panel-guclendirme', 'Casino panel optimizasyonu ve güçlendirme', '<p>Casino panellerinizi SEO ve kullanıcı deneyimi açısından optimize ederim.</p>', '₺5,000', 'Optimizasyon', '["SEO optimizasyonu", "UX iyileştirme", "Hız optimizasyonu", "Güvenlik artırma"]', 'active');

-- Insert sample portfolio items
INSERT INTO portfolio (title, slug, description, client_name, category, project_url, completion_date, status, featured) VALUES 
('Bonus Boss Casino Projesi', 'bonus-boss-casino', 'Tamamen organik büyüme ile 50K+ follower kazandığımız başarılı proje.', 'Bonus Boss', 'Sosyal Medya', 'https://bonusboss8.com', '2024-01-15', 'active', 1),
('Pablo Casino Telegram Yönetimi', 'pablo-casino-telegram', '3 ayda 25K+ üye kazandırdığımız Telegram kanal yönetimi projesi.', 'Pablo Casino', 'Telegram', '#', '2024-02-20', 'active', 1),
('VIP Casino Meta Reklamları', 'vip-casino-meta', 'Aylık 100K+ reach ile başarılı Meta reklam kampanyası yönetimi.', 'VIP Casino', 'Reklam', '#', '2024-03-10', 'active', 0),
('Golden Bet YouTube Kanalı', 'golden-bet-youtube', 'Sıfırdan 15K+ subscriber kazandırdığımız YouTube kanal büyütme projesi.', 'Golden Bet', 'YouTube', '#', '2024-01-30', 'active', 1);

-- Insert sample gallery items
INSERT INTO gallery (title, description, type, file_path, category, tags, status) VALUES 
('Canlı Yayın - Bonus Değerlendirmesi', 'Sweet Bonanza bonus değerlendirme yayını', 'image', 'assets/img/gallery/live1.jpg', 'Canlı Yayın', '["canlı yayın", "bonus", "sweet bonanza"]', 'active'),
('Telegram Kanal Ekran Görüntüsü', 'Başarılı Telegram kanal yönetimi örneği', 'image', 'assets/img/gallery/telegram1.jpg', 'Sosyal Medya', '["telegram", "kanal", "yönetim"]', 'active'),
('Instagram Post Örneği', 'Viral olan Instagram paylaşımı', 'image', 'assets/img/gallery/insta1.jpg', 'Sosyal Medya', '["instagram", "post", "viral"]', 'active'),
('YouTube Yayın Kayıtları', 'Popüler YouTube yayın kayıtları', 'video', 'https://youtube.com/watch?v=example', 'YouTube', '["youtube", "yayın", "kayıt"]', 'active');

-- Insert site settings
INSERT INTO settings (setting_key, setting_value, setting_type, description) VALUES 
('site_title', 'Casino Yayıncısı - BERAT K', 'text', 'Site başlığı'),
('site_description', 'Profesyonel casino yayıncılığı ve dijital pazarlama hizmetleri', 'text', 'Site açıklaması'),
('site_keywords', 'casino, yayıncı, pazarlama, sosyal medya, telegram, youtube', 'text', 'Site anahtar kelimeleri'),
('contact_email', 'info@casinoportfolio.com', 'email', 'İletişim e-postası'),
('contact_phone', '+90 555 123 45 67', 'text', 'İletişim telefonu'),
('contact_address', 'İstanbul, Türkiye', 'text', 'Adres bilgisi'),
('social_instagram', 'https://instagram.com/beratk_casino', 'url', 'Instagram adresi'),
('social_telegram', 'https://t.me/beratk_casino', 'url', 'Telegram adresi'),
('social_youtube', 'https://youtube.com/c/beratkasino', 'url', 'YouTube adresi'),
('social_twitter', 'https://twitter.com/beratk_casino', 'url', 'Twitter adresi'),
('logo_url', 'assets/img/logo.png', 'text', 'Logo dosya yolu'),
('favicon_url', 'assets/img/favicon.ico', 'text', 'Favicon dosya yolu'),
('theme_color', '#021526', 'color', 'Ana tema rengi'),
('analytics_code', '', 'textarea', 'Google Analytics kodu'),
('footer_text', '© 2024 Casino Yayıncısı - BERAT K. Tüm hakları saklıdır.', 'text', 'Footer metni');

-- Insert menu items
INSERT INTO menu_items (title, url, page_id, sort_order, status) VALUES 
('Ana Sayfa', '/', 1, 1, 'active'),
('Hakkımda', '/pages/about.php', 2, 2, 'active'),
('Hizmetler', '/pages/services.php', 3, 3, 'active'),
('Portföy', '/pages/portfolio.php', 4, 4, 'active'),
('Galeri', '/pages/gallery.php', 5, 5, 'active'),
('İletişim', '/pages/contact.php', 6, 6, 'active');

-- Insert slider items
INSERT INTO slider (title, subtitle, description, image, button_text, button_url, sort_order, status) VALUES 
('Profesyonel Casino Yayıncısı', 'BERAT K', 'Deneyimli casino yayıncısı ile işinizi bir üst seviyeye taşıyın. YouTube, Telegram ve sosyal medya uzmanlığı.', 'assets/img/slider/slide1.jpg', 'Hizmetleri İncele', '/pages/services.php', 1, 'active'),
('Organik Büyüme Garantisi', 'Kaliteli İçerik Üretimi', '50K+ follower kazandırdığımız projeler ile kanıtlanmış başarı. Organik ve gerçek kitle büyütme hizmetleri.', 'assets/img/slider/slide2.jpg', 'Portföyü Gör', '/pages/portfolio.php', 2, 'active'),
('7/24 Profesyonel Destek', 'Kesintisiz Hizmet', 'Haftada 3 canlı yayın, günlük sosyal medya yönetimi ve sürekli müşteri desteği ile yanınızdayım.', 'assets/img/slider/slide3.jpg', 'İletişime Geç', '/pages/contact.php', 3, 'active');

-- Insert statistics
INSERT INTO statistics (title, value, icon, description, sort_order, status) VALUES 
('Toplam Follower', '250K+', 'fas fa-users', 'Kazandırdığımız toplam follower sayısı', 1, 'active'),
('Tamamlanan Proje', '50+', 'fas fa-project-diagram', 'Başarıyla tamamlanan proje sayısı', 2, 'active'),
('Canlı Yayın Saati', '1,200+', 'fas fa-video', 'Gerçekleştirilen toplam yayın saati', 3, 'active'),
('Müşteri Memnuniyeti', '%98', 'fas fa-heart', 'Müşteri memnuniyet oranı', 4, 'active');