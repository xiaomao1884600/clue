<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/4/7
 * Time: 下午10:46
 */

namespace App\Service\Cases;


use App\Service\Foundation\BaseService;
use App\Repository\Cases\CaseRep;

class CaseService extends BaseService
{
    public function __construct(CaseRep $caseRep)
    {
        $this->caseRep = $caseRep;
        parent::__construct();
    }
    
    public function getCaseListService($params = [])
    {
        $mark = false;
        if(isset($params['reflected_name']) && $params['reflected_name']){
            $mark = true;
        }
        $rowData = $this->caseRep->getCaseListRep($params, $mark);
        //如果是根据被反映人获取的数据，则对数据进行分页签处理
        if($mark === false){
            return $rowData;
        }else{
            return $this->processDataArr($rowData);
        }
    }
    
    public function processDataArr($rowData)
    {
        return $rowData;
        if(empty($rowData)) return [];
        $res = [];
        //案件基础数据及页签名称，详情detail,两规两指情况sheet,立案情况sheet2,违纪违法行为sheet3,审理情况sheet4,结案情况sheet5,公检法等处理情况sheet6,线索情况sheet7,线索文书sheet8
        foreach ($rowData as $val){
            $res['detail'] = [
                'is_dw' => $val['is_dw'], 'reflected_name' => $val['reflected_name'], 'gender' => $val['gender'],
                'nation' => $val['nation'], 'year_of_birth' => $val['year_of_birth'], 'age' => $val['age'],
                'political' => $val['political'], 'party_time' => $val['party_time'], 'education' => $val['education'],
                'company' => $val['company'], 'is_dw' => $val['is_dw'], 'is_dw' => $val['is_dw'],
                'is_dw' => $val['is_dw'], 'is_dw' => $val['is_dw'], 'is_dw' => $val['is_dw'],
                'is_dw' => $val['is_dw'], 'is_dw' => $val['is_dw'], 'is_dw' => $val['is_dw'],
                'is_dw' => $val['is_dw'], 'is_dw' => $val['is_dw'], 'is_dw' => $val['is_dw'],
                'is_dw' => $val['is_dw'], 'is_dw' => $val['is_dw'], 'is_dw' => $val['is_dw'],
            ];
            $res['sheet'] = [
                
            ];
            $res['sheet2'] = [
                
            ];
            $res['sheet3'] = [
                
            ];
            $res['sheet4'] = [
                
            ];
            $res['sheet5'] = [
                
            ];
            $res['sheet6'] = [
                
            ];
            $res['sheet7'] = [
                
            ];
            $res['sheet8'] = [
                
            ];
        }
        return $res;
    }
}