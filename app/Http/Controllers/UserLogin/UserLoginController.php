<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/11
 * Time: 下午5:41
 */
namespace App\Http\Controllers\UserLogin;

use App\Http\Controllers\Controller;
use App\Service\Exceptions\ApiExceptions;
use App\Service\Exceptions\Message;
use App\Service\UserLogin\UserLoginService;
use Illuminate\Http\Request;

class UserLoginController extends Controller
{

    /**
     * 登录
     * @param Request $request
     * @param UserLoginService $userLoginService
     * @return array|mixed
     */
    public function doLogin(Request $request, UserLoginService $userLoginService)
    {
        try{
            return Message::success($userLoginService->doLogin(requestData($request)));
        }catch(\Exception $e){
            return ApiExceptions::handle($e);
        }
    }

    /**
     * 退出
     * @param Request $request
     * @param UserLoginService $userLoginService
     * @return array|mixed
     */
    public function logout(Request $request, UserLoginService $userLoginService)
    {
        try{
            return Message::success($userLoginService->logout($request->all()));
        }catch(\Exception $e){
            return ApiExceptions::handle($e);
        }
    }

    /**
     * 密码找回
     * @param Request $request
     * @param UserLoginService $userLoginService
     * @return array|mixed
     */
    public function recoverPwd(Request $request, UserLoginService $userLoginService)
    {
        try{
            return Message::success($userLoginService->recoverPwd(requestData($request)));
        }catch(\Exception $e){
            return ApiExceptions::handle($e);
        }
    }
}