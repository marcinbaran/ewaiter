#!/bin/bash

network_name="ewaiter"
username=$(whoami)
default_path="$(pwd)"
docker_composer="1"

load_env() {
    tr -d '\r' < .env.example > env.unix
    mv env.unix .env
    source .env
}

main() {
    echo "# Loading env file..."
    load_env
    echo "# Env file loaded!"
    echo ""

    echo "2. Copying default vite config file..."
   if ! ls -la vite.config.js >/dev/null 2>&1; then
      echo "Vite config file does not exist!"
      echo "Copying default vite config file..."
      cp vite.config.js.example vite.config.js
   fi
   echo "Vite config file exists!"
   echo ""

   echo "3. Checking if docker is installed..."
   if ! docker --version >/dev/null 2>&1; then
      echo "Docker is not installed or you are not allowed to use it!"
      echo "Follow the documentation to install docker: https://docs.docker.com/get-docker/"
   fi
   echo "Docker is installed!"
   echo ""

   echo "4. Checking if docker compose is installed..."
   if [ -x "$(command -v docker-compose)" ]; then
      docker_composer="1"
      echo "Docker compose is installed (docker-compose)."
   elif [ -x "$(command -v docker compose)" ]; then
      docker_composer="2"
      echo "Docker compose is installed (docker compose)."
   else
      echo "Docker compose is not installed or you are not allowed to use it!"
      echo "Follow the documentation to install docker-compose: https://docs.docker.com/compose/install/"
      exit 1
   fi
   echo ""

   echo "5. Checking if docker containers exists..."
   if docker ps -a | grep -q "ewaiter-app" >/dev/null 2>&1; then
      echo "ewaiter-app container exists!"
      echo "Stopping and removing ewaiter-app container..."
      docker stop ewaiter-app
      docker rm ewaiter-app
   fi
    if docker ps -a | grep -q "ewaiter-dns" >/dev/null 2>&1; then
        echo "ewaiter-dns container exists!"
        echo "Stopping and removing ewaiter-dns container..."
        docker stop ewaiter-dns
        docker rm ewaiter-dns
    fi
    if docker ps -a | grep -q "ewaiter-db" >/dev/null 2>&1; then
        echo "ewaiter-db container exists!"
        echo "Stopping and removing ewaiter-db container..."
        docker stop ewaiter-db
        docker rm ewaiter-db
    fi
    if docker ps -a | grep -q "ewaiter-nginx" >/dev/null 2>&1; then
        echo "ewaiter-nginx container exists!"
        echo "Stopping and removing ewaiter-nginx container..."
        docker stop ewaiter-nginx
        docker rm ewaiter-nginx
    fi
    if docker ps -a | grep -q "ewaiter-node" >/dev/null 2>&1; then
        echo "ewaiter-node container exists!"
        echo "Stopping and removing ewaiter-node container..."
        docker stop ewaiter-node
        docker rm ewaiter-node
    fi
    if docker ps -a | grep -q "ewaiter-phpmyadmin" >/dev/null 2>&1; then
        echo "ewaiter-phpmyadmin container exists!"
        echo "Stopping and removing ewaiter-phpmyadmin container..."
        docker stop ewaiter-phpmyadmin
        docker rm ewaiter-phpmyadmin
    fi
   echo "Containers does not exist!"
   echo ""

   echo "6. Copying adguard init files"
   if ls $default_path/docker-compose/adguard/adguard >/dev/null 2>&1; then
        echo "Adguard init files exists!"
   else
        echo "Adguard init files does not exist!"
        echo "Copying adguard init files..."
        cp -r $default_path/docker-compose/adguard/init $default_path/docker-compose/adguard/adguard
   fi
   echo "Adguard init files copied!"
   echo ""

   echo "7. Creating docker containers"
    if [[ $docker_composer == "1" ]]; then
    sudo docker-compose up -d --build --force-recreate
    elif [[ $docker_composer == "2" ]]; then
    sudo docker compose up -d --build --force-recreate
    fi
    echo "Containers created!"
    echo ""

    echo "8. Installing PHP dependencies"
    docker exec ewaiter-app composer install
    echo "PHP dependencies installed!"
    echo ""

    echo "9. Clearing all Laravel cache at once"
    docker exec ewaiter-app php artisan optimize:clear
    echo "Laravel cache cleared!"
    echo ""

    echo "10. Generating encryption keys for API authentication"
    docker exec ewaiter-app php artisan passport:keys
    echo "Encryption keys created!"
    echo ""

    echo "11. Running migrations"
    docker exec ewaiter-app php artisan migrate --force
    docker exec ewaiter-app php artisan tenancy:migrate --force
    echo "Migrations ran!"
    echo ""

    echo "11. Running seeders"
    docker exec ewaiter-app php artisan db:seed --force
    docker exec ewaiter-app php artisan tenancy:db:seed --force
    echo "Seeders ran!"
    echo ""

    echo "11. Adding storage links"
    docker exec ewaiter-app php artisan storage:link
    echo "Storage links added!"
    echo ""

    echo "11. Installing PHP dependencies"
    docker exec ewaiter-app composer install
    echo "PHP dependencies installed!"
    echo ""

    echo "12. Installing NPM dependencies"
    docker exec ewaiter-node npm install
    echo "NPM dependencies installed!"
    echo ""

    echo "13. Configuring dns server"
    echo "Change your dns server to 127.0.0.1"
    echo "If you are using Windows, you can change your DNS server by following 'https://www.windowscentral.com/how-change-your-pcs-dns-settings-windows-10' tutorial"
    echo "If you are using Linux, you can change your DNS server by following 'https://www.linuxfordevices.com/tutorials/linux/change-dns-on-linux/' tutorial"
    echo "If you are using Mac, you can change your DNS server by following 'https://support.apple.com/pl-pl/guide/mac-help/mh14127/mac' tutorial"
    echo ""
    echo "[NOTE] If you change DNS server, ewaiter-dns container is needed to be running to access the internet!"
    echo ""
    echo "Press any key to continue..."
    read -n 1 -s
    echo ""

    echo "14. Project is ready to use!"
    echo "You can access it at http://e-waiter.lan/admin"
    echo "You can access phpmyadmin at http://e-waiter.lan:8080"
    echo ""
    echo "phpmyadmin does not run automatically. You need to run it manually by running 'docker start ewaiter-phpmyadmin' command."
    echo ""
    echo "'npm run watch' on ewaiter-node machine will watch for changes in your assets and compile them automatically."
    docker exec ewaiter-node npm run watch
    echo ""
    echo "Press any key to continue..."
    read -n 1 -s
    clear
    exit 1
}

clear
echo "0. Changing ownership of this folder, subfolders and files to $username"
echo "Current user: $username"
echo "Folder path: $default_path"
echo ""
read -p "Is everything correct? [y/n]: " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    sudo chown -R $username:$username $default_path
    main
    echo ""
fi
echo ""

echo "Press any key to continue..."
read -n 1 -s
clear
