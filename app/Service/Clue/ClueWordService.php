<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/8
 * Time: 下午4:22
 */
namespace App\Service\Clue;

use App\Repository\Clue\ClueRep;
use App\Service\Foundation\BaseService;
use App\Service\Word\WordService;

class ClueWordService extends BaseService
{
    protected $wordService;

    protected $clueRep;

    // 模板路径
    protected $tempPath = '';

    public function __construct(
        WordService $wordService,
        ClueRep $clueRep
    )
    {
        $this->wordService = $wordService;
        $this->clueRep = $clueRep;
        $this->tempPath = public_path('word/clue_temp.docx');
    }

    /**
     * 线索导出到word
     * @param array $params
     */
    public function setClueExportWord(array $params)
    {
        $clueData = [];
        // 获取线索数据
        $clueData = $this->getClueData($params);
        if(! $clueData){
            throw new \Exception('clue info does not exists !');
        }

        // 处理数据
        $clueData = $this->processClueData($clueData);

        // 执行导出

        return $this->executeExportWord(['tempPath' => $this->tempPath, 'data' => $clueData]);
    }

    protected function getClueData(array $params)
    {
        $clueData = $condition = [];
        // TODO 获取数据
        $clueId = _isset($params, 'clue_id');
        if(! $clueId){
            throw new \Exception('clue_id does not null');
        }
        $condition = ['clue_id' => $clueId];
        $clueData = array_column($this->clueRep->getClueInfoByClueId($condition), null, 'clue_id');
        $clueData = $clueData[$clueId] ?? [];

        return $clueData;
    }

    protected function processClueData(array $data)
    {
        // TODO 处理
        $data['entry_date'] = dateFormat(strtotime($data['entry_time']), 'Y 年 m 月 d 日');

        return $data;
    }

    protected function executeExportWord(array $data)
    {
        return $this->wordService->exportTemp($data);
    }
}