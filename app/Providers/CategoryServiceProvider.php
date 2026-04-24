<?php

namespace App\Providers;

use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use Illuminate\Support\ServiceProvider;

class CategoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('CategoryRepositoryInterface', function () {
            return new CategoryRepository;
        });

        $this->app->bind('CategoryService', function () {
            return new CategoryService(app('CategoryRepositoryInterface'));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
