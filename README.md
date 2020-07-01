composer user模块 
以composer形式加载应用模块,这样可以把常用的公共模块放入composer中，如用户模块，订单模块

 此模块包含收下功能：
1. 前台注册登录
2. 用户中心信息修改
3. 后台用户管理



使用`composer`安装：

```
composer require rrbrr/user
```

注册服务，在应用的全局公共文件`service.php`中加入：

```php
return [
    // ...

    mapp\UserService::class,
];
```

发布配置文件和数据库迁移文件：

```
php think user:publish
```

这将自动在app目录下生成 `config/user.php` 文件。


执行迁移工具（**确保数据库配置信息正确**）：

```
php think migrate:run
```

这将创建名为 `user` 的表。

用户表名更改在config/user目录下