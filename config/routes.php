<?php

declare(strict_types=1);

use App\Controller\AuthEndPoint;
use App\Middleware\GetProduct;
use App\Controller\ProductIndex;
use App\Middleware\GetCart;
use App\Controller\CartIndex;
use App\Controller\OrderIndex;
use App\Middleware\GetOrder;
use App\Middleware\JwtMiddleware;
use Slim\Routing\RouteCollectorProxy;

$app->group('/api/products', function (RouteCollectorProxy $group) {

    $group->get("", ProductIndex::class . ':AllProducts');

    $group->get("/{id:[0-9]+}", ProductIndex::class . ':OneProduct')
        ->add(GetProduct::class);
});

$app->group('/api/cart', function (RouteCollectorProxy $group) {

    $group->get("", CartIndex::class . ":getCart")
        ->add(GetCart::class);
})->add(JwtMiddleware::class);

$app->group('/api/orders', function (RouteCollectorProxy $group) {

    $group->get("", OrderIndex::class . ":AllOrders")
        ->add(GetOrder::class . ":AllOrders");

    $group->get("/{cart_id:[0-9]+}", OrderIndex::class . ":OneOrder")
        ->add(GetOrder::class . ":OrderDetail");
})->add(JwtMiddleware::class);

$app->group('/api/auth', function (RouteCollectorProxy $group) {

    $group->post("", AuthEndPoint::class);
});
