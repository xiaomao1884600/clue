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
        //处理搜索条件
        $params = [
            'beginDate' => '2018-03-15',
            'endDate' => '2018-03-20',
            'source' => '省十一巡交办',
            'orders' => [
                [
                    'column' => 'clue_id',
                    'order' => 0//1升0降,默认升序
                ],
                [
                    'column' => 'pk_id',
                    'order' => 1
                ]
            ],
            'export' => 0//是否导出，默认0，即为列表查询，1触发导出
        ];
        //检查查询日期是否正确
        $this->checkSearchDate($params);
        //拼装最终搜索条件
        $condition = $this->processSearchCondition($params);
        //执行查询
        $res = $this->closedRep->getClosedList($condition);
        //导出功能
        if(isset($params['export']) && $params['export']){

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
                $condition['order'][$val['column']] = ($val['order'] == 1) ? 1 : 0;
            }
        }
        $condition['source'] = $params['source'];
        $condition['begin'] = $params['beginDate'] ? $params['beginDate'] . ' 00:00:00' : 0;
        $condition['end'] = $params['endDate'] ? $params['endDate'] . ' 23:59:59' : 0;
        return $condition;
    }

    public function closedClueExport(array $cellData)
    {
        Excel::create('学生成绩',function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }
}