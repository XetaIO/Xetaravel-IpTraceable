<?php
namespace Xetaio\IpTraceable\Listeners\Subscribers;

use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Xetaio\IpTraceable\Traits\IpTraceable as IpTraceableTrait;

class IpTraceableSubscriber
{
    use IpTraceableTrait;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Create the event listener.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Guard $auth
     */
    public function __construct(Request $request, Guard $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Illuminate\Events\Dispatcher $events
     *
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            'Xetaravel\Listeners\Subscribers\IpTraceableSubscriber@onLogin'
        );
    }

    /**
     * Listener for the login event.
     *
     * @param \Illuminate\Auth\Events\Login $event The event that was fired.
     *
     * @return bool
     */
    public function onLogin(Login $event)
    {
        $ip = $this->request->getClientIp();

        $this->updateFields($this->auth, $ip);
    }
}
