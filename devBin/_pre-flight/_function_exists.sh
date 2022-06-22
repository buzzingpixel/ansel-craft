#!/usr/bin/env bash

_function_exists() {
    declare -f -F $1 > /dev/null
    return $?
}
