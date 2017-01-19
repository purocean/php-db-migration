<?php
namespace DbMigration;

/**
* Migrate
*/
class Migrate
{
    const BASE_MIGRATION = 'm000000_000000_base';

    public $migrationTable = '{{%migrations}}';

    public $templateFiles = [
        'create_table' => __DIR__.'/templates/create_table.php',
        'add_column'   => __DIR__.'/templates/add_column.php',
        'drop_column'  => __DIR__.'/templates/drop_column.php',
        'drop_table'   => __DIR__.'/templates/drop_table.php',
        '__default__'  => __DIR__.'/templates/default.php',
    ];

    public $tablePrefix = null;

    public $migrationsPath = './migrations';

    public $dbConfig = [
        'dsn'      => '',
        'username' => 'root',
        'password' => '',
    ];

    private $_db = null;


    public function __construct($config = [])
    {
        foreach ($config as $key => $val) {
            if (is_array($val)) {
                $this->$key = array_merge($this->$key, $val);
            } else {
                $this->$key = $val;
            }
        }

        if (stripos($this->dbConfig['dsn'], 'mysql') !== 0) {
            throw new \Exception('Only Support MySQL');
        }

        $this->_db = new Db($this->dbConfig);
        $this->_init();
    }

    public function create($name)
    {
        $className = 'm'.gmdate('ymd_His').'_'.$name;
        $fileName = $this->migrationsPath.'/'.$className.'.php';
        if ($this->_confirm("Create new migration '{$fileName}'?")) {
            file_put_contents(
                $fileName,
                $this->_generateMigrationSourceCode($className, $name)
            );
        }
    }

    public function up($limit = null)
    {
        $migrations = array_slice($this->_getNew(), 0, $limit);
        // if (!$this->_confirm(
        //     "Total ".count($migrations)." new migration to be applied:\n    "
        //     .implode("\n    ", array_map(function ($migration) {
        //         return basename($migration);
        //     }, $migrations))
        //     ."\nApply the above migration?"
        // )) {
        //     return "No effect";
        // }

        $log = '';
        foreach ($migrations as $migration) {
            try {
                require $migration;
                $name = '\\'.substr(basename($migration), 0, -4);
                $instance = new $name([
                    'db' => $this->_db,
                ]);
                if ($instance->up() and $instance->do()) {
                    $log .= $instance->log;
                    $this->_toUp($name);
                } else {
                    return $instance->log;
                }
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return $log;
    }

    public function down($limit = 1)
    {
        return 'Not implement';
    }

    public function mark($name, $applied = true)
    {
        if (!$this->_migrationExists($name)) {
            return "Migration '{$name}' not exists";
        }

        $result = false;
        if ($applied) {
            $this->_confirm("Mark migration '{$name}' to applied ?")
                and $result = $this->_toUp($name);
        } else {
            $this->_confirm("Mark migration '{$name}' to down ?")
                and $result = $this->_toDown($name);
        }

        return "Mark {$name} to ".($applied ? 'applied' : 'down').' '.($result ? 'success' : 'failed');
    }

    public function redo($name)
    {
        return 'Not implement';
    }

    public function new()
    {
        $new = "\nShowing the new migrations:";
        foreach ($this->_getNew() as $file) {
            $new .= "\n    ".basename($file);
        }

        return $new;
    }

    public function history()
    {
        $history = "\nShowing the applied migrations:";
        foreach ($this->_getHistory() as $row) {
            $history .= "\n    [".date('Y-m-d H:i:s', $row['apply_time']).'] '.$row['name'];
        }

        return $history;
    }

    public static function getQuoted($name)
    {
        return str_replace(
            ['{{%', '{{', '}}', '[[', ']]'],
            ['`'.$this->tablePrefix, '`', '`', '`', '`'],
            $name
        );
    }

    private function _migrationExists($name)
    {
        return file_exists("{$this->migrationsPath}/{$name}.php");
    }

    private function _init()
    {
        $migrationTable = self::getQuoted($this->migrationTable);

        $this->_db->exec("CREATE TABLE IF NOT EXISTS {$migrationTable} (
          `name` varchar(180) NOT NULL,
          `apply_time` int(11) DEFAULT NULL,
          PRIMARY KEY (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8");

        $this->_toUp(self::BASE_MIGRATION);
    }

    private function _toUp($name)
    {
        $migrationTable = self::getQuoted($this->migrationTable);
        try {
            return $this->_db->exec(
                "INSERT INTO {$migrationTable} set `name` = ?, `apply_time` = ?",
                [$name, time()]
            );
        } catch (\PDOException $e) {
            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function _toDown($name)
    {
        $migrationTable = self::getQuoted($this->migrationTable);
        try {
            return $this->_db->exec(
                "DELETE FROM {$migrationTable} WHERE `name` = ?",
                [$name]
            );
        } catch (\PDOException $e) {
            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function _getHistory()
    {
        $migrationTable = self::getQuoted($this->migrationTable);
        return $this->_db->fetchAll("SELECT * FROM {$migrationTable} WHERE 1 ORDER BY `apply_time` ASC");
    }

    private function _getNew()
    {
        $history = array_column($this->_getHistory(), 'name');
        $migrations = glob($this->migrationsPath.'/*');
        $new = array_filter($migrations, function ($migration) use ($history) {
            return !in_array(substr(basename($migration), 0, -4), $history);
        });

        usort($new, function ($migrationA, $migrationB) {
            return strnatcasecmp(basename($migrationA), basename(basename($migrationB)));
        });

        return $new;
    }

    private function _renderTemplate($file, $params)
    {
        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
        require($file);

        return ob_get_clean();
    }

    private function _generateMigrationSourceCode($className, $name)
    {
        $templateFile = $this->templateFiles['__default__'];
        $table = null;
        if (preg_match('/^create_(.+)_table$/', $name, $matches)) {
            $templateFile = $this->templateFiles['create_table'];
            $table = strtolower($matches[1]);
        } elseif (preg_match('/^add_columns?_to_(.+)_table$/', $name, $matches)) {
            $templateFile = $this->templateFiles['add_column'];
            $table = strtolower($matches[1]);
        } elseif (preg_match('/^drop_columns?_from_(.+)_table$/', $name, $matches)) {
            $templateFile = $this->templateFiles['drop_column'];
            $table = strtolower($matches[1]);
        } elseif (preg_match('/^drop_(.+)_table$/', $name, $matches)) {
            $templateFile = $this->templateFiles['drop_table'];
            $table = strtolower($matches[1]);
        }

        return $this->_renderTemplate($templateFile, [
            'table' => $this->tablePrefix ? '{{%'.$table.'}}' : $table,
            'className' => $className,
        ]);
    }

    public function _confirm($text)
    {
        global $argv;
        if (in_array('--interactive=0', $argv)) {
            return true;
        }

        echo "{$text} (yes|no) [no]:";
        $confirmation = trim(fgets(STDIN));
        return $confirmation and $confirmation{0} === 'y';
    }
}
