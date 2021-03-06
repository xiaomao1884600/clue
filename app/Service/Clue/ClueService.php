<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/18
 * Time: 上午10:31
 */

namespace App\Service\Clue;


use App\Repository\Clue\ClueRep;
use App\Repository\Register\RegisterRep;
use App\Service\Foundation\BaseService;
use App\Service\Foundation\DicService;
use Illuminate\Support\Facades\Storage;

class ClueService extends BaseService
{
    protected $clueRep;

    protected $registerRep;

    protected $dicService;

    /**
     * 线索字段
     * @var array
     */
    protected $clueField = [
        'clue_id' => 'clue_id',
        'source_dic' => 'source_dic',
        'source' => 'source',
        'number' => 'number',
        'reflected_name' => 'reflected_name',
        'company' => 'company',
        'post' => 'post',
        'level' => 'level',
        'entry_time' => 'entry_time',
        'closed_time' => 'closed_time',
        'disposal_type' => 'disposal_type',
        'supervisor' => 'supervisor',
        'remind_days' => 'remind_days',
        'clue_next' => 'clue_next',
        'clue_state' => 'clue_state',
        'op_type' => 'op_type',

    ];

    /**
     * 线索附件信息
     * @var array
     */
    protected $clueAttachmentsField = [
        'attachment_type' => 'attachment_type',
        'newFileName' => 'filename',
        'originalName' => 'original_name',
        'file_path' => 'file_path',
        'extension' => 'file_extension',
        'file_id' => 'file_id',
    ];

    protected $testClueInfo = [
        'clue' => [
            'source_dic' => '线索来源字典', // 线索来源字典
            'source' => '线索来源输入框', // 线索来源
            'number' => TIMENOW, // 编号
            'reflected_name' => 'zhangsan', // 被反映人
            'company' => '天宇国际', // 单位
            'post' => '经理',  // 职位
            'level' => 1,  // 级别
            'entry_time' => '2018-03-17 10:00:00',  // 录入日期
            'closed_time' => '2018-05-20 10:00:00', // 结案日期
            'disposal_type' => '集体排查', // 处置类型
            'supervisor' => 1, // 是否上级交办
            'remind_days' => 8, // 提醒天数
            'clue_next' => '干部监督室', // 承办部门
            'clue_state' => '已办', // 线索状态
        ],
        'clue_detail' => [
            'main_content' => '严重违纪', // 主要内容
            'department_opinion' => '予以开除', // 部门意见
            'leader_approval' => '同意',  // 领导批示
            'remark' => '备注',  // 备注
        ],
        'clue_attachments' => [
            [
                'file_id' => '1', //附件id
                'originalName' => '测试.doc', // 附件原始名称
                'newFileName' => '测试_20180318.doc', // 文件名称
                'extension' => 'doc',  // 后缀
                'file_path' => 'uploads/aaa.doc', // 文件路径
                'attachment_type' => 'wrod', // 附件类型
            ],

            [
                'file_id' => '2',
                'originalName' => '测试2.png',
                'newFileName' => '测试2_20180318.png',
                'extension' => 'doc',
                'file_path' => 'uploads/aaa.png',
                'attachment_type' => 'img', // 附件类型(图片)
            ]
        ],
    ];

    public function __construct(
        ClueRep $clueRep,
        RegisterRep $registerRep,
        DicService $dicService
    )
    {
        parent::__construct();
        $this->clueRep = $clueRep;
        $this->registerRep = $registerRep;
        $this->dicService = $dicService;
    }

    public function saveClue(array $params)
    {
        // TODO 临时测试
        //$params = $this->testClueInfo;

        if(! isset($params['clue']) || ! $params['clue']){
            throw new \Exception('Incomplete clue information !');
        }

        // TODO 检测编号是否唯一
        if(current($this->checkClueNumber($params['clue']))){
            throw new \Exception('The number has already existed !');
        }

        // TODO 检测被反映人相关线索、案件、公文信息


        // 处理线索存储
        $clueId = $this->processSaveClue($params['clue']);

        // 处理线索详情存储
        $this->processSaveClueDetail($params['clue_detail'], $clueId);

        // 处理线索附件存储
        $this->processSaveClueAttachments($params['clue_attachments'], $clueId);

        // TODO 检测若为已办线索则转为登记发放
        if(isset($params['clue']['clue_state']) && '已办' == $params['clue']['clue_state']){
            $params['clue']['clue_id'] = $clueId;
            $this->convertClueToRegister($params);
        }

        return ['clue_id' => $clueId];
    }

    /**
     * 将已办线索转为登记发放
     * @param array $params
     */
    public function convertClueToRegister(array $params)
    {
        $clue = $params['clue'] ?? [];
        $clueDetail = $params['clue_detail'] ?? [];
        $register = array_merge($clue, $clueDetail);
        // 保存登记发放
        return $this->registerRep->saveRegister([$register]);
    }

    public function checkClueNumber(array $params)
    {
        $result = ['result' => false];

        $number = _isset($params, 'number');
        $clueId = $params['clue_id'] ?? '';

        if(! $number){
            throw new \Exception('The number is required !');
        }

        // 检测是否存在编号信息
        if($this->clueRep->getClueByNumber(['number' => $number, 'clue_id' => $clueId])){
            $result['result'] = true;
        };

        return $result;
    }

    /**
     * 通过编号获取线索信息
     * @param array $params
     * @return array
     */
    protected function getClueNumber(array $params)
    {
        $number = _isset($params, 'number');

        // 获取线索信息
        return $this->clueRep->getClueByNumber(['number' => $number]);
    }

    /**
     * 存储线索信息
     * @param array $params
     * @return string
     */
    protected function processSaveClue(array $data)
    {
        $clueId = $data['clue_id'] ?? '';
        $data = combineArray($data, $this->clueField);
        if(! $clueId){
            $data['clue_id'] = guid();
        }

        // TODO 需要提交参数未上级交办，以便搜索
        if(isset($data['supervisor']) && 1 == $data['supervisor']){
            $data['supervisor'] = '上级交办';
        }

        $this->clueRep->saveClue([$data]);

        return $data['clue_id'];
    }

    /**
     * 保存线索详情
     * @param array $data
     * @return string
     * @throws \Exception
     */
    protected function processSaveClueDetail(array $data, $clueId)
    {
        $data['clue_id'] = $clueId;
        if(! $data) return [];

        if(! $data['clue_id']){
            throw new \Exception('clue_id does not exists !');
        }

        return $this->clueRep->saveClueDetail([$data]);
    }

    /**
     * 处理附件保存
     * @param array $data
     * @param $clueId
     * @throws \Exception
     */
    protected function processSaveClueAttachments(array $data, $clueId)
    {
        if(! $data) return [];

        if(! $clueId){
            throw new \Exception('clue_id does not exists !');
        }
        $rt = [];

        foreach($data as $key => $value){
            $rt[$key] = combineArray($value, $this->clueAttachmentsField);
            $rt[$key]['clue_id'] = $clueId;
            $rt[$key]['json_data'] = json_encode($value);
        }

        return $this->clueRep->saveClueAttachments($rt);
    }

    /**
     * 获取线索信息
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function viewClue(array $params)
    {
        $result = [];
        $clueId = $this->checkRequestClueId($params);

        // 获取线索信息
        $result['clue'] = $this->clueRep->getClueByClueId(['clue_id' => $clueId]);

        // 计算距离结案日期天数
        $date = getTodayDate();
        $closedDate = $result['clue']['closed_time'] ?? '';
        $days = 0;
        if($closedDate){
            $days = diffDays($closedDate, $date);
        }
        $result['clue']['days_from_closed'] = $days;

        // 获取线索详情
        $result['clue_detail'] = $this->clueRep->getClueDetailByClueId(['clue_id' => $clueId]);

        // 获取线索附件信息
        $result['clue_attachments'] = $this->getClueAttachments(['clue_id' => $clueId]);

        return $result;
    }

    public function getClueAttachments(array $params)
    {
        $clueId = $params['clue_id'] ?? '';
        if(! $clueId) return [];
        $data = $this->clueRep->getClueAttachmentsByClueId(['clue_id' => $clueId]);
        if(! $data) return [];
        array_walk($data, function(& $value){
            $value['is_img'] = 0;
            if('img' == $value['attachment_type']){
                $value['is_img'] = 1;
            }
        });

        return $data;
    }

    public function deleteClue(array $params)
    {
        $clueId = $this->checkRequestClueId($params);

        $condition = ['clue_id' => $clueId];

        // 存储线索删除信息
        $this->saveClueDeletedInfo($condition);

        // 删除线索
        $this->clueRep->deleteClue($condition);

        // 删除线索明细
        $this->clueRep->deleteClueDetail($condition);

        // TODO 删除线索附件信息及文件
        $this->clueRep->deleteClueAttachments($condition);

        return ['result' => true];
    }

    /**
     * 保存线索删除信息
     * @param array $params
     * @return string
     * @throws \Exception
     */
    protected function saveClueDeletedInfo(array $params)
    {
        $clueId = _isset($params, 'clue_id');
        if(! $clueId){
            throw new \Exception('clue_id does not exists !');
        }

        // 获取线索信息
        $result['clue'] = $this->clueRep->getClueByClueId(['clue_id' => $clueId]);

        // 获取线索详情
        $result['clue_detail'] = $this->clueRep->getClueDetailByClueId(['clue_id' => $clueId]);

        // 获取线索附件信息
        $result['clue_attachments'] = $this->clueRep->getClueAttachmentsByClueId(['clue_id' => $clueId]);

        $data = [
            'clue_id' => $clueId,
            'json_data' => json_encode($result),
        ];

        unset($result);

        return $this->clueRep->saveClueDeleted([$data]);
    }

    /**
     * 删除线索附件信息(谨慎操作)
     * @param array $params
     * @return array
     */
    public function deleteClueAttachments(array $params)
    {
        $clueId = $this->checkRequestClueId($params);
        $fileId = _isset($params, 'file_id');

        $condition = ['clue_id' => $clueId, 'file_id' => $fileId];
        // 获取线索附件信息
        $clueAttachments = $this->clueRep->getClueAttachmentsByClueId($condition);
        if(! $clueAttachments){
            throw new \Exception('clue_attachment does not exists');
        }
        // 删除线索附件信息
        $this->clueRep->deleteClueAttachments($condition);

        // TODO 删除附件
        $this->deleteFile(['disk' => 'uploads', 'file' => array_column($clueAttachments, 'file_path', 'file_id')]);

        return ['result' => true];
    }

    protected function deleteFile(array $params)
    {
        $affected = 0;
        $disk = _isset($params, 'disk');
        $file = isset($params['file']) ? convertToArray($params['file']) : [];
        if(! $file) return [];

        foreach($file as $value){
            // 截取储存空间
            $filePath = ltrim(ltrim($value, $disk), '/');
            $affected = Storage::disk($disk)->delete($filePath);
        }

        return $affected;
    }

    protected function checkRequestClueId(array $params)
    {
        $clueId = _isset($params, 'clue_id');
        if(! $clueId){
            throw new \Exception('Incomplete clue_id');
        }
        return $clueId;
    }
    
    /**
     * 超期提醒
     * 
     * @param array $params
     * @return type
     */
    public function overdueRemind(array $params)
    {
        //处理检索条件
        $condition = $this->getClueCondition($params);
        //获取超期提醒总数
        $total = $this->clueRep->getRemindTotal($condition);
        $overdueRemindData = $this->clueRep->getOverdueRemind($condition);
        return ['data' => $overdueRemindData, 'total' => $total];
    }
    
    /**
     * 超期提醒条件整理
     * 
     * @param type $params
     * @return type
     */
    public function getClueCondition($params)
    {
        $condition = [];
        if(isset($params['orders']) && is_array($params['orders']) && !empty($params['orders'])){
            foreach($params['orders'] as $val){
                $condition['order'][$val['column']] = (int)$val['order'];
            }
        }
        $condition['temp'] = ((isset($params['beginDate']) && $params['beginDate']) || (isset($params['endDate']) && $params['endDate'])) ? false : true;
        $condition['begin'] = (isset($params['beginDate']) && $params['beginDate']) ? $params['beginDate'] . ' 00:00:00' : '';
        $condition['end'] = (isset($params['endDate']) && $params['endDate']) ? $params['endDate'] . ' 23:59:59' : '';
        //线索来源
        $condition['source'] = $params['source'] ?? '';
        
        $condition['page'] = (isset($params['page']) && $params['page']) ? (int)$params['page'] : 1;
        $condition['pagesize'] = (isset($params['pagesize']) && $params['pagesize']) ? (int)$params['pagesize'] : 10;
        return $condition;
    }

    /**
     * 通过编号获取线索信息
     * @param array $params
     * @return array
     */
    public function checkClueByNumber(array $params)
    {
        $number = _isset($params, 'number');

        // 获取线索信息
        return $this->clueRep->checkClueByNumber(['number' => $number]);
    }

    /**
     * 保存导入的数据
     * @param array $params
     * @return array
     */
    public function saveExcelClue(array $excelData)
    {
        $rt = [];
        if(! $excelData) return [];
        foreach($excelData as $key => $value){
            $value['op_type'] = 1;
            // TODO 过滤字段
            $rt['clue'] = $value;
            $rt['clue_detail'] = $value;

            // 处理线索存储
            $clueId = $this->processSaveClue($rt['clue']);

            // 处理线索详情存储
            $this->processSaveClueDetail($rt['clue_detail'], $clueId);
        }

        return [];
    }

    /**
     * 结办
     * @param array $params
     */
    public function setClueClosed(array $params)
    {
        $stateDic = $this->dicService->getDicInfo('clue_state');

        $result = [];
        $clueId = $this->checkRequestClueId($params);

        // 查询线索信息
        $clueInfo = $this->clueRep->getClueByClueId(['clue_id' => $clueId]);

        // 当前线索状态
        $clueLastState = $clueInfo['clue_state'] ?? '';

        // 更改线索状态
        $clueState = $stateDic[3] ?? '已结';

        $condition = [
            'clue_id' => $clueId,
            'update' =>
                [
                    'clue_last_state' => $clueLastState,
                    'clue_state' => $clueState
                ],
        ];

        $result = $this->clueRep->setClueClosed($condition);
        return ['result' => true, 'clue_state' => $clueState];
    }

    /**
     * 清除重复编号数据
     * @param array $params
     * @return array
     */
    public function clearClueByNumber(array $params)
    {
        $number = _isset($params, 'number');

        // 清除重复编号线索信息
        return $this->clueRep->clearClueByNumber(['number' => $number]);
    }

    /**
     * 超期提醒总数
     * 
     * @param array $params
     * @return type
     */
    public function remindTotal(array $params)
    {
        return $this->clueRep->getRemindTotal(['temp'=>true]);
    }
}