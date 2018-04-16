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
        $orders = isset($params['order']) ? $params['order'] : [];
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
        if(!empty($orders)){
            foreach ($orders as $c => $o){
                if($o == 0){
                    if(in_array($c, ['number', 'reflected_name', 'company', 'post', 'level'])){
                        $query->orderBy($table.'.'.$c, 'DESC');
                    }else if(in_array($c, ['main_content', 'leader_approval', 'remark'])){
                        $query->orderBy($table2.'.'.$c, 'DESC');
                    }
                }
            }
        }
        $total = $query->count();
        if(!$isAll){
            $query->take($params['pagesize']);
            $query->skip(($params['page'] - 1) * $params['pagesize']);
        }
        $query = $query->get();
        return $query && count($query) ? ['data' => $query->toArray(), 'total' => $total] : ['data' => [], 'total' => 0];
    }
}