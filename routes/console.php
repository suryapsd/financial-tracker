<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
Schedule::command('app:auto-create-salary-to-income-command')->dailyAt('07:00');
Schedule::command('app:check-financial')->dailyAt('07:00');
Schedule::command('queue:work --stop-when-empty')->everyTenMinutes();
