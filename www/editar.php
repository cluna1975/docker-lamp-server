<?php
session_start();
require_once 'config.php';

$conn = conectar_db();
$mensaje_info = null;
$usuario = null;
$id = $_GET['id'] ?? null;

// Redirigir si no hay ID
if (!$id) {
    header("Location: index.php");
    exit();
}

// --- LÓGICA PARA ACTUALIZAR (UPDATE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'actualizar') {
    
    $nombre = trim($_POST['nombre']);
    $email  = trim($_POST['email']);
    $id = $_POST['id'];
    $errors = [];

    // Validaciones (similares a la creación)
    if (empty($nombre)) $errors[] = "El nombre es obligatorio.";
    if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre)) $errors[] = "El nombre solo puede contener letras y espacios.";
    if (empty($email)) $errors[] = "El email es obligatorio.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El formato del email no es válido.";
    
    if (!empty($errors)) {
        // Si hay errores, se mostrarán en la misma página de edición
        $mensaje_info = ['tipo' => 'error', 'texto' => implode('<br>', $errors)];
    } else {
        // Prepara la consulta para actualizar
        $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nombre, $email, $id);

        if ($stmt->execute()) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => "✅ ¡Usuario actualizado con éxito!"];
            header("Location: index.php"); // Redirigir al índice
            exit();
        } else {
            if ($conn->errno == 1062) { // Error de email duplicado
                $mensaje_info = ['tipo' => 'error', 'texto' => "❌ El email '$email' ya está en uso por otro usuario."];
            } else {
                $mensaje_info = ['tipo' => 'error', 'texto' => "❌ Error del servidor: " . $stmt->error];
            }
        }
        $stmt->close();
    }
}

// --- LÓGICA PARA OBTENER DATOS DEL USUARIO (PARA EL FORMULARIO) ---
if ($id) {
    $stmt = $conn->prepare("SELECT id, nombre, email FROM usuarios WHERE id = ?");
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
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        /* --- ESTILOS (copiados de index.php para consistencia) --- */
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
        button { width: 100%; padding: 12px; background: #28a745; color: white; border: none; cursor: pointer; border-radius: 5px; font-size: 16px; font-weight: bold; transition: background-color 0.3s; margin-top: 10px; }
        button:hover { background: #218838; }
        button i { margin-right: 8px; }
        .msg { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; font-weight: bold; }
        .msg-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        a.btn-cancelar { display: block; text-align: center; margin-top: 15px; color: #6c757d; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="fa-solid fa-user-pen"></i> Editar Usuario</h2>

    <?php if ($mensaje_info): ?>
        <div class="msg msg-<?php echo htmlspecialchars($mensaje_info['tipo']); ?>"><?php echo $mensaje_info['texto']; ?></div>
    <?php endif; ?>

    <?php if ($usuario): ?>
    <form id="editarForm" method="POST" action="editar.php?id=<?php echo htmlspecialchars($id); ?>">
        <input type="hidden" name="action" value="actualizar">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">

        <div class="form-control">
            <label for="nombre">Nombre:</label>
            <div class="input-group">
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                <i class="fa-solid fa-user"></i>
            </div>
        </div>
        
        <div class="form-control">
            <label for="email">Email:</label>
            <div class="input-group">
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                <i class="fa-solid fa-envelope"></i>
            </div>
        </div>
        
        <button type="submit" id="submitBtn"><i class="fa-solid fa-save"></i> Guardar Cambios</button>
        <a href="index.php" class="btn-cancelar">Cancelar</a>
    </form>
    <?php endif; ?>
</div>

</body>
</html>
