<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/4/7
 * Time: 下午10:47
 */
namespace App\Repository\Cases;

use App\Model\Cases\CaseClue;
use App\Model\Cases\Filing;
use App\Repository\Foundation\BaseRep;

class CaseRep extends BaseRep
{
    protected $caseClue;

    protected $filing;

    public function __construct(
        CaseClue $caseClue,
        Filing $filing
    )
    {
        parent::__construct();
        $this->caseClue = $caseClue;
        $this->filing = $filing;
    }

    /**
     * 保存案件问题线索
     * @param array $data
     * @return string
     */
    public function saveCaseClue(array $data)
    {
        return $this->caseClue->insertUpdateBatch($data);
    }

    /**
     * 保存立案
     * @param array $data
     * @return string
     */
    public function saveFiling(array $data)
    {
        return $this->filing->insertUpdateBatch($data);
    }
    
    /**
     * 立案案件登记数据获取
     * 
     * @param type $params
     * @param type $mark
     * @return type
     */
    public function getCaseListRep($params = [], $mark = false)
    {
        $table = $this->filing->getTableName();
        $pagesize = (isset($params['pagesize']) && $params['pagesize']) ? $params['pagesize'] : 10;
        $page = (isset($params['page']) && $params['page']) ? $params['page'] : 1;
        $column = !$mark ? ['dw_code', 'dw_title', 'case_num', 'case_user_num', 'reflected_name', 'gender'] : '*';
        $query = $this->filing
            ->select($column);
        if(isset($params['case_num']) && $params['case_num']){
            
            $query->where($table.'.case_num', '=', $params['case_num']);
        }
        if($mark){
            $query = $query->get();
            return $query && count($query) ? $query->toArray() : [];
        }else{
            $total = $query->count();
            $query->take($pagesize);
            $query->skip(($page - 1) * $pagesize);
            $query = $query->get();
            return $query && count($query) ? ['data' => $query->toArray(), 'total' => $total] : ['data' => [], 'total' => 0];
        }
    }
    
    /**
     * 问题线索处置情况数据获取
     * 
     * @param type $params
     * @param type $mark
     * @return type
     */
    public function getProblemListRep($params = [], $mark = false)
    {
        $table = $this->caseClue->getTableName();
        $pagesize = (isset($params['pagesize']) && $params['pagesize']) ? $params['pagesize'] : 10;
        $page = (isset($params['page']) && $params['page']) ? $params['page'] : 1;
        $column = !$mark ? ['clue_source', 'clue_number', 'user_number', 'clue_agency'] : '*';
        $query = $this->caseClue
            ->select($column);
        if(isset($params['clue_number']) && $params['clue_number']){
            
            $query->where($table.'.clue_number', '=', $params['clue_number']);
        }
        if($mark){
            $query = $query->get();
            return $query && count($query) ? $query->toArray() : [];
        }else{
            $total = $query->count();
            $query->take($pagesize);
            $query->skip(($page - 1) * $pagesize);
            $query = $query->get();
            return $query && count($query) ? ['data' => $query->toArray(), 'total' => $total] : ['data' => [], 'total' => 0];
        }
    }
}