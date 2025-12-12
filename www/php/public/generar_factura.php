<?php
require_once __DIR__ . '/../config.php';
require_once SRC_PATH . 'SRIXMLGenerator.php';

$mensaje = '';
$tipo_mensaje = '';
$errores = [];
$claveAcceso = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validaciones
    if (empty($_POST['identificacionComprador'])) {
        $errores['identificacionComprador'] = 'La identificación del comprador es obligatoria';
    }
    
    if (empty($_POST['razonSocialComprador'])) {
        $errores['razonSocialComprador'] = 'La razón social del comprador es obligatoria';
    }
    
    if (empty($_POST['detalles']) || count($_POST['detalles']) == 0) {
        $errores['detalles'] = 'Debe agregar al menos un detalle';
    }
    
    if (empty($errores)) {
        try {
            $generator = new SRIXMLGenerator();
            
            // Configurar información tributaria
            $generator->setInfoTributaria([
                'fecha' => $_POST['fechaEmision'],
                'secuencial' => $_POST['secuencial'] ?? '1'
            ]);
            
            // Calcular totales
            $totalSinImpuestos = 0;
            $totalDescuento = 0;
            $baseIva12 = 0;
            $baseIva0 = 0;
            
            foreach ($_POST['detalles'] as $det) {
                $subtotal = $det['cantidad'] * $det['precioUnitario'];
                $descuento = $det['descuento'] ?? 0;
                $subtotalConDescuento = $subtotal - $descuento;
                $totalSinImpuestos += $subtotalConDescuento;
                $totalDescuento += $descuento;
                
                if ($det['iva'] == '12') {
                    $baseIva12 += $subtotalConDescuento;
                } else {
                    $baseIva0 += $subtotalConDescuento;
                }
            }
            
            $valorIva12 = $baseIva12 * 0.12;
            $importeTotal = $totalSinImpuestos + $valorIva12;
            
            // Configurar información de la factura
            $generator->setInfoFactura([
                'fechaEmision' => $_POST['fechaEmision'],
                'razonSocialComprador' => $_POST['razonSocialComprador'],
                'identificacionComprador' => $_POST['identificacionComprador'],
                'direccionComprador' => $_POST['direccionComprador'] ?? 'N/A',
                'totalSinImpuestos' => $totalSinImpuestos,
                'totalDescuento' => $totalDescuento,
                'importeTotal' => $importeTotal,
                'impuestos' => [
                    [
                        'codigo' => '2',
                        'codigoPorcentaje' => '2',
                        'baseImponible' => $baseIva12,
                        'valor' => $valorIva12
                    ],
                    [
                        'codigo' => '2',
                        'codigoPorcentaje' => '0',
                        'baseImponible' => $baseIva0,
                        'valor' => 0
                    ]
                ]
            ]);
            
            // Agregar detalles
            foreach ($_POST['detalles'] as $det) {
                $subtotal = $det['cantidad'] * $det['precioUnitario'];
                $descuento = $det['descuento'] ?? 0;
                
                $generator->addDetalle([
                    'codigoPrincipal' => $det['codigo'],
                    'descripcion' => $det['descripcion'],
                    'cantidad' => $det['cantidad'],
                    'precioUnitario' => $det['precioUnitario'],
                    'descuento' => $descuento,
                    'precioTotalSinImpuesto' => $subtotal - $descuento,
                    'impuestos' => [
                        [
                            'codigo' => '2',
                            'codigoPorcentaje' => $det['iva'] == '12' ? '2' : '0',
                            'tarifa' => $det['iva'],
                            'baseImponible' => $subtotal - $descuento,
                            'valor' => $det['iva'] == '12' ? ($subtotal - $descuento) * 0.12 : 0
                        ]
                    ]
                ]);
            }
            
            // Guardar XML
            $nombreArchivo = $generator->guardarXML();
            $claveAcceso = $generator->getClaveAcceso();
            
            $mensaje = "✓ XML generado exitosamente: {$nombreArchivo}";
            $tipo_mensaje = 'success';
            
        } catch (Exception $e) {
            // Sanitizar mensaje de error para no mostrar rutas del servidor
            $mensaje = "Error al generar el XML: " . sanitizarMensajeError($e->getMessage());
            $tipo_mensaje = 'error';
        }
    } else {
        $tipo_mensaje = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Generador de Facturas Electrónicas SRI Ecuador">
    <title>Generar Factura Electrónica - SRI Ecuador</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- jQuery (requerido para DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables CSS y JS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            --bg-secondary: #1e293b;
            --bg-card: rgba(255, 255, 255, 0.98);
            
            --border-radius: 16px;
            --border-radius-lg: 24px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 20% 50%, rgba(102, 126, 234, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(118, 75, 162, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 40% 90%, rgba(56, 239, 125, 0.1) 0%, transparent 50%);
            animation: drift 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        
        @keyframes drift {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(30px, -30px) rotate(5deg); }
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            padding: 48px 40px;
            border-radius: var(--border-radius-lg);
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.3),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            animation: slideUp 0.6s ease-out;
            margin-bottom: 30px;
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
            height: 6px;
            background: var(--primary-gradient);
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .lottie-container {
            width: 150px;
            height: 150px;
            margin: 0 auto 24px;
        }
        
        h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 36px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 12px;
            letter-spacing: -0.5px;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .subtitle {
            color: var(--text-secondary);
            font-size: 16px;
            font-weight: 500;
            line-height: 1.6;
        }
        
        .mensaje {
            padding: 18px 24px;
            border-radius: var(--border-radius);
            margin-bottom: 28px;
            font-size: 15px;
            font-weight: 500;
            animation: slideIn 0.4s ease-out;
            display: flex;
            align-items: center;
            gap: 12px;
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
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }
        
        .form-group {
            position: relative;
        }
        
        .form-group.full-width {
            grid-column: 1 / -1;
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
        input[type="number"],
        input[type="date"],
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
        
        .error-message {
            color: var(--error-color);
            font-size: 13px;
            margin-top: 8px;
            font-weight: 500;
        }
        
        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 32px 0 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .section-title::before {
            content: '';
            width: 4px;
            height: 28px;
            background: var(--primary-gradient);
            border-radius: 2px;
        }
        
        .detalle-item {
            background: #f8fafc;
            padding: 24px;
            border-radius: var(--border-radius);
            margin-bottom: 16px;
            border: 2px solid #e2e8f0;
            transition: var(--transition);
            position: relative;
        }
        
        .detalle-item:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: var(--border-radius);
            font-size: 15px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: var(--transition);
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
        
        .btn-success {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 4px 14px rgba(17, 153, 142, 0.4);
            width: 100%;
            justify-content: center;
            padding: 18px;
            font-size: 16px;
            margin-top: 24px;
            text-transform: uppercase;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(17, 153, 142, 0.5);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }
        
        .btn-remove {
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--error-gradient);
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-size: 18px;
        }
        
        .btn-remove:hover {
            transform: scale(1.1);
        }
        
        .nav-links {
            text-align: center;
            margin-top: 24px;
        }
        
        .nav-links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .nav-links a:hover {
            color: var(--primary-dark);
        }
        
        
        /* Estilos personalizados para DataTables */
        .dataTables_wrapper {
            margin: 20px 0;
        }
        
        #detallesTable {
            width: 100% !important;
            border-collapse: collapse;
        }
        
        #detallesTable thead {
            background: var(--primary-gradient);
            color: white;
        }
        
        #detallesTable thead th {
            padding: 12px;
            font-weight: 600;
            font-size: 13px;
            text-align: left;
            border: none !important;
        }
        
        #detallesTable tbody td {
            padding: 10px 12px;
            font-size: 13px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        #detallesTable tbody tr:hover {
            background: #f8fafc;
        }
        
        .dataTables_filter input {
            padding: 8px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
            margin-left: 8px;
        }
        
        .dataTables_filter input:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        
        .dataTables_length select {
            padding: 6px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            margin: 0 8px;
        }
        
        .dataTables_info,
        .dataTables_paginate {
            font-size: 13px;
            color: var(--text-secondary);
        }
        
        .paginate_button {
            padding: 6px 12px !important;
            margin: 0 2px !important;
            border-radius: 6px !important;
            border: 1px solid #e2e8f0 !important;
            background: white !important;
            color: var(--text-primary) !important;
        }
        
        .paginate_button:hover {
            background: var(--primary-color) !important;
            color: white !important;
            border-color: var(--primary-color) !important;
        }
        
        .paginate_button.current {
            background: var(--primary-gradient) !important;
            color: white !important;
            border: none !important;
        }
        
        .btn-delete {
            background: var(--error-gradient);
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .btn-delete:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(250, 112, 154, 0.3);
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 16px;
            max-width: 600px;
            animation: slideUp 0.3s ease-out;
        }
        
        .modal-content h3 {
            color: var(--text-primary);
            margin-bottom: 20px;
            font-family: 'Outfit', sans-serif;
        }
        
        @media (max-width: 640px) {
            .card {
                padding: 32px 24px;
            }
            
            h1 {
                font-size: 28px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            #detallesTable {
                font-size: 11px;
            }
            
            #detallesTable thead th,
            #detallesTable tbody td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <div class="lottie-container" id="lottie-animation"></div>
                <h1>Generar Factura Electrónica</h1>
                <p class="subtitle">Sistema de Facturación Electrónica - SRI Ecuador</p>
            </div>
            
            <?php if(!empty($mensaje)): ?>
                <div class="mensaje <?php echo $tipo_mensaje; ?>">
                    <?php echo $mensaje; ?>
                    <?php if($tipo_mensaje == 'success' && !empty($claveAcceso)): ?>
                        <br><strong>Clave de acceso:</strong> <?php echo $claveAcceso; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="facturaForm">
                <h2 class="section-title">Información General</h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="fechaEmision">Fecha de Emisión *</label>
                        <input type="date" id="fechaEmision" name="fechaEmision" 
                               value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="secuencial">Secuencial *</label>
                        <input type="text" id="secuencial" name="secuencial" 
                               value="1" required>
                    </div>
                </div>
                
                <h2 class="section-title">Datos del Cliente</h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="identificacionComprador">RUC/Cédula *</label>
                        <input type="text" id="identificacionComprador" 
                               name="identificacionComprador" required>
                        <?php if(isset($errores['identificacionComprador'])): ?>
                            <div class="error-message"><?php echo $errores['identificacionComprador']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="razonSocialComprador">Razón Social *</label>
                        <input type="text" id="razonSocialComprador" 
                               name="razonSocialComprador" required>
                        <?php if(isset($errores['razonSocialComprador'])): ?>
                            <div class="error-message"><?php echo $errores['razonSocialComprador']; ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group full-width">
                        <label for="direccionComprador">Dirección</label>
                        <input type="text" id="direccionComprador" 
                               name="direccionComprador">
                    </div>
                </div>
                
                <h2 class="section-title">Detalles de la Factura</h2>
                
                <button type="button" class="btn btn-primary" onclick="abrirModal()" style="margin-bottom: 16px;">
                    + Agregar Producto
                </button>
                
                <!-- Tabla con DataTables -->
                <table id="detallesTable" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Cantidad</th>
                            <th>P. Unit.</th>
                            <th>Desc.</th>
                            <th>IVA</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Los datos se agregan dinámicamente -->
                    </tbody>
                </table>
                
                <!-- Inputs hidden para enviar al servidor -->
                <div id="hiddenInputs"></div>
                
                <button type="submit" class="btn btn-success">
                    🚀 Generar Factura XML
                </button>
            </form>
            
            <!-- Modal para agregar producto -->
            <div id="modalProducto" class="modal">
                <div class="modal-content">
                    <h3>📦 Agregar Producto</h3>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Código *</label>
                            <input type="text" id="modalCodigo" required>
                        </div>
                        
                        <div class="form-group full-width">
                            <label>Descripción *</label>
                            <input type="text" id="modalDescripcion" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Cantidad *</label>
                            <input type="number" id="modalCantidad" step="0.01" value="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Precio Unitario *</label>
                            <input type="number" id="modalPrecio" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Descuento</label>
                            <input type="number" id="modalDescuento" step="0.01" value="0">
                        </div>
                        
                        <div class="form-group">
                            <label>IVA *</label>
                            <select id="modalIva" required>
                                <option value="0">0%</option>
                                <option value="12" selected>12%</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 12px; margin-top: 20px;">
                        <button type="button" class="btn btn-success" onclick="agregarProducto()" style="flex: 1;">
                            ✓ Agregar
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="cerrarModal()" style="flex: 1;">
                            ✗ Cancelar
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="nav-links">
                <a href="firmar_xml.php">→ Ir a Firmar XML</a>
            </div>
        </div>
    </div>
    
    <!-- Lottie Animation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script>
        // Cargar animación Lottie
        lottie.loadAnimation({
            container: document.getElementById('lottie-animation'),
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: 'https://lottie.host/4f3f0c9c-3e3e-4c3e-9c3e-3e3e3e3e3e3e/data.json'
        });
        
        // Inicializar DataTable
        let table;
        let detalleCount = 0;
        
        $(document).ready(function() {
            table = $('#detallesTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                    search: "🔍 Buscar:",
                    lengthMenu: "Mostrar _MENU_ productos",
                    info: "Mostrando _START_ a _END_ de _TOTAL_ productos",
                    infoEmpty: "No hay productos",
                    infoFiltered: "(filtrado de _MAX_ productos totales)",
                    zeroRecords: "No se encontraron productos",
                    emptyTable: "📦 Agregue productos a la factura",
                    paginate: {
                        first: "Primero",
                        last: "Último",
                        next: "Siguiente",
                        previous: "Anterior"
                    }
                },
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [7] } // Columna de Acciones no ordenable
                ]
            });
            
            // Actualizar inputs hidden antes de submit
            $('#facturaForm').on('submit', function(e) {
                actualizarHiddenInputs();
                
                // Validar que haya al menos un producto
                if (table.rows().count() === 0) {
                    e.preventDefault();
                    alert('⚠️ Debe agregar al menos un producto a la factura');
                    return false;
                }
            });
        });
        
        // Abrir modal
        function abrirModal() {
            document.getElementById('modalProducto').style.display = 'block';
            document.getElementById('modalCodigo').focus();
        }
        
        // Cerrar modal
        function cerrarModal() {
            document.getElementById('modalProducto').style.display = 'none';
            limpiarModal();
        }
        
        // Limpiar campos del modal
        function limpiarModal() {
            document.getElementById('modalCodigo').value = '';
            document.getElementById('modalDescripcion').value = '';
            document.getElementById('modalCantidad').value = '1';
            document.getElementById('modalPrecio').value = '';
            document.getElementById('modalDescuento').value = '0';
            document.getElementById('modalIva').value = '12';
        }
        
        // Agregar producto a la tabla
        function agregarProducto() {
            const codigo = document.getElementById('modalCodigo').value.trim();
            const descripcion = document.getElementById('modalDescripcion').value.trim();
            const cantidad = parseFloat(document.getElementById('modalCantidad').value) || 0;
            const precio = parseFloat(document.getElementById('modalPrecio').value) || 0;
            const descuento = parseFloat(document.getElementById('modalDescuento').value) || 0;
            const iva = document.getElementById('modalIva').value;
            
            // Validación
            if (!codigo || !descripcion || cantidad <= 0 || precio <= 0) {
                alert('⚠️ Por favor complete todos los campos requeridos correctamente');
                return;
            }
            
            // Calcular total
            const subtotal = cantidad * precio;
            const subtotalConDesc = subtotal - descuento;
            const valorIva = iva === '12' ? subtotalConDesc * 0.12 : 0;
            const total = subtotalConDesc + valorIva;
            
            // Agregar fila a DataTable
            const rowNode = table.row.add([
                codigo,
                descripcion,
                cantidad.toFixed(2),
                '$' + precio.toFixed(2),
                '$' + descuento.toFixed(2),
                iva + '%',
                '$' + total.toFixed(2),
                '<button type="button" class="btn-delete" onclick="eliminarFila(this)">🗑️ Eliminar</button>'
            ]).draw(false).node();
            
            // Guardar datos en el nodo para recuperar después
            $(rowNode).data('producto', {
                codigo: codigo,
                descripcion: descripcion,
                cantidad: cantidad,
                precioUnitario: precio,
                descuento: descuento,
                iva: iva
            });
            
            cerrarModal();
            actualizarHiddenInputs();
        }
        
        // Eliminar fila de la tabla
        function eliminarFila(btn) {
            if (confirm('¿Está seguro de eliminar este producto?')) {
                table.row($(btn).parents('tr')).remove().draw();
                actualizarHiddenInputs();
            }
        }
        
        // Actualizar inputs hidden para enviar al servidor
        function actualizarHiddenInputs() {
            const container = document.getElementById('hiddenInputs');
            container.innerHTML = '';
            
            let index = 0;
            table.rows().every(function() {
                const row = this.node();
                const data = $(row).data('producto');
                
                if (data) {
                    container.innerHTML += `
                        <input type="hidden" name="detalles[${index}][codigo]" value="${data.codigo}">
                        <input type="hidden" name="detalles[${index}][descripcion]" value="${data.descripcion}">
                        <input type="hidden" name="detalles[${index}][cantidad]" value="${data.cantidad}">
                        <input type="hidden" name="detalles[${index}][precioUnitario]" value="${data.precioUnitario}">
                        <input type="hidden" name="detalles[${index}][descuento]" value="${data.descuento}">
                        <input type="hidden" name="detalles[${index}][iva]" value="${data.iva}">
                    `;
                    index++;
                }
            });
        }
        
        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('modalProducto');
            if (event.target == modal) {
                cerrarModal();
            }
        }
        
        // Manejar Enter en el modal
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && document.getElementById('modalProducto').style.display === 'block') {
                e.preventDefault();
                if (e.target.id !== 'modalDescripcion') { // Permitir Enter en descripción
                    agregarProducto();
                }
            }
            if (e.key === 'Escape') {
                cerrarModal();
            }
        });
    </script>
</body>
</html>
