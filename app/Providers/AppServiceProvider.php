<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // https://stackoverflow.com/a/41434119
        Response::macro('attachment', function ($content) {
            $headers = [
                'Content-type'        => 'text/csv',
                'Content-Disposition' => 'attachment; filename="download.csv"'
            ];
            return Response::make($content, 200, $headers);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
