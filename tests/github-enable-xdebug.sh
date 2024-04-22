#!/usr/bin/env bash

sudo pecl install xdebug
sudo phpenmod xdebug
echo -e "zend_extension=xdebug\nxdebug.mode=coverage" | sudo tee -a "$(cut -d '"' -f 2 < <(php --ini | grep '(php.ini)' | sed -e "s|.*: s*||"))/php.ini"