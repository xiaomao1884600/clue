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
    
    /**
     * 立案案件登记列表、详情
     * 
     * @param type $params
     * @return type
     */
    public function getCaseListService($params = [])
    {
        $mark = false;
        if(isset($params['case_num']) && $params['case_num']){
            $mark = true;
        }
        $rowData = $this->caseRep->getCaseListRep($params, $mark);
        //如果是根据被反映人获取的数据，则对数据进行分页签处理
        if($mark == false){
            return $rowData;
        }else{
            return $this->processDataArr($rowData);
        }
    }
    
    /**
     * 处理返回数据
     * 
     * @param type $rowData
     * @return array
     */
    public function processDataArr($rowData)
    {
        if(empty($rowData)) return [];
        $res = [];
        //案件基础数据及页签名称，详情detail,两规两指情况sheet,立案情况sheet2,违纪违法行为sheet3,审理情况sheet4,结案情况sheet5,公检法等处理情况sheet6,线索情况sheet7,线索文书sheet8
        foreach ($rowData as $val){
            $res['detail'] = [
                'is_dw' => $val['is_dw'], 'reflected_name' => $val['reflected_name'], 'gender' => $val['gender'],
                'nation' => $val['nation'], 'year_of_birth' => $val['year_of_birth'], 'age' => $val['age'],
                'political' => $val['political'], 'party_time' => $val['party_time'], 'education' => $val['education'],
                'company' => $val['company'], 'level' => $val['level'], 'cadre_auth' => $val['cadre_auth'],
                'working_time' => $val['working_time'], 'is_deputies' => $val['is_deputies'], 'is_cppcc_members' => $val['is_cppcc_members'],
                'is_civil_servant' => $val['is_civil_servant'], 'is_supervision' => $val['is_supervision'], 'department' => $val['department'],
                'department1' => $val['department1'], 'enterprise_nature' => $val['enterprise_nature'], 'enterprise_nature1' => $val['enterprise_nature1'],
                'enterprise_level' => $val['enterprise_level'], 'station' => $val['station'], 'enterprise_persion' => $val['enterprise_persion'],
                'leader_violate' => $val['leader_violate'], 'political_status' => $val['political_status']
            ];
            $res['sheet'] = [//两指两规情况
                'use_measures' => $val['use_measures'], 'begin_time' => $val['begin_time'], 'end_time' => $val['end_time'],
                'lglzjg' => $val['lglzjg']
            ];
            $res['sheet2'] = [//立案情况
                'case_num' => $val['case_num'], 'case_user_num' => $val['case_user_num'], 'filing_time' => $val['filing_time'],
                'case_source' => $val['case_source'], 'first_violation_time' => $val['first_violation_time'], 'end_violation_time' => $val['end_violation_time'],
                'other_process_time' => $val['other_process_time'], 'survey_end_time' => $val['survey_end_time'],
                'brief_case' => $val['brief_case'], 'filing_report' => $val['filing_report'], 'filing_decision' => $val['filing_decision'],
                'survey_report' => $val['survey_report'], 'memo' => $val['memo']
            ];
            $res['sheet3'] = [//违纪违法行为
                'disciplinary_action' => $val['disciplinary_action'], 'law_related' => $val['law_related']
            ];
            $res['sheet4'] = [//审理情况
                'trial_accept_time' => $val['trial_accept_time'], 'trial_office' => $val['trial_office'], 'trial_end_time' => $val['trial_end_time'],
                'trial_report' => $val['trial_report']
            ];
            $res['sheet5'] = [//结案情况
                'violation_money' => $val['violation_money'], 'loss' => $val['loss'], 'collected_amount' => $val['collected_amount'],
                'recover_amount' => $val['recover_amount'], 'close_time' => $val['close_time'], 'party_discipline' => $val['party_discipline'],
                'view_year' => $val['view_year'], 'political_disposition' => $val['political_disposition'], 'other_process' => $val['other_process'],
                'organizational' => $val['organizational'], 'abscond_time' => $val['abscond_time'], 'sneak_away' => $val['sneak_away'],
                'fleeing_details' => $val['fleeing_details'], 'captured_time' => $val['captured_time'], 'sales_time' => $val['sales_time'],
                'accountability' => $val['accountability'], 'punish' => $val['punish'], 'analysis' => $val['analysis'],
            ];
            $res['sheet6'] = [//公检法等处理情况
                'transfer_time' => $val['transfer_time'], 'process_time' => $val['process_time'], 'public_inspection' => $val['public_inspection'],
                'public_inspection_two' => $val['public_inspection_two'], 'judicial_amount' => $val['judicial_amount']
            ];
        }
        return $res;
    }
}