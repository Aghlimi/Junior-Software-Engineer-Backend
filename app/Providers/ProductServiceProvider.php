<?php

namespace App\Providers;

use App\Repositories\ProductRepository;
use App\Repositories\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Support\ServiceProvider;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->singleton(ProductService::class, fn ($app) => new ProductService($app->make(ProductRepositoryInterface::class)));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
