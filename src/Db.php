<?php
namespace DbMigration;

/**
* Db
*/
class Db
{
    private $_config = [
        'username' => 'root',
        'password' => '',
        'dsn' => '',
    ];

    public $driverName = null;

    public $pdo = null;

    public function __construct($config)
    {
        $this->_config = array_merge($this->_config, $config);

        try {
            $this->pdo = new \PDO(
                $this->_config['dsn'],
                $this->_config['username'],
                $this->_config['password']
            );
            $this->driverName = explode(':', $this->_config['dsn'], 2)[0];
        } catch (\PDOException $e) {
            throw $e;
        }

        $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function fetch($sql, $data = [], $fetchStyle = \PDO::FETCH_ASSOC)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $stmt->fetch($fetchStyle);
    }

    public function fetchAll($sql, $data = [], $fetchStyle = \PDO::FETCH_ASSOC)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $stmt->fetchAll($fetchStyle);
    }

    public function exec($sql, $data = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $stmt->rowCount();
    }

    public function getLastError()
    {
        $error['code'] = $this->pdo->errorCode();
        $error['info'] = $this->pdo->errorInfo();

        return $error;
    }
}
