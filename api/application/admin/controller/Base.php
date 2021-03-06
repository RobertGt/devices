<?php
/**
 * Created by PhpStorm.
 * Author: Robert
 * Date: 2018/9/12 23:27
 * Email: 1183@mapgoo.net
 */

namespace app\admin\controller;


use app\admin\server\AdminServer;
use think\Request;

class Base
{
    protected $adminInfo = [];

    public function __construct()
    {
        $this->checkToken();
    }

    /**
     * 检查Token
     */
    private function checkToken()
    {
        $request = Request::instance();

        $token     = $request->header('token', '');
        $adminServer = new AdminServer();
        $admin = $adminServer->adminInfo(0, $token);

        if (!$admin){
            ajax_info(401, 'failure of authentication');
        }

        if($admin['aid'] == 1 || $admin['rid'] == 1){
            $this->adminInfo = $admin;
            return true;
        }
        $menu = $request->controller() . '/' . $request->action();

        $auth = $adminServer->userPermission($admin['rid'], $menu);
        if(empty($auth)){
            ajax_info(403 , 'forbidden');
        }
        $this->adminInfo = $admin;

        return true;
    }
}