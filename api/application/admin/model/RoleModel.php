<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/28 19:35
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class RoleModel extends Model
{
    protected $table = 'sys_role';
    protected $pk = 'rid';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}