const preLoad = function () {
    return caches.open("offline").then(function (cache) {
        // Solo cachear archivos estáticos, nunca rutas dinámicas
        const staticFiles = filesToCache.filter(function(file) {
            // Solo archivos estáticos con extensión
            return file.endsWith('.html') || file.endsWith('.json') || 
                   file.endsWith('.png') || file.endsWith('.css') || file.endsWith('.js');
        });
        
        // Convertir todas las URLs a HTTPS si estamos en HTTPS
        const httpsFiles = staticFiles.map(function(file) {
            if (file.startsWith('http://') && self.location.protocol === 'https:') {
                return file.replace('http://', 'https://');
            }
            return file;
        });
        
        // Intentar cachear, pero no fallar si hay errores
        return Promise.allSettled(
            httpsFiles.map(function(file) {
                return cache.add(file).catch(function(error) {
                    // Ignorar errores individuales silenciosamente
                    return null;
                });
            })
        ).then(function() {
            // Siempre resolver exitosamente
            return Promise.resolve();
        });
    });
};

self.addEventListener("install", function (event) {
    // No esperar a que termine el preload, instalar inmediatamente
    event.waitUntil(
        preLoad().catch(function(error) {
            // Ignorar errores de cacheo, no son críticos para la instalación
            console.log('Error al precachear (no crítico):', error);
        }).then(function() {
            // Forzar activación inmediata
            return self.skipWaiting();
        })
    );
});

self.addEventListener("activate", function (event) {
    // Tomar control inmediatamente
    event.waitUntil(self.clients.claim());
});

const filesToCache = [
    // NO incluir '/' porque redirige a /login y causa problemas de contenido mixto
    '/offline.html',
    '/manifest.json',
    '/logo.png',
    '/assets/css/argon-dashboard.css',
    '/assets/css/custom-mobile.css',
    '/assets/js/argon-dashboard.js',
    '/assets/js/custom-nav.js'
];

// Función para convertir URLs HTTP a HTTPS si es necesario
const ensureHttps = function(url) {
    if (url.startsWith('http://') && self.location.protocol === 'https:') {
        return url.replace('http://', 'https://');
    }
    return url;
};

const checkResponse = function (request) {
    return new Promise(function (fulfill, reject) {
        // Asegurar que la URL use HTTPS si estamos en HTTPS
        let url = request.url;
        if (url.startsWith('http://') && self.location.protocol === 'https:') {
            url = url.replace('http://', 'https://');
            request = new Request(url, {
                method: request.method,
                headers: request.headers,
                mode: 'cors',
                credentials: 'same-origin'
            });
        }
        
        fetch(request).then(function (response) {
            // Solo aceptar respuestas exitosas y que no sean redirecciones a HTTP
            if (response.status !== 404 && response.ok) {
                // Verificar que la respuesta final no sea HTTP si estamos en HTTPS
                if (response.url && response.url.startsWith('http://') && self.location.protocol === 'https:') {
                    reject(new Error('Mixed content blocked'));
                    return;
                }
                fulfill(response);
            } else {
                reject();
            }
        }).catch(function(error) {
            // Si hay error de contenido mixto, rechazar silenciosamente
            if (error.message && error.message.includes('Mixed')) {
                reject(error);
            } else {
                reject(error);
            }
        });
    });
};

const addToCache = function (request) {
    // Only cache http(s) requests
    if (!request.url.startsWith('http')) {
        return Promise.resolve();
    }
    // Asegurar que la URL use HTTPS si estamos en HTTPS
    let url = request.url;
    if (url.startsWith('http://') && self.location.protocol === 'https:') {
        url = url.replace('http://', 'https://');
        request = new Request(url, request);
    }
    return caches.open("offline").then(function (cache) {
        return fetch(request).then(function (response) {
            return cache.put(request, response);
        }).catch(function(error) {
            console.log('Error al agregar al cache:', error);
        });
    });
};


const returnFromCache = function (request) {
    return caches.open("offline").then(function (cache) {
        return cache.match(request).then(function (matching) {
            if (!matching || matching.status === 404) {
                return cache.match("offline.html");
            } else {
                return matching;
            }
        });
    });
};

self.addEventListener("fetch", function (event) {
    // Solo manejar solicitudes GET
    if (event.request.method !== 'GET') {
        return;
    }
    
    const requestUrl = event.request.url;
    
    // No interceptar solicitudes a rutas dinámicas que puedan redirigir
    // Solo manejar archivos estáticos y recursos
    const url = new URL(requestUrl);
    const pathname = url.pathname;
    
    // Excluir rutas de la aplicación que puedan redirigir
    if (pathname === '/' || pathname.startsWith('/login') || pathname.startsWith('/register') || 
        pathname.startsWith('/dashboard') || pathname.startsWith('/reparaciones') ||
        pathname.startsWith('/clientes') || pathname.startsWith('/equipos') ||
        pathname.startsWith('/facturas') || pathname.startsWith('/api')) {
        // Dejar que el navegador maneje estas rutas normalmente
        return;
    }
    
    // Solo manejar archivos estáticos
    const isStaticFile = pathname.match(/\.(html|css|js|json|png|jpg|jpeg|gif|svg|ico|woff|woff2|ttf|eot)$/i);
    
    if (!isStaticFile) {
        return;
    }
    
    // Asegurar que todas las solicitudes usen HTTPS si estamos en HTTPS
    let finalUrl = requestUrl;
    if (requestUrl.startsWith('http://') && self.location.protocol === 'https:') {
        finalUrl = requestUrl.replace('http://', 'https://');
    }
    
    const finalRequest = new Request(finalUrl, {
        method: event.request.method,
        headers: event.request.headers,
        mode: 'cors',
        credentials: 'same-origin'
    });
    
    event.respondWith(
        checkResponse(finalRequest).catch(function () {
            return returnFromCache(finalRequest);
        })
    );
    
    // Solo cachear archivos estáticos
    if (isStaticFile) {
        event.waitUntil(addToCache(finalRequest).catch(function(error) {
            // Ignorar errores de cacheo
        }));
    }
});
