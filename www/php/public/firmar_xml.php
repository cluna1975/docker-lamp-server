<?php
require_once __DIR__ . '/../config.php';
require_once SRC_PATH . 'SRIXMLSigner.php';

$mensaje = '';
$tipo_mensaje = '';
$errores = [];
$archivoFirmado = '';

// Listar archivos XML disponibles
$archivosXML = [];
if (is_dir(XML_PATH)) {
    $archivosXML = array_diff(scandir(XML_PATH), array('.', '..'));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['archivoXML']) && !empty($_POST['archivoXML'])) {
        try {
            $archivoXML = $_POST['archivoXML'];
            $rutaXML = XML_PATH . $archivoXML;
            
            if (!file_exists($rutaXML)) {
                throw new Exception("El archivo XML no existe");
            }
            
            // Configurar certificado (CERT_FILE ya contiene ruta completa)
            $password = $_POST['password'] ?? CERT_PASSWORD;
            
            $signer = new SRIXMLSigner(null, $password);
            
            // Firmar XML
            $archivoFirmado = $signer->firmarXML($rutaXML);
            
            $mensaje = "✓ XML firmado exitosamente con XAdES-BES";
            $tipo_mensaje = 'success';
            
        } catch (Exception $e) {
            // Sanitizar mensaje de error para no mostrar rutas del servidor
            $mensaje = "Error al firmar: " . sanitizarMensajeError($e->getMessage());
            $tipo_mensaje = 'error';
        }
    } elseif (isset($_FILES['xmlFile'])) {
        // Subir archivo XML
        if ($_FILES['xmlFile']['error'] == 0) {
            $nombreArchivo = basename($_FILES['xmlFile']['name']);
            $rutaDestino = XML_PATH . $nombreArchivo;
            
            if (move_uploaded_file($_FILES['xmlFile']['tmp_name'], $rutaDestino)) {
                try {
                    $password = $_POST['password'] ?? CERT_PASSWORD;
                    
                    $signer = new SRIXMLSigner(null, $password);
                    $archivoFirmado = $signer->firmarXML($rutaDestino);
                    
                    $mensaje = "✓ XML subido y firmado exitosamente con XAdES-BES";
                    $tipo_mensaje = 'success';
                    
                    // Actualizar lista de archivos
                    $archivosXML = array_diff(scandir(XML_PATH), array('.', '..'));
                    
                } catch (Exception $e) {
                    // Sanitizar mensaje de error para no mostrar rutas del servidor
                    $mensaje = "Error al firmar: " . sanitizarMensajeError($e->getMessage());
                    $tipo_mensaje = 'error';
                }
            } else {
                $mensaje = "Error al subir el archivo";
                $tipo_mensaje = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Firmador de XML con XAdES-BES para SRI Ecuador">
    <title>Firmar XML - XAdES-BES SRI Ecuador</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --error-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            
            --primary-color: #667eea;
            --primary-dark: #5568d3;
            --success-color: #10b981;
            --error-color: #ef4444;
            
            --text-primary: #1a202c;
            --text-secondary: #4a5568;
            --text-light: #718096;
            
            --bg-primary: #0f172a;
            --bg-card: rgba(255, 255, 255, 0.98);
            
            --border-radius: 16px;
            --border-radius-lg: 24px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-primary);
            height: 100vh;
            margin: 0;
            padding: 0;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 40%, rgba(102, 126, 234, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 70% 70%, rgba(240, 147, 251, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 50% 90%, rgba(17, 153, 142, 0.1) 0%, transparent 50%);
            animation: drift 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        
        @keyframes drift {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(30px, -30px) rotate(5deg); }
        }
        
        
        .viewport-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }
        
        .scrollable-content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 16px;
        }
        
        .scrollable-content::-webkit-scrollbar {
            width: 6px;
        }
        
        .scrollable-content::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        
        .scrollable-content::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            padding: 20px 20px;
            border-radius: var(--border-radius);
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            animation: slideUp 0.6s ease-out;
            margin-bottom: 16px;
            position: relative;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--secondary-gradient);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 16px;
        }
        
        .lottie-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 12px;
        }
        
        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(24px, 4vw, 32px);
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .subtitle {
            color: var(--text-secondary);
            font-size: clamp(13px, 2vw, 15px);
            font-weight: 500;
            line-height: 1.5;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            background: var(--primary-gradient);
            color: white;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }
        
        .mensaje {
            padding: 12px 16px;
            border-radius: var(--border-radius);
            margin-bottom: 16px;
            font-size: 14px;
            font-weight: 500;
            animation: slideIn 0.4s ease-out;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .mensaje.success {
            background: linear-gradient(135deg, #d4f8e8 0%, #bef3e0 100%);
            color: #047857;
            border-left: 4px solid var(--success-color);
        }
        
        .mensaje.error {
            background: linear-gradient(135deg, #ffe5e5 0%, #ffd4d4 100%);
            color: #c53030;
            border-left: 4px solid var(--error-color);
        }
        
        .tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 32px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 2px;
        }
        
        .tab {
            padding: 12px 24px;
            background: transparent;
            border: none;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--text-light);
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }
        
        .tab.active {
            color: var(--primary-color);
        }
        
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
            border-radius: 2px 2px 0 0;
        }
        
        .tab:hover {
            color: var(--primary-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .form-group {
            margin-bottom: 24px;
        }
        
        label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.2px;
        }
        
        input[type="text"],
        input[type="password"],
        input[type="file"],
        select {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: var(--border-radius);
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: var(--text-primary);
            background: white;
            transition: var(--transition);
            outline: none;
        }
        
        input:focus,
        select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .file-upload {
            position: relative;
            display: block;
            cursor: pointer;
        }
        
        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 32px;
            border: 2px dashed #cbd5e0;
            border-radius: var(--border-radius);
            background: #f8fafc;
            transition: var(--transition);
        }
        
        .file-upload:hover .file-upload-label {
            border-color: var(--primary-color);
            background: #eef2ff;
        }
        
        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .btn {
            padding: 16px 32px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: var(--transition);
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            text-transform: uppercase;
        }
        
        .btn-primary {
            background: var(--secondary-gradient);
            color: white;
            box-shadow: 0 4px 14px rgba(240, 147, 251, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(240, 147, 251, 0.5);
        }
        
        .archivo-lista {
            max-height: 300px;
            overflow-y: auto;
            border: 2px solid #e2e8f0;
            border-radius: var(--border-radius);
            padding: 16px;
            background: #f8fafc;
        }
        
        .archivo-item {
            padding: 12px 16px;
            background: white;
            border-radius: 12px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: var(--transition);
            cursor: pointer;
        }
        
        .archivo-item:hover {
            background: #eef2ff;
            transform: translateX(4px);
        }
        
        .archivo-item input[type="radio"] {
            margin-right: 12px;
        }
        
        .nav-links {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }
        
        .nav-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            margin: 0 12px;
        }
        
        .nav-links a:hover {
            color: var(--primary-dark);
        }
        
        .info-box {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            padding: 16px 20px;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
            margin-bottom: 24px;
        }
        
        .info-box p {
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.6;
        }
        
        .info-box code {
            background: rgba(102, 126, 234, 0.1);
            padding: 2px 8px;
            border-radius: 6px;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 13px;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        /* Media queries para altura */
        @media (max-height: 750px) {
            .lottie-container {
                width: 60px;
                height: 60px;
                margin: 0 auto 8px;
            }
            
            .header {
                margin-bottom: 12px;
            }
            
            h1 {
                font-size: clamp(20px, 3vw, 28px);
                margin-bottom: 6px;
            }
            
            .card {
                padding: 16px;
            }
        }
        
        @media (max-width: 640px) {
            .card {
                padding: 16px 12px;
            }
            
            h1 {
                font-size: 22px;
            }
            
            .tabs {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="viewport-wrapper">
        <div class="scrollable-content">
            <div class="container">
                <div class="card">
                    <div class="header">
                        <div class="lottie-container" id="lottie-animation"></div>
                        <h1>Firmar XML Electrónico</h1>
                        <p class="subtitle">Firma digital con certificado .p12 - Estándar XAdES-BES</p>
                        <span class="badge">🔒 SRI ECUADOR</span>
                    </div>
            
            <?php if(!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?>">
                    <div>
                        <?php echo $mensaje; ?>
                        <?php if($tipo_mensaje == 'success' && !empty($archivoFirmado)): ?>
                            <br><br><strong>Archivo firmado:</strong> <?php echo basename($archivoFirmado); ?>
                            <br><a href="<?php echo $archivoFirmado; ?>" download style="color: inherit; text-decoration: underline;">Descargar XML firmado</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="tabs">
                <button class="tab active" onclick="switchTab('select')">
                    📄 Seleccionar XML
                </button>
                <button class="tab" onclick="switchTab('upload')">
                    ⬆️ Subir XML
                </button>
            </div>
            
            <!-- Tab: Seleccionar XML -->
            <div id="tab-select" class="tab-content active">
                <?php if(count($archivosXML) > 0): ?>
                    <form method="POST">
                        <div class="form-group">
                            <label>Archivos XML disponibles</label>
                            <div class="archivo-lista">
                                <?php foreach($archivosXML as $archivo): ?>
                                    <?php if(pathinfo($archivo, PATHINFO_EXTENSION) == 'xml'): ?>
                                        <label class="archivo-item">
                                            <div style="display: flex; align-items: center;">
                                                <input type="radio" name="archivoXML" 
                                                       value="<?php echo htmlspecialchars($archivo); ?>" required>
                                                <span>📄 <?php echo htmlspecialchars($archivo); ?></span>
                                            </div>
                                            <small style="color: var(--text-light);">
                                                <?php echo number_format(filesize(XML_PATH . $archivo) / 1024, 2); ?> KB
                                            </small>
                                        </label>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Contraseña del Certificado (opcional)</label>
                            <input type="password" id="password" name="password" 
                                   placeholder="Ingrese la contraseña si su certificado la requiere">
                        </div>
                        
                        <div class="info-box">
                            <p><strong>ℹ️ Información:</strong> El sistema está configurado para usar el certificado 
                            <code><?php echo basename(CERT_FILE); ?></code>. Si necesita cambiar el certificado, 
                            actualice la configuración del sistema.</p>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            🔐 Firmar XML con XAdES-BES
                        </button>
                    </form>
                <?php else: ?>
                    <div class="info-box">
                        <p><strong>⚠️ No hay archivos XML disponibles</strong><br>
                        Genere primero una factura o suba un archivo XML.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Tab: Subir XML -->
            <div id="tab-upload" class="tab-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Subir archivo XML</label>
                        <div class="file-upload">
                            <input type="file" name="xmlFile" accept=".xml" required 
                                   onchange="updateFileName(this)">
                            <div class="file-upload-label">
                                <span id="file-name">📁 Seleccionar archivo XML</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password2">Contraseña del Certificado (opcional)</label>
                        <input type="password" id="password2" name="password" 
                               placeholder="Ingrese la contraseña si su certificado la requiere">
                    </div>
                    
                    <div class="info-box">
                        <p><strong>ℹ️ Información:</strong> El sistema está configurado para usar el certificado 
                        <code><?php echo basename(CERT_FILE); ?></code>. Si necesita cambiar el certificado, 
                        actualice la configuración del sistema.</p>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        🔐 Subir y Firmar XML
                    </button>
                </form>
            </div>
            
            <div class="nav-links">
                <a href="generar_factura.php">← Generar Factura</a>
                <span style="color: var(--text-light);">|</span>
                <a href="test.php">Ver Test</a>
            </div>
        </div>
    </div>
    
    <!-- Lottie Animation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script>
        // Cargar animación Lottie - Firma digital
        lottie.loadAnimation({
            container: document.getElementById('lottie-animation'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'https://lottie.host/embed/b0497c67-4c7b-4e10-9d71-5aa26fe0e57b/WKwnvMpqpV.json'
        });
        
        function switchTab(tabName) {
            // Ocultar todos los contenidos
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Desactivar todos los tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Activar el tab seleccionado
            document.getElementById('tab-' + tabName).classList.add('active');
            event.target.classList.add('active');
        }
        
        function updateFileName(input) {
            const fileName = input.files[0]?.name || '📁 Seleccionar archivo XML';
            document.getElementById('file-name').textContent = '📄 ' + fileName;
        }
    </script>
            </div>
        </div>
    </div>
</body>
</html>
