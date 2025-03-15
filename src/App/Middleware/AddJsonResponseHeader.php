<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AddJsonResponseHeader
{
    public function __invoke(Request $req, RequestHandler $reqHandler): Response
    {

        $res = $reqHandler->handle($req);

        return $res->withHeader('Content-type', "application/json");
    }
}
