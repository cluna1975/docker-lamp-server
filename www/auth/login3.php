<?php
/**
 * Arquitectura: Single File Component para Login
 * Autor: Tu Experto PHP (Gemini)
 * Descripción: Implementación pixel-perfect de la imagen provista.
 */

session_start();
require_once '../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (!empty($email) && !empty($password)) {
        $conn = conectar_db();
        $stmt = $conn->prepare("SELECT id, email, password_hash, first_name, role, status FROM users WHERE email = ? AND status = 'active'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['first_name'];
                $_SESSION['user_role'] = $user['role'];
                header('Location: ../users/index.php');
                exit;
            }
        }
        $error = "Credenciales incorrectas";
    } else {
        $error = "Por favor completa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hello World</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- RESET & BASE --- */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        body {
            /* Fondo general similar a la imagen (púrpura oscuro difuminado) */
            background: linear-gradient(135deg, #2c003e 0%, #511a54 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* --- TARJETA PRINCIPAL --- */
        .login-container {
            background-color: #fff;
            width: 100%;
            max-width: 900px;
            height: 550px;
            border-radius: 10px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
            display: flex;
            overflow: hidden; /* Para que la imagen no se salga de los bordes redondeados */
        }

        /* --- COLUMNA IZQUIERDA (IMAGEN) --- */
        .left-panel {
            flex: 1;
            /* Imagen de un paisaje synthwave similar */
            background: url('https://images.unsplash.com/photo-1534234828569-1f92e597c459?q=80&w=1000&auto=format&fit=crop') no-repeat center center;
            background-size: cover;
            position: relative;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
        }

        /* Overlay oscuro para que el texto se lea mejor sobre la imagen */
        .left-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(44, 0, 62, 0.6));
            z-index: 1;
        }

        .left-content {
            position: relative;
            z-index: 2;
        }

        .left-content h1 {
            font-size: 3.5rem;
            line-height: 1.1;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .left-content p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 40px;
            max-width: 300px;
            line-height: 1.5;
        }

        .social-buttons {
            display: flex;
            gap: 15px;
        }

        .btn-social {
            padding: 8px 20px;
            border-radius: 20px; /* Bordes redondeados como en la foto */
            text-decoration: none;
            color: white;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: opacity 0.3s;
        }
        
        .btn-social:hover { opacity: 0.8; }
        .fb { background-color: #3b5998; }
        .tw { background-color: #1da1f2; }

        /* --- COLUMNA DERECHA (FORMULARIO) --- */
        .right-panel {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .right-panel h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 40px;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 30px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 5px;
        }

        /* ESTILO CLAVE: Input solo con borde inferior */
        .form-control {
            width: 100%;
            border: none;
            border-bottom: 2px solid #ddd;
            padding: 10px 0;
            font-size: 1rem;
            color: #333;
            outline: none;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-bottom-color: #1a237e; /* Azul oscuro al enfocar */
        }

        .btn-submit {
            background-color: #1a237e; /* Azul oscuro de la imagen */
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px; /* Botón píldora */
            font-size: 1rem;
            cursor: pointer;
            float: right; /* Alinear a la derecha */
            box-shadow: 0 4px 10px rgba(26, 35, 126, 0.3);
            transition: transform 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
        }

        .register-link {
            margin-top: 50px;
            text-align: center; /* Centrado como en la imagen? No, en la imagen parece a la izquierda, pero centrado queda mejor */
            font-size: 0.9rem;
            color: #666;
        }

        .register-link a {
            color: #6a1b9a; /* Color morado */
            text-decoration: none;
            font-weight: 600;
        }

        /* Mensajes de error/éxito */
        .alert { padding: 10px; margin-bottom: 20px; border-radius: 5px; font-size: 0.9rem; }
        .alert-error { background: #fdecea; color: #dc3545; }
        .alert-success { background: #d4edda; color: #155724; }

        /* Responsive para móviles */
        @media (max-width: 768px) {
            .login-container { flex-direction: column; height: auto; }
            .left-panel { min-height: 250px; padding: 30px; }
            .right-panel { padding: 30px; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="left-panel">
            <div class="left-content">
                <h1>Hello<br>World.</h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.</p>
                
                <div class="social-buttons">
                    <a href="#" class="btn-social fb"><i class="fab fa-facebook-f"></i> Facebook</a>
                    <a href="#" class="btn-social tw"><i class="fab fa-twitter"></i> Twitter</a>
                </div>
            </div>
        </div>

        <div class="right-panel">
            <h2>Login</h2>

            <?php if($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>

                <div style="overflow: hidden;"> <button type="submit" class="btn-submit">Enviar</button>
                </div>

                <div class="register-link">
                    ¿No tienes cuenta? <a href="#">Registrate</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>