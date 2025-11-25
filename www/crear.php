<?php
session_start();
require_once 'config.php';

$mensaje_info = null;

// --- LÓGICA PARA CREAR (CREATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $conn = conectar_db();
    $nombre = trim($_POST['nombre']);
    $email  = trim($_POST['email']);
    $errors = [];

    // Validaciones
    if (empty($nombre)) $errors[] = "El nombre es obligatorio.";
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre)) $errors[] = "El nombre solo puede contener letras y espacios.";
    if (empty($email)) $errors[] = "El email es obligatorio.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El formato del email no es válido.";
    
    if (!empty($errors)) {
        $mensaje_info = ['tipo' => 'error', 'texto' => implode('<br>', $errors)];
    } else {
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $nombre, $email);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => "✅ ¡Usuario $nombre registrado con éxito!"];
            header("Location: index.php"); // Redirigir a la lista
            exit();
        } else {
            if ($conn->errno == 1062) {
                $mensaje_info = ['tipo' => 'error', 'texto' => "❌ El email '$email' ya está registrado."];
            } else {
                $mensaje_info = ['tipo' => 'error', 'texto' => "❌ Error del servidor: " . $stmt->error];
            }
        }
        $stmt->close();
    }
    $conn->close();
}

// Mensaje de la sesión (por si hay una redirección con error)
if (isset($_SESSION['mensaje'])) {
    $mensaje_info = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { background: white; padding: 30px 40px; border-radius: 10px; width: 100%; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin: 20px auto; }
        h2 { margin-bottom: 25px; color: #333; text-align: center; }
        .form-control { margin-bottom: 20px; text-align: left; }
        .input-group { position: relative; }
        .input-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa; }
        .input-group input:focus + i { color: #007bff; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: bold; }
        input { width: 100%; padding: 12px 12px 12px 45px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 5px; }
        input:focus { border-color: #007bff; outline: none; }
        input.invalid { border-color: #dc3545 !important; }
        .error-text { color: #dc3545; font-size: 0.875em; display: none; margin-top: 5px; height: 1em; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 5px; font-size: 16px; font-weight: bold; margin-top: 10px; }
        button:disabled { background: #a0cffa; cursor: not-allowed; }
        button i { margin-right: 8px; }
        .msg { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; font-weight: bold; }
        .msg-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        a.btn-cancelar { display: block; text-align: center; margin-top: 15px; color: #6c757d; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fa-solid fa-user-plus"></i> Registrar Nuevo Usuario</h2>
    
    <?php if ($mensaje_info): ?>
        <div class="msg msg-<?php echo htmlspecialchars($mensaje_info['tipo']); ?>"><?php echo $mensaje_info['texto']; ?></div>
    <?php endif; ?>

    <form id="registroForm" method="POST" action="crear.php" novalidate>
        <div class="form-control">
            <label for="nombre">Nombre:</label>
            <div class="input-group">
                <input type="text" id="nombre" name="nombre" required pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$" placeholder="p. ej., Ada Lovelace">
                <i class="fa-solid fa-user"></i>
            </div>
            <span class="error-text" id="error-nombre"></span>
        </div>
        
        <div class="form-control">
            <label for="email">Email:</label>
            <div class="input-group">
                <input type="email" id="email" name="email" required placeholder="p. ej., ada.lovelace@email.com">
                <i class="fa-solid fa-envelope"></i>
            </div>
            <span class="error-text" id="error-email"></span>
        </div>
        
        <button type="submit" id="submitBtn"><i class="fa-solid fa-paper-plane"></i> Guardar Usuario</button>
        <a href="index.php" class="btn-cancelar">Volver a la lista</a>
    </form>
</div>

<script>
// --- SCRIPT DE VALIDACIÓN DEL FORMULARIO ---
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registroForm');
    const nombreInput = document.getElementById('nombre');
    const emailInput = document.getElementById('email');
    const submitBtn = document.getElementById('submitBtn');
    const errorNombre = document.getElementById('error-nombre');
    const errorEmail = document.getElementById('error-email');

    const nombrePattern = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,}$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function validateField(input, pattern, errorElement, emptyMsg, invalidMsg) {
        const value = input.value.trim();
        if (value === '') {
            input.classList.add('invalid');
            errorElement.textContent = emptyMsg;
            errorElement.style.display = 'block';
            return false;
        } else if (!pattern.test(value)) {
            input.classList.add('invalid');
            errorElement.textContent = invalidMsg;
            errorElement.style.display = 'block';
            return false;
        } else {
            input.classList.remove('invalid');
            errorElement.style.display = 'none';
            return true;
        }
    }

    function validateForm() {
        const isNombreValid = validateField(nombreInput, nombrePattern, errorNombre, 'El nombre es obligatorio.', 'El nombre solo debe contener letras y espacios.');
        const isEmailValid = validateField(emailInput, emailPattern, errorEmail, 'El email es obligatorio.', 'Por favor, introduce un email válido.');
        submitBtn.disabled = !(isNombreValid && isEmailValid);
    }

    ['input', 'blur'].forEach(event => {
        nombreInput.addEventListener(event, validateForm);
        emailInput.addEventListener(event, validateForm);
    });

    validateForm();

    form.addEventListener('submit', function(e) {
        validateForm(); 
        if (submitBtn.disabled) {
            e.preventDefault();
        }
    });
});
</script>

</body>
</html>
