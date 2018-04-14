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

    protected $clueDic;

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

    /**
     * 线索字典
     * @var array
     */
    protected $clueDicField = [
        'source',
        'disposal_type',
        'clue_state',
        'supervisor',
    ];

    protected $caseClueField = [
        'pk_id',
        'case_clue_id',
        'clue_source',
        'clue_number',
        'user_number',
        'clue_agency',
        'clue_accept_time',
        'clue_disposal_type',
        'clue_disposal_approval_time',
        'reflected_name',
        'company',
        'post',
        'level',
        'cadre_auth',
        'department',
        'station',
        'units_event',
        'clue_supervisor',
    ];

    protected $caseFilingField = [
        'id',
        'source',
        'number',
        'reflected_name',
        'company',
        'post',
        'level',
        'disposal_type',
        'trial_accept_time',
        'case_num',
        'trial_accept_time',
        'case_user_num',
        'trial_office',
        'end_time',
    ];

    public function __construct(
        ClueSearchRep $clueSearchRep,
        ClueDic $clueDic
    )
    {
        parent::__construct();
        $this->clueSearchRep = $clueSearchRep;
        $this->clueDic = $clueDic;
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
        
        if (!$condition) return [];

        $condition['size'] = _isset($params, 'size', PAGESIZE);

        // 查询线索信息
        $result = $this->getClueKeyWordSearch($condition);

        $result = Response::responsePaginate($result, $result['data']);

        return $result;
    }

    /**
     * 高级搜索
     * @param array $params
     * @return array
     */
    public function clueAdvancedSearch(array $params)
    {
        $condition = [];

        // 设置页码
        $this->setPage($params);

        // 测试
        //$params = $this->testParams;

        $condition = $this->setAdvancedSearchCondition($params);

        if (!$condition) return [];

        $condition['size'] = _isset($params, 'size', PAGESIZE);

        // 查询线索信息
        $result = $this->getClueKeyWordSearch($condition);

        $result = Response::responsePaginate($result, $result['data']);

        return $result;
    }

    /**
     * 设置分页参数
     * @param array $params
     * @return mixed|string
     */
    protected function setPage(array $params)
    {
        $index = _isset($params, 'index', '1');
        request()->offsetSet('page', $index);

        return $index;
    }

    protected function getKeyWordCondition(string $keyWord)
    {
        $condition = [];
        $keyWord = trim($keyWord);
        if(! $keyWord) return $condition;

        $conditon =  [
            'source' => $keyWord,
            'number' => $keyWord,
            'reflected_name' => $keyWord,
            'company' => $keyWord,
            'post' => $keyWord,
            'level' => $keyWord,
            'disposal_type' => $keyWord,
            'clue_next' => $keyWord,
            'supervisor' => $keyWord

        ];

//        // 检测是否上级交办搜索
//        if($keyWord == '上级交办'){
//            $conditon['supervisor'] = 1;
//        }

        return $conditon;
    }

    /**
     * 设置关键字查询条件
     * @param array $params
     * @return array
     */
    protected function setKeyWordSearchCondition(array $params)
    {
        $keyWord = trim(_isset($params, 'keyword'));

        $orders = _isset($params, 'orders');

        // TODO 处理检索条件
        $conditon = [
            'orWhere' => [],
            'orderBy' => [],
        ];

        if($keyWord){
            $conditon['orWhere'] = $this->getKeyWordCondition($keyWord);
        }


        if ($orders) {
            foreach ($orders as $k => $v) {
                $v['order'] = (int)$v['order'];
                $conditon['orderBy'][] = ['field' => $v['column'], 'order' => self::ORDER_TYPE[$v['order']]];
            }
        }

        $conditon['orderBy'][] = ['field' => 'entry_time', 'order' => self::ORDER_TYPE[self::ORDER_DESC]];

        return $conditon;
    }

    /**
     * 高级搜索条件
     * @param array $params
     * @return array
     */
    protected function setAdvancedSearchCondition(array $params)
    {
        $keyWord = trim(_isset($params, 'keyword'));

        $index = _isset($params, 'index', '1');
        request()->offsetSet('page', $index);

        $orders = _isset($params, 'orders');

        // TODO 处理检索条件
        $conditon = [];
        $conditon['orWhere'] = [];
        // 关键字搜索条件
        if($keyWord){
            $conditon['orWhere'] = $this->getKeyWordCondition($keyWord);
        }

        // whereBetween条件
        if (_isset($params, 'entry_start_time')) {
            $entryEndTime = _isset($params, 'entry_end_time', getTodayDate());
            $conditon['whereBetween'][] = ['field' => 'entry_time', 'between' => $params['entry_start_time'], 'and' => $entryEndTime];
        }

        // 线索来源
        if (_isset($params, 'source')) {
            $conditon['where'][] = ['field' => 'source', 'operator' => 'like', 'value' => "%" . _isset($params, 'source') . "%"];
        }

        // 单位
        if (_isset($params, 'company')) {
            $conditon['where'][] = ['field' => 'company', 'operator' => 'like', 'value' => "%" . _isset($params, 'company') . "%"];
        }

        // 级别
        if (_isset($params, 'level')) {
            $conditon['where'][] = ['field' => 'level', 'operator' => 'like', 'value' => "%" . _isset($params, 'level') . "%"];
        }

        // 职位
        if (_isset($params, 'post')) {
            $conditon['where'][] = ['field' => 'post', 'operator' => 'like', 'value' => "%" . _isset($params, 'post') . "%"];
        }

        // 状态
        if (_isset($params, 'clue_state')) {
            $conditon['where'][] = ['field' => 'clue_state', 'operator' => '=', 'value' => _isset($params, 'clue_state')];
        }


        /**
         * whereIn 条件
         */

        // 职位
        if (isset($params['whereIn']['post'])) {
            $conditon['whereIn'][] = ['field' => 'post', 'in' => convertToArray($params['whereIn']['post'])];
        }

        // 状态
        if (isset($params['whereIn']['clue_state'])) {
            $conditon['whereIn'][] = ['field' => 'clue_state', 'in' => convertToArray($params['whereIn']['clue_state'])];
        }

        if ($orders) {
            foreach ($orders as $k => $v) {
                $v['order'] = (int)$v['order'];
                $conditon['orderBy'][] = ['field' => $v['column'], 'order' => self::ORDER_TYPE[$v['order']]];
            }
        }

        $conditon['orderBy'][] = ['field' => 'entry_time', 'order' => self::ORDER_TYPE[self::ORDER_DESC]];

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

    /**
     * 获取被反映人线索、公文、案件等信息
     * @param array $params
     * @throws \Exception
     */
    public function getClueByReflectedName(array $params)
    {
        $responseData = [];

        $reflectedName = _isset($params, 'reflected_name');
        $condition = ['reflected_name' => $reflectedName];

        if (!$reflectedName) {
            throw new \Exception('reflected_name does not null');
        }

        // 获取线索信息
        $responseData['clue'] = $this->getClueInfoByReflectedName($condition);

        // 获取公文信息
        $responseData['document'] = $this->getDocumentByReflectedName($condition);

        // TODO 获取案件信息，临时测试信息
        // 案件线索
        $responseData['case']['case_clue'] = $this->getCaseClueByReflectedName($condition);
        // 立案信息
        $responseData['case']['case_filing'] = $this->getCaseFilingByReflectedName($condition);

        return $responseData;
    }

    /**
     * 获取被反映人线索信息
     * @param array $params
     * @return array
     */
    protected function getClueInfoByReflectedName(array $params)
    {
        $data = [];
        // 设置页码
        $this->setPage($params);

        $condition = [];
        $condition = [
            'where' => [
                ['field' => 'reflected_name', 'operator' => 'like', 'value' => "%" . $params['reflected_name'] . "%"],
            ],
            'orderBy' => [
                ['field' => 'entry_time', 'order' => self::ORDER_TYPE[self::ORDER_DESC]],
            ],
        ];
        $result = $this->clueSearchRep->getClueKeyWordSearch($condition);
        $result['data'] = $this->processClueInfo($result['data']);

        // 处理字典信息
        $result['data'] = $this->processClueDic($result['data']);

        $data = Response::responsePaginate($result, $result['data']);

        return $data;
    }

    /**
     * 处理线索字典信息
     * @param array $data
     */
    protected function processClueDic(array $data)
    {
        return $this->clueDic->convertClueDic($data);
    }

    /**
     * 获取被反映人公文信息
     * @param array $params
     * @return array
     */
    protected function getDocumentByReflectedName(array $params)
    {
        // 设置页码
        $this->setPage($params);

        $result = $this->clueSearchRep->getDocumentByReflectedName(['reflected_name' => $params['reflected_name']]);

        return Response::responsePaginate($result, $result['data']);
    }

    /**
     * 获取案件问题线索
     * @param array $params
     * @return array
     */
    protected function getCaseClueByReflectedName(array $params)
    {
        // 设置页码
        $this->setPage($params);

        $result = $this->clueSearchRep->getCaseClueByReflectedName(['reflected_name' => $params['reflected_name']]);
        $result['data'] = $result['data'] ? screenArray($result['data'], $this->caseClueField) : [];

        return Response::responsePaginate($result, $result['data']);
    }

    /**
     * 获取案件立案信息
     * @param array $params
     * @return array
     */
    protected function getCaseFilingByReflectedName(array $params)
    {
        // 设置页码
        $this->setPage($params);

        $result = $this->clueSearchRep->getCaseFilingByReflectedName(['reflected_name' => $params['reflected_name']]);
        $result['data'] = $result['data'] ? screenArray($result['data'], $this->caseFilingField) : [];

        return Response::responsePaginate($result, $result['data']);
    }
}