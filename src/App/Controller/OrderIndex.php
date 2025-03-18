<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repo\OrdersRepo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Valitron\Validator;

class OrderIndex
{
    public function __construct(
        private OrdersRepo $orderR,
        private Validator $validator
    ) {}

    public function AllOrders(Request $req, Response $res): Response
    {

        $data = $req->getAttribute("orders");

        $body = json_encode($data);
        $res->getBody()->write($body);

        return $res;
    }

    public function OneOrder(Request $req, Response $res): Response
    {

        $data = $req->getAttribute("order");

        $body = json_encode($data);
        $res->getBody()->write($body);

        return $res;
    }

    public function submitOrder(Request $req, Response $res): Response
    {
        $user = $req->getAttribute("user");
        $user_id = $user->user_id;

        $body = $req->getParsedBody();

        $this->validator->mapFieldsRules([
            "cart_id" => ['required', 'integer', ['min', 1]],
        ]);
        $this->validator = $this->validator->withData($body);

        if (!$this->validator->validate()) {
            $res->getBody()->write(json_encode($this->validator->errors()));
            return $res->withStatus(422);
        }

        $cart_id = $body["cart_id"];

        $data = $this->orderR->createOrder($user_id, (int)$cart_id);

        if ($data === false) {
            $temp = json_encode([
                "message" => " order not created!",
            ]);
            $res = $res->withStatus(404);
        } else {
            $temp = json_encode([
                "message" => " order submitted",
                "id" => $data
            ]);
            $res = $res->withStatus(201);
        }
        $res->getBody()->write($temp);

        return $res;
    }
}
