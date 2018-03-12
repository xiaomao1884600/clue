<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/8
 * Time: 下午3:24
 */

if(! function_exists('requestData')){
    function requestData($request, $column = 'data'){
        $requestData = $request->all();
        if(isset($request[$column])){
            return $request[$column] ? $request[$column] : [];
        }else{
            return $requestData;
        }
    }
}

/**
 * 将数组按某一键重组
 * @param $field  字段
 * @param $data   数据
 * @param $value  值
 */
if (!function_exists('fieldByData'))
{
    function fieldByData ($field, $data = [], $value = '')
    {
        if (empty($field) || empty($data)) {
            return [];
        }
        $result = [];
        foreach ($data as $val) {
            if ($value) {
                $result[$val[$field]] = isset($val[$value]) ? $val[$value] : '';
            } else {
                $result[$val[$field]] = $val;
            }
        }
        return $result;
    }
}

/**
 * 将数组按某一键重组为二维数组
 * @param $field  字段
 * @param $data   数据
 * @param $value  值
 */
if (!function_exists('fieldToArrayByData'))
{
    function fieldToArrayByData ($field, $data = [], $value = '')
    {
        if (empty($field) || empty($data)) {
            return [];
        }
        $result = [];
        foreach ($data as $val) {
            if ($value) {
                $result[$val[$field]][] = isset($val[$value]) ? $val[$value] : '';
            } else {
                $result[$val[$field]][] = $val;
            }
        }
        return $result;
    }
}

/**
 * Prints out debug information about given variable.
 *
 * @param string $var Variable to show debug information for.
 * @param boolean $exit If set to true, exit.
 * @param boolean $showFrom If set to true, the method prints from where the function was called.
 * @param boolean $showHtml If set to true, the method prints the debug data in a screen-friendly way.
 */
if (!function_exists('debuger'))
{
    function debuger($var = '', $exit = false, $showFrom = true, $showHtml = false)
    {
        if ($showFrom)
        {
            $calledFrom = debug_backtrace();
            echo '<strong>' . $calledFrom[0]['file'] . '</strong>';
            echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
        }
        echo "\n<pre>\n";

        $var = print_r($var, true);
        if ($showHtml)
        {
            $var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
        }
        echo $var . "\n</pre>\n";

        if ($exit)
        {
            exit();
        }
    }
}
if (!function_exists('x'))
{
    function x($var = '')
    {
        $calledFrom = debug_backtrace();
        echo '<strong>' . $calledFrom[0]['file'] . '</strong>';
        echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
        echo "\n<pre>\n";
        $var = print_r($var, true);
        echo $var . "\n</pre>\n";
        exit();
    }
}

/**
 * Return a new response from the application.
 * @param type $content
 * @param type $code
 * @param type $message
 * @param type $options = JSON_FORCE_OBJECT 使用非关联数组时输出一个对象
 * @return type
 */
if (!function_exists('responseJson'))
{
    function responseJson($content = '', $code = 200, $message = '', $success = true, $options = JSON_ERROR_NONE)
    {
        //$calledFrom = debug_backtrace();
        //$file = $calledFrom[0]['file'] . ' line : ' . $calledFrom[0]['line'];
        $data = [];
        $data = [
            'success' => $success,
            'errorMessage'=>$message,
            'errorCode'=> $code,
            //    'file' => $file,
            'data' => $content
        ];
        return response()->json($data, 200, [], $options);
    }
}

if (!function_exists('curlRequest'))
{
    function curlRequest($url, $params = [], $method = 'GET', $header = [], $timeout = 600)
    {
        $res = [];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($header)) {
            // 增加header信息需要将header数组信息拼接成冒号拼接
            foreach ($header as $k => $v) {
                $headers[] = $k . ":" . $v;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLINFO_HEADER_OUT, true); //当需要通过curl_getinfo来获取发出请求的header信息时,该选项需要设置为true
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        switch ($method) {
            case 'GET':
                if (!empty($params)) {
                    $url = $url . (strpos($url, '?') ? '&' : '?') . (is_array($params) ? http_build_query($params) : $params);
                }
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
                break;
            case 'POST':
                if (class_exists('\CURLFile')) {
                    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
                } else if (defined('CURLOPT_SAFE_UPLOAD')) {
                    curl_setopt($ch, CURLOPT_SAFE_UPLOAD, false);
                }
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                break;
        }
        $res['url'] = $url;
        $beginTime = microtime(true);
        $res['tmpInfo'] = curl_exec($ch);
        $res['data'] = $params;

        $res['execTime'] = microtime(true) - $beginTime;
        if (curl_errno($ch)) { //curl报错
            $res['error_code'] = curl_errno($ch);
            $res['error_msg'] = curl_error($ch);
        } else {
            $res['getInfo'] = curl_getinfo($ch);
        }
        curl_close($ch); //关闭会话
        return $res;
    }
}

if(!function_exists('checkTimeout'))
{
    /**
     *  检查是否已经超时, 默认30秒
     * @param int $timeout
     */
    function checkTimeout($timeout = 5){
        // 开始时间
        $timeStart = time();
        // 5秒超时
        $timeout = 5;
        if(time()-$timeStart > $timeout){
            exit("超时{$timeout}秒\n");
        }
    }
}


if(!function_exists('filterEmoji'))
{
    /**
     * 过滤掉emoji表情
     * @param type $str
     * @return type
     */
    function filterEmoji($str)
    {
        $regex = '/(\\\u[ed][0-9a-f]{3})/i';
        $str = json_encode($str);
        $str = preg_replace($regex, '', $str);
        return json_decode($str);
    }
}

/**
 * 转换数组
 */
if (!function_exists('convertToArray'))
{
    function convertToArray($key = '')
    {
        return $key ? (is_array($key) ? $key : [$key]) : [];
    }
}

if (!function_exists('returnJson'))
{
    function returnJson($data)
    {
        return [
            'SUCCESS' => true,
            'DATA' => $data,
        ];
    }
}

if (!function_exists('convertObjectToArray'))
{
    function convertObjectToArray($data)
    {
        return json_decode(json_encode($data), true);
    }
}

if (! function_exists('convertDictionary'))
{
    /**
     * 转换字典
     * @param type $array
     * @param type $columns
     * @return type
     */
    function convertDictionary($array = [], $columns = ['id', 'title'])
    {
        $result = [];
        list($a, $b) = $columns;
        foreach($array as $key => $val){
            $result[] = [$a => $key, $b => $val];
        }
        return $result;
    }
}


if (! function_exists('sliceArray'))
{
    /**
     * 截取数组
     * @param string $str
     * @return string
     */
    function sliceArray($array, $keys = [])
    {
        $newArray = [];
        if($array){
            foreach($array as $k => $v){
                if(in_array($k, $keys)){
                    $newArray[$k] = $v;
                }
            }
        }
        return $newArray;
    }
}

if(! function_exists('screenArray'))
{
    /**
     * 筛选二维数组中指定key的元素
     * @param $array
     * @param array $column
     * @return array
     */
    function screenArray($array, $column = [])
    {
        if(! $column) return $array;
        $newArray = [];
        if($array){
            foreach($array as $key => $value){
                if(! is_array($value)) continue;
                foreach($value as $k => $v){
                    if(in_array($k, $column)){
                        $newArray[$key][$k] = $v;
                    }
                }
            }
        }
        return $newArray;
    }
}

if(! function_exists('requestZLData'))
{
    function requestZLData($request)
    {
        if (!is_object($request)) return [];
        $requestInfo = $request->all();
        if($request->isMethod('post')){
            $data = key($requestInfo);
            $data = is_string($data) ? json_decode($data, true) : [];
        }else{
            $data = $requestInfo;
        }
        return $data;
    }
}

if(! function_exists('combineArray')){

    /**
     *  组合数组元素
     * @param $data， 数据信息
     * @param $fieldConfig 组合字段配置
     * @return array
     */
    function combineArray($data, $fieldConfig)
    {
        $rt = [];
        foreach($fieldConfig as  $fromField => $field){
            $rt[$field] = isset($data[$fromField]) ? $data[$fromField] : '';
        }
        return $rt;
    }
}

if(! function_exists('getLastDate'))
{
    /**
     * PHP返回前几天的日期
     * @param int $offset
     * @return false|string
     */
    function getLastDate($offset = 1) {
        $offset = $offset ? $offset : 1;
        $date = mktime(0,0,0,date("m"),date("d") - $offset,date("Y"));
        return date("Y-m-d", $date);
    }

}

if(! function_exists('getTodayDate'))
{
    /**
     * PHP返回今天的日期
     * @return mixed
     */
    function getTodayDate() {
        $today=date("Y-m-d");
        return $today;
    }
}

if(! function_exists('getNextDate'))
{
    /**
     * PHP返回明天的日期
     * @param int $offset
     * @return false|string
     */
    function getNextDate($offset = 1) {
        $offset = $offset ? $offset : 1;
        $date = mktime(0,0,0,date("m"),date("d") + $offset,date("Y"));
        return date("Y-m-d", $date);
    }
}

if (!function_exists('minuteDiff')) {
    /**
     * 计算2个日期之间相差的分钟
     *
     * @param string $date1 yyyy-mm-dd, mm/dd/yyyy
     * @param string $date2 yyyy-mm-dd, mm/dd/yyyy
     * @return integer
     */
    function minuteDiff($date1, $date2)
    {
        $t1 = strtotime($date1);
        $t2 = strtotime($date2);

        if ($t1 > $t2)
        {
            $t = $t2;
            $t2 = $t1;
            $t1 = $t;
        }

        return round(($t2 - $t1) / 60);
    }
}
if (!function_exists('secondsDiff')) {
    /**
     * 计算2个日期之间相差的秒数
     *
     * @param string $date1 yyyy-mm-dd, mm/dd/yyyy
     * @param string $date2 yyyy-mm-dd, mm/dd/yyyy
     * @return integer
     */
    function secondsDiff($date1, $date2)
    {
        $t1 = strtotime($date1);
        $t2 = strtotime($date2);

        if ($t1 > $t2)
        {
            $t = $t2;
            $t2 = $t1;
            $t1 = $t;
        }

        return $t2 - $t1;
    }
}

if(! function_exists('splitString')){
    /**
     * 拆分字符串
     * @param $string
     * @param string $salt
     * @return array
     */
    function splitString($string, $salt = '###')
    {
        return explode($salt, $string);
    }
}

if (!function_exists('guid'))
{
    /**
     * 生成GUID方法
     * @return string
     */
    function guid(){
        if (function_exists('com_create_guid')){
            return com_create_guid();
        }else{
            mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);// "-"
            // chr(123)// "{"
            // chr(125)// "}"
            $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid,12, 4).$hyphen
                .substr($charid,16, 4).$hyphen
                .substr($charid,20,12);
            return $uuid;
        }
    }
}

if(! function_exists('getYearMonth')){

    function getYearMonthByYear($year, $format = 'Y-m'){
        $month = [];
        $s = date('Y-m-d', strtotime($year . '-01-01'));
        $e = date('Y-m-d', strtotime('+12 months', strtotime($s)));
        $begin = new Datetime($s);
        $end = new Datetime($e);

        //$end = $end->modify('+1 month');
        $interval = new DateInterval('P1M');
        $dateRange = new DatePeriod($begin, $interval, $end);
        foreach($dateRange as $date){
            $m = $date->format($format);
            $month[$m] = $m;
        }
        return $month;
    }
}

if(! function_exists('getYearMonthByDay'))
{
    /**
     * 获取时间段范围的年月份
     * @param type $startdate
     * @param type $enddate
     * @return type
     */
    function getYearMonthByDay ($startdate, $enddate){
        $month = [];
        if (!$startdate || ! $enddate){
            return [];
        }
        $starttime = strtotime($startdate);
        $endtime = strtotime($enddate);
        $num = ($endtime - $starttime)/ (24*3600);
        for($i = 0; $i <= $num; $i ++){
            $m = date('Y-m', $starttime);
            $month[$m] = $m;
            $starttime = $starttime + (24*3600);
        }
        return $month;
    }
}

if (! function_exists('cli_system_ip')) {
    /**
     * 在命令行下获取系统IP地址
     *
     * @return string
     * @throws Exception | string
     */
    function cli_system_ip()
    {
        if (strtolower(PHP_SAPI) === 'cli') {
            switch (PHP_OS) {
                case 'Darwin':
                    $ip = exec('ifconfig | grep inet | grep -v inet6 | grep -v 127 | cut -d " " -f2');
                    break;
                case 'Linux' :
                    $ip = exec('/sbin/ip a | grep inet | grep -v inet6 | grep -v 127 | sed "s/^[ \t]*//g" | cut -d " " -f2');
                    break;
                default :
                    throw new Exception(PHP_OS . ' 系统不支持');
            }
        } else {
            $ip = request()->server('SERVER_ADDR');
        }

        $ip = explode('/', $ip)[0];

        $longIP = ip2long($ip);

        if ($longIP === false || ($longIP >= ip2long('127.0.0.1') && $longIP <= ip2long('127.255.255.255'))) {
            throw new Exception('获取IP地址出错');
        }
        return $ip;
    }
}

if (! function_exists('secToTime')) {
    /**
     * 把秒数转换为时分秒的格式
     *
     * @param int $times
     * @return string
     */
    function secToTime(int $times, string $format = 'H:i:s')
    {
        $format = $format . '__';
        $hourStr = strBetween('H', 'i', $format);
        $minuteStr = strBetween('i', 's', $format);
        $secondStr = strBetween('s', '__', $format);

        $result = '0' . $secondStr;
        if ($times > 0) {
            $result = '';
            if (empty($hour = floor($times/3600)) === false) {
                $result = $hour . $hourStr;
            }

            if (empty($minute = floor(($times-3600 * $hour)/60)) === false){
                $result .= $minute . $minuteStr;
            }

            if (empty($second = floor((($times-3600 * $hour) - 60 * $minute) % 60)) === false) {
                $result .= $second . $secondStr;
            }
        }

        return $result;
    }
}

if (! function_exists('strBetween')) {
    /**
     * 获取两个字符串之间的字符串
     *
     * @param string $beginStr
     * @param string $endStr
     * @param string $string
     * @return string
     */
    function strBetween(string $beginStr, string $endStr, string $string)
    {
        $begin = mb_strpos($string, $beginStr) + mb_strlen($beginStr);
        $end = mb_strpos($string, $endStr) - $begin;
        return mb_substr($string, $begin, $end);
    }
}

if(! function_exists('secTime')){

    function secTime($time)
    {
        if(! $time) return '0秒';
        $str = '';
        $dt = ['hour' => 0, 'minute' => 0, 'second' => 0,];

        if($time >= 3600){
            $dt['hour'] = floor($time / 3600);
            $time = $time % 3600;
            $str .= $dt['hour'] . '小时';
        }

        if($time >= 60){
            $dt['minute'] = floor($time / 60);
            $time = $time % 60;
            $str .= $dt['minute'] . '分钟';
        }

        $dt['second'] = $time;

        if($dt['second']){
            $str .= $dt['second'] . '秒';
        }

        return $str;
    }
}

if(! function_exists('array_orderby'))
{
    /**
     * 二维数组排序
     * $sorted = array_orderby($data, $key1, $sort1, $key2, $sort2);
     * $data 要排序的数据
     * $key1 第一规则字段名
     * $sort1 第一排序规则(SORT_DESC  /   SORT_ASC)
     * $key2 第二规则字段名
     * $sort2 第二排序规则
     * @return mixed
     */
    function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);

        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    if(is_string($row[$field])){
                        $tmp[$key] = iconv('UTF-8', 'GB2312//IGNORE', $row[$field]);
                    }else{
                        $tmp[$key] = $row[$field];
                    }
                $args[$n] = $tmp;
            }
        }

        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
}

if(! function_exists('getDateFromRange')){
    /**
     * 获取指定日期段内每一天的日期
     * @param  Date  $startdate 开始日期
     * @param  Date  $enddate   结束日期
     * @return Array
     */
    function getDateFromRange($startdate, $enddate){

        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);

        // 计算日期段内有多少天
        $days = ($etimestamp-$stimestamp)/86400+1;

        // 保存每天日期
        $date = array();

        for($i=0; $i<$days; $i++){
            $date[] = date('Y-m-d', $stimestamp+(86400*$i));
        }

        return $date;
    }

}

/**
 * 将数组按某一键重组为二维数组
 * @param $field  字段
 * @param $data   数据
 * @param $value  值
 */
if (!function_exists('fieldToArrayByData'))
{
    function fieldToArrayByData ($field, $data = [], $value = '')
    {
        if (empty($field) || empty($data)){
            return [];
        }
        $result = [];
        foreach ($data as $val) {
            if ($value) {
                $result[$val[$field]][] = isset($val[$value]) ? $val[$value] : '';
            }else{
                $result[$val[$field]][] = $val;
            }
        }
        return $result;
    }
}

/**
 * 将数组每个元素增加另一个数组的指定的值
 * @param $field  字段
 * @param $data   数据
 * @param $value  值
 */
if (!function_exists('unionTwoArrayByKey'))
{
    function unionTwoArrayByKey (& $first = [], $second = [], $columns = [], $field = '')
    {
        if (empty($first) || empty($second)) {
            return [];
        }
        foreach ($second as $key => $val) {
            if (array_key_exists($field, $val)) {
                foreach ($val as $k => $v) {
                    if (in_array($k, $columns)) {
                        $first[$key][$k] = $v;
                    }
                }
            }
        }

        return $first;
    }
}

if(! function_exists('convertStrToArray'))
{
    function convertStrToArray($str, $seperator = ',')
    {
        if(is_string($str)){
            $str = explode($seperator, $str);
        }
        return $str;
    }
}

/**
 * 分词
 */
if (!function_exists('elasticParticiple')) {
    function elasticParticiple(string $text)
    {
        $url = 'http://hxsd-bd.hxsd.local:9200/record/_analyze?analyzer=ik_smart&pretty=true&text=';
        return json_decode(file_get_contents($url.urlencode($text)), true);
    }
}

if(! function_exists('getTodayTime'))
{
    function getTodayTime()
    {
        $date = date('Y-m-d');

        return strtotime($date);
    }
}

if(! function_exists('getNextTime'))
{
    function getNextTime($offset = 1) {
        $offset = $offset ? $offset : 1;
        $date = mktime(0,0,0,date("m"),date("d") + $offset,date("Y"));
        return strtotime(date("Y-m-d", $date));
    }
}


if (!function_exists('getTodayStartTime'))
{
    /**
     * 获取今天开始时间的时间戳
     *
     * @return integer
     */
    function getTodayStartTime()
    {
        return strtotime(date('Y-m-d') . ' 00:00:00');
    }
}

if (!function_exists('getTodayEndTime'))
{
    /**
     * 获取今天结束时间的时间戳
     *
     * @return integer
     */
    function getTodayEndTime()
    {
        return strtotime(date('Y-m-d') . ' 23:59:59');
    }
}

if (!function_exists('getBeginAndEndTime'))
{
    /**
     * 获取某天的开始结束时间戳
     *
     * @param string $day
     * @param integer $begin
     * @param integer $end
     */
    function getBeginAndEndTime($day, & $begin, & $end)
    {
        $begin = getUtCtime($day);
        $end = getUTCtime($day . ' 23:59:59');
    }
}

if (!function_exists('getHourRange'))
{
    /**
     * 获取时段范围
     * @param $begin 08:00:00
     * @param $end   23:00:00
     */
    function getHourRange($begin, $end)
    {
        $hour = [];
        $sh = explode(':', $begin);
        $eh = explode(':', $end);
        if(! $sh || ! $eh) return $hour;
        $sh[0];
        $eh[0];

        for($i = $sh[0]; $i <= $eh[0]; $i ++){
            $h = str_pad($i, 2, 0, STR_PAD_LEFT) . ':' . $sh[1] . ':' . $sh[2];
            $hour[$h] = $h;
        }
        return $hour;
    }
}

if (!function_exists('array_numeric'))
{

    /**
     * 将数组中每个元素字符串数字强转数字
     * @param $data
     */
    function array_numeric($data)
    {
        foreach($data as &$value){
            foreach($value as &$v){
                if(is_numeric($v)){
                    $v = (int) $v;
                }
            }
        }
        return $data;
    }
}

if(! function_exists('getMillisecond'))
{
    /**
     * 获取当前时间戳（毫秒）
     * @return array|string
     */
    function getMillisecond()
    {
        $time = explode (" ", microtime () );
        $time = $time [1] . ($time [0] * 1000);
        $time2 = explode ( ".", $time );
        $time = $time2 [0];
        return $time;
    }
}

if(! function_exists('getDateMillisecond'))
{
    /**
     * 获取指定时间毫秒数
     * @param $date
     * @return false|int
     */
    function getDateMillisecond($date)
    {
        return strtotime($date) * 1000;
    }
}

if(! function_exists('convertTimeByMillisecond'))
{
    /**
     * 将含毫秒时间戳转为秒时间戳
     * @param $millisencond
     * @return float
     */
    function convertTimeByMillisecond($millisencond)
    {
        return round($millisencond / 1000);
    }
}

if(! function_exists('convertDateByMillisecond'))
{
    /**
     * 将含毫秒时间戳转为格式化时间
     * @param $millisencond
     * @return false|string
     */
    function convertDateByMillisecond($millisencond)
    {
        $time = round($millisencond / 1000);
        return date('Y-m-d H:i:s', $time);
    }
}

if(! function_exists('object_array'))
{
    //调用这个函数，将其幻化为数组，然后取出对应值
    function object_array($array)
    {
        if(is_object($array)){
            $array = (array)$array;
        }
        if(is_array($array)){
            foreach($array as $key=>$value){
                $array[$key] = object_array($value);
            }
        }
        return $array;
    }
}

if(! function_exists('convert_iconv'))
{
    function convert_iconv($str, $in_charset = 'utf-8', $out_charset = 'GB2312')
    {
        return iconv($in_charset, $out_charset . '//IGNORE', $str);
    }
}

/**
 * 随机生成干扰码
 * @param  integer $length [description]
 * @return [type]          [description]
 */
if (!function_exists('getSalt')) {
    function getSalt($length = 4)
    {
        $arr = array(
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '0',
            'a',
            'b',
            'c',
            'd',
            'e',
            'f',
            'g',
            'h',
            'i',
            'j',
            'k',
            'l',
            'm',
            'n',
            'o',
            'p',
            'q',
            'r',
            's',
            't',
            'u',
            'v',
            'w',
            'x',
            'y',
            'z',
            'A',
            'B',
            'C',
            'D',
            'E',
            'F',
            'G',
            'H',
            'I',
            'J',
            'K',
            'L',
            'M',
            'N',
            'O',
            'P',
            'Q',
            'R',
            'S',
            'T',
            'U',
            'V',
            'W',
            'X',
            'Y',
            'Z'
        );
        $salt = '';
        for ($i = 0; $i < $length; $i++)
        {
            $salt .= $arr[rand(0, 61)];
        }
        return $salt;
    }
}

if(! function_exists('dateFormat'))
{
    function dateFormat($time = 0, $format = 'Y-m-d H:i:s')
    {
        if(! $time) $time = time();
        return date($format, $time);
    }
}

if(! function_exists('_isset'))
{
    function _isset(array $data, string $field, $default = '')
    {
        if(! $data || ! $field) return '';

        return isset($data[$field]) && $data[$field] ? $data[$field] : $default;
    }
}