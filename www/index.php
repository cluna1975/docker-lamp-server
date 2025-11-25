<?php
session_start();
require_once 'config.php';

$conn = conectar_db();

// --- LÓGICA PARA LEER (READ) ---
$resultado = $conn->query("SELECT * FROM usuarios ORDER BY id DESC");
$usuarios = $resultado->fetch_all(MYSQLI_ASSOC);
$conn->close();

// Mensajes de sesión para mostrar feedback (de crear, editar, eliminar)
$mensaje_info = null;
if (isset($_SESSION['mensaje'])) {
    $mensaje_info = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD de Usuarios</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .container { background: white; padding: 30px 40px; border-radius: 10px; width: 100%; max-width: 800px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin: 20px auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .header h2 { margin: 0; color: #333; }
        .header .btn-crear {
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .header .btn-crear:hover { background: #0056b3; }
        .header .btn-crear i { margin-right: 8px; }
        
        .msg { padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: center; font-weight: bold; }
        .msg-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        tr:hover { background-color: #f1f1f1; }
        .acciones a, .acciones button {
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 4px;
            color: white;
            font-size: 0.9em;
            border: none;
            cursor: pointer;
            margin-right: 5px;
        }
        .btn-editar { background-color: #ffc107; }
        .btn-eliminar { background-color: #dc3545; }
        .no-usuarios { text-align: center; color: #777; padding: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2><i class="fa-solid fa-users"></i> Lista de Usuarios</h2>
        <a href="crear.php" class="btn-crear"><i class="fa-solid fa-plus"></i> Crear Nuevo Usuario</a>
    </div>

    <?php if ($mensaje_info): ?>
        <div class="msg msg-<?php echo htmlspecialchars($mensaje_info['tipo']); ?>"><?php echo $mensaje_info['texto']; ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Fecha Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="5" class="no-usuarios">Aún no hay usuarios registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($usuario['fecha_registro']))); ?></td>
                    <td class="acciones">
                        <a href="editar.php?id=<?php echo $usuario['id']; ?>" class="btn-editar"><i class="fa-solid fa-pencil"></i></a>
                        <form action="eliminar.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');">
                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                            <button type="submit" class="btn-eliminar"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
