# 🐳 Docker LAMP Server - Sistema de Facturación Electrónica SRI Ecuador

Stack LAMP completo en Docker para el sistema de facturación electrónica del SRI Ecuador.

## 📋 Contenido

- **Web Server**: Apache + PHP 8.2 con extensiones openssldom, xml
- **Database**: MySQL 8.0
- **Sistema**: Facturación electrónica con firma digital XAdES-BES

---

## 🚀 Inicio Rápido

### Prerrequisitos

- Docker Desktop instalado
- Docker Compose

### Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone <url-repo>
   cd docker-lamp-server
   ```

2. **Construir y levantar los contenedores:**
   ```bash
   docker-compose up -d --build
   ```

3. **Acceder a la aplicación:**
   - **Web**: http://localhost:8080
   - **Facturación**: http://localhost:8080/php/

---

## 📁 Estructura del Proyecto

```
docker-lamp-server/
├── Dockerfile              # Configuración del contenedor web
├── docker-compose.yml      # Orquestación de servicios
├── .env                    # Variables de entorno
├── .dockerignore          # Archivos excluidos del build
├── 000-default.conf       # Configuración Apache
└── www/
    └── php/               # Aplicación de facturación
        ├── index.php      # Página principal
        ├── config.php     # Configuración
        ├── src/           # Clases PHP
        ├── public/        # Archivos públicos
        ├── data/          # Datos (XMLs, certificados)
        ├── docs/          # Documentación
        ├── logs/          # Logs del sistema
        └── assets/        # Recursos estáticos
```

---

## ⚙️ Configuración

### Variables de Entorno (.env)

```env
MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=mi_proyecto
MYSQL_USER=root
MYSQL_PASSWORD=root
```

### Extensiones PHP Instaladas

- ✅ `mysqli` - Conexión MySQL
- ✅ `pdo` / `pdo_mysql` - PDO MySQL
- ✅ `dom` - Manipulación XML/DOM
- ✅ `xml` - Parser XML
- ✅ `openssl` - Criptografía y firmas digitales

---

## 🔧 Comandos Útiles

### Gestión de Contenedores

```bash
# Levantar servicios
docker-compose up -d

# Detener servicios
docker-compose down

# Ver logs
docker-compose logs -f

# Reconstruir contenedores
docker-compose up -d --build

# Reiniciar servicios
docker-compose restart
```

### Acceso a Contenedores

```bash
# Acceder al contenedor web
docker exec -it mi_servidor_web bash

# Acceder a MySQL
docker exec -it MySQL mysql -u root -proot

# Ver logs de Apache
docker exec mi_servidor_web tail -f /var/log/apache2/error.log
```

### Gestión de Apache

```bash
# Recargar Apache
docker exec mi_servidor_web service apache2 reload

# Reiniciar Apache
docker exec mi_servidor_web service apache2 restart

# Ver estado
docker exec mi_servidor_web service apache2 status
```

---

## 📦 Servicios

### Web Server (mi_servidor_web)

- **Puerto**: 8080
- **Base**: PHP 8.2-Apache
- **Volumen**: `./www` → `/var/www/html`
- **Healthcheck**: Cada 30s
- **Restart**: unless-stopped

### MySQL (MySQL)

- **Puerto**: 3306
- **Versión**: 8.0
- **Volumen**: `db_data` (persistente)
- **Healthcheck**: Cada 10s
- **Restart**: unless-stopped

---

## 🔒 Seguridad

### Directorios Protegidos

El `.htaccess` protege:
- `/src` - Clases PHP
- `/data` - XMLs y certificados
- `/logs` - Logs del sistema
- `/temp` - Archivos temporales

### Permisos

```bash
# Directorios con permisos 755
- /data/certificados
- /data/xml_generados
- /data/xml_firmados
- /logs
- /temp

# Owner: www-data:www-data
```

---

## 🐛 Troubleshooting

### El servidor web no inicia

```bash
# Ver logs
docker-compose logs webserver

# Verificar permisos
docker exec mi_servidor_web ls -la /var/www/html
```

### Error de conexión a MySQL

```bash
# Verificar que MySQL esté running
docker-compose ps

# Ver logs de MySQL
docker-compose logs db

# Esperar healthcheck
docker-compose ps | grep healthy
```

### Problemas con certificados .p12

```bash
# Verificar OpenSSL
docker exec mi_servidor_web openssl version

# Verificar extensiones PHP
docker exec mi_servidor_web php -m | grep openssl
```

---

## 🔄 Backup y Restore

### Backup de Base de Datos

```bash
docker exec MySQL mysqldump -u root -proot mi_proyecto > backup.sql
```

### Restore de Base de Datos

```bash
docker exec -i MySQL mysql -u root -proot mi_proyecto < backup.sql
```

### Backup de Datos

```bash
# Copiar datos del contenedor
docker cp mi_servidor_web:/var/www/html/php/data ./backup_data
```

---

## 📊 Monitoreo

### Ver Recursos

```bash
# Stats en tiempo real
docker stats

# Inspeccionar contenedor
docker inspect mi_servidor_web
```

### Healthchecks

```bash
# Ver estado de salud
docker-compose ps

# Inspeccionar healthcheck
docker inspect --format='{{json .State.Health}}' mi_servidor_web
```

---

## 🚀 Producción

### Recomendaciones

1. **Cambiar credenciales** en `.env`
2. **Usar HTTPS** (SSL/TLS)
3. **Limitar recursos** en docker-compose
4. **Backups automáticos**
5. **Logging centralizado**
6. **Monitoring** (Prometheus/Grafana)

### Ejemplo límites de recursos

```yaml
webserver:
  deploy:
    resources:
      limits:
        cpus: '2'
        memory: 2G
      reservations:
        cpus: '1'
        memory: 1G
```

---

## 📝 Notas

- **PHP Version**: 8.2
- **MySQL Version**: 8.0
- **Apache Modules**: rewrite
- **Certificados**: Colocar .p12 en `/data/certificados/`

---

## 🤝 Contribuir

1. Fork el proyecto
2. Crear feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit cambios (`git commit -m 'Add AmazingFeature'`)
4. Push al branch (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

---

## 📄 Licencia

Este proyecto es para uso interno/educativo.

---

## ✨ Autor

Sistema de Facturación Electrónica - SRI Ecuador

---

**¿Problemas?** Revisa la [documentación](./docs/) o abre un issue.
