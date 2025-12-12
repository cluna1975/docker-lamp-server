<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Variables para mensajes
$mensaje = '';
$tipo_mensaje = '';
$errores = [];

// PROCESAMIENTO DEL FORMULARIO POST
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validación del campo nombre
    if(empty(trim($_POST['nombre']))) {
        $errores['nombre'] = 'El nombre es obligatorio';
    } elseif(strlen(trim($_POST['nombre'])) < 2) {
        $errores['nombre'] = 'El nombre debe tener al menos 2 caracteres';
    }
    
    // Validación del campo apellido
    if(empty(trim($_POST['apellido']))) {
        $errores['apellido'] = 'El apellido es obligatorio';
    } elseif(strlen(trim($_POST['apellido'])) < 2) {
        $errores['apellido'] = 'El apellido debe tener al menos 2 caracteres';
    }
    
    // Si no hay errores, procesar el formulario
    if(empty($errores)) {
        $nombre = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
        $apellido = htmlspecialchars(trim($_POST['apellido']), ENT_QUOTES, 'UTF-8');
        
        // Aquí iría la lógica de firma XMLDSig simulada
        $mensaje = "✓ Formulario enviado correctamente para: {$nombre} {$apellido}";
        $tipo_mensaje = 'success';
        
        // Limpiar campos después del envío exitoso
        $_POST = [];
    } else {
        $tipo_mensaje = 'error';
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
    <meta name="description" content="Formulario de firma digital XMLDSig simulada con validación avanzada">
    <title>Firma Digital XMLDSig - Formulario Seguro</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            /* Color Palette - Vibrant & Premium */
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --error-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            
            --primary-color: #667eea;
            --primary-dark: #5568d3;
            --secondary-color: #764ba2;
            
            --text-primary: #1a202c;
            --text-secondary: #4a5568;
            --text-light: #718096;
            
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-card: rgba(255, 255, 255, 0.95);
            
            --border-radius: 16px;
            --border-radius-lg: 24px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(118, 75, 162, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 40% 90%, rgba(79, 172, 254, 0.1) 0%, transparent 50%);
            animation: drift 20s ease-in-out infinite;
            pointer-events: none;
        }
        
        @keyframes drift {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(30px, -30px) rotate(5deg); }
        }
        
        .contenedor {
            max-width: 520px;
            width: 100%;
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            padding: 48px 40px;
            border-radius: var(--border-radius-lg);
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
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
        
        /* Decorative gradient border */
        .contenedor::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--primary-gradient);
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .icon-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .icon-container svg {
            width: 40px;
            height: 40px;
            fill: white;
        }
        
        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }
        
        .subtitle {
            color: var(--text-secondary);
            font-size: 15px;
            font-weight: 500;
            line-height: 1.6;
        }
        
        /* Messages */
        .mensaje {
            padding: 16px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 28px;
            font-size: 14px;
            font-weight: 500;
            animation: slideIn 0.4s ease-out;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .mensaje.success {
            background: linear-gradient(135deg, #d4f8e8 0%, #bef3e0 100%);
            color: #047857;
            border-left: 4px solid #10b981;
        }
        
        .mensaje.error {
            background: linear-gradient(135deg, #ffe5e5 0%, #ffd4d4 100%);
            color: #c53030;
            border-left: 4px solid #ef4444;
        }
        
        form {
            width: 100%;
        }
        
        .form-group {
            margin-bottom: 24px;
            position: relative;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.2px;
            transition: var(--transition);
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            opacity: 0.5;
            transition: var(--transition);
            pointer-events: none;
        }
        
        input[type="text"],
        input[type="number"],
        input[type="date"],
        input[type="email"] {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: var(--border-radius);
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            background: white;
            transition: var(--transition);
            outline: none;
        }
        
        input[type="text"]::placeholder,
        input[type="number"]::placeholder,
        input[type="date"]::placeholder,
        input[type="email"]::placeholder {
            color: #a0aec0;
        }
        
        input[type="text"]:hover,
        input[type="number"]:hover,
        input[type="date"]:hover,
        input[type="email"]:hover {
            border-color: #cbd5e0;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="email"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        input[type="text"]:focus + .input-icon,
        input[type="number"]:focus + .input-icon,
        input[type="date"]:focus + .input-icon,
        input[type="email"]:focus + .input-icon {
            opacity: 1;
            fill: var(--primary-color);
        }
        
        .error-message {
            color: #e53e3e;
            font-size: 13px;
            margin-top: 8px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .form-group.has-error input {
            border-color: #fc8181;
            background: #fff5f5;
        }
        
        .form-group.has-error label {
            color: #e53e3e;
        }
        
        input[type="submit"] {
            width: 100%;
            background: var(--primary-gradient);
            color: white;
            padding: 16px 32px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 12px;
        }
        
        input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.5);
        }
        
        input[type="submit"]:active {
            transform: translateY(0);
            box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
        }
        
        /* Security Badge */
        .security-badge {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }
        
        .security-badge p {
            color: var(--text-light);
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .security-icon {
            width: 16px;
            height: 16px;
            fill: #10b981;
        }
        
        /* Responsive */
        @media (max-width: 640px) {
            .contenedor {
                padding: 32px 24px;
            }
            
            h1 {
                font-size: 26px;
            }
            
            .subtitle {
                font-size: 14px;
            }
        }
        
        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }
    </style>    
</head>
<body>
    <div class="contenedor">
        <div class="header">
            <div class="icon-container">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1>Firma Digital XMLDSig</h1>
            <p class="subtitle">Complete el formulario para simular el proceso de firma digital segura</p>
        </div>
        
        <?php if(!empty($mensaje)): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php if($tipo_mensaje == 'success'): ?>
                    <svg style="width: 20px; height: 20px; fill: currentColor;" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                <?php else: ?>
                    <svg style="width: 20px; height: 20px; fill: currentColor;" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                <?php endif; ?>
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="post" novalidate>
            <div class="form-group <?php echo isset($errores['nombre']) ? 'has-error' : ''; ?>">
                <label for="nombre">Nombre *</label>
                <div class="input-wrapper">
                    <input 
                        type="text" 
                        id="nombre"
                        name="nombre" 
                        placeholder="Ingrese su nombre"
                        value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>"
                        required
                    >
                    <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <?php if(isset($errores['nombre'])): ?>
                    <div class="error-message">
                        <svg style="width: 14px; height: 14px; fill: currentColor;" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <?php echo $errores['nombre']; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group <?php echo isset($errores['apellido']) ? 'has-error' : ''; ?>">
                <label for="apellido">Apellido *</label>
                <div class="input-wrapper">
                    <input 
                        type="text" 
                        id="apellido"
                        name="apellido" 
                        placeholder="Ingrese su apellido"
                        value="<?php echo isset($_POST['apellido']) ? htmlspecialchars($_POST['apellido']) : ''; ?>"
                        required
                    >
                    <svg class="input-icon" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <?php if(isset($errores['apellido'])): ?>
                    <div class="error-message">
                        <svg style="width: 14px; height: 14px; fill: currentColor;" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <?php echo $errores['apellido']; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <input type="submit" value="Firmar Documento">
        </form>
        
        <div class="security-badge">
            <p>
                <svg class="security-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Conexión segura y encriptada
            </p>
        </div>
    </div>
</body>
</html>
