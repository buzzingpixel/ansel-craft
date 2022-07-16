#!/usr/bin/env bash

function docker-up() {
    START_DIR=$(pwd);
    cd ${START_DIR}/docker;

    # Up the env
    docker compose -f docker-compose.yml -p anselcraft up -d;

    # Craft 3
    docker exec -w /var/www/craft3 anselcraft-php74 bash -c "mkdir storage/session";
    docker exec -w /var/www/craft3 anselcraft-php74 bash -c "composer install";
    docker exec -w /var/www/craft3/config anselcraft-php74 bash -c "chmod -R 0777 project";
    docker exec -w /var/www/craft3/public anselcraft-php74 bash -c "chmod -R 0777 cpresources";
    docker exec -w /var/www/craft3/public anselcraft-php74 bash -c "chmod -R 0777 uploads";
    docker exec -w /var/www/craft3 anselcraft-php74 bash -c "chmod -R 0777 storage";

    cd ${START_DIR};

    return 0;
}
