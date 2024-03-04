<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User; // Assurez-vous d'importer le modèle User s'il n'est pas déjà importé
use App\Models\Utilisateur;

class BlockUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:block-user {user_id : ID de l\'utilisateur à bloquer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bloquer un utilisateur en définissant TemporaryBlock sur true';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');

        // Recherchez l'utilisateur dans la base de données
        $user = Utilisateur::find($id);

        if (!$user) {
            $this->error("L'utilisateur avec l'ID $id n'existe pas.");
            return;
        }

        $user->TemporaryBlock = true;
        $user->save();

        $this->info("L'utilisateur avec l'ID $id a été bloqué avec succès.");
    }
}
