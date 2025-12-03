<?php
session_start();
require_once '../config.php';
require_once '../includes/auth_check.php';

$conn = conectar_db();
$mensaje_info = null;
$usuario = null;
$id = $_POST['id'] ?? $_GET['id'] ?? null;

// Redirigir si no hay ID
if (!$id) {
    header("Location: index.php");
    exit();
}

// --- LÓGICA PARA ACTUALIZAR (UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'actualizar') {
    
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];
    $id = $_POST['id'];
    $errors = [];

    // Validaciones
    if (empty($first_name)) $errors[] = "El nombre es obligatorio.";
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $first_name)) $errors[] = "El nombre solo puede contener letras y espacios.";
    if (empty($email)) $errors[] = "El email es obligatorio.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El formato del email no es válido.";
    if (!empty($username) && !preg_match("/^[a-zA-Z0-9_]+$/", $username)) $errors[] = "El username solo puede contener letras, números y guiones bajos.";
    
    if (!empty($errors)) {
        // Si hay errores, se mostrarán en la misma página de edición
        $mensaje_info = ['tipo' => 'error', 'texto' => implode('<br>', $errors)];
    } else {
        try {
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, role = ?, status = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $first_name, $last_name, $username, $email, $role, $status, $id);

            if ($stmt->execute()) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => "✅ ¡Usuario actualizado con éxito!"];
                header("Location: index.php");
                exit();
            } else {
                if ($conn->errno == 1062) {
                    $mensaje_info = ['tipo' => 'error', 'texto' => "❌ El email o username ya está en uso por otro usuario."];
                } else {
                    $mensaje_info = ['tipo' => 'error', 'texto' => "❌ Error del servidor: " . $stmt->error];
                }
            }
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            $error_code = $e->getCode();
            error_log("Error MySQL en editar.php [{$error_code}]: " . $e->getMessage());
            header("Location: error_db.php?error={$error_code}");
            exit;
        }
    }
}

// --- LÓGICA PARA OBTENER DATOS DEL USUARIO (PARA EL FORMULARIO) ---
if ($id) {
    try {
        $stmt = $conn->prepare("SELECT id, uuid, email, username, first_name, last_name, role, status FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ Usuario no encontrado.'];
            header("Location: index.php");
            exit();
        }
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        $error_code = $e->getCode();
        error_log("Error MySQL en editar.php SELECT [{$error_code}]: " . $e->getMessage());
        header("Location: error_db.php?error={$error_code}");
        exit;
    }
}

$conn->close();

?>
<?php $page_title = 'Editar Usuario'; include '../header.php'; ?>

</head>
<body>

<div class="container">
    <h2><i class="fa-solid fa-user-pen"></i> Editar Usuario</h2>

    <?php if ($mensaje_info): ?>
        <div class="msg msg-<?php echo htmlspecialchars($mensaje_info['tipo']); ?>"><?php echo $mensaje_info['texto']; ?></div>
    <?php endif; ?>

    <?php if ($usuario): ?>
    <form id="editarForm" method="POST" action="editar.php">
        <input type="hidden" name="action" value="actualizar">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">

        <div class="form-control">
            <label for="first_name">Nombre:</label>
            <div class="input-group">
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($usuario['first_name']); ?>" required>
                <i class="fa-solid fa-user"></i>
            </div>
        </div>
        
        <div class="form-control">
            <label for="last_name">Apellido:</label>
            <div class="input-group">
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($usuario['last_name'] ?? ''); ?>">
                <i class="fa-solid fa-user"></i>
            </div>
        </div>
        
        <div class="form-control">
            <label for="username">Username:</label>
            <div class="input-group">
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($usuario['username'] ?? ''); ?>">
                <i class="fa-solid fa-at"></i>
            </div>
        </div>
        
        <div class="form-control">
            <label for="email">Email:</label>
            <div class="input-group">
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                <i class="fa-solid fa-envelope"></i>
            </div>
        </div>
        
        <div class="form-control">
            <label for="role">Rol:</label>
            <select id="role" name="role" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px;">
                <option value="user" <?php echo $usuario['role'] == 'user' ? 'selected' : ''; ?>>Usuario</option>
                <option value="moderator" <?php echo $usuario['role'] == 'moderator' ? 'selected' : ''; ?>>Moderador</option>
                <option value="admin" <?php echo $usuario['role'] == 'admin' ? 'selected' : ''; ?>>Administrador</option>
            </select>
        </div>
        
        <div class="form-control">
            <label for="status">Estado:</label>
            <select id="status" name="status" style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px;">
                <option value="active" <?php echo $usuario['status'] == 'active' ? 'selected' : ''; ?>>Activo</option>
                <option value="inactive" <?php echo $usuario['status'] == 'inactive' ? 'selected' : ''; ?>>Inactivo</option>
                <option value="banned" <?php echo $usuario['status'] == 'banned' ? 'selected' : ''; ?>>Baneado</option>
            </select>
        </div>
        
        <button type="submit" id="submitBtn"><i class="fa-solid fa-save"></i> Guardar Cambios</button>
        <a href="index.php" class="btn-cancelar">Cancelar</a>
    </form>
    <?php endif; ?>
</div>


<?php include '../footer.php'; ?>
