<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/21
 * Time: 下午10:13
 */

namespace App\Service\Clue;


use App\Repository\Clue\ClueSearchRep;
use App\Service\Foundation\BaseService;
use App\Service\Foundation\Response;

class ClueSearchService extends BaseService
{
    protected $clueSearchRep;

    const ORDER_ASC = 1;
    const ORDER_DESC = 0;

    // 排序类型
    const ORDER_TYPE = [
        self::ORDER_ASC => 'ASC',
        self::ORDER_DESC => 'DESC',
    ];

    protected $testParams = [
        'keyword' => 'zhangsan',
        'where' => [
            ['field' => 'source', 'operator' => '=', 'value' => '2'],
        ],
        'whereBetween' => [
            ['field' => 'entry_time', 'between' => '2018-03-16', 'and' => '2018-03-25'],
        ],

        'orders' => [
            ['column' => 'reflected_name', 'order' => 1],
            ['column' => 'source', 'order' => 1],
        ],
    ];

    public function __construct(
        ClueSearchRep $clueSearchRep
    )
    {
        parent::__construct();
        $this->clueSearchRep = $clueSearchRep;
    }

    /**
     * 关键字查询线索信息
     * @param array $params
     * @return array
     */
    public function clueKeyWordSearch(array $params)
    {
        $condition = [];

        // 设置页码
        $this->setPage($params);

        // 测试
        //$params = $this->testParams;

        // TODO 定义搜索关键字条件
        $condition = $this->setKeyWordSearchCondition($params);
        if(! $condition) return [];

        // 查询线索信息
        $result = $this->getClueKeyWordSearch($condition);

        $result = Response::responsePaginate($result, $result['data']);

        return $result;
    }

    public function clueAdvancedSearch(array $params)
    {
        $condition = [];

        // 设置页码
        $this->setPage($params);

        // 测试
        //$params = $this->testParams;

        $condition = $this->setAdvancedSearchCondition($params);

        if(! $condition) return [];

        // 查询线索信息
        $result = $this->getClueKeyWordSearch($condition);

        $result = Response::responsePaginate($result, $result['data']);

        return $result;
    }

    protected function setPage(array $params)
    {
        $index = _isset($params, 'index', '1');
        request()->offsetSet('page', $index);

        return $index;
    }

    /**
     * 设置关键字查询条件
     * @param array $params
     * @return array
     */
    protected function setKeyWordSearchCondition(array $params)
    {
        $keyWord = trim(_isset($params, 'keyword'));
        if(! $keyWord) return [];
        $orders = _isset($params, 'orders');

        // TODO 处理检索条件
        $conditon = [
            'orWhere' => [
                'source' => $keyWord,
                'number' => $keyWord,
                'reflected_name' => $keyWord,
                'company' => $keyWord,

            ],
            'orderBy' => [],
        ];

        if($orders){
            foreach ($orders as $k => $v){
                $conditon['orderBy'][] = [$v['column'] => self::ORDER_TYPE[$v['order']]];
            }
            $conditon['orderBy'][] = ['entry_time' => self::ORDER_DESC];
        }

        return $conditon;
    }

    /**
     * 高级搜索条件
     * @param array $params
     * @return array
     */
    protected function setAdvancedSearchCondition(array $params)
    {
        //$keyWord = trim(_isset($params, 'keyword'));

        $index = _isset($params, 'index', '1');
        request()->offsetSet('page', $index);

        $orders = _isset($params, 'orders');

        // TODO 处理检索条件
        $conditon = [];

        // whereBetween条件
        if(_isset($params, 'entry_start_time')){
            $entryEndTime = _isset($params, 'entry_end_time', getTodayDate());
            $conditon['whereBetween'][] = ['field' => 'entry_time', 'between' => $params['entry_start_time'], 'and' => $entryEndTime];
        }

        // 线索来源
        if(_isset($params, 'source')){
            $conditon['where'][] = ['field' => 'source', 'operator' => '=', 'value' => _isset($params, 'source')];
        }

        // 单位
        if(_isset($params, 'company')){
            $conditon['where'][] = ['field' => 'company', 'operator' => '=', 'value' => _isset($params, 'company')];
        }

        // 级别
        if(_isset($params, 'level')){
            $conditon['where'][] = ['field' => 'level', 'operator' => '=', 'value' => _isset($params, 'level')];
        }

        // 职位
        if(_isset($params, 'post')){
            $conditon['where'][] = ['field' => 'post', 'operator' => '=', 'value' => _isset($params, 'post')];
        }

        // 状态
        if(_isset($params, 'clue_state')){
            $conditon['where'][] = ['field' => 'clue_state', 'operator' => '=', 'value' => _isset($params, 'clue_state')];
        }


        /**
         * whereIn 条件
         */

        // 职位
        if(isset($params['whereIn']['post'])){
            $conditon['whereIn'][] = ['field' => 'post', 'in' => convertToArray($params['whereIn']['post'])];
        }

        // 状态
        if(isset($params['whereIn']['clue_state'])){
            $conditon['whereIn'][] = ['field' => 'clue_state', 'in' => convertToArray($params['whereIn']['clue_state'])];
        }

        if($orders){
            foreach ($orders as $k => $v){
                $conditon['orderBy'][] = [$v['column'] => self::ORDER_TYPE[$v['order']]];
            }
            $conditon['orderBy'][] = ['entry_time' => self::ORDER_DESC];
        }

        return $conditon;
    }

    /**
     * 关键字查询线索
     * @param array $condition
     */
    protected function getClueKeyWordSearch(array $condition)
    {
        $result = $this->clueSearchRep->getClueKeyWordSearch($condition);

        // TODO 数据
        $result['data'] = $this->processClueInfo($result['data']);

        return $result;
    }

    protected function processClueInfo(array $data)
    {
        // TODO 字典转化
        return $data;
    }
}