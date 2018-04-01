<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/18
 * Time: 下午4:05
 */

namespace App\Service\Clue;


use App\Repository\Clue\FileRep;
use App\Service\Excel\ExcelService;
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

    protected $excelService;

    protected $clueService;

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
        FileRep $fileRep,
        ExcelService $excelService,
        ClueService $clueService,
        ImportRep $importRep
    )
    {
        parent::__construct();

        $this->uploadService = $uploadService;
        $this->fileRep = $fileRep;
        $this->excelService = $excelService;
        $this->clueService = $clueService;
        $this->importRep = $importRep;
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

            // TODO 上传excel文件
            $params['fileInfo'] = $this->uploadExcel($request, $params);

            // TODO 导入excel数据
            $excelData = $this->getExcelData($params);

            // TODO 处理线索保存
            $result = $this->setSaveClueData($excelData, $params);

            return $params['fileInfo'];
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 上传excel
     * @param Request $request
     * @param array $params
     * @return array
     */
    protected function uploadExcel(Request $request, array $params)
    {
        // 检测文件有效
        $this->checkUploadValid($request, $params);

        // 上传文件
        $file = current($request->file());

        $params['disk'] = $this->excelDisk;
        $params['attachment_type'] = 'excel';
        $params['path'] = $this->excelImportPath;

        // 上传文件
        $fileInfo = $this->uploadService->uploadFile($file, $params);

        // 存储文件信息
        $fileInfo['file_id'] = $this->saveAttachments($fileInfo);

        return $fileInfo;
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

    protected function getExcelData(array $params)
    {
        $titleRule = $ruleInfo = [];
        $filePath = $params['fileInfo']['file_path'] ?? '';
        $filePath = public_path($filePath);
        $params['file_path'] = $filePath;
        $params['ruleInfo'] = config('clue.clue_excel');
        if(! file_exists($filePath)){
            throw new \Exception('The excel file does not exists!');
        }

        // 获取excel数据
        $excelData = $this->excelService->getExcelData($params);

        // 获取excel转换数据
        $excelData = $this->excelService->getConvertExcelData($excelData, $params);

        return $excelData;
    }

    protected function setSaveClueData(array $excelData, array $params = [])
    {
        $error = [];
        // todo 检测编号重复数据
        $error = $this->verifyClueNumber($excelData, $error);

        // 处理失败信息
        $result = $this->setFailedData($excelData, $error, $params);

        // 处理线索数据保存
        $this->clueService->saveExcelClue($excelData);
        
        return $result;

    }

    protected function verifyClueNumber(array $data, & $error)
    {
        $condition = [];

        $condition['number'] = array_column($data, 'number');
        $result = $this->clueService->checkClueByNumber($condition);
        $result = array_column($result, 'number', 'number');
        foreach($data as $key => $value){
            if(isset($result[$value['number']])){
                $error[$key]['number'] = '编号重复';
            }
        }

        return $error;
    }

    protected function setFailedData(array & $data, $error, $params)
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
            'file_info' => json_encode($params, 'fileInfo', []),
            'failed_data' => json_encode($failedData),
        ];

        // 存储失败数据
        $this->importRep->saveImportFailedData([$rt]);

        return $failedData;
    }
}