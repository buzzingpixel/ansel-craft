#!/usr/bin/env bash

function container-anselcraft-db-help() {
    printf "[some_command] (Execute command in \`db\` container. Empty argument starts a bash session)";
}

function container-anselcraft-db() {
    if [ -t 0 ]; then
        interactiveArgs='-it';
    else
        interactiveArgs='';
    fi

    printf "${Yellow}You're working inside the 'db' container of this project.${Reset}\n";

    if [[ -z "${allArgsExceptFirst}" ]]; then
        printf "${Yellow}Remember to 'exit' when you're done.${Reset}\n";
        docker exec ${interactiveArgs} anselcraft-db bash;
    else
        docker exec ${interactiveArgs} anselcraft-db bash -c "${allArgsExceptFirst}";
    fi

    return 0;
}
