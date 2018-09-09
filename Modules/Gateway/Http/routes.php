<?php
Route::group(['middleware' => 'gateway', 'prefix' => 'api'], function()
{
    Route::group(['prefix' => 'gateway', 'namespace' => 'Modules\Gateway\Http\Controllers'], function()
    {
        Route::get('/{slug?}', 'GatewayController@index');
    });
});

