<?php

Route::group(['middleware' => ['auth'],'namespace'=>'Admin','prefix' => 'admin'], function () {

    Route::get('/index', function () {
        $data=array(
            'cours'=>\App\Http\Models\Cours::select('id')->count('id'),
            'chapitres'=>\App\Http\Models\Chapitre::select('id')->count('id'),
            'articles'=>\App\Http\Models\Article::select('id')->count('id'),
            'etablissements'=>\App\Http\Models\Etude::select('id')->count('id'),
            'corriges'=>\App\Http\Models\Corrige::select('id')->count('id')
        );
        return view('admin.home',$data);
    });

    Route::controller('users', 'UserController');

    // Include route
     require_once('routes/admin.php');

});

// Include route
require_once('routes/front.php');

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
