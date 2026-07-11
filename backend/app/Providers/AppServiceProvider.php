<?php

namespace App\Providers;

use App\Models\MatchModel;
use App\Observers\MatchModelObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        MatchModel::observe(MatchModelObserver::class);
    }
}
