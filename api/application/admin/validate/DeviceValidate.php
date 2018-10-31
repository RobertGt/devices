<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/31 23:36
 * Email: 1183@mapgoo.net
 */

namespace app\admin\validate;


use app\admin\model\DeviceModel;
use think\Validate;

class DeviceValidate extends Validate
{
    protected $rule = [
        'deviceId'      => 'require',
        'deviceImei'    => 'require|checkImei',
        'deviceName'    => 'require',
        'deviceTypeId'  => 'require'
    ];

    protected $scene = [
        'checkId'      =>  ['deviceId'],
        'insert'       =>  ['deviceImei', 'deviceName', 'deviceTypeId'],
        'update'       =>  ['deviceId', 'deviceImei', 'deviceName', 'deviceTypeId']
    ];

    public function checkImei($deviceImei, $rule, $data)
    {
        $where['deviceImei'] = $deviceImei;
        if(isset($data['deviceId'])){
            $where['deviceId'] = ['neq', $data['deviceId']];
        }
        $admin = (new DeviceModel())->where($where)->field("deviceId")->find();
        if($admin){
            return "设备号已存在";
        }
        return true;
    }
}