#!/usr/bin/env bash

git clone https://github.com/matomo-org/matomo /tmp/matomo
git clone https://github.com/matomo-org/component-network.git /tmp/matomo-net
cd /tmp/matomo || exit 1
git checkout 5.2.0
git submodule update --init
composer install --prefer-dist --no-progress
