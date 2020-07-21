const staticCacheName = 'LyOS-CACHE';
const filesToCache = [];
self.addEventListener('install', event => {
	console.log('Instalando ficheros CACHE en el cliente...');
	event.waitUntil(
		caches.open(staticCacheName)
			.then(cache => {
				return cache.addAll(filesToCache);
			})
	);
});

self.addEventListener('fetch', function(event) {
	event.respondWith(
		caches.open(staticCacheName).then(function(cache) {
			return cache.match(event.request).then(function (response) {
				return response || fetch(event.request).then(function(response) {
					if (event.request.destination == "script" || event.request.destination == "image" || event.request.destination == "font" || event.request.destination == "manifest") {
						console.log("Instalando fichero");
						cache.put(event.request, response.clone()); //Clonamos el fichero recibido por GET si es un fichero estático
					} else {
						//console.log("Fichero dinámico")
					}
					return response;
				});
			});
		})
	);
});


self.addEventListener('activate', event => {
	console.log('Activando nueva versión del SW...');
	const cacheWhitelist = [staticCacheName];
	event.waitUntil(
		caches.keys().then(cacheNames => {
			return Promise.all(
				cacheNames.map(cacheName => {
					if (cacheWhitelist.indexOf(cacheName) === -1) {
						return caches.delete(cacheName);
					}
				})
			);
		})
	);
});

self.addEventListener('push', function(event) {
	console.log('[Service Worker] Mensaje push recibido.');
	console.log('[Service Worker] Mensaje push: "${event.data.text()}"');
	const title = 'Openlove.ME';
	const options = {
		body: event.data.text(),
		icon: '../favicon.png',
		vibrate: [300, 200, 300],
		badge: '../favicon-16x16.png'
	};
	event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function(event) {
	console.log('Se ha hecho click en la notificación.');
	event.notification.close();
	event.waitUntil(
		clients.openWindow('#')
	);
});


