<aside class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 "
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-dark position-absolute end-0 top-0 d-xl-none"
            aria-hidden="true" id="iconSidenav" style="font-size: 1.5rem; opacity: 0.9; z-index: 10;"></i>
        <a class="navbar-brand m-0" href="{{ route('dashboard') }}">
            <img src="/img/logo-ct-dark.png" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-1 font-weight-bold">Martinez Devices</span>
        </a>
    </div>
    {{-- En móvil: usuario y cerrar sesión visibles al abrir el menú --}}
    @auth
    <div class="d-xl-none px-4 py-3 border-bottom border-sm-none" id="sidenav-mobile-user">
        <p class="text-xs text-body opacity-8 mb-1">Conectado como</p>
        <p class="nav-link-text mb-2 fw-bold text-dark">{{ auth()->user()->firstname ?? '' }} {{ auth()->user()->lastname ?? '' }}</p>
        <a class="btn btn-sm btn-outline-danger w-100" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
            <i class="ni ni-user-run me-1"></i> Cerrar Sesión
        </a>
    </div>
    @endauth
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse show w-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav" id="sidenav-scrollbar">
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Gestión</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ str_contains(request()->url(), 'reparaciones') ? 'active' : '' }}" href="{{ route('reparaciones.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-settings-gear-65 text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Reparaciones</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ str_contains(request()->url(), 'clientes') ? 'active' : '' }}" href="{{ route('clientes.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-info text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Clientes</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ str_contains(request()->url(), 'equipos') ? 'active' : '' }}" href="{{ route('equipos.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-laptop text-success text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Equipos</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ str_contains(request()->url(), 'contabilidad') ? 'active' : '' }}" href="{{ route('contabilidad.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-money-coins text-danger text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Contabilidad</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ str_contains(request()->url(), 'actividad-usuarios') ? 'active' : '' }}" href="{{ route('actividad-usuarios.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Actividad Usuarios</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ str_contains(request()->url(), 'facturas') && !str_contains(request()->url(), 'configuracion') ? 'active' : '' }}" href="{{ route('facturas.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-copy-04 text-success text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Facturas</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Configuración</h6>
            </li>
            @if(auth()->check() && auth()->user()->hasRole('administrador'))
            <li class="nav-item">
                <a class="nav-link {{ str_contains(request()->url(), 'usuarios') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-danger text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Usuarios del Sistema</span>
                </a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link {{ str_contains(request()->url(), 'facturas/configuracion') ? 'active' : '' }}" href="{{ route('facturas.configuracion') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-copy-04 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Config. Facturas</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ str_contains(request()->url(), 'sistema/configuracion') ? 'active' : '' }}" href="{{ route('sistema.configuracion') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-settings-gear-65 text-warning text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Configurar Sistema</span>
                </a>
            </li>
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Cuenta</h6>
            </li>
            @auth
            <li class="nav-item px-4 py-2">
                <span class="text-xs text-body opacity-8">Conectado como</span>
                <p class="nav-link-text ms-1 mb-0 fw-bold text-dark">{{ auth()->user()->firstname ?? '' }} {{ auth()->user()->lastname ?? '' }}</p>
            </li>
            @endauth
            <li class="nav-item">
                <a class="nav-link {{ Route::currentRouteName() == 'profile' ? 'active' : '' }}" href="{{ route('profile') }}">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Mi Perfil</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-sidebar').submit();">
                    <div
                        class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="ni ni-user-run text-danger text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Cerrar Sesión</span>
                </a>
                <form id="logout-form-sidebar" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
    <div class="sidenav-footer mx-3 ">
        <div class="card card-plain shadow-none" id="sidenavCard">
            <div class="card-body text-center p-3 w-100 pt-0">
                <div class="docs-info">
                    <h6 class="mb-0">Martinez Devices v1.0</h6>
                    <p class="text-xs font-weight-bold mb-0">Sistema de Gestión de Reparaciones</p>
                </div>
            </div>
        </div>
    </div>
</aside>
