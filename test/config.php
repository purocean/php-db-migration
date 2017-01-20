<?php

return [
    'tablePrefix' => 'test_prefix_', // 表前缀
    'migrationsPath' => __DIR__.'/migrations', // 迁移储存位置
    'dbConfig' => [ // 数据库配置
        'username' => 'root',
        'password' => '',
        'dsn' => 'mysql:host=127.0.0.1;port=3306;dbname=db_migration_test;charset=utf8',
    ]
];
