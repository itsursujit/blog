<?php

namespace App\Http\Middleware;

use App\Service\Curl;
use App\Service\MultiCurl;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Dingo\Api\Dispatcher;

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
        if(\request('type'))
        {
            $applicationType = \request('type');
        }
        else
        {
            $applicationType = config('sms.application_type');
        }
        $data = $request->all();
        $method = $request->getMethod();
        $uri = $request->getRequestUri();
        $queryString = $request->getQueryString();
        $get_first = function($x){
            return $x[0];
        };
        $headers = array_map($get_first, $request->headers->all());

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
        $curl = new Curl();
        $response = $curl->request($url, $method, $data, $headers)->exec();
        return $response;

        $getUrl = 'https://kantipur.ekantipur.com';
        $startTime = microtime(true);
        $c = new Curl();
        $c->get($getUrl, null,$headers);
        $response = $c->exec();
        /*if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            var_dump($response->getBody());
        }*/

        //Reuse $c
        $c->get($getUrl);
        $response = $c->exec();
        /*if ($response->hasError()) {
            //Fail
            var_dump($response->getError());
        } else {
            //Success
            var_dump($response->getBody());
        }*/
        $endTime = microtime(true);
        echo $endTime - $startTime . "\n";
//Multi http request
        $startTime = microtime(true);
        $getUrl = 'https://kantipur.ekantipur.com';
        $c2 = new Curl();
        $c2->get($getUrl);

        $c3 = new Curl();
        $c3->get($getUrl);

        $mc = new MultiCurl();

        $mc->addCurls([$c2, $c3]);
        $allSuccess = $mc->exec();
        /*if ($allSuccess) {
            //All success
            var_dump($c2->getResponse()->getBody(), $c3->getResponse()->getBody());
        } else {
            //Some curls failed
            var_dump($c2->getResponse()->getError(), $c3->getResponse()->getError());
        }*/
        $endTime = microtime(true);
        echo $endTime - $startTime . "\n";
        $startTime = microtime(true);
        /*if ($allSuccess) {
            //All success
            var_dump($c4->getResponse()->getBody(), $c5->getResponse()->getBody());
        } else {
            //Some curls failed
            var_dump($c4->getResponse()->getError(), $c5->getResponse()->getError());
        }*/
        dd(1);
    }

    function getRequestServiceProxies(Request $request)
    {

        $method = $request->getMethod();
        $uri = $request->getRequestUri();
        return $this->getServiceRoute($uri, $method);

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
