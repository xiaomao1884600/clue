<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/20
 * Time: 下午10:04
 */
namespace App\Http\Controllers\Register;

use App\Http\Controllers\Controller;
use App\Service\Exceptions\ApiExceptions;
use App\Service\Exceptions\Message;
use Illuminate\Http\Request;
use App\Service\Register\ClueClosedService;

/**
 * 已结案件管理(登记发放)
 * Class ClueClosedController
 * @package App\Http\Controllers\Register
 */
class ClueClosedController extends Controller
{
    public function __construct()
    {

    }

    /**
     * 获取已结线索列表
     *
     * @param Request $request
     * @param ClueClosedService $clueService
     * @return array|mixed
     */
    public function getClosedList(Request $request, ClueClosedService $clueService)
    {
        try {
            return Message::success($clueService->getClosedListService(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }
}