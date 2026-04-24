<?php

declare(strict_types=1);

namespace Xetaio\IpTraceable\Listeners\Subscribers;

use Illuminate\Auth\Events\Login;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Xetaio\IpTraceable\Traits\IpTraceable as IpTraceableTrait;

class IpTraceableSubscriber
{
    use IpTraceableTrait;

    /**
     * Create the event listener.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events The event dispatcher to register the listeners with.
     *
     * @return void
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(Login::class, [self::class, 'onLogin']);
    }

    /**
     * Listener for the login event.
     *
     * @param \Illuminate\Auth\Events\Login $event The event that was fired.
     *
     * @return void
     */
    public function onLogin(Login $event): void
    {
        $this->updateFields($event->user, $this->request->getClientIp());
    }
}
