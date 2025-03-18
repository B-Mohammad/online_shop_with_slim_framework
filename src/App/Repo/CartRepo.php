<?php

declare(strict_types=1);

namespace App\Repo;

use App\Database;
use PDO;

class CartRepo
{
    public function __construct(private Database $db) {}

    public function getCart(int $userId): array
    {

        $pdo = $this->db->getConn();

        $sql = "SELECT 
                    cart.id AS cart_id, 
                    products.id AS products_id, 
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

    public function getCartById(int $cartId): array
    {

        $pdo = $this->db->getConn();

        $sql = "SELECT 
                    products.name, 
                    products.price, 
                    cart_items.quantity
                FROM cart_items
                JOIN products ON cart_items.product_id = products.id
                WHERE cart_items.cart_id = :cart_id ;";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":cart_id", $cartId, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addToCart(int $userId, int $product_id, int $quantity): string|bool
    {

        $pdo = $this->db->getConn();

        $sql5 = "SELECT * FROM products WHERE id = :product_id";

        $stmt = $pdo->prepare($sql5);
        $stmt->bindValue(":product_id", $product_id, PDO::PARAM_INT);

        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res === false || $res["quantity"] < $quantity) {
            return false;
        }


        $sql = "SELECT id FROM cart WHERE user_id = :user_id AND is_active = TRUE";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);

        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($res === false) {
            $sql = "INSERT INTO cart (user_id) VALUES (:user_id)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);

            $stmt->execute();
            $cart_id = $pdo->lastInsertId();

            $sql2 = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (:cart_id, :product_id, :quantity)";
            $stmt = $pdo->prepare($sql2);
            $stmt->bindValue(":cart_id", $cart_id, PDO::PARAM_INT);
            $stmt->bindValue(":product_id", $product_id, PDO::PARAM_INT);
            $stmt->bindValue(":quantity", $quantity, PDO::PARAM_INT);

            $stmt->execute();
            return $pdo->lastInsertId();
        } else {
            $sql3 = "SELECT * FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";
            $stmt = $pdo->prepare($sql3);
            $stmt->bindValue(":cart_id", $res['id'], PDO::PARAM_INT);
            $stmt->bindValue(":product_id", $product_id, PDO::PARAM_INT);

            $stmt->execute();
            $res2 = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($res2 === false) {
                $sql2 = "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES (:cart_id, :product_id, :quantity)";
                $stmt = $pdo->prepare($sql2);
                $stmt->bindValue(":cart_id", $res['id'], PDO::PARAM_INT);
                $stmt->bindValue(":product_id", $product_id, PDO::PARAM_INT);
                $stmt->bindValue(":quantity", $quantity, PDO::PARAM_INT);

                $stmt->execute();
                return $pdo->lastInsertId();
            } else {
                return false;
            }
        }
    }

    public function deleteFromCart(int $userId, int $product_id): int|bool
    {
        $pdo = $this->db->getConn();

        $sql5 = "SELECT * FROM products WHERE id = :product_id";

        $stmt = $pdo->prepare($sql5);
        $stmt->bindValue(":product_id", $product_id, PDO::PARAM_INT);

        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res === false) {
            return false;
        }

        $sql = "SELECT id FROM cart WHERE user_id = :user_id AND is_active = TRUE";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);

        $stmt->execute();
        $res2 = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res2 === false) {
            return false;
        }

        $sqlD = "DELETE FROM cart_items WHERE product_id = :product_id AND cart_id = :cart_id";
        $stmt = $pdo->prepare($sqlD);
        $stmt->bindValue(":product_id", $product_id, PDO::PARAM_INT);
        $stmt->bindValue(":cart_id", $res2['id'], PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function editCart(int $userId, int $product_id, int $quantity): int|bool
    {

        $pdo = $this->db->getConn();

        $sql5 = "SELECT * FROM products WHERE id = :product_id";

        $stmt = $pdo->prepare($sql5);
        $stmt->bindValue(":product_id", $product_id, PDO::PARAM_INT);

        $stmt->execute();
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res === false) {
            return false;
        }


        $sql = "SELECT id FROM cart WHERE user_id = :user_id AND is_active = TRUE";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":user_id", $userId, PDO::PARAM_INT);

        $stmt->execute();
        $res2 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($res2 === false) {
            return false;
        }

        $sql3 = "SELECT * FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id";
        $stmt = $pdo->prepare($sql3);
        $stmt->bindValue(":cart_id", $res2['id'], PDO::PARAM_INT);
        $stmt->bindValue(":product_id", $product_id, PDO::PARAM_INT);

        $stmt->execute();
        $res3 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($res3 === false || $res3["quantity"]  + $quantity < 0 || $res3["quantity"] + $quantity > $res["quantity"]) {
            return false;
        }

        $temp = "UPDATE cart_items SET cart_items.quantity = :quantity WHERE cart_items.product_id = :product_id AND cart_items.cart_id = :cart_id;";
        $stmts = $pdo->prepare($temp);

        $stmts->bindValue(":cart_id", $res2["id"], PDO::PARAM_INT);
        $stmts->bindValue(":product_id", $product_id, PDO::PARAM_INT);
        $stmts->bindValue(":quantity", $res3["quantity"] + $quantity, PDO::PARAM_INT);
        $stmts->execute();

        return $stmts->rowCount();
    }
}
