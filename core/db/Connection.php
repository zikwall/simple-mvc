<?php

namespace core\db;

use core\Core;

class Connection
{
    /**
     * The connection to the database.
     *
     * @access protected
     * @var    \PDO
     */

    protected $_connection;
    /**
     * The query that we have just run.
     *
     * @access protected
     * @var    \PDOStatement
     */

    protected $_statement;

    /**
     * @var \PDO
     */
    protected $pdoInstance;

    /**
     * @var null|\PDOStatement
     */
    protected $pdoStatement = null;

    /**
     * Connect to the database if we have not already.
     *
     * @access protected
     */
    protected function connect()
    {
        $host     = Core::$app->config->db['host'];
        $database = Core::$app->config->db['dbname'];
        $username = Core::$app->config->db['user'];
        $password = Core::$app->config->db['password'];
        try {
            $this->_connection = new \PDO(
                "mysql:host={$host};dbname={$database};charset=utf8",
                $username,
                $password
            );
            $this->setPdoInstance($this->_connection);
        } catch(\PDOException $e) {
            die('<p>Sorry, we were unable to complete your request.</p>');
        }
    }

    /**
     * @param \PDO $pdo
     *
     * @return $this
     */
    public function setPdoInstance(\PDO $pdo)
    {
        $this->pdoInstance = $pdo;
        return $this;
    }

    /**
     * @return \PDO
     */
    public function getPdoInstance()
    {
        return $this->pdoInstance;
    }

}