<?php
namespace DbMigration;

/**
* Cli
*/
class Cli
{
    public $migrationConfig = [];

    public function __construct($migrationConfig = [])
    {
        $this->_migration = new Migrate($migrationConfig);
    }

    public function run()
    {
        global $argv;

        $params = array_filter($argv, function ($param) {
            return strpos($param, '-') === false;
        });

        if (isset($params[1])) {
            switch ($params[1]) {
                case 'create':
                    if (isset($params[2])) {
                        echo $this->_migration->create($params[2]);
                    } else {
                        echo $this->help();
                    }
                    break;

                case 'up':
                    $limit = isset($params[2]) ? (int) $params[2] : null;
                    echo $this->_migration->up($limit);
                    break;

                case 'down':
                    $limit = isset($params[2]) ? (int) $params[2] : 1;
                    echo $this->_migration->down($limit);
                    break;

                case 'mark':
                    if (isset($params[2])) {
                        $applied = !(isset($params[3]) and $params[3] === '0');
                        echo $this->_migration->mark($params[2], $applied);
                    } else {
                        echo $this->help();
                    }
                    break;

                case 'new':
                    echo $this->_migration->new();
                    break;

                case 'history':
                    echo $this->_migration->history();
                    break;

                default:
                    echo $this->help();
                    break;
            }
        } else {
            echo $this->help();
        }
    }

    public function help()
    {
        return <<<HELP
USAGE
    php {$_SERVER['PHP_SELF']} create <name> [--interactive=0|1]
    php {$_SERVER['PHP_SELF']} up [1-n]
    php {$_SERVER['PHP_SELF']} down <name> [1-n] [--interactive=0|1]
    php {$_SERVER['PHP_SELF']} history
    php {$_SERVER['PHP_SELF']} new
    php {$_SERVER['PHP_SELF']} mark <version> [0|1] [--interactive=0|1]

OPTIONS
    --interactive: boolean, 0 or 1 (defaults to 1)
      whether to run the command interactively.


HELP;
    }
}
