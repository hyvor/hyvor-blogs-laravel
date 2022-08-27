<?php

namespace Hyvor\HyvorBlogs\Tests\Feature\Webhook;

it('fails on invalid subdomain', function() {
    $this->post('/hyvorblogs/webhook')->assertUnprocessable();
});

it('returns a 200 response and logs when the subdomain is not found', function() {
    $this->post('/hyvorblogs/webhook', [
        'subdomain' => 'unknown'
    ])->assertOk();
});

it('validates webhook', function() {
    updateConfig(['webhook_secret' => 'test_secret']);
    callWebhook('cache.all')->assertOk();
});

it('requires valid signautre', function() {
    updateConfig(['webhook_secret' => 'test_secret']);
    callWebhook('cache.all', [], 'invalid')
        ->assertOk()
        ->assertSee('Unable to validate webhook');
});

it('does not validate when secret is null', function() {
    callWebhook('cache.all', [], 'invalid')
        ->assertOk()
        ->assertDontSee('Unable to validate webhook');
});