<?php
require_once __DIR__ . '/../config.php';
require_once SRC_PATH . 'SRIXMLSigner.php';

echo "=== TEST DE RUTA DEL CERTIFICADO ===\n\n";

echo "1. BASE_PATH: " . BASE_PATH . "\n";
echo "2. DATA_PATH: " . DATA_PATH . "\n";
echo "3. CERT_PATH: " . CERT_PATH . "\n";
echo "4. CERT_FILE: " . CERT_FILE . "\n\n";

echo "5. ¿Existe CERT_FILE? " . (file_exists(CERT_FILE) ? "SÍ ✓" : "NO ✗") . "\n";
echo "6. Tamaño: " . (file_exists(CERT_FILE) ? filesize(CERT_FILE) . " bytes" : "N/A") . "\n\n";

// Probar crear instancia del firmador
try {
    echo "7. Creando instancia de SRIXMLSigner...\n";
    $signer = new SRIXMLSigner(null, 'ECUA2024');
    echo "   ✓ Instancia creada correctamente\n\n";
    
    // Intentar cargar certificado
    echo "8. Intentando cargar certificado...\n";
    $signer->cargarCertificado();
    echo "   ✓ Certificado cargado correctamente\n\n";
    
    echo "=== TODAS LAS PRUEBAS PASARON ===\n";
    
} catch (Exception $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n\n";
    echo "=== PRUEBA FALLIDA ===\n";
}
?>
