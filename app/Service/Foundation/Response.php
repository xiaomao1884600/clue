<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/21
 * Time: 下午11:37
 */

namespace App\Service\Foundation;


class Response
{
    /**
     * 返回分页参数
     * @param array $result  数据库返回分页信息
     * @param array $data    记录数
     */
    public static function responsePaginate(array $result, array $data = [])
    {
        return [
            'current_page' => $result['current_page'] ?? 1,
            'last_page' => $result['last_page'] ?? 1,
            'total' => $result['total'] ?? 1,
            'from' => $result['from'] ?? 1,
            'to' => $result['to'] ?? 1,
            'data' => $data ? $data : $result['data'],
        ];
    }
}