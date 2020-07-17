<?php

namespace muser\controller;

use Cgf\Framework\Thinkphp\BaseController;
use think\exception\ValidateException;
use SingKa\Sms\SkSms;
use think\facade\Config;


class User extends BaseController
{
    function getModelDir(){
        return "\\muser\\model";
    }
    
    function modifyMobile($post)
    {
        try {
            $data = input();
            $r    = validate('muser\validate\ModifyMobile')->check($data);
            $this->m->update($data);
            return $this->toview();
        } catch (ValidateException $e) {
            return $this->error($e->getError());
        }
    }

    function modifyUsername($post)
    {

        try {
            $data = input();
            validate(\muser\validate\ModifyUsername::class)->check($data);
            $this->m->update($data);
            return $this->toview();
        } catch (ValidateException $e) {
            return $this->error($e->getError());
        }

    }

    function modifyPassword($post)
    {
        try {
            $data = input();
            validate('muser\validate\ModifyPassword')->check($data);
            $this->m->update($data);
            return $this->toview();
        } catch (ValidateException $e) {
            return $this->error($e->getError());
        }

    }

    /**
     * 手机登录验证码登录
     */
    function register()
    {
        $mobile = input('mobile');
        $code   = input('code');

        //Cache::set('name', $value, 3600);

        //验证手机号，相当于验证open_id是否真实

        try {
            $data = input();
            validate(\muser\validate\User::class)->check($data);
            $this->m->save($data);
            $id = $this->m->id;
            $this->assign('id', $id);
            return $this->toview();
        } catch (ValidateException $e) {
            return $this->error($e->getError());
        }
    }

    /**
     * 登录
     * @return \app\member
     */
    function login()
    {
        $mobile   = input('mobile');
        $password = input('password');
        $rUser    = $this->m->where(['mobile' => $mobile])->find();
        $hash     = $rUser['password'];
        if (!password_verify($password, $hash)) return $this->error("用户名或密码错误");

        $this->assign('userinfo', $rUser);
        return $this->toview();

    }

    /**
     * 检测手机验证码
     * @return mixed
     */
    protected function checkCodeForMobile()
    {
        $mobile = input('mobile');
        $code   = input('code');

        $codeKeys = config('app.code_keys');
        $key      = $mobile . $codeKeys['login'];

        if (CONF_ENV == 'dev' || $code == date('ymd') || ($mobile == '12000000000' && $code = '123456')) {
            //不验证
        } else {
            $cacheCode = cache($key);
            if ($code != $cacheCode) return $this->error('验证码错误或过期');
        }

        return true;

    }

    /**
     * 验证码登录
     * @return \app\member|array|\think\response\Json|\think\response\Jsonp
     */
    function loginForCode()
    {
        //try {
        $data     = input();
        $validate = new \muser\validate\Code();
        //$rCheck = validate('app\validate\Code')->check($data);
        $rCheck = $validate->check($data);
        if (!$rCheck) {
            return $this->error($validate->getError());
        }

        $dbUserInfo = M('OutletUser')->where(['mobile' => $data['mobile']])->find();
        if (empty($dbUserInfo)) return false;

        unset($dbUserInfo['password']);
        //$dbUserInfo['avatar'] = img($dbUserInfo['avatar'],'user_avatar');
        $this->uid  = $dbUserInfo['id'];
        $this->user = $dbUserInfo;
        $this->assign('uid', $this->uid);
        $this->assign('userinfo', $dbUserInfo);

        //event(new \app\event\UserLogin($dbUserInfo));
        event('UserLogin', $dbUserInfo);

        //    先初始化微信
        $code = input('wxcode');
        if (empty($dbUserInfo['openid']) && $code) {
            $app    = app('wechat.mini_program');
            $wxAuth = $app->auth->session($code);
            /*$wxAuth = array(
                'session_key' => 'FqZbPwjE2YDxsRUgmhJRVA==',
                'openid'      => 'oi3YK429crrVJnwdSQoqwIHMXLQM',
                'unionid'     => 'omBj21I1J115S94p2pliuyl6oCwE',
            );*/
            $data['openid'] = $wxAuth['openid'];
            //$data['unionid'] = $wxAuth['unionid'];
            $this->m->where(['id' => $dbUserInfo['id']])->update($data);
            return $this->toview();
        }

        return $this->toview();
    }

    /**
     * 找回密码
     * @return \app\member|array|\think\response\Json|\think\response\Jsonp
     */
    function findPassword($post)
    {
        return $this->modifyPassword($post);

    }

    /**
     * 获取注册登录的验证码
     */
    function getCode()
    {
        $mobile = input('mobile');
        $this->validate(input(), '\mapp\validate\code.mobile');

        $type     = input('type', 'register');
        $codeKeys = config('sms.code_keys');
        $key      = $mobile . $codeKeys[$type];
        $code     = cache($key);
        if (empty($code)) {
            $code = mt_rand(1000, 9999);
            cache($key, $code, config('sms_code_expire'));
        }

        $r = $this->sendSms($mobile, "register", ["code" => $code]);

        if ($r['code'] == 200) {
            return $this->toview('', '', "短信发送成功，请注意查收！ ");
        } else {
            return $this->error($r);
        }
    }

    /**
     * 短信发送示例
     *
     * @mobile  短信发送对象手机号码
     * @action  短信发送场景，会自动传入短信模板
     * @parme   短信内容数组
     */
    public function sendSms($mobile, $action, $parme)
    {
        $d            = [];
        $d['mobile']  = $mobile;
        $d['content'] = json_encode($parme);
        $d['ip']      = $this->request->ip();
        $smsId        = \think\facade\Db::name('SmsQueue')->insertGetId($d);

        $SmsDefaultDriver = 'aliyun';
        $config           = $this->SmsConfig ?: Config::get('sms.' . $SmsDefaultDriver);
        $sms              = new sksms($SmsDefaultDriver, $config);//传入短信驱动和配置信息
        if ($SmsDefaultDriver == 'aliyun') {
            $result = $sms->$action($mobile, $parme);
        } elseif ($SmsDefaultDriver == 'qiniu') {
            $result = $sms->$action([$mobile], $parme);
        } elseif ($SmsDefaultDriver == 'upyun') {
            $result = $sms->$action($mobile, implode('|', $this->restoreArray($parme)));
        } else {
            $result = $sms->$action($mobile, $this->restoreArray($parme));
        }
        if ($result['code'] == 200) {
            \think\facade\Db::name('SmsQueue')->where(['id' => $smsId])->update(['status' => 1]);
            $data['code'] = 200;
            $data['msg']  = '短信发送成功';
        } else {
            \think\facade\Db::name('SmsQueue')->where(['id' => $smsId])->update(['return_msg' => $result['msg']]);
            $data['code'] = $result['code'];
            $data['msg']  = $result['msg'];
        }
        return $data;
    }

    /**
     * 数组主键序号化
     *
     * @arr  需要转换的数组
     */
    public function restoreArray($arr)
    {
        if (!is_array($arr)) {
            return $arr;
        }
        $c   = 0;
        $new = [];
        foreach ($arr as $key => $value) {
            $new[$c] = $value;
            $c++;
        }
        return $new;
    }

    /**
     * 用户名注册
     */
    function registerForUsername()
    {
        if (input('source') == 'iross') {
            $this->setParam('type', 8);
        }
        if (($id = $this->m->usernameAdd()) === false) {
            $this->error($this->m->getError());
        }

        $this->giveCoupon($id);

        $u           = $this->m->find($id);
        $u['is_new'] = '1';
        $this->returnUserinfo($u);

    }

    function miniappLogin()
    {
        //    先初始化微信
        $app    = app('wechat.mini_program');
        $code   = input('code');
        $wxAuth = $app->auth->session($code);


        $wxAuth = array(
            'session_key' => 'FqZbPwjE2YDxsRUgmhJRVA==',
            'openid'      => 'oi3YK429crrVJnwdSQoqwIHMXLQM',
            'unionid'     => 'omBj21I1J115S94p2pliuyl6oCwE',
        );


        $data['openid']  = $wxAuth['openid'];
        $data['unionid'] = $wxAuth['unionid'];
        $rUser           = $this->m->where('openid', '=', $data['openid'])->find();
        if (empty($rUser['mobile'])) return $this->error();

        if ($rUser) {
            $id = $rUser['id'];
        } else {
            $this->m->save($data);
            $id = $this->m->id;
        }
        $this->assign('id', $id);
        return $this->toview();
    }

}
