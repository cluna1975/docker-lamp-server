<?php
/**
 * LÓGICA DE LOGIN (PHP 8.2+)
 * ---------------------------------------------------------
 * Procesamiento de autenticación de usuarios.
 */

session_start();
require_once '../config.php';

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "⚠️ Por favor, ingresa un email válido.";
        $messageType = "error";
    } elseif (empty($password)) {
        $message = "⚠️ La contraseña es obligatoria.";
        $messageType = "error";
    } else {
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
        $message = "❌ Credenciales incorrectas.";
        $messageType = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Launch & Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <style>
        /* ESTILOS CSS (Para replicar el diseño de la imagen) */
        :root {
            --bg-color: #f0f2f5;
            --card-white: #ffffff;
            --card-blue: #7da0c8; /* El azul suave de la derecha */
            --btn-blue: #557096;
            --btn-orange: #ff7f50;
            --text-dark: #333;
            --text-white: #fff;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            display: flex;
            gap: 20px;
            max-width: 900px;
            width: 100%;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* Diseño de Tarjetas Base */
        .card {
            width: 350px;
            height: 600px;
            border-radius: 30px;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
            box-sizing: border-box;
        }

        /* --- TARJETA IZQUIERDA (Blanca) --- */
        .card-launch {
            background-color: var(--card-white);
            color: var(--text-dark);
        }

        .card-launch h2 {
            font-family: 'Georgia', serif; /* Serif para el título como en la imagen */
            font-size: 2rem;
            margin-top: 10px;
        }

        .graphic-rocket {
            width: 200px;
            height: 200px;
            margin: 20px 0;
        }

        .btn {
            width: 100%;
            padding: 15px;
            border-radius: 25px;
            border: none;
            font-weight: bold;
            font-size: 0.9rem;
            cursor: pointer;
            margin-bottom: 15px;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: transform 0.2s;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        
        .btn:hover { transform: scale(1.02); }

        .btn-create { background-color: var(--btn-blue); color: white; }
        .btn-signin { background-color: var(--btn-orange); color: white; }

        .terms { font-size: 0.7rem; color: #aaa; margin-top: auto; }

        /* --- TARJETA DERECHA (Azul - Formulario PHP) --- */
        .card-account {
            background-color: var(--card-blue);
            color: var(--text-white);
        }

        .card-account h2 {
            font-family: 'Georgia', serif;
            font-size: 2rem;
            margin-top: 10px;
        }

        .graphic-satellite {
            width: 150px;
            height: 150px;
            margin: 10px 0;
        }

        .input-group {
            width: 100%;
            margin-bottom: 15px;
            position: relative;
        }

        .input-field {
            width: 100%;
            padding: 15px 15px 15px 45px; /* Espacio para el icono */
            background: transparent;
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: 25px;
            color: white;
            outline: none;
            box-sizing: border-box;
        }

        .input-field::placeholder { color: rgba(255,255,255,0.8); }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.8);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            font-size: 0.85rem;
            margin-bottom: 20px;
            width: 100%;
        }

        .checkbox-group input { margin-right: 10px; }

        .btn-go {
            background-color: var(--btn-blue);
            color: white;
            width: 100%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .forgot {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            margin-top: 15px;
        }

        /* Feedback PHP Messages */
        .php-message {
            position: absolute;
            top: 10px;
            width: 90%;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            font-size: 0.8rem;
            z-index: 10;
        }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }

    </style>
</head>
<body>

<div class="container">
    
    <div class="card card-launch">
        <h2>Launch Your App</h2>
        
        <div class="graphic-rocket">
            <lottie-player src="https://assets5.lottiefiles.com/packages/lf20_fclga8fl.json" background="transparent" speed="1" loop autoplay></lottie-player>
        </div>

        <div style="width: 100%;">
            <a href="../users/crear.php" class="btn btn-create">Create Account</a>
            <a href="#" class="btn btn-signin" onclick="document.querySelector('.card-account form').scrollIntoView(); return false;">Sign In</a>
        </div>

        <div class="terms">Terms & Conditions</div>
    </div>

    <div class="card card-account">
        
        <?php if ($message): ?>
            <div class="php-message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <h2>Login</h2>
        
        <div class="graphic-satellite">
            <lottie-player src="https://assets2.lottiefiles.com/packages/lf20_jcikwtux.json" background="transparent" speed="1" loop autoplay></lottie-player>
        </div>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="width: 100%;">
            
            <div class="input-group">
                <i class="fa-regular fa-envelope input-icon"></i>
                <input type="email" name="email" class="input-field" placeholder="E-Mail" required>
            </div>

            <div class="input-group">
                <i class="fa-solid fa-lock input-icon"></i>
                <input type="password" name="password" class="input-field" placeholder="Password" required>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" name="keep_logged_in" id="keep">
                <label for="keep">Keep Me Logged In</label>
            </div>

            <button type="submit" class="btn btn-go">GO!</button>
        </form>

        <a href="#" class="forgot">Forgot Password?</a>
    </div>

</div>

</body>
</html>