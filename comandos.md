cd ~/proyecto-lamp
docker compose up -d

Luego, entra a tu navegador:

Web: http://localhost:8080

Base de Datos: http://localhost:8081


docker compose stop
docker compose start


docker compose down



-- comandos git

--validar repositorio remoto
git remote -v
git branch && git status
git add .

git commit -m "Agregando DDL y actualizando comandos"
git pull origin main
git push origin main


git config pull.rebase false
git pull origin main

git stash

git stash pop

git remote add origin https://github.com/cluna1975/docker-lamp-server.git



