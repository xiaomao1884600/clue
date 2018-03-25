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
        $orders = $params['order'];
        $pagesize = isset($params['pagesize']) && $params['pagesize'] ?: 1;
        $page = isset($params['page']) && $params['page'] ?: 2;
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
        if(isset($orders['document_type']) && $params['document_type']){
            $query->orderBy($table.'.document_type', 'DESC');
        }else{
            $query->orderBy($table.'.document_type', 'ASC');
        }
        $total = $query->count();
        if(!$isAll){
            $query->take($pagesize);
            $query->skip(($page - 1) * $pagesize);
        }
        $query = $query->get();
        return $query && count($query) ? ['data' => $query->toArray(), 'total' => $total] : ['data' => [], 'total' => 0];
    }
}