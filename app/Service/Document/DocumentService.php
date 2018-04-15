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
use Excel;

/**
 * 公文管理
 * Class DocumentService
 * @package App\Service\Document
 */

class DocumentService extends BaseService
{
    
    protected $dacumentHeader = [
        [
            'document_date' => '发文日期',
            'document_code' => '发文字号',
            'document_title' => '文件标题',
            'document_user' => '发文人',
            'document_unit' => '发文单位',
            'memo' => '备注'
        ]
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
    
    /**
     * 公文列表
     * 
     * @param array $params
     * @return type
     */
    public function documentListService(array $params)
    {
        //检查查询日期是否正确
        $this->checkSearchDate($params);
        //拼装最终搜索条件
        $condition = $this->processSearchCondition($params);
        $isAll = false;
        if(isset($params['export']) && $params['export']) $isAll = true;
        //执行查询
        $res = $this->dRep->getDocumentList($condition, $isAll);
        //导出功能
        if($isAll && !empty($res['data'])){
            //过滤多余字段
            $data = [];
            foreach($res['data'] as $val){
                $data[] = [
                    'document_date' => $val['document_date'],
                    'document_code' => $val['document_code'],
                    'document_title' => $val['document_title'],
                    'document_user' => $val['document_user'],
                    'document_unit' => $val['document_unit'],
                    'memo' => $val['memo']
                ];
            }
            $this->documentExport($data);
        }
        return $res;
    }
    
    /**
     * 校验搜索日期
     *
     * @param array $params
     * @throws \Exception
     */
    public function checkSearchDate(array $params)
    {
        $beginDateline =  isset($params['beginDate']) ? strtotime($params['beginDate'] . ' 00:00:00') : 0;
        $endDateline =  isset($params['endDate']) ? strtotime($params['endDate'] . '23:59:59') : 0;
        if($endDateline && $beginDateline > $endDateline){
            throw new \Exception("开始日期不能晚于结束日期");
        }
    }
    
    /**
     * 处理搜索条件
     *
     * @param array $params
     * @return array
     */
    public function processSearchCondition(array $params)
    {
        $condition = [];
        if(is_array($params['orders']) && !empty($params['orders'])){
            foreach($params['orders'] as $val){
                $condition['order'][$val['column']] = (int)$val['order'];
            }
        }
        $condition['document_type'] = $params['document_type'];
        $condition['document_user'] = $params['document_user'];
        $condition['begin'] = $params['beginDate'] ? $params['beginDate'] . ' 00:00:00' : 0;
        $condition['end'] = $params['endDate'] ? $params['endDate'] . ' 23:59:59' : 0;
        return $condition;
    }
    
    /**
     * 公文导出
     * 
     * @param array $cellData
     */
    public function documentExport(array $cellData)
    {
        $cellData = array_merge_recursive($this->dacumentHeader, $cellData);
        Excel::create('发文数据',function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }

    /**
     * 公文详情
     * @param array $params
     * @return array|mixed
     * @throws \Exception
     */
    public function documentView(array $params)
    {
        $id = _isset($params, 'id');

        if(! $id){
            throw new \Exception('id required !');
        }

        $result = $this->dRep->getDocumentById(['id' => $id]);
        $result = $result ? current($result) : [];
        return $result;
    }
}