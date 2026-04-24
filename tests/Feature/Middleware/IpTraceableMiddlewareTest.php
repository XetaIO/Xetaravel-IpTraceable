<?php

declare(strict_types=1);

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Tests\Stubs\User;
use Xetaio\IpTraceable\Http\Middleware\IpTraceable;

function makeMiddleware(Guard $guard): IpTraceable
{
    return new IpTraceable($guard);
}

function makeRequest(string $ip = '192.168.0.1'): Request
{
    return Request::create('/', 'GET', server: ['REMOTE_ADDR' => $ip]);
}

describe('IpTraceable Middleware', function () {
    it('always passes the request to the next middleware', function () {
        $guard = Mockery::mock(Guard::class);
        $guard->shouldReceive('check')->andReturn(false);

        $nextCalled = false;
        $next = function ($request) use (&$nextCalled) {
            $nextCalled = true;

            return response('ok');
        };

        makeMiddleware($guard)->handle(makeRequest(), $next);

        expect($nextCalled)->toBeTrue();
    });

    it('does not update the user when not authenticated', function () {
        $guard = Mockery::mock(Guard::class);
        $guard->shouldReceive('check')->andReturn(false);
        $guard->shouldNotReceive('viaRemember');
        $guard->shouldNotReceive('user');

        $next = fn ($req) => response('ok');

        makeMiddleware($guard)->handle(makeRequest(), $next);
    });

    it('does not update the user when authenticated without a remember token', function () {
        $guard = Mockery::mock(Guard::class);
        $guard->shouldReceive('check')->andReturn(true);
        $guard->shouldReceive('viaRemember')->andReturn(false);
        $guard->shouldNotReceive('user');

        $next = fn ($req) => response('ok');

        makeMiddleware($guard)->handle(makeRequest(), $next);
    });

    it('updates last_login_ip when authenticated via remember token', function () {
        $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

        $guard = Mockery::mock(Guard::class);
        $guard->shouldReceive('check')->andReturn(true);
        $guard->shouldReceive('viaRemember')->andReturn(true);
        $guard->shouldReceive('user')->andReturn($user);

        $next = fn ($req) => response('ok');

        makeMiddleware($guard)->handle(makeRequest('10.10.10.10'), $next);

        expect($user->fresh()->last_login_ip)->toBe('10.10.10.10');
    });

    it('stores null when the IP address is invalid in the remember token flow', function () {
        $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

        $guard = Mockery::mock(Guard::class);
        $guard->shouldReceive('check')->andReturn(true);
        $guard->shouldReceive('viaRemember')->andReturn(true);
        $guard->shouldReceive('user')->andReturn($user);

        $next = fn ($req) => response('ok');

        // Simuler un proxy qui renvoie une IP invalide
        $request = Request::create('/', 'GET', server: ['REMOTE_ADDR' => 'invalid-ip']);

        makeMiddleware($guard)->handle($request, $next);

        expect($user->fresh()->last_login_ip)->toBeNull();
    });
});
