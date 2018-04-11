<?php
namespace App\Service\Cases;


use App\Service\Foundation\BaseService;
use App\Repository\Cases\CaseRep;

class ProblemCluesService extends BaseService
{
    public function __construct(CaseRep $caseRep)
    {
        $this->caseRep = $caseRep;
        parent::__construct();
    }
    
    /**
     * 获取问题线索处置情况数据
     * 
     * @param type $params
     * @return type
     */
    public function getProblemList($params = [])
    {
        $mark = false;
        if(isset($params['clue_number']) && $params['clue_number']){
            $mark = true;
        }
        $rowData = $this->caseRep->getProblemListRep($params, $mark);
        //如果是根据被反映人获取的数据，则对数据进行分页签处理
        if($mark === false){
            return $rowData;
        }else{
            return $this->processListArr($rowData);
        }
    }
    
    /**
     * 处理返回数据
     * 
     * @param type $rowData
     * @return array
     */
    public function processListArr($rowData)
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