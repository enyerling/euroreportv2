const CACHE_NAME = 'offline-v1';
const FILES_TO_CACHE = [
    '/offline.html',
    '/vendor/adminlte/dist/img/logo_1.png',
    '/favicon.ico',
    '/vendor/adminlte/dist/css/adminlte.min.css',
    '/vendor/adminlte/dist/js/adminlte.min.js',
    '/vendor/jquery/jquery.min.js',
    '/vendor/bootstrap/js/bootstrap.bundle.min.js',
    
];

const STORE_NAME = 'requests';

self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function(cache) {
            console.log('Caching files:', FILES_TO_CACHE);
            const cachePromises = FILES_TO_CACHE.map(url => {
                return fetch(url).then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to fetch ' + url);
                    }
                    return cache.put(url, response);
                }).catch(error => {
                    console.error('Error caching ' + url + ':', error);
                });
            });

            return Promise.all(cachePromises).then(() => {
                console.log('Files cached successfully');
            }).catch(error => {
                console.error('Error caching files:', error);
            });
        })
    );
});

self.addEventListener('activate', function(event) {
    var cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request).catch(() => {
                if (event.request.mode === 'navigate') {
                    return caches.match('/offline.php');
                }
            });
        })
    );
});

self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-evaluations') {
        event.waitUntil(syncEvaluations());
    }
});

// Función para sincronizar evaluaciones desde IndexedDB con el servidor
function syncEvaluations() {
    return openDatabase().then((db) => {
        const tx = db.transaction('pendingEvaluations', 'readonly');
        const store = tx.objectStore('pendingEvaluations');
        const request = store.getAll();

        return new Promise((resolve, reject) => {
            request.onsuccess = (event) => {
                const evaluations = event.target.result;
                Promise.all(
                    evaluations.map((evaluation) => {
                        return fetch('/save/evaluation', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': evaluation._token
                            },
                            body: JSON.stringify(evaluation)
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Datos sincronizados con éxito:', data);
                            // Eliminar datos sincronizados de IndexedDB
                            const deleteTx = db.transaction('pendingEvaluations', 'readwrite');
                            const deleteStore = deleteTx.objectStore('pendingEvaluations');
                            deleteStore.delete(evaluation.id);
                            deleteTx.oncomplete = () => {
                                console.log('Datos eliminados de IndexedDB tras la sincronización.');
                            };
                            deleteTx.onerror = (event) => {
                                console.error('Error al eliminar los datos:', event.target.error);
                            };
                        })
                        .catch(error => {
                            console.error('Error al sincronizar datos con el servidor:', error);
                        });
                    })
                ).then(() => {
                    resolve();
                }).catch((error) => {
                    reject(error);
                });
            };

            request.onerror = (event) => {
                reject(event.target.error);
            };
        });
    });
}

// Función para abrir la base de datos IndexedDB
function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('EvaluationsDB', 1);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains('pendingEvaluations')) {
                db.createObjectStore('pendingEvaluations', { keyPath: 'id', autoIncrement: true });
            }
        };

        request.onsuccess = (event) => {
            resolve(event.target.result);
        };

        request.onerror = (event) => {
            reject(event.target.error);
        };
    });
}