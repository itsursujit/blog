<?php namespace Modules\Gateway\Http\Requests;
use Modules\Gateway\Contracts\RouteContract;

/**
 * File Request
 *
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    Modules\Gateway\Http\Requests
 * @subpackage
 * @author     Sujit Baniya <sujit@intergo.com.cy>
 * @copyright  2018 Kyvio.com. All rights reserved.
 */
class Request extends \Illuminate\Http\Request
{
    /**
     * @var RouteContract
     */
    protected $currentRoute;

    /**
     * @param RouteContract $route
     * @return $this
     */
    public function attachRoute(RouteContract $route)
    {
        $this->currentRoute = $route;

        return $this;
    }

    /**
     * @return RouteContract
     */
    public function getRoute()
    {
        return $this->currentRoute;
    }

    /**
     * @return array
     */
    public function getRouteParams()
    {
        $route = call_user_func($this->getRouteResolver());

        return $route ? $route[2] : [];
    }
}