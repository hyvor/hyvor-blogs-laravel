<?php

namespace Hyvor\HyvorBlogs\Http\Controllers;

use Hyvor\HyvorBlogs\CacheService;
use Hyvor\HyvorBlogs\Exception\UnknownSubdomainException;
use Hyvor\HyvorBlogs\Exception\WebhookValidationException;
use Hyvor\HyvorBlogs\Helper;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;


class WebhookController
{
    /**
     * Returns a 200 response on known errors to prevent webhook re-trying
     */
    public function handle(Request $request)
    {
        $msg = 'OK';

        try {

            $subdomain = $request->post('subdomain');

            if (!is_string($subdomain)) {
                throw new UnprocessableEntityHttpException('Invalid subdomain');
            }

            $blogConfig = Helper::getConfigBySubdomain($subdomain);

            $webhookSecret = $blogConfig['webhook_secret'];
            if (is_string($webhookSecret)) {
                $this->validateWebhook($request, $webhookSecret);
            }

            $cacheService = new CacheService($subdomain);
            $event = $request->post('event');

            match ($event) {
                'cache.single' => $cacheService->clearSingleCache($request->input('data.path')),
                'cache.templates' => $cacheService->clearTemplateCache(),
                'cache.all' => $cacheService->clearAllCache(),
                default => null
            };
        } catch (UnknownSubdomainException) {
            $msg = "Webhook for invalid subdomain: $subdomain";
        } catch (WebhookValidationException) {
            $msg = 'Unable to validate webhook';
        }

        return response($msg);
    }

    private function validateWebhook(Request $request, string $webhookSecret) : void
    {

        $content = (string) $request->getContent();
        $knownHash = hash_hmac('sha256', $content, $webhookSecret);

        $signature = $request->header('X-Signature');
        if (!hash_equals($knownHash, $signature))
            throw new WebhookValidationException;
    }
}
