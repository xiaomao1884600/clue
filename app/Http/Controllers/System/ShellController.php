<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/4/11
 * Time: 下午11:15
 */
namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Service\Exceptions\ApiExceptions;
use App\Service\Exceptions\Message;
use App\Service\System\ShellService;
use Illuminate\Http\Request;

class ShellController extends Controller
{
    public function __construct()
    {

    }

    public function getCpu(Request $request, ShellService $shellService)
    {
        try {
            return Message::success($shellService->getCpu(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }
}