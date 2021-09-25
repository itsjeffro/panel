<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('resources/{resource}/fields', 'ResourceFieldController@show');

    Route::get('resources/{resource}', 'ResourceController@index');
    Route::get('resources/{resource}/{id}', 'ResourceController@show');
    Route::get('resources/{resource}/{id}/edit', 'ResourceController@edit');
    Route::put('resources/{resource}/{id}', 'ResourceController@update');
    Route::post('resources/{resource}', 'ResourceController@store');
    Route::delete('resources/{resource}/{id}', 'ResourceController@destroy');
});

Route::get('/{view?}', 'AppController@show')->where('view', '(.*)');