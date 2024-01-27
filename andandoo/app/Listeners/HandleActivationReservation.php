<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Mail;
use App\Events\ActivationReservation;
use App\Models\Utilisateur;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleActivationReservation
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
    public function handle(ActivationReservation $event)
    {
        $trajet = $event->trajet;
        $trajet->voiture()->update(['disponible' => true]);
        $users = Utilisateur::where('zone_id', $trajet->voiture->utilisateur->zone_id)->get();

        if ($users) {
            foreach ($users as $user) {

                if ($user->role == "client") {
                    Mail::send('MailActivationTrajet', function ($message) use ($user) {
                        $message->to($user->Email);
                        $message->subject('Reservation Disponible');
                    });
                }
            }
        }
    }
}
