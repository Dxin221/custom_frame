<?php

namespace fastphp\db;

use PDO;
use PDOException;

class Db
{
    private static $pdo = null;

    public static function pdo()
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        try {
            $dsn      = sprintf("mysql:host=%s;dbname=%s;charset=utf8", DB_HOST, DB_NAME);
            $position = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::FETCH_ASSOC);

            return self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $position);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}