<?php
namespace mapp\user\controller;



use think\facade\Request;
//use think\Request;
use think\facade\Db;


class User
{

    function register(){
        //Request->isPost();
        if(request()->isPost()){
            /*$data = [];
            $data['username'] = input('username');
            $data['password'] = input('password');
            Db::name('user')->insert($data);*/
            //echo 'post';
            //$mUser = new \user\model\User();
            $mUser = new \mapp\model\User();
            $r = $mUser->msave($_POST);
            echo '保存成功';
            redirect('/');
            // 设置session标记完成
            //session('complete', true);
            // 跳回之前的来源地址
            //return redirect()->restore();

        }else{
            //echo 'sb';

            $dir = dirname(__DIR__).'/view';
            $dir = "../../../vendor/rrbrr/user/src/user/view";
            return view($dir.'/user/register');
        }

    }

}
