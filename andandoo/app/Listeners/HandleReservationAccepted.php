<?php

namespace App\Listeners;

use App\Mail\RefusedReservation;
use App\Events\ReservationAccepted;
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


        $total_place_reserve = $trajet->reservations()->where('Accepted', true)->sum('NombrePlaces');
        if ($total_place_reserve == $trajet->voiture->NbrPlaces) {
            $trajet->voiture()->update(['disponible' => false]);
            $reservations = $trajet->reservations->disponible;
            foreach ($reservations as $reservation) {
                if (!$reservations->disponible) {
                    Mail::to($reservation->utilisateur->Email)->send(new RefusedReservation());
                    $reservation->delete();
                }
            }
        }
    }
}
