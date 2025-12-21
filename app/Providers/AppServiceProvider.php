<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar Excel si no está disponible automáticamente
        if (class_exists('Maatwebsite\Excel\ExcelServiceProvider')) {
            $this->app->register('Maatwebsite\Excel\ExcelServiceProvider');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
