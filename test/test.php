<?php
require '../src/Db.php';

$config = require('./config.php');
$db = new \DbMigration\Db($config['dbConfig']);

$config['dbConfig']['dsn'] = str_replace('dbname=db_migration_test;', '', $config['dbConfig']['dsn']);
$xdb = new \DbMigration\Db($config['dbConfig']);
$xdb->exec('DROP DATABASE IF EXISTS db_migration_test');
$xdb->exec('CREATE DATABASE IF NOT EXISTS db_migration_test');

foreach (glob(__DIR__.'/migrations/*_table.php') as $file) {
    @unlink($file);
}

$log = "\n\n--------------------------";

$time = time();

passthru('php "'.__DIR__.'/migrate.php" create create_'.$time.'_table --interactive=0');
$log .= expectTrue(count(glob(__DIR__.'/migrations/m*_create_'.$time.'_table.php')) === 1, 'create_migration_create_table');
sleep(1);

passthru('php "'.__DIR__.'/migrate.php" create drop_'.$time.'_table --interactive=0');
$log .= expectTrue(count(glob(__DIR__.'/migrations/m*_drop_'.$time.'_table.php')) === 1, 'create_migration_drop_table');
sleep(1);

passthru('php "'.__DIR__.'/migrate.php" create add_column_to_'.$time.'_table --interactive=0');
$log .= expectTrue(count(glob(__DIR__.'/migrations/m*_add_column_to_'.$time.'_table.php')) === 1, 'create_migration_add_column_to_table');
sleep(1);

passthru('php "'.__DIR__.'/migrate.php" create drop_column_from_'.$time.'_table --interactive=0');
$log .= expectTrue(count(glob(__DIR__.'/migrations/m*_drop_column_from_'.$time.'_table.php')) === 1, 'create_migration_drop_column_from_table');

$migrationName = 'm170119_101310_test_migration';
passthru('php "'.__DIR__.'/migrate.php" up 1 --interactive=0');
$log .= expectTrue(
    strpos(exec('php "'.__DIR__.'/migrate.php" history'), $migrationName)
    and $db->fetch('SHOW CREATE TABLE '.\DbMigration\Db::getQuoted('{{%new_test}}', $config['tablePrefix']))
    and !$db->fetchAll('SELECT * FROM '.\DbMigration\Db::getQuoted('{{%new_test}}', $config['tablePrefix']).'WHERE 1')
, 'up_m170119_101310_test_migration');

passthru('php "'.__DIR__.'/migrate.php" down --interactive=0');
$log .= expectTrue(
    strpos(exec('php "'.__DIR__.'/migrate.php" history'), $migrationName) === false
    and count($db->fetchAll('SHOW TABLES')) === 1
, 'done_m170119_101310_test_migration');

$markName = substr(basename(glob(__DIR__.'/migrations/m*_drop_'.$time.'_table.php')[0]), 0, -4);
passthru('php "'.__DIR__.'/migrate.php" mark '.$markName.' --interactive=0');
$log .= expectTrue(strpos(exec('php "'.__DIR__.'/migrate.php" history'), $markName), 'mark_drop_table');

passthru('php "'.__DIR__.'/migrate.php" up --interactive=0');

function expectTrue($flag, $name)
{
    return "\n".($flag ? '√' : '×').' '.$name;
}

var_dump($db->fetch('show create table '.\DbMigration\Db::getQuoted('{{%new_test}}', $config['tablePrefix'])));

echo $log;
