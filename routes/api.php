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
    Route::any('recover_pwd', 'UserLoginController@recoverPwd');
});


Route::get('formupload', 'Clue\UploadController@formUpload');
Route::post('doupload', 'Clue\UploadController@doUpload');


/**
 * 上传
 */
Route::group(['namespace' => 'Clue', 'prefix' => 'clue'], function(){
    // 上传线索附件
    Route::any('clue_upload', 'ClueController@doClueUpload');
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

        // 线索录入
        Route::any('save_clue', 'ClueController@saveClue');

        // 检测线索编号
        Route::any('check_clue_number', 'ClueController@checkClueNumber');

        // 线索明细查看
        Route::any('view_clue', 'ClueController@viewClue');

        // 删除线索
        Route::any('delete_clue', 'ClueController@deleteClue');

        // 删除线索附件信息
        Route::any('delete_clue_attachments', 'ClueController@deleteClueAttachments');

    });


    /**
     * 登记发放管理（已结线索）
     */
    Route::group(['namespace' => 'Register', 'prefix' => 'clue'], function(){
        // 列表
        Route::any('closedlist', 'ClueClosedController@getClosedList');
    });


    /**
     * 公文管理（文书）
     */
    Route::group(['namespace' => 'Document', 'prefix' => 'document'], function(){

    });

});



