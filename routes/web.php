<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/




//Route::get('/ranker',function (){
//    (new BayesianRating())->triggerRanking();
//});

//Route::get('/reset_password','HomeController@showResetPassword');

Route::group([
    'prefix' => 'password'
], function () {
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset')->name('reset_password');
});


Route::get('/', 'HomeController@index')->name('home');
