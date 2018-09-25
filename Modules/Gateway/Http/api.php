<?php
/**
 * File ${NAME}
 * ${CARET}
 *
 * PHP version 7
 *
 * @category   PHP
 * @package    ${NAMESPACE}
 * @subpackage ${CARET}
 * @author     Sujit Baniya <sujit@intergo.com.cy>
 * @copyright  2018 intergo.com.cy. All rights reserved.
 */

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'gateway', 'namespace' => 'Modules\Gateway\Http\Controllers'], function($api)
    {
        $api->get('/{slug?}', 'Api\ApiGatewayController@index');
    });
});