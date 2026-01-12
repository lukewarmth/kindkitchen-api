#!/usr/bin/env bash
composer install --no-dev
php artisan migrate --force