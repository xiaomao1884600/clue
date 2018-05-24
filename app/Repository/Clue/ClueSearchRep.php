<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/21
 * Time: 下午11:17
 */

namespace App\Repository\Clue;


use App\Model\Cases\CaseClue;
use App\Model\Cases\Filing;
use App\Model\Clue\Clue;
use App\Model\Clue\ClueDetail;
use App\Model\Document\Document;
use App\Model\Register\Register;
use App\Repository\Foundation\BaseRep;

class ClueSearchRep extends BaseRep
{
    protected $clue;

    protected $clueDetail;

    protected $document;

    protected $caseClue;

    protected $filing;

    protected $register;

    public function __construct(
        Clue $clue,
        ClueDetail $clueDetail,
        Document $document,
        CaseClue $caseClue,
        Filing $filing,
        Register $register
    )
    {
        parent::__construct();
        $this->clue = $clue;
        $this->clueDetail = $clueDetail;
        $this->document = $document;
        $this->caseClue = $caseClue;
        $this->filing = $filing;
        $this->register = $register;
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
                    $query->orWhere($k, 'like', "%" . $v . "%");
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

        //debuger($query->toSql());

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

        // TODO 公文的关联改为标题匹配
        $result = $this->document
                ->where('document_title','like', "%" . $reflectedName . "%")
                ->orderBy('document_date', 'DESC')
                ->paginate($size)
                ->toArray();

        return $result;
    }

    /**
     * 获取案件线索被反映人数据信息
     * @param array $condition
     * @return array
     */
    public function getCaseClueByReflectedName(array $condition)
    {
        $reflectedName = _isset($condition, 'reflected_name');
        $size = _isset($condition, 'size', PAGESIZE);
        if(! $reflectedName) return [];

        $result = $this->caseClue
            ->where('reflected_name', 'like',"%" . $reflectedName . "%")
            ->orderBy('clue_accept_time', 'DESC')
            ->paginate($size)
            ->toArray();

        return $result;
    }

    /**
     * 获取案件立案被反映人数据信息
     * @param array $condition
     * @return array
     */
    public function getCaseFilingByReflectedName(array $condition)
    {
        $reflectedName = _isset($condition, 'reflected_name');
        $size = _isset($condition, 'size', PAGESIZE);
        if(! $reflectedName) return [];
        // TODO 后期改为立案的数据
        $result = $this->filing
            ->where('reflected_name', 'like', "%" . $reflectedName . "%")
            ->orderBy('trial_accept_time', 'DESC')
            ->paginate($size)
            ->toArray();

        return $result;
    }

    /**
     * 获取登记发放被反映人数据信息
     * @param array $condition
     * @return array
     */
    public function getRegisterByReflectedName(array $condition)
    {
        $reflectedName = _isset($condition, 'reflected_name');
        $size = _isset($condition, 'size', PAGESIZE);
        if(! $reflectedName) return [];
        // TODO 后期改为立案的数据
        $result = $this->register
            ->where('reflected_name', 'like', "%" . $reflectedName . "%")
            ->orderBy('entry_time', 'DESC')
            ->paginate($size)
            ->toArray();

        return $result;
    }
}