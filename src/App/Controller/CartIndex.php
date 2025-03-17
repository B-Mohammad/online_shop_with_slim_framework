<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repo\CartRepo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class CartIndex
{
    public function __construct(private CartRepo $cartR) {}

    public function getCart(Request $req, Response $res): Response
    {

        $data = $req->getAttribute("cart");

        $body = json_encode($data);
        $res->getBody()->write($body);

        return $res;
    }
}
