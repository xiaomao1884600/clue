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
        if(empty($rowData)) return [];
        $res = [];
        //案件基础数据及页签名称，详情detail,两规两指情况sheet,立案情况sheet2,违纪违法行为sheet3,审理情况sheet4,结案情况sheet5,公检法等处理情况sheet6,线索情况sheet7,线索文书sheet8
        foreach ($rowData as $val){
            $res['detail'] = [
                'units_event' => $val['units_event'], 'reflected_name' => $val['reflected_name'], 'gender' => $val['gender'],
                'nation' => $val['nation'], 'birthday' => $val['birthday'], 'political' => $val['political'], 'political_2' => $val['political_2'],
                'join_party_time' => $val['join_party_time'], 'education' => $val['education'], 'company' => $val['company'],
                'level' => $val['level'], 'cadre_auth' => $val['cadre_auth'], 'join_company_time' => $val['join_company_time'],
                'npc_member' => $val['npc_member'], 'cppcc_member' => $val['cppcc_member'], 'servant' => $val['servant'],
                'supervision' => $val['supervision'], 'department' => $val['department'], 'enterprise_nature' => $val['enterprise_nature'],
                'enterprise_level' => $val['enterprise_level'], 'station' => $val['station'], 'enterprise_persion_level' => $val['enterprise_persion_level'],
                'leader_violate' => $val['leader_violate'], 'party_member' => $val['party_member'], 'supervision_type_two' => $val['supervision_type_two'],
                'unparty_supervision' => $val['unparty_supervision'], 'unsupervision_type_two' => $val['unsupervision_type_two'],
            ];
            $res['sheet'] = [//线索情况
                'clue_number' => $val['clue_number'], 'user_number' => $val['user_number'], 'clue_accept_time' => $val['clue_accept_time'],
                'clue_agency' => $val['clue_agency'], 'clue_verify' => $val['clue_verify'], 'clue_source' => $val['clue_source'],
                'clue_violate' => $val['clue_violate'], 'clue_involve_law' => $val['clue_involve_law'], 'clue_disposal_type' => $val['clue_disposal_type'],
                'clue_disposal_type_2' => $val['clue_disposal_type_2'], 'clue_collection_money' => $val['clue_collection_money'],
                'crime_of_duty' => $val['crime_of_duty'], 'other_offenses' => $val['other_offenses'],
                'clue_redeem_money' => $val['clue_redeem_money']
            ];
            $res['sheet2'] = [//线索文书
                'document_main_content' => $val['document_main_content'], 'document_disposal_report' => $val['document_disposal_report'], 'document_remark' => $val['document_remark'],
                'clue_measures' => $val['clue_measures'], 'clue_measures_count_time' => $val['clue_measures_count_time'],
            ];
        }
        return $res;
    }
}