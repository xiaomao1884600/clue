<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/4/5
 * Time: 下午1:35
 */

namespace App\Repository\Clue;

use App\Model\Clue\ExcelConfig;
use App\Model\Clue\ImportFailed;
use App\Repository\Foundation\BaseRep;
use DB;

class ImportRep extends BaseRep
{
    protected $importFailed;

    protected $excelConfig;

    public function __construct(
        ImportFailed $importFailed,
        ExcelConfig $excelConfig
    )
    {
        parent::__construct();
        $this->importFailed = $importFailed;
        $this->excelConfig = $excelConfig;
    }

    public function saveImportFailedData(array $data)
    {
        return $this->importFailed->insertUpdateBatch($data);
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