<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $message = "🕒 Laravel Cron Job is running at " . now();
    Log::info($message); // Store in Laravel logs
    echo $message . PHP_EOL; // Output to console
})->everyTwoMinutes();
