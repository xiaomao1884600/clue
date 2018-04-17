<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/18
 * Time: 下午4:05
 */

namespace App\Service\Clue;


use App\Model\Clue\ExcelConfig;
use App\Repository\Clue\ClueRep;
use App\Repository\Clue\FileRep;
use App\Repository\Clue\ImportRep;
use App\Service\Excel\ImportExcelService;
use App\Service\Foundation\BaseService;
use App\Service\Upload\UploadService;
use Illuminate\Http\Request;

class ClueUploadService extends BaseService
{
    protected $disk = 'uploads';

    protected $excelDisk = 'excel';
    // excel 导入地址
    protected $excelImportPath = 'import';

    // excel 导出地址
    protected $excelExportPath = 'export';

    protected $uploadService;

    protected $fileRep;

    protected $clueService;

    protected $importExcelService;

    protected $clueRep;

    protected $clueDic;

    protected $attachmentsField = [
        'attachment_type' => 'attachment_type',
        'newFileName' => 'filename',
        'originalName' => 'original_name',
        'file_path' => 'file_path',
        'extension' => 'file_extension',

    ];

    public function __construct(
        UploadService $uploadService,
        FileRep $fileRep,
        ClueService $clueService,
        ImportExcelService $importExcelService,
        ClueRep $clueRep,
        ClueDic $clueDic
    )
    {
        parent::__construct();

        $this->uploadService = $uploadService;
        $this->fileRep = $fileRep;
        $this->clueService = $clueService;
        $this->importExcelService = $importExcelService;
        $this->clueRep = $clueRep;
        $this->clueDic = $clueDic;
    }

    /**
     * 上传线索附件
     * @param Request $request
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function clueUpload(Request $request, array $params)
    {
        try{
            if(! $request->isMethod('post')){
                throw new \Exception('request method must be post');
            }

            // 取消验证上传类型
//            if(! isset($params['attachment_type'])){
//                throw new \Exception('The attachment_type does not exists !');
//            }

            if(! $request->file('file')){
                throw new \Exception('upload name is file !');
            }


            //file_put_contents(public_path('upload_test.json'), json_encode((array) current($request->file())));

            //$file = $request->file('file');
            $file = current($request->file());

            $params['disk'] = $this->disk;
            $params['attachment_type'] = $params['attachment_type'] ?? 'file';

            // 上传文件
            $fileInfo = $this->uploadService->uploadFile($file, $params);

            // 存储文件信息
            $fileInfo['file_id'] = $this->saveAttachments($fileInfo);

            return ['fileInfo' => $fileInfo];

        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 存储附件信息
     * @param array $fileInfo
     * @return \App\Model\Foundation\type|int
     */
    protected function saveAttachments(array $fileInfo)
    {
        if(! $fileInfo) return 0;
        $data = combineArray($fileInfo, $this->attachmentsField);
        $data['json_data'] = json_encode($fileInfo);

        return $this->fileRep->saveAttachments($data);
    }

    /**
     * 导入线索excel文件
     * @param array $params
     */
    public function importClueExcel(Request $request, array $params)
    {
        try{
            //

            $params['op_type'] = ExcelConfig::OP_TYPE_CLUE;
            // TODO 上传excel文件
            $params['fileInfo'] = $this->importExcelService->uploadExcel($request, $params);

            // TODO 导入excel数据
            $excelData = $this->importExcelService->getExcelData($params);

            // TODO 处理线索保存
            $result = $this->setSaveClueData($excelData, $params);

            return $result;
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 检测文件有效性
     * @param Request $request
     * @param array $params
     * @return bool
     * @throws \Exception
     */
    protected function checkUploadValid(Request $request, array $params = [])
    {
        if(! $request->isMethod('post')){
            throw new \Exception('The method must be post');
        }

        if(! $request->file()){
            throw new \Exception('upload file missing');
        }

        // 检测文件是否有效
        $file = current($request->file());
        if(! $file->isValid()){
            throw new \Exception('upload file invalid');
        }

        return true;
    }

    protected function setSaveClueData(array $excelData, array $params = [])
    {
        $result = [];
        $error = [];
        $requiredRule = $params['ruleInfo']['required_rule'] ?? [];
        $dicField = isset($params['ruleInfo']['dic_rule']) ? array_values($params['ruleInfo']['dic_rule']) : [];

        // 检测必填项
        $error = $this->importExcelService->verifyRequired($excelData, $requiredRule, $error);

        // 处理失败信息
        $failedData = $this->importExcelService->setFailedData($excelData, $error, $params);

        // todo 检测编号重复数据并删除
        $this->clearVerifyClueNumber($excelData);

        // 线索字典入库转换
        $excelData = $this->clueDic->convertDic($excelData, $dicField, 1);
        
        // 处理线索数据保存
        $this->clueService->saveExcelClue($excelData);

        $result = [
            'successData' => ['total' => count($excelData)],
            'failedData' => ['total' => count($failedData) ,'data' => $failedData],
        ];
        return $result;

    }

    /**
     * 清除编号重复线索
     * @param array $data
     * @return array
     */
    protected function clearVerifyClueNumber(array $data)
    {
        $condition = [];

        $condition['number'] = array_column($data, 'number');

        // 获取线索数据
        $clueInfo = $this->clueService->checkClueByNumber($condition);

        if(! $clueInfo) return [];

        $clueId = $clueInfo ? array_column($clueInfo, 'clue_id') : [];

        $delCondition = ['clue_id' => $clueId];

        // 清除线索
        $result = $this->clueRep->deleteClue($delCondition);

        // 清除线索详情
        $result = $this->clueRep->deleteClueDetail($delCondition);

        return $data;
    }
}