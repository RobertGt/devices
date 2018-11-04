<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

Route::post([

    'admin/login'          => 'admin/Index/login',
    'admin/reset'          => 'admin/Index/reset',

    'admin/adminInsert'      => 'admin/Admin/adminInsert',
    'admin/adminUpdate'      => 'admin/Admin/adminUpdate',
    'admin/adminPermission'  => 'admin/Admin/adminPermission',

    'admin/roleInsert'      => 'admin/Role/roleInsert',
    'admin/roleUpdate'      => 'admin/Role/roleUpdate',

    'admin/deviceInsert'    => 'admin/Device/deviceInsert',
    'admin/deviceUpdate'    => 'admin/Device/deviceUpdate',

]);

Route::get([

    'admin/adminList'      => 'admin/Admin/adminList',
    'admin/adminDelete'    => 'admin/Admin/adminDelete',
    'admin/adminInfo'      => 'admin/Admin/adminInfo',
    'admin/adminRole'      => 'admin/Admin/adminRole',
    'admin/permission'     => 'admin/Admin/permission',


    'admin/roleList'      => 'admin/Role/roleList',
    'admin/roleDelete'    => 'admin/Role/roleDelete',
    'admin/roleInfo'      => 'admin/Role/roleInfo',

    'admin/deviceList'    => 'admin/Device/deviceList',
    'admin/deviceDelete'  => 'admin/Device/deviceDelete',
    'admin/deviceInfo'    => 'admin/Device/deviceInfo',
    'admin/deviceType'    => 'admin/Device/deviceType',
]);
