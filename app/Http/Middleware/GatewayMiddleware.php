<?php

namespace App\Http\Middleware;

use App\Service\Curl;
use Closure;
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
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle($request, Closure $next)
    {
        $gatewayProxies = config('app.gateway_proxies');
        if(!$gatewayProxies)
        {
            return $next($request);
        }
        $data = $request->all();
        $method = $request->getMethod();
        $uri = $request->getRequestUri();
        $gatewayUrl = $gatewayProxies[array_rand($gatewayProxies)]  . $uri;
        $queryString = $request->getQueryString();
        $gatewayUrl .= empty($queryString) ?'':'?' . $queryString;
        $headers = $request->headers->all();

        $response = Curl::request($gatewayUrl, $method, $data);
        return Response::make($response);
    }
}
