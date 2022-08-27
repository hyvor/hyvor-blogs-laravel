<?php

use Hyvor\HyvorBlogs\DeliveryAPIResponseObject;
use Hyvor\HyvorBlogs\Tests\TestCase;

uses(TestCase::class)->in('Feature', 'Unit');

function fileResponseObject(array $extend = []) : DeliveryAPIResponseObject {
    return DeliveryAPIResponseObject::create(array_merge([
        'type' => 'file',
        'at' => now()->timestamp,
        'cache' => true,
        'status' => 200,
        'file_type' => 'template',
        'content' => base64_encode('test'),
        'mime_type' => 'text/html'
    ], $extend));
}

uses()->beforeEach(function() {
    config(['hyvorblogs.blogs' => defaultConfig()]);
})->in('Feature', 'Unit');

function defaultConfig() {
    return [
        [
            'subdomain' => 'test',
            'delivery_api_key' => '',
            'webhook_secret' => null,
            'route' => '/blog',
            'cache_store' => '',
            'middleware' => []
        ],
        [
            'subdomain' => 'test-2',
            'delivery_api_key' => '',
            'webhook_secret' => null,
            'route' => '/blog-2',
            'cache_store' => '',
            'middleware' => []
        ]
    ];
}

// updates blog config
function updateConfig(array $update) {
    $blog = defaultConfig()[0];
    config(['hyvorblogs.blogs' => [array_merge($blog, $update)]]);
}

function callWebhook(string $event, array $data = [], string $signature = null) {

    $payload = [
        'subdomain' => 'test',
        'timestamp' => now()->timestamp,
        'event' => $event,
        'data' => $data
    ];

    $signature ??= hash_hmac('sha256', json_encode($payload), 'test_secret');
    return test()->call('POST', '/hyvorblogs/webhook', $payload, [], [], [
        'HTTP_X_SIGNATURE' => $signature
    ]);
}