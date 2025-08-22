<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ApiService;
use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
{
    $this->app->singleton(ApiService::class, function () {
        return new ApiService(new Client([
            'base_uri' => rtrim(config('services.remote_api.base_url'), '/').'/',
            'timeout'  => (int) config('services.remote_api.timeout', 15),
            'headers'  => array_filter([
                'Accept'        => 'application/json',
                'Authorization' => config('services.remote_api.bearer')
                    ? 'Bearer '.config('services.remote_api.bearer')
                    : null,
            ]),
            'verify'   => false, // si tu SSL remoto tiene self-signed
        ]));
    });
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
