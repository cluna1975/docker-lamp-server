<?php
session_start();
require_once '../config.php';
require_once '../includes/auth_check.php';

// Solo proceder si es una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $id = $_POST['id'] ?? null;

    if (!$id) {
        $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ No se proporcionó un ID de usuario.'];
        header("Location: index.php");
        exit();
    }

    try {
        $conn = conectar_db();
        
        // Preparar la consulta para cambiar status a inactive
        $stmt = $conn->prepare("UPDATE users SET status = 'inactive' WHERE id = ? AND status != 'inactive'");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            // Verificar si alguna fila fue afectada para confirmar la eliminación
            if ($stmt->affected_rows > 0) {
                $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => '⏸️ Usuario desactivado correctamente.'];
            } else {
                $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ No se encontró el usuario a desactivar.'];
            }
        } else {
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ Error del servidor al intentar desactivar.'];
            error_log("Error en eliminar.php: " . $stmt->error);
        }
        
        $stmt->close();
        $conn->close();
        
    } catch (mysqli_sql_exception $e) {
        $error_code = $e->getCode();
        error_log("Error MySQL en eliminar.php [{$error_code}]: " . $e->getMessage());
        
        if ($error_code == 1054) { // Unknown column
            $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ Error de configuración de base de datos.'];
        }
        
        header("Location: error_db.php?error={$error_code}");
        exit;
    }



} else {
    // Si no es POST, simplemente redirigir
    $_SESSION['mensaje'] = ['tipo' => 'error', 'texto' => '❌ Acción no permitida.'];
}

header("Location: index.php");
exit();

?>
