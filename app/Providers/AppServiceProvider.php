<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale(config('app.locale'));
        Carbon::setToStringFormat('d-m-Y H:i');

        // Custom error handler to suppress DOMPDF deprecation warnings
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            // Suppress deprecation warnings related to DOMPDF
            if ($errno === E_DEPRECATED && strpos($errstr, 'file_get_contents') !== false) {
                return true; // Suppress this error
            }
            if ($errno === E_DEPRECATED && strpos($errfile, 'dompdf') !== false) {
                return true; // Suppress all deprecation warnings from dompdf
            }

            // Let other errors through
            return false;
        }, E_DEPRECATED);
    }
}
