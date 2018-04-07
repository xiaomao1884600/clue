<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/20
 * Time: 下午10:09
 */
namespace App\Repository\Register;

use App\Repository\Foundation\BaseRep;
use App\Model\Clue\Clue;
use App\Model\Clue\ClueDetail;
use DB;

class ClueClosedRep extends BaseRep
{
    public $condition;
    public function __construct(
        Clue $clue,
        ClueDetail $clueDetail
    )
    {
        $this->clue = $clue;
        $this->clueDetail = $clueDetail;
    }

    /**
     * 获取已结线索列表
     *
     * @param array $params
     * @return array
     */
    public function getClosedList(array $params, $isAll = false){
        $table = $this->clue->getTableName();
        $table2 = $this->clueDetail->getTableName();
        $orders = isset($params['order']) ?: [];
        $pagesize = isset($params['pagesize']) && $params['pagesize'] ?: 5;
        $page = isset($params['page']) && $params['page'] ?: 1;
        $query = $this->clue
            ->select($table.'.number', $table.'.reflected_name', $table.'.company', $table.'.post',
                $table.'.level', $table2.'.main_content', $table2.'.leader_approval', $table2.'.remark')
            ->join($table2, $table2.'.clue_id', '=', $table.'.clue_id');
        if(isset($params['source']) && $params['source']){
            $query->where($table.'.source', '=', $params['source']);
        }
        if(isset($params['begin']) && $params['begin']){
            $query->where($table.'.entry_time', '>=', $params['begin']);
        }
        if(isset($params['end']) && $params['end']){
            $query->where($table.'.entry_time', '<=', $params['end']);
        }
        if(isset($orders['number']) && $params['number']){
            $query->orderBy($table.'.number', 'DESC');
        }else{
            $query->orderBy($table.'.number', 'ASC');
        }
        if(isset($orders['reflected_name']) && $params['reflected_name']){
            $query->orderBy($table.'.reflected_name', 'DESC');
        }else{
            $query->orderBy($table.'.reflected_name', 'ASC');
        }
        $total = $query->count();
        if(!$isAll){
            $query->take($pagesize);
            $query->skip(($page - 1) * $pagesize);
        }
        $query = $query->get();
        return $query && count($query) ? ['data' => $query->toArray(), 'total' => $total] : ['data' => [], 'total' => 0];
    }
}