<?php

namespace App\Services;

use PDO;
use PDOException;

class DatabaseService
{
    private static ?PDO $instance = null;
    private array $config;

    private function __construct()
    {
        $this->config = [
            'host' => config('database.connections.mysql.host'),
            'port' => config('database.connections.mysql.port'),
            'database' => config('database.connections.mysql.database'),
            'username' => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
            'charset' => 'utf8mb4',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ];
    }

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $self = new self();
            $self->connect();
        }
        
        return self::$instance;
    }

    private function connect(): void
    {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $this->config['host'],
                $this->config['port'],
                $this->config['database'],
                $this->config['charset']
            );

            self::$instance = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
        } catch (PDOException $e) {
            throw new PDOException('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function disconnect(): void
    {
        self::$instance = null;
    }

    public function beginTransaction(): bool
    {
        return self::getInstance()->beginTransaction();
    }

    public function commit(): bool
    {
        return self::getInstance()->commit();
    }

    public function rollback(): bool
    {
        return self::getInstance()->rollback();
    }

    public function lastInsertId(): string
    {
        return self::getInstance()->lastInsertId();
    }
}
