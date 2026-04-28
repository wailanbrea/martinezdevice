<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl
        {{ str_contains(Request::url(), 'virtual-reality') == true ? ' mt-3 mx-3 bg-primary' : '' }}" id="navbarBlur"
        data-scroll="false">
    <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb" class="flex-grow-1">
            <h6 class="font-weight-bolder text-white mb-0">{{ $title }}</h6>
        </nav>

        {{-- En móvil/tablet: usuario y cerrar sesión siempre visibles (el collapse del navbar está oculto sin toggler) --}}
        <div class="d-lg-none d-flex align-items-center gap-2 ms-auto me-2" id="navbarMobileUser">
            @auth
            <span class="text-white text-sm font-weight-normal d-flex align-items-center px-2" title="{{ auth()->user()->firstname ?? '' }} {{ auth()->user()->lastname ?? '' }}">
                <i class="fa fa-user-circle me-1 opacity-8"></i>
                <span class="text-nowrap text-truncate" style="max-width: 100px;">{{ auth()->user()->firstname ?? '' }} {{ auth()->user()->lastname ?? '' }}</span>
            </span>
            <form role="form" method="post" action="{{ route('logout') }}" id="logout-form-topnav" class="d-inline">
                @csrf
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form-topnav').submit();"
                    class="nav-link text-white font-weight-bold px-2 py-1 d-flex align-items-center"
                    aria-label="Cerrar sesión"
                    title="Cerrar sesión">
                    <i class="fa fa-sign-out-alt"></i>
                </a>
            </form>
            @endauth
        </div>

        <div class="d-xl-none d-flex align-items-center me-1" id="navbarHamburgerWrapper">
            <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav" aria-label="Abrir menú lateral"
                role="button">
                <div class="sidenav-toggler-inner">
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                </div>
            </a>
        </div>

        {{-- Escritorio: usuario y cerrar sesión (visibles en lg y superiores) --}}
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <ul class="navbar-nav ms-auto justify-content-end align-items-center">
                @auth
                <li class="nav-item">
                    <span class="nav-link text-white text-sm font-weight-normal px-2">
                        <i class="fa fa-user-circle me-1 opacity-8"></i>
                        <span class="d-none d-sm-inline">{{ auth()->user()->firstname ?? '' }} {{ auth()->user()->lastname ?? '' }}</span>
                    </span>
                </li>
                @endauth
                <li class="nav-item d-flex align-items-center">
                    <form role="form" method="post" action="{{ route('logout') }}" id="logout-form">
                        @csrf
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="nav-link text-white font-weight-bold px-0">
                            <i class="fa fa-sign-out-alt me-sm-1"></i>
                            <span class="d-sm-inline d-none">Cerrar Sesión</span>
                        </a>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->
