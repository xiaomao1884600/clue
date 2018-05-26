<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/5/26
 * Time: 下午3:59
 */

namespace App\Service\Register;


use App\Model\Clue\ExcelConfig;
use App\Repository\Register\RegisterRep;
use App\Service\Excel\ImportExcelService;
use App\Service\Foundation\BaseService;
use Illuminate\Http\Request;

class RegisterUploadService extends BaseService
{
    protected $importExcelService;

    protected $registerRep;

    public function __construct(
        ImportExcelService $importExcelService,
        RegisterRep $registerRep
    )
    {
        parent::__construct();
        $this->importExcelService = $importExcelService;
        $this->registerRep = $registerRep;
    }

    /**
     * 导入登记发放
     * @param Request $request
     * @param array $params
     * @throws \Exception
     */
    public function importRegister(Request $request, array $params)
    {
        try{

            $params['op_type'] = ExcelConfig::OP_TYPE_REGISTER;
            // TODO 上传excel文件
            $params['fileInfo'] = $this->importExcelService->uploadExcel($request, $params);

            // TODO 导入excel数据
            $excelData = $this->importExcelService->getExcelData($params);

            // TODO 处理登记发放保存
            $result = $this->setSaveRegisterData($excelData, $params);

            return $result;
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 处理登记发放保存
     * @param array $excelData
     * @param array $params
     * @return array
     */
    protected function setSaveRegisterData(array $excelData, array $params = [])
    {
        $result = [];
        $error = [];
        $requiredRule = $params['ruleInfo']['required_rule'] ?? [];

        // 检测必填项
        $error = $this->importExcelService->verifyRequired($excelData, $requiredRule, $error);

        // 处理失败信息
        $failedData = $this->importExcelService->setFailedData($excelData, $error, $params);

        array_walk($excelData, function(& $value){
            $value['clue_id'] = guid();
            $value['op_type'] = 1;
        });

        // 处理案件线索数据保存
        $this->registerRep->saveRegister($excelData);

        $result = [
            'type' => 't_register',
            'successData' => ['total' => count($excelData)],
            'failedData' => ['total' => count($failedData) ,'data' => $failedData],
        ];
        return $result;

    }
}