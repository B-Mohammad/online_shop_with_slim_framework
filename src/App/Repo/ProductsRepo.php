<?php

declare(strict_types=1);

namespace App\Repo;

use App\Database;
use PDO;

class ProductsRepo
{
    public function __construct(private Database $db) {}

    public function getAll(): array
    {
        $pdo = $this->db->getConn();

        $stmt = $pdo->query("SELECT * FROM products");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    public function getByID(int $id): array|bool
    {

        $pdo = $this->db->getConn();

        $sql = "SELECT * FROM products WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
