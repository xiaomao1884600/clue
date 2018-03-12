<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/11
 * Time: 下午5:57
 */
namespace App\Repository\Ucenter;

use App\Model\Ucenter\User;
use App\Repository\Foundation\BaseRep;

class UserLoginRep extends BaseRep
{
    protected $user;

    public function __construct(
        User $user
    )
    {
        parent::__construct();
        $this->user = $user;
    }

    public function getUserByName(array $condition = [])
    {
        $result = [];
        $loginName = isset($condition['loginName']) ? (string) $condition['loginName'] : '';
        if(! $loginName) return [];
        $query = $this->user
                    ->where('login_name', $loginName)
                    ->orWhere('user_name', $loginName)
                    ->first();
        if($query){
            $result = $query->toArray();
        }
        return $result;
    }

    public function updateUserInfo(array $condition)
    {
        $userids = convertToArray($condition['userid']);
        $update = (array) $condition['update'];
        return $this->user
            ->whereIn('userid', $userids)
            ->update($update);
    }
}