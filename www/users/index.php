<?php
session_start();
require_once '../config.php';
require_once '../includes/auth_check.php';

$conn = conectar_db();

// --- LÓGICA PARA LEER (READ) ---
try {
    $resultado = $conn->query("SELECT id, uuid, email, username, first_name, last_name, role, status, created_at FROM users ORDER BY id DESC");
    $usuarios = $resultado->fetch_all(MYSQLI_ASSOC);
    $conn->close();
} catch (mysqli_sql_exception $e) {
    $error_code = $e->getCode();
    error_log("Error MySQL en index.php [{$error_code}]: " . $e->getMessage());
    header("Location: error_db.php?error={$error_code}");
    exit;
}

// Mensajes de sesión para mostrar feedback (de crear, editar, eliminar)
$mensaje_info = null;
if (isset($_SESSION['mensaje'])) {
    $mensaje_info = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
?>

<?php $page_title = 'Lista de Usuarios'; include '../header.php'; ?>

<div class="container">
    <div class="header">
        <div class="header-left">
             <h2><i class="fa-solid fa-users"></i> Lista de Usuarios</h2>
        </div>
        <div class="header-right">
            <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Usuario'); ?></span>
            <a href="crear.php" class="btn-crear"><i class="fa-solid fa-plus"></i> Crear Nuevo Usuario</a>
            <a href="../auth/logout.php" class="btn-logout"><i class="fa-solid fa-sign-out-alt"></i> Salir</a>
        </div>
    </div>

    <?php if ($mensaje_info): ?>
        <div class="msg msg-<?php echo htmlspecialchars($mensaje_info['tipo']); ?>"><?php echo $mensaje_info['texto']; ?></div>
    <?php endif; ?>

    <table id="usuariosTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Username</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Fecha Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="8" class="no-usuarios">Aún no hay usuarios registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td><?php echo htmlspecialchars(trim(($usuario['first_name'] ?? '') . ' ' . ($usuario['last_name'] ?? ''))); ?></td>
                    <td><?php echo htmlspecialchars($usuario['username'] ?? '-'); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td><span class="badge badge-<?php echo $usuario['role']; ?>"><?php echo ucfirst($usuario['role']); ?></span></td>
                    <td>
                        <?php if ($usuario['status'] == 'active'): ?>
                            <span style="color: green;">✅ Activo</span>
                        <?php elseif ($usuario['status'] == 'inactive'): ?>
                            <span style="color: orange;">⏸️ Inactivo</span>
                        <?php else: ?>
                            <span style="color: red;">🚫 Baneado</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($usuario['created_at']))); ?></td>
                    <td class="acciones">
                        <form action="editar.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                            <button type="submit" class="btn-editar"><i class="fa-solid fa-pencil"></i></button>
                        </form>

                        <?php if ($usuario['status'] == 'active'): ?>
                            <form action="eliminar.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que quieres desactivar a este usuario?');">
                                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                <button type="submit" class="btn-eliminar" title="Desactivar"><i class="fa-solid fa-user-slash"></i></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: bold;
    text-transform: uppercase;
}
.badge-user { background-color: #e3f2fd; color: #1976d2; }
.badge-moderator { background-color: #fff3e0; color: #f57c00; }
.badge-admin { background-color: #ffebee; color: #d32f2f; }
</style>

<?php include '../footer.php'; ?>

<script>
$(document).ready(function() {
    var table = $('#usuariosTable').DataTable({
        "responsive": {
            "details": {
                "type": 'column',
                "target": 'tr'
            }
        },
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "dom": '<"top"Bf>rt<"bottom"lip><"clear">',
        "buttons": [
            {
                extend: 'excelHtml5',
                text: '<i class="fa-solid fa-file-excel"></i> Excel',
                titleAttr: 'Exportar a Excel',
                className: 'btn-export'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa-solid fa-file-pdf"></i> PDF',
                titleAttr: 'Exportar a PDF',
                className: 'btn-export'
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fa-solid fa-file-csv"></i> CSV',
                titleAttr: 'Exportar a CSV',
                className: 'btn-export'
            }
        ],
        "order": [[ 0, "desc" ]],
        "columnDefs": [
            { "responsivePriority": 1, "targets": [0, 1, 7] },
            { "responsivePriority": 2, "targets": [3, 5] },
            { "responsivePriority": 3, "targets": [2, 4, 6] }
        ]
    });
});

</script>
