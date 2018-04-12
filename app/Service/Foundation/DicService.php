<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/31
 * Time: 下午3:35
 */

namespace App\Service\Foundation;

use App\Repository\Foundation\BaseRep;

class DicService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
        $this->baseRep = new BaseRep();
    }
    
    /**
     * 获取字典
     * 
     * @return type
     */
    public function getDicList()
    {
        //生产字典
        $data = $this->baseRep->getDicData();
        $dieList = $this->processDicData($data);
        
        return $dieList;
    }
    
    /**
     * 处理数据格式
     * 
     * @param type $data
     * @return type
     */
    public function processDicData($data)
    {
        $return = [];
        foreach($data as $k => $v){
            if($v['pid'] == 0 && $v['code'] == 0){
                $return[$v['fieldname']] = [
                    'id' => $v['id'],
                    'code' => $v['code'],
                    'title' => $v['dictionnam'],
                    'pid' => $v['pid']
                ];
            }else{
                $return[$v['fieldname']]['data'][] = [
                    'id' => $v['id'],
                    'code' => $v['code'],
                    'title' => $v['itemname'],
                    'pid' => $v['pid']
                ];
            }
        }
        return $return;
    }

    /**
     * 获取字典关键字
     * @param string $key
     * @return type|array
     */
    public function getDicInfo(string $key = '')
    {
        $df = [];

        $dicInfo = $this->getDicList();

        if($key){
            $df = $this->getDic($dicInfo, $key);
        }else{
            $df = $dicInfo;
        }

        return $df;
    }

    /**
     * 获取字典
     * @param array $dicInfo
     * @param string $key
     * @param int $type ，为1 时，则获取title对应字典
     * @return array
     */
    public function getDic(array $dicInfo, string $key, $type = 0)
    {
        if($type){
            $dic = isset($dicInfo[$key]['data']) ? array_column($dicInfo[$key]['data'], 'code', 'title') : [];
        }else{
            $dic = isset($dicInfo[$key]['data']) ? array_column($dicInfo[$key]['data'], 'title', 'code') : [];
        }

        return $dic;
    }
}