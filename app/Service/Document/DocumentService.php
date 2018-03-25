<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/20
 * Time: 下午10:01
 */
namespace App\Service\Document;

use App\Service\Foundation\BaseService;
use App\Repository\Document\DocumentRep;
use Validator;

/**
 * 公文管理
 * Class DocumentService
 * @package App\Service\Document
 */

class DocumentService extends BaseService
{
    protected $saveParams = [
        'document_date' => '2018-03-25',
        'document_code' => '政发〔97〕8号',
        'username' => '王大锤',
        'document_type' => 'excel',//发文类型
        'document_title' => '发文标题',
        'document_user' => '发文人',
        'document_unit' => '发文单位',
        'memo' => '备注信息'
    ];
    public function __construct(DocumentRep $documentRep)
    {
        parent::__construct();
        $this->dRep = $documentRep;
    }
    
    /**
     * 新增发文登记
     * 
     * @param array $params
     * @return type
     */
    public function saveDocumentService(array $params)
    {
        //测试参数
        $params = $this->saveParams;
        //校验必填项
        $this->checkPostData($params);
        //过滤特殊字符，防止写入数据报错
        $this->filterPostData($params);
        //执行数据库操作
        $this->dRep->saveDocumentRep($params);
        return [];
    }
    
    /**
     * 校验新增数据必填项
     * 
     * @param array $params
     * @throws \Exception
     */
    public function checkPostData(array $params)
    {
        $validator = Validator::make($params, [
            'document_date' => 'required|date',
            'document_code' => 'required|string',
            'username' => 'required|string',
            'document_type' => 'required|string',
            'document_title' => 'required|string',
            'document_user' => 'required|string',
            'document_unit' => 'required|string',
        ], [
            'document_date.required' => '缺少发文日期',
            'document_code.required' => '缺少发文字号',
            'username.required' => '缺少姓名',
            'document_type.required' => '请选择发文类型',
            'document_title.required' => '请输入文件标题',
            'document_user.required' => '请输入发文人',
            'document_unit.required' => '请输入发文单位',
        ]);
        if($validator->fails()){
            throw new \Exception($validator->errors()->first(), 6666);
        }
    }
    
    /**
     * 过滤表情符号
     * 
     * @param type $params
     */
    public function filterPostData(&$params)
    {
        $params = array_map('filterEmoji', $params);
    }
    
    public function documentListService(array $params)
    {
        
    }
}