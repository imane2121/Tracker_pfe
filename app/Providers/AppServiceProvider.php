<?php

namespace App\Providers;
use App\Models\Signal;
use App\Observers\SignalObserver;
use Illuminate\Support\ServiceProvider;

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
    }
}
