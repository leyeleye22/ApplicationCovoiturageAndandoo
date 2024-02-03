#Script pour exécuter les seeders
php artisan optimize:clear

#Exécuter migrate:fresh uniquement si la vérification précédente réussit
php artisan migrate:fresh
php artisan test