<?php

namespace mapp\model;

use think\Model;
use think\contract\Arrayable;

/**
 * Rule Model
 */
class User extends Model implements Arrayable
{

    public function setPasswordAttr($value)
    {
        $pwd = password_hash($value, PASSWORD_DEFAULT);
        return $pwd;
    }

    function msave($data = ''){
        //$this->_auto[] = array('password','pwd',3,'callback'); //是编辑用户其它字段也更新密码，还是只更新密码字段才触发生成密码？
        //if(false === $this->create($data))  return false;
        if(!empty($data[$this->getPk()])){
            return $this->save();
        }else{
            return $this->insert($data);
        }
    }

    protected function autoSalt(){
        return substr(uniqid(mt_rand()), 0, 4);
    }
}