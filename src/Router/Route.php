<?php

namespace App\Router;

use DI\Container;
use League\Route\RouteCollection;

/**
 * @package App\Router
 */
class Route
{
    /**
     * @access public
     * @param Container $container
     * @return void
     */
    public static function build(Container $container)
    {
        $route = new RouteCollection($container);
        $route = self::routes($route);
        return $route;
    }

    /**
     * @access private
     * @param  $route
     * @return void
     */
    private static function routes($route) 
    {
        $route->get("/", '\App\Controller\TaskController::main');

        $route->get("/create", '\App\Controller\TaskController::createTask');
        $route->post("/create", '\App\Controller\TaskController::handlerCreateTask');

        $route->get("/login", '\App\Controller\SecurityController::signInPage');
        $route->post("/login", '\App\Controller\SecurityController::handlerSignInForm');
        $route->get("/logout", '\App\Controller\SecurityController::adminSignOut');

        $route->get("/admin", '\App\Controller\AdminController::adminMainPage');
        $route->get("/admin/task/{id}", '\App\Controller\AdminController::adminSingleTask');
        $route->post("/admin/task/{id}", '\App\Controller\AdminController::handlerEditTask');

        return $route;
    }
}