<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/31
 * Time: 下午3:33
 */
namespace App\Http\Controllers\Foundation;

use App\Http\Controllers\Controller;
use App\Service\Foundation\DicService;
use App\Service\Exceptions\Message;
use App\Service\Exceptions\ApiExceptions;

class DicController extends Controller
{
    public function __construct()
    {
        
    }
    
    /**
     * 字典
     * 
     * @param DicService $dicService
     * @return type
     */
    public function dicList(DicService $dicService)
    {
        try {
            return Message::success($dicService->getDicList());
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }
}