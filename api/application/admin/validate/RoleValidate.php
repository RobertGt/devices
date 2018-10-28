<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/10/28 23:15
 * Email: 1183@mapgoo.net
 */

namespace app\admin\validate;


use think\Validate;

class RoleValidate extends Validate
{
    protected $rule = [
        'rid'            => 'require',
        'name'           => 'require|max:20'
    ];

    protected $scene = [
        'insert'       =>  ['name'],
        'update'       =>  ['rid', 'name'],
        'checkId'      =>  ['rid']
    ];

    protected $message = [

    ];
}