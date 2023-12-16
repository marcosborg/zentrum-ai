<?php

Route::redirect('/', '/login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Assistant
    Route::delete('assistants/destroy', 'AssistantController@massDestroy')->name('assistants.massDestroy');
    Route::resource('assistants', 'AssistantController');

    // Openai
    Route::delete('openais/destroy', 'OpenaiController@massDestroy')->name('openais.massDestroy');
    Route::resource('openais', 'OpenaiController');

    // Instruction
    Route::delete('instructions/destroy', 'InstructionController@massDestroy')->name('instructions.massDestroy');
    Route::resource('instructions', 'InstructionController');

    // Training
    Route::prefix('trainings')->group(function () {
        Route::get('/', 'TrainingController@index')->name('trainings.index');
        Route::get('assistant/{assistant_id}', 'TrainingController@assistant');
        Route::prefix('instructions')->group(function () {
            Route::post('create', 'TrainingController@instructionsCreate');
            Route::get('load/{assistant_id}', 'TrainingController@instructionsLoad');
            Route::get('delete/{instruction_id}', 'TrainingController@instructionDelete');
            Route::post('sync-assistant', 'TrainingController@syncAssistant');
        });
        Route::prefix('chat')->group(function () {
            Route::post('create-thread-and-run', 'TrainingController@chatCreateThreadAndRun');
            Route::get('list-messages/{assistant_id}/{thread_id}', 'TrainingController@chatListMessages');
            Route::post('create-message', 'TrainingController@chatCreateMessage');
            Route::get('create-run/{assistant_id}/{thread_id}', 'TrainingController@chatCreateRun');
        });
        Route::prefix('api')->group(function(){
            Route::get('search/{assistant_id}/{search}', 'TrainingController@apiSearch');
        });
    });

    // Project
    Route::delete('projects/destroy', 'ProjectController@massDestroy')->name('projects.massDestroy');
    Route::resource('projects', 'ProjectController');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});
