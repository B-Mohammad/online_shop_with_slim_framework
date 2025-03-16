<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repo\ProductsRepo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class ProductIndex
{
    public function __construct(private ProductsRepo $productR) {}

    // public function __invoke(Request $req, Response $res): Response
    // {

    //     $data = $this->productR->getAll();

    //     $body = json_encode($data);
    //     $res->getBody()->write($body);
    //     return $res;
    // }

    public function AllProducts(Request $req, Response $res): Response
    {

        $data = $this->productR->getAll();

        $body = json_encode($data);
        $res->getBody()->write($body);
        return $res;
    }

    public function OneProduct(Request $req, Response $res, string $id): Response
    {

        $data = $req->getAttribute("product");

        $body = json_encode($data);
        $res->getBody()->write($body);

        return $res;
    }
}
