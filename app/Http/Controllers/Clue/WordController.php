<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/8
 * Time: 下午3:31
 */
namespace App\Http\Controllers\Clue;

use App\Http\Controllers\Controller;
use App\Service\Clue\ClueWordService;
use App\Service\Exceptions\ApiExceptions;
use App\Service\Exceptions\Message;
use Illuminate\Http\Request;

class WordController extends Controller
{

    /**
     * 导出线索word
     * @param Request $request
     * @param ClueWordService $clueWordService
     * @return array|mixed
     */
    public function clueExportWord(Request $request, ClueWordService $clueWordService)
    {
        try {
            return Message::success($clueWordService->setClueExportWord(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }
}