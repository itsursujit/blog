<?php

namespace App\Http\Middleware;

use App\Service\Curl;
use Closure;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ServicesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $gatewayProxies = config('sms.application_type');
        if(!in_array(config('sms.application_type'), ['app', 'gateway']))
        {
            return abort(404, 'Gateway not found');
        }
        $service = $this->getRequestServiceProxies($request);
        $serviceProxy = $service['proxies'];
        if(empty($serviceProxy))
        {
            return abort(404, "Proxy not found");
        }
        $data = $request->all();
        $method = $request->getMethod();
        $uri = $request->getRequestUri();
        $serviceUrl = $serviceProxy[array_rand($serviceProxy)]  . $uri;
        $queryString = $request->getQueryString();
        $serviceUrl .= empty($queryString) ?'':'?' . $queryString;
        $headers = $request->headers->all();
        $response = Curl::request($serviceUrl, $method, $data);
        return Response::make($response);
    }

    function getRequestServiceProxies($request)
    {

        $method = $request->getMethod();
        $uri = $request->getRequestUri();
        return $this->getServiceRoute($uri, $method);

        /*$gatewayUrl = $gatewayProxies[array_rand($gatewayProxies)]  . $uri;
        $queryString = $request->getQueryString();
        $gatewayUrl .= empty($queryString) ?'':'?' . $queryString;
        $headers = $request->headers->all();
        $response = Curl::request($gatewayUrl, $method, $data);
        return Response::make($response);*/

    }

    function getServiceRoute($uri, $method)
    {
        $services = json_decode(file_get_contents(storage_path() . '/app/service-routes.json'), true);
        foreach($services as $service)
        {
            $route = $this->getRoute($service['routes'], $uri, $method);
            if(!empty($route))
            {
                return ["service" => $service['alias'], "proxies" => $service['proxies']];
            }
        }
        return null;
    }

    function getRoute($routes, $uri, $method)
    {
        foreach($routes as $route)
        {
            if(($route['uri'] === $uri || $uri === '/' . $route['uri']) && $route['method'] === $method)
            {
                return $route;
            }
        }
        return null;
    }

}
