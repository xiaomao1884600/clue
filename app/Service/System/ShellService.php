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

    protected $macInfo = array (
        0 => '',
        1 => 'B8-CA-3A-7A-6D-E6   \\Device\\Tcpip_{77FE3DF6-0848-4AC6-89BA-DDE0620F694E}',
        2 => '???                ??????',
        3 => '00-50-56-C0-00-01   \\Device\\Tcpip_{FC2DD010-8D92-4299-BCF5-8BEAC2B31BD5}',
        4 => '00-50-56-C0-00-08   \\Device\\Tcpip_{95788A17-12DC-408B-B356-9B3D97205168}',
        5 => '???                ??????',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function getCpu(array $params = [])
    {
        $cpu = $info = $macInfo = [];
        $macAddress = '';
        $info = $this->macInfo;

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
            //exec("wmic nicconfig get macaddress", $cpu);
            //$macAddress = $cpu[8] ?? '';

            exec("getmac /NH", $info);

            if($info){
                foreach($info as $key => $value){
                    if(! $value) continue;
                    list($a, $b) = explode('   ', $value);
                    if(is_string($a) && preg_match("/-/", $a, $array)){
                        $macInfo[] = $a;
                    }
                }
            }

            sort($macInfo);
        }

//        $cpu = $this->testMacAddress;
//        $cpu = $cpu[8] ?? '';
        
        $macAddress = json_encode($macInfo);

        return [
            'macAddress' => $macAddress,
            'secretKey' => md5($macAddress),
        ];

    }
}