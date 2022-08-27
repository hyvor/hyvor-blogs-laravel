<?php

namespace Hyvor\HyvorBlogs\Http\Controllers;

use Hyvor\HyvorBlogs\ResponseGenerator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class BlogController extends Controller
{
    public function handle(Request $request)
    {

        /**
         * Get the blog's subdomain
         * This is set by Hyvor\HyvorBlogs\Http\Middleware\BlogMiddleware
         */
        $subdomain = $request->attributes->get('hyvor_blogs_subdomain');

        /**
         * path is now null or string without slash
         */
        $path = $request->route('path');

        /**
         * If path is null, it means the index page
         * Add leading slash if else
         */
        $path = $path === null ? '/' : '/'.$path;

        /**
         * Generate laravel response
         */
        $response = new ResponseGenerator($subdomain, $path);

        return $response->getResponse();
    }
}
