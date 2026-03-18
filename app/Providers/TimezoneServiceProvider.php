<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;

class TimezoneServiceProvider extends ServiceProvider
{
    public function boot()
    {
        try {
            $timezone = Setting::get('app_timezone', 'Asia/Jakarta');
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        } catch (\Exception $e) {
            // Ignore if database not ready
        }
    }
}