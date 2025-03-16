<?php

declare(strict_types=1);

namespace App\Repo;

use App\Database;
use PDO;

class CartRepo
{
    public function __construct(private Database $db) {}

    // public function getCart(): array
    // {
    //     $pdo = $this->db->getConn();

    //     $stmt = $pdo->query("SELECT * FROM products");
    //     $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    //     return $data;
    // }

    public function getCart(int $userId): array
    {

        $pdo = $this->db->getConn();

        $sql = "SELECT 
                    cart.id AS cart_id, 
                    products.name, 
                    products.price, 
                    cart_items.quantity
                FROM cart
                JOIN cart_items ON cart.id = cart_items.cart_id
                JOIN products ON cart_items.product_id = products.id
                WHERE cart.user_id = :user_id AND cart.is_active = TRUE;";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
