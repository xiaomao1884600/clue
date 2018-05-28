<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/20
 * Time: 下午9:59
 */
namespace App\Service\Register;

use App\Service\Foundation\BaseService;
use App\Repository\Register\ClueClosedRep;
use Excel;

/**
 * 已结线索服务
 * Class ClueClosedService
 * @package App\Service\Register
 */

class ClueClosedService extends BaseService
{
    protected $closedheader = [
        'number' => '编号',
        'reflected_name' => '被反映人',
        'company' => '工作单位及职务',
        'level' => '级别',
        'source' => '线索来源I',
        'source_dic' => '线索来源II',
        'clue_next' => '承办单位',
        'undertake_leader' => '承办领导',
        'progress' => '进展情况',
        'leader_approval' => '集体排查意见及领导批示',
        'main_content' => '主要内容',
        'signatory' => '领取人签字',
        'remark' => '备注'
    ];
    
    public function __construct(ClueClosedRep $clueClosedRep)
    {
        parent::__construct();
        $this->closedRep = $clueClosedRep;
    }

    /**
     * 获取已结线索列表
     *
     * @param array $params
     * @return array
     */
    public function getClosedListService(array $params){
        //拼装最终搜索条件
        $condition = $this->processSearchCondition($params);
        $isAll = false;
        if(isset($params['export']) && $params['export']) $isAll = true;
        //执行查询
        $res = $this->closedRep->getClosedList($condition, $isAll);
        //导出功能
        if($isAll && !empty($res['data'])){
            //过滤多余字段
            $data = [];
            foreach($res['data'] as $val){
                $data[] = [
                    'clue_id' => $val['clue_id'],
                    'number' => $val['number'],
                    'reflected_name' => $val['reflected_name'],
                    'company' => $val['company'] .'—'. $val['post'],
                    'clue_next' => $val['clue_next'],
                    'progress' => $val['progress'],
                    'source' => $val['source'],
                    'source_dic' => $val['source_dic'],
                    'level' => $val['level'],
                    'leader_approval' => $val['leader_approval'],
                    'main_content' => $val['main_content'],
                    'signatory' => $val['signatory'],
                    'undertake_leader' => $val['undertake_leader'],
                    'remark' => $val['remark'],
                ];


            }
            $fields = $params['fields'] ?? [];//选中字段
            $this->closedClueExport($data, $fields);
        }
        return $res;
    }

    /**
     * 处理搜索条件
     *
     * @param array $params
     * @return array
     */
    public function processSearchCondition(array $params)
    {
        $condition = [];
        if(isset($params['orders']) && is_array($params['orders']) && !empty($params['orders'])){
            foreach($params['orders'] as $val){
                $condition['order'][$val['column']] = (int)$val['order'];
            }
        }
        $condition['clue_next'] = $params['clue_next'] ?? '';
        $condition['begin'] = isset($params['beginDate']) && $params['beginDate'] ? $params['beginDate'] . ' 00:00:00' : 0;
        $condition['end'] = isset($params['endDate']) && $params['endDate'] ? $params['endDate'] . ' 23:59:59' : 0;
        $condition['keywords'] = $params['keywords'] ?? '';
        $condition['page'] = (isset($params['page']) && $params['page']) ? (int)$params['page'] : 1;
        $condition['pagesize'] = (isset($params['pagesize']) && $params['pagesize']) ? (int)$params['pagesize'] : 10;
        return $condition;
    }

    /**
     * 已结案线索导出
     * 
     * @param array $cellData
     */
    public function closedClueExport(array $cellData, $fields = [])
    {
        $cellData = excelExportSort($cellData, $this->closedheader, true);
        if(!empty($fields)){
            foreach($cellData as $k => &$v){
                foreach($v as $k2 => $v2){
                    if(!in_array($k2, $fields)){
                        unset($v[$k2]);
                    }
                }
            }
        }
        Excel::create('已结案线索',function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }
}