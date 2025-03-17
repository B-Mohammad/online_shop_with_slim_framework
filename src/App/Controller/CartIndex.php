<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repo\CartRepo;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteContext;
use Valitron\Validator;

class CartIndex
{
    public function __construct(
        private CartRepo $cartR,
        private Validator $validator
    ) {}

    public function getCart(Request $req, Response $res): Response
    {

        $data = $req->getAttribute("cart");

        $body = json_encode($data);
        $res->getBody()->write($body);

        return $res;
    }

    public function addToCart(Request $req, Response $res): Response
    {
        $user = $req->getAttribute("user");
        $user_id = $user->user_id;

        $body = $req->getParsedBody();

        $this->validator->mapFieldsRules([
            "product_id" => ['required', 'integer', ['min', 1]],
            'quantity' => ['required', 'integer', ['min', 1]]
        ]);
        $this->validator = $this->validator->withData($body);

        if (!$this->validator->validate()) {
            $res->getBody()->write(json_encode($this->validator->errors()));
            return $res->withStatus(422);
        }

        $product_id = $body["product_id"];
        $quantity = $body["quantity"];

        $data = $this->cartR->addToCart($user_id, (int)$product_id, (int)$quantity);

        if ($data === false) {
            $temp = json_encode([
                "message" => " item not added",
            ]);
            $res = $res->withStatus(404);
        } else {
            $temp = json_encode([
                "message" => " item added",
                "id" => $data
            ]);
            $res = $res->withStatus(201);
        }
        $res->getBody()->write($temp);

        return $res;
    }

    public function deleteFromCart(Request $req, Response $res): Response
    {
        $user = $req->getAttribute("user");
        $user_id = $user->user_id;

        $context = RouteContext::fromRequest($req);
        $route = $context->getRoute();
        $product_id = (int) $route->getArgument("product_id");

        $this->validator->mapFieldsRules(["product_id" => ['required', 'integer', ['min', 1]]]);
        $this->validator = $this->validator->withData(['product_id' => $product_id]);

        if (!$this->validator->validate()) {
            $res->getBody()->write(json_encode($this->validator->errors()));
            return $res->withStatus(422);
        }


        $data = $this->cartR->deleteFromCart($user_id, (int)$product_id);

        if ($data === false || $data === 0) {
            $temp = json_encode([
                "message" => " item not deleted",
            ]);
            $res =  $res->withStatus(404);
        } else {
            $temp = json_encode([
                "message" => " item deleted",
                "row" => $data
            ]);
        }
        $res->getBody()->write($temp);

        return $res;
    }
}
