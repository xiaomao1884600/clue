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
use App\Model\Document\Document;
use App\Repository\Foundation\BaseRep;

class ClueSearchRep extends BaseRep
{
    protected $clue;

    protected $clueDetail;

    protected $document;

    public function __construct(
        Clue $clue,
        ClueDetail $clueDetail,
        Document $document
    )
    {
        parent::__construct();
        $this->clue = $clue;
        $this->clueDetail = $clueDetail;
        $this->document = $document;
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
        if(isset($condition['orderBy']) && $condition['orderBy']){
            foreach($condition['orderBy'] as $orderBy){
                $query->orderBy($orderBy['field'], $orderBy['order']);
            }
        }

        $condition['size'] = _isset($condition, 'size', PAGESIZE);

        $result = $query->paginate($condition['size'])->toArray();

        return $result;
    }

    /**
     * 获取被反映人公文
     * @param array $condition
     * @return array
     */
    public function getDocumentByReflectedName(array $condition)
    {
        $reflectedName = _isset($condition, 'reflected_name');
        $size = _isset($condition, 'size', PAGESIZE);
        if(! $reflectedName) return [];

        $result = $this->document
                ->where('username', $reflectedName)
                ->orderBy('document_date', 'DESC')
                ->paginate($size)
                ->toArray();

        return $result;
    }
}