<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/11
 * Time: 下午5:42
 */
namespace App\Service\UserLogin;

use App\Repository\Ucenter\UserLoginRep;
use App\Repository\Ucenter\UserTokenRep;
use App\Service\Foundation\BaseService;
use Cache;

class UserLoginService extends BaseService
{
    protected $userLoginRep;

    protected $userTokenRep;

    public function __construct(
        UserLoginRep $userLoginRep,
        UserTokenRep $userTokenRep
    )
    {
        $this->userLoginRep = $userLoginRep;
        $this->userTokenRep = $userTokenRep;
    }

    /**
     * 处理登录
     * @param array $params
     */
    public function doLogin(array $params)
    {
        $userInfo = [];
        $loginName = _isset($params, 'loginName', '');
        $passWord = _isset($params, 'password', '');
        
        if(! $loginName || ! $passWord){
            throw new \Exception('Account or password can not be empty !');
        }

        // 检测用户信息
        $userInfo = $this->userLoginRep->getUserByName(['loginName' => $loginName]);
        if(! $userInfo || !$this->verifyAccount($passWord, $userInfo)){
            throw new \Exception('The account or password does incorrect !');
        }

        // 处理用户登录信息
        return $this->processLogin($userInfo, $params);
    }

    protected function verifyAccount(string $passWord, array $userInfo)
    {
        return $userInfo['password'] == md5(md5($passWord) . $userInfo['salt']);
    }

    /**
     * 处理登录
     * @param array $userInfo
     * @param array $params
     * @return mixed
     */
    protected function processLogin(array $userInfo, array $params)
    {
        $tokenInfo = [];
        //获取token
        $tokenInfo['token'] = $this->createToken($userInfo['userid']);
        $tokenInfo['exptime'] = $this->getExpTimeByAccessInfo($userInfo, $params);

        //更新用户登录信息
        $this->updateLogin($userInfo);

        //cache
        $this->setCacheUserInfo($tokenInfo['token'], $userInfo);

        // 记录用户登录token
        $this->processUserLoginToken($userInfo, $tokenInfo);

        return  $this->responseUserInfo($userInfo, $tokenInfo);
    }

    /**
     * 创建token
     * @param type $salt
     * @return type
     */
    protected function createToken($salt = '')
    {
        return strtolower(getSalt(13).md5(time() . $salt));
    }

    /**
     * 通过accessInfo 获取过期时间
     * @param $userInfo
     * @return bool|string
     */
    protected function getExpTimeByAccessInfo(array $userInfo, array $params)
    {
        $expTime = 0;

        if ( ! $userInfo) return $expTime;

        if(isset($params['remember']) && $params['remember']) {
           $expTime = time() + 3600 * 24;
        }
        return $expTime;

    }

    private function updateLogin(array $userInfo)
    {
        $data = [];
        if (!$userInfo) {
            throw new \Exception ('invalid account infomation', 1205);
        }
        $data['update']['last_visit'] = $userInfo['last_activity'];
        $data['update']['last_activity'] = dateFormat();
        $data['userid'] = $userInfo['userid'];
        return $this->userLoginRep->updateUserInfo($data);
    }

    /**
     * token写入 session
     * @param type $userInfo
     * @param type $tokenInfo
     */
    protected function setTokenSession ($userid = '', $tokenInfo = [])
    {
        Session::put('token_' .$userid, $tokenInfo);
    }

    /**
     * 清除登录信息
     * @param type $userid
     * @return type
     */
    protected function clearTokenSession ($userid = '')
    {
        Session::forget('token_' . $userid);
    }

    /**
     * 写缓存
     * @param type $token
     * @param type $userInfo
     */
    protected function setCacheUserInfo (string $token, array $userInfo)
    {
        if ($token && $userInfo) {
            Cache::forever('userinfo_' . $token, $userInfo);
        }
    }

    protected function processUserLoginToken(array $userInfo, array $tokenInfo)
    {
        $rt = [];
        if(! $userInfo || ! $tokenInfo) return $rt;

        $rt = [
            'userid' => $userInfo['userid'] ?? 0,
            'token' => $tokenInfo['token'] ?? '',
            'login_time' => dateFormat(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ];

        return $this->userTokenRep->saveUserToken($rt);
    }

    protected function responseUserInfo ($userInfo, $tokenInfo)
    {
        $responseInfo = [];
        $responseInfo = [
            'userid' => $userInfo['userid'] ?? 0,
            'login_name' => $userInfo['login_name'] ?? 0,
            'user_name' => $userInfo['user_name'] ?? 0,
            'last_visit' => $userInfo['last_visit'] ?? 0,
            'last_activity' => $userInfo['last_activity'] ?? 0,
            'token' => $tokenInfo['token'] ?? '',
            'exptime' => $tokenInfo['exptime'] ?? 0,
        ];

        return $responseInfo;
    }

    public function logout(array $params)
    {
        $token = _isset($params, 'token');
        if(! $token){
            throw new \Exception('invalid token');
        }

        // 清除token信息
        $this->clearCacheUser($token);

        // 处理用户退出信息
        $this->processUserLogoutToken($token);

        return [];
    }

    /**
     * 清除用户缓存信息
     * @param type $token
     */
    public function clearCacheUser ($token = '')
    {
        Cache::forget('userinfo_' . $token);
    }

    /**
     * 处理用户token退出登录信息
     * @param string $token
     * @return boolean
     */
    protected function processUserLogoutToken(string $token)
    {
        $result = 0;
        $changeUserToken = [];
        if (! $token){
            return $result;
        }

        $changeUserToken = [
            'token' => $token,
            'invalid' => 1,
            'logout_time' => dateFormat(),
        ];
        $result = $this->userTokenRep->setTokenInvalidByToken($changeUserToken);
        return $result;
    }

    /**
     * 密码找回
     * @param array $params
     * @throws \Exception
     */
    public function recoverPwd(array $params)
    {
        $userInfo = [];
        $loginName = _isset($params, 'loginName', '');
        $passWord = _isset($params, 'newPassword', '');
        if(! $loginName || ! $passWord){
            throw new \Exception('Account or newPassword can not be empty !');
        }

        // 检测用户信息
        $userInfo = $this->userLoginRep->getUserByName(['loginName' => $loginName]);
        if(! $userInfo){
            throw new \Exception('The account or password does incorrect !');
        }

        $data['update']['salt'] = getSalt(3);

        $data['update']['password'] = md5(md5($passWord) . $data['update']['salt']);
        $data['userid'] = $userInfo['userid'];

        $this->userLoginRep->updateUserInfo($data);

        return ['result' => true];
    }
}