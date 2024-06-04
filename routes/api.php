<?php

Route::post('login', 'Api\\AuthController@login');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('send-photo', 'Api\\AuthController@sendPhoto');

});