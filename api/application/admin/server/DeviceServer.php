<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/31 22:52
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\DeviceModel;
use app\admin\model\DeviceTypeModel;
use think\db\Expression;
use think\Exception;
use think\Log;

class DeviceServer
{
    public function deviceList($param = [], $aid = 0)
    {
        $where = [];
       // if($aid != 1){
            $where[] = ['exp', new Expression("find_in_set({$aid}, a.parentAll)")];
        //}

        if(!empty($param['seach']['device'])){
            $where['d.deviceImei|d.deviceName'] = ['like', '%' . $param['seach']['device'] . '%'];
        }

        $adminModel = new DeviceModel();
        $total = $adminModel->alias('d')->join('sys_admin a', 'a.aid = d.aid', 'LEFT')->where($where)->count();
        $list = $adminModel->alias('d')->where($where)->field('d.deviceId id, a.name, d.deviceImei, d.deviceName, t.name deviceType, d.remark, d.createTime, d.updateTime, d.lastTime')
            ->join('sys_admin a', 'a.aid = d.aid', 'LEFT')
            ->join('sys_devices_type t', 't.deviceTypeId = d.deviceTypeId', 'LEFT')
            ->page($param['pageNum'], $param['pageSize'])
            ->order("d.createTime desc")
            ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['id'] = authcode($info['id'], 'ENCODE');
            $info['deviceState'] = "<div class=\"btn btn-default btn-circle\" type=\"button\"></div>";
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['updateTime'] = date('Y-m-d H:i:s', $info['updateTime']);
            $info['lastTime'] = $info['lastTime'] ? date('Y-m-d H:i:s', $info['lastTime']) : '';
            $info['remark'] = $info['remark'] ? $info['remark']  : '';
            $info['name'] = $info['name'] ? $info['name']  : '';
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function deviceDelete($deviceId)
    {
        try{
            $where['deviceId'] = $deviceId;
            (new DeviceModel())->where($where)->delete();
        }catch (Exception $e){
            Log::error("deviceDelete error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function deviceInsert($param = [], $aid = 0)
    {
        $create['aid'] = $aid;
        $create['deviceImei'] = $param['deviceImei'];
        $create['deviceName'] = $param['deviceName'];
        $create['deviceTypeId'] = $param['deviceTypeId'];
        $create['remark'] = $param['remark'];
        try{
            (new DeviceModel())->create($create);
        }catch (Exception $e){
            Log::error("deviceInsert error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function deviceUpdate($param = [])
    {
        $save['deviceImei'] = $param['deviceImei'];
        $save['deviceName'] = $param['deviceName'];
        $save['deviceTypeId'] = $param['deviceTypeId'];
        $save['remark'] = $param['remark'];
        try{
            (new DeviceModel())->save($save, ['deviceId' => $param['deviceId']]);
        }catch (Exception $e){
            Log::error("deviceUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function deviceInfo($deviceId)
    {
        $where['deviceId'] = $deviceId;

        $deviceInfo = (new DeviceModel())->field('deviceId, deviceImei, deviceName, deviceTypeId, remark')->where($where)->find();

        if($deviceInfo){
            $deviceInfo = $deviceInfo->getData();
            $deviceInfo['deviceTypeId'] = authcode($deviceInfo['deviceTypeId'], 'ENCODE');
            $deviceInfo['deviceId'] = authcode($deviceInfo['deviceId'], 'ENCODE');
        }else{
            $deviceInfo = [];
        }
        return $deviceInfo;
    }

    public function deviceType($deviceTypeId = 0)
    {
        $deviceType = (new DeviceTypeModel())->field('deviceTypeId, name')->select();
        $row = [];
        foreach ($deviceType as $v){
            $info = $v->getData();
            $info['select'] = $deviceTypeId == $info['deviceTypeId'] ? 1 : 0;
            $info['deviceTypeId'] = authcode($info['deviceTypeId'], 'ENCODE');
            $row[] = $info;
        }
        return $row;
    }
}