<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/20
 * Time: 下午10:04
 */
namespace App\Http\Controllers\Document;

use App\Http\Controllers\Controller;
use App\Service\Exceptions\ApiExceptions;
use App\Service\Exceptions\Message;
use Illuminate\Http\Request;
use App\Service\Document\DocumentService;

/**
 * 公文管理
 * Class DocumentController
 * @package App\Http\Controllers\Clue
 */
class DocumentController extends Controller
{
    public function __construct()
    {

    }
    
    /**
     * 新增发文登记
     * 
     * @param Request $request
     * @param DocumentService $documentService
     * @return type
     */
    public function save(Request $request, DocumentService $documentService)
    {
        try {
            return Message::success($documentService->saveDocumentService(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }
    
    public function documentList(Request $request, DocumentService $documentService)
    {
        try {
            return Message::success($documentService->documentListService(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }
}