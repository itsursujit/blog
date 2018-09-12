<?php

Route::group(['prefix' => 'api/sms', 'namespace' => 'Modules\SMS\Http\Controllers'], function()
{
    Route::get('/', 'SMSController@index');
});
