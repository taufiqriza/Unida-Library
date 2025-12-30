// Service Worker for Push Notifications
self.addEventListener('push', function(event) {
    const data = event.data ? event.data.json() : {};
    
    const options = {
        body: data.body || 'Ada pesan baru',
        icon: data.icon || '/images/logo-icon.png',
        badge: data.badge || '/images/logo-icon.png',
        vibrate: [200, 100, 200],
        tag: data.tag || 'default',
        renotify: data.renotify || true,
        requireInteraction: true, // Notifikasi tetap sampai user klik/dismiss
        data: {
            url: data.data?.url || '/staff'
        }
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title || 'Perpustakaan UNIDA', options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    
    const url = event.notification.data?.url || '/staff';
    
    event.waitUntil(
        clients.matchAll({type: 'window', includeUncontrolled: true}).then(function(clientList) {
            for (let client of clientList) {
                if (client.url.includes('/staff') && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});
