<?php
$error = $_GET['error'] ?? 'desconocido';

$errores = [
    '1045' => [
        'mensaje' => 'Error de autenticación: Credenciales incorrectas',
        'icon' => '🔒'
    ],
    '1049' => [
        'mensaje' => 'Base de datos no encontrada',
        'icon' => '📁'
    ],
    '2002' => [
        'mensaje' => 'Servidor MySQL no disponible',
        'icon' => '📶'
    ],
    '2003' => [
        'mensaje' => 'No se puede conectar al servidor MySQL',
        'icon' => '🚫'
    ],
    '1040' => [
        'mensaje' => 'Demasiadas conexiones activas',
        'icon' => '🚦'
    ],
    '2006' => [
        'mensaje' => 'Conexión perdida con el servidor',
        'icon' => '🔌'
    ],
    'general' => [
        'mensaje' => 'Error general de conexión',
        'icon' => '⚠️'
    ],
    'desconocido' => [
        'mensaje' => 'Error desconocido',
        'icon' => '❓'
    ]
];

$info = $errores[$error] ?? $errores['desconocido'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de Base de Datos</title>

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .icon-container {
            margin: 0 auto 30px;
        }
        .error-icon {
            font-size: 120px;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-20px); }
            60% { transform: translateY(-10px); }
        }
        h1 {
            color: #e74c3c;
            font-size: 2.5em;
            margin-bottom: 20px;
            font-weight: 300;
        }
        .message {
            color: #555;
            font-size: 1.2em;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        .error-code {
            background: #f8f9fa;
            color: #6c757d;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            font-family: monospace;
            font-size: 0.9em;
        }
        .retry-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 1em;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .retry-btn:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-container">
            <div class="error-icon"><?= $info['icon'] ?></div>
        </div>
        <h1>¡Oops! Error de Conexión</h1>
        <p class="message"><?= htmlspecialchars($info['mensaje']) ?></p>
        <div class="error-code">Código: <?= htmlspecialchars($error) ?></div>
        <button class="retry-btn" onclick="history.back()">Volver a intentar</button>
    </div>
</body>
</html>