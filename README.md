> <h1 align="center">Xetaravel IpTraceable</h1>
>
> |Stable Version|Downloads|Laravel|License|
> |:-------:|:------:|:-------:|:-------:|
> |[![Latest Stable Version](https://img.shields.io/packagist/v/XetaIO/Xetaravel-IpTraceable.svg?style=flat-square)](https://packagist.org/packages/xetaio/xetaravel-iptraceable)|[![Total Downloads](https://img.shields.io/packagist/dt/xetaio/xetaravel-iptraceable.svg?style=flat-square)](https://packagist.org/packages/xetaio/xetaravel-iptraceable)|[![Laravel 5.6](https://img.shields.io/badge/Laravel->=7.0-f4645f.svg?style=flat-square)](http://laravel.com)|[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/XetaIO/Xetaravel-IpTraceable/blob/master/LICENSE)|
>
> A simple package that update an IP field when the user login into the application. Also work with the `remember_me` token.
>
> ## Requirement
> ![PHP](https://img.shields.io/badge/PHP->=7.2.5-brightgreen.svg?style=flat-square)
>
> ## Installation
>
> ```
> composer require xetaio/xetaravel-iptraceable
> ```
>
> #### ServiceProviders
> Import the `IpTraceableServiceProvider` in your `config/app.php`:
> ```php
> 'providers' => [
>   //...
>   Xetaio\IpTraceable\Providers\IpTraceableServiceProvider::class,
>   //...
> ]
> ```
>
> #### Middleware
> Import the `IpTraceable` middleware in your `app/Http/Kernel.php` in the `web` part:
> ```php
> protected $middlewareGroups = [
>    'web' => [
>        //...
>        \Illuminate\Session\Middleware\StartSession::class,
>        \Xetaio\IpTraceable\Http\Middleware\IpTraceable::class,
>        //...
>    ],
>    //...
> ];
> ```
> **Note** : It's **very important** to import the middleware **after** the `Illuminate\Session\Middleware\StartSession` middleware, since this package use the session.
>
> #### Config file
> Publish the package config file to your application :
> ```
> php artisan vendor:publish --provider="Xetaio\IpTraceable\Providers\IpTraceableServiceProvider" --tag=config
> ```
>
> #### Database
> Create 2 fields `last_login_ip` and `last_login_date` (optional) in your database :
> ```php
> // Must be nullable
> $table->ipAddress('last_login_ip')->nullable();
> $table->dateTime('last_login_date')->nullable(); // (optional) Disabled by default
> ```
> The fields name can be changed in the configuration file.
> ## Contribute
> If you want to contribute to the project by adding new features or just fix a bug, feel free to do a PR.
