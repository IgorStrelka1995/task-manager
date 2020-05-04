<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../vendor/autoload.php";

use libs\Db\Db;
use App\Service\Session;

try {
    $database = require_once(__DIR__ . "/../config/database.php");

    $containerBuilder = new DI\ContainerBuilder();
    $containerBuilder->addDefinitions(__DIR__ . '/../config/di.php');
    $container = $containerBuilder->build();

    $session = new Session();
    $session->sessionStart();
    
    $route = \App\Router\Route::build($container);

    Db::setDriver($database['driver']);
    Db::setHost($database['host']);
    Db::setDbName($database['db_name']);
    Db::setLogin($database['username']);
    Db::setPassword($database['password']);
    Db::connect();

    $response = $route->dispatch($container->get('request'), $container->get('response'));

    $container->get('emitter')->emit($response);
} catch (\League\Route\Http\Exception\NotFoundException $re) {
    echo $container->get(\Twig\Environment::class)->render('404.twig');
} catch (\libs\Db\Exception\DbException $de) {
    echo $container->get(\Twig\Environment::class)->render('500.twig', ['error' => $de->getMessage()]);
} catch (\League\Route\Http\Exception\BadRequestException $be) {
    echo $container->get(\Twig\Environment::class)->render('500.twig', ['error' => $be->getMessage()]);
} catch (\Exception $e) {
    echo $container->get(\Twig\Environment::class)->render('500.twig', ['error' => $e->getMessage()]);
}
