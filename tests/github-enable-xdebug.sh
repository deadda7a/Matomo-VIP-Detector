#!/usr/bin/env bash

sudo pecl install xdebug &>/dev/null
sudo phpenmod xdebug
echo "xdebug.mode=coverage" | sudo tee -a "$(cut -d '"' -f 2 < <(php --ini | grep '(php.ini)' | sed -e "s|.*: s*||"))/php.ini"