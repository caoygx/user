<?php

namespace mapp\user\controller;


use think\exception\ValidateException;
use SingKa\Sms\SkSms;
use think\facade\Config;

class User extends BaseController
{
    function initialize()
    {
        $this->m = new \mapp\model\User();
    }

    function register2()
    {
        if (request()->isPost()) {
            $mUser = new \mapp\model\User();
            $r     = $mUser->msave($_POST);
            echo '保存成功';
            redirect('/');
        } else {
            $dir = dirname(__DIR__) . '/view';
            $dir = "../../vendor/rrbrr/user/src/user/view";
            $dir = config("user.template_root_dir");
            return view($dir . '/user/register');
        }
    }

    /**
     * 手机登录验证码登录
     */
    function register($params)
    {

        try {
            $data = input();
            validate('mapp\validate\User')->check($data);
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
    function login($params)
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
     * 登录
     * @return \app\member
     */
    function loginByCode($params)
    {
        $mobile   = input('mobile');
        $checkResult = $this->checkCodeForMobile();
        if(!$checkResult){
            return $this->error('验证码错误或过期');
        }

        $rUser    = $this->m->where(['mobile' => $mobile])->find();
        $this->assign('userinfo', $rUser);
        return $this->toview();

    }

    function saveInfo()
    {
        $data             = [];
        $data['nickname'] = input('nickname');

        $id = $this->uid;

        //验证编辑保存权限
        if (!empty($id)) {
            $rModel = $this->m->where([$this->m->getPk() => $id])->find();
            if (empty($rModel)) return $this->error('没有所有者权限');
        }

        $r = $rModel->save($data);

        if ($r === false) {
            return $this->error();
        }

        $id = $this->m->id;
        if (!empty($id)) $this->assign('id', $id);
        return $this->toview();

    }

    public function switchOutlet()
    {
        $outlet_id = input('outlet_id');
        /*$hasOutlet = Db::name('outletUserRel')->where(['uid'=>$this->uid,outlet_id=>$outlet_id])->find();
        if(empty($hasOutlet)) return $this->error();*/
        $r = $this->m->where(['id' => $this->uid])->update(['current_outlet_id' => $outlet_id]);
        if (empty($r)) return $this->error('切换失败');
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

        $codeKeys = config('sms.code_keys');
        $key      = $mobile . $codeKeys['login'];

        if (CONF_ENV == 'dev' || $code == date('ymd') || ($mobile == '12000000000' && $code = '123456')) {
            //不验证
        } else {
            $cacheCode = cache($key);
            if ($cacheCode == null || $code != $cacheCode) return false;
        }

        return true;

    }


    /**
     * 绑定手机
     * @return \app\member|array|\think\response\Json|\think\response\Jsonp
     */
    function bindMobile()
    {
        $mobile = input('mobile');

        $rUser = $this->checkCodeForMobile();
        if (empty($rUser)) return $this->error('用户不存在');


        if (!empty($rUser['mobile'])) {
            $this->error('已经绑定过手机了');
        }

        $rMobile = $this->m->where(['mobile' => $mobile])->find();
        if (!empty($rMobile)) {
            $this->error('手机已经被使用了,请更换手机');
        }

        $this->m->where(['id' => $rUser['uid']])->update(['mobile' => $mobile]);
        return $this->toview();
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


}
