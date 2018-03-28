<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/28
 * Time: 上午9:06
 */
namespace App\Service\Excel;

use App\Service\Foundation\BaseService;
use Excel;

class ExcelService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getExcelData(array $params)
    {
        $filePath = _isset($params, 'file_path');
        if(! file_exists($filePath)){
            throw new \Exception('The file does not exists');
        }

        $excelData = [];

        Excel::load($filePath, function($reader) use (& $excelData){
            $excelData = $reader->get()->toArray();
            return $excelData;
        });
        if (! $excelData){
            throw new \Exception('文件没有内容!', 3005);
        }

        return $excelData;
    }
}