<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/11/1 0:05
 * Email: 1183@mapgoo.net
 */

namespace app\admin\model;


use think\Model;

class DeviceTypeModel extends Model
{
    protected $table = 'sys_devices_type';
    protected $pk = 'deviceTypeId';
}