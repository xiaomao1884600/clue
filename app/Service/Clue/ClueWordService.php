<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/8
 * Time: 下午4:22
 */
namespace App\Service\Clue;

use App\Service\Foundation\BaseService;
use App\Service\Word\WordService;

class ClueWordService extends BaseService
{
    protected $wordService;

    public function __construct(
        WordService $wordService
    )
    {
        $this->wordService = $wordService;
    }

    /**
     * 线索导出到word
     * @param array $params
     */
    public function setClueExportWord(array $params)
    {
        $clueData = [];
        // 获取线索数据

        // 处理数据
        $clueData = $this->processClueData($clueData);

        // 执行导出
        return $this->executeExportWord($clueData);
    }

    protected function getClueData(array $params)
    {
        $clueData = [];
        // TODO 获取数据

        return $clueData;
    }

    protected function processClueData(array $data)
    {
        // TODO 处理
        return $data;
    }

    protected function executeExportWord(array $data)
    {
        return $this->wordService->exportTemp($data);
    }
}