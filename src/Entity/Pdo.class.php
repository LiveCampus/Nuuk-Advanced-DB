<?php

class Db
{
    private static $connection;

    private function __construct()
    {
    }
    private function __clone()
    {
    }

    public static function connect()
    {
        if (!self::$connection) {
            try {
                self::$connection = new PDO("mysql:host=db:" . $_ENV["MYSQL_PORT"] . ";dbname=" . $_ENV["MYSQL_DB"], $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASSWORD"]);
            } catch (PDOException $e) {
                self::$connection = null;
                dd($e->getMessage());
            }
        }
        return self::$connection;
    }
}

$a = Db::connect();