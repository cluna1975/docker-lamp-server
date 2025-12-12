<!DOCTYPE html>
<html>
<head>
<parameter name="title">Test Certificado Web</title>
    <style>
        body {
            font-family: monospace;
            background: #1a202c;
            color: #e2e8f0;
            padding: 40px;
        }
        .result {
            background: #2d3748;
            padding: 20px;
            border-radius: 8px;
            margin: 10px 0;
        }
        .success { color: #48bb78; }
        .error { color: #f56565; }
    </style>
</head>
<body>
    <h1>🔐 Test de Certificado Digital</h1>
    
    <?php
    require_once __DIR__ . '/../config.php';
    require_once SRC_PATH . 'SRIXMLSigner.php';
    
    echo '<div class="result">';
    echo "<h2>Configuración de Rutas</h2>";
    echo "<p><strong>BASE_PATH:</strong> " . BASE_PATH . "</p>";
    echo "<p><strong>DATA_PATH:</strong> " . DATA_PATH . "</p>";
    echo "<p><strong>CERT_FILE:</strong> " . CERT_FILE . "</p>";
    echo "</div>";
    
    echo '<div class="result">';
    echo "<h2>Verificación de Archivo</h2>";
    $exists = file_exists(CERT_FILE);
    echo "<p><strong>¿Existe CERT_FILE?</strong> <span class='" . ($exists ? "success" : "error") . "'>" . 
         ($exists ? "SÍ ✓" : "NO ✗") . "</span></p>";
    
    if ($exists) {
        echo "<p><strong>Tamaño:</strong> " . filesize(CERT_FILE) . " bytes</p>";
        echo "<p><strong>Permisos:</strong> " . substr(sprintf('%o', fileperms(CERT_FILE)), -4) . "</p>";
    }
    echo "</div>";
    
    echo '<div class="result">';
    echo "<h2>Prueba de Carga del Certificado</h2>";
    
    try {
        $signer = new SRIXMLSigner(null, 'ECUA2024');
        echo "<p><span class='success'>✓ Instancia creada correctamente</span></p>";
        
        $signer->cargarCertificado();
        echo "<p><span class='success'>✓ Certificado cargado correctamente</span></p>";
        echo "<p><span class='success'><strong>ÉXITO:</strong> El certificado se puede cargar correctamente desde la web</span></p>";
        
    } catch (Exception $e) {
        echo "<p><span class='error'>✗ ERROR: " . htmlspecialchars($e->getMessage()) . "</span></p>";
    }
    echo "</div>";
    ?>
    
    <div class="result">
        <h2>Enlaces</h2>
        <p><a href="firmar_xml.php" style="color: #4299e1;">← Volver a Firmar XML</a></p>
    </div>
</body>
</html>
