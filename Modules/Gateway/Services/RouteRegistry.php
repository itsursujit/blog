<?php namespace Modules\Gateway\Services;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Modules\Gateway\Contracts\RouteContract;
use Modules\Gateway\Http\Controllers\GatewayController;
use Ramsey\Uuid\Uuid;

/**
 * File RouteRegistry
 *
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    Modules\Gateway\Services
 * @subpackage
 * @author     Sujit Baniya <sujit@intergo.com.cy>
 * @copyright  2018 Kyvio.com. All rights reserved.
 */
class RouteRegistry
{
    /**
     * @var array
     */
    protected $routes = [];

    /**
     * RouteRegistry constructor.
     */
    public function __construct()
    {
        $this->parseConfigRoutes();
    }

    /**
     * @param RouteContract $route
     */
    public function addRoute(RouteContract $route)
    {
        $this->routes[] = $route;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->routes);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRoutes()
    {
        return collect($this->routes);
    }

    /**
     * @param string $id
     * @return RouteContract
     */
    public function getRoute($id)
    {
        return collect($this->routes)->first(function (Route $route) use ($id) {
            return $route->getId() == $id;
        });
    }

    /**
     * @param Application $app
     */
    public function bind(Application $app)
    {
        $this->getRoutes()->each(function (Route $route) use ($app) {
            $method = strtolower($route->getMethod());

            $middleware = [ 'helper:' . $route->getId() ];
            if (! $route->isPublic()) $middleware[] = 'auth';

            $app->router->{$method}($route->getPath(), [
                'uses' => GatewayController::class . '@' . $method,
                'middleware' => $middleware
            ]);
        });
    }

    /**
     * @return $this
     */
    private function parseConfigRoutes()
    {
        $config = config('gateway');
        if (empty($config)) return $this;

        $this->parseRoutes($config['routes']);

        return $this;
    }

    /**
     * @param string $filename
     *
     * @return RouteRegistry
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public static function initFromFile($filename = null)
    {
        $registry = new self;
        $filename = $filename ?: 'routes.json';
        if (! Storage::disk('local')->exists($filename)) return $registry;
        $routes = json_decode(Storage::disk('local')->get($filename), true);
        if ($routes === null) return $registry;
        // We want to re-parse config routes to allow route overwriting
        return $registry->parseRoutes($routes['paths'])->parseConfigRoutes();
    }

    public static function initFromObjectArray($array)
    {
        $registry = new self;
        if ($array === null) return $registry;
        // We want to re-parse config routes to allow route overwriting
        return $registry->parseRoutes($array)->parseConfigRoutes();
    }

    /**
     * @param array $routes
     * @return $this
     */
    private function parseRoutes(array $serviceRoutes)
    {
        collect($serviceRoutes)->each(function ($routes, $key) {
            collect($routes)->each(function (\Illuminate\Routing\Route $route, $key) {
                //dd($route);
                $routeDetails = [
                    'id' => (string) Uuid::uuid4(),
                    'method' => $route->methods()[0],
                    'path' => $route->uri,
                    'public' => true,
                    'raw' => false
                ];

                $thisRoute = new Route($routeDetails);
                collect($routeDetails)->each(function ($action, $alias) use ($thisRoute) {
                    $thisRoute->addAction(new Action(array_merge($action, ['alias' => $alias])));
                });

                $this->addRoute($thisRoute);
            });
        });

        return $this;
    }
}