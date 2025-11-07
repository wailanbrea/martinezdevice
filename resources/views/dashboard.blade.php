<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Martínez Service</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 800px;
            width: 100%;
        }

        h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-size: 2.5em;
        }

        .welcome {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.2em;
        }

        .user-info {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 5px;
        }

        .user-info h3 {
            color: #667eea;
            margin-bottom: 10px;
        }

        .user-info p {
            color: #4a5568;
            margin: 5px 0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .action-btn {
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            padding: 15px 20px;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
        }

        .action-btn:hover {
            background: #667eea;
            color: white;
        }

        .logout-form {
            text-align: center;
        }

        .logout-btn {
            background: #e53e3e;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: #c53030;
        }

        .success-message {
            background: #48bb78;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-message">
            ✓ ¡Login exitoso! Bienvenido al sistema
        </div>

        <h1>Dashboard</h1>
        <p class="welcome">Bienvenido al Sistema de Gestión Martínez Service</p>

        <div class="user-info">
            <h3>Información del Usuario</h3>
            <p><strong>Nombre:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Última sesión:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>

        <div class="stats">
            <div class="stat-card">
                <h3>✓</h3>
                <p>Sistema Activo</p>
            </div>
            <div class="stat-card">
                <h3>🔒</h3>
                <p>Sesión Segura</p>
            </div>
            <div class="stat-card">
                <h3>🚀</h3>
                <p>Funcionando</p>
            </div>
        </div>

        <h3 style="color: #667eea; margin-bottom: 15px;">Acciones Rápidas</h3>
        <div class="quick-actions">
            <a href="#" class="action-btn">👥 Clientes</a>
            <a href="#" class="action-btn">🔧 Equipos</a>
            <a href="#" class="action-btn">📋 Reparaciones</a>
            <a href="#" class="action-btn">💰 Facturas</a>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">Cerrar Sesión</button>
        </form>
    </div>
</body>
</html>

