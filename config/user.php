<?php
return [
    /*
     *Default Tauthz enforcer
     */
    'default' => 'basic',

    'log' => [
        'enabled' => false,
        //'logger' => rrbrr\Log::class,
    ],

    'enforcers' => [
        'basic' => [
            /*
            * Model 设置
            */
            'model' => [
                'config_type' => 'file',
                'config_text' => '',
            ],

            // 适配器 .
            'adapter' => user\adapter\DatabaseAdapter::class,

            /*
            * 数据库设置.
            */
            'database' => [
                // 数据库连接名称，不填为默认配置.
                'connection' => '',
                // 策略表名（不含表前缀）
                'rules_name' => 'rules',
                // 策略表完整名称.
                'rules_table' => null,
            ],
        ],
    ],
];