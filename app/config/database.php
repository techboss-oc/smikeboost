<?php

/**
 * Database connection using PDO (MySQL)
 */
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $options = class_exists('PDO') ? [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ] : [];
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            // Ensure UTF-8 encoding
            $this->pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->pdo->exec("SET CHARACTER SET utf8mb4");
        } catch (Throwable $e) {
            $this->pdo = new class {
                public function prepare($sql)
                {
                    return new class {
                        public function execute($params = [])
                        {
                            return false;
                        }
                        public function fetch()
                        {
                            return null;
                        }
                        public function fetchAll()
                        {
                            return [];
                        }
                        public function bindValue($param, $value, $type = null)
                        {
                            return null;
                        }
                    };
                }
                public function lastInsertId()
                {
                    return 0;
                }
                public function beginTransaction()
                {
                    return false;
                }
                public function commit()
                {
                    return false;
                }
                public function rollBack()
                {
                    return false;
                }
            };
        }
    }

    public static function connection()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
