<?php

declare(strict_types=1);

use App\Middleware\AddJsonResponseHeader;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestResponseArgs;

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

require APP_ROOT . "/config/routes.php";

$app->run();
