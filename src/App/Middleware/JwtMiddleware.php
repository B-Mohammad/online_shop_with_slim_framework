<?php

declare(strict_types=1);

namespace App\Middleware;

use DateTime;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Factory\ResponseFactory;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware
{
    function __construct(private ResponseFactory $factory) {}

    public function __invoke(Request $req, RequestHandler $reqHandler): Response
    {
        if (!$req->hasHeader("Authorization")) {

            $res = $this->factory->createResponse();
            $res->getBody()->write(json_encode(["error" => "api-key missing from request!"]));
            return $res->withStatus(400);
        }

        $authHeader = $req->getHeaderLine("Authorization");
        if (empty($authHeader) || strpos($authHeader, 'Bearer ') !== 0) {

            $res = $this->factory->createResponse();
            $res->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $res->withStatus(401);
        }

        $jwt = substr($authHeader, 7);
        try {
            $decoded = JWT::decode($jwt, new Key("8e4baba57ab5ebbdde59ff06c34df2fd260daf6d7fc1b8c0e4066ed0074a8606", "HS256"));
            if ($decoded->exp < time()) {

                $res = $this->factory->createResponse();
                $res->getBody()->write(json_encode(['error' => 'Unauthorized']));
                return $res->withStatus(401);
            }

            $req->withAttribute('user', $decoded);
        } catch (\Throwable $th) {

            $res = $this->factory->createResponse();
            $res->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $res->withStatus(401);
        }

        $res = $reqHandler->handle($req);

        return $res;
    }
}
