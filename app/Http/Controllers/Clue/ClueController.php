<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/18
 * Time: 上午10:29
 */

namespace App\Http\Controllers\Clue;


use App\Http\Controllers\Controller;
use App\Service\Clue\ClueService;
use App\Service\Clue\ClueUploadService;
use App\Service\Exceptions\ApiExceptions;
use App\Service\Exceptions\Message;
use Illuminate\Http\Request;

class ClueController extends Controller
{
    public function __construct()
    {

    }

    /**
     * 上传线索附件
     * @param Request $request
     * @param ClueService $clueService
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

    /**
     * 录入线索
     * @param Request $request
     * @param ClueService $clueService
     * @return array|mixed
     */
    public function saveClue(Request $request, ClueService $clueService)
    {
        try {
            return Message::success($clueService->saveClue(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     * 检测线索编号
     * @param Request $request
     * @param ClueService $clueService
     * @return array|mixed
     */
    public function checkClueNumber(Request $request, ClueService $clueService)
    {
        try {
            return Message::success($clueService->checkClueNumber(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     * 查看线索明细
     * @param Request $request
     * @param ClueService $clueService
     * @return array|mixed
     */
    public function viewClue(Request $request, ClueService $clueService)
    {
        try {
            return Message::success($clueService->viewClue(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     * 删除线索
     * @param Request $request
     * @param ClueService $clueService
     * @return array|mixed
     */
    public function deleteClue(Request $request, ClueService $clueService)
    {
        try {
            return Message::success($clueService->deleteClue(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }
}