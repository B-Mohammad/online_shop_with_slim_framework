<?php

declare(strict_types=1);

namespace App\Repo;

use App\Database;
use PDO;

class UserRepo
{
    public function __construct(private Database $db) {}

    public function getByPhone(string $phone): array|bool
    {

        $pdo = $this->db->getConn();

        $sql = "SELECT * FROM users WHERE phone = :phone";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":phone", $phone, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
