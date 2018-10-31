<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/31 22:51
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\DeviceServer;
use app\admin\validate\DeviceValidate;
use think\Request;

class Device extends Base
{
    public function deviceList(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
        ];

        $response = (new DeviceServer())->deviceList($param, $this->adminInfo['aid']);

        ajax_info(0,'success', $response);
    }

    public function deviceDelete(Request $request)
    {
        $param = [
            'deviceId'  => authcode($request->param('id', ''))
        ];

        $validate = new DeviceValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new DeviceServer())->deviceDelete($param['deviceId']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function deviceType(Request $request)
    {
        $param = [
            'deviceTypeId'  => authcode($request->param('deviceTypeId', ''))
        ];
        $response = (new DeviceServer())->deviceType($param['deviceTypeId']);
        ajax_info(0,'success', $response);
    }

    public function deviceInsert(Request $request)
    {
        $param = [
            'deviceImei'     => $request->param('deviceImei',''),
            'deviceName'     => $request->param('deviceName',''),
            'deviceTypeId'   => authcode($request->param('deviceTypeId', '')),
            'remark'         => $request->param('remark','')
        ];

        $validate = new DeviceValidate();
        if(!$validate->scene('insert')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new DeviceServer())->deviceInsert($param, $this->adminInfo['aid']);

        if($response){
            ajax_info(0,'Success');
        }else{
            ajax_info(1,'添加失败');
        }
    }

    public function deviceUpdate(Request $request)
    {
        $param = [
            'deviceId'       => authcode($request->param('id', '')),
            'deviceImei'     => $request->param('deviceImei',''),
            'deviceName'     => $request->param('deviceName',''),
            'deviceTypeId'   => authcode($request->param('deviceTypeId', '')),
            'remark'         => $request->param('remark','')
        ];

        $validate = new DeviceValidate();
        if(!$validate->scene('insert')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new DeviceServer())->deviceUpdate($param, $this->adminInfo['aid']);

        if($response){
            ajax_info(0,'Success');
        }else{
            ajax_info(1,'添加失败');
        }
    }

    public function deviceInfo(Request $request)
    {
        $param = [
            'deviceId'       => authcode($request->param('id','')),
        ];

        $validate = new DeviceValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new DeviceServer())->deviceInfo($param['deviceId']);
        if($response){
            ajax_info(0,'success', $response, false);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function adminUpdate(Request $request)
    {
        $param = [
            'aid'       => authcode($request->param('id',  '')),
            'account'  => $request->param('username',''),
            'password' => $request->param('editPassword',''),
            'name'     => $request->param('name',''),
            'rid'      => authcode($request->param('rid', '')),
            'remark'   => $request->param('remark','')
        ];

        if($this->adminInfo['aid'] == 1)$param['rid'] = 1;

        $validate = new LoginValidate();
        if(!$validate->scene('update')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new AdminServer())->adminUpdate($param, $this->adminInfo['aid']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'修改失败');
        }
    }
}