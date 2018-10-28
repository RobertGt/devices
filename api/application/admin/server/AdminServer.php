<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/18 23:38
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\AdminModel;
use app\admin\model\RoleModel;
use think\db\Expression;
use think\Exception;
use think\Log;

class AdminServer
{
    public function adminInfo($id = 0, $token = '')
    {
        $where = [];
        if($id){
            $where['aid'] = $id;
        }else{
            $where['token'] = $token;
        }

        $adminInfo = (new AdminModel())->field('aid, account, name, rid, password, createTime, remark')->where($where)->find();

        if($adminInfo){
            $adminInfo = $adminInfo->getData();
            $adminInfo['admin'] = $adminInfo['aid'] == 1 ? 1 : 0;
            $adminInfo['aid'] = $id ? authcode($adminInfo['aid'], 'ENCODE') : $adminInfo['aid'];
            $adminInfo['createTime'] = date('Y-m-d H:i:s', $adminInfo['createTime']);
        }else{
            $adminInfo = [];
        }
        return $adminInfo;
    }

    public function adminList($param = [], $aid = 0)
    {
        $where = [];
        if(!empty($param['seach']['account'])){
            $where['a.account'] = ['like', '%' . $param['seach']['account'] . '%'];
        }
        $where[] = ['exp', new Expression("find_in_set({$aid}, a.parentAll)")];
        $adminModel = new AdminModel();
        $total = $adminModel->alias('a')->where($where)->count();
        $list = $adminModel->alias('a')->where($where)->field('a.aid id, a.name, a.account, a.remark, a1.name parentAdmin, r.roleName, a.createTime, a.loginTime, a.loginIp, a.loginCount, a.remark')
                ->join('sys_admin a1', 'a1.aid = a.parentAdmin', 'LEFT')
                ->join('sys_role r', 'r.rid = a.rid', 'LEFT')
                ->page($param['pageNum'], $param['pageSize'])
                ->order("createTime desc")
                ->select();
        $response['row'] = [];
        $rowNum = ($param['pageNum'] - 1) * $param['pageSize'];
        $i = 1;
        foreach ($list as $value){
            $info = $value->getData();
            $info['id'] = authcode($info['id'], 'ENCODE');
            $info['createTime'] = date('Y-m-d H:i:s', $info['createTime']);
            $info['loginTime'] = $info['loginTime'] ? date('Y-m-d H:i:s', $info['loginTime']) : '';
            $info['remark'] = $info['remark'] ? $info['remark']  : '';
            $info['loginIp'] = $info['loginIp'] ? $info['loginIp']  : '';
            $info['parentAdmin'] = $info['parentAdmin'] ? $info['parentAdmin']  : '';
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function login($param = [])
    {
        $checkPassword = $this->checkPassword($param['account'], $param['password']);
        if(!$checkPassword){
            return false;
        }

        $save['loginTime'] = time();
        $save['loginIp'] = getClientIp();
        $save['loginCount'] = ['inc', 1];
        $save['token'] = md5($save['loginTime'] . $param['account'] . rand(100, 999));
        try{
            (new AdminModel())->save($save, ['aid' => $checkPassword['aid']]);
        }catch (Exception $e){
            Log::error("login error:" . $e->getMessage());
            return false;
        }
        $response['token'] = $save['token'];
        $response['account'] = $checkPassword['account'];
        return $response;
    }

    public function reset($param = [])
    {
        $save['salt'] = getRandStr();
        $save['password'] = md5($param['repeatPassword'] . $save['salt']);

        $where['account'] = $param['account'];
        try{
            (new AdminModel())->save($save, $where);
        }catch (Exception $e){
            Log::error("reset error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function checkPassword($account = '', $password = '')
    {
        if(!$account || !$password)return false;
        $where['account'] = $account;
        $adminInfo = (new AdminModel())->field('aid, account, password, salt')->where($where)->find();

        if(!$adminInfo){
            return false;
        }
        $adminInfo = $adminInfo->getData();
        if($adminInfo['password'] == md5($password . $adminInfo['salt'])){
            return $adminInfo;
        }else{
            return false;
        }
    }

    public function adminDelete($aid = 0)
    {
        if($aid == 1)return false;
        try{
            $where['aid'] = $aid;
            (new AdminModel())->where($where)->delete();
        }catch (Exception $e){
            Log::error("adminDelete error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function adminInsert($param = [], $aid = 0)
    {
        $adminModel = new AdminModel();
        $parent = $adminModel->where(['aid' => $aid])->field('parentAll')->find();
        $parent = $parent->getData();
        $create['account'] = $param['account'];
        $create['rid'] = $param['rid'];
        $create['name'] = $param['name'];
        $create['parentAdmin'] = $aid;
        $create['salt'] = getRandStr();
        $create['password'] = md5($param['password'] . $create['salt']);
        $create['remark'] = $param['remark'];
        $adminModel->startTrans();
        try{
            $admin = $adminModel->create($create);
            $update['parentAll'] = $parent['parentAll'] . ',' . $admin->aid;
            $adminModel->save($update, ['aid' => $admin->aid]);
            $adminModel->commit();
        }catch (Exception $e){
            $adminModel->rollback();
            Log::error("adminInsert error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function adminUpdate($param = [], $aid = 0)
    {
        if($param['aid'] != 1 || $aid != $param['aid']){
            $save['name'] = $param['name'];
            $save['rid'] = $param['rid'];
            $save['account'] = $param['account'];
        }
        if($param['password']){
            $save['salt'] = getRandStr();
            $save['password'] = md5($param['password'] . $save['salt']);
        }
        $save['remark'] = $param['remark'];
        try{
            (new AdminModel())->save($save, ['aid' => $param['aid']]);
        }catch (Exception $e){
            Log::error("adminUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function adminRole($id, $aid = 0)
    {
        $rid = 0;
        if($aid){
            $adminInfo = (new AdminModel())->field('rid')->where(['aid' => $aid])->find();
            $rid = $adminInfo->getData('rid');
        }
        $roleList = (new RoleModel())->where(['aid' => $id])->field('rid, roleName')->order('createTime desc')->select();
        $row = [];
        foreach ($roleList as $v){
            $info = $v->getData();
            $info['select'] = $rid == $info['rid'] ? 1 : 0;
            $info['rid'] = authcode($info['rid'], 'ENCODE');
            $row[] = $info;
        }
        return $row;
    }
}