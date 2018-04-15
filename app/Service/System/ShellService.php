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
    protected $testMacAddress = array (
        0 => 'MACAddress',
        1 => '',
        2 => '',
        3 => '',
        4 => '',
        5 => '',
        6 => '',
        7 => '',
        8 => 'B8:CA:3A:7A:6D:E6',
        9 => '',
        10 => '',
        11 => '20:41:53:59:4E:FF',
        12 => '',
        13 => '',
        14 => '',
        15 => '',
        16 => '',
        17 => 'B8:CA:3A:7A:6D:E6',
        18 => '',
        19 => '00:50:56:C0:00:01',
        20 => '00:50:56:C0:00:08',
        21 => '',
        22 => '',
        23 => '',
        24 => '',
    );
    public function __construct()
    {
        parent::__construct();
    }

    public function getCpu(array $params = [])
    {
        $cpu = [];
        $macAddress = '';

        //exec("/bin/ls -l", $cpu);
        //exec("arp -a", $cpu);
        //exec("ipconfig /all", $a);
        //exec('top n 1 b i', $top, $error);
        $phpOs = strtolower(PHP_OS);

        if('darwin' == $phpOs){
            exec("ifconfig", $cpu);
        }elseif('linux' == $phpOs){
            exec("arp -a", $cpu);
        }elseif('winnt' == $phpOs){
            exec("wmic nicconfig get macaddress", $cpu);
            $macAddress = $cpu[8] ?? '';

        }

//        $cpu = $this->testMacAddress;
//        $cpu = $cpu[8] ?? '';

        return [
            'macAddress' => $macAddress,
            'secretKey' => md5($macAddress),
        ];

    }
}