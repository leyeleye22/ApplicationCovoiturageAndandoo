<?php

namespace App\Providers;

use App\Events\ActivationReservation;
use App\Events\DesactivationReservation;
use App\Events\ReservationAccepted;
use App\Listeners\HandleActivationReservation;
use App\Listeners\HandleDesactivationReservation;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\HandleReservationAccepted;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ReservationAccepted::class=>[
            HandleReservationAccepted::class
        ],
        DesactivationReservation::class=>[
            HandleDesactivationReservation::class
        ],
        ActivationReservation::class=>[
            HandleActivationReservation::class
        ],
   
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
