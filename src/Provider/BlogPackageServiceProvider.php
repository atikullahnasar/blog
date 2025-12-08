<?php

namespace atikullahnasar\blog\Provider;

use atikullahnasar\blog\Repositories\BlogCategories\BlogCategoryRepository;
use atikullahnasar\blog\Repositories\BlogCategories\BlogCategoryRepositoryInterface;
use atikullahnasar\blog\Repositories\Blogs\BlogRepository;
use atikullahnasar\blog\Repositories\Blogs\BlogRepositoryInterface;
use atikullahnasar\blog\Services\BlogCategories\BlogCategoryService;
use atikullahnasar\blog\Services\BlogCategories\BlogCategoryServiceInterface;
use atikullahnasar\blog\Services\Blogs\BlogService;
use atikullahnasar\blog\Services\Blogs\BlogServiceInterface;
use Illuminate\Support\ServiceProvider;

class BlogPackageServiceProvider extends ServiceProvider
{

    public function boot()
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'blogs');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/../Database/migrations' => database_path('migrations'),
        ], 'blog-migrations');

        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');

    }

    public function register()
    {
        $this->app->bind(BlogCategoryServiceInterface::class, BlogCategoryService::class);
        $this->app->bind(BlogServiceInterface::class, BlogService::class);

        $this->app->bind(BlogRepositoryInterface::class, BlogRepository::class);
        $this->app->bind(BlogCategoryRepositoryInterface::class, BlogCategoryRepository::class);
    }
}
