#!/bin/bash
sed -i '/DB_DATABASE=laravel/d\
DB_DATABASE=laraveltest' .env
php artisan optimize:clear
php artisan test
sed -i '/DB_DATABASE=laraveltest/d\
DB_DATABASE=laravel' .env