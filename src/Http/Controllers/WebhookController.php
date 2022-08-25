<?php

namespace Hyvor\HyvorBlogs\Http\Controllers;

use Hyvor\HyvorBlogs\CacheService;
use Hyvor\HyvorBlogs\Exception\UnknownSubdomainException;
use Hyvor\HyvorBlogs\Exception\WebhookValidationException;
use Hyvor\HyvorBlogs\Helper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController
{
    /**
     * Returns a 200 response on known errors to prevent webhook re-trying
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            $subdomain = $request->post('subdomain');
            $blogConfig = Helper::getConfigBySubdomain($subdomain);

            $webhookSecret = $blogConfig['webhook_secret'];
            if (is_string($webhookSecret)) {
                $this->validateWebhook($request, $webhookSecret);
            }

            $cacheService = new CacheService($subdomain);
            $event = $request->post('event');

            match ($event) {
                'cache.single' => $cacheService->clearSingleCache($request->post('data.path')),
                'cache.templates' => $cacheService->clearTemplateCache(),
                'cache.all' => $cacheService->clearAllCache(),
                default => null
            };
        } catch (UnknownSubdomainException) {
            Log::alert("Webhook for invalid subdomain: $subdomain");
        } catch (WebhookValidationException) {
            Log::alert('Unable to validate webhook');
        }

        return $this->okResponse();
    }

    private function okResponse(): JsonResponse
    {
        return response()->json();
    }

    private function validateWebhook(Request $request, string $webhookSecret)
    {
    }
}
