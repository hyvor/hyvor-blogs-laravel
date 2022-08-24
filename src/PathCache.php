<?php
namespace Hyvor\HyvorBlogs;

use Illuminate\Cache\Repository;
use Illuminate\Support\Facades\Cache;

class PathCache {

    private string $subdomain;
    private string $path;

    private string $cacheKey;
    private Repository $cacheInst;

    public function __construct(string $subdomain, string $path) 
    {

        $this->subdomain = $subdomain;
        $this->path = $path;

        $this->setCacheKey();
        $this->setCacheInst();

    }

    public function get() : ?array
    {
        $responseObject = $this->cacheInst->get($this->cacheKey);
        return $responseObject ? json_decode($responseObject, true) : null;
    }

    public function set(array $responseObject)
    {
        $this->cacheInst->set($this->cacheKey, json_encode($responseObject));
    }

    private function setCacheInst() 
    {

        $config = Helper::getConfigBySubdomain($this->subdomain);
        $this->cacheInst = Cache::store($config['cache_store']);

    }

    private function setCacheKey() 
    {
        $this->cacheKey = "hyvor_blogs:{$this->subdomain}:$this->path";
    }

}