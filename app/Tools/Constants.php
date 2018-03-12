<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/12
 * Time: 下午1:15
 */

define('TIMENOW', time());

/** 请求操作类型 */
//用户请求
define('REQUEST_TYPE_USER', 1);
//系统请求
define('REQUEST_TYPE_SYSTEM', 2);

/** 分页 */
//每页显示
define('PAGESIZE', 20);

/** 缓存时间 分钟 */
//缓存20分钟
define('CACHE_MINUTE_TWENTY', 20);
//缓存一个小时
define('CACHE_HOUR', 60);
//缓存一天
define('CACHE_DAY', 1440);
//缓存一周
define('CACHE_WEEK', 10080);
//缓存一月 30天
define('CACHE_MONTH', 43200);

/** 目录分隔符 */
// 目录分隔符
define('DS', DIRECTORY_SEPARATOR);
