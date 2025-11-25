<?php
// --- CONFIGURACIÓN DE BASE DE DATOS ---
$host = 'db'; // Importante: En Docker, el host es el nombre del servicio
$user = 'root';
$pass = 'root';
$db   = 'mi_proyecto';

$mensaje = "";

// Solo ejecutamos esto si el usuario envió el formulario (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Conexión
    $conn = new mysqli($host, $user, $pass, $db);

    // 2. Verificar errores de conexión (Esto saldrá en los logs)
    if ($conn->connect_error) {
        error_log("Error Crítico de MySQL: " . $conn->connect_error); // Escribe en el log de Docker
        die("La conexión falló.");
    }

    // 3. Recibir datos (Simulamos limpieza básica)
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $email  = $conn->real_escape_string($_POST['email']);

    // 4. Insertar en BD
    $sql = "INSERT INTO usuarios (nombre, email) VALUES ('$nombre', '$email')";

    if ($conn->query($sql) === TRUE) {
        $mensaje = "✅ ¡Usuario $nombre registrado con éxito!";
        // También escribimos en el log para saber que alguien se registró
        error_log("Nuevo registro exitoso: $email");
    } else {
        $mensaje = "❌ Error: " . $conn->error;
        error_log("Error al insertar: " . $conn->error);
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Docker</title>
    <style>
        body { font-family: sans-serif; padding: 50px; background: #f4f4f4; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 400px; margin: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .msg { padding: 10px; margin-bottom: 10px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>

<div class="container">
    <h2>Registro de Usuarios</h2>
    
    <?php if ($mensaje): ?>
        <div class="msg"><?php echo $mensaje; ?></div>
    <?php endif; ?>

    <form method="POST" action="registro.php">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        
        <label>Email:</label>
        <input type="email" name="email" required>
        
        <button type="submit">Guardar en Base de Datos</button>
    </form>
</div>

</body>
</html>