<?php

declare(strict_types=1);

namespace Xetaio\IpTraceable\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Xetaio\IpTraceable\Traits\IpTraceable as IpTraceableTrait;

class IpTraceable
{
    use IpTraceableTrait;

    /**
     * Create a new filter instance.
     *
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(protected Guard $auth)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if ($this->auth->check() && $this->auth->viaRemember()) {
            $ip = $request->getClientIp();

            $this->updateFields($this->auth->user(), $ip);
        }

        return $next($request);
    }
}
