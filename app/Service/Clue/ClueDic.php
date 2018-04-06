<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/4/6
 * Time: 下午6:45
 */

namespace App\Service\Clue;


use App\Service\Foundation\BaseService;
use App\Service\Foundation\DicService;

class ClueDic extends BaseService
{
    protected $dicService;

    public function __construct(
        DicService $dicService
    )
    {
        parent::__construct();
        $this->dicService = $dicService;
    }

    public function convertClueDic(array $data)
    {
        $dicInfo = $this->dicService->getDicList();
        $source = $this->getDic($dicInfo, 'source');
        $disposalType = $this->getDic($dicInfo, 'disposal_type');
        $clueState = $this->getDic($dicInfo, 'clue_state');

        // 匹配线索
        foreach($data as $key => &$value){
            // 来源
            $value['source'] = $source[$value['source']] ?? $value['source'];

            // 处置类型
            $value['disposal_type'] = $disposalType[$value['disposal_type']] ?? $value['disposal_type'];

            // 线索状态
            $value['clue_state'] = $clueState[$value['clue_state']] ?? $value['clue_state'];

        }

        return $data;
    }

    protected function getDic(array $dicInfo, string $key)
    {
        $dic = isset($dicInfo[$key]['data']) ? array_column($dicInfo[$key]['data'], 'title', 'code') : [];
        return $dic;
    }
}