<?php
session_start();
require_once 'config.php';

// Solo proceder si es una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id = $_POST['id'] ?? null;

    if (!$id) {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ No se proporcionó un ID de usuario.'];
        header("Location: index.php");
        exit();
    }

    $conn = conectar_db();
    
    // Preparar la consulta para eliminar
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Verificar si alguna fila fue afectada para confirmar la eliminación
        if ($stmt->affected_rows > 0) {
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => '🗑️ Usuario eliminado correctamente.'];
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ No se encontró el usuario a eliminar.'];
        }
    } else {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ Error del servidor al intentar eliminar.'];
        error_log("Error en eliminar.php: " . $stmt->error); // Log del error
    }

    $stmt->close();
    $conn->close();

} else {
    // Si no es POST, simplemente redirigir
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ Acción no permitida.'];
}

header("Location: index.php");
exit();

?>
