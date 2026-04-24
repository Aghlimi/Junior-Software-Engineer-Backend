<?php

namespace App\Providers;

use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('ProductRepositoryInterface', function () {
            return new ProductRepository;
        });

        $this->app->bind('ProductService', function () {
            return new ProductService(app('ProductRepositoryInterface'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
