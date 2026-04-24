<?php
namespace Xetaio\IpTraceable\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Xetaio\IpTraceable\Listeners\Subscribers\IpTraceableSubscriber;

class IpTraceableServiceProvider extends ServiceProvider
{
    /**
     * Register the application's event listeners and publish the config file.
     *
     * @return void
     */
    public function boot(): void
    {
        Event::subscribe(IpTraceableSubscriber::class);

        $this->publishes([
            __DIR__ . '/../../config/iptraceable.php' => config_path('iptraceable.php'),
        ], 'config');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/iptraceable.php', 'iptraceable');
    }
}
