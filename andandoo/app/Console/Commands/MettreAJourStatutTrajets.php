<?php

namespace App\Console\Commands;

use App\Models\Trajet;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MettreAJourStatutTrajets extends Command
{
    protected $signature = 'trajets:update-status';

    protected $description = 'Mettre à jour le statut des trajets dont la date est passée';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $trajets = Trajet::where('DateDepart', '<', Carbon::now())->get();
        

        foreach ($trajets as $trajet) {
            $trajet->update(['Status' => 'terminee']);
        }

        $this->info('Les statuts des trajets ont été mis à jour avec succès.');
    }
}
