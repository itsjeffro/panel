<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('resources', 'ResourcesController@index');

    Route::get('resources/{resource}/fields', 'ResourceFieldController@show');

    Route::get('resources/{resource}', 'ResourceController@index');
    Route::get('resources/{resource}/{id}', 'ResourceController@show');
    Route::put('resources/{resource}/{id}', 'ResourceController@update');
    Route::post('resources/{resource}', 'ResourceController@store');
});

Route::get('/{view?}', 'AppController@show')->where('view', '(.*)');