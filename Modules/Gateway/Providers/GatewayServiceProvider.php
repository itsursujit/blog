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
        /*$router = $this->app['router'];
        $router->pushMiddlewareToGroup('gateway', HelperMiddleware::class);*/
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
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
}
