<?php

namespace App\Http\Middleware;

use App\Service\Exceptions\ApiExceptions;
use Closure;
use Config;
use App\Service\Ucenter\UserService;

class VerifyToken
{
    // 加载用户基础服务
    use UserService;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        error_reporting(E_ALL & ~ E_NOTICE);
        // 检测token
        try{
            $token = $request['token'] ?? '';
            if(! $token){
                throw new \Exception('not have access !', 1005);
            }
            $this->_userInfo = $this->getUserInfoByToken($token);
            if(! $this->_userInfo){
                throw new \Exception('not have access !', 1105);
            }

            if(REQUEST_TYPE_USER == $request['type']){
                $this->_userInfo['last_activity'] = dateFormat(TIMENOW);
            }

            // 设置用户信息
            Config::set('userinfo', $this->_userInfo);

        }catch(\Exception $e){
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $next($request);
    }
}
