<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/11
 * Time: 下午5:58
 */
namespace App\Repository\Foundation;

use App\Model\Foundation\DictModel;

class BaseRep
{
    public function __construct()
    {
        $this->dictModel = new DictModel();
    }
    
    /**
     * 获取字典数据
     * @return type
     */
    public function getDicData()
    {
        $query = $this->dictModel
            ->select('*');
        $query = $query->get();
        return $query && count($query) ? $query->toArray() : [];
    }
}