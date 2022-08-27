<?php

namespace Hyvor\HyvorBlogs\Tests\Feature\Blog;

use Hyvor\HyvorBlogs\CacheService;
use Hyvor\HyvorBlogs\DeliveryAPIResponseObject;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

it('renders blog from API data', function() {

    Http::fake([
        'https://blogs.hyvor.com/api/delivery/v0/test*' => Http::response([
            'type' => 'file',
            'at' => now()->timestamp,
            'cache' => true,
            'status' => 201,
            'file_type' => 'template',
            'content' => base64_encode('Test Index Page'),
            'mime_type' => 'text/test'
        ])
    ]);

    test()->call('GET', '/blog')
        ->assertSee('Test Index Page')
        ->assertHeader('Content-Type', 'text/test; charset=UTF-8')
        ->assertStatus(201);

});

it('sets redirects from API data', function() {

    Http::fake([
        'https://blogs.hyvor.com/api/delivery/v0/test*' => Http::response([
            'type' => 'redirect',
            'at' => now()->timestamp,
            'cache' => true,
            'status' => 301,
            'to' => 'https://supun.io'
        ])
    ]);

    test()->call('GET', '/blog')
        ->assertRedirect('https://supun.io');

});

it('works with path', function() {

    Http::fake([
        'https://blogs.hyvor.com/api/delivery/v0/test*' => Http::response([
            'type' => 'file',
            'at' => now()->timestamp,
            'cache' => true,
            'status' => 201,
            'file_type' => 'template',
            'content' => base64_encode('Test Page'),
            'mime_type' => 'text/test'
        ])
    ]);

    test()->call('GET', '/blog/page')
        ->assertSee('Test Page')
        ->assertHeader('Content-Type', 'text/test; charset=UTF-8')
        ->assertStatus(201);

    Http::assertSent(function (Request $request) {
        return $request['path'] === '/page';
    });

});

it('works from cache', function() {

    $cacheService = new CacheService('test');
    $cacheService->set('/page', fileResponseObject([
        'content' => base64_encode('new test message')
    ]));

    test()->call('GET', '/blog/page')
        ->assertSee('new test message')
        ->assertHeader('Content-Type', 'text/html; charset=UTF-8')
        ->assertStatus(200);

});

it('caches', function() {

    Http::fake([
        'https://blogs.hyvor.com/api/delivery/v0/test*' => Http::response([
            'type' => 'file',
            'at' => now()->timestamp,
            'cache' => true,
            'status' => 200,
            'file_type' => 'template',
            'content' => base64_encode('Test Index Page'),
            'mime_type' => 'text/test'
        ])
    ]);

    test()->call('GET', '/blog')->assertOk();

    $cacheService = new CacheService('test');
    expect($cacheService->get('/'))->toBeInstanceOf(DeliveryAPIResponseObject::class);

});

it('does not cache when cache is false', function() {

    Http::fake([
        'https://blogs.hyvor.com/api/delivery/v0/test*' => Http::response([
            'type' => 'file',
            'at' => now()->timestamp,
            'cache' => false,
            'status' => 200,
            'file_type' => 'template',
            'content' => base64_encode('Test Index Page'),
            'mime_type' => 'text/test'
        ])
    ]);

    test()->call('GET', '/blog')->assertOk();

    $cacheService = new CacheService('test');
    expect($cacheService->get('/'))->toBeNull();

});

test('blog-2 works', function() {

    Http::fake([
        'https://blogs.hyvor.com/api/delivery/v0/test*' => Http::response([
            'type' => 'file',
            'at' => now()->timestamp,
            'cache' => false,
            'status' => 200,
            'file_type' => 'template',
            'content' => base64_encode('blog 2'),
            'mime_type' => 'text/test'
        ])
    ]);

    test()->call('GET', '/blog')->assertOk()->assertSee('blog 2');

});