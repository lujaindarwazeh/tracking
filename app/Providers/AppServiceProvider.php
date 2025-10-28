<?php

namespace App\Providers;

use App\Models\companyusers;
use Illuminate\Support\ServiceProvider;
use App\Observers\CompanyUserObserver;

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
    public function boot(): void
    {
        companyusers::observe(CompanyUserObserver::class);


        //
    }
}
