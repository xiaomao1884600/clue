<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/5/21
 * Time: 下午10:47
 */

namespace App\Repository\Register;


use App\Model\Clue\ClueAttachments;
use App\Model\Register\Register;
use App\Repository\Foundation\BaseRep;

class RegisterRep extends BaseRep
{
    protected $register;

    protected $clueAttachments;

    public function __construct(
        Register $register,
        ClueAttachments $clueAttachments
    )
    {
        parent::__construct();
        $this->register = $register;
        $this->clueAttachments = $clueAttachments;
    }

    /*
     * 存储等级发放
     */
    public function saveRegister(array $data)
    {
        return $this->register->insertUpdateBatch($data);
    }

    /**
     * 通过编号查询登记发放信息
     * @param array $condition
     * @return array
     */
    public function getClueByNumber(array $condition)
    {
        $result = [];

        $number = $condition['number'] ?? '';
        $clueId = $condition['clue_id'] ?? '';
        $clueId = convertToArray($clueId);
        if(! $number) return $result;

        $query = $this->register
            ->where('number', $number)
            ->whereNotIn('clue_id', $clueId)
            ->first();

        if($query){
            $result = $query->toArray();
        }

        return $result;
    }

    public function saveClueAttachments(array $data)
    {
        return $this->clueAttachments->insertUpdateBatch($data);
    }

    /**
     * 获取登记发放
     * @param array $condition
     * @return array
     */
    public function getRegisterByClueId(array $condition)
    {
        $result = [];

        $clueId = $condition['clue_id'] ?? '';
        if(! $clueId) return $result;

        $query = $this->register
            ->where('clue_id', $clueId)
            ->first();

        if($query){
            $result = $query->toArray();
        }

        return $result;
    }

}