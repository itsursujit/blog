<?php

use Dingo\Api\Dispatcher;
use Dingo\Api\Routing\Router;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
$api = app(Router::class);
$dispatcher = app(Dispatcher::class);
$api->version('v1'/*, ['middleware' => 'api.auth']*/, function ($api) use($dispatcher) {
    $api->get('/test', function() {
        return "Hello world";
    });
});
