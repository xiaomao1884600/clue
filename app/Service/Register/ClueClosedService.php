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
        [
            'number' => '编号',
            'reflected_name' => '被反映人',
            'company' => '工作单位及职务',
            'main_content' => '反应的主要问题',
            'leader_approval' => '领导批示',
            'remark' => '备注'
        ]
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
        //检查查询日期是否正确
        $this->checkSearchDate($params);
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
                    'number' => $val['number'],
                    'reflected_name' => $val['reflected_name'],
                    'company' => $val['company'] .'—'. $val['post'],
                    'main_content' => $val['main_content'],
                    'leader_approval' => $val['leader_approval'],
                    'remark' => $val['remark']
                ];
            }
            $this->closedClueExport($data);
        }
        return $res;
    }

    /**
     * 校验搜索日期
     *
     * @param array $params
     * @throws \Exception
     */
    public function checkSearchDate(array $params)
    {
        $beginDateline =  $params['beginDate'] ? strtotime($params['beginDate'] . ' 00:00:00') : 0;
        $endDateline =  $params['endDate'] ? strtotime($params['endDate'] . '23:59:59') : 0;
        if($endDateline && $beginDateline > $endDateline){
            throw new \Exception("开始日期不能晚于结束日期");
        }
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
        if(is_array($params['orders']) && !empty($params['orders'])){
            foreach($params['orders'] as $val){
                $condition['order'][$val['column']] = (int)$val['order'];
            }
        }
        $condition['source'] = $params['source'];
        $condition['begin'] = $params['beginDate'] ? $params['beginDate'] . ' 00:00:00' : 0;
        $condition['end'] = $params['endDate'] ? $params['endDate'] . ' 23:59:59' : 0;
        $condition['page'] = (isset($params['page']) && $params['page']) ? (int)$params['page'] : 1;
        $condition['pagesize'] = (isset($params['pagesize']) && $params['pagesize']) ? (int)$params['pagesize'] : 10;
        return $condition;
    }

    /**
     * 已结案线索导出
     * 
     * @param array $cellData
     */
    public function closedClueExport(array $cellData)
    {
        $cellData = array_merge_recursive($this->closedheader, $cellData);
        Excel::create('已结案线索',function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }
}