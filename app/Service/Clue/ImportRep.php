<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/31
 * Time: 下午6:13
 */

namespace App\Service\Clue;


use App\Model\Clue\ImportFailed;
use App\Repository\Foundation\BaseRep;

class ImportRep extends BaseRep
{
    protected $importFailed;

    public function __construct(
        ImportFailed $importFailed
    )
    {
        parent::__construct();
        $this->importFailed = $importFailed;
    }

    public function saveImportFailedData(array $data)
    {
        return $this->importFailed->insertUpdateBatch($data);
    }

}