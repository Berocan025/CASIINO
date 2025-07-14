/*!
 * Casino Portfolio Website - Main JavaScript
 * Geliştirici: BERAT K
 * Main JS file with animations, interactions, and form handling
 */

(function() {
    'use strict';

    // Global variables
    let isScrolled = false;
    let lastScrollTop = 0;
    let ticking = false;

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeNavigation();
        initializeHeroSlider();
        initializeAnimations();
        initializeCounters();
        initializeForms();
        initializeModals();
        initializePortfolioFilter();
        initializeGallery();
        initializeScrollEffects();
        initializePreloader();
        
        console.log('🎰 Casino Portfolio Website - JavaScript Initialized');
    });

    // ==========================================
    // NAVIGATION
    // ==========================================
    
    function initializeNavigation() {
        const navbar = document.querySelector('.navbar');
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        const navLinks = document.querySelectorAll('.nav-link');

        // Navbar scroll effect
        function handleNavbarScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 100 && !isScrolled) {
                navbar.classList.add('scrolled');
                isScrolled = true;
            } else if (scrollTop <= 100 && isScrolled) {
                navbar.classList.remove('scrolled');
                isScrolled = false;
            }
        }

        // Smooth scroll for navigation links
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                if (href.startsWith('#')) {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    
                    if (target) {
                        const headerOffset = 100;
                        const elementPosition = target.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });

                        // Close mobile menu if open
                        if (navbarCollapse.classList.contains('show')) {
                            navbarToggler.click();
                        }

                        // Update active state
                        updateActiveNavLink(href);
                    }
                }
            });
        });

        // Active navigation link based on scroll position
        function updateActiveNavLink(targetId = null) {
            if (targetId) {
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === targetId) {
                        link.classList.add('active');
                    }
                });
                return;
            }

            const sections = document.querySelectorAll('section[id]');
            const scrollPos = window.pageYOffset + 150;

            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');
                
                if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        if (link.getAttribute('href') === `#${sectionId}`) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        }

        // Throttled scroll event
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(() => {
                    handleNavbarScroll();
                    updateActiveNavLink();
                    ticking = false;
                });
                ticking = true;
            }
        });

        // Mobile menu auto-close on outside click
        document.addEventListener('click', function(e) {
            if (!navbar.contains(e.target) && navbarCollapse.classList.contains('show')) {
                navbarToggler.click();
            }
        });
    }

    // ==========================================
    // HERO SLIDER
    // ==========================================
    
    function initializeHeroSlider() {
        const sliders = document.querySelectorAll('.hero-slider');
        
        sliders.forEach(slider => {
            // Initialize Bootstrap carousel if present
            if (window.bootstrap && slider.classList.contains('carousel')) {
                new bootstrap.Carousel(slider, {
                    interval: 5000,
                    ride: 'carousel',
                    pause: 'hover'
                });
            }
        });

        // Custom hero animations
        const heroElements = document.querySelectorAll('.hero-title, .hero-description, .hero-buttons');
        heroElements.forEach((element, index) => {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                element.style.transition = 'all 0.8s ease';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, index * 200 + 500);
        });
    }

    // ==========================================
    // ANIMATIONS
    // ==========================================
    
    function initializeAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    element.classList.add('animate-in');
                    
                    // Trigger specific animations
                    if (element.classList.contains('counter')) {
                        animateCounter(element);
                    }
                    
                    if (element.classList.contains('progress-bar')) {
                        animateProgressBar(element);
                    }
                    
                    observer.unobserve(element);
                }
            });
        }, observerOptions);

        // Observe elements for animation
        const animateElements = document.querySelectorAll([
            '.card', '.service-card', '.portfolio-card', '.gallery-item',
            '.stat-card', '.testimonial-card', '.contact-card',
            '.section-title', '.section-description'
        ].join(', '));

        animateElements.forEach(element => {
            element.classList.add('animate-element');
            observer.observe(element);
        });

        // Parallax effect for hero background
        const heroSection = document.querySelector('.hero-section');
        if (heroSection) {
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const rate = scrolled * -0.5;
                
                if (scrolled < window.innerHeight) {
                    heroSection.style.transform = `translateY(${rate}px)`;
                }
            });
        }
    }

    // ==========================================
    // COUNTERS
    // ==========================================
    
    function initializeCounters() {
        const counters = document.querySelectorAll('[data-count]');
        
        counters.forEach(counter => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            
            observer.observe(counter);
        });
    }

    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = parseInt(element.getAttribute('data-duration')) || 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            
            // Format number based on content
            const text = element.textContent;
            if (text.includes('K')) {
                element.textContent = Math.floor(current / 1000) + 'K+';
            } else if (text.includes('M')) {
                element.textContent = Math.floor(current / 1000000) + 'M+';
            } else if (text.includes('%')) {
                element.textContent = Math.floor(current) + '%';
            } else {
                element.textContent = Math.floor(current).toLocaleString('tr-TR');
            }
        }, 16);
    }

    function animateProgressBar(element) {
        const percentage = element.getAttribute('data-percentage') || 0;
        element.style.width = '0%';
        
        setTimeout(() => {
            element.style.transition = 'width 1.5s ease-in-out';
            element.style.width = percentage + '%';
        }, 200);
    }

    // ==========================================
    // FORMS
    // ==========================================
    
    function initializeForms() {
        const contactForm = document.getElementById('contactForm');
        const newsletterForm = document.getElementById('newsletterForm');
        
        if (contactForm) {
            initializeContactForm(contactForm);
        }
        
        if (newsletterForm) {
            initializeNewsletterForm(newsletterForm);
        }

        // Form field animations
        const formControls = document.querySelectorAll('.form-control');
        formControls.forEach(control => {
            control.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            control.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    }

    function initializeContactForm(form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Gönderiliyor...';
            
            // Clear previous errors
            clearFormErrors(form);
            
            try {
                const formData = new FormData(form);
                const data = Object.fromEntries(formData);
                
                const response = await fetch('api/contact-form.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', result.message);
                    form.reset();
                    
                    // Optional: redirect or show success page
                    setTimeout(() => {
                        // You can redirect here if needed
                    }, 2000);
                } else {
                    if (result.errors) {
                        showFormErrors(form, result.errors);
                    } else {
                        showAlert('danger', result.message);
                    }
                }
            } catch (error) {
                console.error('Form submission error:', error);
                showAlert('danger', 'Bir hata oluştu. Lütfen tekrar deneyin.');
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    function initializeNewsletterForm(form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = form.querySelector('input[type="email"]').value;
            const submitBtn = form.querySelector('button[type="submit"]');
            
            if (!validateEmail(email)) {
                showAlert('warning', 'Geçerli bir e-posta adresi giriniz.');
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            try {
                const response = await fetch('api/newsletter.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ email })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showAlert('success', 'Newsletter aboneliğiniz başarıyla oluşturuldu!');
                    form.reset();
                } else {
                    showAlert('danger', result.message);
                }
            } catch (error) {
                showAlert('danger', 'Bir hata oluştu. Lütfen tekrar deneyin.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
            }
        });
    }

    function showFormErrors(form, errors) {
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                
                // Create or update error message
                let errorDiv = input.parentElement.querySelector('.invalid-feedback');
                if (!errorDiv) {
                    errorDiv = document.createElement('div');
                    errorDiv.className = 'invalid-feedback';
                    input.parentElement.appendChild(errorDiv);
                }
                errorDiv.textContent = errors[field];
            }
        });
    }

    function clearFormErrors(form) {
        const invalidInputs = form.querySelectorAll('.is-invalid');
        const errorDivs = form.querySelectorAll('.invalid-feedback');
        
        invalidInputs.forEach(input => input.classList.remove('is-invalid'));
        errorDivs.forEach(div => div.remove());
    }

    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // ==========================================
    // MODALS
    // ==========================================
    
    function initializeModals() {
        // Portfolio modal
        const portfolioModal = document.getElementById('portfolioModal');
        const portfolioButtons = document.querySelectorAll('[data-bs-toggle="modal"][data-bs-target="#portfolioModal"]');
        
        portfolioButtons.forEach(button => {
            button.addEventListener('click', function() {
                const title = this.getAttribute('data-title');
                const description = this.getAttribute('data-description');
                const image = this.getAttribute('data-image');
                const category = this.getAttribute('data-category');
                const client = this.getAttribute('data-client');
                const date = this.getAttribute('data-date');
                const url = this.getAttribute('data-url');
                
                if (portfolioModal) {
                    portfolioModal.querySelector('.modal-title').textContent = title;
                    portfolioModal.querySelector('.modal-body img').src = image;
                    portfolioModal.querySelector('.modal-body .description').textContent = description;
                    // Add more modal content updates as needed
                }
            });
        });

        // Gallery lightbox
        const galleryItems = document.querySelectorAll('.gallery-item img');
        galleryItems.forEach(img => {
            img.addEventListener('click', function() {
                openLightbox(this.src, this.alt);
            });
        });
    }

    function openLightbox(src, alt) {
        const lightbox = document.createElement('div');
        lightbox.className = 'lightbox';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <img src="${src}" alt="${alt}">
                <button class="lightbox-close">&times;</button>
            </div>
        `;
        
        document.body.appendChild(lightbox);
        document.body.style.overflow = 'hidden';
        
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox || e.target.classList.contains('lightbox-close')) {
                closeLightbox(lightbox);
            }
        });
        
        // ESC key to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLightbox(lightbox);
            }
        });
    }

    function closeLightbox(lightbox) {
        lightbox.remove();
        document.body.style.overflow = '';
    }

    // ==========================================
    // PORTFOLIO FILTER
    // ==========================================
    
    function initializePortfolioFilter() {
        const filterButtons = document.querySelectorAll('.portfolio-filter .btn, .filter-btn');
        const portfolioItems = document.querySelectorAll('.portfolio-item, .gallery-item');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Update active button
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Filter items
                portfolioItems.forEach(item => {
                    if (filter === '*' || item.classList.contains(filter.substring(1))) {
                        item.style.display = 'block';
                        item.classList.add('animate-in');
                    } else {
                        item.style.display = 'none';
                        item.classList.remove('animate-in');
                    }
                });
            });
        });
    }

    // ==========================================
    // GALLERY
    // ==========================================
    
    function initializeGallery() {
        // Video play buttons
        const playButtons = document.querySelectorAll('.play-button');
        playButtons.forEach(button => {
            button.addEventListener('click', function() {
                const videoUrl = this.getAttribute('data-video-url');
                if (videoUrl) {
                    openVideoModal(videoUrl);
                }
            });
        });

        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            observer.unobserve(img);
                        }
                    }
                });
            });

            const lazyImages = document.querySelectorAll('img[data-src]');
            lazyImages.forEach(img => imageObserver.observe(img));
        }
    }

    function openVideoModal(videoUrl) {
        const modal = document.createElement('div');
        modal.className = 'video-modal';
        modal.innerHTML = `
            <div class="video-modal-content">
                <div class="video-wrapper">
                    <video controls autoplay>
                        <source src="${videoUrl}" type="video/mp4">
                        Tarayıcınız video oynatmayı desteklemiyor.
                    </video>
                </div>
                <button class="video-modal-close">&times;</button>
            </div>
        `;
        
        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal || e.target.classList.contains('video-modal-close')) {
                closeVideoModal(modal);
            }
        });
    }

    function closeVideoModal(modal) {
        const video = modal.querySelector('video');
        if (video) {
            video.pause();
        }
        modal.remove();
        document.body.style.overflow = '';
    }

    // ==========================================
    // SCROLL EFFECTS
    // ==========================================
    
    function initializeScrollEffects() {
        // Back to top button
        const backToTopBtn = createBackToTopButton();
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 500) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });

        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Reading progress bar
        createReadingProgressBar();
    }

    function createBackToTopButton() {
        const button = document.createElement('button');
        button.className = 'back-to-top';
        button.innerHTML = '<i class="fas fa-arrow-up"></i>';
        button.setAttribute('aria-label', 'Yukarı çık');
        
        document.body.appendChild(button);
        return button;
    }

    function createReadingProgressBar() {
        const progressBar = document.createElement('div');
        progressBar.className = 'reading-progress';
        document.body.appendChild(progressBar);
        
        window.addEventListener('scroll', () => {
            const scrollTop = window.pageYOffset;
            const documentHeight = document.documentElement.scrollHeight - window.innerHeight;
            const progress = (scrollTop / documentHeight) * 100;
            
            progressBar.style.width = progress + '%';
        });
    }

    // ==========================================
    // PRELOADER
    // ==========================================
    
    function initializePreloader() {
        const preloader = document.querySelector('.preloader');
        
        if (preloader) {
            window.addEventListener('load', () => {
                preloader.style.opacity = '0';
                setTimeout(() => {
                    preloader.style.display = 'none';
                }, 500);
            });
        }
    }

    // ==========================================
    // UTILITY FUNCTIONS
    // ==========================================
    
    function showAlert(type, message, duration = 5000) {
        const alertContainer = getOrCreateAlertContainer();
        
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        alertContainer.appendChild(alert);
        
        // Auto dismiss
        setTimeout(() => {
            if (alert.parentElement) {
                alert.remove();
            }
        }, duration);
        
        return alert;
    }

    function getOrCreateAlertContainer() {
        let container = document.getElementById('alert-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'alert-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        return container;
    }

    function debounce(func, wait, immediate) {
        let timeout;
        return function executedFunction() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // ==========================================
    // SOCIAL MEDIA INTEGRATION
    // ==========================================
    
    function initializeSocialFeatures() {
        // Social share buttons
        const shareButtons = document.querySelectorAll('.social-share');
        shareButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const platform = this.getAttribute('data-platform');
                const url = encodeURIComponent(window.location.href);
                const title = encodeURIComponent(document.title);
                
                let shareUrl = '';
                switch(platform) {
                    case 'facebook':
                        shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                        break;
                    case 'twitter':
                        shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                        break;
                    case 'linkedin':
                        shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
                        break;
                    case 'whatsapp':
                        shareUrl = `https://wa.me/?text=${title} ${url}`;
                        break;
                }
                
                if (shareUrl) {
                    window.open(shareUrl, '_blank', 'width=600,height=400');
                }
            });
        });
    }

    // ==========================================
    // PERFORMANCE MONITORING
    // ==========================================
    
    function initializePerformanceMonitoring() {
        // Page load time
        window.addEventListener('load', () => {
            const loadTime = performance.now();
            console.log(`Page loaded in ${Math.round(loadTime)}ms`);
            
            // Send to analytics if needed
            if (typeof gtag !== 'undefined') {
                gtag('event', 'timing_complete', {
                    'name': 'load',
                    'value': Math.round(loadTime)
                });
            }
        });
    }

    // ==========================================
    // ACCESSIBILITY
    // ==========================================
    
    function initializeAccessibility() {
        // Skip link
        const skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.className = 'skip-link';
        skipLink.textContent = 'Ana içeriğe geç';
        document.body.insertBefore(skipLink, document.body.firstChild);

        // Focus management for modals
        document.addEventListener('shown.bs.modal', function(e) {
            const modal = e.target;
            const focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            if (focusableElements.length) {
                focusableElements[0].focus();
            }
        });

        // Keyboard navigation for custom elements
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                if (e.target.classList.contains('clickable')) {
                    e.preventDefault();
                    e.target.click();
                }
            }
        });
    }

    // Initialize additional features
    document.addEventListener('DOMContentLoaded', function() {
        initializeSocialFeatures();
        initializePerformanceMonitoring();
        initializeAccessibility();
    });

    // ==========================================
    // EXPORT FOR GLOBAL ACCESS
    // ==========================================
    
    window.CasinoPortfolio = {
        showAlert,
        animateCounter,
        openLightbox,
        closeLightbox,
        debounce,
        throttle
    };

})();