<?php
// config.php

/**
 * Crea y devuelve una conexión a la base de datos.
 * @return mysqli La instancia de la conexión.
 */
function conectar_db() {
    // --- CONFIGURACIÓN DE BASE DE DATOS ---
    $host = $_ENV['DB_HOST'] ?? 'db';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? 'root';
    $db   = $_ENV['DB_NAME'] ?? 'mi_proyecto';

    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $conn = new mysqli($host, $user, $pass, $db);
        $conn->set_charset("utf8mb4");
        
        return $conn;
        
    } catch (mysqli_sql_exception $e) {
        $error_code = $e->getCode();
        $error_msg = $e->getMessage();
        
        error_log("MySQL Error [{$error_code}]: {$error_msg}");
        
        switch ($error_code) {
            case 1045: // Access denied
                error_log("Error de autenticación MySQL: Credenciales incorrectas");
                break;
                
            case 1049: // Unknown database
                error_log("Base de datos no encontrada: {$db}");
                break;
                
            case 1054: // Unknown column
                error_log("Columna desconocida en consulta SQL");
                break;
                
            case 2002: // Connection refused
            case 2003: // Can't connect to MySQL server
                error_log("Servidor MySQL no disponible en {$host}");
                break;
                
            case 1040: // Too many connections
                error_log("Demasiadas conexiones MySQL activas");
                break;
                
            case 2006: // MySQL server has gone away
                error_log("Conexión MySQL perdida durante la operación");
                break;
                
            default:
                error_log("Error MySQL no manejado: [{$error_code}] {$error_msg}");
                break;
        }
        
        header("Location: error_db.php?error={$error_code}");
        exit;
        
    } catch (Exception $e) {
        error_log("Error general de conexión: " . $e->getMessage());
        header('Location: error_db.php?error=general');
        exit;
    }
}
?>
