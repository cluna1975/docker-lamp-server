# 🐳 Comandos Docker LAMP - Facturación SRI Ecuador

## 🚀 Inicio y Parada

```bash
# Levantar todos los servicios
docker-compose up -d

# Levantar y rebuild
docker-compose up -d --build

# Detener servicios
docker-compose down

# Detener y eliminar volúmenes
docker-compose down -v

# Reiniciar servicios
docker-compose restart

# Reiniciar solo web
docker-compose restart webserver
```

---

## 📊 Monitoreo

```bash
# Ver estado de contenedores
docker-compose ps

# Ver logs en tiempo real
docker-compose logs -f

# Ver logs de un servicio específico
docker-compose logs -f webserver
docker-compose logs -f db

# Ver últimas 50 líneas
docker-compose logs --tail=50 webserver

# Stats de recursos
docker stats
```

---

## 🔍 Acceso a Contenedores

```bash
# Bash en webserver
docker exec -it mi_servidor_web bash

# Bash en MySQL
docker exec -it MySQL bash

# MySQL CLI
docker exec -it MySQL mysql -u root -proot mi_proyecto

# Ver archivos en webserver
docker exec mi_servidor_web ls -la /var/www/html/php
```

---

## ⚙️ Apache

```bash
# Reload Apache  
docker exec mi_servidor_web service apache2 reload

# Restart Apache
docker exec mi_servidor_web service apache2 restart

# Ver estado
docker exec mi_servidor_web service apache2 status

# Ver error log
docker exec mi_servidor_web tail -f /var/log/apache2/error.log

# Ver access log
docker exec mi_servidor_web tail -f /var/log/apache2/access.log

# Test configuración
docker exec mi_servidor_web apachectl configtest
```

---

## 🐘 PHP

```bash
# Ver versión PHP
docker exec mi_servidor_web php -v

# Ver módulos instalados
docker exec mi_servidor_web php -m

# Ver configuración
docker exec mi_servidor_web php -i

# Verificar extensión específica
docker exec mi_servidor_web php -m | grep openssl
docker exec mi_servidor_web php -m | grep dom
docker exec mi_servidor_web php -m | grep xml
```

---

## 🗄️ MySQL

```bash
# Conectar a MySQL
docker exec -it MySQL mysql -u root -proot

# Ver bases de datos
docker exec MySQL mysql -u root -proot -e "SHOW DATABASES;"

# Ver tablas
docker exec MySQL mysql -u root -proot mi_proyecto -e "SHOW TABLES;"

# Backup database
docker exec MySQL mysqldump -u root -proot mi_proyecto > backup_$(date +%Y%m%d).sql

# Restore database
docker exec -i MySQL mysql -u root -proot mi_proyecto < backup.sql

# Ver usuarios
docker exec MySQL mysql -u root -proot -e "SELECT user, host FROM mysql.user;"
```

---

## 📁 Gestión de Archivos

```bash
# Copiar archivo AL contenedor
docker cp archivo.txt mi_servidor_web:/var/www/html/php/

# Copiar archivo DEL contenedor
docker cp mi_servidor_web:/var/www/html/php/archivo.txt ./

# Copiar directorio completo
docker cp ./data mi_servidor_web:/var/www/html/php/

# Ver permisos
docker exec mi_servidor_web ls -la /var/www/html/php/data
```

---

## 🔧 Mantenimiento

```bash
# Limpiar contenedores detenidos
docker container prune

# Limpiar imágenes sin uso
docker image prune

# Limpiar todo (cuidado!)
docker system prune -a

# Ver espacio usado
docker system df

# Rebuild forzado
docker-compose build --no-cache
docker-compose up -d
```

---

## 🔒 Permisos

```bash
# Cambiar permisos data
docker exec mi_servidor_web chown -R www-data:www-data /var/www/html/php/data

# Cambiar permisos logs
docker exec mi_servidor_web chown -R www-data:www-data /var/www/html/php/logs

# Verificar owner
docker exec mi_servidor_web ls -la /var/www/html/php/

# Chmod directorios
docker exec mi_servidor_web chmod -R 755 /var/www/html/php/data
docker exec mi_servidor_web chmod -R 755 /var/www/html/php/logs
```

---

## 🐛 Debug

```bash
# Ver variables de entorno
docker exec mi_servidor_web env

# Ver procesos
docker exec mi_servidor_web ps aux

# Ver puertos
docker port mi_servidor_web

# Inspeccionar contenedor
docker inspect mi_servidor_web

# Ver red
docker network ls
docker network inspect docker-lamp-server_lamp-network

# Healthcheck status
docker inspect --format='{{json .State.Health}}' mi_servidor_web | jq
```

---

## 📦 Volúmenes

```bash
# Listar volúmenes
docker volume ls

# Inspeccionar volumen
docker volume inspect docker-lamp-server_db_data

# Backup volumen MySQL
docker run --rm -v docker-lamp-server_db_data:/data -v $(pwd):/backup ubuntu tar czf /backup/db_backup.tar.gz /data

# Restore volumen
docker run --rm -v docker-lamp-server_db_data:/data -v $(pwd):/backup ubuntu tar xzf /backup/db_backup.tar.gz -C /
```

---

## 🔄 Actualización

```bash
# Actualizar código sin rebuild
git pull
docker-compose restart webserver

# Actualizar con rebuild
git pull
docker-compose up -d --build

# Actualizar solo base de datos
docker-compose pull db
docker-compose up -d db
```

---

## 🧪 Testing

```bash
# Test conexión web
curl -I http://localhost:8080

# Test conexión MySQL
docker exec MySQL mysqladmin -u root -proot ping

# Test PHP
docker exec mi_servidor_web php -r "echo 'PHP OK';"

# Test OpenSSL
docker exec mi_servidor_web openssl version

# Test escritura
docker exec mi_servidor_web touch /var/www/html/php/temp/test.txt
docker exec mi_servidor_web ls -la /var/www/html/php/temp/
```

---

## 📊 Performance

```bash
# Ver uso de CPU/RAM en tiempo real
docker stats --no-stream

# Ver top procesos en contenedor
docker exec mi_servidor_web top -n 1

# Ver conexiones MySQL
docker exec MySQL mysql -u root -proot -e "SHOW PROCESSLIST;"

# Ver uso de disco
docker exec mi_servidor_web df -h
```

---

## 🚨 Emergencia

```bash
# Forzar parada
docker-compose kill

# Eliminar todo y empezar de cero
docker-compose down -v
docker-compose up -d --build

# Ver últimos errores
docker-compose logs --tail=100 webserver | grep -i error

# Restart hard
docker restart mi_servidor_web MySQL
```

---

**💡 Tip**: Crear aliases en `~/.bashrc` o `~/.zshrc`:

```bash
alias dcu='docker-compose up -d'
alias dcd='docker-compose down'
alias dcl='docker-compose logs -f'
alias dps='docker-compose ps'
alias dweb='docker exec -it mi_servidor_web bash'
alias ddb='docker exec -it MySQL mysql -u root -proot'
```
