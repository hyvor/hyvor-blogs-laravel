<?php

return [

    /**
     * Sett up one or more blogs within your application
     */
    'blogs' => [
        [
            /**
             * The subdomain given by Hyvor Blogs
             * Sign up at https://blogs.hyvor.com/console to create a new one
             */
            'subdomain' => '',

            /**
             * Blog's Delivery API key
             */
            'delivery_api_key' => '',

            /**
             * Where should we host the blog?
             * If the value is /blog, all /blog/* routes will be reserved for the blog
             */
            'route' => '/blog',

            /**
             * What Laravel cache store should we use for caching?
             * null = use default
             * Or, you can set it to any cache store name defined in your config/cache.php file.
             */
            'cache_store' => null,

            /**
             * Need any middleware? Add them here
             */
            'middleware' => []
        ],
        /**
         * You can add more blogs here ;)
         * Just make sure the route is different
         */
    ]

];