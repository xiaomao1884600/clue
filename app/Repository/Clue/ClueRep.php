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

    /**
     * 获取线索
     * @param array $condition
     * @return array
     */
    public function getClueByClueId(array $condition)
    {
        $result = [];

        $clueId = $condition['clue_id'] ?? '';
        if(! $clueId) return $result;

        $query = $this->clue
            ->where('clue_id', $clueId)
            ->first();

        if($query){
            $result = $query->toArray();
        }

        return $result;
    }

    /**
     * 获取线索详情
     * @param array $condition
     * @return array
     */
    public function getClueDetailByClueId(array $condition)
    {
        $result = [];

        $clueId = $condition['clue_id'] ?? '';
        if(! $clueId) return $result;

        $query = $this->clueDetail
            ->where('clue_id', $clueId)
            ->first();

        if($query){
            $result = $query->toArray();
        }

        return $result;
    }

    /**
     * 获取线索附件信息
     * @param array $condition
     * @return array
     */
    public function getClueAttachmentsByClueId(array $condition)
    {
        $result = [];

        $clueId = $condition['clue_id'] ?? '';
        if(! $clueId) return $result;

        $result = $this->clueAttachments
                ->select(
                    'id', 'clue_id', 'attachment_type', 'filename', 'file_path',
                    'file_extension', 'file_id'
                )
                ->where('clue_id', $clueId)
                ->get()
                ->toArray();

        return $result;
    }

    /**
     * 删除线索信息
     * @param array $condition
     * @return array|bool|null
     */
    public function deleteClue(array $condition)
    {
        $clueId = _isset($condition, 'clue_id');
        $clueId = converToArray($clueId);
        if(! $clueId) return [];

        return $this->clue
                ->whereIn('clue_id', $clueId)
                ->delete();
    }

    /**
     * 删除线索详情
     * @param array $condition
     * @return array|bool|null
     */
    public function deleteClueDetail(array $condition)
    {
        $clueId = _isset($condition, 'clue_id');
        $clueId = converToArray($clueId);
        if(! $clueId) return [];

        return $this->clueDetail
            ->whereIn('clue_id', $clueId)
            ->delete();
    }

    /**
     * 删除线索相关附件信息
     * @param array $condition
     * @return array|bool|null
     */
    public function deleteClueAttachments(array $condition)
    {
        $clueId = _isset($condition, 'clue_id');
        $ids = convertToArray(_isset($condition, 'id', 0));
        if(! $clueId) return [];

        $query = $this->clueAttachments
                ->where('clue_id', $clueId);

        if($ids){
            $query->whereIn('id', $ids);
        }

        return $query->delete();
    }
}