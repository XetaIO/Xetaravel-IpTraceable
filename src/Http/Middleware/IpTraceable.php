<?php
namespace Xetaio\IpTraceable\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Xetaio\IpTraceable\Traits\IpTraceable as IpTraceableTrait;

class IpTraceable
{
    use IpTraceableTrait;

    /**
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     *
     * @return Closure
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->check() && $this->auth->viaRemember()) {
            $ip = $request->getClientIp();

            $this->updateFields($this->auth, $ip);
        }

        return $next($request);
    }
}
