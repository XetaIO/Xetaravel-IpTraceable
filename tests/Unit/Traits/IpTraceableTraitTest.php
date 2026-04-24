<?php

declare(strict_types=1);

use Tests\Stubs\User;
use Xetaio\IpTraceable\Traits\IpTraceable;

// Concrete class that exposes the trait for testing
$traitUser = new class
{
    use IpTraceable;
};

describe('updateFields', function () use ($traitUser) {
    it('updates last_login_ip with a valid IPv4 address', function () use ($traitUser) {
        $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

        $result = $traitUser->updateFields($user, '192.168.1.1');

        expect($result)->toBeTrue()
            ->and($user->fresh()->last_login_ip)->toBe('192.168.1.1');
    });

    it('updates last_login_ip with a valid IPv6 address', function () use ($traitUser) {
        $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

        $result = $traitUser->updateFields($user, '2001:0db8:85a3:0000:0000:8a2e:0370:7334');

        expect($result)->toBeTrue()
            ->and($user->fresh()->last_login_ip)->toBe('2001:0db8:85a3:0000:0000:8a2e:0370:7334');
    });

    it('stores null when the IP address is invalid', function () use ($traitUser) {
        $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

        $result = $traitUser->updateFields($user, 'not-an-ip');

        expect($result)->toBeTrue()
            ->and($user->fresh()->last_login_ip)->toBeNull();
    });

    it('stores null when no IP address is provided', function () use ($traitUser) {
        $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

        $result = $traitUser->updateFields($user);

        expect($result)->toBeTrue()
            ->and($user->fresh()->last_login_ip)->toBeNull();
    });

    it('returns false when the user is not an Eloquent Model instance', function () use ($traitUser) {
        $nonModel = new class implements \Illuminate\Contracts\Auth\Authenticatable {
            public function getAuthIdentifierName(): string { return 'id'; }
            public function getAuthIdentifier(): mixed { return 1; }
            public function getAuthPasswordName(): string { return 'password'; }
            public function getAuthPassword(): string { return 'secret'; }
            public function getRememberToken(): string { return ''; }
            public function setRememberToken($value): void {}
            public function getRememberTokenName(): string { return 'remember_token'; }
        };

        $result = $traitUser->updateFields($nonModel, '192.168.1.1');

        expect($result)->toBeFalse();
    });

    it('updates last_login_date when the field is configured', function () use ($traitUser) {
        config(['iptraceable.fields.last_login_date' => 'last_login_date']);

        $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

        $traitUser->updateFields($user, '10.0.0.1');

        $date = $user->fresh()->last_login_date;

        expect($date)->not->toBeNull()
            ->and(\Carbon\Carbon::parse($date)->diffInSeconds(now()))->toBeLessThan(10);
    });

    it('does not update last_login_date when the field is null in config', function () use ($traitUser) {
        config(['iptraceable.fields.last_login_date' => null]);

        $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

        $traitUser->updateFields($user, '10.0.0.1');

        expect($user->fresh()->last_login_date)->toBeNull();
    });

    it('uses the IP field name defined in config', function () use ($traitUser) {
        config(['iptraceable.fields.last_login_ip' => 'last_login_ip']);

        $user = User::create(['email' => 'test@example.com', 'password' => 'secret']);

        $traitUser->updateFields($user, '172.16.0.1');

        expect($user->fresh()->last_login_ip)->toBe('172.16.0.1');
    });
});
