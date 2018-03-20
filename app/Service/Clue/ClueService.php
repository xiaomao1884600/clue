<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/18
 * Time: 上午10:31
 */

namespace App\Service\Clue;


use App\Repository\Clue\ClueRep;
use App\Service\Foundation\BaseService;

class ClueService extends BaseService
{
    protected $clueRep;

    /**
     * 线索字段
     * @var array
     */
    protected $clueField = [
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
            'source' => 2, // 线索来源
            'number' => TIMENOW, // 编号
            'reflected_name' => 'zhangsan', // 被反映人
            'company' => '天宇国际', // 单位
            'post' => '经理',  // 职位
            'level' => 1,  // 级别
            'entry_time' => '2018-03-17 10:00:00',  // 录入日期
            'closed_time' => '2018-05-20 10:00:00', // 结案日期
            'disposal_type' => 1, // 处置类型
            'supervisor' => 1, // 是否上级交办
            'remind_days' => 8, // 提醒天数
            'clue_next' => 8, // 线索去向
            'clue_state' => 8, // 线索状态
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
        ClueRep $clueRep
    )
    {
        parent::__construct();
        $this->clueRep = $clueRep;
    }

    public function saveClue(array $params)
    {
        // TODO 临时测试
        $params = $this->testClueInfo;

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

        return ['clue_id' => $clueId];
    }

    public function checkClueNumber(array $params)
    {
        $result = ['result' => false];

        $number = _isset($params, 'number');

        if(! $number){
            throw new \Exception('The number is required !');
        }

        // 检测是否存在编号信息
        if($this->clueRep->getClueByNumber(['number' => $number])){
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

        // 获取线索详情
        $result['clue_detail'] = $this->clueRep->getClueDetailByClueId(['clue_id' => $clueId]);

        // 获取线索附件信息
        $result['clue_attachments'] = $this->clueRep->getClueAttachmentsByClueId(['clue_id' => $clueId]);

        return $result;
    }

    public function deleteClue(array $params)
    {
        $clueId = $this->checkRequestClueId($params);

        $condition = ['clue_id' => $clueId];

        // 删除线索
        $this->clueRep->deleteClue($condition);

        // 删除线索明细
        $this->clueRep->deleteClueDetail($condition);

        // TODO 删除线索附件信息及文件
        $this->clueRep->deleteClueAttachments($condition);

        return ['result' => true];
    }

    public function deleteClueAttachments(array $params)
    {
        $clueId = $this->checkRequestClueId($params);

        $condition = ['clue_id' => $clueId];
        // 获取线索附件信息
        $clueAttachments = $this->clueRep->getClueAttachmentsByClueId($condition);

        // 删除线索附件信息
        $this->clueRep->deleteClueAttachments($condition);

        // TODO 删除附件

        return ['result' => true];
    }

    protected function checkRequestClueId(array $params)
    {
        $clueId = _isset($params, 'clue_id');
        if(! $clueId){
            throw new \Exception('Incomplete clue_id');
        }
        return $clueId;
    }
}