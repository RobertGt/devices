<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/21 0:57
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\AdminServer;
use app\admin\validate\LoginValidate;
use think\Request;

class Admin extends Base
{
    public function adminList(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
        ];

        $response = (new AdminServer())->adminList($param, $this->adminInfo['aid']);

        ajax_info(0,'success', $response);
    }

    public function adminDelete(Request $request)
    {
        $param = [
            'adminid'  => authcode($request->param('id', ''))
        ];

        if($param['adminid'] == 1){
            ajax_info(1 , "无法删除系统管理员");
        }

        if($param['adminid'] == $this->adminInfo['aid']){
            ajax_info(1 , "无法删除自己");
        }
        $validate = new LoginValidate();
        if(!$validate->scene('checkCount')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new AdminServer())->adminDelete($param['adminid']);

        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(1,'操作失败');
        }
    }

    public function adminInsert(Request $request)
    {
        $param = [
            'account'  => $request->param('username',''),
            'password' => $request->param('newPassword',''),
            'name'     => $request->param('name',''),
            'rid'      => authcode($request->param('rid', '')),
            'remark'   => $request->param('remark','')
        ];

        $validate = new LoginValidate();
        if(!$validate->scene('insert')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new AdminServer())->adminInsert($param, $this->adminInfo['aid']);

        if($response){
            ajax_info(0,'Success');
        }else{
            ajax_info(1,'添加失败');
        }
    }

    public function adminInfo(Request $request)
    {
        $param = [
            'aid'       => authcode($request->param('aid','')),
        ];

        $validate = new LoginValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new AdminServer())->adminInfo($param['aid']);
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

    public function adminRole(Request $request)
    {
        $param = [
            'aid' => authcode($request->param('aid',  ''))
        ];
        $response = (new AdminServer())->adminRole($this->adminInfo['aid'], $param['aid']);
        ajax_info(0,'success', $response);
    }

    public function permission(Request $request)
    {
        $param = [
            'menu' => $request->param('menu', 0) ? 1 : 0
        ];
        $response = (new AdminServer())->permission($this->adminInfo['rid'], $param['menu']);
        ajax_info(0,'success', $response);
    }

    public function adminPermission(Request $request)
    {
        $param = [
            'rid' => authcode($request->param('rid', '')),
            'fid' => $request->param('fid', ''),
        ];
        $response = (new AdminServer())->adminPermission($param);
        if($response){
            ajax_info(0,'success');
        }else{
            ajax_info(0,'error');
        }
    }
}