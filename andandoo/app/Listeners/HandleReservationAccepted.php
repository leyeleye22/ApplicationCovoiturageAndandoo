<?php

namespace App\Listeners;

use App\Events\DesactivationReservation;
use App\Mail\RefusedReservation;
use App\Events\ReservationAccepted;
use App\Mail\ConfirmationReservation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleReservationAccepted implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }
    /**
     * Handle the event.
     */
    public function handle(ReservationAccepted $event)
    {

        $reservation = $event->reservation;
        $trajet = $reservation->trajet;
        $data[] = [
            'NomClients' => $reservation->utilisateur->Nom,
            'PrenomClients' => $reservation->utilisateur->Prenom,
            'LieuDepart' => $trajet->LieuDepart,
            'LieuArrivee' => $trajet->LieuArrivee,
            'DateDepart' => $trajet->DateDepart,
            'HeureD' => $trajet->HeureD,
            'Prix' => $trajet->Prix,
            'NomChauffeur' => $trajet->voiture->utilisateur->Prenom,
            'TelephoneChauffeur' => $trajet->voiture->utilisateur->Telephone
        ];
        Mail::to($reservation->utilisateur->email)->send(new ConfirmationReservation($data));
        $total_place_reserve = $trajet->reservations()->where('Accepted', true)->sum('NombrePlaces');
        if ($total_place_reserve == $trajet->voiture->NbrPlaces) {
            $trajet->voiture()->update(['disponible' => false]);
            event(new DesactivationReservation($trajet->voiture));
        }
    }
}
