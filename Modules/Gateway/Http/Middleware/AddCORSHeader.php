<?php namespace Modules\Gateway\Http\Middleware;
use Closure;
use Modules\Gateway\Http\Requests\Request;

/**
 * File AddCORSHeader
 *
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    Modules\Gateway\Http\Middleware
 * @subpackage
 * @author     Sujit Baniya <sujit@intergo.com.cy>
 * @copyright  2018 Kyvio.com. All rights reserved.
 */
class AddCORSHeader
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /**
         * @var Response $response
         */
        $response = $next($request);
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

}