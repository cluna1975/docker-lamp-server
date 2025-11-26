<?php
session_start();
require_once 'config.php';

$conn = conectar_db();

// --- LÓGICA PARA LEER (READ) ---
try {
    $resultado = $conn->query("SELECT id, nombre, email, fecha_registro, COALESCE(estado, 1) as estado FROM usuarios ORDER BY id DESC");
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

<?php $page_title = 'Lista de Usuarios'; include 'header.php'; ?>

<div class="container">
    <div class="header">
        <div class="header-left">
             <h2><i class="fa-solid fa-users"></i> Lista de Usuarios</h2>
        </div>
        <div class="header-right">
            <a href="crear.php" class="btn-crear"><i class="fa-solid fa-plus"></i> Crear Nuevo Usuario</a>
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
                <th>Email</th>
                <th>Estado</th>
                <th>Fecha Creación</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="6" class="no-usuarios">Aún no hay usuarios registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td>
                        <?php if ($usuario['estado'] == 1): ?>
                            <span style="color: green;">✅ Activo</span>
                        <?php else: ?>
                            <span style="color: red;">❌ Inactivo</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($usuario['fecha_registro']))); ?></td>
                    <td class="acciones">
                        <form action="editar.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                            <button type="submit" class="btn-editar"><i class="fa-solid fa-pencil"></i></button>
                        </form>

                        <?php if ($usuario['estado'] == 1): ?>
                            <form action="eliminar.php" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este usuario?');">
                                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                <button type="submit" class="btn-eliminar"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>

<script>
$(document).ready(function() {
    var table = $('#usuariosTable').DataTable({
        "responsive": true,
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        "dom": 'Bfrtip',
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
        "columnDefs": [
            { "orderable": false, "targets": 5 },
            { "searchable": false, "targets": 5 }
        ],
        "order": [[ 0, "desc" ]]
    });
});

</script>
