<?php

namespace Hyvor\HyvorBlogs\Tests;

use Hyvor\HyvorBlogs\HyvorBlogsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            HyvorBlogsServiceProvider::class,
        ];
    }
}
