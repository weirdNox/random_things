#!/usr/bin/env bash
mkdir "$(dirname "$0")/build"
pushd "$(dirname "$0")/build"
clang -lm -lcurl -Wall -g "../main.c" -o "AfonsoAmaral"
popd
