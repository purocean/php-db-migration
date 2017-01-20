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
        echo "  > execute SQL: $sql ...\n";
        $time = microtime(true);
        $this->db->exec(Db::getQuoted($sql, $this->tablePrefix), $params);
        echo '  < done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function createTable($table, $columns, $options = '')
    {
        $table = Db::getQuoted($table, $this->tablePrefix);

        echo "\n> create table $table ...\n";
        $time = microtime(true);
        $sql = "CREATE TABLE {$table} (";
        foreach ($columns as $key => $column) {
            $sql .= "\n    ".$column->buildCompleteString(Db::getQuoted("[[{$key}]]")).',';
        }
        $sql = trim($sql, ',');
        $sql .= "\n) ".$options;
        $this->execute($sql);
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function renameTable($table, $newName)
    {
        $table = Db::getQuoted($table, $this->tablePrefix);
        $newName = Db::getQuoted($newName, $this->tablePrefix);

        echo "\n> rename table $table to $newName ...\n";
        $time = microtime(true);
        $this->execute("RENAME TABLE {$table} TO {$newName}");
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropTable($table, $checkExists = false)
    {
        $table = Db::getQuoted($table, $this->tablePrefix);

        echo "\n> drop table $table ...\n";
        $time = microtime(true);
        $this->execute("DROP TABLE ".($checkExists ? 'IF EXISTS' : ''). " {$table}");
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function truncateTable($table)
    {
        $table = Db::getQuoted($table, $this->tablePrefix);

        echo "\n> truncate table $table ...\n";
        $time = microtime(true);
        $this->execute("TRUNCATE TABLE {$table}");
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function addColumn($table, $column, $type)
    {
        $table = Db::getQuoted($table, $this->tablePrefix);
        $column = Db::getQuoted("[[{$column}]]");
        $type = $type->buildCompleteString($column);

        echo "\n> add column $type to table $table ...\n";
        $time = microtime(true);
        $this->execute("ALTER TABLE {$table} ADD COLUMN {$type}");
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropColumn($table, $column)
    {
        $table = Db::getQuoted($table, $this->tablePrefix);
        $column = Db::getQuoted("[[{$column}]]");

        echo "\n> drop column $column from table $table ...\n";
        $time = microtime(true);
        $this->execute("ALTER TABLE {$table} DROP COLUMN {$column}");
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function renameColumn($table, $name, $newName)
    {
        $table = Db::getQuoted($table, $this->tablePrefix);
        $name = Db::getQuoted("[[{$name}]]");
        $newName = Db::getQuoted("[[{$newName}]]");

        echo "\n> rename column $name in table $table to $newName ...\n";
        $time = microtime(true);

        if (!$row = $this->db->fetch("SHOW CREATE TABLE {$table}")) {
            throw new \Exception("Unable to find column '{$name}' in table '{$table}'.");
        }

        $sql = '';
        if (preg_match_all('/^\s*(`.+?`)\s+(.*?),?$/m', $row['Create Table'], $matches)) {
            $columns = array_combine($matches[1], $matches[2]);
            if (isset($columns[$name])) {
                $sql = "ALTER TABLE {$table} CHANGE COLUMN {$name} {$newName} {$columns[$name]}";
            }
        }

        if ($sql) {
            $this->execute($sql);
        } else {
            echo "ERROR";
        }

        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function alterColumn($table, $column, $type)
    {
        $table = Db::getQuoted($table, $this->tablePrefix);
        $column = Db::getQuoted("[[{$column}]]");
        $type = $type->buildCompleteString($column);
        var_dump($type);

        echo "\n> alter column $column in table $table to $type ...\n";
        $time = microtime(true);
        $this->execute("ALTER TABLE {$table} CHANGE COLUMN {$column} {$type}");
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function addPrimaryKey($name, $table, $columns)
    {
        echo "\n> add primary key $name on $table (" . (is_array($columns) ? implode(',', $columns) : $columns) . ') ...';
        $time = microtime(true);
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropPrimaryKey($name, $table)
    {
        echo "\n> drop primary key $name ...\n";
        $time = microtime(true);
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null)
    {
        echo "\n> add foreign key $name: $table (" . implode(',', (array) $columns) . ") references $refTable (" . implode(',', (array) $refColumns) . ') ...';
        $time = microtime(true);
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropForeignKey($name, $table)
    {
        echo "\n> drop foreign key $name from table $table ...\n";
        $time = microtime(true);
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function createIndex($name, $table, $columns, $unique = false)
    {
        $table = Db::getQuoted($table, $this->tablePrefix);
        $name = Db::getQuoted("[[{$name}]]");

        $columns = '('.implode(',', array_map(
            function ($column) {
                return Db::getQuoted('[['.trim($column).']]');
            },
            is_array($columns) ? $columns : explode(',', trim(trim(trim($columns), '('), ')'))
        )).')';

        echo '    > create' . ($unique ? ' unique' : '') . " index $name on $table $columns ...";
        $time = microtime(true);
        $this->execute(
            ($unique ? 'CREATE UNIQUE INDEX ' : 'CREATE INDEX ')
            ."{$name} ON {$table} {$columns}"
        );
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropIndex($name, $table)
    {
        $table = Db::getQuoted($table, $this->tablePrefix);
        $name = Db::getQuoted("[[{$name}]]");

        echo "\n> drop index $name on $table ...\n";
        $time = microtime(true);
        $this->execute("DROP INDEX {$name} ON {$table}");
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function addCommentOnColumn($table, $column, $comment)
    {
        echo "\n> add comment on column $column ...\n";
        $time = microtime(true);
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function addCommentOnTable($table, $comment)
    {
        echo "\n> add comment on table $table ...\n";
        $time = microtime(true);
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropCommentFromColumn($table, $column)
    {
        echo "\n> drop comment from column $column ...\n";
        $time = microtime(true);
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }

    public function dropCommentFromTable($table)
    {
        echo "\n> drop comment from table $table ...\n";
        $time = microtime(true);
        echo '< done (time: ' . sprintf('%.3f', microtime(true) - $time) . "s)\n";
    }
}
