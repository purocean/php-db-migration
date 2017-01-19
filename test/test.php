<?php
require '../src/Db.php';

$log = "\n\n--------------------------";

$time = time();

passthru('php "'.__DIR__.'/migrate.php" create create_'.$time.'_table --interactive=0');
$log .= expectTrue(count(glob(__DIR__.'/migrations/m*_create_'.$time.'_table.php')) === 1, 'create_migration_create_table');

passthru('php "'.__DIR__.'/migrate.php" create drop_'.$time.'_table --interactive=0');
$log .= expectTrue(count(glob(__DIR__.'/migrations/m*_drop_'.$time.'_table.php')) === 1, 'create_migration_drop_table');

passthru('php "'.__DIR__.'/migrate.php" create add_column_to_'.$time.'_table --interactive=0');
$log .= expectTrue(count(glob(__DIR__.'/migrations/m*_add_column_to_'.$time.'_table.php')) === 1, 'create_migration_add_column_to_table');

passthru('php "'.__DIR__.'/migrate.php" create drop_column_from_'.$time.'_table --interactive=0');
$log .= expectTrue(count(glob(__DIR__.'/migrations/m*_drop_column_from_'.$time.'_table.php')) === 1, 'create_migration_drop_column_from_table');

$markName = substr(basename(glob(__DIR__.'/migrations/m*_drop_column_from_'.$time.'_table.php')[0]), 0, -4);
passthru('php "'.__DIR__.'/migrate.php" mark '.$markName.' --interactive=0');
$log .= expectTrue(strpos(exec('php "'.__DIR__.'/migrate.php" history'), $markName), 'mark_create_migration_drop_column_from_table');

function expectTrue($flag, $name)
{
    return "\n".($flag ? '√' : '×').' '.$name;
}

echo $log;
