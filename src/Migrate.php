<?php
namespace DbMigration;

/**
* Migrate
*/
class Migrate
{
    const BASE_MIGRATION = 'm000000_000000_base';

    public $migrationTable = '{{%migration}}';

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
        return 'Not implement';
    }

    public function down($limit = null)
    {
        return 'Not implement';
    }

    public function mark($name, $done = true)
    {
        return 'Not implement';
    }

    public function redo($name)
    {
        return 'Not implement';
    }

    public function new()
    {
        return 'Not implement';
    }

    public function history()
    {
        return 'Not implement';
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
