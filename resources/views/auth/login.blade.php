<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Martínez Service</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --login-bg-light: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --login-bg-dark: linear-gradient(135deg, #1a1d29 0%, #2d3238 100%);
            --login-card-bg-light: #ffffff;
            --login-card-bg-dark: #212529;
            --login-text-light: #344767;
            --login-text-dark: #e9ecef;
            --login-subtext-light: #8392ab;
            --login-subtext-dark: #adb5bd;
            --login-border-light: #e9ecef;
            --login-border-dark: #495057;
        }

        [data-theme="dark"] body,
        body.dark-mode {
            --login-bg: var(--login-bg-dark);
            --login-card-bg: var(--login-card-bg-dark);
            --login-text: var(--login-text-dark);
            --login-subtext: var(--login-subtext-dark);
            --login-border: var(--login-border-dark);
        }

        body {
            --login-bg: var(--login-bg-light);
            --login-card-bg: var(--login-card-bg-light);
            --login-text: var(--login-text-light);
            --login-subtext: var(--login-subtext-light);
            --login-border: var(--login-border-light);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--login-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            transition: background 0.3s ease;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: var(--login-card-bg);
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease;
            transition: background 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            background: linear-gradient(135deg, #5e72e4 0%, #4c63d2 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .login-logo {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            opacity: 0.9;
            font-size: 0.875rem;
        }

        .login-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--login-text);
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--login-subtext);
            font-size: 1.125rem;
            transition: color 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 1px solid var(--login-border);
            border-radius: 0.75rem;
            font-size: 0.875rem;
            background: var(--login-card-bg);
            color: var(--login-text);
            transition: all 0.3s ease;
        }

        .form-control::placeholder {
            color: var(--login-subtext);
        }

        .form-control:focus {
            outline: none;
            border-color: #5e72e4;
            box-shadow: 0 0 0 3px rgba(94, 114, 228, 0.1);
        }

        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .form-check-label {
            font-size: 0.875rem;
            color: var(--login-subtext);
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #5e72e4 0%, #7889e8 100%);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(94, 114, 228, 0.4);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .alert-danger {
            background: rgba(245, 54, 92, 0.1);
            color: #f5365c;
            border: 1px solid rgba(245, 54, 92, 0.3);
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        [data-theme="dark"] .alert-danger,
        body.dark-mode .alert-danger {
            background: rgba(245, 54, 92, 0.15);
            border-color: rgba(245, 54, 92, 0.4);
        }

        .invalid-feedback {
            display: block;
            color: #f56565;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        .is-invalid {
            border-color: #f56565;
        }
    </style>
    <script>
        // Cargar modo oscuro inmediatamente para evitar flash
        (function() {
            // Verificar que el DOM esté listo
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDarkMode);
            } else {
                initDarkMode();
            }
            
            function initDarkMode() {
                const savedMode = localStorage.getItem('darkMode');
                const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                if (savedMode === 'enabled' || (!savedMode && prefersDark)) {
                    if (document.documentElement) {
                        document.documentElement.setAttribute('data-theme', 'dark');
                    }
                    if (document.body) {
                        document.body.classList.add('dark-mode');
                    }
                    // Esperar a que el elemento esté disponible
                    setTimeout(function() {
                        const icon = document.getElementById('darkModeIconLogin');
                        if (icon) {
                            icon.className = 'bi bi-sun-fill';
                        }
                    }, 0);
                }
            }
        })();

        // Dark mode toggle en login
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeToggle = document.getElementById('darkModeToggleLogin');
            const darkModeIcon = document.getElementById('darkModeIconLogin');
            
            if (darkModeToggle && document.body) {
                darkModeToggle.addEventListener('click', function() {
                    const html = document.documentElement;
                    const body = document.body;
                    
                    if (!html || !body) return;
                    
                    const isDark = html.getAttribute('data-theme') === 'dark' || body.classList.contains('dark-mode');
                    
                    if (!isDark) {
                        html.setAttribute('data-theme', 'dark');
                        body.classList.add('dark-mode');
                        if (darkModeIcon) darkModeIcon.className = 'bi bi-sun-fill';
                        localStorage.setItem('darkMode', 'enabled');
                    } else {
                        html.removeAttribute('data-theme');
                        body.classList.remove('dark-mode');
                        if (darkModeIcon) darkModeIcon.className = 'bi bi-moon-fill';
                        localStorage.setItem('darkMode', 'disabled');
                    }
                });
            }
        });
    </script>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header" style="position: relative;">
                <button class="dark-mode-toggle-login" id="darkModeToggleLogin" title="Cambiar modo" style="position: absolute; top: 1rem; right: 1rem; background: rgba(255,255,255,0.2); border: none; color: white; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; z-index: 10;">
                    <i class="bi bi-moon-fill" id="darkModeIconLogin"></i>
                </button>
                <div class="login-logo">
                    <i class="bi bi-tools"></i>
                </div>
                <h1 class="login-title">Martínez Service</h1>
                <p class="login-subtitle">Sistema de Gestión y Reparación</p>
            </div>
            
            <div class="login-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0; padding-left: 1.25rem;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <div class="input-group">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="usuario@ejemplo.com"
                                   required 
                                   autofocus>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <div class="input-group">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   placeholder="••••••••"
                                   required>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Iniciar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
