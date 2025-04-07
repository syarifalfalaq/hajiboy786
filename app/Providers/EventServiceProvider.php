<?php

namespace App\Providers;


use App\Models\Resiumum;
use App\Observers\ResiumumObserver;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Resiumum::observe(ResiumumObserver::class);
    }
}
