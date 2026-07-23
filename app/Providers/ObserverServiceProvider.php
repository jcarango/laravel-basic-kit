<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Suffragan;
use App\Observers\SuffraganObserver;

class ObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Suffragan::observe(SuffraganObserver::class);
    }
}
