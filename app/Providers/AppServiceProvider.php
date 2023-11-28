<?php

namespace App\Providers;

use App\Models\DepartamentoModel;
use App\Observers\DepartamentoObserver;
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
        DepartamentoModel::observe(DepartamentoObserver::class);
    }
}
