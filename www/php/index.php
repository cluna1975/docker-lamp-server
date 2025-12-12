<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Facturación Electrónica SRI Ecuador - Generación y Firma XML con XAdES-BES">
    <title>Sistema de Facturación Electrónica - SRI Ecuador</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
    
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
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            
            --primary-color: #667eea;
            --secondary-color: #f093fb;
            --success-color: #11998e;
            --accent-color: #4facfe;
            
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
            background: 
                radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(240, 147, 251, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 90%, rgba(17, 153, 142, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 60% 30%, rgba(79, 172, 254, 0.1) 0%, transparent 50%);
            animation: drift 25s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        
        @keyframes drift {
            0%, 100% { 
                transform: translate(0, 0) rotate(0deg);
                opacity: 1;
            }
            50% { 
                transform: translate(30px, -30px) rotate(5deg);
                opacity: 0.8;
            }
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
            padding: 20px 16px;
        }
        
        /* Scrollbar personalizado */
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
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .hero {
            text-align: center;
            margin-bottom: 24px;
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
        
        .lottie-hero {
            width: 100px;
            height: 100px;
            margin: 0 auto 16px;
        }
        
        .hero h1 {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(28px, 5vw, 42px);
            font-weight: 900;
            color: white;
            margin-bottom: 12px;
            letter-spacing: -1px;
            line-height: 1.1;
        }
        
        .hero .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #f093fb 50%, #38ef7d 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 5s ease infinite;
            background-size: 200% 200%;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .hero p {
            color: #cbd5e0;
            font-size: clamp(14px, 2vw, 16px);
            font-weight: 500;
            max-width: 600px;
            margin: 0 auto 16px;
            line-height: 1.5;
        }
        
        .badges {
            display: flex;
            justify-content: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            color: white;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            margin-bottom: 16px;
        }
        
        .card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            padding: 20px 16px;
            border-radius: var(--border-radius);
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
            animation-fill-mode: both;
        }
        
        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        
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
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            opacity: 0;
            transition: var(--transition);
        }
        
        .card.primary::before {
            background: var(--primary-gradient);
        }
        
        .card.secondary::before {
            background: var(--secondary-gradient);
        }
        
        .card.success::before {
            background: var(--success-gradient);
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 
                0 30px 80px rgba(0, 0, 0, 0.4),
                0 0 0 1px rgba(255, 255, 255, 0.2) inset;
        }
        
        .card:hover::before {
            opacity: 1;
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            margin: 0 auto 12px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            position: relative;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .card.primary .card-icon {
            background: var(--primary-gradient);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        }
        
        .card.secondary .card-icon {
            background: var(--secondary-gradient);
            box-shadow: 0 8px 24px rgba(240, 147, 251, 0.3);
        }
        
        .card.success .card-icon {
            background: var(--success-gradient);
            box-shadow: 0 8px 24px rgba(17, 153, 142, 0.3);
        }
        
        .card-title {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 8px;
            text-align: center;
        }
        
        .card-description {
            color: var(--text-secondary);
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 12px;
            text-align: center;
        }
        
        .card-features {
            list-style: none;
            margin-bottom: 12px;
        }
        
        .card-features li {
            padding: 6px 0;
            color: var(--text-secondary);
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .card-features li::before {
            content: '✓';
            width: 18px;
            height: 18px;
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 10px;
            flex-shrink: 0;
        }
        
        .btn {
            width: 100%;
            padding: 10px 20px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 13px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: var(--transition);
            letter-spacing: 0.5px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-transform: uppercase;
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(102, 126, 234, 0.5);
        }
        
        .btn-secondary {
            background: var(--secondary-gradient);
            color: white;
            box-shadow: 0 4px 14px rgba(240, 147, 251, 0.4);
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(240, 147, 251, 0.5);
        }
        
        .btn-success {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 4px 14px rgba(17, 153, 142, 0.4);
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(17, 153, 142, 0.5);
        }
        
        .footer {
            text-align: center;
            padding: 16px 0;
            color: #94a3b8;
            font-size: 12px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 16px;
        }
        
        .footer p {
            margin-bottom: 8px;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        
        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 11px;
            transition: var(--transition);
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        /* Media queries para altura */
        @media (max-height: 800px) {
            .lottie-hero {
                width: 80px;
                height: 80px;
                margin: 0 auto 12px;
            }
            
            .hero {
                margin-bottom: 16px;
            }
            
            .hero h1 {
                font-size: clamp(24px, 4vw, 36px);
                margin-bottom: 8px;
            }
            
            .hero p {
                margin: 0 auto 12px;
            }
            
            .badges {
                margin-bottom: 12px;
            }
        }
        
        @media (max-height: 700px) {
            .lottie-hero {
                width: 60px;
                height: 60px;
                margin: 0 auto 8px;
            }
            
            .hero {
                margin-bottom: 12px;
            }
            
            .card {
                padding: 16px 12px;
            }
            
            .card-features {
                margin-bottom: 8px;
            }
            
            .footer {
                padding: 12px 0;
                margin-top: 12px;
            }
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 28px;
            }
            
            .hero p {
                font-size: 14px;
            }
            
            .cards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="viewport-wrapper">
        <div class="scrollable-content">
            <div class="container">
                <div class="hero">
                    <div class="lottie-hero" id="lottie-hero"></div>
                    <h1>
                        Sistema de <span class="gradient-text">Facturación Electrónica</span>
                    </h1>
            <p>
                Genera y firma tus comprobantes electrónicos con el estándar XAdES-BES 
                para el Servicio de Rentas Internas del Ecuador
            </p>
            <div class="badges">
                <span class="badge">
                    🇪🇨 SRI Ecuador
                </span>
                <span class="badge">
                    🔐 XAdES-BES
                </span>
                <span class="badge">
                    ⚡ Certificado .p12
                </span>
            </div>
        </div>
        
        <div class="cards-grid">
            <div class="card primary">
                <div class="card-icon">📄</div>
                <h2 class="card-title">Generar Facturas</h2>
                <p class="card-description">
                    Crea facturas electrónicas en formato XML siguiendo las 
                    especificaciones del SRI Ecuador
                </p>
                <ul class="card-features">
                    <li>Generación automática de clave de acceso</li>
                    <li>Validación de datos según normas SRI</li>
                    <li>Cálculo automático de impuestos</li>
                    <li>Múltiples detalles por factura</li>
                </ul>
                <a href="public/generar_factura.php" class="btn btn-primary">
                    Generar Factura →
                </a>
            </div>
            
            <div class="card secondary">
                <div class="card-icon">🔐</div>
                <h2 class="card-title">Firmar XML</h2>
                <p class="card-description">
                    Firma digitalmente tus archivos XML con certificado .p12 
                    utilizando el estándar XAdES-BES
                </p>
                <ul class="card-features">
                    <li>Firma con certificado digital .p12</li>
                    <li>Estándar XAdES-BES certificado</li>
                    <li>Seleccionar o subir archivos XML</li>
                    <li>Verificación de firma incluida</li>
                </ul>
                <a href="public/firmar_xml.php" class="btn btn-secondary">
                    Firmar Documento →
                </a>
            </div>
            
            <div class="card success">
                <div class="card-icon">🧪</div>
                <h2 class="card-title">Modo Prueba</h2>
                <p class="card-description">
                    Prueba el formulario de validación y conoce las funcionalidades 
                    del sistema de firma digital
                </p>
                <ul class="card-features">
                    <li>Interfaz de prueba completa</li>
                    <li>Validación de formularios</li>
                    <li>Diseño moderno y responsivo</li>
                    <li>Animaciones y efectos visuales</li>
                </ul>
                <a href="test.php" class="btn btn-success">
                    Ver Prueba →
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>Sistema de Facturación Electrónica</strong> - SRI Ecuador</p>
            <p>Desarrollado con tecnología moderna PHP + XAdES-BES</p>
            <div class="footer-links">
                <a href="https://www.sri.gob.ec" target="_blank">🌐 SRI Ecuador</a>
                <a href="public/guia.php">📚 Guía de Uso</a>
                <a href="docs/README.md">📖 Documentación</a>
            </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Lottie Animation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script>
        // Cargar animación Lottie principal
        lottie.loadAnimation({
            container: document.getElementById('lottie-hero'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'https://lottie.host/embed/a7d3e8e5-47fe-4e4f-9e8e-9a0c1a0a5a5a/wTsKVqZlr5.json'
        });
    </script>
</body>
</html>
