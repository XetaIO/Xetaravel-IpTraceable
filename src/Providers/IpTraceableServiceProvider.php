<?php
namespace Xetaio\IpTraceable\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class IpTraceableServiceProvider extends ServiceProvider
{

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        \Xetaravel\Listeners\Subscribers\IpTraceableSubscriber::class,
    ];

    /**
     * Register the application's event listeners and publish the config file.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }

        $this->publishes([
            __DIR__ . '/../../config/iptraceable.php' => config_path('iptraceable.php'),
        ], 'config');
    }
}
