/**
 * Casino Portfolio Website - Main JavaScript
 * Geliştirici: BERAT K
 * Main JavaScript file for interactive features and animations
 */

(function($) {
    'use strict';

    // Global variables
    let isLoading = true;
    let scrollPosition = 0;

    // Document ready
    $(document).ready(function() {
        initializeWebsite();
    });

    // Window load
    $(window).on('load', function() {
        hideLoadingSpinner();
        initializeAnimations();
    });

    /**
     * Initialize website functionality
     */
    function initializeWebsite() {
        // Initialize components
        initializeNavigation();
        initializeScrollToTop();
        initializeScrollEffects();
        initializeCounters();
        initializeLightbox();
        initializeContactForm();
        initializeNewsletterForm();
        initializeLazyLoading();
        initializeSmoothScrolling();
        
        // Set up event listeners
        setupEventListeners();
        
        console.log('Casino Portfolio Website initialized - Geliştirici: BERAT K');
    }

    /**
     * Hide loading spinner
     */
    function hideLoadingSpinner() {
        const spinner = document.getElementById('loading-spinner');
        if (spinner) {
            spinner.style.opacity = '0';
            setTimeout(() => {
                spinner.style.display = 'none';
                isLoading = false;
            }, 500);
        }
    }

    /**
     * Initialize navigation functionality
     */
    function initializeNavigation() {
        // Header scroll effect
        $(window).scroll(function() {
            const header = $('.header');
            if ($(this).scrollTop() > 100) {
                header.addClass('scrolled');
            } else {
                header.removeClass('scrolled');
            }
        });

        // Mobile menu close on link click
        $('.navbar-nav .nav-link').on('click', function() {
            if ($(window).width() < 992) {
                $('.navbar-collapse').collapse('hide');
            }
        });

        // Active menu item highlighting
        updateActiveMenuItem();
        $(window).scroll(updateActiveMenuItem);
    }

    /**
     * Update active menu item based on scroll position
     */
    function updateActiveMenuItem() {
        const scrollPos = $(window).scrollTop() + 100;
        
        $('.navbar-nav .nav-link').each(function() {
            const link = $(this);
            const href = link.attr('href');
            
            if (href && href.startsWith('#')) {
                const section = $(href);
                if (section.length && 
                    section.offset().top <= scrollPos && 
                    section.offset().top + section.outerHeight() > scrollPos) {
                    $('.navbar-nav .nav-link').removeClass('active');
                    link.addClass('active');
                }
            }
        });
    }

    /**
     * Initialize scroll to top button
     */
    function initializeScrollToTop() {
        const scrollTopBtn = $('#btnScrollTop');
        
        // Show/hide button based on scroll position
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                scrollTopBtn.fadeIn();
            } else {
                scrollTopBtn.fadeOut();
            }
        });

        // Scroll to top functionality
        scrollTopBtn.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 800, 'easeInOutQuart');
        });
    }

    /**
     * Initialize scroll effects and parallax
     */
    function initializeScrollEffects() {
        // Parallax effect for hero section
        $(window).scroll(function() {
            const scrolled = $(this).scrollTop();
            const parallaxElements = $('.hero-bg');
            
            parallaxElements.each(function() {
                const speed = 0.5;
                const yPos = -(scrolled * speed);
                $(this).css('transform', `translateY(${yPos}px)`);
            });
        });

        // Reveal animations on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe elements for animation
        document.querySelectorAll('.service-card, .portfolio-card, .stat-card, .gallery-item').forEach(el => {
            observer.observe(el);
        });
    }

    /**
     * Initialize counter animations
     */
    function initializeCounters() {
        const counters = $('.stat-number[data-count]');
        let hasAnimated = false;

        const countObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting && !hasAnimated) {
                    hasAnimated = true;
                    animateCounters();
                }
            });
        }, { threshold: 0.5 });

        if (document.querySelector('.statistics-section')) {
            countObserver.observe(document.querySelector('.statistics-section'));
        }

        function animateCounters() {
            counters.each(function() {
                const counter = $(this);
                const target = counter.attr('data-count');
                const numTarget = parseInt(target.replace(/[^0-9]/g, ''));
                const suffix = target.replace(/[0-9]/g, '');
                
                $({ countNum: 0 }).animate({
                    countNum: numTarget
                }, {
                    duration: 2000,
                    easing: 'easeInOutQuart',
                    step: function() {
                        const num = Math.floor(this.countNum);
                        counter.text(num + suffix);
                    },
                    complete: function() {
                        counter.text(target);
                    }
                });
            });
        }
    }

    /**
     * Initialize lightbox for gallery
     */
    function initializeLightbox() {
        // Lightbox options
        if (typeof lightbox !== 'undefined') {
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'fitImagesInViewport': true,
                'showImageNumberLabel': true,
                'albumLabel': "Resim %1 / %2"
            });
        }

        // Video gallery functionality
        $('.gallery-video').on('click', function(e) {
            e.preventDefault();
            const videoUrl = $(this).find('a').attr('href');
            if (videoUrl) {
                window.open(videoUrl, '_blank');
            }
        });
    }

    /**
     * Initialize contact form
     */
    function initializeContactForm() {
        $('#contactForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const formData = new FormData(this);
            
            // Show loading state
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.text();
            submitBtn.prop('disabled', true).text('Gönderiliyor...');

            // Send form data
            $.ajax({
                url: 'api/contact-form.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            showAlert('success', 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.');
                            form[0].reset();
                        } else {
                            showAlert('error', result.message || 'Bir hata oluştu. Lütfen tekrar deneyin.');
                        }
                    } catch (e) {
                        showAlert('error', 'Bir hata oluştu. Lütfen tekrar deneyin.');
                    }
                },
                error: function() {
                    showAlert('error', 'Bağlantı hatası. Lütfen tekrar deneyin.');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text(originalText);
                }
            });
        });
    }

    /**
     * Initialize newsletter form
     */
    function initializeNewsletterForm() {
        $('.newsletter-form').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const email = form.find('input[name="email"]').val();
            
            if (!isValidEmail(email)) {
                showAlert('error', 'Geçerli bir e-mail adresi girin.');
                return;
            }

            const submitBtn = form.find('button[type="submit"]');
            const originalHtml = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            // Send newsletter subscription
            $.ajax({
                url: 'api/newsletter.php',
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.success) {
                            showAlert('success', 'Başarıyla abone oldunuz!');
                            form[0].reset();
                        } else {
                            showAlert('error', result.message || 'Bir hata oluştu.');
                        }
                    } catch (e) {
                        showAlert('error', 'Bir hata oluştu.');
                    }
                },
                error: function() {
                    showAlert('error', 'Bağlantı hatası.');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalHtml);
                }
            });
        });
    }

    /**
     * Initialize lazy loading for images
     */
    function initializeLazyLoading() {
        const imageObserver = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }

    /**
     * Initialize smooth scrolling for anchor links
     */
    function initializeSmoothScrolling() {
        $('a[href*="#"]:not([href="#"])').on('click', function(e) {
            const target = $(this.hash);
            if (target.length) {
                e.preventDefault();
                const headerHeight = $('.header').outerHeight();
                $('html, body').animate({
                    scrollTop: target.offset().top - headerHeight
                }, 800, 'easeInOutQuart');
            }
        });
    }

    /**
     * Setup additional event listeners
     */
    function setupEventListeners() {
        // Portfolio filter (if exists)
        $('.portfolio-filter').on('click', 'button', function() {
            const filter = $(this).data('filter');
            $('.portfolio-filter button').removeClass('active');
            $(this).addClass('active');
            
            if (filter === '*') {
                $('.portfolio-item').fadeIn();
            } else {
                $('.portfolio-item').hide();
                $(`.portfolio-item[data-category="${filter}"]`).fadeIn();
            }
        });

        // Service card hover effects
        $('.service-card').hover(
            function() {
                $(this).find('.service-image img').addClass('scaled');
            },
            function() {
                $(this).find('.service-image img').removeClass('scaled');
            }
        );

        // Portfolio card hover effects
        $('.portfolio-card').hover(
            function() {
                $(this).find('.portfolio-overlay').addClass('visible');
            },
            function() {
                $(this).find('.portfolio-overlay').removeClass('visible');
            }
        );

        // Typing effect for hero title (if exists)
        if ($('.hero-title').hasClass('typing-effect')) {
            initializeTypingEffect();
        }

        // Handle form validation
        $('form').on('submit', function() {
            return validateForm($(this));
        });
    }

    /**
     * Initialize typing effect
     */
    function initializeTypingEffect() {
        const element = $('.hero-title')[0];
        const text = element.textContent;
        element.textContent = '';
        
        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                element.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            }
        };
        
        setTimeout(typeWriter, 1000);
    }

    /**
     * Initialize animations
     */
    function initializeAnimations() {
        // Add entrance animations
        $('.hero-content').addClass('animate__animated animate__fadeInUp');
        
        // Stagger card animations
        $('.service-card, .portfolio-card').each(function(index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
        });
    }

    /**
     * Show alert message
     */
    function showAlert(type, message) {
        const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
        const alert = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('.container:first').prepend(alert);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            alert.fadeOut(() => alert.remove());
        }, 5000);
    }

    /**
     * Validate email address
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Validate form
     */
    function validateForm(form) {
        let isValid = true;
        
        // Remove previous error states
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
        
        // Validate required fields
        form.find('[required]').each(function() {
            const field = $(this);
            const value = field.val().trim();
            
            if (!value) {
                showFieldError(field, 'Bu alan zorunludur.');
                isValid = false;
            } else if (field.attr('type') === 'email' && !isValidEmail(value)) {
                showFieldError(field, 'Geçerli bir e-mail adresi girin.');
                isValid = false;
            }
        });
        
        return isValid;
    }

    /**
     * Show field error
     */
    function showFieldError(field, message) {
        field.addClass('is-invalid');
        field.after(`<div class="invalid-feedback">${message}</div>`);
    }

    /**
     * Utility functions
     */
    const utils = {
        // Debounce function
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        // Throttle function
        throttle: function(func, limit) {
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
        },

        // Get random number
        random: function(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        },

        // Format number with commas
        formatNumber: function(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }
    };

    // Expose utils globally if needed
    window.CasinoUtils = utils;

    // Performance optimization
    $(window).on('scroll', utils.throttle(function() {
        scrollPosition = $(this).scrollTop();
    }, 10));

    // Handle touch events for mobile
    if ('ontouchstart' in window) {
        $(document).on('touchstart', '.btn, .card', function() {
            $(this).addClass('touched');
        });
        
        $(document).on('touchend', '.btn, .card', function() {
            const element = $(this);
            setTimeout(() => element.removeClass('touched'), 150);
        });
    }

    // Social sharing functionality
    window.shareToSocial = function(platform, url, text) {
        url = url || window.location.href;
        text = text || document.title;
        
        const shareUrls = {
            facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
            twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`,
            linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`,
            whatsapp: `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`
        };
        
        if (shareUrls[platform]) {
            window.open(shareUrls[platform], '_blank', 'width=600,height=400');
        }
    };

    // Console message
    console.log(`
    🎰 Casino Portfolio Website
    👨‍💻 Geliştirici: BERAT K
    🚀 Version: 1.0.0
    🎨 Modern casino-themed portfolio
    `);

})(jQuery);

// Add custom easing functions
jQuery.easing.easeInOutQuart = function(x, t, b, c, d) {
    if ((t /= d / 2) < 1) return c / 2 * t * t * t * t + b;
    return -c / 2 * ((t -= 2) * t * t * t - 2) + b;
};

// Service Worker registration (if available)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('SW registered: ', registration);
            })
            .catch(function(registrationError) {
                console.log('SW registration failed: ', registrationError);
            });
    });
}