<?php
session_start();
require_once '../config.php';
// require_once '../includes/auth_check.php'; // Permitir registro público

$mensaje_info = null;
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = ($is_logged_in && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

// --- LÓGICA PARA CREAR (CREATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $conn = conectar_db();
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Solo permitir cambiar rol si es admin, sino forzar 'user'
    if ($is_admin) {
        $role = $_POST['role'] ?? 'user';
    } else {
        $role = 'user';
    }

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
                
                // Si no estaba logueado, redirigir al login
                if (!$is_logged_in) {
                    header("Location: ../auth/login.php");
                } else {
                    header("Location: index.php");
                }
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
<?php $page_title = 'Crear Usuario'; include '../header.php'; ?>

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
        
        <?php if ($is_admin): ?>
        <div class="form-control">
            <label for="role">Rol:</label>
            <select id="role" name="role" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px;">
                <option value="user">Usuario</option>
                <option value="moderator">Moderador</option>
                <option value="admin">Administrador</option>
            </select>
        </div>
        <?php endif; ?>
        
        <button type="submit" id="submitBtn" disabled><i class="fa-solid fa-paper-plane"></i> Guardar Usuario</button>
        <a href="index.php" class="btn-cancelar">Volver a la lista</a>
    </form>
</div>

<?php include '../footer.php'; ?>

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
