<?php 
 return array (
  'base' => 
  array (
    'id' => 
    array (
      'name' => 'id',
      'type' => 'text',
      'size' => 10,
      'zh' => 'id',
    ),
    'openid' => 
    array (
      'name' => 'openid',
      'type' => 'text',
      'size' => 30,
      'zh' => 'openid',
    ),
    'password' => 
    array (
      'name' => 'password',
      'type' => 'text',
      'size' => 30,
      'zh' => '',
    ),
    'nickname' => 
    array (
      'name' => 'nickname',
      'type' => 'text',
      'size' => 30,
      'zh' => '昵称',
    ),
    'gender' => 
    array (
      'name' => 'gender',
      'type' => 'text',
      'size' => 10,
      'zh' => '',
    ),
    'birthday' => 
    array (
      'name' => 'birthday',
      'type' => 'datePicker',
      'size' => 10,
      'zh' => '生日',
    ),
    'mobile' => 
    array (
      'name' => 'mobile',
      'type' => 'text',
      'size' => 30,
      'zh' => '手机',
    ),
    'avatar' => 
    array (
      'name' => 'avatar',
      'type' => 'img',
      'size' => 30,
      'function' => 'tpl_function=show_img()',
      'zh' => '图像',
    ),
    'ch' => 
    array (
      'name' => 'ch',
      'type' => 'text',
      'size' => 30,
      'zh' => '用户渠道',
    ),
    'deviceid' => 
    array (
      'name' => 'deviceid',
      'type' => 'text',
      'size' => 30,
      'zh' => '设备id',
    ),
    'address' => 
    array (
      'name' => 'address',
      'type' => 'textarea',
      'row' => 10,
      'zh' => '地址',
    ),
    'realname' => 
    array (
      'name' => 'realname',
      'type' => 'text',
      'size' => 30,
      'zh' => '姓名',
    ),
    'balance' => 
    array (
      'name' => 'balance',
      'type' => 'text',
      'size' => 10,
      'zh' => '余额',
    ),
    'create_t' => 
    array (
      'name' => 'create_t',
      'type' => 'text',
      'size' => 10,
      'function' => 'date("y-m-d h:i:s",###)',
      'zh' => '创建时间',
    ),
    'modify_t' => 
    array (
      'name' => 'modify_t',
      'type' => 'text',
      'size' => 10,
      'function' => 'date("y-m-d h:i:s",###)',
      'zh' => '修改时间',
    ),
    'login_time' => 
    array (
      'name' => 'login_time',
      'type' => 'text',
      'size' => 10,
      'function' => 'date("y-m-d h:i:s",###)',
      'zh' => '登录时间',
    ),
    'platform' => 
    array (
      'name' => 'platform',
      'type' => 'select',
      'size' => 10,
      'rawOption' => '1:android,2:iOS',
      'options' => 
      array (
        1 => 'android',
        2 => 'iOS',
      ),
      'zh' => '平台',
      'show_text' => 'platform_text',
    ),
    'ip' => 
    array (
      'name' => 'ip',
      'type' => 'text',
      'size' => 30,
      'zh' => 'ip',
    ),
    'area' => 
    array (
      'name' => 'area',
      'type' => 'text',
      'size' => 30,
      'zh' => '区域',
    ),
    'memberno' => 
    array (
      'name' => 'memberno',
      'type' => 'text',
      'size' => 30,
      'zh' => '会员编号',
    ),
    'status_flag' => 
    array (
      'name' => 'status_flag',
      'type' => 'text',
      'size' => 10,
      'rawOption' => '0:禁用,1:正常',
      'options' => 
      array (
        0 => '禁用',
        1 => '正常',
      ),
      'zh' => '用户状态',
      'show_text' => 'status_flag_text',
    ),
    'update_time' => 
    array (
      'name' => 'update_time',
      'type' => 'time',
      'zh' => '更新时间',
    ),
  ),
  'add' => 
  array (
    'id' => 
    array (
    ),
    'nickname' => 
    array (
    ),
    'realname' => 
    array (
    ),
    'balance' => 
    array (
    ),
  ),
  'edit' => 
  array (
    'id' => 
    array (
    ),
    'nickname' => 
    array (
    ),
    'realname' => 
    array (
    ),
    'balance' => 
    array (
    ),
  ),
  'list' => 
  array (
    'id' => 
    array (
    ),
    'openid' => 
    array (
    ),
    'nickname' => 
    array (
    ),
    'mobile' => 
    array (
    ),
    'avatar' => 
    array (
    ),
    'ch' => 
    array (
    ),
    'deviceid' => 
    array (
    ),
    'address' => 
    array (
    ),
    'realname' => 
    array (
    ),
    'balance' => 
    array (
    ),
    'modify_t' => 
    array (
    ),
    'platform' => 
    array (
    ),
    'ip' => 
    array (
    ),
    'area' => 
    array (
    ),
    'memberno' => 
    array (
    ),
    'status_flag' => 
    array (
    ),
  ),
  'search' => 
  array (
    'id' => 
    array (
    ),
    'openid' => 
    array (
    ),
    'nickname' => 
    array (
    ),
    'mobile' => 
    array (
    ),
    'deviceid' => 
    array (
    ),
    'realname' => 
    array (
    ),
    'platform' => 
    array (
    ),
    'ip' => 
    array (
    ),
    'memberno' => 
    array (
    ),
  ),
  'tableInfo' => 
  array (
    'action' => 'edit:编辑:id,view_recharge:查看充值记录:openid',
    'property' => '',
    'title' => '用户',
    'name' => 'user',
  ),
);