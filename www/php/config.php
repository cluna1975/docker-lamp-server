<?php
/**
 * Configuración para Facturación Electrónica SRI Ecuador
 */

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Configuración de zona horaria
date_default_timezone_set('America/Guayaquil');

// Rutas de archivos (nueva estructura organizada)
define('BASE_PATH', __DIR__);
define('SRC_PATH', BASE_PATH . '/src/');
define('PUBLIC_PATH', BASE_PATH . '/public/');
define('DATA_PATH', BASE_PATH . '/data/');
define('ASSETS_PATH', BASE_PATH . '/assets/');
define('DOCS_PATH', BASE_PATH . '/docs/');
define('LOGS_PATH', BASE_PATH . '/logs/');
define('TEMP_PATH', BASE_PATH . '/temp/');

define('CERT_PATH', DATA_PATH . 'certificados/');
define('XML_PATH', DATA_PATH . 'xml_generados/');
define('XML_FIRMADOS_PATH', DATA_PATH . 'xml_firmados/');

// Crear directorios si no existen
if (!file_exists(CERT_PATH)) {
    mkdir(CERT_PATH, 0755, true);
}
if (!file_exists(XML_PATH)) {
    mkdir(XML_PATH, 0755, true);
}
if (!file_exists(XML_FIRMADOS_PATH)) {
    mkdir(XML_FIRMADOS_PATH, 0755, true);
}

// Configuración de la empresa
define('EMPRESA_RUC', '1234567890001');
define('EMPRESA_RAZON_SOCIAL', 'MI EMPRESA S.A.');
define('EMPRESA_NOMBRE_COMERCIAL', 'MI EMPRESA');
define('EMPRESA_DIRECCION', 'Av. Principal 123');
define('EMPRESA_OBLIGADO_CONTABILIDAD', 'SI');

// Configuración del certificado
define('CERT_FILE', DATA_PATH . 'certificados/mr.p12'); // Ruta completa al archivo .p12
define('CERT_PASSWORD', 'ECUA2024'); // Contraseña del certificado (cambiar según corresponda)

// Tipos de comprobante SRI
define('TIPO_FACTURA', '01');
define('TIPO_NOTA_CREDITO', '04');
define('TIPO_NOTA_DEBITO', '05');
define('TIPO_GUIA_REMISION', '06');
define('TIPO_RETENCION', '07');

// Ambiente
define('AMBIENTE_PRUEBAS', '1');
define('AMBIENTE_PRODUCCION', '2');
define('AMBIENTE', AMBIENTE_PRUEBAS);

// Tipos de emisión
define('EMISION_NORMAL', '1');
define('EMISION_CONTINGENCIA', '2');

// Versión del XML
define('VERSION_XML', '1.0.0');
define('VERSION_XML_110', '1.1.0');

/**
 * Función para generar clave de acceso
 */
function generarClaveAcceso($fecha, $tipoComprobante, $ruc, $ambiente, $serie, $numero, $codigoNumerico, $tipoEmision) {
    $claveAcceso = $fecha . $tipoComprobante . $ruc . $ambiente . $serie . $numero . $codigoNumerico . $tipoEmision;
    
    // Calcular dígito verificador módulo 11
    $suma = 0;
    $factor = 7;
    
    for ($i = 0; $i < strlen($claveAcceso); $i++) {
        $suma += intval($claveAcceso[$i]) * $factor;
        $factor--;
        if ($factor == 1) {
            $factor = 7;
        }
    }
    
    $digitoVerificador = 11 - ($suma % 11);
    
    if ($digitoVerificador == 11) {
        $digitoVerificador = 0;
    } elseif ($digitoVerificador == 10) {
        $digitoVerificador = 1;
    }
    
    return $claveAcceso . $digitoVerificador;
}

/**
 * Función para formatear fecha en formato SRI
 */
function formatearFechaSRI($fecha = null) {
    if ($fecha === null) {
        $fecha = date('d/m/Y');
    }
    return $fecha;
}

/**
 * Función para obtener fecha en formato ddmmaaaa
 */
function obtenerFechaFormato($fecha = null) {
    if ($fecha === null) {
        return date('dmY');
    }
    $timestamp = strtotime(str_replace('/', '-', $fecha));
    return date('dmY', $timestamp);
}

/**
 * Generar código numérico aleatorio de 8 dígitos
 */
function generarCodigoNumerico() {
    return str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
}

/**
 * Función para limpiar caracteres especiales XML
 */
function limpiarXML($texto) {
    $texto = str_replace('&', '&amp;', $texto);
    $texto = str_replace('<', '&lt;', $texto);
    $texto = str_replace('>', '&gt;', $texto);
    $texto = str_replace('"', '&quot;', $texto);
    $texto = str_replace("'", '&apos;', $texto);
    return $texto;
}

/**
 * Función para sanitizar mensajes de error (seguridad)
 * Elimina rutas completas del servidor que no deben exponerse al usuario
 */
function sanitizarMensajeError($mensaje) {
    // Reemplazar rutas absolutas con [ruta]
    $mensaje = preg_replace('/\/[^ ]*\//', '[ruta]/', $mensaje);
    // Reemplazar referencias a directorios internos
    $mensaje = str_replace(BASE_PATH, '[sistema]', $mensaje);
    $mensaje = str_replace(__DIR__, '[sistema]', $mensaje);
    $mensaje = str_replace(DATA_PATH, '[datos]/', $mensaje);
    $mensaje = str_replace(CERT_PATH, '[certificados]/', $mensaje);
    $mensaje = str_replace(XML_PATH, '[xml]/', $mensaje);
    $mensaje = str_replace(XML_FIRMADOS_PATH, '[firmados]/', $mensaje);
    // Reemplazar rutas de sistema operativo
    $mensaje = str_replace('/var/www/html', '[servidor]', $mensaje);
    $mensaje = str_replace('/usr/', '[sistema]/', $mensaje);
    $mensaje = str_replace('/home/', '[usuario]/', $mensaje);
    return $mensaje;
}

