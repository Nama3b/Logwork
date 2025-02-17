<?php

namespace App\Providers;

use App\Services\EventService;
use App\Services\Interfaces\IEventService;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * All the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        IEventService::class => EventService::class,
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        if (env('APP_ENV') == 'production') {
            $url->forceScheme('https');
        }
    }
}
