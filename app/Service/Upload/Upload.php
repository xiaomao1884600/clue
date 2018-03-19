<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/18
 * Time: 下午5:19
 */

namespace App\Service\Upload;


class Upload
{

    /**
     * 允许文件类型
     * @var array
     */
    protected static $fileType = [
        'audio' => [

        ],

        'img' => [
            'png', 'jpg', 'gif', 'jpeg', 'bmp'
        ],

        'pdf' => [
            'pdf'
        ],

        'word' => [
            'doc', 'docx', 'dot', 'dotx', 'docm', 'dotm', 'txt', 'wtf', 'xml'
        ],

        'excel' => [
            'csv', 'xlsx', 'xls', 'xlsm'
        ],

    ];

    protected static $fileSize = [
        'audio' => 1024000,
        'img' => 102400,
        'pdf' => 1024000,
        'word' => 1024000,
        'excel' => 1024000,
    ];

    public static function checkFileType($fileType, $type)
    {
        $flag = true;
        $allowType = self::$fileType[$type] ?? [];
        // 未设置类型则允许全部类型
        if(! $allowType) return $flag;

        return isset($allowType[$fileType]) ? true : false;
    }

    public static function checkFileSize($fileSize, $type)
    {
        $flag = true;
        $size = self::$fileSize[$type] ?? 1024000;
        if($fileSize > $size) $flag = false;
        return $flag;
    }

    public static function getFileType($fileType, $type)
    {
        return self::$fileType[$type] ?? [];
    }

    public static function getFileSize($fileType, $type)
    {
        return self::$fileSize[$type] ?? 1024000;
    }
}