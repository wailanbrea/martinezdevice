<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if(app()->environment('production'))
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; img-src 'self' data: https:; connect-src 'self' https://cdn.jsdelivr.net; worker-src 'self' blob:;">
    @endif
    <title>@yield('title', 'Martínez Service')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Intro.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/minified/introjs.min.css">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body>
    <script>
        // Cargar modo oscuro inmediatamente para evitar flash
        (function() {
            const savedMode = localStorage.getItem('darkMode');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            if (savedMode === 'enabled' || (!savedMode && prefersDark)) {
                document.documentElement.setAttribute('data-theme', 'dark');
                document.body.classList.add('dark-mode');
            }
        })();
    </script>
    <!-- Sidebar Overlay para móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <i class="bi bi-tools"></i>
                <span>Martínez Service</span>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        
        <nav class="sidebar-nav" data-intro="Menú principal de navegación. Aquí puedes acceder a todas las secciones del sistema." data-step="1">
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-intro="Dashboard: Visualiza estadísticas y resúmenes del sistema." data-step="2">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('clientes.index') }}" class="nav-item {{ request()->routeIs('clientes.*') ? 'active' : '' }}" data-intro="Clientes: Gestiona la información de tus clientes." data-step="3">
                <i class="bi bi-people"></i>
                <span>Clientes</span>
            </a>
            <a href="{{ route('equipos.index') }}" class="nav-item {{ request()->routeIs('equipos.*') ? 'active' : '' }}" data-intro="Entradas: Registra y gestiona los equipos de tus clientes." data-step="4">
                <i class="bi bi-laptop"></i>
                <span>Entradas</span>
            </a>
            <a href="{{ route('reparaciones.index') }}" class="nav-item {{ request()->routeIs('reparaciones.*') ? 'active' : '' }}" data-intro="Historial de Reparaciones: Administra las reparaciones y diagnósticos de equipos." data-step="5">
                <i class="bi bi-wrench-adjustable"></i>
                <span>Historial de Reparaciones</span>
            </a>
            <a href="{{ route('facturas.index') }}" class="nav-item {{ request()->routeIs('facturas.*') ? 'active' : '' }}" data-intro="Facturación: Crea y gestiona las facturas de los servicios realizados." data-step="6">
                <i class="bi bi-receipt"></i>
                <span>Facturación</span>
            </a>
            <a href="{{ route('contabilidad.index') }}" class="nav-item {{ request()->routeIs('contabilidad.*') ? 'active' : '' }}" data-intro="Contabilidad: Revisa reportes financieros y estadísticas de ingresos." data-step="7">
                <i class="bi bi-calculator"></i>
                <span>Contabilidad</span>
            </a>
            @if(auth()->user()->tieneRol('administrador'))
            <a href="{{ route('usuarios.index') }}" class="nav-item {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" data-intro="Usuarios y Roles: Gestiona los usuarios del sistema y sus permisos." data-step="8">
                <i class="bi bi-person-gear"></i>
                <span>Usuarios y Roles</span>
            </a>
            @endif
        </nav>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <div class="user-details">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <span class="user-role">{{ Auth::user()->roles->first()->nombre ?? 'Usuario' }}</span>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="bi bi-list"></i>
                </button>
                <div class="search-bar">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Buscar...">
                </div>
            </div>
            <div class="header-right">
                <button class="header-btn" id="tutorialBtn" title="Ver tutorial">
                    <i class="bi bi-question-circle"></i>
                </button>
                <button class="dark-mode-toggle" id="darkModeToggle" title="Cambiar modo">
                    <i class="bi bi-moon-fill" id="darkModeIcon"></i>
                </button>
                <button class="header-btn" title="Notificaciones">
                    <i class="bi bi-bell"></i>
                    <span class="badge">3</span>
                </button>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="header-btn" title="Cerrar sesión">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="page-content">
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/minified/intro.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/tutorial.js') }}"></script>
    @stack('scripts')
</body>
</html>
