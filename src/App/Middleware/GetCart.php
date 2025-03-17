<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Repo\CartRepo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpNotFoundException;

class GetCart
{
    public function __construct(private CartRepo $cartR) {}

    public function __invoke(Request $req, RequestHandler $reqHandler): Response
    {

        $user = $req->getAttribute("user");
        $user_id = $user->user_id;

        $data = $this->cartR->getCart((int) $user_id);

        if (empty($data)) {
            throw new HttpNotFoundException($req, message: "Cart is Empty!");
        }

        $req = $req->withAttribute('cart', $data);

        return $reqHandler->handle($req);
    }
}
