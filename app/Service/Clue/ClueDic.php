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

    /**
     * 字典转换
     * @param array $data
     * @param int $type 若为导入excel需要title转code则传1，默认0
     * @return array
     */
    public function convertClueDic(array $data, $type = 0)
    {
        $dicInfo = $this->dicService->getDicList();
        $source = $this->getDic($dicInfo, 'source', $type);
        $disposalType = $this->getDic($dicInfo, 'disposal_type', $type);
        $clueState = $this->getDic($dicInfo, 'clue_state', $type);
        $supervisor = $this->getDic($dicInfo, 'supervisor', $type);

        // 匹配线索
        foreach($data as $key => &$value){
            // 来源
            $value['source'] = $source[$value['source']] ?? $value['source'];

            // 处置类型
            $value['disposal_type'] = $disposalType[$value['disposal_type']] ?? $value['disposal_type'];

            // 线索状态
            $value['clue_state'] = $clueState[$value['clue_state']] ?? $value['clue_state'];

            // 上级交办类型
            $value['supervisor'] = $supervisor[$value['supervisor']] ?? $value['supervisor'];

        }

        return $data;
    }

    /**
     * 获取字典
     * @param array $dicInfo
     * @param string $key
     * @param int $type ，为1 时，则获取title对应字典
     * @return array
     */
    protected function getDic(array $dicInfo, string $key, $type = 0)
    {
        if($type){
            $dic = isset($dicInfo[$key]['data']) ? array_column($dicInfo[$key]['data'], 'code', 'title') : [];
        }else{
            $dic = isset($dicInfo[$key]['data']) ? array_column($dicInfo[$key]['data'], 'title', 'code') : [];
        }

        return $dic;
    }

    /**
     * 字典转换
     * @param array $data
     * @param int $type 若为导入excel需要title转code则传1，默认0
     * @return array
     */
    public function convertDic(array $data, array $dicField, $type = 0)
    {
        if(! $dicField) return $data;
        $convertDicInfo = [];
        $dicInfo = $this->dicService->getDicList();

        foreach($dicField as $field){
            $convertDicInfo[$field] = $this->getDic($dicInfo, $field, $type);
        }

        array_walk($data, function(& $value) use($dicInfo, $convertDicInfo, $type){
            foreach($convertDicInfo as $field => $df){
                if(isset($value[$field])){
                    $value[$field] = $df[$value[$field]] ?? $value[$field];
                }
            }
        });

        return $data;
    }
}