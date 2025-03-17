<?php

declare(strict_types=1);

use App\Controller\AuthEndPoint;
use App\Middleware\AddJsonResponseHeader;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestResponseArgs;
use App\Middleware\GetProduct;
use App\Controller\ProductIndex;
use App\Middleware\GetCart;
use App\Controller\CartIndex;
use App\Controller\OrderIndex;
use App\Middleware\GetOrder;
use App\Middleware\JwtMiddleware;
use Slim\Routing\RouteCollectorProxy;

define('APP_ROOT', dirname(__DIR__));


require APP_ROOT . "/vendor/autoload.php";

$containerBl = new ContainerBuilder();
$container = $containerBl->addDefinitions(APP_ROOT . "/config/definition.php")->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector = $app->getRouteCollector();
$collector->setDefaultInvocationStrategy(new RequestResponseArgs);

$app->addBodyParsingMiddleware();

$error_middleware = $app->addErrorMiddleware(true, true, true);
$error_handler = $error_middleware->getDefaultErrorHandler();
$error_handler->forceContentType("application/json");

$app->add(new AddJsonResponseHeader);

$app->group('/api/products', function (RouteCollectorProxy $group) {

    $group->get("", ProductIndex::class . ':AllProducts');

    $group->get("/{id:[0-9]+}", ProductIndex::class . ':OneProduct')
        ->add(GetProduct::class);
});

$app->group('/api/cart', function (RouteCollectorProxy $group) {

    $group->get("/{user_id:[0-9]+}", CartIndex::class . ":getCart")
        ->add(GetCart::class);
});

$app->group('/api/orders', function (RouteCollectorProxy $group) {

    $group->get("/{user_id:[0-9]+}", OrderIndex::class . ":AllOrders")->add(GetOrder::class . ":AllOrders");


    $group->get("/{user_id:[0-9]+}/{cart_id:[0-9]+}", OrderIndex::class . ":OneOrder")
        ->add(GetOrder::class . ":OrderDetail");
})->add(JwtMiddleware::class);

$app->group('/api/auth', function (RouteCollectorProxy $group) {

    $group->post("", AuthEndPoint::class);
});

$app->run();
