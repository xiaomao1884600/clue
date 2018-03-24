<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/21
 * Time: 下午11:17
 */

namespace App\Repository\Clue;


use App\Model\Clue\Clue;
use App\Model\Clue\ClueDetail;
use App\Repository\Foundation\BaseRep;

class ClueSearchRep extends BaseRep
{
    protected $clue;

    protected $clueDetail;

    public function __construct(
        Clue $clue,
        ClueDetail $clueDetail
    )
    {
        parent::__construct();
        $this->clue = $clue;
        $this->clueDetail = $clueDetail;
    }

    /**
     * 关键字查询
     * @param array $condition
     */
    public function getClueKeyWordSearch(array $condition)
    {
        $table = $this->clue->getTableName();

        $query = $this->clue
                ->select();

        // TODO 查询关键字条件
        if(isset($condition['where']) && $condition['where']){
            foreach($condition['where'] as $where){
                $query->where($where['field'], $where['operator'], $where['value']);
            }
        }

        // TODO 查询关键字条件
        if(isset($condition['orWhere']) && $condition['orWhere']){
            $query->where(function($query) use($condition){
                foreach($condition['orWhere'] as $k => $v){
                    $query->orWhere($k, $v);
                }
            });
        }

        if(isset($condition['whereBetween']) && $condition['whereBetween']){
            foreach($condition['whereBetween'] as $whereBetween){
                $query->whereBetween($whereBetween['field'], [$whereBetween['between'], $whereBetween['and']]);
            }
        }

        // whereIn
        if(isset($condition['whereIn']) && $condition['whereIn']){
            foreach($condition['whereIn'] as $whereIn){
                $query->whereIn($whereIn['field'], $whereIn['in']);
            }
        }

        // 排序
        if(isset($condition['orders']) && $condition['orders']){
            foreach($condition['orders'] as $orders){
                list($k, $v) = $orders;
                $query->orderBy($k, $v);
            }
        }

        $condition['size'] = _isset($condition, 'size', PAGESIZE);

        $result = $query->paginate($condition['size'])->toArray();

        return $result;
    }
}