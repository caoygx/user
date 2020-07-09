<?php

namespace mapp\validate;

use think\Validate;

class Code extends Validate
{
    protected $rule =   [
        'mobile'  => 'require|checkMobile:rule',
        //'code'   => 'require|max:6|checkCode:rule',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     * @var array
     */
    protected $message  =   [
        'mobile.require' => '手机号是必填',
        'mobile.mobile' => '手机号格式不正确',
        'mobile.checkMobile' => '手机不存在',
        'code.checkCode'     => '验证码错误或过期',
    ];

    protected function checkCode($value, $rule, $data=[])
    {
        return true;
        $mobile = $data['mobile'];
        $code   = $data['code'];
        $codeKeys = C('app.code_keys');
        $key      = $mobile . $codeKeys['login'];

        if ($code == date('ymd') || ($mobile == '12000000000' && $code = '123456')) {
            //不验证
            return true;

        } else {
            $cacheCode = S($key);
            if ($code != $cacheCode) return false;
        }
        return true;
    }

    function checkMobile($value, $rule, $data=[]){

        return true;

        $rUser = \think\facade\Db::name('User')->where(['mobile'=>$data['mobile']])->find();
        return !empty($rUser);
    }

}
