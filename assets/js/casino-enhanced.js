/**
 * GÜÇLÜ KUMARHANE ATMOSFERI - ENHANCED CASINO EFFECTS
 * Advanced JavaScript for Casino Portfolio Website
 * Geliştirici: BERAT K
 */

(function() {
    'use strict';

    // ==========================================
    // GLOBAL VARIABLES
    // ==========================================
    
    let isLoading = true;
    let particles = [];
    let mouseX = 0;
    let mouseY = 0;
    let lastScrollY = 0;
    
    // ==========================================
    // CASINO LOADING ANIMATION
    // ==========================================
    
    function initCasinoLoading() {
        const loader = document.getElementById('casinoLoading');
        const loadingTexts = [
            'Yükleniyor...',
            'Hazırlanıyor...',
            'Kumarhane Açılıyor...',
            'Masalar Hazırlanıyor...',
            'Şanslı Hissediyor musun?'
        ];
        
        let currentTextIndex = 0;
        const loaderText = document.createElement('div');
        loaderText.className = 'casino-loader-text';
        loaderText.style.cssText = `
            position: absolute;
            bottom: 20%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 1.5rem;
            color: var(--casino-gold);
            text-shadow: var(--shadow-gold);
            animation: neon-flicker 1s ease-in-out infinite;
        `;
        
        if (loader) {
            loader.appendChild(loaderText);
            
            const textInterval = setInterval(() => {
                loaderText.textContent = loadingTexts[currentTextIndex];
                currentTextIndex = (currentTextIndex + 1) % loadingTexts.length;
            }, 800);
            
            // Hide loader after 3 seconds
            setTimeout(() => {
                loader.style.opacity = '0';
                clearInterval(textInterval);
                setTimeout(() => {
                    loader.style.display = 'none';
                    isLoading = false;
                    initCasinoEffects();
                }, 500);
            }, 3000);
        }
    }
    
    // ==========================================
    // PARTICLE SYSTEM
    // ==========================================
    
    class CasinoParticle {
        constructor() {
            this.x = Math.random() * window.innerWidth;
            this.y = Math.random() * window.innerHeight;
            this.vx = (Math.random() - 0.5) * 2;
            this.vy = (Math.random() - 0.5) * 2;
            this.size = Math.random() * 3 + 1;
            this.color = this.getRandomColor();
            this.opacity = Math.random();
            this.pulseSpeed = Math.random() * 0.02 + 0.01;
            this.pulsePhase = Math.random() * Math.PI * 2;
        }
        
        getRandomColor() {
            const colors = ['#ffd700', '#ff00ff', '#00d4ff', '#ff073a', '#00ff41'];
            return colors[Math.floor(Math.random() * colors.length)];
        }
        
        update() {
            this.x += this.vx;
            this.y += this.vy;
            this.pulsePhase += this.pulseSpeed;
            this.opacity = 0.5 + Math.sin(this.pulsePhase) * 0.3;
            
            // Bounce off edges
            if (this.x <= 0 || this.x >= window.innerWidth) this.vx *= -1;
            if (this.y <= 0 || this.y >= window.innerHeight) this.vy *= -1;
            
            // Mouse interaction
            const dx = mouseX - this.x;
            const dy = mouseY - this.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance < 100) {
                this.vx += dx * 0.0001;
                this.vy += dy * 0.0001;
            }
        }
        
        draw(ctx) {
            ctx.save();
            ctx.globalAlpha = this.opacity;
            ctx.fillStyle = this.color;
            ctx.shadowColor = this.color;
            ctx.shadowBlur = 10;
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
            ctx.restore();
        }
    }
    
    function initParticleSystem() {
        const canvas = document.createElement('canvas');
        canvas.id = 'casinoParticles';
        canvas.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        `;
        
        document.body.appendChild(canvas);
        
        const ctx = canvas.getContext('2d');
        
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        
        // Create particles
        for (let i = 0; i < 50; i++) {
            particles.push(new CasinoParticle());
        }
        
        function animateParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            particles.forEach(particle => {
                particle.update();
                particle.draw(ctx);
            });
            
            requestAnimationFrame(animateParticles);
        }
        
        animateParticles();
    }
    
    // ==========================================
    // MOUSE TRACKING
    // ==========================================
    
    function initMouseTracking() {
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
            
            // Create cursor trail effect
            if (!isLoading) {
                createCursorTrail(e.clientX, e.clientY);
            }
        });
    }
    
    function createCursorTrail(x, y) {
        const trail = document.createElement('div');
        trail.className = 'cursor-trail';
        trail.style.cssText = `
            position: fixed;
            width: 10px;
            height: 10px;
            background: var(--casino-gold);
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            left: ${x - 5}px;
            top: ${y - 5}px;
            box-shadow: 0 0 10px var(--casino-gold);
            animation: fadeOut 0.5s ease-out forwards;
        `;
        
        document.body.appendChild(trail);
        
        setTimeout(() => {
            trail.remove();
        }, 500);
    }
    
    // ==========================================
    // SCROLL EFFECTS
    // ==========================================
    
    function initScrollEffects() {
        const navbar = document.querySelector('.casino-navbar');
        
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;
            
            // Navbar effects
            if (navbar) {
                if (scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            }
            
            // Parallax effects
            const parallaxElements = document.querySelectorAll('[data-parallax]');
            parallaxElements.forEach(element => {
                const speed = element.getAttribute('data-parallax') || 0.5;
                const yPos = -(scrollY * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
            
            lastScrollY = scrollY;
        });
    }
    
    // ==========================================
    // CASINO CARD EFFECTS
    // ==========================================
    
    function initCardEffects() {
        const cards = document.querySelectorAll('.casino-card');
        
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-10px) scale(1.02)';
                card.style.boxShadow = 'var(--shadow-deep), var(--shadow-gold)';
                
                // Add sparkle effect
                createSparkles(card);
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
                card.style.boxShadow = 'var(--shadow-card)';
            });
        });
    }
    
    function createSparkles(element) {
        const rect = element.getBoundingClientRect();
        
        for (let i = 0; i < 8; i++) {
            const sparkle = document.createElement('div');
            sparkle.className = 'casino-sparkle';
            sparkle.style.cssText = `
                position: absolute;
                width: 4px;
                height: 4px;
                background: var(--casino-gold);
                border-radius: 50%;
                pointer-events: none;
                z-index: 1000;
                left: ${rect.left + Math.random() * rect.width}px;
                top: ${rect.top + Math.random() * rect.height}px;
                animation: sparkleAnimation 1s ease-out forwards;
            `;
            
            document.body.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
            }, 1000);
        }
    }
    
    // ==========================================
    // GALLERY EFFECTS
    // ==========================================
    
    function initGalleryEffects() {
        const galleryItems = document.querySelectorAll('.casino-gallery-item');
        
        galleryItems.forEach(item => {
            const overlay = item.querySelector('.casino-gallery-overlay');
            const image = item.querySelector('.casino-gallery-image');
            
            item.addEventListener('mouseenter', () => {
                if (overlay) {
                    overlay.style.opacity = '0.9';
                }
                if (image) {
                    image.style.transform = 'scale(1.1)';
                    image.style.filter = 'brightness(1.2) contrast(1.1)';
                }
            });
            
            item.addEventListener('mouseleave', () => {
                if (overlay) {
                    overlay.style.opacity = '0';
                }
                if (image) {
                    image.style.transform = 'scale(1)';
                    image.style.filter = 'brightness(1) contrast(1)';
                }
            });
            
            // Add click effect
            item.addEventListener('click', () => {
                item.style.animation = 'card-flip 0.6s ease-in-out';
                setTimeout(() => {
                    item.style.animation = '';
                }, 600);
            });
        });
    }
    
    // ==========================================
    // BUTTON EFFECTS
    // ==========================================
    
    function initButtonEffects() {
        const buttons = document.querySelectorAll('.casino-btn');
        
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                button.style.transform = 'translateY(-3px)';
                button.style.boxShadow = 'var(--shadow-neon-pink)';
                
                // Add ripple effect
                createRipple(button);
            });
            
            button.addEventListener('mouseleave', () => {
                button.style.transform = 'translateY(0)';
                button.style.boxShadow = 'none';
            });
            
            button.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Add click animation
                button.style.animation = 'casino-pulse 0.3s ease-in-out';
                setTimeout(() => {
                    button.style.animation = '';
                }, 300);
                
                // Original click handler
                if (button.hasAttribute('onclick')) {
                    eval(button.getAttribute('onclick'));
                }
            });
        });
    }
    
    function createRipple(element) {
        const ripple = document.createElement('div');
        ripple.className = 'casino-ripple';
        ripple.style.cssText = `
            position: absolute;
            width: 100%;
            height: 100%;
            background: var(--gradient-neon);
            border-radius: 50%;
            transform: scale(0);
            animation: rippleAnimation 0.6s ease-out;
            top: 0;
            left: 0;
            pointer-events: none;
        `;
        
        element.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    }
    
    // ==========================================
    // SOUND EFFECTS
    // ==========================================
    
    function initSoundEffects() {
        // Create audio context for Web Audio API
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        
        function playBeep(frequency = 440, duration = 200) {
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = frequency;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + duration / 1000);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + duration / 1000);
        }
        
        // Add sound to buttons
        const buttons = document.querySelectorAll('.casino-btn');
        buttons.forEach(button => {
            button.addEventListener('mouseenter', () => {
                playBeep(800, 100);
            });
            
            button.addEventListener('click', () => {
                playBeep(1000, 200);
            });
        });
        
        // Add sound to gallery items
        const galleryItems = document.querySelectorAll('.casino-gallery-item');
        galleryItems.forEach(item => {
            item.addEventListener('mouseenter', () => {
                playBeep(600, 150);
            });
        });
    }
    
    // ==========================================
    // TYPING EFFECT
    // ==========================================
    
    function initTypingEffect() {
        const typingElements = document.querySelectorAll('[data-typing]');
        
        typingElements.forEach(element => {
            const text = element.getAttribute('data-typing');
            const speed = parseInt(element.getAttribute('data-typing-speed')) || 50;
            
            element.textContent = '';
            
            let i = 0;
            const typeInterval = setInterval(() => {
                if (i < text.length) {
                    element.textContent += text.charAt(i);
                    i++;
                } else {
                    clearInterval(typeInterval);
                }
            }, speed);
        });
    }
    
    // ==========================================
    // SMOOTH SCROLLING
    // ==========================================
    
    function initSmoothScrolling() {
        const navLinks = document.querySelectorAll('a[href^="#"]');
        
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                const targetId = link.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    }
    
    // ==========================================
    // CASINO EFFECTS INITIALIZATION
    // ==========================================
    
    function initCasinoEffects() {
        initParticleSystem();
        initMouseTracking();
        initScrollEffects();
        initCardEffects();
        initGalleryEffects();
        initButtonEffects();
        initSoundEffects();
        initTypingEffect();
        initSmoothScrolling();
        
        // Add CSS animations
        addCSSAnimations();
    }
    
    function addCSSAnimations() {
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeOut {
                0% { opacity: 1; transform: scale(1); }
                100% { opacity: 0; transform: scale(0); }
            }
            
            @keyframes sparkleAnimation {
                0% { opacity: 1; transform: scale(1) rotate(0deg); }
                100% { opacity: 0; transform: scale(0) rotate(180deg); }
            }
            
            @keyframes rippleAnimation {
                0% { transform: scale(0); opacity: 1; }
                100% { transform: scale(2); opacity: 0; }
            }
            
            .cursor-trail {
                animation: fadeOut 0.5s ease-out forwards;
            }
            
            .casino-sparkle {
                animation: sparkleAnimation 1s ease-out forwards;
            }
            
            .casino-ripple {
                animation: rippleAnimation 0.6s ease-out forwards;
            }
        `;
        
        document.head.appendChild(style);
    }
    
    // ==========================================
    // INITIALIZATION
    // ==========================================
    
    document.addEventListener('DOMContentLoaded', () => {
        initCasinoLoading();
        
        // Enable sound on user interaction
        document.addEventListener('click', () => {
            if (window.AudioContext) {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                audioContext.resume();
            }
        }, { once: true });
    });
    
    // ==========================================
    // WINDOW RESIZE HANDLER
    // ==========================================
    
    window.addEventListener('resize', () => {
        // Recalculate particle positions
        particles.forEach(particle => {
            if (particle.x > window.innerWidth) particle.x = window.innerWidth;
            if (particle.y > window.innerHeight) particle.y = window.innerHeight;
        });
    });
    
    // ==========================================
    // PERFORMANCE OPTIMIZATION
    // ==========================================
    
    // Throttle scroll events
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
    
    // Debounce resize events
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), wait);
        };
    }
    
    // Apply throttling and debouncing
    window.addEventListener('scroll', throttle(() => {
        // Scroll optimizations
    }, 16));
    
    window.addEventListener('resize', debounce(() => {
        // Resize optimizations
    }, 250));
    
})();