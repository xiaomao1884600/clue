<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/5/21
 * Time: 下午10:43
 */

namespace App\Http\Controllers\Register;


use App\Http\Controllers\Controller;
use App\Service\Exceptions\ApiExceptions;
use App\Service\Exceptions\Message;
use App\Service\Register\RegisterService;
use App\Service\Register\RegisterWordService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __construct()
    {

    }

    /**
     * 录入登记发放
     * @param Request $request
     * @param RegisterService $registerService
     * @return array|mixed
     */
    public function saveRegister(Request $request, RegisterService $registerService)
    {
        try {
            return Message::success($registerService->saveRegister(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     * 检测登记发放编号
     * @param Request $request
     * @param RegisterService $registerService
     * @return array|mixed
     */
    public function checkRegisterNumber(Request $request, RegisterService $registerService)
    {
        try {
            return Message::success($registerService->checkClueNumber(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     * 导出登记发放word
     * @param Request $request
     * @param RegisterWordService $registerWordService
     * @return array|mixed
     */
    public function registerExportWord(Request $request, RegisterWordService $registerWordService)
    {
        try {
            return Message::success($registerWordService->setRegisterExportWord(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }

    /**
     * 查看
     * @param Request $request
     * @param RegisterService $registerService
     * @return array|mixed
     */
    public function viewRegister(Request $request, RegisterService $registerService)
    {
        try {
            return Message::success($registerService->viewRegister(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }
}