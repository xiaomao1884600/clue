<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/4/5
 * Time: 下午3:13
 */

namespace App\Repository\Clue;


use App\Model\Clue\ExcelConfig;
use App\Repository\Foundation\BaseRep;

class ExcelRep extends BaseRep
{
    protected $excelConfig;

    public function __construct(
        ExcelConfig $excelConfig
    )
    {
        parent::__construct();
        $this->excelConfig = $excelConfig;
    }

    public function getExcelConfig(array $condition)
    {
        $opType = $condition['op_type'] ?? '';
        if(! $opType) return [];
        $result = $this->excelConfig
                ->select()
                ->where('op_type', $opType)
                ->get()
                ->toArray();
        return $result;
    }
}