<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/12
 * Time: 上午8:16
 */

namespace App\Repository\Ucenter;


use App\Model\Ucenter\UserToken;
use App\Repository\Foundation\BaseRep;

class UserTokenRep extends BaseRep
{
    protected $userToken;

    public function __construct(
        UserToken $userToken
    )
    {
        parent::__construct();
        $this->userToken = $userToken;
    }

    public function saveUserToken(array $data)
    {
        return $this->userToken
                ->insertGetId($data);
    }

    /**
     * 修改用户token失效
     * @param type $condition
     * @return type
     */
    public function setTokenInvalidByToken ($condition = [])
    {
        if(! isset($condition['token'])){
            return 0;
        }

        $tokens = convertToArray($condition['token']);
        $invalid = _isset($condition, 'invalid', 1);
        $logoutTime = _isset($condition, 'logout_time', dateFormat());
        return $this->userToken
                ->whereIn('token', $tokens)
                ->where('invalid', 0)
                ->update(['invalid' => $invalid, 'logout_time' => $logoutTime]);
    }
}