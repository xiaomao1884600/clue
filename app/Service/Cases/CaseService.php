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
                'is_dw' => $val['is_dw'], 'reflected_name' => $val['reflected_name'], 'gender' => $val['gender'],//是否单位， 被调查人， 性别
                'nation' => $val['nation'], 'year_of_birth' => $val['year_of_birth'], 'age' => $val['age'],//民族， 出生年月， 年龄
                'political' => $val['political'], 'party_time' => $val['party_time'], 'education' => $val['education'],//政治面貌， 入党时间， 学历
                'company' => $val['company'], 'level' => $val['level'], 'cadre_auth' => $val['cadre_auth'],//工作单位及职务， 职级， 干部管理权限
                'working_time' => $val['working_time'], 'is_deputies' => $val['is_deputies'], 'is_cppcc_members' => $val['is_cppcc_members'],//任现职时间， 人大代表， 政协委员
                'is_civil_servant' => $val['is_civil_servant'], 'is_supervision' => $val['is_supervision'], 'department' => $val['department'],//国家公务员， 监察对象， 部门分类
                'department1' => $val['department1'], 'enterprise_nature' => $val['enterprise_nature'], 'enterprise_nature1' => $val['enterprise_nature1'],//部门分类1， 企业性质， 企业性质1
                'enterprise_level' => $val['enterprise_level'], 'station' => $val['station'], 'enterprise_persion' => $val['enterprise_persion'],//企业级别， 岗位， 企业人员类别
                'leader_violate' => $val['leader_violate'], 'political_status' => $val['political_status'], 'dw_code' => $val['dw_code'],//一把手违纪违法， 政治面貌细节， 单位代码
                'dw_title' => $val['dw_title'], 'idcard_type' => $val['idcard_type'], 'idcard' => $val['idcard'],//单位名称， 证件类型， 证件号码
                'supervision' => $val['supervision'], 'supervision_details' => $val['supervision_details'], 'party_member' => $val['party_member'],//是否国家监察对象， 国家监察对象详情情况， 是否中共党员
                'party_member_represent' => $val['party_member_represent'], 'unparty_supervision' => $val['unparty_supervision'],//中共党代表, 非党员非监察对象
                'unparty_supervision_details' => $val['unparty_supervision_details'], 'committee_member' => $val['committee_member'], 'party_committee_member' => $val['party_committee_member'],//非党员非监察对象详情情况, 纪委委员, 党委委员
                'enterprise_persion1' => $val['enterprise_persion1'], 'enterprise_post' => $val['enterprise_post'],//企业人员类别1, 企业岗位
            ];
            $res['sheet'] = [//两指两规情况
                'use_measures' => $val['use_measures'], 'begin_time' => $val['begin_time'], 'end_time' => $val['end_time'],//使用措施， 实施时间， 实施结束时间
                'lglzjg' => $val['lglzjg']//两规两指机关
            ];
            $res['sheet2'] = [//立案情况
                'case_num' => $val['case_num'], 'case_user_num' => $val['case_user_num'], 'filing_time' => $val['filing_time'],//案件编码， 涉案人员编号， 纪委立案时间
                'case_source' => $val['case_source'], 'first_violation_time' => $val['first_violation_time'], 'end_violation_time' => $val['end_violation_time'],//案件来源， 首次违纪时间， 末次违纪时间
                'other_process_time' => $val['other_process_time'], 'survey_end_time' => $val['survey_end_time'], 'filing_office' => $val['filing_office'],//其他处理统计时间， 调查中（终）止时间， 纪委立案机关
                'brief_case' => $val['brief_case'], 'filing_report' => $val['filing_report'], 'filing_decision' => $val['filing_decision'],//简要案情， 立案报告， 立案决定书
                'survey_report' => $val['survey_report'], 'memo' => $val['memo'], 'supervisory_office' => $val['supervisory_office'], //调查报告， 备注， 监委立案机关
                'supervisory_time' => $val['supervisory_time'], 'transfer_inspection' => $val['transfer_inspection'], 'transfer_inspection_type' => $val['transfer_inspection_type'], //监委立案时间， 是否其他纪检监察机关立案后移送，其他纪检监察机关立案后移送方式
            ];
            $res['sheet3'] = [//违纪违法行为
                'disciplinary_action' => $val['disciplinary_action'], 'law_related' => $val['law_related']//违纪行为， 涉法行为
            ];
            $res['sheet4'] = [//审理情况
                'trial_accept_time' => $val['trial_accept_time'], 'trial_office' => $val['trial_office'], 'trial_end_time' => $val['trial_end_time'],//审理受理时间， 受理机关， 审结时间
                'trial_report' => $val['trial_report']//审理报告
            ];
            $res['sheet5'] = [//结案情况
                'violation_money' => $val['violation_money'], 'loss' => $val['loss'], 'collected_amount' => $val['collected_amount'],//违纪总金额（万元），失职渎职损失，  收缴涉案金额（万元）
                'recover_amount' => $val['recover_amount'], 'close_time' => $val['close_time'], 'party_discipline' => $val['party_discipline'],//挽回经济损失（万元）， 结案时间， 党纪处分
                'view_year' => $val['view_year'], 'political_disposition' => $val['political_disposition'], 'other_process' => $val['other_process'],//留党察看年限， 政纪处分， 其他处理
                'organizational' => $val['organizational'], 'abscond_time' => $val['abscond_time'], 'sneak_away' => $val['sneak_away'],//组织措施， 潜逃时间， 潜逃去向
                'fleeing_details' => $val['fleeing_details'], 'captured_time' => $val['captured_time'], 'sales_time' => $val['sales_time'],//潜逃去向细节， 抓获时间， 销案时间
                'accountability' => $val['accountability'], 'punish' => $val['punish'], 'analysis' => $val['analysis'],//责任追究， 处分决定， 案件剖析
                'transfer_unit' => $val['transfer_unit'], 'violation_rules' => $val['violation_rules'], 'is_accountability' => $val['is_accountability'],//移送单位 是否违反中央八项规定精神 是否属于问责
                'government_discipline' => $val['government_discipline'], 'trial_organ' => $val['trial_organ'],//政务处分 审理机关
            ];
            $res['sheet6'] = [//公检法等处理情况
                'transfer_time' => $val['transfer_time'], 'process_time' => $val['process_time'], 'public_inspection' => $val['public_inspection'],//移送司法机关时间， 公检法处理时间， 公检法处理详情情况
                'public_inspection_two' => $val['public_inspection_two'], 'judicial_amount' => $val['judicial_amount']//公检法处理二级， 司法判决金额（万元）
            ];
            $res['sheet7'] = [//留置措施情况
                'measures_office' => $val['measures_office'], 'measures_office_name' => $val['measures_office_name'], 'lien_start_time' => $val['lien_start_time'],//采取措施机关 采取措施机关名称 采取措施机关名称
                'measures_address' => $val['measures_address'], 'measures_address_category' => $val['measures_address_category'], 'lien_approval' => $val['lien_approval'],//采取措施地点 采取措施地点分类 留置审批情况
                'lien_end_time' => $val['lien_end_time'], 'lien_days' => $val['lien_days'], 'delay_days' => $val['delay_days'],//留置结束时间 留置天数 延期天数
                'delay_approval' => $val['delay_approval'], 'transfer_money' => $val['transfer_money'],//延期审批情况 移送司法机关金额（万元）
            ];
        }
        return $res;
    }
}