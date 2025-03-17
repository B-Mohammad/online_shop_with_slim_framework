<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repo\OrdersRepo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class OrderIndex
{
    public function __construct(private OrdersRepo $orderR) {}

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
}
