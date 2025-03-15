<?php

declare(strict_types=1);

namespace App;

use PDO;

class Database
{
    public function __construct(
        private string $host,
        private string $dbname,
        private string $user,
        private string $pass
    ) {}

    public function getConn(): PDO
    {
        try {
            $dsn = "mysql:host=localhost;dbname=online_shop";
            $pdo = new PDO(
                $dsn,
                'root',
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (\Throwable $th) {
            echo "database coon error!";
        }

        return $pdo;
    }
}
