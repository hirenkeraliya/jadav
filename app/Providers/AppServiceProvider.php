<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
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
    public function boot(): void
    {
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $activeCompany = auth()->user()->activeCompany;
                $view->with([
                    'activeCompany'  => $activeCompany,
                    'primaryColor'   => $activeCompany->primary_color ?? '#6366f1',
                    'secondaryColor' => $activeCompany->secondary_color ?? '#f59e0b',
                ]);
            }
        });
    }
}
