#!/usr/bin/env bash

command -v pre-commit >/dev/null 2>&1 || {
    echo ""
    echo "The pre-commit tool was not found on your system. You will"
    echo "need to install the pre-commit tool from http://pre-commit.com/"
    echo "and then run 'pre-commit install' in this directory. Check out the"
    echo ".pre-commit-config.yaml file to learn more about this repositories pre-commit checks."
    exit 0;
}

echo ""
echo "Installing the pre-commit hooks for this repo"
echo ""

pre-commit install --hook-type pre-commit
pre-commit install --hook-type pre-push
