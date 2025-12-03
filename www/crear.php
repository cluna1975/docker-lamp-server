<?php
session_start();
require_once 'config.php';

$mensaje_info = null;

// --- LÓGICA PARA CREAR (CREATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $conn = conectar_db();
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'] ?? 'user';
    $errors = [];

    // Validaciones
    if (empty($first_name)) $errors[] = "El nombre es obligatorio.";
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $first_name)) $errors[] = "El nombre solo puede contener letras y espacios.";
    if (empty($email)) $errors[] = "El email es obligatorio.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El formato del email no es válido.";
    if (empty($password)) $errors[] = "La contraseña es obligatoria.";
    if (strlen($password) < 6) $errors[] = "La contraseña debe tener al menos 6 caracteres.";
    if (!empty($username) && !preg_match("/^[a-zA-Z0-9_]+$/", $username)) $errors[] = "El username solo puede contener letras, números y guiones bajos.";
    
    if (!empty($errors)) {
        $mensaje_info = ['tipo' => 'error', 'texto' => implode('<br>', $errors)];
    } else {
        try {
            $uuid = bin2hex(random_bytes(16));
            $uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20, 12);
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $conn->prepare("INSERT INTO users (uuid, email, password_hash, username, first_name, last_name, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $uuid, $email, $password_hash, $username, $first_name, $last_name, $role);

            if ($stmt->execute()) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => "✅ ¡Usuario $first_name registrado con éxito!"];
                header("Location: index.php");
                exit();
            } else {
                if ($conn->errno == 1062) {
                    $mensaje_info = ['tipo' => 'error', 'texto' => "❌ El email o username ya está registrado."];
                } else {
                    $mensaje_info = ['tipo' => 'error', 'texto' => "❌ Error del servidor: " . $stmt->error];
                }
            }
            $stmt->close();
            $conn->close();
        } catch (mysqli_sql_exception $e) {
            $error_code = $e->getCode();
            error_log("Error MySQL en crear.php [{$error_code}]: " . $e->getMessage());
            header("Location: error_db.php?error={$error_code}");
            exit;
        }
    }
}

// Mensaje de la sesión (por si hay una redirección con error)
if (isset($_SESSION['mensaje'])) {
    $mensaje_info = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
?>
<?php $page_title = 'Crear Usuario'; include 'header.php'; ?>
    <style>
        .content { display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 100px); }
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
            <label for="first_name">Nombre:</label>
            <div class="input-group">
                <input type="text" id="first_name" name="first_name" required pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$" placeholder="p. ej., Ada">
                <i class="fa-solid fa-user"></i>
            </div>
            <span class="error-text" id="error-first_name"></span>
        </div>
        
        <div class="form-control">
            <label for="last_name">Apellido:</label>
            <div class="input-group">
                <input type="text" id="last_name" name="last_name" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$" placeholder="p. ej., Lovelace">
                <i class="fa-solid fa-user"></i>
            </div>
            <span class="error-text" id="error-last_name"></span>
        </div>
        
        <div class="form-control">
            <label for="username">Username (opcional):</label>
            <div class="input-group">
                <input type="text" id="username" name="username" pattern="^[a-zA-Z0-9_]+$" placeholder="p. ej., ada_lovelace">
                <i class="fa-solid fa-at"></i>
            </div>
            <span class="error-text" id="error-username"></span>
        </div>
        
        <div class="form-control">
            <label for="email">Email:</label>
            <div class="input-group">
                <input type="email" id="email" name="email" required placeholder="p. ej., ada.lovelace@email.com">
                <i class="fa-solid fa-envelope"></i>
            </div>
            <span class="error-text" id="error-email"></span>
        </div>
        
        <div class="form-control">
            <label for="password">Contraseña:</label>
            <div class="input-group">
                <input type="password" id="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres">
                <i class="fa-solid fa-lock"></i>
            </div>
            <span class="error-text" id="error-password"></span>
        </div>
        
        <div class="form-control">
            <label for="role">Rol:</label>
            <select id="role" name="role" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px;">
                <option value="user">Usuario</option>
                <option value="moderator">Moderador</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        
        <button type="submit" id="submitBtn" disabled><i class="fa-solid fa-paper-plane"></i> Guardar Usuario</button>
        <a href="index.php" class="btn-cancelar">Volver a la lista</a>
    </form>
</div>

<?php include 'footer.php'; ?>

<script>
// --- SCRIPT DE VALIDACIÓN DEL FORMULARIO ---
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registroForm');
    const firstNameInput = document.getElementById('first_name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const submitBtn = document.getElementById('submitBtn');
    const errorFirstName = document.getElementById('error-first_name');
    const errorEmail = document.getElementById('error-email');
    const errorPassword = document.getElementById('error-password');

    const namePattern = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{2,}$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    function validateField(input, pattern, errorElement, emptyMsg, invalidMsg, required = true) {
        const value = input.value.trim();
        if (required && value === '') {
            input.classList.add('invalid');
            errorElement.textContent = emptyMsg;
            errorElement.style.display = 'block';
            return false;
        } else if (value !== '' && !pattern.test(value)) {
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

    function validatePassword() {
        const value = passwordInput.value;
        if (value === '') {
            passwordInput.classList.add('invalid');
            errorPassword.textContent = 'La contraseña es obligatoria.';
            errorPassword.style.display = 'block';
            return false;
        } else if (value.length < 6) {
            passwordInput.classList.add('invalid');
            errorPassword.textContent = 'La contraseña debe tener al menos 6 caracteres.';
            errorPassword.style.display = 'block';
            return false;
        } else {
            passwordInput.classList.remove('invalid');
            errorPassword.style.display = 'none';
            return true;
        }
    }

    function validateForm() {
        const isFirstNameValid = validateField(firstNameInput, namePattern, errorFirstName, 'El nombre es obligatorio.', 'El nombre solo debe contener letras y espacios.');
        const isEmailValid = validateField(emailInput, emailPattern, errorEmail, 'El email es obligatorio.', 'Por favor, introduce un email válido.');
        const isPasswordValid = validatePassword();
        submitBtn.disabled = !(isFirstNameValid && isEmailValid && isPasswordValid);
    }

    ['input', 'blur'].forEach(event => {
        firstNameInput.addEventListener(event, validateForm);
        emailInput.addEventListener(event, validateForm);
        passwordInput.addEventListener(event, validateForm);
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
