var CACHE_STATIC_NAME = 'static-v25';

self.addEventListener('install', function(event) {
    event.waitUntil(
      caches.open(CACHE_STATIC_NAME)
        .then(function(cache) {
          console.log('[Service Worker] Precaching App Shell');
          cache.addAll([
            './offline',
            './assets/front/img/static/offline.png',
            './assets/front/img/static/offline-breadcrumb.jpeg'
          ]);
        })
    )
});

self.addEventListener('activate', function(event) {
    console.log('[Service Worker] Activating Service Worker ....', event);
    event.waitUntil(
      caches.keys()
        .then(function(keyList) {
          return Promise.all(keyList.map(function(key) {
            if (key !== CACHE_STATIC_NAME) {
              console.log('[Service Worker] Removing old cache.', key);
              return caches.delete(key);
            }
          }));
        })
    );
    return self.clients.claim();
});

self.addEventListener('fetch', function(event) {
    event.respondWith(
      caches.match(event.request)
        .then(function(response) {
          if (response) {
            return response;
          } else {
            return fetch(event.request)
              .catch(function(err) {
                return caches.open(CACHE_STATIC_NAME)
                  .then(function(cache) {
                    return cache.match('./offline');
                  });
              });
          }
        })
    );
});

self.addEventListener('push', function(e) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        //notifications aren't supported or permission not granted!
        return;
    }

    if (e.data) {
        var msg = e.data.json();
        console.log(msg)
        var options = {
            body: msg.body,
            icon: msg.icon
        };
        if (msg.actions && msg.actions.length > 0) {
            options.actions = msg.actions;
        }
        e.waitUntil(self.registration.showNotification(msg.title, options));
    }
});


self.addEventListener('notificationclick', function(e) {
    if (e.action.length > 0) {
        self.clients.openWindow(e.action);
    }
});