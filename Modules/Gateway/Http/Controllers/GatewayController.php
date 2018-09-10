<?php

namespace Modules\Gateway\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use Modules\Gateway\Contracts\PresenterContract;
use Modules\Gateway\Exceptions\DataFormatException;
use Modules\Gateway\Exceptions\NotImplementedException;
use Modules\Gateway\Http\Middleware\HelperMiddleware;
use Modules\Gateway\Http\Requests\Request;
use \Illuminate\Support\Facades\Route as OrigRoute;
use Modules\Gateway\Services\RestClient;
use Nwidart\Modules\Facades\Module;

class GatewayController extends Controller
{

    /**
     * @var array ActionContract
     */
    protected $actions;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var PresenterContract
     */
    protected $presenter;

    /**
     * GatewayController constructor.
     *
     * @param Request $request
     *
     * @throws \Modules\Gateway\Exceptions\DataFormatException
     */
    public function __construct(Request $request)
    {
        /*$routes = collect(Route::getRoutes())->filter(function ($item) {
            return isset($item->action['namespace']) && starts_with($item->action['namespace'], "Modules\\Gateway");
        })->values()->all();
        $routesJson = [];
        foreach($routes as $route)
        {
            $routesJson[] = [
                'uri' => $route->uri,
                'method' => $route->methods[0],
                'parameters' => json_encode($route->parameterNames)
            ];
        }*/

        return;
        if (empty($request->getRoute())) throw new DataFormatException('Unable to find original URI pattern');

        $this->config = $request
            ->getRoute()
            ->getConfig();

        $this->actions = $request
            ->getRoute()
            ->getActions()
            ->groupBy(function ($action) {
                return $action->getSequence();
            })
            ->sortBy(function ($batch, $key) {
                return intval($key);
            });

        $this->presenter = $request
            ->getRoute()
            ->getPresenter();
    }

    /**
     * @param Request    $request
     * @param RestClient $client
     *
     * @return Response
     * @throws \Modules\Gateway\Exceptions\NotImplementedException
     */
    public function get(Request $request, RestClient $client)
    {
        if (! $request->getRoute()->isAggregate()) return $this->simpleRequest($request, $client);

        $parametersJar = array_merge($request->getRouteParams(), ['query_string' => $request->getQueryString()]);

        $output = $this->actions->reduce(function($carry, $batch) use (&$parametersJar, $client) {
            $responses = $client->asyncRequest($batch, $parametersJar);
            $parametersJar = array_merge($parametersJar, $responses->exportParameters());

            return array_merge($carry, $responses->getResponses()->toArray());
        }, []);

        return $this->presenter->format($this->rearrangeKeys($output), 200);
    }

    /**
     * @param array $output
     * @return array
     */
    private function rearrangeKeys(array $output)
    {
        return collect(array_keys($output))->reduce(function($carry, $alias) use ($output) {
            $key = $this->config['actions'][$alias]['output_key'] ?? $alias;

            if ($key === false) return $carry;

            $data = isset($this->config['actions'][$alias]['input_key']) ? $output[$alias][$this->config['actions'][$alias]['input_key']] : $output[$alias];

            if (empty($key)) {
                return array_merge($carry, $data);
            }

            if (is_string($key)) {
                array_set($carry, $key, $data);
            }

            if (is_array($key)) {
                collect($key)->each(function($outputKey, $property) use (&$data, &$carry, $key) {
                    if ($property == '*') {
                        array_set($carry, $outputKey, $data);
                        return;
                    }

                    if (isset($data[$property])) {
                        array_set($carry, $outputKey, $data[$property]);
                        unset($data[$property]);
                    }
                });
            }

            return $carry;
        }, []);
    }

    /**
     * @param Request    $request
     * @param RestClient $client
     *
     * @return Response
     * @throws \Modules\Gateway\Exceptions\NotImplementedException
     */
    public function delete(Request $request, RestClient $client)
    {
        return $this->simpleRequest($request, $client);
    }

    /**
     * @param Request    $request
     * @param RestClient $client
     *
     * @return Response
     * @throws \Modules\Gateway\Exceptions\NotImplementedException
     */
    public function post(Request $request, RestClient $client)
    {
        return $this->simpleRequest($request, $client);
    }

    /**
     * @param Request    $request
     * @param RestClient $client
     *
     * @return Response
     * @throws \Modules\Gateway\Exceptions\NotImplementedException
     */
    public function put(Request $request, RestClient $client)
    {
        return $this->simpleRequest($request, $client);
    }

    /**
     * @param Request $request
     * @param RestClient $client
     * @return Response
     * @throws NotImplementedException
     */
    private function simpleRequest(Request $request, RestClient $client)
    {
        if ($request->getRoute()->isAggregate()) throw new NotImplementedException('Aggregate ' . strtoupper($request->method()) . 's are not implemented yet');

        $client->setBody($request->getContent());

        if (count($request->allFiles()) !== 0) {
            $client->setFiles($request->allFiles());
        }

        $parametersJar = array_merge($request->getRouteParams(), ['query_string' => $request->getQueryString()]);
        $response = $client->syncRequest($this->actions->first()->first(), $parametersJar);

        return $this->presenter->format((string)$response->getBody(), $response->getStatusCode());
    }

    function getModuleRoutes($moduleName, $baseModulesPath = 'Modules')
    {
        return collect(OrigRoute::getRoutes())->filter(function ($item) use ($baseModulesPath, $moduleName) {
            return isset($item->action['namespace']) && starts_with($item->action['namespace'], "{$baseModulesPath}\\{$moduleName}");
        })->values()->all();
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('gateway::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('gateway::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     *
     * @return void
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('gateway::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('gateway::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     *
     * @return void
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    public function destroy()
    {
    }
}
