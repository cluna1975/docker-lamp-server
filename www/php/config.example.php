<?php
/**
 * ARCHIVO DE CONFIGURACIÓN DE EJEMPLO
 * 
 * Copia este archivo a config.php y ajusta los valores según tu configuración
 */

// ========================================
// CONFIGURACIÓN DE ERRORES
// ========================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Configuración de zona horaria
date_default_timezone_set('America/Guayaquil');

// ========================================
// RUTAS DE ARCHIVOS
// ========================================
define('CERT_PATH', __DIR__ . '/certificados/');
define('XML_PATH', __DIR__ . '/xml_generados/');
define('XML_FIRMADOS_PATH', __DIR__ . '/xml_firmados/');

// ========================================
// CONFIGURACIÓN DE LA EMPRESA
// ========================================
// TODO: Actualizar con tus datos reales
define('EMPRESA_RUC', '1234567890001');              // 13 dígitos
define('EMPRESA_RAZON_SOCIAL', 'MI EMPRESA S.A.');
define('EMPRESA_NOMBRE_COMERCIAL', 'MI EMPRESA');
define('EMPRESA_DIRECCION', 'Av. Principal 123, Quito, Ecuador');
define('EMPRESA_OBLIGADO_CONTABILIDAD', 'SI');       // SI o NO

// ========================================
// CONFIGURACIÓN DEL CERTIFICADO DIGITAL
// ========================================
// TODO: Actualizar con tu certificado .p12
define('CERT_FILE', 'mr.p12');                       // Nombre del archivo .p12
define('CERT_PASSWORD', '');                         // Contraseña del certificado

// ========================================
// TIPOS DE COMPROBANTE SRI
// ========================================
define('TIPO_FACTURA', '01');
define('TIPO_NOTA_CREDITO', '04');
define('TIPO_NOTA_DEBITO', '05');
define('TIPO_GUIA_REMISION', '06');
define('TIPO_RETENCION', '07');

// ========================================
// AMBIENTE DE TRABAJO
// ========================================
define('AMBIENTE_PRUEBAS', '1');
define('AMBIENTE_PRODUCCION', '2');

// TODO: Cambiar a AMBIENTE_PRODUCCION cuando esté listo
define('AMBIENTE', AMBIENTE_PRUEBAS);

// ========================================
// TIPOS DE EMISIÓN
// ========================================
define('EMISION_NORMAL', '1');
define('EMISION_CONTINGENCIA', '2');

// ========================================
// VERSIÓN DEL XML
// ========================================
define('VERSION_XML', '1.0.0');
define('VERSION_XML_110', '1.1.0');

// ========================================
// FUNCIONES AUXILIARES
// ========================================

/**
 * Función para generar clave de acceso según especificaciones del SRI
 * 
 * @param string $fecha Fecha en formato ddmmaaaa
 * @param string $tipoComprobante Código del tipo de comprobante (01-07)
 * @param string $ruc RUC de 13 dígitos
 * @param string $ambiente 1=Pruebas, 2=Producción
 * @param string $serie Establecimiento + Punto de emisión (6 dígitos)
 * @param string $numero Secuencial (9 dígitos)
 * @param string $codigoNumerico Código numérico aleatorio (8 dígitos)
 * @param string $tipoEmision 1=Normal, 2=Contingencia
 * @return string Clave de acceso de 49 dígitos
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
 * Formatear fecha en formato SRI (dd/mm/yyyy)
 */
function formatearFechaSRI($fecha = null) {
    if ($fecha === null) {
        $fecha = date('d/m/Y');
    }
    return $fecha;
}

/**
 * Obtener fecha en formato ddmmaaaa
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
 * Limpiar caracteres especiales para XML
 */
function limpiarXML($texto) {
    $texto = str_replace('&', '&amp;', $texto);
    $texto = str_replace('<', '&lt;', $texto);
    $texto = str_replace('>', '&gt;', $texto);
    $texto = str_replace('"', '&quot;', $texto);
    $texto = str_replace("'", '&apos;', $texto);
    return $texto;
}

// ========================================
// CREAR DIRECTORIOS SI NO EXISTEN
// ========================================
if (!file_exists(CERT_PATH)) {
    mkdir(CERT_PATH, 0755, true);
}
if (!file_exists(XML_PATH)) {
    mkdir(XML_PATH, 0755, true);
}
if (!file_exists(XML_FIRMADOS_PATH)) {
    mkdir(XML_FIRMADOS_PATH, 0755, true);
}

// ========================================
// NOTAS DE CONFIGURACIÓN
// ========================================
/*
 * IMPORTANTE:
 * 
 * 1. CERTIFICADO DIGITAL:
 *    - Coloca tu archivo .p12 en el directorio raíz del proyecto
 *    - Actualiza CERT_FILE con el nombre de tu certificado
 *    - Actualiza CERT_PASSWORD con tu contraseña
 * 
 * 2. DATOS DE EMPRESA:
 *    - El RUC debe tener exactamente 13 dígitos
 *    - La razón social debe coincidir con el certificado
 * 
 * 3. AMBIENTE:
 *    - Usa AMBIENTE_PRUEBAS durante el desarrollo
 *    - Cambia a AMBIENTE_PRODUCCION solo cuando estés listo
 * 
 * 4. PERMISOS:
 *    - Los directorios xml_generados/, xml_firmados/ y certificados/
 *      deben tener permisos de escritura (755 o 777)
 * 
 * 5. EXTENSIONES PHP:
 *    - openssl (para firma digital)
 *    - dom (para manipulación XML)
 *    - mbstring (para manejo de caracteres UTF-8)
 * 
 * 6. SEGURIDAD:
 *    - NO subas este archivo a repositorios públicos
 *    - Protege tu certificado .p12
 *    - Usa HTTPS en producción
 */
