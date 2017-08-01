<?php

Route::group(['middleware' => 'web', 'prefix' => 'allotment', 'namespace' => 'Api\Allotment\Http\Controllers'], function()
{
    Route::get('/', 'AllotmentController@index');
});
