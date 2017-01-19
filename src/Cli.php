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

        if (isset($argv[1])) {
            switch ($argv[1]) {
                case 'create':
                    if (isset($argv[2])) {
                        echo $this->_migration->create($argv[2]);
                    } else {
                        echo $this->help();
                    }
                    break;

                case 'up':
                    $limit = isset($argv[2]) ? (int) $argv[2] : null;
                    echo $this->_migration->up($limit);
                    break;

                case 'down':
                    $limit = isset($argv[2]) ? (int) $argv[2] : null;
                    echo $this->_migration->down($limit);
                    break;

                case 'mark':
                    if (isset($argv[2])) {
                        echo $this->_migration->mark($argv[2]);
                    } else {
                        echo $this->help();
                    }
                    break;

                case 'redo':
                    if (isset($argv[2])) {
                        echo $this->_migration->redo($argv[2]);
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
    php {$_SERVER['PHP_SELF']} create <name> [...options...]
    php {$_SERVER['PHP_SELF']} history
    php {$_SERVER['PHP_SELF']} new

OPTIONS
    --interactive: boolean, 0 or 1 (defaults to 1)
      whether to run the command interactively.


HELP;
    }
}
