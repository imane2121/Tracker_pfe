<?php

namespace App\Providers;
use App\Models\Signal;
use App\Observers\SignalObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
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
    public function boot()
    {
        Signal::observe(SignalObserver::class);
        
        // Force HTTPS for assets when running through ngrok
        if(str_contains(config('app.url'), 'ngrok')) {
            URL::forceScheme('https');
        }

        Blade::componentNamespace('App\\View\\Components', 'messaging');
    }
}
