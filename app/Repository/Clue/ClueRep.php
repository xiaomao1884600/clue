<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/18
 * Time: 上午10:36
 */
namespace App\Repository\Clue;

use App\Model\Clue\Clue;
use App\Model\Clue\ClueAttachments;
use App\Model\Clue\ClueDetail;
use App\Repository\Foundation\BaseRep;

class ClueRep extends BaseRep
{
    protected $clue;

    protected $clueDetail;

    protected $clueAttachments;

    public function __construct(
        Clue $clue,
        ClueDetail $clueDetail,
        ClueAttachments $clueAttachments
    )
    {
        parent::__construct();
        $this->clue = $clue;
        $this->clueDetail = $clueDetail;
        $this->clueAttachments = $clueAttachments;
    }

    /*
     * 存储线索
     */
    public function saveClue(array $data)
    {
        return $this->clue->insertUpdateBatch($data);
    }

    /**
     * 通过编号查询线索信息
     * @param array $condition
     * @return array
     */
    public function getClueByNumber(array $condition)
    {
        $result = [];

        $number = $condition['number'] ?? '';
        if(! $number) return $result;

        $query = $this->clue
            ->where('number', $number)
            ->first();

        if($query){
            $result = $query->toArray();
        }

        return $result;
    }

    public function saveClueDetail(array $data)
    {
        return $this->clueDetail->insertUpdateBatch($data);
    }

    public function saveClueAttachments(array $data)
    {
        return $this->clueAttachments->insertUpdateBatch($data);
    }
}