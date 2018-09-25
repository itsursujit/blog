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

Route::group(['middleware' => ['web'] ,'prefix' => 'gateway', 'namespace' => 'Modules\Gateway\Http\Controllers'], function()
{
    Route::get('/{slug?}', 'GatewayController@index');
});