<?php

namespace App\Providers;

use App\Events\EmergencyDetected;
use App\Listeners\SendEmergencyNotifications;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        EmergencyDetected::class => [
            SendEmergencyNotifications::class,
        ],
    ];
}
