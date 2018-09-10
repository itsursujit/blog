<?php

namespace App\Http\Middleware;

use Closure;

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
        $gatewayUrl = $gatewayProxies[array_rand($gatewayProxies)];
        $queryString = $request->getQueryString();
        $gatewayUrl .= '?' . $queryString;
        $headers = $request->headers->all();
        $client = new \GuzzleHttp\Client([
            'verify' => false,
            'timeout' => 5, // Response timeout
            'connect_timeout' => 5, // Connection timeout
            'peer' => false
        ]);
        $response = $client->request($method, $gatewayUrl, [
            'json' => $data,
            'headers' => $headers,
        ]);

        $response = $response->getBody()->getContents();
        dd($response);
    }
}
