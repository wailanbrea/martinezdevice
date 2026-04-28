@extends('layouts.app')

@section('content')
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                @include('layouts.navbars.guest.navbar')
            </div>
        </div>
    </div>

    <style>
        .login-card-modern {
            border-radius: 1.25rem;
            border: none;
            box-shadow: 0 20px 45px rgba(32, 58, 147, 0.18);
        }

        .login-card-modern .form-control {
            border-radius: 0.75rem;
            border: 1px solid #d8dce7;
            padding: 0.9rem 1rem;
        }

        .login-card-modern .form-control:focus {
            border-color: #5e72e4;
            box-shadow: 0 0 0 0.25rem rgba(94, 114, 228, 0.15);
        }

        .login-feature-pill {
            background: rgba(94, 114, 228, 0.1);
            color: #3b4cca;
            border-radius: 999px;
            padding: 0.35rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        @media (max-width: 991.98px) {
            .auth-hero-content {
                min-height: 240px;
            }
        }
    </style>

    <main class="main-content mt-0">
        <section class="pt-4 pt-lg-0">
            <div class="page-header min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #f8f9fc 0%, #eef1ff 50%, #f8f9fc 100%);">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
                            <div class="card login-card-modern mt-7 mt-md-0">
                                <div class="card-body p-4 p-lg-5">
                                    <div class="text-center mb-4">
                                        <span class="login-feature-pill mb-2">
                                            <i class="fas fa-bolt"></i> Martinez Devices
                                        </span>
                                        <h4 class="fw-bold text-primary mb-2">Quien anda ahi?</h4>
                                        <p class="text-muted mb-0">
                                            Ingresa con tus credenciales para continuar gestionando servicios y reparaciones.
                                        </p>
                                    </div>

                                    <form method="POST" action="{{ route('login.perform') }}">
                                        @csrf

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Correo electronico</label>
                                            <input
                                                type="email"
                                                name="email"
                                                id="email"
                                                class="form-control form-control-lg"
                                                placeholder="usuario@empresa.com"
                                                value="{{ old('email') }}"
                                                required
                                                autofocus
                                                autocomplete="username"
                                            >
                                            @error('email') <p class="text-danger text-xs pt-1 mb-0">{{ $message }}</p>@enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label for="password" class="form-label mb-0">Contrasena</label>
                                                <a href="{{ route('reset-password') }}" class="text-sm text-primary fw-semibold">
                                                    Olvidaste tu contrasena?
                                                </a>
                                            </div>
                                            <input
                                                type="password"
                                                name="password"
                                                id="password"
                                                class="form-control form-control-lg"
                                                placeholder="********"
                                                required
                                                autocomplete="current-password"
                                            >
                                            @error('password') <p class="text-danger text-xs pt-1 mb-0">{{ $message }}</p>@enderror
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="form-check form-switch">
                                                <input
                                                    class="form-check-input"
                                                    name="remember"
                                                    type="checkbox"
                                                    id="rememberMe"
                                                    value="1"
                                                    @checked(old('remember'))
                                                >
                                                <label class="form-check-label" for="rememberMe">Mantener sesion iniciada</label>
                                            </div>
                                            @if (Route::has('register'))
                                                <a class="text-sm text-primary fw-semibold" href="{{ route('register') }}">
                                                    Crear cuenta
                                                </a>
                                            @endif
                                        </div>

                                        <div class="d-grid mb-4">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                Ingresar
                                            </button>
                                        </div>

                                        <div class="small text-muted text-center">
                                            Accede para revisar reparaciones, clientes y contabilidad en un solo lugar.
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
