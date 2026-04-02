<?php

namespace App\Providers;

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
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\Product::observe(\App\Observers\ProductObserver::class);
        \App\Models\Sale::observe(\App\Observers\SaleObserver::class);
        \App\Models\ProductReception::observe(\App\Observers\ProductReceptionObserver::class);
        \App\Models\InventoryMovement::observe(\App\Observers\InventoryMovementObserver::class);
    }
}
