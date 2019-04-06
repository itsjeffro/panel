<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('resources', 'ResourceController@index');
    Route::get('resources/{resource}', 'ResourceController@show');
});

Route::get('/{view?}', 'AppController@show')->where('view', '(.*)');