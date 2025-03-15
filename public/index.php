<?php

declare(strict_types=1);

use App\Middleware\AddJsonResponseHeader;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Handlers\Strategies\RequestResponseArgs;

define('APP_ROOT', dirname(__DIR__));


require APP_ROOT . "/vendor/autoload.php";

$containerBl = new ContainerBuilder();
$container = $containerBl->addDefinitions(APP_ROOT . "/config/definition.php")->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector = $app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$error_middleware = $app->addErrorMiddleware(false, true, true);
$error_handler = $error_middleware->getDefaultErrorHandler();
$error_handler->forceContentType("application/json");

$app->add(new AddJsonResponseHeader);

$app->get("/api/products", function (Request $req, Response $res) {

    $productR = $this->get(App\Repo\ProductsRepo::class);
    $data = $productR->getAll();

    $body = json_encode($data);
    $res->getBody()->write($body);

    return $res;
});

$app->get("/api/products/{id:[0-9]+}", function (Request $req, Response $res, string $id) {

    $data = $req->getAttribute("product");

    $body = json_encode($data);
    $res->getBody()->write($body);

    return $res;
})->add(App\Middleware\GetProduct::class);

$app->run();
