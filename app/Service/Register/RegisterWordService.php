<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/5/22
 * Time: 下午2:52
 */

namespace App\Service\Register;


use App\Repository\Register\RegisterRep;
use App\Service\Foundation\BaseService;
use App\Service\Word\WordService;

class RegisterWordService extends BaseService
{
    protected $wordService;

    protected $registerRep;

    // 模板路径
    protected $tempPath = '';

    public function __construct(
        WordService $wordService,
        RegisterRep $registerRep
    )
    {
        parent::__construct();
        $this->wordService = $wordService;
        $this->registerRep = $registerRep;
        $this->tempPath = public_path('word/register_temp.docx');
    }

    /**
     * 等级发放导出到word
     * @param array $params
     */
    public function setRegisterExportWord(array $params)
    {
        $clueData = [];
        // 获取线索数据
        $clueData = $this->getRegisterData($params);
        if(! $clueData){
            throw new \Exception('clue info does not exists !');
        }

        // 执行导出
        return $this->wordService->exportTemp(['tempPath' => $this->tempPath, 'data' => $clueData]);
    }

    protected function getRegisterData(array $params)
    {
        $clueData = $condition = [];
        // TODO 获取数据
        $clueId = _isset($params, 'clue_id');
        if(! $clueId){
            throw new \Exception('clue_id does not null');
        }

        $condition = ['clue_id' => $clueId];
        $clueData = $this->registerRep->getRegisterByClueId($condition);

        // TODO 处理
        $clueData['entry_date'] = dateFormat(strtotime($clueData['entry_time']), 'Y 年 m 月 d 日');

        return $clueData;
    }
}