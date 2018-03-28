<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/13
 * Time: 下午9:18
 */

namespace App\Http\Controllers\Clue;


use App\Http\Controllers\Controller;
use App\Service\Clue\ClueUploadService;
use App\Service\Exceptions\ApiExceptions;
use App\Service\Exceptions\Message;
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

    public function formExcel()
    {
        return view('excel');
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
}