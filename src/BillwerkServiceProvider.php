<?php

declare(strict_types=1);

namespace Omnipay\Billwerk;

use Illuminate\Support\ServiceProvider;
use Omnipay\Omnipay;

/**
 * Billwerk Service Provider for Laravel
 */
class BillwerkServiceProvider extends ServiceProvider
{
    /**
     * Register services
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('omnipay.billwerk', function ($app) {
            $gateway = Omnipay::create('Billwerk');

            $config = $app['config']->get('services.billwerk', []);

            if (!empty($config)) {
                $gateway->initialize($config);
            }

            return $gateway;
        });
    }

    /**
     * Bootstrap services
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/billwerk.php' => config_path('billwerk.php'),
        ], 'config');
    }

    /**
     * Get the services provided by the provider
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return ['omnipay.billwerk'];
    }
}
