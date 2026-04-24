<?php

declare(strict_types=1);

namespace Xetaio\IpTraceable\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

trait IpTraceable
{
    /**
     * Update the fields for the user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user The user to update the fields for.
     * @param null|string $ip The IP to set.
     *
     * @return bool
     */
    public function updateFields(Authenticatable $user, ?string $ip = null): bool
    {
        $ip = $this->setIpValue($ip);

        if (!$user instanceof Model) {
            return false;
        }
        $user->{config('iptraceable.fields.last_login_ip')} = $ip;

        $lastLoginDate = config('iptraceable.fields.last_login_date');

        if (!is_null($lastLoginDate)) {
            $user->{$lastLoginDate} = now();
        }

        return $user->save();
    }

    /**
     * Validate the IP and return it if it's a valid IP.
     *
     * @param string|null $ip The IP to validate.
     *
     * @return string|null
     */
    protected function setIpValue(?string $ip = null): ?string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
            return $ip;
        }

        return null;
    }
}
