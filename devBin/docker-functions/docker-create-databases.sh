#!/usr/bin/env bash

#!/usr/bin/env bash

function docker-create-databases-help() {
    printf "(Creates databases)";
}

function docker-create-databases() {
    # Craft
    docker exec anselcraft-db bash -c "mysql -uroot -proot -e \"CREATE DATABASE anselcraft\"";
    docker exec anselcraft-db bash -c "mysql -uroot -proot -e \"CREATE USER 'anselcraft'@'%' IDENTIFIED BY 'secret'\"";
    docker exec anselcraft-db bash -c "mysql -uroot -proot -e \"GRANT ALL on anselcraft.* to 'anselcraft'@'%'\"";

    return 0;
}
