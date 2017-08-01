<?php

Route::get('/parts', ['as' => 'dashboard.index', 'uses' => 'Api\Dashboard\Http\Controllers\DashboardController@index']);