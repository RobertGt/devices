<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/28 22:50
 * Email: 1183@mapgoo.net
 */

namespace app\admin\server;


use app\admin\model\AdminModel;
use app\admin\model\RoleModel;
use think\Exception;
use think\Log;

class RoleServer
{
    public function roleList($param = [], $aid = 0)
    {
        $where = [];
        if(!empty($param['seach']['roleName'])){
            $where['r.roleName'] = ['like', '%' . $param['seach']['roleName'] . '%'];
        }
        $parent = (new AdminModel())->where("find_in_set({$aid}, parentAll)")->field('aid')->select();
        $admin = [];
        foreach ($parent as $v){
            $admin[] = $v->getData('aid');
        }
        $where['r.aid'] = ['in', $admin];
        $roleModel = new RoleModel();
        $total = $roleModel->alias('r')->where($where)->count();
        $list = $roleModel->alias('r')->where($where)->field('r.rid id, a.name parent, r.roleName, r.createTime, r.updateTime,r.remark')
            ->join('sys_admin a', 'r.aid = a.aid', 'LEFT')
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
            $info['updateTime'] = date('Y-m-d H:i:s', $info['updateTime']);
            $info['parent'] = $info['parent'] ? $info['parent']  : '';
            $info['remark'] = $info['remark'] ? $info['remark']  : '';
            $info['row'] = $i + $rowNum;
            $response['row'][] = $info;
            $i++;
        }
        $response['total'] = (int)$total;
        return $response;
    }

    public function roleInsert($param = [], $aid = 0)
    {
        $create['roleName'] = $param['name'];
        $create['aid'] = $aid;
        $create['remark'] = $param['remark'];
        try{
            $admin = (new RoleModel())->create($create);
        }catch (Exception $e){
            Log::error("roleInsert error:" . $e->getMessage());
            return false;
        }
        return true;
    }

    public function roleInfo($rid = 0)
    {
        $where['rid'] = $rid;

        $roleInfo = (new RoleModel())->field('rid, roleName, remark')->where($where)->find();

        if($roleInfo){
            $roleInfo = $roleInfo->getData();
            $roleInfo['role'] = $roleInfo['rid'] == 1 ? 1 : 0;
            $roleInfo['rid'] = authcode($roleInfo['rid'], 'ENCODE');
            $roleInfo['remark'] = $roleInfo['remark'] ? $roleInfo['remark'] : "";
        }else{
            $roleInfo = [];
        }
        return $roleInfo;
    }

    public function roleUpdate($param)
    {
        if($param['rid'] != 1){
            $save['roleName'] = $param['name'];
        }
        $save['remark'] = $param['remark'];
        try{
            (new RoleModel())->save($save, ['rid' => $param['rid']]);
        }catch (Exception $e){
            Log::error("roleUpdate error:" . $e->getMessage());
            return false;
        }
        return true;
    }

}