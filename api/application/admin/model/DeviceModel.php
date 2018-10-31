<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/31 22:59
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class DeviceModel extends Model
{
    protected $table = 'sys_devices';
    protected $pk = 'deviceId';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createTime';
    protected $updateTime = 'updateTime';
}