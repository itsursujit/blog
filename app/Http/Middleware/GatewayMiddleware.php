<?php

namespace App\Http\Middleware;

use App\Service\Curl;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class GatewayMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $gatewayProxies = config('sms.gateway_proxies');
        $applicationType = config('sms.application_type');
        $data = $request->all();
        $method = $request->getMethod();
        $uri = $request->getRequestUri();
        $queryString = $request->getQueryString();
        $headers = $request->headers->all();
        $url = url();
        switch($applicationType)
        {
            case 'app':
                if(!$gatewayProxies)
                {
                    return $next($request);
                }
                $url = $gatewayProxies[array_rand($gatewayProxies)]  . $uri;
                break;
            case 'gateway':
                $service = $this->getRequestServiceProxies($request);
                $serviceProxy = $service['proxies'];
                if(empty($serviceProxy))
                {
                    return $next($request);
                }
                $url = $serviceProxy[array_rand($serviceProxy)]  . $uri;
                break;
            case 'service':
                return $next($request);
                break;
            case 'auth':
                return $next($request);
                break;
            default:
                return abort(404, 'Page Not found');
                break;
        }
        $url .= empty($queryString) ?'':'?' . $queryString;

        $response = Curl::request($url, $method, $data);
        return $response;
    }

    function getRequestServiceProxies(Request $request)
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
