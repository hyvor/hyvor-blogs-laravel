<?php
namespace Hyvor\HyvorBlogs\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * This middleware does nothing more than passing 
 * the subdomain from the route to the controller
 */
class BlogMiddleware {

    public function handle(Request $request, Closure $next, string $subdomain) {

        /**
         * Set the hyvor_blogs_subdomain to get it in the Controller later
         */
        $request->attributes->set('hyvor_blogs_subdomain', $subdomain);

        return $next($request);

    }

}