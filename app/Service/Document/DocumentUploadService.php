<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/5/26
 * Time: 下午4:20
 */

namespace App\Service\Document;

use App\Model\Clue\ExcelConfig;
use App\Repository\Document\DocumentRep;
use App\Service\Excel\ImportExcelService;
use Illuminate\Http\Request;
use App\Service\Foundation\BaseService;

class DocumentUploadService extends BaseService
{
    protected $importExcelService;

    protected $documentRep;

    public function __construct(
        ImportExcelService $importExcelService,
        DocumentRep $documentRep
    )
    {
        parent::__construct();
        $this->importExcelService = $importExcelService;
        $this->documentRep = $documentRep;
    }

    /**
     * 导入文书管理
     * @param Request $request
     * @param array $params
     * @throws \Exception
     */
    public function importDocument(Request $request, array $params)
    {
        try{

            $params['op_type'] = ExcelConfig::OP_TYPE_DOCUMENT;
            // TODO 上传excel文件
            $params['fileInfo'] = $this->importExcelService->uploadExcel($request, $params);

            // TODO 导入excel数据
            $excelData = $this->importExcelService->getExcelData($params);

            // TODO 处理文书管理保存
            $result = $this->setSaveDocumentData($excelData, $params);

            return $result;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 处理文书管理保存
     * @param array $excelData
     * @param array $params
     * @return array
     */
    protected function setSaveDocumentData(array $excelData, array $params = [])
    {
        $result = [];
        $error = [];
        $requiredRule = $params['ruleInfo']['required_rule'] ?? [];

        // 检测必填项
        $error = $this->importExcelService->verifyRequired($excelData, $requiredRule, $error);

        // 处理失败信息
        $failedData = $this->importExcelService->setFailedData($excelData, $error, $params);

        array_walk($excelData, function(& $value){
            $value['op_type'] = 1;
        });

        // 处理登记发放保存
        $this->documentRep->saveDocument($excelData);

        $result = [
            'type' => 't_document',
            'successData' => ['total' => count($excelData)],
            'failedData' => ['total' => count($failedData) ,'data' => $failedData],
        ];
        return $result;

    }
}