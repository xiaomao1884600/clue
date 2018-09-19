<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/20
 * Time: 下午10:09
 */
namespace App\Repository\Register;

use App\Repository\Foundation\BaseRep;
use App\Model\Register\Register;
use DB;

class ClueClosedRep extends BaseRep
{

    public function __construct(Register $register){
        $this->register = $register;
    }

    /**
     * 获取已结线索列表
     *
     * @param array $params
     * @return array
     */
    public function getClosedList(array $params, $isAll = false){
        $table = $this->register->getTableName();
        $tableRows = $this->register->getTableDesc($table);
        $orders = isset($params['order']) ? $params['order'] : [];
        $query = $this->register
            ->select($table.'.clue_id', $table.'.number', $table.'.reflected_name', $table.'.company', $table.'.post', $table.'.clue_next', $table.'.progress', $table.'.source',
                $table.'.level', $table.'.leader_approval', $table.'.main_content', $table.'.signatory', $table.'.undertake_leader', $table.'.source_dic', $table.'.remark');
        if(isset($params['clue_next']) && $params['clue_next']){
            $query->where($table.'.clue_next', '=', $params['clue_next']);
        }
        if(isset($params['keywords']) && $params['keywords']){

            $query->where(function($query)use($tableRows, $table, $params){
                foreach($tableRows as $key => $val){
                    if(!in_array($key, ['pk_id', 'entry_time', 'closed_time', 'created_at', 'updated_at'])){
                        $query->orWhere($table.'.'.$key, 'like', "%{$params['keywords']}%");
                    }
                }
            });

        }
        if(isset($params['begin']) && $params['begin']){
            $query->where($table.'.created_at', '>=', $params['begin']);
        }
        if(isset($params['end']) && $params['end']){
            $query->where($table.'.created_at', '<=', $params['end']);
        }
        if(!empty($orders)){
            foreach ($orders as $c => $o){
                if($o == 0 && array_key_exists($c, $tableRows)){
                    $query->orderBy($table.'.'.$c, 'DESC');
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