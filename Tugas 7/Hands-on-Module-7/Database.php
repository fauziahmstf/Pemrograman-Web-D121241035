<?php
declare(strict_types=1);

class Database
{
    private string $host = '127.0.0.1';
    private string $db = 'akademik_db';
    private string $user = 'root';
    private string $pass = '';
    private ?PDO $pdo = null;

    public function getConnection(): PDO
    {
        if ($this->pdo === null) {
            $dsn = "mysql:host={$this->host};port=3307;dbname={$this->db};charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                $this->pdo = new PDO(
                    $dsn,
                    $this->user,
                    $this->pass,
                    $options
                );
            } catch (PDOException $e) {
                error_log($e->getMessage());
                die('Sistem mengalami kegagalan teknis.');
            }
        }

        return $this->pdo;
    }
}