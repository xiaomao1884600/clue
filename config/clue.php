<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/3/28
 * Time: 下午11:54
 */
return [
    // 系统配置
    'system' => [
        'run_start_time' => '2018-05-01',
        'run_days' => 365,
    ],

    'clue_excel' => [
        'title_rule' => [
            '线索来源' => 'source',
            '编号' => 'number',
            '被反映人姓名' => 'reflection_name',
            '单位' => 'company',
            '职位' => 'post',
            '级别' => 'level',
            '录入时间' => 'entry_time',
            '办结期限' => 'closed_time',
            '处置类型' => 'disposal_type',
            '上级交办' => 'supervisor',
            '提醒天数' => 'remind_days',
            '去向' => 'clue_next',
            '线索状态' => 'clue_state',
            '主要内容' => 'main_content',
            '部门意见' => 'department_opinion',
            '领导批示' => 'leader_approval',
            '备注' => 'remark',
        ],

        'type_rule' => [
            '录入时间' => 'type_gmdate',
            '办结期限' => 'type_gmdate',
        ],

        'dic_rule' => [

        ],
    ],
];