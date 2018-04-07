<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/4/7
 * Time: 下午10:47
 */
namespace App\Repository\Cases;

use App\Model\Cases\CaseClue;
use App\Model\Cases\Filing;
use App\Repository\Foundation\BaseRep;

class CaseRep extends BaseRep
{
    protected $caseClue;

    protected $filing;

    public function __construct(
        CaseClue $caseClue,
        Filing $filing
    )
    {
        parent::__construct();
        $this->caseClue = $caseClue;
        $this->filing = $filing;
    }

    /**
     * 保存案件问题线索
     * @param array $data
     * @return string
     */
    public function saveCaseClue(array $data)
    {
        return $this->caseClue->insertUpdateBatch($data);
    }

    /**
     * 保存立案
     * @param array $data
     * @return string
     */
    public function saveFiling(array $data)
    {
        return $this->filing->insertUpdateBatch($data);
    }
}