<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/5/21
 * Time: 下午10:48
 */

namespace App\Service\Register;


use App\Repository\Clue\ClueRep;
use App\Repository\Register\RegisterRep;
use App\Service\Foundation\BaseService;

class RegisterService extends BaseService
{
    protected $registerRep;

    protected $clueRep;

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
        'main_content' => 'main_content',
        'department_opinion' => 'department_opinion',
        'leader_approval' => 'leader_approval',
        'signatory' => 'signatory',
        'undertake_leader' => 'undertake_leader',
        'progress' => 'progress',
        'remark' => 'remark',
    ];

    protected $testClueInfo = [
        'register' => [
            //'source_dic' => '线索来源字典', // 线索来源I
            //'source' => '线索来源输入框', // 线索来源II
            'number' => TIMENOW, // 编号
            'reflected_name' => 'zhangsan', // 被反映人
            'company' => '天宇国际', // 工作单位及职务
            //'post' => '经理',  // 职位
            //'level' => '处级',  // 级别
            //'entry_time' => '2018-03-17 10:00:00',  // 录入日期
            //'closed_time' => '2018-05-20 10:00:00', // 结案日期
            //'disposal_type' => '集体排查', // 处置类型
            //'supervisor' => '上级交办', // 是否上级交办
            //'remind_days' => 8, // 提醒天数
            'clue_next' => '干部监督室', // 承办部门
            //'clue_state' => '待办', // 线索状态
            'main_content' => '反应的主要问题',
            //'department_opinion' => '部门意见',
            'leader_approval' => '集体排查意见及领导批示',
            'signatory' => '领取人签字',
            'undertake_leader' => '承办领导',
            'progress' => '进展',
            'remark' => '备注',
        ],
//        'register_attachments' => [
//            [
//                'file_id' => '1', //附件id
//                'originalName' => '测试.doc', // 附件原始名称
//                'newFileName' => '测试_20180318.doc', // 文件名称
//                'extension' => 'doc',  // 后缀
//                'file_path' => 'uploads/aaa.doc', // 文件路径
//                'attachment_type' => 'wrod', // 附件类型
//            ],
//
//            [
//                'file_id' => '2',
//                'originalName' => '测试2.png',
//                'newFileName' => '测试2_20180318.png',
//                'extension' => 'doc',
//                'file_path' => 'uploads/aaa.png',
//                'attachment_type' => 'img', // 附件类型(图片)
//            ]
//        ],
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

    public function __construct(
        RegisterRep $registerRep,
        ClueRep $clueRep
    )
    {
        parent::__construct();
        $this->registerRep = $registerRep;
        $this->clueRep = $clueRep;
    }

    /**
     * 存储登记发放
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function saveRegister(array $params)
    {
        // TODO 临时测试
        //$params = $this->testClueInfo;

        if(! isset($params['register']) || ! $params['register']){
            throw new \Exception('Incomplete register information !');
        }

        // TODO 检测编号是否唯一
        if(current($this->checkClueNumber($params['register']))){
            throw new \Exception('The number has already existed !');
        }

        // 处理登记发放存储
        $clueId = $this->processSaveRegister($params['register']);

        // 处理线索附件存储
        //$this->processSaveClueAttachments($params['register_attachments'], $clueId);

        return ['clue_id' => $clueId];
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
        if($this->registerRep->getClueByNumber(['number' => $number, 'clue_id' => $clueId])){
            $result['result'] = true;
        };

        return $result;
    }

    /**
     * 处理登记发放存储
     * @param array $params
     * @return string
     */
    protected function processSaveRegister(array $data)
    {
        $clueId = $data['clue_id'] ?? '';
        //$data = combineArray($data, $this->clueField);
        if(! $clueId){
            $data['clue_id'] = guid();
        }

        // TODO 需要提交参数未上级交办，以便搜索
        if(isset($data['supervisor']) && 1 == $data['supervisor']){
            $data['supervisor'] = '上级交办';
        }

        $this->registerRep->saveRegister([$data]);

        return $data['clue_id'];
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

        return $this->registerRep->saveClueAttachments($rt);
    }

    /**
     * 获取等级发放
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function viewRegister(array $params)
    {
        $result = [];
        $clueId = $this->checkRequestClueId($params);

        // 获取线索信息
        $result['register'] = $this->registerRep->getRegisterByClueId(['clue_id' => $clueId]);

        // 计算距离结案日期天数
        $date = getTodayDate();
        $closedDate = $result['register']['closed_time'] ?? '';
        $days = 0;
        if($closedDate){
            $days = diffDays($closedDate, $date);
        }
        $result['register']['days_from_closed'] = $days;

        // 获取线索附件信息
        $result['register_attachments'] = $this->getClueAttachments(['clue_id' => $clueId]);

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

    protected function checkRequestClueId(array $params)
    {
        $clueId = _isset($params, 'clue_id');
        if(! $clueId){
            throw new \Exception('Incomplete clue_id');
        }
        return $clueId;
    }
}