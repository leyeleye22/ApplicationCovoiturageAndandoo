<?php

namespace App\Listeners;

use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;
use App\Events\DesactivationReservation;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleDesactivationReservation
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
    public function handle(DesactivationReservation $event)
    {
        $voiture = $event->voiture;
        $voiture->update(['disponible' => false]);
        $reservations=Reservation::where('voiture_id',$voiture->id)->get();
        foreach($reservations as $reservation){
            Mail::send('MailDesactivationTrajet', function ($message) use ($reservation){
                $message->to($reservation->utilisateur->email);
                $message->subject('Reservation Annuler');
           });
        }
    }
}
