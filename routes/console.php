<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Register commands from app/Console/Commands
Artisan::command('emergency:test-sms {userId?} {--contact=} {--phone=} {--message=}', function () {
    $this->call(\App\Console\Commands\TestEmergencySms::class);
})->purpose('Send a test emergency SMS to verify TextBee gateway');
