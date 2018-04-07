<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/4/5
 * Time: 下午1:57
 */

namespace App\Service\Excel;

use App\Repository\Clue\FileRep;
use App\Repository\Clue\ImportRep;
use App\Service\Excel\ExcelService;
use App\Service\Upload\UploadService;
use App\Service\Foundation\BaseService;
use Illuminate\Http\Request;

class ImportExcelService extends BaseService
{
    protected $excelDisk = 'excel';
    // excel 导入地址
    protected $excelImportPath = 'import';

    // excel 导出地址
    protected $excelExportPath = 'export';

    protected $uploadService;

    protected $excelService;

    protected $fileRep;

    protected $importRep;

    protected $attachmentsField = [
        'attachment_type' => 'attachment_type',
        'newFileName' => 'filename',
        'originalName' => 'original_name',
        'file_path' => 'file_path',
        'extension' => 'file_extension',

    ];

    public function __construct(
        UploadService $uploadService,
        ExcelService $excelService,
        FileRep $fileRep,
        ImportRep $importRep
    )
    {
        parent::__construct();
        $this->uploadService = $uploadService;
        $this->excelService = $excelService;
        $this->fileRep = $fileRep;
        $this->importRep = $importRep;
    }

    public function getExcelConfig(array $params)
    {
        $data = [
            'title_rule' => [],
            'type_rule' => [],
            'dic_rule' => [],
            'required_rule' => [],
        ];
        $opType = $params['op_type'] ?? [];
        if(! $opType){
            throw new \Exception('op_type required !');
        }
        $result = $this->importRep->getExcelConfig(['op_type' => $opType]);
        if(! $result) return $result;

        foreach($result as $key => $value){
            $data['title_rule'][$value['title']] = $value['field'];

            // 字段类型
            if($value['field_type']){
                $data['type_rule'][$value['title']] = $value['field_type'];
            }

            // 字典
            if($value['field_dic']){
                $data['dic_rule'][$value['title']] = $value['field_dic'];
            }

            // 必填
            if($value['field_required']){
                $data['required_rule'][$value['title']] = $value['field'];
            }
        }

        return $data;
    }

    /**
     * 上传excel
     * @param Request $request
     * @param array $params
     * @return array
     */
    public function uploadExcel(Request $request, array $params)
    {
        // 检测文件有效
        $this->checkUploadValid($request, $params);

        // 上传文件
        $file = current($request->file());

        $params['disk'] = $params['disk'] ?? $this->excelDisk;
        $params['attachment_type'] = 'excel';
        $params['path'] = $params['path'] ?? $this->excelImportPath;

        // 上传文件
        $fileInfo = $this->uploadService->uploadFile($file, $params);

        // 存储文件信息
        $fileInfo['file_id'] = $this->saveAttachments($fileInfo);

        return $fileInfo;
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

    public function getExcelData(array &$params)
    {
        $titleRule = $ruleInfo = [];
        $filePath = $params['fileInfo']['file_path'] ?? '';
        $filePath = public_path($filePath);
        $params['file_path'] = $filePath;
        //$params['ruleInfo'] = config('clue.clue_excel');
        $opType = $params['op_type'] ?? '';
        if(! $opType){
            throw new \Exception('op_type required !');
        }
        $params['ruleInfo'] = $this->getExcelConfig(['op_type' => $opType]);

        if(! file_exists($filePath)){
            throw new \Exception('The excel file does not exists!');
        }

        // 获取excel数据
        $excelData = $this->excelService->getExcelData($params);

        // 获取excel转换数据
        $excelData = $this->excelService->getConvertExcelData($excelData, $params);

        return $excelData;
    }

    public function setFailedData(array & $data, $error, $params)
    {
        $failedData = [];
        if(! $data || ! $error) return [];
        foreach($error as $k => $v){
            if(isset($data[$k])){
                $failedData[] = [
                    'data' => $data[$k],
                    'error' => $v,
                ];
                unset($data[$k]);
            }
        }

        $rt = [
            'file_id' => $params['fileInfo']['file_id'] ?? 0,
            'file_info' => json_encode(_isset($params, 'fileInfo', [])),
            'failed_data' => json_encode($failedData),
        ];

        // 存储失败数据
        $this->importRep->saveImportFailedData([$rt]);

        return $failedData;
    }

    /**
     * 检测必填
     * @param array $data
     * @param $requiredRule
     * @param $error
     * @return array
     */
    public function verifyRequired(array $data, $requiredRule,  & $error)
    {
        $condition = [];
        if(! $requiredRule) return [];
        $requiredRule = array_flip($requiredRule);

        foreach($data as $key => $value){
            foreach($value as $field => $v){
                if(isset($requiredRule[$field]) && ! $v){
                    $error[$key][$field] = "【" . $requiredRule[$field] . '】必填';
                }
            }
        }

        return $error;
    }
}