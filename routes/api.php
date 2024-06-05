<?php

Route::post('login', 'Api\\AuthController@login');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('send-photo', 'Api\\AuthController@sendPhoto');
    Route::get('get-user', 'Api\\AuthController@getUser');
    Route::post('update-user', 'Api\\AuthController@updateUser');
    Route::get('form-datas', 'Api\\AuthController@formDatas');
    Route::get('form-data/{form_data_id}', 'Api\\AuthController@formData');

});