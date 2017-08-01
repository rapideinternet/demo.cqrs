<?php

/*
|--------------------------------------------------------------------------
| Event sourced child entity
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'web', 'prefix' => 'parts', 'namespace' => 'Api\Parts\Http\Controllers'], function () {

    Route::get('index', ['as' => 'parts.index', 'uses' => 'PartsController@index']);
    Route::post('store', ['as' => 'parts.store', 'uses' => 'PartsController@store']);
    Route::post('update', ['as' => 'parts.update', 'uses' => 'PartsController@update']);
    Route::delete('destroy', ['as' => 'parts.destroy', 'uses' => 'PartsController@destroy']);

});