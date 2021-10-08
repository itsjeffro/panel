<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    // Actions
    Route::post('resources/{resource}/actions/{action}', 'ResourceActionController@handle')->name('panel.resources.actions.handle');

    // Fields
    Route::get('resources/{resource}/fields', 'ResourceFieldController@show')->name('panel.resources.fields.show');

    // Resources
    Route::get('resources/{resource}', 'ResourceController@index')->name('panel.resources.index');
    Route::get('resources/{resource}/{id}', 'ResourceController@show')->name('panel.resources.show');
    Route::get('resources/{resource}/{id}/edit', 'ResourceController@edit')->name('panel.resources.edit');
    Route::put('resources/{resource}/{id}', 'ResourceController@update')->name('panel.resources.update');
    Route::post('resources/{resource}', 'ResourceController@store')->name('panel.resources.store');
    Route::delete('resources/{resource}/{id}', 'ResourceController@destroy')->name('panel.resources.destroy');
});

Route::get('/{view?}', 'AppController@show')->where('view', '(.*)');