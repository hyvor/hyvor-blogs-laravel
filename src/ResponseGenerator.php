<?php
namespace Hyvor\HyvorBlogs;

use Illuminate\Support\Facades\Http;

class ResponseGenerator {

    private string $subdomain;
    private string $path;

    public function __construct(string $subdomain, string $path) {
        $this->subdomain = $subdomain;
        $this->path = $path;
    }

    public function getResponse() {

        /**
         * First, check cache
         */
        $pathCache = new PathCache($this->subdomain, $this->path);
        $cachedResponseObject = $pathCache->get();

        if ($cachedResponseObject) {
            return $this->convertResponseObjectToLaravelResponse($cachedResponseObject);
        }

        /**
         * If not found in cache,
         * call the Delivery API
         */
        $responseObject = $this->callDeliveryApi();

        if ($responseObject['cache']) {
            $pathCache->set($responseObject);
        }

        return $this->convertResponseObjectToLaravelResponse($responseObject);

    }

    private function callDeliveryApi() {

        $response = Http::withOptions(["verify"=>false])
            ->get('https://blogs.hyvor.test/api/delivery/v0/blog/' . $this->subdomain, [
                'path' => $this->path
            ]);

        return $response->json();

    }

    /**
     * Converts [Hyvor Blogs Delivery API Response Object](https://blogs.hyvor.com/docs/api-delivery#response-object)
     * To a Laravel response
     */
    private function convertResponseObjectToLaravelResponse(array $responseObject) {

        if ($responseObject['type'] === 'file') {

            // return a file response
            return response(base64_decode($responseObject['content']), $responseObject['status'])
                ->header('Content-Type', $responseObject['mime_type']);

        } else if ($responseObject['type'] === 'redirect') {

            // return a redirect response
            return redirect($responseObject['to'], $responseObject['status']);

        }

    }

}