<?php

declare(strict_types=1);

namespace App\Repo;

use App\Database;
use PDO;

class OrdersRepo
{
    public function __construct(private Database $db) {}

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

    public function createOrder(int $userId, int $cartId): string|bool
    {
        $pdo = $this->db->getConn();

        $sql1 = "SELECT * FROM cart WHERE id = :cart_id  AND user_id = :user_id AND is_active = TRUE";

        $stmt = $pdo->prepare($sql1);
        $stmt->bindValue("cart_id", $cartId, PDO::PARAM_INT);
        $stmt->bindValue("user_id", $userId, PDO::PARAM_INT);
        $stmt->execute();

        $res1 = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res1 === false) {
            echo "1111";
            return false;
        }

        $sql2 = "UPDATE cart SET is_active = FALSE WHERE id = :cart_id";

        $stmt = $pdo->prepare($sql2);
        $stmt->bindValue("cart_id", $cartId, PDO::PARAM_INT);
        $stmt->execute();

        $res2 = $stmt->rowCount();
        if ($res2 !== 1) {
            echo "2222";

            return false;
        }

        $sql3 = "SELECT * FROM cart_items WHERE cart_id = :cart_id";

        $stmt = $pdo->prepare($sql3);
        $stmt->bindValue("cart_id", $cartId, PDO::PARAM_INT);
        $stmt->execute();

        $res3 = $stmt->fetchAll();
        if (empty($res3)) {
            echo "333";

            return false;
        }

        foreach ($res3 as $key => $value) {
            $sql4 = "SELECT * FROM products WHERE id = :product_id";

            $stmt = $pdo->prepare($sql4);
            $stmt->bindValue("product_id", $value["product_id"], PDO::PARAM_INT);
            $stmt->execute();

            $res4 = $stmt->fetch();
            if ($res4['quantity'] < $res3['quantity']) {
                echo "44";

                return false;
            }
        }
        $total_pice = 0;
        foreach ($res3 as $key => $value) {
            $sql4 = "SELECT * FROM products WHERE id = :product_id";

            $stmt = $pdo->prepare($sql4);
            $stmt->bindValue("product_id", $value["product_id"], PDO::PARAM_INT);
            $stmt->execute();

            $res4 = $stmt->fetch();

            $sql5 = "UPDATE products SET quantity = :quantity WHERE id = :product_id";

            $stmt = $pdo->prepare($sql5);
            $stmt->bindValue(":product_id", $value["product_id"], PDO::PARAM_INT);
            $stmt->bindValue(":quantity", $res4["quantity"] - $value["quantity"], PDO::PARAM_INT);
            $stmt->execute();

            $total_pice += ((float)$res4["price"] * $res3["quantity"]);
        }

        $sql6 = "INSERT INTO orders(user_id, cart_id, total_price) VALUES (:user_id, :cart_id, :total_price)";

        $stmt = $pdo->prepare($sql6);
        $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);
        $stmt->bindValue(":cart_id", $cartId, PDO::PARAM_INT);
        $stmt->bindValue(":total_price", $total_pice, PDO::PARAM_STR);
        $stmt->execute();

        $res6 = $pdo->lastInsertId();

        return $res6;
    }
}
