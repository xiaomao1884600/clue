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
            // 关闭日期格式
            //$reader->formatDates(false);
            // 设置日期默认格式
            $reader->setDateFormat('Y-m-d H:i:s');
            $excelData = $reader->get()->toArray();
            return $excelData;
        });
        if (! $excelData){
            throw new \Exception('文件没有内容!', 3005);
        }

        return $excelData;
    }

    public function convertExcelDataRule($excelData, array $rule)
    {
        $newData = [];
        $titleRule = $rule['titleRule'] ?? [];
        $typeRule = $rule['typeRule'] ?? [];
        $dicRule = $rule['dicRule'] ?? [];
        if(! $excelData) return [];

        foreach($excelData as $key => $value){
            if(! $value) continue;
            foreach($value as $k => $v){
                // 标题规则
                $nk = $titleRule[$k] ?? '';
                if(! $nk) continue;

                // 类型规则
                $v = $this->convertTypeRule($k, $v, $typeRule);
                // 字典规则

                $newData[$key][$nk] = $v;
            }
        }
        unset($excelData);
        return $newData;
    }

    protected function convertTypeRule($k, $v, $typeRule)
    {
        $type = $typeRule[$k] ?? '';
        switch($type){
            case 'type_int' :
                debuger(322);
                $v = (int) $v;
                break;
            case 'type_int_format' :
                debuger($v);
                $v = is_numeric($v) ? number_format($v, 0, '', '') : $v;
                break;
            case 'type_date' :
                $v = date('Y-m-d', strtotime($v));
                break;
            case 'type_gmdate' :
                $v = date('Y-m-d H:i:s', strtotime($v));
                break;
        }

        return $v;
    }
}