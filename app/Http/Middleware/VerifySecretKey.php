<?php

namespace App\Http\Middleware;

use App\Service\System\ShellService;
use Closure;

class VerifySecretKey
{

    protected $shellService;

    public function __construct(ShellService $shellService)
    {
        $this->shellService = $shellService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        // 测试环境不检测
        $appEnv = $_ENV['APP_ENV'] ?? '';
        if('dev' == $appEnv){
            return $next($request);
        }

        // 检测macadress
        $result = $this->shellService->getCpu();
        $macAddress = $result['macAddress'] ?? '';

        $secretKey = md5($macAddress);

        // 获取配置秘钥
        $envSecretKey = $_ENV['APP_SECRET_KEY'] ?? '';

        if($envSecretKey != $secretKey){
            throw new \Exception('Incorrect secretKey', 1006);
        }

        // 检测运行是否过期
        $currentDay = getTodayDate();
        $runExpireTime = $result['run_expire_time'] ?? '';

        if($runExpireTime && $currentDay > $runExpireTime){
            throw new \Exception("run expire time is 【{$runExpireTime}】", 1006);
        }

        return $next($request);

    }
}
