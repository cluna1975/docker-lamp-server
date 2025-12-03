-- -----------------------------------------------------
-- Schema SRI_Facturacion_Electronica
-- Basado en Ficha Técnica Offline v2.32 (Oct 2025)
-- -----------------------------------------------------
CREATE DATABASE IF NOT EXISTS sri_fe_schema CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sri_fe_schema;

-- ==================================================================
-- 1. TABLAS DE CATÁLOGOS (MAESTROS) DEL SRI
-- ==================================================================

-- Tabla 2: Tipo de Emisión
CREATE TABLE sri_tipo_emision (
    codigo CHAR(1) PRIMARY KEY,
    descripcion VARCHAR(50) NOT NULL
);

-- Tabla 3: Tipos de Comprobantes
CREATE TABLE sri_tipo_comprobante (
    codigo CHAR(2) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL,
    tag_xml VARCHAR(50) -- Etiqueta raíz en el XML
);

-- Tabla 4: Tipo de Ambiente
CREATE TABLE sri_ambiente (
    codigo CHAR(1) PRIMARY KEY,
    descripcion VARCHAR(20) NOT NULL
);

-- Tabla 6: Tipos de Identificación
CREATE TABLE sri_tipo_identificacion (
    codigo CHAR(2) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

-- Tabla 16: Códigos de Impuestos (Padres)
CREATE TABLE sri_impuesto_tipo (
    codigo CHAR(1) PRIMARY KEY,
    descripcion VARCHAR(50) NOT NULL
);

-- Tabla 17: Tarifas de IVA (Porcentajes)
CREATE TABLE sri_tarifa_iva (
    codigo CHAR(1) PRIMARY KEY, -- Ojo: algunos códigos pueden ser de 1 dígito o más según evolución
    descripcion VARCHAR(50) NOT NULL,
    porcentaje DECIMAL(5,2) NOT NULL -- Ej: 15.00
);

-- Tabla 18: Tarifas de ICE (Simplificada para estructura)
CREATE TABLE sri_tarifa_ice (
    codigo CHAR(4) PRIMARY KEY,
    descripcion VARCHAR(255) NOT NULL,
    tarifa_ad_valorem DECIMAL(5,2),
    tarifa_especifica DECIMAL(10,4)
);

-- Tabla 19: Códigos de Retención (Impuesto a retener)
CREATE TABLE sri_impuesto_retener (
    codigo CHAR(1) PRIMARY KEY,
    descripcion VARCHAR(50) NOT NULL
);

-- Tabla 20: Porcentajes de Retención (IVA y Renta)
CREATE TABLE sri_porcentaje_retencion (
    codigo VARCHAR(5) PRIMARY KEY, -- Puede ser alfanumérico ej. 3481
    tipo_impuesto CHAR(1), -- FK a sri_impuesto_retener
    descripcion VARCHAR(255),
    porcentaje DECIMAL(5,2),
    FOREIGN KEY (tipo_impuesto) REFERENCES sri_impuesto_retener(codigo)
);

-- Tabla 24: Formas de Pago
CREATE TABLE sri_forma_pago (
    codigo CHAR(2) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

-- Tabla 25: Países (Para exportación y paraísos fiscales)
CREATE TABLE sri_pais (
    codigo CHAR(3) PRIMARY KEY,
    descripcion VARCHAR(100) NOT NULL
);

-- ==================================================================
-- 2. TABLAS TRANSACCIONALES (EMISIÓN DE COMPROBANTES)
-- ==================================================================

-- Cabecera del Comprobante (Datos comunes de InfoTributaria)
CREATE TABLE fe_comprobante (
    id_comprobante INT AUTO_INCREMENT PRIMARY KEY,
    ambiente CHAR(1) NOT NULL,
    tipo_emision CHAR(1) NOT NULL DEFAULT '1',
    razon_social_emisor VARCHAR(300) NOT NULL,
    nombre_comercial VARCHAR(300),
    ruc_emisor CHAR(13) NOT NULL,
    clave_acceso CHAR(49) NOT NULL UNIQUE,
    cod_doc CHAR(2) NOT NULL,
    estab CHAR(3) NOT NULL,
    pto_emi CHAR(3) NOT NULL,
    secuencial CHAR(9) NOT NULL,
    dir_matriz VARCHAR(300) NOT NULL,
    fecha_emision DATE NOT NULL, -- Dato de InfoFactura/InfoNotaCredito etc.
    estado_sri VARCHAR(20) DEFAULT 'CREADO', -- CREADO, FIRMADO, ENVIADO, AUTORIZADO, RECHAZADO
    xml_generado LONGTEXT,
    mensaje_error TEXT,
    
    FOREIGN KEY (ambiente) REFERENCES sri_ambiente(codigo),
    FOREIGN KEY (tipo_emision) REFERENCES sri_tipo_emision(codigo),
    FOREIGN KEY (cod_doc) REFERENCES sri_tipo_comprobante(codigo)
);

-- Información específica de Factura (InfoFactura)
CREATE TABLE fe_factura_info (
    id_comprobante INT PRIMARY KEY,
    dir_establecimiento VARCHAR(300),
    contribuyente_especial VARCHAR(13),
    obligado_contabilidad ENUM('SI','NO'),
    tipo_identificacion_comprador CHAR(2),
    guia_remision VARCHAR(20), -- Formato 001-001-000000001
    razon_social_comprador VARCHAR(300) NOT NULL,
    identificacion_comprador VARCHAR(20) NOT NULL,
    direccion_comprador VARCHAR(300),
    total_sin_impuestos DECIMAL(14,2) NOT NULL,
    total_descuento DECIMAL(14,2) NOT NULL,
    propina DECIMAL(14,2) DEFAULT 0.00,
    importe_total DECIMAL(14,2) NOT NULL,
    moneda VARCHAR(15) DEFAULT 'DOLAR',
    placa VARCHAR(20), -- Anexo 12 (Gasolineras)
    
    FOREIGN KEY (id_comprobante) REFERENCES fe_comprobante(id_comprobante),
    FOREIGN KEY (tipo_identificacion_comprador) REFERENCES sri_tipo_identificacion(codigo)
);

-- Detalle de Factura
CREATE TABLE fe_factura_detalle (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_comprobante INT NOT NULL,
    codigo_principal VARCHAR(25) NOT NULL,
    codigo_auxiliar VARCHAR(25), -- Anexo 25: H492001, etc.
    descripcion VARCHAR(300) NOT NULL,
    cantidad DECIMAL(18,6) NOT NULL, -- Versión 1.1.0 permite 6 decimales
    precio_unitario DECIMAL(18,6) NOT NULL,
    descuento DECIMAL(14,2) NOT NULL,
    precio_total_sin_impuesto DECIMAL(14,2) NOT NULL,
    
    FOREIGN KEY (id_comprobante) REFERENCES fe_comprobante(id_comprobante)
);

-- Impuestos por Detalle (Factura)
CREATE TABLE fe_detalle_impuesto (
    id_impuesto_det INT AUTO_INCREMENT PRIMARY KEY,
    id_detalle INT NOT NULL,
    codigo_impuesto CHAR(1) NOT NULL, -- 2 para IVA, 3 ICE
    codigo_porcentaje VARCHAR(4) NOT NULL, -- Ej: 0, 2, 3072
    tarifa DECIMAL(5,2) NOT NULL, -- Ej: 15.00
    base_imponible DECIMAL(14,2) NOT NULL,
    valor DECIMAL(14,2) NOT NULL,
    
    FOREIGN KEY (id_detalle) REFERENCES fe_factura_detalle(id_detalle),
    FOREIGN KEY (codigo_impuesto) REFERENCES sri_impuesto_tipo(codigo)
);

-- Formas de Pago Factura
CREATE TABLE fe_factura_pago (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    id_comprobante INT NOT NULL,
    forma_pago CHAR(2) NOT NULL,
    total DECIMAL(14,2) NOT NULL,
    plazo DECIMAL(14,2),
    unidad_tiempo VARCHAR(10),
    
    FOREIGN KEY (id_comprobante) REFERENCES fe_comprobante(id_comprobante),
    FOREIGN KEY (forma_pago) REFERENCES sri_forma_pago(codigo)
);

-- Campos Adicionales (InfoAdicional)
CREATE TABLE fe_info_adicional (
    id_info_adicional INT AUTO_INCREMENT PRIMARY KEY,
    id_comprobante INT NOT NULL,
    nombre VARCHAR(300) NOT NULL,
    valor VARCHAR(300) NOT NULL,
    
    FOREIGN KEY (id_comprobante) REFERENCES fe_comprobante(id_comprobante)
);

-- ==================================================================
-- 3. DML: POBLADO DE DATOS (CATÁLOGOS OFICIALES)
-- ==================================================================

-- Insertar Tipo de Emisión (Tabla 2)
INSERT INTO sri_tipo_emision (codigo, descripcion) VALUES 
('1', 'Emisión Normal');

-- Insertar Tipos de Comprobantes (Tabla 3)
INSERT INTO sri_tipo_comprobante (codigo, descripcion, tag_xml) VALUES 
('01', 'FACTURA', 'factura'),
('03', 'LIQ. COMPRA BIENES Y PRESTACION SERVICIOS', 'liquidacionCompra'),
('04', 'NOTA DE CRÉDITO', 'notaCredito'),
('05', 'NOTA DE DÉBITO', 'notaDebito'),
('06', 'GUÍA DE REMISIÓN', 'guiaRemision'),
('07', 'COMPROBANTE DE RETENCIÓN', 'comprobanteRetencion');

-- Insertar Ambientes (Tabla 4)
INSERT INTO sri_ambiente (codigo, descripcion) VALUES 
('1', 'PRUEBAS'),
('2', 'PRODUCCIÓN');

-- Insertar Tipos de Identificación (Tabla 6)
INSERT INTO sri_tipo_identificacion (codigo, descripcion) VALUES 
('04', 'RUC'),
('05', 'CÉDULA'),
('06', 'PASAPORTE'),
('07', 'VENTA A CONSUMIDOR FINAL'),
('08', 'IDENTIFICACIÓN DEL EXTERIOR');

-- Insertar Códigos de Impuestos (Tabla 16)
INSERT INTO sri_impuesto_tipo (codigo, descripcion) VALUES 
('2', 'IVA'),
('3', 'ICE'),
('5', 'IRBPNR');

-- Insertar Tarifas de IVA (Tabla 17 - Actualizado a Oct 2025)
-- Nota: La tabla PDF muestra códigos históricos y vigentes. Se incluyen los más relevantes.
INSERT INTO sri_tarifa_iva (codigo, descripcion, porcentaje) VALUES 
('0', '0%', 0.00),
('2', '12%', 12.00),
('3', '14%', 14.00),
('4', '15%', 15.00), -- Nueva tarifa IVA vigente 2024-2025
('5', '5%', 5.00),   -- Tarifa materiales construcción
('6', 'No Objeto de Impuesto', 0.00),
('7', 'Exento de IVA', 0.00),
('8', 'IVA diferenciado (Turismo)', 8.00),
('10', '13%', 13.00);

-- Insertar Impuestos a Retener (Tabla 19)
INSERT INTO sri_impuesto_retener (codigo, descripcion) VALUES 
('1', 'RENTA'),
('2', 'IVA'),
('6', 'ISD');

-- Insertar Códigos de Retención IVA (Tabla 20 Parcial)
INSERT INTO sri_porcentaje_retencion (codigo, tipo_impuesto, descripcion, porcentaje) VALUES 
('9', '2', 'Retención IVA 10%', 10.00),
('10', '2', 'Retención IVA 20%', 20.00),
('1', '2', 'Retención IVA 30%', 30.00),
('11', '2', 'Retención IVA 50%', 50.00),
('2', '2', 'Retención IVA 70%', 70.00),
('3', '2', 'Retención IVA 100%', 100.00),
('7', '2', 'Retención IVA 0%', 0.00),
('8', '2', 'No procede retención', 0.00);

-- Insertar Formas de Pago (Tabla 24)
INSERT INTO sri_forma_pago (codigo, descripcion) VALUES 
('01', 'SIN UTILIZACION DEL SISTEMA FINANCIERO'),
('15', 'COMPENSACIÓN DE DEUDAS'),
('16', 'TARJETA DE DÉBITO'),
('17', 'DINERO ELECTRÓNICO'),
('18', 'TARJETA PREPAGO'),
('19', 'TARJETA DE CRÉDITO'),
('20', 'OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO'),
('21', 'ENDOSO DE TÍTULOS');

-- Insertar Países (Tabla 25 - Muestra)
INSERT INTO sri_pais (codigo, descripcion) VALUES 
('593', 'ECUADOR'),
('110', 'ESTADOS UNIDOS'),
('105', 'COLOMBIA'),
('120', 'PERÚ'),
('209', 'ESPAÑA');

-- Fin del Script