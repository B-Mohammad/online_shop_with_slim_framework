<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repo\CartRepo;
use App\Repo\OrdersRepo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use Slim\Exception\HttpNotFoundException;

class GetOrder
{
    public function __construct(
        private OrdersRepo $orderR,
        private CartRepo $cartR
    ) {}

    public function AllOrders(Request $req, RequestHandler $reqHandler): Response
    {
        $user = $req->getAttribute("user");
        $userId = $user->user_id;


        $data = $this->orderR->getAll((int) $userId);

        if (empty($data)) {
            throw new HttpNotFoundException($req, message: "orders not found!");
        }

        $req = $req->withAttribute('orders', $data);

        return $reqHandler->handle($req);
    }

    public function OrderDetail(Request $req, RequestHandler $reqHandler): Response
    {
        $user = $req->getAttribute("user");
        $userId = $user->user_id;

        $context = RouteContext::fromRequest($req);
        $route = $context->getRoute();
        $cartId = $route->getArgument("cart_id");


        $oData = $this->orderR->getByID(userId: (int) $userId, cartId: (int)$cartId);
        $cData = $this->cartR->getCartById(cartId: (int)$cartId);

        if ($oData === false) {
            throw new HttpNotFoundException($req, message: "order not found!");
        }
        $oData["detail"] = $cData;
        $req = $req->withAttribute('order', $oData);

        return $reqHandler->handle($req);
    }
}
