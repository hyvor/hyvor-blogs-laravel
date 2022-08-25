<?php

namespace Hyvor\HyvorBlogs;

use Illuminate\Support\Facades\Http;

class ResponseGenerator
{
    private string $subdomain;

    private string $path;

    public function __construct(string $subdomain, string $path)
    {
        $this->subdomain = $subdomain;
        $this->path = $path;
    }

    public function getResponse()
    {

        /**
         * First, check cache
         */
        $cacheService = new CacheService($this->subdomain);
        $cacheService->clearAllCache();
        $cachedResponseObject = $cacheService->get($this->path);

        if ($cachedResponseObject) {
            return $this->convertResponseObjectToLaravelResponse($cachedResponseObject);
        }

        /**
         * If not found in cache,
         * call the Delivery API
         */
        $responseObject = $this->callDeliveryApi();

        if ($responseObject->cache === true) {
            $cacheService->set($this->path, $responseObject);
        }

        return $this->convertResponseObjectToLaravelResponse($responseObject);
    }

    private function callDeliveryApi()
    {
        $baseUrl = config('hyvorblogs.hb_base_url');
        $response = Http::get("$baseUrl/api/delivery/v0/$this->subdomain", [
            'path' => $this->path,
        ]);

        $response->throw();

        return (object) $response->json();
    }

    /**
     * Converts [Hyvor Blogs Delivery API Response Object](https://blogs.hyvor.com/docs/api-delivery#response-object)
     * To a Laravel response
     */
    private function convertResponseObjectToLaravelResponse(object $responseObject)
    {
        if ($responseObject->type === 'file') {

            // return a file response
            return response(
                base64_decode($responseObject->content),
                $responseObject->status
            )
                ->header('Content-Type', $responseObject->mime_type);
        } elseif ($responseObject->type === 'redirect') {

            // return a redirect response
            return redirect($responseObject->to, $responseObject->status);
        }
    }
}
