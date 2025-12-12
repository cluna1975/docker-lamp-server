<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Certificado .p12</title>
</head>
<body>
<?php
/**
 * Script de prueba para verificar el certificado .p12
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Test de Certificado .p12</h2>";
echo "<hr>";

// Configuración
$certFile = __DIR__ . '/mr.p12';
$password = 'ECUA2024';

echo "<h3>1. Verificación del archivo</h3>";
echo "Ruta: <strong>" . $certFile . "</strong><br>";

if (file_exists($certFile)) {
    echo "✓ <span style='color: green;'>El archivo existe</span><br>";
    echo "Tamaño: " . filesize($certFile) . " bytes<br>";
    echo "Permisos: " . substr(sprintf('%o', fileperms($certFile)), -4) . "<br>";
} else {
    echo "✗ <span style='color: red;'>El archivo NO existe</span><br>";
    die();
}

echo "<h3>2. Verificación de extensión OpenSSL</h3>";
if (extension_loaded('openssl')) {
    echo "✓ <span style='color: green;'>Extensión OpenSSL cargada</span><br>";
    echo "Versión OpenSSL: " . OPENSSL_VERSION_TEXT . "<br>";
} else {
    echo "✗ <span style='color: red;'>Extensión OpenSSL NO disponible</span><br>";
    die();
}

echo "<h3>3. Intentando leer el certificado con soporte LEGACY</h3>";

// HABILITAR LEGACY PROVIDER para OpenSSL 3.x
$opensslConfig = sys_get_temp_dir() . '/openssl_legacy_test.cnf';
$configContent = <<<EOT
openssl_conf = openssl_init

[openssl_init]
providers = provider_sect

[provider_sect]
default = default_sect
legacy = legacy_sect

[default_sect]
activate = 1

[legacy_sect]
activate = 1
EOT;
file_put_contents($opensslConfig, $configContent);
putenv("OPENSSL_CONF=" . $opensslConfig);

echo "<em style='color: #667eea;'>Legacy provider habilitado para certificados con algoritmos antiguos (RC2, 3DES)</em><br><br>";

$p12content = file_get_contents($certFile);
$certs = [];

echo "Intentando con contraseña: '<strong>" . $password . "</strong>'<br><br>";

// Limpiar errores previos
while (openssl_error_string() !== false);

$result = @openssl_pkcs12_read($p12content, $certs, $password);

// Limpiar archivo temporal
@unlink($opensslConfig);

if ($result) {
    echo "✓ <span style='color: green; font-size: 18px;'>¡CERTIFICADO LEÍDO CORRECTAMENTE!</span><br><br>";
    
    echo "<h3>4. Información del certificado</h3>";
    
    if (isset($certs['cert'])) {
        echo "<strong>Certificado público encontrado:</strong><br>";
        $certInfo = openssl_x509_parse($certs['cert']);
        echo "<pre>";
        echo "Nombre (CN): " . (isset($certInfo['subject']['CN']) ? $certInfo['subject']['CN'] : 'N/A') . "\n";
        echo "Organización: " . (isset($certInfo['subject']['O']) ? $certInfo['subject']['O'] : 'N/A') . "\n";
        echo "Emisor: " . (isset($certInfo['issuer']['CN']) ? $certInfo['issuer']['CN'] : 'N/A') . "\n";
        echo "Válido desde: " . date('Y-m-d H:i:s', $certInfo['validFrom_time_t']) . "\n";
        echo "Válido hasta: " . date('Y-m-d H:i:s', $certInfo['validTo_time_t']) . "\n";
        echo "Número de serie: " . $certInfo['serialNumber'] . "\n";
        
        // Verificar si está vigente
        $ahora = time();
        if ($ahora < $certInfo['validFrom_time_t']) {
            echo "\n⚠️ <span style='color: orange;'>ADVERTENCIA: El certificado aún no es válido</span>\n";
        } elseif ($ahora > $certInfo['validTo_time_t']) {
            echo "\n✗ <span style='color: red;'>ERROR: El certificado ha expirado</span>\n";
        } else {
            echo "\n✓ <span style='color: green;'>El certificado está vigente</span>\n";
        }
        
        echo "</pre>";
    } else {
        echo "✗ <span style='color: red;'>No se encontró el certificado público</span><br>";
    }
    
    if (isset($certs['pkey'])) {
        echo "<strong>✓ Clave privada encontrada</strong><br>";
    } else {
        echo "✗ <span style='color: red;'>No se encontró la clave privada</span><br>";
    }
    
    if (isset($certs['extracerts']) && count($certs['extracerts']) > 0) {
        echo "<strong>Certificados adicionales:</strong> " . count($certs['extracerts']) . "<br>";
    }
    
} else {
    echo "✗ <span style='color: red; font-size: 18px;'>ERROR AL LEER EL CERTIFICADO</span><br><br>";
    
    echo "<h3>4. Errores de OpenSSL</strong></h3>";
    $errores = [];
    while ($msg = openssl_error_string()) {
        $errores[] = $msg;
    }
    
    if (count($errores) > 0) {
        echo "<pre style='background: #ffebee; padding: 10px; border-left: 4px solid red;'>";
        foreach ($errores as $error) {
            echo $error . "\n";
        }
        echo "</pre>";
    }
    
    echo "<h3>5. Intentos con otras contraseñas (con legacy provider)</h3>";
    
    // Habilitar legacy provider nuevamente
    file_put_contents($opensslConfig, $configContent);
    putenv("OPENSSL_CONF=" . $opensslConfig);
    
    $passwordsToTry = ['', null, 'ECUA2024', 'ecua2024', '123456', 'mr', 'MR'];
    
    foreach ($passwordsToTry as $tryPass) {
        while (openssl_error_string() !== false); // Limpiar errores
        
        $displayPass = $tryPass === '' ? '(vacía)' : ($tryPass === null ? '(null)' : $tryPass);
        echo "Probando con: <strong>" . $displayPass . "</strong> ... ";
        
        if (@openssl_pkcs12_read($p12content, $testCerts, $tryPass)) {
            echo "<span style='color: green;'>✓ ¡FUNCIONA!</span><br>";
            echo "<div style='background: #d4f8e8; padding: 10px; margin: 10px 0; border-left: 4px solid #10b981;'>";
            echo "<strong>✓ La contraseña correcta es: </strong>" . $displayPass;
            echo "</div>";
            break;
        } else {
            echo "<span style='color: red;'>✗ Falló</span><br>";
        }
    }
    
    @unlink($opensslConfig);
}

echo "<hr>";
echo "<h3>6. Información del servidor PHP</h3>";
echo "Versión PHP: " . PHP_VERSION . "<br>";
echo "Sistema operativo: " . PHP_OS . "<br>";

echo "<hr>";
echo "<p><a href='firmar_xml.php'>&larr; Volver a firmar XML</a> | <a href='index.php'>&larr; Volver al inicio</a></p>";
?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 900px;
        margin: 40px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h2 {
        color: #333;
        border-bottom: 3px solid #667eea;
        padding-bottom: 10px;
    }
    h3 {
        color: #555;
        margin-top: 20px;
    }
    pre {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #667eea;
        overflow-x: auto;
    }
    hr {
        border: none;
        border-top: 2px solid #ddd;
        margin: 30px 0;
    }
</style>
</body>
</html>
