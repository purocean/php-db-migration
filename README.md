# 数据库迁移工具，仿照Yii2
    仅支持 MySQL

## 使用
```php
// migration.php
date_default_timezone_set('PRC');
$migrationConfig = [
    'tablePrefix' => 'test_prefix_', // 表前缀
    'migrationsPath' => __DIR__.'/migrations', // 迁移储存位置
    'dbConfig' => [ // 数据库配置
        'username' => 'root',
        'password' => '',
        'dsn' => 'mysql:host=localhost;port=3306;dbname=db_migration_test;charset=utf8',
    ]
];

(new \DbMigration\Cli($migrationConfig))->run();

```

```bash
php migration.php help
```
迁移示例：/test/migrations/m170119_101310_test_migration.php

## 功能
- [x] create   创建迁移
- [x] up       升级一个迁移
- [x] down     降级一个迁移
- [x] mark     讲某个迁移标记为已升级/未升级
- [x] new      显示未升级迁移
- [x] history  查看迁移历史

## 测试
```bash
cd test
php test.php
```

## MySQL
- [x] execute
- [x] createTable
- [x] renameTable
- [x] dropTable
- [x] truncateTable
- [x] addColumn
- [x] dropColumn
- [x] renameColumn
- [x] alterColumn
- [x] addPrimaryKey
- [x] dropPrimaryKey
- [x] addForeignKey
- [x] dropForeignKey
- [x] createIndex
- [x] dropIndex
- [x] addCommentOnColumn()
- [x] addCommentOnTable
- [x] dropCommentFromColumn
- [x] dropCommentFromTable
- [x] primaryKey
- [x] char
- [x] varchar
- [x] string
- [x] text
- [x] smallInteger
- [x] integer
- [x] bigInteger
- [x] float
- [x] double
- [x] decimal
- [x] dateTime
- [x] timestamp
- [x] time
- [x] date
- [x] binary
- [x] boolean
- [x] notNull
- [x] null
- [x] unique
- [x] defaultValue
- [x] comment
- [x] unsigned
- [x] after
- [x] first
- [x] append
