<?php

namespace Models;

use PDO;

/**
 * Singleton allowing the access to the database
 * through the getConnection() method.
 * Configuration on config.php
 */
class Base
{

    private static $dblink;    // The PDO connecting to the database.

    /**
     * Private constructor, for singleton instance
     */
    final private function __construct()
    {
    }

    /**
     * Private clone, for singleton instance
     */
    final private function __clone()
    {
    }

    /**
     * Trie to connect with the database.
     *
     * @return PDO The PDO connected to the database.
     * @throws \PDOException
     */
    private static function connect()
    {
        global $config;
        $db = new PDO($config['db']['host'], $config['db']['user'], $config['db']['password'],
            array(
                PDO::ERRMODE_EXCEPTION => true,
                PDO::ATTR_PERSISTENT => true
            ));
        return $db;
    }


    /**
     * Return the connection with the database
     *
     * @return PDO The PDO connected to the database.
     */
    public static function getConnection()
    {
        if (isset(self::$dblink)) {
            return self::$dblink;
        } else {
            self::$dblink = self::connect();
            return self::$dblink;
        }
    }
}