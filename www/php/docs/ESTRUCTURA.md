# Sistema de Facturación Electrónica - SRI Ecuador
## Estructura del Proyecto

```
php/
├── index.php                           # Página principal del sistema
├── config.php                          # Configuración central
├── config.example.php                  # Ejemplo de configuración
├── .htaccess                          # Configuración Apache
│
├── public/                            # Archivos públicos accesibles
│   ├── generar_factura.php           # Formulario generación de facturas
│   ├── firmar_xml.php                # Formulario firma de XML
│   ├── test_certificado.php          # Diagnóstico de certificado
│   └── guia.php                      # Guía de uso del sistema
│
├── src/                               # Código fuente (clases PHP)
│   ├── SRIXMLGenerator.php           # Generador de XML SRI
│   └── SRIXMLSigner.php              # Firmador XML XAdES-BES
│
├── assets/                            # Recursos estáticos
│   ├── css/                          # Hojas de estilo
│   ├── js/                           # JavaScript
│   └── images/                       # Imágenes
│
├── data/                              # Datos de la aplicación
│   ├── certificados/                 # Certificados .p12
│   │   └── mr.p12                   # Certificado de ejemplo
│   ├── xml_generados/                # XMLs generados (sin firmar)
│   │   └── *.xml
│   └── xml_firmados/                 # XMLs firmados con XAdES-BES
│       └── *.xml
│
├── docs/                              # Documentación
│   ├── README.md                     # Documentación principal
│   └── ESTRUCTURA.md                 # Este archivo
│
├── logs/                              # Logs del sistema
│   └── .gitkeep
│
└── temp/                              # Archivos temporales
    └── .gitkeep
```

## Descripción de Directorios

### `/public/`
Contiene todos los archivos accesibles directamente por los usuarios. 
Formularios y páginas web del sistema.

### `/src/`
Clases PHP principales del sistema. No accesibles directamente desde web.
- **SRIXMLGenerator.php**: Genera XML según especificaciones del SRI Ecuador
- **SRIXMLSigner.php**: Firma XML con estándar XAdES-BES usando OpenSSL

### `/assets/`
Recursos estáticos (CSS, JS, imágenes). Actualmente vacío, listo para expansión.

### `/data/`
Almacenamiento de datos de la aplicación:
- **certificados/**: Certificados digitales .p12
- **xml_generados/**: XMLs generados sin firma
- **xml_firmados/**: XMLs con firma digital XAdES-BES

### `/docs/`
Documentación del proyecto:
- README.md: Guía completa de instalación y uso
- ESTRUCTURA.md: Estructura del proyecto

### `/logs/`
Registro de actividades del sistema (futuro uso para auditoría).

### `/temp/`
Archivos temporales generados durante la ejecución.

## Archivos Principales

### `config.php`
Configuración central del sistema:
- Rutas de directorios
- Datos de la empresa
- Configuración del certificado
- Constantes del SRI
- Funciones auxiliares

### `index.php`
Página de inicio con:
- Menú principal de navegación
- Descripción del sistema
- Enlaces a funcionalidades
- Diseño moderno con animaciones

## Características de Seguridad

1. **Protección de directorios**: `.htaccess` protege `/src`, `/data`, `/logs`, `/temp`
2. **Archivos de configuración**: No accesibles directamente
3. **Validación de datos**: Todas las entradas validadas
4. **Logs de actividad**: Preparado para auditoría

## Rutas de Acceso

### URLs Públicas
- `http://localhost:8080/php/` - Página principal
- `http://localhost:8080/php/public/generar_factura.php` - Generar facturas
- `http://localhost:8080/php/public/firmar_xml.php` - Firmar XML
- `http://localhost:8080/php/public/test_certificado.php` - Test certificado
- `http://localhost:8080/php/public/guia.php` - Guía de uso

### Directorios Protegidos
- `/src/` - Solo accesible vía include/require
- `/data/` - Solo accesible por PHP
- `/config.php` - Solo accesible vía include/require

## Mantenimiento

### Backup Recomendado
- `/data/certificados/` - Certificados digitales
- `/data/xml_firmados/` - XMLs firmados importantes
- `/config.php` - Configuración personalizada

### Logs
Los archivos de log futuros se almacenarán en `/logs/` con formato:
- `sistema_YYYY-MM-DD.log`
- `errores_YYYY-MM-DD.log`

## Expansión Futura

El sistema está preparado para:
- [ ] Sistema de usuarios y autenticación
- [ ] API REST para integración
- [ ] Panel de administración
- [ ] Reportes y estadísticas
- [ ] Sistema de logs completo
- [ ] Cache de XMLs
- [ ] Cola de procesos

---

**Versión:** 1.0.0  
**Última actualización:** Diciembre 2024  
**Autor:** Sistema de Facturación Electrónica SRI Ecuador
