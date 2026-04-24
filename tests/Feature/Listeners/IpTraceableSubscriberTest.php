<?php

declare(strict_types=1);

use Illuminate\Auth\Events\Login;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Tests\Stubs\User;
use Xetaio\IpTraceable\Listeners\Subscribers\IpTraceableSubscriber;

function makeSubscriber(string $ip = '192.168.0.1'): IpTraceableSubscriber
{
    $request = Request::create('/', 'GET', server: ['REMOTE_ADDR' => $ip]);

    return new IpTraceableSubscriber($request);
}

describe('IpTraceableSubscriber', function () {
    describe('subscribe', function () {
        it('registers onLogin as a listener for Login::class', function () {
            $dispatcher = Mockery::mock(Dispatcher::class);
            $dispatcher
                ->shouldReceive('listen')
                ->once()
                ->with(Login::class, [IpTraceableSubscriber::class, 'onLogin']);

            makeSubscriber()->subscribe($dispatcher);
        });
    });

    describe('onLogin', function () {
        it('updates last_login_ip on the user when the Login event fires', function () {
            $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

            $event = new Login('web', $user, false);

            makeSubscriber('203.0.113.42')->onLogin($event);

            expect($user->fresh()->last_login_ip)->toBe('203.0.113.42');
        });

        it('updates last_login_date when the Login event fires and the field is configured', function () {
            config(['iptraceable.fields.last_login_date' => 'last_login_date']);

            $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

            $event = new Login('web', $user, false);

            makeSubscriber('1.2.3.4')->onLogin($event);

            $date = $user->fresh()->last_login_date;
            expect($date)->not->toBeNull()
                ->and(\Carbon\Carbon::parse($date)->diffInSeconds(now()))->toBeLessThan(10);
        });

        it('stores null when the request IP address is invalid', function () {
            $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

            $event = new Login('web', $user, false);

            makeSubscriber('not-an-ip')->onLogin($event);

            expect($user->fresh()->last_login_ip)->toBeNull();
        });

        it('triggers onLogin end-to-end via a dispatched Laravel Login event', function () {
            $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

            // Replace the Request in the container so the subscriber registered by the ServiceProvider picks up our IP
            $this->app->instance(
                Request::class,
                Request::create('/', 'GET', server: ['REMOTE_ADDR' => '8.8.8.8'])
            );

            event(new Login('web', $user, false));

            expect($user->fresh()->last_login_ip)->toBe('8.8.8.8');
        });
    });
});
