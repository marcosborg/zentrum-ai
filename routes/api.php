<?php

Route::post('login', 'Api\\AuthController@login');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('send-photo', 'Api\\AuthController@sendPhoto');
    Route::get('get-user', 'Api\\AuthController@getUser');
    Route::post('update-user', 'Api\\AuthController@updateUser');
    Route::get('form-datas', 'Api\\AuthController@formDatas');
    Route::get('form-data/{form_data_id}', 'Api\\AuthController@formData');
    Route::post('search-stock', 'Api\\AuthController@searchStock');
    Route::get('update-state/{form_data_id}', 'Api\\AuthController@updateState');
    Route::get('prestashop-categories', 'Api\\AuthController@prestashopCategories');

});