<?php

namespace Hyvor\HyvorBlogs\Tests\Feature\Webhook;

use Hyvor\HyvorBlogs\CacheService;
use Hyvor\HyvorBlogs\DeliveryAPIResponseObject;
use Illuminate\Support\Facades\Cache;

it('clears single cache', function() {

    $cacheService = new CacheService('test');
    $cacheService->set('/test', fileResponseObject());

    expect($cacheService->get('/test'))->toBeInstanceOf(DeliveryAPIResponseObject::class);

    callWebhook('cache.single', [
        'path' => '/test'
    ])->assertOk();

    expect($cacheService->get('/test'))->toBeNull();

});

it('clears template cache', function() {

    callWebhook('cache.templates')->assertOk();
    expect(Cache::get('hyvor_blogs:test:LAST_TEMPLATE_CACHE_CLEARED_AT'))->toBeInt();

});

it('clears all cache', function() {

    callWebhook('cache.all')->assertOk();
    expect(Cache::get('hyvor_blogs:test:LAST_ALL_CACHE_CLEARED_AT'))->toBeInt();

});