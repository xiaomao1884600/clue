<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/13
 * Time: 下午9:18
 */

namespace App\Http\Controllers\Clue;


use App\Http\Controllers\Controller;
use App\Service\Cases\CaseUploadService;
use App\Service\Clue\ClueUploadService;
use App\Service\Document\DocumentUploadService;
use App\Service\Exceptions\ApiExceptions;
use App\Service\Exceptions\Message;
use App\Service\Register\RegisterUploadService;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function __construct()
    {

    }

    public function formUpload()
    {
        return view('upload');
    }

    /**
     * 线索上传附件
     * @param Request $request
     * @return array|mixed
     */
    public function doClueUpload(Request $request, ClueUploadService $clueUploadService)
    {
        try {
            return Message::success($clueUploadService->clueUpload($request, requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    public function formExcel(Request $request)
    {
        $params = $request->all();
        $type = $params['type'] ?? 'clue';

        if('clue' == $type){
            return view('excel');
        }

        if('case_clue' == $type){
            return view('caseclue');
        }

        if('filing' == $type){
            return view('filing');
        }

        if('register' == $type){
            return view('register');
        }

        if('document' == $type){
            return view('document');
        }

        return [];
    }

    /**
     * 导入线索excel
     * @param Request $request
     * @return array|mixed
     */
    public function importClueExcel(Request $request, ClueUploadService $clueUploadService)
    {
        try {
            return Message::success($clueUploadService->importClueExcel($request, requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     *  导入案件问题线索
     * @param Request $request
     * @param CaseUploadService $caseUploadService
     * @return array|mixed
     */
    public function importCaseClueExcel(Request $request, CaseUploadService $caseUploadService)
    {
        try {
            return Message::success($caseUploadService->importCaseClue($request, requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     *  导入立案
     * @param Request $request
     * @param CaseUploadService $caseUploadService
     * @return array|mixed
     */
    public function importFilingExcel(Request $request, CaseUploadService $caseUploadService)
    {
        try {
            return Message::success($caseUploadService->importFiling($request, requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     *  导入登记发放
     * @param Request $request
     * @param CaseUploadService $caseUploadService
     * @return array|mixed
     */
    public function importRegisterExcel(Request $request, RegisterUploadService $registerUploadService)
    {
        try {
            return Message::success($registerUploadService->importRegister($request, requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     *  导入文书管理
     * @param Request $request
     * @param CaseUploadService $caseUploadService
     * @return array|mixed
     */
    public function importDocumentExcel(Request $request, DocumentUploadService $documentUploadService)
    {
        try {
            return Message::success($documentUploadService->importDocument($request, requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }
}