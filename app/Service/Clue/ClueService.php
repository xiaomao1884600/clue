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
            'number' => TIMENOW,
            'reflected_name' => 'zhangsan',
            'entry_time' => '2018-03-17 10:00:00',
            'closed_time' => '2018-05-20 10:00:00',
        ],
        'clue_detail' => [
            'main_content' => '严重违纪',
            'department_opinion' => '予以开除',
            'leader_approval' => '同意',
        ],
        'clue_attachments' => [
            [
                'file_id' => '1',
                'originalName' => '测试.doc',
                'newFileName' => '测试_20180318.doc',
                'extension' => 'doc',
                'file_path' => 'uploads/aaa.doc',
            ],

            [
                'file_id' => '2',
                'originalName' => '测试2.doc',
                'newFileName' => '测试2_20180318.doc',
                'extension' => 'doc',
                'file_path' => 'uploads/aaa.doc',
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
}