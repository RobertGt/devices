<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/28 19:29
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\RoleServer;
use app\admin\validate\RoleValidate;
use think\Request;

class Role extends Base
{
    public function roleList(Request $request)
    {
        $param = [
            'seach'    => $request->param('seach/a',[]),
            'pageNum'  => $request->param('pageNum',1,'intval'),
            'pageSize' => $request->param('pageSize',10,'intval'),
        ];

        $response = (new RoleServer())->roleList($param, $this->adminInfo['aid']);

        ajax_info(0,'success', $response);
    }

    public function roleInsert(Request $request)
    {
        $param = [
            'name'     => $request->param('name',''),
            'remark'   => $request->param('remark','')
        ];

        $validate = new RoleValidate();
        if(!$validate->scene('insert')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new RoleServer())->roleInsert($param, $this->adminInfo['aid']);

        if($response){
            ajax_info(0,'Success');
        }else{
            ajax_info(1,'添加失败');
        }
    }

    public function roleInfo(Request $request)
    {
        $param = [
            'rid'       => authcode($request->param('rid','')),
        ];

        $validate = new RoleValidate();
        if(!$validate->scene('checkId')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new RoleServer())->roleInfo($param['rid']);
        if($response){
            ajax_info(0,'success', $response, false);
        }else{
            ajax_info(1,'获取详情失败');
        }
    }

    public function roleUpdate(Request $request)
    {
        $param = [
            'rid'       => authcode($request->param('id','')),
            'name'     => $request->param('name',''),
            'remark'   => $request->param('remark','')
        ];

        $validate = new RoleValidate();
        if(!$validate->scene('update')->check($param)){
            ajax_info(1 , $validate->getError());
        }

        $response = (new RoleServer())->roleUpdate($param, $this->adminInfo['aid']);

        if($response){
            ajax_info(0,'Success');
        }else{
            ajax_info(1,'修改失败');
        }
    }
}