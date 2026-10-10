<?php
declare(strict_types=1);

class Database
{
    private string $host = '127.0.0.1';
    private int $port = 3307;
    private string $dbname = 'buku_tamu_db';
    private string $username = 'root';
    private string $password = '';

    public function getConnection(): PDO
    {
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->dbname};charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        return new PDO(
            $dsn,
            $this->username,
            $this->password,
            $options
        );
    }
}