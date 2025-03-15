<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repo\ProductsRepo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use Slim\Exception\HttpNotFoundException;

class GetProduct
{
    public function __construct(private ProductsRepo $productR) {}

    public function __invoke(Request $req, RequestHandler $reqHandler): Response
    {
        $context = RouteContext::fromRequest($req);
        $route = $context->getRoute();
        $id = $route->getArgument("id");


        $data = $this->productR->getByID((int) $id);

        if ($data === false) {
            throw new HttpNotFoundException($req, message: "Product not found!");
        }

        $req = $req->withAttribute('product', $data);

        return $reqHandler->handle($req);
    }
}
