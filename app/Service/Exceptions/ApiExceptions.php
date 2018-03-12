<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/8
 * Time: 下午4:26
 */
namespace App\Service\Exceptions;

use Illuminate\Support\Facades\Request;
use Throwable;

class ApiExceptions extends \Exception
{
    public function __construct(array $message, $code = 0, Throwable $previous = null)
    {
        parent::__construct(json_encode($message), $code, $previous);
    }

    /**
     * 处理错误
     *
     * @param \Exception $exception
     * @return array|mixed
     */
    static public function handle(\Exception $exception)
    {
        // 解析错误消息
        $message = json_decode($exception->getMessage(), true);

        // 如果未能正常解析消息
        if (is_array($message) === false || empty($message)) {
            // 是程序出错，组装程序错误消息
            $message = Message::error($exception->getMessage(), 500);
        }

        // 如果不是线上环境，开启调戏信息
        if (config('app.env') !== 'production') {
            $message['debug'] = [
                // 请求URL
                'request_url' => Request::url(),
                // 请求参数
                'request_form' => Request::all(),
                // 出错误的文件
                'error_file' => $exception->getFile(),
                // 出现错误的行数
                'error_file_line' => $exception->getLine(),
            ];
        }
        // 反回错误
        return $message;
    }
}