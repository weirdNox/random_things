#!/usr/bin/env bash
# This is set up to work with my emacs configuration
mkdir "$(dirname "$0")/build"
pushd "$(dirname "$0")/build"
filename=$(basename "$1")
filename=${filename%.*}
clang -lm -Wall -g "$1" -o "$filename.out"
popd
