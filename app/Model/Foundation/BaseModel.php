<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/11
 * Time: 下午6:02
 */
namespace App\Model\Foundation;

use Illuminate\Database\Eloquent\Model;
use DB;

class BaseModel extends Model
{
    /**
     * BaseModel constructor.
     * 查询执行sql
     * //DB::connection()->enableQueryLog();
     *  执行sql操作
     * //$sql = DB::getQueryLog();
     */

    protected $pdo;

    public function __construct()
    {
        $this->pdo = self::getConnection()->getPdo();
        parent::__construct();
    }

    /**
     * 保存信息
     * @param type $condition
     * @param type $tablename
     * @return type
     */
    public function saveTable($condition, $tablename = '')
    {
        $tablename = $tablename ? $tablename : $this->table;
        return DB::table($tablename)
            ->insertGetId($condition);
    }

    /**
     * 保存信息
     * @param type $condition
     * @param type $tablename
     * @return type
     */
    public function saveTableBatch($condition, $tablename = '')
    {
        $tablename = $tablename ? $tablename : $this->table;
        return DB::table($tablename)
            ->insert($condition);
    }

    /**
     *  pdo方式
     * @return mixed
     */
    public function getPdo(){
        return  $this->pdo;
    }

    /**
     * 执行sql
     * @param $sql
     * @param array $params
     * @return mixed
     */
    public function getRecords($sql , $params = []){
        $pdo = $this->getPdo();
        $records = $this->getPdo()->prepare($sql);

        $records->execute($params);

        return $records->fetchAll($pdo::FETCH_ASSOC);
    }

    /**
     * 获取表结构
     * @param type $tablename
     * @param type bool 为 true时保留主键
     * @return type
     */
    public function getTableDesc ($tablename = '', $leaveKey = false)
    {
        $tableField = [];
        if(empty($tablename)) return $tableField;
        $row = $this->getRecords(" DESCRIBE {$tablename}", []);
        $this->primaryKey = '';
        if ($row){
            foreach ($row as $value){
                if ('PRI' === $value['Key']){
                    $this->primaryKey = $value['Field'];
                    if($leaveKey){
                        $tableField[$value['Field']] =  preg_replace("#\(.*\)#", "", $value['Type']);
                    }
                }
                else{
                    $tableField[$value['Field']] =  preg_replace("#\(.*\)#", "", $value['Type']);
                }
            }
        }
        return $tableField;
    }


    /**
     * 检测字段是否存在
     * @param type $data
     * @param type $tablename
     * @param type $field
     * @param type $exist
     * @return type
     */
    public function checkField ($data, $tablename, $field)
    {
        return isset($data[$field]) ? TRUE : FALSE;
    }

    /**
     * 批量保存更新数据
     * @param type $data
     * @param type $primaryKey
     * @param type $tablename
     * @return string
     */
    public function insertUpdateBatch($data, $primaryKey = '', $tablename = '', $connection = '')
    {
        $tablename = $tablename ? $tablename : (isset($this->table) ? $this->table : '');
        if (! $tablename || ! is_array($data)){
            throw new \Exception('tablename or data does not exists', 3306);
        }
        $sql = "";
        $iv = [];
        $insertValues = [];
        $updateValues = [];
        $affected = 0;
        if (! $data){
            return 1;
        }
        //获取表结构
        $tableField = $this->getTableDesc($tablename,true);
        $key = $primaryKey ? $primaryKey : $this->primaryKey;
        if (empty($tableField) || empty($key)){
            throw new \Exception('The database structure is incorrect', 3306);
        }
        foreach ($data as $k => $item){
            // 判断null值
            foreach($item as $dk => &$dv){
                if($dv === null){
                    $dv = '';
                }
            }

            $iv = [];
            foreach ($tableField as $field => $value){
                $exist = $this->checkField($item, $tablename, $field);
                if ($exist){
                    $insertField[$field] = "`" . $field . "`";
                    $iv[$field] = sprintf("'%s'", $this->filterContent($item[$field]));
                    $updateValues[$field] = sprintf("`%s` = VALUES(`%s`)", $field, $field);
                }
            }
            $insertValues[] = "(" . join(',', $iv) . ")";
            unset($data[$k]);
        }

        if (empty($insertField) || empty($insertValues) || empty($updateValues)){
            throw new \Exception('The data  format is incorrect', 3306);
        }
        $insertField = join(',', $insertField);
        $insertValues = join(',', $insertValues);
        $updateValues = join(',', $updateValues);
        $sql = " INSERT INTO {$tablename} ($insertField) VALUES {$insertValues} ON DUPLICATE KEY UPDATE {$updateValues}";
        //DB::beginTransaction();
        $affected = $connection ? DB::connection($connection)->statement($sql) : DB::statement($sql);
        //DB::commit();
        unset($data, $insertField, $insertValues, $updateValues, $sql);
        return $affected;
    }

    public function beginTransaction($connection = '') {
        $connection ? DB::connection($connection)->beginTransaction() : DB::beginTransaction();
    }


    public function commit($connection = '') {
        $connection ? DB::connection($connection)->commit() : DB::commit();
    }

    /**
     * 执行SQL
     * @param type $sql
     * @return type
     */
    public function execSql($sql)
    {
        return DB::statement($sql);
    }

    /**
     * 过滤内容 表情符号
     * @param type $string
     * @return type
     */
    public function filterContent($string = "")
    {
        $string = (string) $string;
        $string = filterEmoji($string);
        $string = addslashes($string);
        return preg_replace('/\xEE[\x80-\xBF][\x80-\xBF]|\xEF[\x81-\x83][\x80-\xBF]/', '', $string);
    }

    public function getTableName()
    {
        return $this->table;
    }
}