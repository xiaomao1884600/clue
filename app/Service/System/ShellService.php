<?php
/**
 * Created by PhpStorm.
 * User: wangwujun
 * Date: 2018/4/11
 * Time: 下午11:19
 */
namespace App\Service\System;

use App\Service\Foundation\BaseService;

class ShellService extends BaseService
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCpu(array $params)
    {
        exec('wmic nicconfig get macaddress', $cpu);

        x($cpu);
    }
}