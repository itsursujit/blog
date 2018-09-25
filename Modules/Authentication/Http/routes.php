<?php

use Modules\Authentication\Http\Controllers\Auth\LoginController;

Route::get('/login', LoginController::class . '@showLoginForm');
Route::group(['middleware' => 'web', 'prefix' => 'authentication', 'namespace' => 'Modules\Authentication\Http\Controllers'], function()
{
    Route::get('/login', LoginController::class . '@showLoginForm')->name('login');
});
