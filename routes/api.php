<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * 登录
 */
Route::group(['namespace' => 'UserLogin'], function(){

    Route::any('dologin', 'UserLoginController@doLogin');
    Route::any('logout', 'UserLoginController@logout');
});


// token验证
//Route::group(['middleware' => ['verify_token']], function(){
Route::group(['middleware' => []], function(){
    /**
     * 导出word TODO 增加身份验证
     */
    Route::group(['namespace' => 'Clue', 'prefix' => 'clue'], function(){
        // 导出word文档
        Route::any('export_word', 'WordController@clueExportWord');
    });
});



