<?php


use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:cancel-expired-orders-comand')->everyFourHours($minutes = 0); //ya en proyecto y servidor real
//Schedule::command('app:cancel-expired-orders-comand')->everyMinute(); //para pruebas en servidor local
