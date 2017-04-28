#!/usr/bin/env bash
mkdir build 2>/dev/null
pushd build
clang -lm -Wall -g ../calculator.c -o calculator
popd
