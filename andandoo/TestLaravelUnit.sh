#!/bin/bash

# Changement de la base de données en laraveltest dans le fichier .env
sed -i '/DB_DATABASE=laravel/d' .env
echo "DB_DATABASE=laravelTest" >> .env


# Nettoyage de l'optimisation des performances
php artisan optimize:clear
#php artisan migrate:fresh
# Exécution des tests
php artisan test --filter=VoitureTest

# Restauration de la base de données en laravel après les tests
sed -i '/DB_DATABASE=laravelTest/d' .env
echo "DB_DATABASE=AndandooApp" >> .env
#source script2.sh Pour executer en meme un autre fichier sh
