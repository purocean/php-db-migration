<?php
require '../src/Column.php';
require '../src/ColumnTrait.php';
require '../src/Migrate.php';
require '../src/Migration.php';
require '../src/Db.php';
require '../src/Cli.php';

date_default_timezone_set('PRC');

$migrationConfig = require('./config.php');

(new \DbMigration\Cli($migrationConfig))->run();
