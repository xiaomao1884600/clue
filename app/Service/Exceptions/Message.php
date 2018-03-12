<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/8
 * Time: 下午4:25
 */
namespace App\Service\Exceptions;

class Message
{
    /**
     * 错误信息组装
     *
     * @param string $message
     * @param int $code
     * @param array $options
     * @return array
     */
    static public function error(string $message,  int $code = 400, array $options = []) : array
    {
        return [
            'success' => false,
            'errorCode'=> $code,
            'errorMessage' => $message,
            'data' => []
        ];
    }

    /**
     * 成功请求信息组装
     *
     * @param array $data
     * @param int $code
     * @param string $message
     * @return array
     */
    static public function success(array $data = [], int $code = 200, string $message = '成功') : array
    {
        return [
            'success' => true,
            'errorCode' => $code,
            'errorMessage' => $message,
            'data' => $data,
        ];
    }
}