<?php

namespace Hyvor\HyvorBlogs;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    public const LAST_TEMPLATE_CACHE_CLEARED_AT = 'LAST_TEMPLATE_CACHE_CLEARED_AT';

    public const LAST_ALL_CACHE_CLEARED_AT = 'LAST_ALL_CACHE_CLEARED_AT';

    private Repository $cacheStore;

    public function __construct(private string $subdomain)
    {
        $config = Helper::getConfigBySubdomain($this->subdomain);
        $this->cacheStore = Cache::store($config['cache_store']);
    }

    private function getKey(string $key): string
    {
        return "hyvor_blogs:$this->subdomain:$key";
    }

    private function getFromCache(string $key): ?string
    {
        $key = $this->getKey($key);
        return $this->cacheStore->get($key);
    }

    public function set(string $path, DeliveryAPIResponseObject $response) : void
    {
        $this->cacheStore->put($this->getKey($path), json_encode($response));
    }

    public function get(string $path): ?DeliveryAPIResponseObject
    {
        $cached = $this->getFromCache($path);

        if (! $cached) {
            return null;
        }

        $response = DeliveryAPIResponseObject::createFromJson($cached);

        if (! $response) {
            return null;
        }

        $at = $response->at;

        $lastCacheAllClearedAt = $this->getFromCache(self::LAST_ALL_CACHE_CLEARED_AT) ?? 0;
        if ($at < $lastCacheAllClearedAt) {
            return null;
        }

        if (
            $response->type === 'file' &&
            $response->file_type === 'template'
        ) {
            $templateCacheClearedAt = $this->getFromCache(self::LAST_TEMPLATE_CACHE_CLEARED_AT) ?? 0;

            if ($at < $templateCacheClearedAt) {
                return null;
            }
        }

        return $response;
    }

    public function clearSingleCache(string $path) : void
    {
        $this->cacheStore->forget($this->getKey($path));
    }

    public function clearTemplateCache() : void
    {
        $this->cacheStore->put($this->getKey(self::LAST_TEMPLATE_CACHE_CLEARED_AT), now()->timestamp);
    }

    public function clearAllCache() : void
    {
        $this->cacheStore->put($this->getKey(self::LAST_ALL_CACHE_CLEARED_AT), now()->timestamp);
    }
}
