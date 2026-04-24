<h1 align="center">Xetaravel IpTraceable</h1>

<p align="center">
    <a href="https://packagist.org/packages/xetaio/xetaravel-iptraceable"><img src="https://img.shields.io/packagist/v/XetaIO/Xetaravel-IpTraceable.svg?style=flat-square" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/xetaio/xetaravel-iptraceable"><img src="https://img.shields.io/packagist/dt/xetaio/xetaravel-iptraceable.svg?style=flat-square" alt="Total Downloads"></a>
    <a href="https://github.com/XetaIO/Xetaravel-IpTraceable/actions"><img src="https://img.shields.io/github/actions/workflow/status/XetaIO/Xetaravel-IpTraceable/tests.yml?branch=master&style=flat-square&label=tests" alt="Tests"></a>
    <a href="http://laravel.com"><img src="https://img.shields.io/badge/Laravel->=13.0-f4645f.svg?style=flat-square" alt="Laravel 13+"></a>
    <a href="https://github.com/XetaIO/Xetaravel-IpTraceable/blob/master/LICENSE"><img src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square" alt="License"></a>
</p>

A simple Laravel package that tracks the IP address of a user on login. Also handles the `remember_me` token via middleware.

## Requirements

- **PHP** >= 8.3
- **Laravel** >= 13.0

## Installation

```bash
composer require xetaio/xetaravel-iptraceable
```

### Service Provider

Laravel auto-discovers the service provider. If you have disabled auto-discovery, register it manually in `bootstrap/providers.php`:

```php
return [
    // ...
    Xetaio\IpTraceable\Providers\IpTraceableServiceProvider::class,
];
```

### Middleware (remember me support)

The package ships with an optional middleware that updates the IP on requests authenticated via a **remember me** token. Register it in `bootstrap/app.php` **after** `StartSession`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \Xetaio\IpTraceable\Http\Middleware\IpTraceable::class,
    ]);
})
```

> **Note**: The middleware must be registered **after** `Illuminate\Session\Middleware\StartSession`.

### Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="Xetaio\IpTraceable\Providers\IpTraceableServiceProvider" --tag=config
```

Available options in `config/iptraceable.php`:

```php
return [
    'fields' => [
        'last_login_ip' => 'last_login_ip',   // Column name for the IP address
        'last_login_date' => null,               // Set a column name to enable, or null to disable
    ],
];
```

### Database

Add the required columns to your `users` table:

```php
$table->ipAddress('last_login_ip')->nullable();
$table->dateTime('last_login_date')->nullable(); // Optional — enable in config
```

## How it works

| Trigger | Mechanism |
|---|---|
| Standard login | `IpTraceableSubscriber` listens to `Illuminate\Auth\Events\Login` and updates the IP automatically |
| Remember me token | `IpTraceable` middleware checks `viaRemember()` on each request and updates the IP if needed |

## Testing

```bash
composer test
```

## Contributing

Pull requests are welcome.