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
}