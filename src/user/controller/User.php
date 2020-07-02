<?php
namespace mapp\user\controller;


use think\facade\Request;
use think\facade\Db;

class User extends BaseController
{
    function register(){
        if(request()->isPost()){
            $mUser = new \mapp\model\User();
            $r = $mUser->msave($_POST);
            echo '保存成功';
            redirect('/');
        }else{
            $dir = dirname(__DIR__).'/view';
            $dir = "../../vendor/rrbrr/user/src/user/view";
            $dir = config("user.template_root_dir");
            return view($dir.'/user/register');
        }
    }
}
