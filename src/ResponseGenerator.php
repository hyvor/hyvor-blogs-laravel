<?php

namespace Hyvor\HyvorBlogs;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
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

    public function getResponse() : Response|RedirectResponse
    {

        /**
         * First, check cache
         */
        $cacheService = new CacheService($this->subdomain);
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

    private function callDeliveryApi() : DeliveryAPIResponseObject
    {
        /**
         * @var string $baseUrl
         */
        $baseUrl = config('hyvorblogs.hb_base_url');
        $response = Http::get("$baseUrl/api/delivery/v0/$this->subdomain", [
            'path' => $this->path,
            'api_key' => Helper::getConfigBySubdomain($this->subdomain)['delivery_api_key']
        ]);

        $response->throw();

        return DeliveryAPIResponseObject::create($response->json());
    }

    /**
     * Converts [Hyvor Blogs Delivery API Response Object](https://blogs.hyvor.com/docs/api-delivery#response-object)
     * To a Laravel response
     */
    private function convertResponseObjectToLaravelResponse(
        DeliveryAPIResponseObject $responseObject
    ) : Response|RedirectResponse
    {
        if ($responseObject->type === 'file') {

            // return a file response
            return response(
                base64_decode($responseObject->content),
                $responseObject->status
            )
                ->header('Content-Type', $responseObject->mime_type)
                ->header('Cache-Control', $responseObject->cache_control);

        } else {

            // return a redirect response
            return redirect($responseObject->to, $responseObject->status);
        }
    }
}
