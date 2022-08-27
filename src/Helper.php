<?php

namespace Hyvor\HyvorBlogs;

use Hyvor\HyvorBlogs\Exception\UnknownSubdomainException;

class Helper
{
    /**
     * @return array{
     *     subdomain: string,
     *     delivery_api_key: string,
     *     webhook_secret: ?string,
     *     route: string,
     *     cache_store: ?string,
     *     middleware: array<mixed>
     * }
     * @throws UnknownSubdomainException
     */
    public static function getConfigBySubdomain(string $subdomain): array
    {
        $config = config('hyvorblogs.blogs');

        foreach ($config as $blog) {
            if (isset($blog['subdomain']) && $blog['subdomain'] === $subdomain) {
                return $blog;
            }
        }

        throw new UnknownSubdomainException;
    }
}
