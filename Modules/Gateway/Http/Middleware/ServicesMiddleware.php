<?php namespace Modules\Gateway\Http\Middleware;

use Closure;

/**
 * File ServicesMiddleware
 *
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    Modules\Gateway\Http\Middleware
 * @subpackage
 * @author     Sujit Baniya <sujit@intergo.com.cy>
 * @copyright  2018 intergo.com.cy. All rights reserved.
 */

class ServicesMiddleware
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