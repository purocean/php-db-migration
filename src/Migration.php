<?php
namespace DbMigration;

/**
* Migration
*/
class Migration
{
    use ColumnTrait;

    public $db = null;
    public $tablePrefix = '';

    public function __construct($config)
    {
        foreach ($config as $key => $val) {
            $this->$key = $val;
        }
    }

    /**
     * 升级迁移
     * @return bool 是否能被升级，返回 false 表示此迁移不能被升级
     */
    public function up()
    {
        return true;
    }

    /**
     * 回滚迁移
     * @return bool 是否能被回滚，返回 false 表示此迁移不能被回滚
     */
    public function down()
    {
        return true;
    }

    public function execute($sql, $params = [])
    {
        echo "    > execute SQL: $sql ...";
        $time = microtime(true);
        $this->db->exec(Db::getQuoted($sql, $this->tablePrefix), $params);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function createTable($table, $columns, $options = '')
    {
        $table = Db::getQuoted($table, $this->tablePrefix);
        echo "    > create table $table ...";
        $time = microtime(true);
        $sql = "\nCREATE TABLE {$table} (";
        foreach ($columns as $key => $column) {
            $sql .= "\n    ".$column->buildCompleteString($key).',';
        }
        $sql = trim($sql, ',');
        $sql .= "\n) ".$options;
        $this->execute($sql);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function renameTable($table, $newName)
    {
        echo "    > rename table $table to $newName ...";
        $time = microtime(true);
        $this->execute(
            "RENAME TABLE "
            .Db::getQuoted($table, $this->tablePrefix)
            ." TO ".Db::getQuoted($newName, $this->tablePrefix)
        );
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropTable($table, $checkExists = false)
    {
        $table = Db::getQuoted($table, $this->tablePrefix);
        echo "    > drop table $table ...";
        $time = microtime(true);
        $this->execute("DROP TABLE ".($checkExists ? 'IF EXISTS' : ''). " {$table}");
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function truncateTable($table)
    {
        echo "    > truncate table $table ...";
        $time = microtime(true);
        $this->execute("TRUNCATE TABLE ".Db::getQuoted($table, $this->tablePrefix));
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function addColumn($table, $column, $type)
    {
        echo "    > add column $column $type to table $table ...";
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropColumn($table, $column)
    {
        echo "    > drop column $column from table $table ...";
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function renameColumn($table, $name, $newName)
    {
        echo "    > rename column $name in table $table to $newName ...";
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function alterColumn($table, $column, $type)
    {
        echo "    > alter column $column in table $table to $type ...";
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function addPrimaryKey($name, $table, $columns)
    {
        echo "    > add primary key $name on $table (" . (is_array($columns) ? implode(',', $columns) : $columns) . ') ...';
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropPrimaryKey($name, $table)
    {
        echo "    > drop primary key $name ...";
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        echo "    > add foreign key $name: $table (" . implode(',', (array) $columns) . ") references $refTable (" . implode(',', (array) $refColumns) . ') ...';
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropForeignKey($name, $table)
    {
        echo "    > drop foreign key $name from table $table ...";
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function createIndex($name, $table, $columns, $unique = false)
    {
        echo '    > create' . ($unique ? ' unique' : '') . " index $name on $table (" . implode(',', (array) $columns) . ') ...';
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropIndex($name, $table)
    {
        echo "    > drop index $name on $table ...";
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function addCommentOnColumn($table, $column, $comment)
    {
        echo "    > add comment on column $column ...";
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function addCommentOnTable($table, $comment)
    {
        echo "    > add comment on table $table ...";
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropCommentFromColumn($table, $column)
    {
        echo "    > drop comment from column $column ...";
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropCommentFromTable($table)
    {
        echo "    > drop comment from table $table ...";
        $time = microtime(true);
        echo ' done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }
}
