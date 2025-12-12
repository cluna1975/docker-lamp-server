# Sistema de Facturación Electrónica - SRI Ecuador 🇪🇨

Sistema completo de generación y firma de documentos electrónicos XML con estándar **XAdES-BES** para el Servicio de Rentas Internas del Ecuador.

## 🌟 Características

- ✅ **Generación de XML** según especificaciones del SRI Ecuador
- 🔐 **Firma Digital XAdES-BES** con certificados .p12
- 📄 **Facturas Electrónicas** completas con validación
- 🎨 **Interfaz Moderna** con animaciones Lottie
- 📱 **Diseño Responsivo** optimizado para todos los dispositivos
- ⚡ **Validación en tiempo real** de datos
- 🔒 **Seguridad** con cifrado SSL

## 📋 Requisitos

- PHP 7.4 o superior
- Extensión OpenSSL habilitada
- Extensión DOM habilitada
- Servidor web (Apache/Nginx)
- Certificado digital .p12 válido

## 🚀 Instalación

1. **Clonar o descargar** los archivos en tu directorio web:
   ```bash
   /www/php/
   ```

2. **Configurar el certificado:**
   - Colocar tu archivo `.p12` en el directorio raíz
   - Editar `config.php` y actualizar:
     ```php
     define('CERT_FILE', 'tu_certificado.p12');
     define('CERT_PASSWORD', 'tu_contraseña');
     ```

3. **Configurar datos de empresa:**
   Editar en `config.php`:
   ```php
   define('EMPRESA_RUC', '1234567890001');
   define('EMPRESA_RAZON_SOCIAL', 'TU EMPRESA S.A.');
   define('EMPRESA_DIRECCION', 'Tu Dirección');
   ```

4. **Verificar permisos:**
   Los directorios `xml_generados/`, `xml_firmados/` y `certificados/` 
   deben tener permisos de escritura (755 o 777).

## 📂 Estructura de Archivos

```
php/
├── index.php                    # Página principal del sistema
├── config.php                   # Configuración general
├── sri_xml_generator.php        # Clase generadora de XML
├── sri_xml_signer.php          # Clase firmadora de XML (XAdES-BES)
├── generar_factura.php         # Formulario de generación de facturas
├── firmar_xml.php              # Formulario de firma de XML
├── test.php                    # Página de prueba
├── mr.p12                      # Certificado digital (ejemplo)
├── certificados/               # Directorio para certificados
├── xml_generados/              # XMLs generados (sin firmar)
└── xml_firmados/               # XMLs firmados
```

## 💻 Uso del Sistema

### 1. Generar Factura Electrónica

1. Acceder a `generar_factura.php`
2. Completar los datos del cliente:
   - RUC o Cédula
   - Razón Social
   - Dirección
3. Agregar detalles de productos/servicios:
   - Código
   - Descripción
   - Cantidad
   - Precio unitario
   - IVA (0% o 12%)
4. Click en "Generar Factura XML"
5. El sistema genera:
   - Clave de acceso automática
   - XML con estructura SRI
   - Archivo guardado en `xml_generados/`

### 2. Firmar XML con XAdES-BES

1. Acceder a `firmar_xml.php`
2. Seleccionar método:
   - **Seleccionar XML:** Elegir de archivos generados
   - **Subir XML:** Cargar archivo desde tu computadora
3. Ingresar contraseña del certificado (si aplica)
4. Click en "Firmar XML con XAdES-BES"
5. El sistema:
   - Carga el certificado .p12
   - Firma con estándar XAdES-BES
   - Guarda en `xml_firmados/`
   - Permite descargar el archivo firmado

## 🔐 Estándar XAdES-BES

El sistema implementa **XML Advanced Electronic Signatures - Basic Electronic Signature (XAdES-BES)**, que incluye:

- ✅ Firma XML estándar (XMLDSig)
- ✅ SignedProperties con información del certificado
- ✅ SigningTime (marca de tiempo)
- ✅ SigningCertificate (digest del certificado)
- ✅ IssuerSerial (información del emisor)
- ✅ Canonicalização C14N
- ✅ Algoritmo SHA-1 para digest
- ✅ Algoritmo RSA-SHA1 para firma

## 🎨 Características de Diseño

### Paleta de Colores
- **Primario:** Gradiente púrpura (#667eea → #764ba2)
- **Secundario:** Gradiente rosa (#f093fb → #f5576c)
- **Éxito:** Gradiente verde (#11998e → #38ef7d)

### Tipografía
- **Títulos:** Outfit (Google Fonts)
- **Texto:** Inter (Google Fonts)

### Animaciones
- ✨ Lottie animations para elementos visuales
- 🌊 Fondo animado con gradientes
- 💫 Efectos hover en tarjetas y botones
- 🎭 Transiciones suaves cubic-bezier

## 📱 Responsive Design

El sistema está optimizado para:
- 💻 Desktop (1920px+)
- 💻 Laptop (1366px - 1920px)
- 📱 Tablet (768px - 1366px)
- 📱 Mobile (320px - 768px)

## ⚙️ Configuración Avanzada

### Ambiente de Trabajo

En `config.php` puedes cambiar el ambiente:

```php
// Pruebas
define('AMBIENTE', AMBIENTE_PRUEBAS); // '1'

// Producción
define('AMBIENTE', AMBIENTE_PRODUCCION); // '2'
```

### Tipos de Comprobante

```php
TIPO_FACTURA         = '01'
TIPO_NOTA_CREDITO    = '04'
TIPO_NOTA_DEBITO     = '05'
TIPO_GUIA_REMISION   = '06'
TIPO_RETENCION       = '07'
```

## 🔧 Funciones Principales

### Generación de Clave de Acceso
```php
generarClaveAcceso($fecha, $tipoComprobante, $ruc, 
                   $ambiente, $serie, $numero, 
                   $codigoNumerico, $tipoEmision)
```

### Generación de XML
```php
$generator = new SRIXMLGenerator();
$generator->setInfoTributaria($datos);
$generator->setInfoFactura($datos);
$generator->addDetalle($detalle);
$nombreArchivo = $generator->guardarXML();
```

### Firma de XML
```php
$signer = new SRIXMLSigner($certificadoPath, $password);
$archivoFirmado = $signer->firmarXML($rutaXML);
$verificacion = $signer->verificarFirma($rutaXML);
```

## 🐛 Solución de Problemas

### Error: "No se puede leer el certificado"
- Verificar que el archivo .p12 existe
- Confirmar que la contraseña es correcta
- Verificar permisos de lectura del archivo

### Error: "No se puede crear directorio"
- Verificar permisos del directorio padre
- Asegurar que Apache/Nginx tiene permisos de escritura

### XML no válido
- Verificar que todos los campos obligatorios estén completos
- Revisar que el RUC tenga 13 dígitos
- Confirmar que los valores numéricos sean válidos

### Firma no válida
- Verificar que el certificado esté vigente
- Confirmar que el XML no ha sido modificado
- Revisar que la extensión OpenSSL esté habilitada

## 📚 Referencias

- [SRI Ecuador - Facturación Electrónica](https://www.sri.gob.ec/facturacion-electronica)
- [Especificación XAdES](http://uri.etsi.org/01903/v1.3.2/)
- [W3C XML Signature](https://www.w3.org/TR/xmldsig-core/)

## 👨‍💻 Desarrollo

### Tecnologías Utilizadas
- **Backend:** PHP 7.4+
- **Frontend:** HTML5, CSS3, JavaScript
- **Librerías:** 
  - OpenSSL (firma digital)
  - DOM (manipulación XML)
  - Lottie (animaciones)

### Extensiones PHP Requeridas
```
- openssl
- dom
- mbstring
- xml
```

## 📄 Licencia

Este sistema es de código abierto y puede ser utilizado libremente.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:
1. Fork el proyecto
2. Crea una rama para tu feature
3. Commit tus cambios
4. Push a la rama
5. Abre un Pull Request

## 📞 Soporte

Para soporte o consultas:
- 📧 Email: soporte@ejemplo.com
- 🌐 Web: www.ejemplo.com
- 📱 Teléfono: +593 XX XXX XXXX

---

**Desarrollado con ❤️ para Ecuador** 🇪🇨

Sistema compatible con las normativas del SRI Ecuador vigentes a 2024.
