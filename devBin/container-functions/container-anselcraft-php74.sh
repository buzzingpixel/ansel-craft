#!/usr/bin/env bash

function container-anselcraft-php74-help() {
    printf "[some_command] (Execute command in \`php74\` container. Empty argument starts a bash session)";
}

function container-anselcraft-php74() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    printf "${Yellow}You're working inside the 'php74' container of this project.${Reset}\n";

    if [[ -z "${allArgsExceptFirst}" ]]; then
        printf "${Yellow}Remember to 'exit' when you're done.${Reset}\n";
        docker exec ${interactiveArgs} -e XDEBUG_MODE=off -w /var/www anselcraft-php74 bash;
    else
        docker exec ${interactiveArgs} -w /var/www anselcraft-php74 bash -c "XDEBUG_MODE=off ${allArgsExceptFirst}";
    fi

    return 0;
}
