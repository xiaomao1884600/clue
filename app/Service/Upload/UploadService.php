<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/18
 * Time: 下午4:03
 */
namespace App\Service\Upload;

use App\Service\Foundation\BaseService;
use Illuminate\Support\Facades\Storage;

class UploadService extends BaseService
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 上传文件
     * @param $file
     * @param array $params
     */
    public function uploadFile($file, array $params)
    {
        $fileInfo = [];
        // 检测文件是否有效
        $this->checkFileValid($file);

        // 获取文件信息
        $fileInfo = $this->getFileInfo($file);

        // 检测文件类型
        $this->checkFileType($file, $params);

        // 检测文件大小
        $this->checkFileSize($file, $params);

        // 上传类型
        $fileInfo['attachment_type'] = $params['uploadType'] ?? 0;

        // 获取原文件名称
        $fileInfo['name'] = $this->getFileName($fileInfo['originalName']);

        // 获取新文件名称
        $fileInfo['newFileName'] = $this->getNewFileName($fileInfo);

        // 上传文件地址
        $fileInfo['path'] = $this->getUploadFilePath($params);

        $fileName = $fileInfo['path'] ? $fileInfo['path'] . DS . $fileInfo['newFileName'] : $fileInfo['newFileName'];

        // TODO 可以使用move方式
        //$fileInfo['file_path'] = $file->move($fileInfo['fileurl'], iconv('UTF-8', 'GBK//IGNORE', $fileInfo['newFileName']));

        // 上传文件
        $fileInfo['bool'] = Storage::disk($params['disk'])->put($fileName, $fileInfo['realPath']);

        // 新文件路径
        $fileInfo['file_path'] = $params['disk'] . DS . $fileName;

        return $fileInfo;
    }

    /**
     * 检测文件是否有效
     * @param $file
     * @return mixed
     * @throws \Exception
     */
    protected function checkFileValid($file)
    {
        if(! $file->isValid()){
            throw new \Exception('File does invalid');
        }

        return $file;
    }

    protected function getFileInfo($file)
    {
        $fileInfo = [];
        // 获取文件原始名称
        $fileInfo['originalName'] = $file->getClientOriginalName();
        // 临时文件名称
        $fileInfo['tmpName'] = $file->getFileName();
        // 临时绝对路径
        $fileInfo['realPath'] = $file->getRealPath();
        // 文件类型
        $fileInfo['mimeType'] = $file->getClientMimeType();
        // 文件大小
        $fileInfo['size'] = $file->getSize();
        // 扩展
        $fileInfo['extension'] = $file->getClientOriginalExtension();

        return $fileInfo;
    }

    protected function checkFileType($file, $params)
    {
        $uploadType = $params['attachment_type'] ?? '';
        $extension = strtolower($file->getClientOriginalExtension());
        $allowType = Upload::getFileType($extension, $uploadType);

        if($allowType && ! in_array($extension, $allowType)){
            throw new \Exception('Allowed file type 【 ' . json_encode($allowType) . '】!');
        }

        return true;
    }

    protected function checkFileSize($file, $params)
    {
        $uploadType = $params['attachment_type'] ?? '';
        $fileSize = strtolower($file->getsize());
        $allowSize = Upload::getFileSize($fileSize, $uploadType);

        if($fileSize > $allowSize){
            throw new \Exception('allowed file size 【 ' . round($allowSize/1000) . 'M 】 !');
        }

        return true;
    }

    /**
     * 获取原文件名
     * @param $originalName
     * @return bool|string
     */
    protected function getFileName($originalName)
    {
        return substr($originalName, 0, strrpos($originalName, '.'));
    }

    /**
     * 获取新文件名
     * @param $originalName
     * @return bool|string
     */
    protected function getNewFileName($fileInfo, $params = [])
    {
        return $fileInfo['name'] . '_' . date('ymdhis', TIMENOW).".".$fileInfo['extension'];
    }

    /**
     * 获取上传文件的路径
     * @param array $params
     * @return mixed|string
     */
    protected function getUploadFilePath (& $params = [])
    {
        return $params['path'] ?? '';
    }
}