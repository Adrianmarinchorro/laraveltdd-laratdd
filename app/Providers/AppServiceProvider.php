<?php

namespace App\Providers;


use App\Sortable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\UserFieldsComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('shared._card', 'card');

        Paginator::useBootstrap();

        $this->app->bind(LengthAwarePaginator::class, \App\LengthAwarePaginator::class); // esta ultima tenemos que crearla nosotros
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Sortable::class, function ($app) {
           return new Sortable(request()->url()); // nos devuelve mediante el helper request la url de donde nos encontramos en este momento
        });
    }
}
