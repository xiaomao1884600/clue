<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Cases;


use App\Http\Controllers\Controller;
use App\Service\Exceptions\ApiExceptions;
use App\Service\Exceptions\Message;
use Illuminate\Http\Request;
use App\Service\Cases\CaseService;

class CasesController extends Controller
{
    public function __construct()
    {

    }
    
    /**
     * 立案案件登记列表、详情
     * 
     * @param Request $request
     * @param CaseService $caseService
     * @return type
     */
    public function getCaseList(Request $request, CaseService $caseService)
    {
        try {
            return Message::success($caseService->getCaseListService(requestData($request)));
        } catch (\Exception $exception) {
            return ApiExceptions::handle($exception);
        }
    }
}