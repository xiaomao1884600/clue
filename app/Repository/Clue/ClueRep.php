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
use App\Model\Clue\ClueDeleted;
use App\Model\Clue\ClueDetail;
use App\Repository\Foundation\BaseRep;

class ClueRep extends BaseRep
{
    protected $clue;

    protected $clueDetail;

    protected $clueAttachments;

    protected $clueDeleted;

    public function __construct(
        Clue $clue,
        ClueDetail $clueDetail,
        ClueAttachments $clueAttachments,
        ClueDeleted $clueDeleted
    )
    {
        parent::__construct();
        $this->clue = $clue;
        $this->clueDetail = $clueDetail;
        $this->clueAttachments = $clueAttachments;
        $this->clueDeleted = $clueDeleted;
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

        $query = $this->clueAttachments
                ->select(
                    'id', 'clue_id', 'attachment_type', 'filename', 'file_path',
                    'file_extension', 'file_id'
                )
                ->where('clue_id', $clueId);

        // 获取指定文件信息
        if(isset($condition['file_id']) && $condition['file_id']){
            $query->whereIn('file_id', convertToArray($condition['file_id']));
        }

        $result = $query->get()->toArray();

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
        $clueId = convertToArray($clueId);
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
        $clueId = convertToArray($clueId);
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
        $fileId = convertToArray(_isset($condition, 'file_id', 0));
        if(! $clueId) return [];

        $query = $this->clueAttachments
                ->where('clue_id', $clueId);

        if($fileId){
            $query->whereIn('file_id', $fileId);
        }

        return $query->delete();
    }

    /**
     * 保存线索删除信息
     * @param array $data
     * @return string
     */
    public function saveClueDeleted(array $data)
    {
        return $this->clueDeleted->insertUpdateBatch($data);
    }

    
    /**
     * 超期提醒列表
     * 
     * @param array $data
     * @return type
     */
    public function getOverdueRemind(array $data)
    {
        $table = $this->clue->getTableName();
        $pagesize = isset($data['pagesize']) && $data['pagesize'] ?: 1;
        $page = isset($data['page']) && $data['page'] ?: 2;
        $query = $this->clue
            ->select($table . '.clue_id', $table . '.source', $table . '.number', $table . '.reflected_name',
                $table . '.closed_time', $table . '.remind_days', $table . '.remind_days');
        $query->orderBy($table . '.remind_days');
        $query->orderBy($table . '.closed_time');
        $total = $query->count();
        $query->take($pagesize);
        $query->skip(($page - 1) * $pagesize);
        $query = $query->get();
        return $query && count($query) ? ['data' => $query->toArray(), 'total' => $total] : ['data' => [], 'total' => 0];
    }

    /**
     * 获取线索信息
     * @param array $condition
     */
    public function getClueInfoByClueId(array $condition)
    {
        $result = [];

        $clueId = isset($condition['clue_id']) ? convertToArray($condition['clue_id']) : [];
        if(! $clueId) return $result;
        $table = $this->clue->getTableName();
        $table2 = $this->clueDetail->getTableName();

        $result = $this->clue
                ->select(
                    $table . '.*', $table2 . '.main_content', $table2 . '.department_opinion',
                    $table2 . '.leader_approval', $table2 . '.remark'
                )
                ->leftJoin($table2, $table . '.clue_id', '=', $table2 . '.clue_id')
                ->whereIn($table . '.clue_id', $clueId)
                ->get()
                ->toArray();

        return $result;
    }
}