<?php

declare(strict_types=1);

namespace App\Repo;

use App\Database;
use PDO;

class OrdersRepo
{
    public function __construct(private Database $db) {}

    // public function getAll(): array
    // {
    //     $pdo = $this->db->getConn();

    //     $stmt = $pdo->query("SELECT * FROM orders");
    //     $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     return $data;
    // }

    public function getAll(int $userId): array
    {

        $pdo = $this->db->getConn();

        $sql = "SELECT * FROM orders WHERE user_id = :userId";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $userId, int $cartId): array|bool
    {

        $pdo = $this->db->getConn();

        $sql = "SELECT * FROM orders WHERE user_id = :userId and cart_id = :cartId";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
        $stmt->bindValue(":cartId", $cartId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
