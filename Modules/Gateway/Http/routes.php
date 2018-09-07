<?php

Route::group(['middleware' => 'web', 'prefix' => 'gateway', 'namespace' => 'Modules\Gateway\Http\Controllers'], function()
{
    Route::get('/', 'GatewayController@index');
});
