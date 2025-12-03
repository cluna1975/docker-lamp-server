<?php
session_start();
require_once 'config.php';

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
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, username = ?, email = ?, role = ?, status = ? WHERE id = ? AND deleted_at IS NULL");
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
        $stmt = $conn->prepare("SELECT id, uuid, email, username, first_name, last_name, role, status FROM users WHERE id = ? AND deleted_at IS NULL");
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
<?php $page_title = 'Editar Usuario'; include 'header.php'; ?>
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
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; cursor: pointer; border-radius: 5px; font-size: 16px; font-weight: bold; transition: background-color 0.3s; margin-top: 10px; }
        button:hover { background: #218838; }
        button i { margin-right: 8px; }
        .msg { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; font-weight: bold; }
        .msg-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        a.btn-cancelar { display: block; text-align: center; margin-top: 15px; color: #6c757d; text-decoration: none; }
        .switch { position: relative; display: inline-block; width: 60px; height: 34px; margin-right: 10px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 26px; width: 26px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #28a745; }
        input:checked + .slider:before { transform: translateX(26px); }
        .estado-text { vertical-align: middle; font-weight: bold; }
    </style>
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


<?php include 'footer.php'; ?>
