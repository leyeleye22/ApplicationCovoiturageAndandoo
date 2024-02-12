sleep 10
composer install  --no-interaction 
php artisan key:generate --force
php artisan optimize:clear
php artisan migrate --force
php artisan db:seed
apache2-foreground