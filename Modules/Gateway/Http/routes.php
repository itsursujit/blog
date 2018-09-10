<?php
Route::group(['prefix' => 'api/gateway', 'namespace' => 'Modules\Gateway\Http\Controllers'], function()
{
    Route::get('/{slug?}', 'GatewayController@index');
});

