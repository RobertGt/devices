<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/18 23:50
 * Email: 1183@mapgoo.net
 */

namespace app\admin\validate;


use app\admin\model\AdminModel;
use app\admin\server\AdminServer;
use think\Validate;

class LoginValidate extends Validate
{
    protected $rule = [
        'aid'            => 'require',
        'name'           => 'require|max:20',
        'rid'            => 'require|gt:0',
        'account'        => 'require|max:10',
        'password'       => 'require|min:6',
        'newPassword'    => 'require|min:6|checkPassword',
        'repeatPassword' => 'require|min:6',
        'adminid'        => 'require|checkDelete'
    ];

    protected $scene = [
        'login'        =>  ['account', 'password'],
        'reset'        =>  ['account', 'password', 'newPassword', 'repeatPassword'],
        'insert'       =>  ['account' => 'require|max:10|checkAccount', 'password', 'name', 'rid'],
        'update'       =>  ['aid', 'account' => 'require|max:10|checkUpdateAccount', 'password' => 'min:6', 'name', 'rid'],
        'checkId'      =>  ['aid'],
        'checkCount'   =>  ['adminid']
    ];

    protected $message = [
        'rid.require'   =>  '请选择角色',
        'rid.gt'        =>  '角色有误'
    ];

    public function checkPassword($newPassword, $rule, $data)
    {
        $checkPassword = (new AdminServer())->checkPassword($data['account'], $data['password']);

        if(!$checkPassword){
            return "原密码错误";
        }

        return true;
    }

    public function checkAccount($account, $rule, $data)
    {
        $admin = (new AdminModel())->where(['account' => $account])->field("aid")->find();
        if($admin){
            return "账号名已存在";
        }
        return true;
    }

    public function checkUpdateAccount($account, $rule, $data)
    {
        if($data['aid'] == 1){
            return true;
        }
        $admin = (new AdminModel())->where(['account' => $account, 'aid' => ['neq', $data['aid']]])->field("aid")->find();
        if($admin){
            return "账号名已存在";
        }
        return true;
    }

    public function checkDelete($id, $rule, $data)
    {
        $count = (new AdminModel())->where("find_in_set({$id}, parentAll)")->count();
        if($count > 1){
            return "账号存在子账号，请先删除子账号";
        }
        return true;
    }
}