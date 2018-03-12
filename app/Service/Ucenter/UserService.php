<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/12
 * Time: 下午1:27
 */
namespace App\Service\Ucenter;

use App\Service\Foundation\BaseService;
use Cache;

trait UserService
{
    protected $_userInfo = [];

    public function registUserAboutInfo()
    {

        // TODO 注册需要的服务
    }

    protected function getUserInfoByToken(string $token)
    {
        $userInfo = Cache::get('userinfo_' . $token, []);

        return $userInfo ? (array) $userInfo : [];
    }
}