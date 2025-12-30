// Service Worker for Push Notifications
self.addEventListener('push', function(event) {
    const data = event.data ? event.data.json() : {};
    
    const options = {
        body: data.body || 'Ada pesan baru',
        icon: data.icon || '/images/logo-icon.png',
        badge: '/images/logo-icon.png',
        vibrate: [100, 50, 100],
        data: {
            url: data.actions?.[0]?.action || '/staff'
        },
        actions: data.actions || []
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
            // Check if there's already a window open
            for (let client of clientList) {
                if (client.url.includes('/staff') && 'focus' in client) {
                    return client.focus();
                }
            }
            // Open new window
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});
