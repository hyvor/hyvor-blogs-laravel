<?php

namespace Hyvor\HyvorBlogs;

use Hyvor\HyvorBlogs\Http\Middleware\BlogMiddleware;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class HyvorBlogsServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'hyvorblogs');
    }

    public function boot() : void
    {

        /**
         * Routes
         */
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        /**
         * Publishable
         */
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('hyvorblogs.php'),
            ], 'config');
        }

        /**
         * Middleware
         * @var Router $router
         */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('hyvor_blogs_middleware', BlogMiddleware::class);
    }
}
