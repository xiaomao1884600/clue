<?php

namespace App\Http\Middleware;

use Closure;

class VerifyCorsMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 获取客户端地址
        // $hostOrigin = $this->getHostOrigin();
        $hostOrigin = '*';
        $headers = [
            'Access-Control-Allow-Origin' => $hostOrigin,
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers'=> $request->header('Access-Control-Request-Headers')
        ];

        if($request->isMethod('OPTIONS')) {
            return \Response::make('OK', 200, $headers);
        }

        $this->setRequest();

        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }
        return $response;
    }

    /**
     * 获取客户端地址
     */
    public function getHostOrigin()
    {
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        $originList = Config('hostorigin.originlist');
        if(in_array($origin, $originList)){
            return $origin;
        }else{
            return '';
        }
    }

    public function setRequest()
    {
        $source = json_decode(request()->instance()->getContent(), true);
        if (empty($source) === false) {
            foreach ($source as $key => $value) {
                if (empty(request()->get($key))) {
                    request()->offsetSet($key, $value);
                }
            }
        }
    }
}
