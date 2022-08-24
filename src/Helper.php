<?php
namespace Hyvor\HyvorBlogs;

class Helper {

    public static function getConfigBySubdomain(string $subdomain) : array 
    {

        $config = config('hyvorblogs.blogs');

        foreach ($config as $blog) {
            if ($blog['subdomain'] === $subdomain) {
                return $blog;
            }
        }

        // TODO: Add exception

    }

}