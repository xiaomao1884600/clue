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

Route::any('test', function(){
    return 'test';
});

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
Route::get('formexcel', 'Clue\UploadController@formExcel');

/**
 * 上传
 */
Route::group(['namespace' => 'Clue', 'prefix' => 'clue'], function(){
    // 上传线索附件
    Route::any('clue_upload', 'ClueController@doClueUpload');
});


// token、secretkey验证
Route::group(['middleware' => ['verify_token', 'verify_secretkey']], function(){
//Route::group(['middleware' => []], function(){
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

        // 线索管理关键字搜索
        Route::any('clue_keyword_search', 'ClueController@getClueKeyWordSearch');

        // 线索管理高级搜索
        Route::any('clue_advanced_search', 'ClueController@getClueAdvancedSearch');

        // 检测被反映人线索、公文等信息
        Route::any('get_reflected_name_clue', 'ClueController@getClueByReflectedName');
        
        // 线索超期提醒
        Route::any('overdue', 'ClueController@overdueRemind');
        
        // 线索超期提醒个数
        Route::any('remindtotal', 'ClueController@remindTotal');

//        // 导入线索excel
//        Route::any('import_clue_excel', 'UploadController@importClueExcel');

        // 结办线索
        Route::any('set_clue_closed', 'ClueController@setClueClosed');
        
        // 问题线索处置情况列表、详情
        Route::any('problem_clues_list', 'ClueController@problemCluesList');

    });

});


/**
 * 登记发放管理（已结线索）
 */
Route::group(['namespace' => 'Register', 'prefix' => 'clue'], function(){
    // 列表
    Route::any('closedlist', 'ClueClosedController@getClosedList');

    // 导出登记发放word文档
    Route::any('register_export_word', 'RegisterController@registerExportWord');

    // 录入等级发放
    Route::any('save_register', 'RegisterController@saveRegister');

    // 检测登记发放编号
    Route::any('check_register_number', 'RegisterController@checkRegisterNumber');

    // 线索明细查看
    Route::any('view_register', 'RegisterController@viewRegister');

});


/**
 * 公文管理（文书）
 */
Route::group(['namespace' => 'Document', 'prefix' => 'document'], function(){
    // 公文添加
    Route::any('save', 'DocumentController@save');
    //公文列表
    Route::any('list', 'DocumentController@documentList');
    // 详情
    Route::any('view', 'DocumentController@documentView');
});


Route::group(['namespace' => 'Foundation', 'prefix' => 'dic'], function(){
    // 字典
    Route::any('getdic', 'DicController@dicList');
});

/**
 * 导入Excel
 */
Route::group(['namespace' => 'Clue', 'prefix' => 'excel'], function(){
    // 导入离线线索
    Route::any('import_clue', 'UploadController@importClueExcel');

    //导入案件问题线索
    Route::any('import_case_clue', 'UploadController@importCaseClueExcel');

    //导入立案
    Route::any('import_filing', 'UploadController@importFilingExcel');

    //导入登记发放
    Route::any('import_register', 'UploadController@importRegisterExcel');

    //导入文书管理
    Route::any('import_document', 'UploadController@importDocumentExcel');

});

/**
 * 案件相关
 */
Route::group(['namespace' => 'Cases', 'prefix' => 'cases'], function(){
    // 获取案件列表
    Route::any('list', 'CasesController@getCaseList');
});

/**
 * 系统信息
 */
Route::group(['namespace' => 'System', 'prefix' => 'system'], function(){
    // 获取cpu信息
    Route::any('get_cpu', 'ShellController@getCpu');
});