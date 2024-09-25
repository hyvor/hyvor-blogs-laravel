# hyvor-blogs-laravel

This a Laravel package that provides a simple way to add a blog to your Laravel application using [Hyvor Blogs blogging platform](https://blogs.hyvor.com).

See the tutorial on our Blog. 👇

[**Adding a blog to your Laravel Application**](https://hyvor.com/blog/laravel-blog).

## Installation

```bash
composer require hyvor/hyvor-blogs-laravel
```

## Configuration

```bash
php artisan vendor:publish --provider="Hyvor\HyvorBlogs\HyvorBlogsServiceProvider" --tag="config"
```

This will create a `hyvorblogs.php` file in your `config` directory.

```php
<?php
return [
    
    'blogs' => [
        [
            'subdomain' => '',
            'delivery_api_key' => '',
            'webhook_secret' => null,
            'route' => '/blog',
            'cache_store' => null,
            'middleware' => [],
        ],
    ],
];
```