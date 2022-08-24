<?php

use Hyvor\HyvorBlogs\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

/**
 * First, add blog routes!
 */
$blogs = config('hyvorblogs.blogs');

foreach ($blogs as $blog) {
    $route = $blog['route'] . '/{path?}';
    $subdomain = $blog['subdomain'];

    /**
     * Set the main middleware with subdomain
     */
    $middleware = ["hyvor_blogs_middleware:$subdomain"];

    /**
     * Connect user-defined middleware
     */
    $middleware += $blog['middleware'] ?? [];

    /**
     * Send /route/{path?} to BlogController::handle
     */
    Route::get($route, [BlogController::class, 'handle'])
        /**
         * Add Middleware
         */
        ->middleware($middleware)
        /**
         * Make sure path matches anything
         */
        ->where('path', '.*');

}