<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, user-scalable=yes, shrink-to-fit=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#5e72e4">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/apple-icon.png">
    <link rel="apple-touch-icon" sizes="192x192" href="{{ asset('logo.png') }}">
    <link rel="apple-touch-icon" sizes="512x512" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/png" href="/img/favicon.png">
    <title>
        Martinez Devices - Sistema de Gestión de Reparaciones
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
    <!-- Nucleo Icons -->
    <link href="/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="/assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <!-- CSS Files -->
    <link id="pagestyle" href="/assets/css/argon-dashboard.css" rel="stylesheet" />
    <!-- Design System CSS -->
    <link href="/assets/css/design-system.css" rel="stylesheet" />
    <!-- Custom Mobile & Responsive CSS -->
    <link href="/assets/css/custom-mobile.css" rel="stylesheet" />
    <link href="/assets/css/custom-responsive.css" rel="stylesheet" />
    
    @stack('css')
</head>

<body class="{{ $class ?? '' }}">

    @guest
        @yield('content')
    @endguest

    @auth
        @if (in_array(request()->route()->getName(), ['sign-in-static', 'sign-up-static', 'login', 'register', 'recover-password', 'rtl', 'virtual-reality']))
            @yield('content')
        @else
            @if (!in_array(request()->route()->getName(), ['profile', 'profile-static']))
                <div class="min-height-300 bg-primary position-absolute w-100"></div>
            @elseif (in_array(request()->route()->getName(), ['profile-static', 'profile']))
                <div class="position-absolute w-100 min-height-300 top-0" style="background-image: url('https://raw.githubusercontent.com/creativetimofficial/public-assets/master/argon-dashboard-pro/assets/img/profile-layout-header.jpg'); background-position-y: 50%;">
                    <span class="mask bg-primary opacity-6"></span>
                </div>
            @endif
            @include('layouts.navbars.auth.sidenav')
                <main class="main-content border-radius-lg">
                    @yield('content')
                </main>
        @endif
    @endauth

    <button id="pwa-install-btn" class="btn btn-primary btn-sm shadow-lg" style="display:none; position: fixed; bottom: 1.5rem; right: 1.5rem; z-index: 1300; border-radius: 999px; padding: 0.75rem 1.5rem;">
        Instalar Martinez Devices
    </button>


    <!--   Core JS Files - Optimizado con defer para carga asíncrona -->
    <script src="/assets/js/core/popper.min.js" defer></script>
    <script src="/assets/js/core/bootstrap.min.js" defer></script>
    <script src="/assets/js/plugins/perfect-scrollbar.min.js" defer></script>
    <script src="/assets/js/plugins/smooth-scrollbar.min.js" defer></script>

    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="/assets/js/argon-dashboard.js" defer></script>

    <!-- Custom Navigation Handler (must load after argon-dashboard) -->
    <script src="/assets/js/custom-nav.js" defer></script>
    
    <script src="{{ asset('pwa-install.js') }}" defer></script>
    <script>
        // Service Worker - registro silencioso (sin logs en producción)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                const swPath = '/sw.js';
                navigator.serviceWorker.register(swPath)
                    .catch(function (error) {
                        // Solo registrar errores críticos (no HTTPS/HTTP mismatch)
                        if (!error.message.includes('origin') && !error.message.includes('SecurityError')) {
                            console.error('SW Error:', error);
                        }
                    });
            });
        }
    </script>
    
    @stack('js')
    
    <!-- Script para ocultar mensajes de éxito automáticamente después de 10 segundos -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Buscar todos los mensajes de éxito
            const successAlerts = document.querySelectorAll('.alert-success, .alert.alert-success');
            successAlerts.forEach(function(alert) {
                // Ocultar después de 10 segundos
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease-out';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 10000);
            });
        });
    </script>
    
    <!-- Script global para prevenir doble envío de formularios -->
    <script>
        (function() {
            // Prevenir doble envío de formularios
            document.addEventListener('DOMContentLoaded', function() {
                const forms = document.querySelectorAll('form');
                
                forms.forEach(function(form) {
                    // Excluir formularios de configuración (evitar que deshabilitar botón cancele el envío)
                    if (form.hasAttribute('data-skip-double-submit')) return;
                    
                    let isSubmitting = false;
                    
                    form.addEventListener('submit', function(e) {
                        if (isSubmitting) {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        }
                        
                        isSubmitting = true;
                        
                        // Deshabilitar botones DESPUÉS de que el formulario inicie el envío (evita cancelar envío en algunos navegadores)
                        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                        setTimeout(function() {
                        submitButtons.forEach(function(btn) {
                            btn.disabled = true;
                            btn.setAttribute('data-original-text', btn.textContent || btn.value);
                            if (btn.tagName === 'BUTTON') {
                                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
                            } else {
                                btn.value = 'Procesando...';
                            }
                        });
                        }, 100);
                        
                        // Si hay un error de validación, re-habilitar después de 2 segundos
                        setTimeout(function() {
                            if (!form.checkValidity()) {
                                isSubmitting = false;
                                submitButtons.forEach(function(btn) {
                                    btn.disabled = false;
                                    if (btn.tagName === 'BUTTON') {
                                        btn.innerHTML = btn.getAttribute('data-original-text') || 'Enviar';
                                    } else {
                                        btn.value = btn.getAttribute('data-original-text') || 'Enviar';
                                    }
                                });
                            }
                        }, 2000);
                    });
                });
            });
            
            // Prevenir múltiples clics en botones que no son submit pero que hacen acciones
            document.addEventListener('click', function(e) {
                const button = e.target.closest('button, a.btn');
                if (button && !button.hasAttribute('data-allow-multiple')) {
                    if (button.classList.contains('processing')) {
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                    
                    // Si es un botón que hace una acción importante, marcar como procesando
                    if (button.type === 'submit' || 
                        button.classList.contains('btn-danger') || 
                        button.classList.contains('btn-primary') ||
                        button.getAttribute('onclick')) {
                        button.classList.add('processing');
                        setTimeout(function() {
                            button.classList.remove('processing');
                        }, 3000);
                    }
                }
            });
        })();
    </script>
</body>

</html>

