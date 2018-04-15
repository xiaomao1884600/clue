<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/20
 * Time: 下午10:08
 */
namespace App\Repository\Document;

use App\Repository\Foundation\BaseRep;
use App\Model\Document\Document;

class DocumentRep extends BaseRep
{
    public function __construct(Document $document)
    {
        $this->documentModel = $document;
    }
    
    /**
     * 新增发文
     * 
     * @param array $data
     * @return type
     */
    public function saveDocumentRep(array $data)
    {
        return $this->documentModel->insert($data);
    }
    
    /**
     * 公文列表
     * 
     * @param array $params
     * @param type $isAll
     * @return type
     */
    public function getDocumentList(array $params, $isAll)
    {
        $table = $this->documentModel->getTableName();
        $orders = isset($params['order']) ? $params['order'] : [];
        $pagesize = (isset($params['pagesize']) && $params['pagesize']) ? $params['pagesize'] : 10;
        $page = (isset($params['page']) && $params['page']) ? $params['page'] : 1;
        $query = $this->documentModel
            ->select('*');
        if(isset($params['document_user']) && $params['document_user']){
            $query->where($table.'.document_user', 'like', "%{$params['document_user']}%");
        }
        if(isset($params['document_user']) && $params['document_user']){
            $query->where($table.'.document_user', '=', $params['document_user']);
        }
        if(isset($params['begin']) && $params['begin']){
            $query->where($table.'.document_date', '>=', $params['begin']);
        }
        if(isset($params['end']) && $params['end']){
            $query->where($table.'.document_date', '<=', $params['end']);
        }
        if(!empty($orders)){
            //防止参数错传，获取表结构进行验证
            $tableRows = $this->documentModel->getTableDesc($table);
            foreach ($orders as $c => $o){
                if($o == 0 && array_key_exists($c, $tableRows))
                    $query->orderBy($table.'.'.$c, 'DESC');
            }
        }
        $total = $query->count();
        if(!$isAll){
            $query->take($pagesize);
            $query->skip(($page - 1) * $pagesize);
        }
        $query = $query->get();
        return $query && count($query) ? ['data' => $query->toArray(), 'total' => $total] : ['data' => [], 'total' => 0];
    }

    /**
     * 公文详情
     * @param array $condition
     * @return array
     */
    public function getDocumentById(array $condition)
    {
        $id = _isset($condition, 'id');
        if(! $id) return [];

        $result = $this->documentModel
            ->where('id',$id)
            ->get()
            ->toArray();

        return $result;
    }
}