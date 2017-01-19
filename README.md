# 数据库迁移工具，仿照Yii2
    开发中……

## 使用
```php
// migration.php
$migrationConfig = [
    'tablePrefix' => 'test_prefix_', // 表前缀
    'migrationsPath' => __DIR__.'/migrations', // 迁移储存位置
    'dbConfig' => [ // 数据库配置
        'username' => 'root',
        'password' => 'yang',
        'dsn' => 'mysql:host=localhost;port=3306;dbname=db_migration_test;charset=utf8',
    ]
];

(new \DbMigration\Cli($migrationConfig))->run();

```

```bash
php migration.php create create_user_table
```

## 功能
- [x] create   创建迁移
- [ ] up       升级一个迁移
- [ ] down     降级一个迁移
- [ ] mark     讲某个迁移标记为已升级/未升级
- [ ] redo     重做最近迁移
- [x] new      显示未升级迁移
- [x] history  查看迁移历史

## 测试
```bash
cd test
php test.php
```
