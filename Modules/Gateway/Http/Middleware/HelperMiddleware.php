<?php namespace Modules\Gateway\Http\Middleware;
use Closure;
use Modules\Gateway\Http\Requests\Request;
use Modules\Gateway\Services\RouteRegistry;

/**
 * File HelperMiddleware
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
class HelperMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure  $next
     * @param string $id
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $id)
    {
        $request->attachRoute(
            app()->make(RouteRegistry::class)->getRoute($id)
        );

        return $next($request);
    }

}