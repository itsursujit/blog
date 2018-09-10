<?php

namespace Modules\Gateway\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Gateway\Contracts\ServiceRegistryContract;
use Modules\Gateway\Http\Middleware\HelperMiddleware;
use Modules\Gateway\Http\Requests\Request;
use Modules\Gateway\Services\DNSRegistry;
use Modules\Gateway\Services\RouteRegistry;
use Nwidart\Modules\Facades\Module;
use \Illuminate\Support\Facades\Route as OrigRoute;

class GatewayServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('gateway', HelperMiddleware::class);
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerRoutes();
        $this->registerFactories();
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        //also you can register your route level middlewares using the router
        //$router->pushMiddlewareToGroup('gateway', HelperMiddleware::class);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('gateway.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'gateway'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/gateway');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/gateway';
        }, Config::get('view.paths')), [$sourcePath]), 'gateway');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/gateway');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'gateway');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'gateway');
        }
    }

    /**
     * Register an additional directory of factories.
     * 
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    function getModuleRoutes($moduleName, $baseModulesPath = 'Modules')
    {
        return collect(OrigRoute::getRoutes())->filter(function ($item) use ($baseModulesPath, $moduleName) {
            return isset($item->action['namespace']) && starts_with($item->action['namespace'], "{$baseModulesPath}\\{$moduleName}");
        })->values()->all();
    }

    /**
     * @return void
     */
    protected function registerRoutes()
    {
        $routes = [];
        /*Module::toCollection()->each(function ($module) use (&$routes) {
            if($module->name != 'Gateway')
            {
                $routes[$module->getLowerName()] = $this->getModuleRoutes($module->name);
            }
        });*/

        /*$this->app->singleton(RouteRegistry::class, function() use($routes) {
            return RouteRegistry::initFromObjectArray($routes);
        });*/

        $this->app->singleton(Request::class, function () {
            return $this->prepareRequest(Request::capture());
        });

        $this->app->bind(ServiceRegistryContract::class, DNSRegistry::class);

        $this->app->singleton(Client::class, function() {
            return new Client([
                'timeout' => 5,
                'connect_timeout' => 5
            ]);
        });

        $this->app->alias(Request::class, 'request');
        $registry = $this->app->make(RouteRegistry::class);

        if ($registry->isEmpty()) {
            Log::info('Not adding any service routes - route file is missing');
            return;
        }

        $registry->bind(app());
    }



    /**
     * Prepare the given request instance for use with the application.
     *
     * @param   Request $request
     * @return  Request
     */
    protected function prepareRequest(Request $request)
    {
        $request->setUserResolver(function () {
            return $this->app->make('auth')->user();
        })->setRouteResolver(function () {
            return $this->app->currentRoute;
        })->setTrustedProxies([
            '10.7.0.0/16', // Docker Cloud
            '103.21.244.0/22', // Cloud Flare
            '103.22.200.0/22',
            '103.31.4.0/22',
            '104.16.0.0/12',
            '108.162.192.0/18',
            '131.0.72.0/22',
            '141.101.64.0/18',
            '162.158.0.0/15',
            '172.64.0.0/13',
            '173.245.48.0/20',
            '188.114.96.0/20',
            '190.93.240.0/20',
            '197.234.240.0/22',
            '198.41.128.0/17',
            '199.27.128.0/21',
            '172.31.0.0/16', // Rancher
            '10.42.0.0/16' // Rancher
        ], \Illuminate\Http\Request::HEADER_X_FORWARDED_ALL);

        return $request;
    }
}
