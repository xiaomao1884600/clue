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
use DB;

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
        $clueId = $condition['clue_id'] ?? '';
        $clueId = convertToArray($clueId);
        if(! $number) return $result;

        $query = $this->clue
            ->where('number', $number)
            ->whereNotIn('clue_id', $clueId)
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
    public function getOverdueRemind(array $params)
    {
        //排序
        $orders = isset($params['order']) ? $params['order'] : [];
        $order = '';
        if(!empty($orders)){
            //防止参数错传，获取表结构进行验证
            $tableRows = $this->clue->getTableDesc('t_clue');
            foreach ($orders as $c => $o){
                if($o == 0 && array_key_exists($c, $tableRows))
                    $order .= 'c.' . $c . ' DESC,';
            }
            $order = rtrim($order, ',');
        }
        $res = DB::select("
            SELECT c.clue_id, c.source_dic, c.source, c.number, c.reflected_name, c.closed_time, a.days FROM (
                SELECT pk_id, CEILING((UNIX_TIMESTAMP(closed_time) - UNIX_TIMESTAMP()) / 86400) AS days FROM t_clue
            ) a
            INNER JOIN t_clue AS c ON c.pk_id = a.pk_id AND a.days <= c.remind_days
            WHERE c.clue_state <> 1
            ".((isset($params['begin']) && $params['begin']) ? "AND c.closed_time >= '{$params['begin']}'" : '')."
            ".((isset($params['end']) && $params['end']) ? "AND c.closed_time <= '{$params['end']}'" : '')."
            ".(($params['temp'] === true) ? "AND a.days >= 0" : '')."
            ".($order ? " ORDER BY ".$order : '')."
            LIMIT ".($params['page'] - 1) * $params['pagesize'].", ".$params['pagesize']."
        ");
        return array_map('get_object_vars', $res);
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

    /**
     * 通过编号查询线索信息
     * @param array $condition
     * @return array
     */
    public function checkClueByNumber(array $condition)
    {
        $result = [];

        $number = $condition['number'] ?? '';
        $number = convertToArray($number);
        if(! $number) return $result;

        $result = $this->clue
            ->whereIn('number', $number)
            ->get()
            ->toArray();

        return $result;
    }

    /**
     * 线索结办
     * @param array $condition
     * @return array|bool|null
     */
    public function setClueClosed(array $condition)
    {
        $clueId = _isset($condition, 'clue_id');
        $clueId = convertToArray($clueId);
        $update = $condition['update'] ?? [];
        if(! $clueId || ! $update) return [];
        return $this->clue
            ->whereIn('clue_id', $clueId)
            ->update($update);
    }

    /**
     * 通过编号删除线索
     * @param array $condition
     * @return array
     */
    public function clearClueByNumber(array $condition)
    {
        $result = [];

        $number = $condition['number'] ?? '';
        $number = convertToArray($number);
        if(! $number) return $result;

        $result = $this->clue
            ->whereIn('number', $number)
            ->delete();

        return $result;
    }
    
    /**
     * 超期提醒数据
     * 
     * @param array $data
     * @return type
     */
    public function getRemindTotal(array $params)
    {
        $res = DB::select("
            SELECT COUNT(*) AS total FROM (
                SELECT pk_id, CEILING((UNIX_TIMESTAMP(closed_time) - UNIX_TIMESTAMP()) / 86400) AS days FROM t_clue
            ) a
            INNER JOIN t_clue AS c ON c.pk_id = a.pk_id AND a.days <= c.remind_days
            WHERE c.clue_state <> 1
            ".((isset($params['begin']) && $params['begin']) ? "AND c.closed_time >= '{$params['begin']}'" : '')."
            ".((isset($params['end']) && $params['end']) ? "AND c.closed_time <= '{$params['end']}'" : '')."
            ".(($params['temp'] === true) ? "AND a.days >= 0" : '')."
        ");
        return $res[0] ? $res[0]->total : 0;
    }
}