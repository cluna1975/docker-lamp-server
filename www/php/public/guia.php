<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Guía de Uso - Sistema de Facturación Electrónica SRI">
    <title>Guía de Uso - Sistema SRI</title>
    
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
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            
            --primary-color: #667eea;
            --success-color: #10b981;
            --info-color: #4facfe;
            --warning-color: #f59e0b;
            
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
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            padding: 40px 20px;
            color: var(--text-primary);
        }
        
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(79, 172, 254, 0.15) 0%, transparent 50%);
            animation: drift 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        
        @keyframes drift {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(30px, -30px) rotate(5deg); }
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .header {
            text-align: center;
            margin-bottom: 50px;
            animation: fadeInDown 0.8s ease-out;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 48px;
            font-weight: 800;
            color: white;
            margin-bottom: 16px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .header p {
            color: #cbd5e0;
            font-size: 18px;
        }
        
        .card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            margin-bottom: 32px;
            animation: slideUp 0.6s ease-out;
            animation-fill-mode: both;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .section {
            margin-bottom: 40px;
        }
        
        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .section-title::before {
            content: '';
            width: 5px;
            height: 32px;
            background: var(--primary-gradient);
            border-radius: 3px;
        }
        
        .step {
            margin-bottom: 24px;
            padding: 24px;
            background: #f8fafc;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--primary-color);
            transition: var(--transition);
        }
        
        .step:hover {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
            transform: translateX(4px);
        }
        
        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background: var(--primary-gradient);
            color: white;
            border-radius: 50%;
            font-weight: 700;
            font-size: 18px;
            margin-right: 12px;
        }
        
        .step-title {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        
        .step-content {
            color: var(--text-secondary);
            font-size: 15px;
            line-height: 1.7;
            margin-left: 48px;
        }
        
        .step-content ul {
            margin-top: 8px;
            margin-left: 20px;
        }
        
        .step-content li {
            margin-bottom: 6px;
        }
        
        .code-block {
            background: #1e293b;
            color: #e2e8f0;
            padding: 16px;
            border-radius: 12px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            margin: 12px 0;
            overflow-x: auto;
        }
        
        .info-box {
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
            padding: 20px 24px;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--info-color);
            margin: 20px 0;
        }
        
        .info-box strong {
            color: var(--primary-color);
        }
        
        .warning-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 20px 24px;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--warning-color);
            margin: 20px 0;
        }
        
        .warning-box strong {
            color: #d97706;
        }
        
        .success-box {
            background: linear-gradient(135deg, #d4f8e8 0%, #bef3e0 100%);
            padding: 20px 24px;
            border-radius: var(--border-radius);
            border-left: 4px solid var(--success-color);
            margin: 20px 0;
        }
        
        .success-box strong {
            color: #047857;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            background: var(--primary-gradient);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            font-weight: 700;
            transition: var(--transition);
            box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.5);
        }
        
        .back-link {
            text-align: center;
            margin-top: 40px;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 32px;
            }
            
            .card {
                padding: 24px;
            }
            
            .step-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Guía de Uso</h1>
            <p>Aprende a usar el Sistema de Facturación Electrónica</p>
        </div>
        
        <div class="card">
            <div class="section">
                <h2 class="section-title">🚀 Inicio Rápido</h2>
                
                <div class="info-box">
                    <strong>ℹ️ Antes de comenzar:</strong> Asegúrate de tener un certificado digital .p12 
                    válido y configurado en el archivo <code>config.php</code>
                </div>
                
                <div class="step">
                    <div class="step-title">
                        <span class="step-number">1</span>
                        Configurar el Sistema
                    </div>
                    <div class="step-content">
                        Editar el archivo <strong>config.php</strong> con tus datos:
                        <div class="code-block">
define('EMPRESA_RUC', '1234567890001');<br>
define('EMPRESA_RAZON_SOCIAL', 'TU EMPRESA S.A.');<br>
define('EMPRESA_DIRECCION', 'Tu Dirección');<br>
define('CERT_FILE', 'tu_certificado.p12');<br>
define('CERT_PASSWORD', 'tu_contraseña');
                        </div>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-title">
                        <span class="step-number">2</span>
                        Verificar Requisitos
                    </div>
                    <div class="step-content">
                        Asegúrate de tener instalado:
                        <ul>
                            <li>PHP 7.4 o superior</li>
                            <li>Extensión OpenSSL habilitada</li>
                            <li>Extensión DOM habilitada</li>
                            <li>Permisos de escritura en directorios</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title">📄 Generar Factura</h2>
                
                <div class="step">
                    <div class="step-title">
                        <span class="step-number">1</span>
                        Acceder al Formulario
                    </div>
                    <div class="step-content">
                        Ir a <strong>generar_factura.php</strong> desde el menú principal
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-title">
                        <span class="step-number">2</span>
                        Completar Datos
                    </div>
                    <div class="step-content">
                        Ingresar la información requerida:
                        <ul>
                            <li><strong>Información General:</strong> Fecha y número secuencial</li>
                            <li><strong>Datos del Cliente:</strong> RUC/Cédula, razón social, dirección</li>
                            <li><strong>Detalles:</strong> Productos/servicios con precios e IVA</li>
                        </ul>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-title">
                        <span class="step-number">3</span>
                        Generar XML
                    </div>
                    <div class="step-content">
                        Click en <strong>"Generar Factura XML"</strong>. El sistema:
                        <ul>
                            <li>Calcula automáticamente los totales e impuestos</li>
                            <li>Genera la clave de acceso de 49 dígitos</li>
                            <li>Crea el archivo XML en formato SRI</li>
                            <li>Guarda en el directorio <code>xml_generados/</code></li>
                        </ul>
                    </div>
                </div>
                
                <div class="success-box">
                    <strong>✓ Éxito:</strong> El archivo XML ha sido generado y está listo para ser firmado.
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title">🔐 Firmar XML</h2>
                
                <div class="step">
                    <div class="step-title">
                        <span class="step-number">1</span>
                        Seleccionar Método
                    </div>
                    <div class="step-content">
                        En <strong>firmar_xml.php</strong> elige:
                        <ul>
                            <li><strong>Seleccionar XML:</strong> Elegir de archivos ya generados</li>
                            <li><strong>Subir XML:</strong> Cargar un archivo desde tu computadora</li>
                        </ul>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-title">
                        <span class="step-number">2</span>
                        Configurar Certificado
                    </div>
                    <div class="step-content">
                        <ul>
                            <li>El certificado .p12 debe estar en el directorio raíz</li>
                            <li>Ingresar la contraseña si el certificado la requiere</li>
                        </ul>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-title">
                        <span class="step-number">3</span>
                        Firmar Documento
                    </div>
                    <div class="step-content">
                        Click en <strong>"Firmar XML con XAdES-BES"</strong>. El sistema:
                        <ul>
                            <li>Carga el certificado digital</li>
                            <li>Aplica firma XAdES-BES según estándares</li>
                            <li>Guarda el XML firmado en <code>xml_firmados/</code></li>
                            <li>Permite descargar el archivo</li>
                        </ul>
                    </div>
                </div>
                
                <div class="warning-box">
                    <strong>⚠️ Importante:</strong> El certificado debe estar vigente y la contraseña 
                    debe ser correcta para que la firma sea válida.
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title">🛠️ Solución de Problemas</h2>
                
                <div class="step">
                    <div class="step-title">
                        <span class="step-number">❌</span>
                        Error: Certificado no válido
                    </div>
                    <div class="step-content">
                        <strong>Solución:</strong>
                        <ul>
                            <li>Verificar que el archivo .p12 existe en el directorio</li>
                            <li>Confirmar que la contraseña es correcta</li>
                            <li>Verificar que el certificado esté vigente</li>
                        </ul>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-title">
                        <span class="step-number">❌</span>
                        Error: No se puede escribir archivo
                    </div>
                    <div class="step-content">
                        <strong>Solución:</strong>
                        <ul>
                            <li>Verificar permisos de los directorios (755 o 777)</li>
                            <li>Asegurar que Apache/Nginx tenga permisos de escritura</li>
                        </ul>
                        <div class="code-block">
chmod 755 xml_generados/<br>
chmod 755 xml_firmados/
                        </div>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-title">
                        <span class="step-number">❌</span>
                        Error: XML no válido
                    </div>
                    <div class="step-content">
                        <strong>Solución:</strong>
                        <ul>
                            <li>Verificar que todos los campos obligatorios estén completos</li>
                            <li>Revisar que el RUC tenga exactamente 13 dígitos</li>
                            <li>Confirmar que los valores numéricos sean válidos</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2 class="section-title">📊 Estructura del XML</h2>
                
                <div class="info-box">
                    El XML generado cumple con las especificaciones del SRI Ecuador e incluye:
                    <ul style="margin-top: 12px; margin-left: 20px;">
                        <li><strong>infoTributaria:</strong> Datos del emisor y clave de acceso</li>
                        <li><strong>infoFactura:</strong> Información de la factura y cliente</li>
                        <li><strong>detalles:</strong> Productos/servicios con impuestos</li>
                        <li><strong>Firma XAdES-BES:</strong> Firma digital certificada (después de firmar)</li>
                    </ul>
                </div>
            </div>
            
            <div class="back-link">
                <a href="index.php" class="btn">
                    ← Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</body>
</html>
