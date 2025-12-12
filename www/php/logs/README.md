# Logs Directory

Este directorio contiene los archivos de log del sistema.

## Tipos de Logs

- `sistema_*.log` - Logs generales del sistema
- `errores_*.log` - Logs de errores
- `firmas_*.log` - Log de firmas digitales realizadas
- `generacion_*.log` - Log de XMLs generados

## Rotación de Logs

Los logs se rotan automáticamente cada día.
Se mantienen logs de los últimos 30 días.

## Formato

```
[YYYY-MM-DD HH:MM:SS] [NIVEL] Mensaje
```

Niveles: INFO, WARNING, ERROR, CRITICAL
