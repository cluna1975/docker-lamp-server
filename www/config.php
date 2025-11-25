<?php
// config.php

/**
 * Crea y devuelve una conexión a la base de datos.
 * Las credenciales están definidas directamente dentro de la función.
 * @return mysqli La instancia de la conexión.
 */
function conectar_db() {
    // --- CONFIGURACIÓN DE BASE DE DATOS ---
    $host = 'db'; 
    $user = 'root';
    $pass = 'root';
    $db   = 'mi_proyecto';

    $conn = new mysqli($host, $user, $pass, $db);

    // Verificar errores de conexión
    if ($conn->connect_error) {
        error_log("Error Crítico de MySQL: " . $conn->connect_error);
        // En un entorno de producción, es mejor no mostrar el error detallado.
        die("❌ Error de conexión: " . $conn->connect_error);
    }

    // Asegurar que la conexión use UTF-8
    $conn->set_charset("utf8");

    return $conn;
}
?>
