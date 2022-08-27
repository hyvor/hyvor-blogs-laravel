<?php

namespace Hyvor\HyvorBlogs\Tests\Unit;

use Hyvor\HyvorBlogs\CacheService;
use Hyvor\HyvorBlogs\DeliveryAPIResponseObject;
use Illuminate\Support\Facades\Cache;


it('sets cache', function() {

    $cacheService = new CacheService('test');
    $responseObject = fileResponseObject();
    $cacheService->set('/', $responseObject);

    $cache = Cache::get('hyvor_blogs:test:/');
    expect($cache)->toBe(json_encode($responseObject));

});

it('get - null when no cache', function() {
    $cacheService = new CacheService('test');
    expect($cacheService->get('/'))->toBeNull();
});

it('get - null on invalid json', function() {
    $cacheService = new CacheService('test');
    Cache::put('hyvor_blogs:test:/', 'invalid');
    expect($cacheService->get('/'))->toBeNull();
});

it('get - null when all cache cleared', function() {
    $cacheService = new CacheService('test');
    Cache::put('hyvor_blogs:test:/', json_encode(fileResponseObject()));
    Cache::put('hyvor_blogs:test:LAST_ALL_CACHE_CLEARED_AT', now()->addDays(2)->timestamp);
    expect($cacheService->get('/'))->toBeNull();
});

it('get - null when template cache is cleared for templates', function() {
    $cacheService = new CacheService('test');
    Cache::put('hyvor_blogs:test:/', json_encode(fileResponseObject()));
    Cache::put('hyvor_blogs:test:LAST_TEMPLATE_CACHE_CLEARED_AT', now()->addDays(2)->timestamp);
    expect($cacheService->get('/'))->toBeNull();
});

it('get - not null for non-templates when template cache is cleared', function() {
    $cacheService = new CacheService('test');
    Cache::put('hyvor_blogs:test:/', json_encode(fileResponseObject(['file_type' => 'media'])));
    Cache::put('hyvor_blogs:test:LAST_TEMPLATE_CACHE_CLEARED_AT', now()->addDays(2)->timestamp);
    expect($cacheService->get('/'))->toBeInstanceOf(DeliveryAPIResponseObject::class);
});