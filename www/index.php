<?php
$host = 'db'; // En Docker, el host es el nombre del servicio en el compose
$user = 'root';
$pass = 'root';
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}
echo "<h1>🚀 ¡Éxito! Tu entorno LAMP con Docker funciona perfecto.</h1>";
echo "Conectado a MySQL " . $conn->server_info;
phpinfo();
?>