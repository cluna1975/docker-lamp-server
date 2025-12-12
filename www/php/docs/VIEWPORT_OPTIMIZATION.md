# Optimización de Pantallas para Viewport Sin Scroll

## Estrategia General

Para evitar scroll en todas las pantallas, implementaremos:

1. **Estructura de Viewport**: `height: 100vh` + `overflow: hidden`
2. **Scroll Interno**: Solo en áreas de contenido específicas
3. **Diseño Compacto**: Reducción de paddings, margins y tamaños de fuente
4. **Diseño Flexible**: Uso de `flexbox` y `grid` para optimizar espacio
5. **Responsive Height**: Media queries para pantallas cortas (`max-height`)

## Cambios por Pantalla

### 1. index.php ✅
- Ya está optimizado con diseño de tarjetas
- Ajustar para usar 100vh sin scroll externo
- Implementar scrollable-content interno

### 2. general_factura.php
**Problemas actuales:**
- Formulario muy extenso
- Detalles dinámicos pueden crecer mucho
- Padding excesivo

**Solución:**
- Header compacto (60px-80px)
- Form grid más denso
- Detalles con scroll interno
- Reducir padding general
- Botones fixed al bottom

### 3. firmar_xml.php
**Problemas actuales:**
- Tabs + formularios
- Lista de archivos puede ser extensa
- Mucho espacio en blanco

**Solución:**
- Header compacto
- Lista con scroll interno
- Tabs más compactos
- Info-box condensado

### 4. guia.php
**Problemas actuales:**
- Contenido muy extenso
- Muchas secciones

**Solución:**
- Scroll interno obligatorio
- Navegación lateral sticky
- Contenido en columnas

## Implementación

### CSS Global (assets/css/global.css)
```css
body {
    height: 100vh;
    overflow: hidden;
}

.viewport-container {
    height: 100vh;
    display: flex;
    flex-direction: column;
}

.scrollable-content {
    flex: 1;
    overflow-y: auto;
}
```

### Estructura HTML
```html
<body>
    <div class="viewport-container">
        <div class="scrollable-content">
            <div class="container">
                <!-- Contenido aquí -->
            </div>
        </div>
    </div>
</body>
```

## Medidas Específicas

- **Header**: 60px - 80px
- **Padding body**: 12px - 16px
- **Card padding**: 16px - 20px
- **Input padding**: 10px - 12px
- **Button padding**: 10px - 12px
- **Margin bottom**: 12px - 16px max
- **Font sizes**:
  - H1: 24px - 32px (clamp)
  - H2: 18px - 22px
  - Body: 13px -14px
  - Small: 12px

## Media Queries Críticos

```css
/* Pantallas cortas */
@media (max-height: 700px) {
    /* Reducir espacios */
}

@media (max-height: 600px) {
    /* Modo ultra compacto */
}

/* Móvil */
@media (max-width: 768px) {
    /* Diseño vertical */
}
```

## Prioridades

1. ✅ CSS Global creado
2. ⏳ Optimizar generar_factura.php (más crítico)
3. ⏳ Optimizar firmar_xml.php
4. ⏳ Optimizar index.php
5. ⏳ Optimizar guia.php

## Testing

Probar en:
- 1920x1080 (Full HD)
- 1366x768 (Laptop común)
- 1280x720 (HD)
- 1024x600 (Netbook)
- Mobile (375x667, 414x896)
