<?php
namespace mapp\admin\controller;



use think\facade\Request;
//use think\Request;
use think\facade\Db;


class User
{

    function index(){

        if(request()->isPost()){
            $mUser = new \mapp\model\User();
            $r = $mUser->msave($_POST);
            //echo '保存成功';
            //redirect('/');
            echo '{"code":1,"msg":"设置更新成功","data":"","url":"http:\/\/www.yzncms.com\/admin\/config\/setting\/menuid\/13.html","wait":3}';
        }else{
            $viewRoot = "../../";
            $dir = $viewRoot."vendor/rrbrr/user/src/admin/view";
            return view($dir.'/user/index');
        }
    }

}
