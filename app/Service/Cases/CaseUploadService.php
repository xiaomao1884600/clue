<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/4/7
 * Time: 下午10:37
 */
namespace App\Service\CaseS;

use App\Model\Clue\ExcelConfig;
use App\Repository\Cases\CaseRep;
use App\Service\Excel\ImportExcelService;
use App\Service\Foundation\BaseService;
use Illuminate\Http\Request;

class CaseUploadService extends BaseService
{
    protected $importExcelService;

    protected $caseRep;

    public function __construct(
        ImportExcelService $importExcelService,
        CaseRep $caseRep
    )
    {
        parent::__construct();
        $this->importExcelService = $importExcelService;
        $this->caseRep = $caseRep;
    }

    /**
     * 导入案件问题线索
     * @param Request $request
     * @param array $params
     * @throws \Exception
     */
    public function importCaseClue(Request $request, array $params)
    {
        try{

            $params['op_type'] = ExcelConfig::OP_TYPE_CASE_CLUE;
            // TODO 上传excel文件
            $params['fileInfo'] = $this->importExcelService->uploadExcel($request, $params);

            // TODO 导入excel数据
            $excelData = $this->importExcelService->getExcelData($params);

            // TODO 处理案件问题线索保存
            $result = $this->setSaveCaseClueData($excelData, $params);

            return $result;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 处理案件问题线索保存
     * @param array $excelData
     * @param array $params
     * @return array
     */
    protected function setSaveCaseClueData(array $excelData, array $params = [])
    {
        $result = [];
        $error = [];
        $requiredRule = $params['ruleInfo']['required_rule'] ?? [];

        // 检测必填项
        $error = $this->importExcelService->verifyRequired($excelData, $requiredRule, $error);

        // 处理失败信息
        $failedData = $this->importExcelService->setFailedData($excelData, $error, $params);

        array_walk($excelData, function(& $value){
            $value['case_clue_id'] = guid();
        });

        // 处理案件线索数据保存
        $this->caseRep->saveCaseClue($excelData);

        $result = [
            'successData' => ['total' => count($excelData)],
            'failedData' => ['total' => count($failedData) ,'data' => $failedData],
        ];
        return $result;

    }

    /**
     * 导入立案数据
     * @param Request $request
     * @param array $params
     * @throws \Exception
     */
    public function importFiling(Request $request, array $params)
    {
        try{

            $params['op_type'] = ExcelConfig::OP_TYPE_CASE_FILING;
            // TODO 上传excel文件
            $params['fileInfo'] = $this->importExcelService->uploadExcel($request, $params);

            // TODO 导入excel数据
            $excelData = $this->importExcelService->getExcelData($params);

            // TODO 处理案件问题线索保存
            $result = $this->setSaveFilingData($excelData, $params);

            return $result;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 处理立案保存
     * @param array $excelData
     * @param array $params
     * @return array
     */
    protected function setSaveFilingData(array $excelData, array $params = [])
    {
        $result = [];
        $error = [];
        $requiredRule = $params['ruleInfo']['required_rule'] ?? [];

        // 检测必填项
        $error = $this->importExcelService->verifyRequired($excelData, $requiredRule, $error);

        // 处理失败信息
        $failedData = $this->importExcelService->setFailedData($excelData, $error, $params);

        array_walk($excelData, function(& $value){
            $value['id'] = guid();
        });

        // TODO 立案是否每次覆盖式导入

        // 处理案件线索数据保存
        $this->caseRep->saveFiling($excelData);

        $result = [
            'successData' => ['total' => count($excelData)],
            'failedData' => ['total' => count($failedData) ,'data' => $failedData],
        ];
        return $result;

    }
}