<?php

namespace libs\Db;

use PDO;
use PDOException;
use libs\Db\Exception\DbException;
use libs\Db\tAccess;
use libs\Db\DbConfig;

/**
 * Singleton Db
 * 
 * Using for query execution and connection to database
 * @package libs\Db
 */
final class Db
{
    use tAccess;
    
    /**
     * connection
     *
     * @access private
     * @var object
     */
    private static $connection = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * connect
     *
     * Connecting to dataabse
     *
     * @throws DbException
     * @access public
     * @return object PDO
     */
    public static function connect()
    {
        if(null === self::$connection) {
            try {
                $config = [
                    PDO::ATTR_ERRMODE, 
                    PDO::ERRMODE_EXCEPTION, 
                    PDO::ERRMODE_WARNING
                ];

                $params = self::$driver.":host=".self::$host.";dbname=".self::$dbName.";charset=utf8";
                self::$connection = new PDO($params, self::$login, self::$password, $config);
            } catch (PDOException $e) {
                throw new DbException(DbConfig::ERROR_CONNECT);
            }
        }    
        
        return self::$connection;
    }

    /**
     * query
     *
     * Execute sql query with method query of PDO
     *
     * @param string $sql sql query
     * @access public
     * @uses DbValid
     * @throws DbException
     * @return $stmt object PDO
     */
    public static function query($sql)
    {
        $emptySql = (new DbValid())->emptySql($sql);

        if($emptySql) {
            $stmt = self::$connection->query($sql);

            if(!$stmt) {
                throw new DbException(DbConfig::ERROR_QUERY);
            }

            return $stmt;
        }
    }

    /**
     * prepare
     *
     * Execute sql query with method prepare of PDO
     *
     * @param string $sql sql query
     * @param array $args arguments for prepare method
     * @access public
     * @throws DbException
     * @uses ValidDb
     * @return $stmt object PDO
     */
    public static function prepare($sql, $args)
    {
        $emptySql = (new DbValid())->emptySql($sql);
        $emptyArgs = (new DbValid())->emptyArgs($args);

        if($emptySql && $emptyArgs) {
            $stmt = self::$connection->prepare($sql);

            foreach($args as $id => &$arg) {
                $stmt->bindParam("$id", $arg, PDO::PARAM_STR);
            }

            $stmt->execute();

            if($stmt->errorCode() == 0) {
                return $stmt;
            } else {
                throw new DbException(DbConfig::ERROR_QUERY);
            }
        }
    }

    /**
     * lastInsertId
     * 
     * Return last added entry
     *
     * @access public
     * @return int id
     */
    public static function lastInsertId()
    {
        return self::$connection->lastInsertId();
    }
}
