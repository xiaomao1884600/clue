<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/4/5
 * Time: 下午3:12
 */

namespace App\Model\Clue;


use App\Model\Foundation\BaseModel;

class ExcelConfig extends BaseModel
{
    protected $table = 't_excel_config';

    // 线索
    const OP_TYPE_CLUE = 1;

    // 案件问题线索
    const OP_TYPE_CASE_CLUE = 2;

    // 案件立案
    const OP_TYPE_CASE_FAILING = 3;
}