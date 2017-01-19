# 数据库迁移工具，仿照Yii2
    开发中……

## 使用
```php
// migration.php
date_default_timezone_set('PRC');
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
php migration.php help
```

## 功能
- [x] create   创建迁移
- [ ] up       升级一个迁移
- [ ] down     降级一个迁移
- [x] mark     讲某个迁移标记为已升级/未升级
- [ ] redo     重做最近迁移
- [x] new      显示未升级迁移
- [x] history  查看迁移历史

## 测试
```bash
cd test
php test.php
```

## Mysql
- [x] execute
- [ ] createTable
- [ ] renameTable
- [ ] dropTable
- [ ] truncateTable
- [ ] addColumn
- [ ] dropColumn
- [ ] renameColumn
- [ ] alterColumn
- [ ] addPrimaryKey
- [ ] dropPrimaryKey
- [ ] addForeignKey
- [ ] dropForeignKey
- [ ] createIndex
- [ ] dropIndex
- [ ] addCommentOnColumn
- [ ] addCommentOnTable
- [ ] dropCommentFromColumn
- [ ] dropCommentFromTable
- [ ] primaryKey
- [ ] bigPrimaryKey
- [ ] char
- [ ] string
- [ ] text
- [ ] smallInteger
- [ ] integer
- [ ] bigInteger
- [ ] float
- [ ] double
- [ ] decimal
- [ ] dateTime
- [ ] timestamp
- [ ] time
- [ ] date
- [ ] binary
- [ ] boolean
- [ ] money
- [ ] notNull
- [ ] null
- [ ] unique
- [ ] check
- [ ] defaultValue
- [ ] comment
- [ ] unsigned
- [ ] after
- [ ] first
- [ ] defaultExpression
- [ ] append
