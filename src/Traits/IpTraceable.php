<?php
namespace Xetaio\IpTraceable\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Guard;
use InvalidArgumentException;

trait IpTraceable
{
    /**
     * Update the fields for the user.
     *
     * @param \Illuminate\Contracts\Auth\Guard $auth
     * @param null|string $ip The IP to set.
     *
     * @return bool
     */
    public function updateFields(Guard $auth, $ip = null): bool
    {
        $ip = $this->setIpValue($ip);

        $user = $auth->user();

        if (!$user instanceof Model) {
            return false;
        }
        $user->last_login_ip = $ip;

        $lastLoginDate = config('iptraceable.fields.last_login_date');

        if (!is_null($lastLoginDate)) {
            $user->{$lastLoginDate} = Carbon::now();
        }

        return $user->save();
    }

    /**
     * Validate the IP and return it if it's a valide IP.
     *
     * @param null|int $ip The IP to validate.
     *
     * @return string|null
     */
    protected function setIpValue($ip = null)
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return $ip;
        }

        return null;
    }
}
