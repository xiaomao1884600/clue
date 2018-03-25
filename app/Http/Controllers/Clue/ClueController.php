<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/18
 * Time: 上午10:29
 */

namespace App\Http\Controllers\Clue;


use App\Http\Controllers\Controller;
use App\Service\Clue\ClueSearchService;
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

    /**
     * 删除线索附件信息（内部使用）
     * @param Request $request
     * @param ClueService $clueService
     * @return array|mixed
     */
    public function deleteClueAttachments(Request $request, ClueService $clueService)
    {
        try {
            return Message::success($clueService->deleteClueAttachments(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     * 关键字搜索列表
     * @param Request $request
     * @param ClueSearchService $clueSearchService
     * @return array|mixed
     */
    public function getClueKeyWordSearch(Request $request, ClueSearchService $clueSearchService)
    {
        try {
            return Message::success($clueSearchService->clueKeyWordSearch(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     * 高级搜索
     * @param Request $request
     * @param ClueSearchService $clueSearchService
     * @return array|mixed
     */
    public function getClueAdvancedSearch(Request $request, ClueSearchService $clueSearchService)
    {
        try {
            return Message::success($clueSearchService->clueAdvancedSearch(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     * 检测被反映人是否存在线索、案件等信息
     * @param Request $request
     * @param ClueSearchService $clueSearchService
     * @return array|mixed
     */
    public function getClueByReflectedName(Request $request, ClueSearchService $clueSearchService)
    {
        try {
            return Message::success($clueSearchService->getClueByReflectedName(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }
    
    /**
     * 超期提醒
     * 
     * @param Request $request
     * @param ClueService $clueService
     * @return type
     */
    public function overdueRemind(Request $request, ClueService $clueService)
    {
        try {
            return Message::success($clueService->overdueRemind(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }
}