    </main>
    
    <!-- Footer Section -->
    <footer class="footer">
        <div class="container">
            <!-- Main Footer Content -->
            <div class="row">
                <!-- About Section -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-section">
                        <h4 class="footer-title">
                            <?php $logo_url = get_setting('logo_url', ''); ?>
                            <?php if (!empty($logo_url)): ?>
                                <img src="<?php echo asset_url($logo_url); ?>" alt="<?php echo get_setting('site_title', SITE_NAME); ?>" class="footer-logo">
                            <?php else: ?>
                                BERAT K
                            <?php endif; ?>
                        </h4>
                        <p class="footer-description">
                            Profesyonel casino yayıncısı ve dijital pazarlama uzmanı. 
                            YouTube canlı yayınları, Telegram kanal yönetimi, sosyal medya 
                            pazarlaması ve Meta reklamları konularında uzman hizmet.
                        </p>
                        <div class="social-links-footer">
                            <?php $social_instagram = get_setting('social_instagram', ''); ?>
                            <?php if (!empty($social_instagram)): ?>
                                <a href="<?php echo $social_instagram; ?>" target="_blank" rel="noopener" class="social-link">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php $social_telegram = get_setting('social_telegram', ''); ?>
                            <?php if (!empty($social_telegram)): ?>
                                <a href="<?php echo $social_telegram; ?>" target="_blank" rel="noopener" class="social-link">
                                    <i class="fab fa-telegram"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php $social_youtube = get_setting('social_youtube', ''); ?>
                            <?php if (!empty($social_youtube)): ?>
                                <a href="<?php echo $social_youtube; ?>" target="_blank" rel="noopener" class="social-link">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php $social_twitter = get_setting('social_twitter', ''); ?>
                            <?php if (!empty($social_twitter)): ?>
                                <a href="<?php echo $social_twitter; ?>" target="_blank" rel="noopener" class="social-link">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <div class="footer-section">
                        <h5 class="footer-title">Hızlı Linkler</h5>
                        <ul class="footer-links">
                            <?php 
                            $menu_items = get_menu_items(0);
                            foreach ($menu_items as $item): 
                            ?>
                                <li>
                                    <a href="<?php echo site_url($item['url']); ?>">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Services -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-section">
                        <h5 class="footer-title">Hizmetlerim</h5>
                        <ul class="footer-links">
                            <li><a href="<?php echo site_url('pages/services.php'); ?>">YouTube Canlı Yayınları</a></li>
                            <li><a href="<?php echo site_url('pages/services.php'); ?>">Telegram Kanal Yönetimi</a></li>
                            <li><a href="<?php echo site_url('pages/services.php'); ?>">Sosyal Medya Yönetimi</a></li>
                            <li><a href="<?php echo site_url('pages/services.php'); ?>">Meta Reklamları</a></li>
                            <li><a href="<?php echo site_url('pages/services.php'); ?>">SMS ve Mail Kampanyaları</a></li>
                            <li><a href="<?php echo site_url('pages/services.php'); ?>">Panel Güçlendirme</a></li>
                        </ul>
                    </div>
                </div>
                
                <!-- Contact Info -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="footer-section">
                        <h5 class="footer-title">İletişim</h5>
                        <div class="contact-info-footer">
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo get_setting('contact_email', 'info@casinoportfolio.com'); ?></span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo get_setting('contact_phone', '+90 555 123 45 67'); ?></span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?php echo get_setting('contact_address', 'İstanbul, Türkiye'); ?></span>
                            </div>
                        </div>
                        
                        <!-- Newsletter -->
                        <div class="newsletter mt-3">
                            <h6>Haberdar Olun</h6>
                            <form class="newsletter-form" action="<?php echo site_url('api/newsletter.php'); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="input-group">
                                    <input type="email" class="form-control" name="email" placeholder="E-mail adresiniz" required>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Section -->
            <div class="footer-stats">
                <div class="row">
                    <?php 
                    global $db;
                    $stats = $db->fetchAll("SELECT * FROM statistics WHERE status = 'active' ORDER BY sort_order ASC LIMIT 4");
                    foreach ($stats as $stat): 
                    ?>
                        <div class="col-6 col-md-3 mb-3">
                            <div class="stat-item text-center">
                                <div class="stat-icon">
                                    <i class="<?php echo $stat['icon']; ?>"></i>
                                </div>
                                <div class="stat-number"><?php echo $stat['value']; ?></div>
                                <div class="stat-label"><?php echo $stat['title']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <hr class="footer-divider">
            
            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="copyright mb-0">
                            <?php echo get_setting('footer_text', '© 2024 Casino Yayıncısı - BERAT K. Tüm hakları saklıdır.'); ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="footer-links-bottom text-md-end">
                            <a href="<?php echo site_url('pages/privacy.php'); ?>">Gizlilik Politikası</a>
                            <a href="<?php echo site_url('pages/terms.php'); ?>">Kullanım Şartları</a>
                            <a href="<?php echo site_url('pages/contact.php'); ?>">İletişim</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Developer Credit -->
        <div class="developer-credit">
            <div class="container">
                <div class="text-center">
                    <small>Geliştirici: <strong>BERAT K</strong> | Casino Portfolio Website v<?php echo SITE_VERSION; ?></small>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Scroll to Top Button -->
    <button type="button" class="btn-scroll-top" id="btnScrollTop">
        <i class="fas fa-chevron-up"></i>
    </button>
    
    <!-- WhatsApp Float Button -->
    <div class="whatsapp-float">
        <a href="https://wa.me/<?php echo str_replace(['+', ' ', '(', ')', '-'], '', get_setting('contact_phone', '')); ?>" 
           target="_blank" rel="noopener" class="whatsapp-btn">
            <i class="fab fa-whatsapp"></i>
        </a>
    </div>
    
    <!-- JavaScript Files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="<?php echo asset_url('js/main.js'); ?>"></script>
    
    <!-- Additional JavaScript -->
    <?php if (isset($additional_js)): ?>
        <?php echo $additional_js; ?>
    <?php endif; ?>
    
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Person",
        "name": "BERAT K",
        "jobTitle": "Casino Yayıncısı & Dijital Pazarlama Uzmanı",
        "description": "Profesyonel casino yayıncısı ve dijital pazarlama uzmanı",
        "url": "<?php echo site_url(); ?>",
        "image": "<?php echo asset_url('img/og-image.jpg'); ?>",
        "sameAs": [
            "<?php echo get_setting('social_instagram', ''); ?>",
            "<?php echo get_setting('social_telegram', ''); ?>",
            "<?php echo get_setting('social_youtube', ''); ?>",
            "<?php echo get_setting('social_twitter', ''); ?>"
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "<?php echo get_setting('contact_phone', ''); ?>",
            "contactType": "customer service",
            "availableLanguage": "Turkish"
        }
    }
    </script>
    
</body>
</html><?php
/**
 * Footer Template
 * Geliştirici: BERAT K
 * End of page template for the casino portfolio website
 */
?>