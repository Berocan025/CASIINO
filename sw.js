/**
 * Service Worker
 * Geliştirici: BERAT K
 * PWA functionality and caching for casino portfolio
 */

const CACHE_NAME = 'casino-portfolio-v1.0.0';
const STATIC_CACHE = 'static-v1';
const DYNAMIC_CACHE = 'dynamic-v1';

// Files to cache immediately
const STATIC_FILES = [
    '/',
    '/index.php',
    '/pages/about.php',
    '/pages/services.php',
    '/pages/portfolio.php',
    '/pages/gallery.php',
    '/pages/contact.php',
    '/assets/css/style.css',
    '/assets/js/main.js',
    '/assets/images/logo.png',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
    'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap'
];

// Dynamic files to cache on request
const DYNAMIC_LIMIT = 50;

// Install event - cache static files
self.addEventListener('install', (event) => {
    console.log('🎰 Service Worker: Installing...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            console.log('🎰 Service Worker: Caching static files');
            return cache.addAll(STATIC_FILES);
        }).catch((error) => {
            console.error('🎰 Service Worker: Cache failed', error);
        })
    );
    
    // Force the SW to activate immediately
    self.skipWaiting();
});

// Activate event - clean old caches
self.addEventListener('activate', (event) => {
    console.log('🎰 Service Worker: Activating...');
    
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                        console.log('🎰 Service Worker: Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    
    // Claim all clients immediately
    self.clients.claim();
});

// Fetch event - serve cached files or fetch from network
self.addEventListener('fetch', (event) => {
    const { request } = event;
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip admin panel and API requests from caching
    if (request.url.includes('/admin/') || 
        request.url.includes('/api/') ||
        request.url.includes('php') && request.url.includes('?')) {
        return;
    }
    
    event.respondWith(
        caches.match(request).then((response) => {
            if (response) {
                // Return cached version
                console.log('🎰 Service Worker: Serving from cache:', request.url);
                return response;
            }
            
            // Fetch from network and cache
            return fetch(request).then((fetchResponse) => {
                // Check if valid response
                if (!fetchResponse || fetchResponse.status !== 200 || fetchResponse.type !== 'basic') {
                    return fetchResponse;
                }
                
                // Clone response
                const responseToCache = fetchResponse.clone();
                
                // Cache dynamic content
                caches.open(DYNAMIC_CACHE).then((cache) => {
                    cache.put(request, responseToCache);
                    
                    // Limit dynamic cache size
                    limitCacheSize(DYNAMIC_CACHE, DYNAMIC_LIMIT);
                });
                
                console.log('🎰 Service Worker: Fetched and cached:', request.url);
                return fetchResponse;
                
            }).catch(() => {
                // Return offline page if available
                if (request.destination === 'document') {
                    return caches.match('/offline.html');
                }
            });
        })
    );
});

// Background sync for form submissions
self.addEventListener('sync', (event) => {
    console.log('🎰 Service Worker: Background sync:', event.tag);
    
    if (event.tag === 'contact-form-sync') {
        event.waitUntil(syncContactForm());
    }
    
    if (event.tag === 'newsletter-sync') {
        event.waitUntil(syncNewsletter());
    }
});

// Push notifications
self.addEventListener('push', (event) => {
    console.log('🎰 Service Worker: Push notification received');
    
    const options = {
        body: event.data ? event.data.text() : 'Yeni casino içeriği mevcut!',
        icon: '/assets/images/icon-192.png',
        badge: '/assets/images/icon-72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'İncele',
                icon: '/assets/images/checkmark.png'
            },
            {
                action: 'close',
                title: 'Kapat',
                icon: '/assets/images/xmark.png'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification('🎰 Casino Yayıncısı - BERAT K', options)
    );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    console.log('🎰 Service Worker: Notification clicked');
    
    event.notification.close();
    
    if (event.action === 'explore') {
        // Open the app
        event.waitUntil(
            clients.openWindow('/')
        );
    } else if (event.action === 'close') {
        // Just close the notification
        return;
    } else {
        // Default action - open the app
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// Message handler for communication with main thread
self.addEventListener('message', (event) => {
    console.log('🎰 Service Worker: Message received:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'CACHE_URLS') {
        event.waitUntil(
            caches.open(DYNAMIC_CACHE).then((cache) => {
                return cache.addAll(event.data.urls);
            })
        );
    }
});

// Helper functions
function limitCacheSize(cacheName, maxItems) {
    caches.open(cacheName).then((cache) => {
        cache.keys().then((keys) => {
            if (keys.length > maxItems) {
                cache.delete(keys[0]).then(() => {
                    limitCacheSize(cacheName, maxItems);
                });
            }
        });
    });
}

function syncContactForm() {
    return new Promise((resolve, reject) => {
        // Get pending form submissions from IndexedDB
        const request = indexedDB.open('casino-portfolio-db', 1);
        
        request.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction(['contactForms'], 'readwrite');
            const store = transaction.objectStore('contactForms');
            
            store.getAll().onsuccess = (event) => {
                const forms = event.target.result;
                
                Promise.all(
                    forms.map((form) => {
                        return fetch('/api/contact-form.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(form.data)
                        }).then((response) => {
                            if (response.ok) {
                                // Remove from IndexedDB
                                store.delete(form.id);
                            }
                        });
                    })
                ).then(() => {
                    resolve();
                }).catch(() => {
                    reject();
                });
            };
        };
        
        request.onerror = () => {
            reject();
        };
    });
}

function syncNewsletter() {
    return new Promise((resolve, reject) => {
        // Get pending newsletter subscriptions from IndexedDB
        const request = indexedDB.open('casino-portfolio-db', 1);
        
        request.onsuccess = (event) => {
            const db = event.target.result;
            const transaction = db.transaction(['newsletters'], 'readwrite');
            const store = transaction.objectStore('newsletters');
            
            store.getAll().onsuccess = (event) => {
                const newsletters = event.target.result;
                
                Promise.all(
                    newsletters.map((newsletter) => {
                        return fetch('/api/newsletter.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(newsletter.data)
                        }).then((response) => {
                            if (response.ok) {
                                // Remove from IndexedDB
                                store.delete(newsletter.id);
                            }
                        });
                    })
                ).then(() => {
                    resolve();
                }).catch(() => {
                    reject();
                });
            };
        };
        
        request.onerror = () => {
            reject();
        };
    });
}

// Update notification
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'UPDATE_AVAILABLE') {
        // Notify user of update
        self.registration.showNotification('🎰 Güncelleme Mevcut', {
            body: 'Yeni özellikler için sayfayı yenileyin',
            icon: '/assets/images/icon-192.png',
            actions: [
                {
                    action: 'update',
                    title: 'Güncelle'
                },
                {
                    action: 'dismiss',
                    title: 'Daha Sonra'
                }
            ]
        });
    }
});

// Performance monitoring
self.addEventListener('fetch', (event) => {
    if (event.request.url.includes('/api/')) {
        const startTime = Date.now();
        
        event.respondWith(
            fetch(event.request).then((response) => {
                const endTime = Date.now();
                const duration = endTime - startTime;
                
                // Log slow API calls
                if (duration > 3000) {
                    console.warn(`🎰 Slow API call: ${event.request.url} took ${duration}ms`);
                }
                
                return response;
            })
        );
    }
});

// Error handling
self.addEventListener('error', (event) => {
    console.error('🎰 Service Worker Error:', event.error);
});

self.addEventListener('unhandledrejection', (event) => {
    console.error('🎰 Service Worker Unhandled Rejection:', event.reason);
});

console.log('🎰 Service Worker: Loaded successfully');