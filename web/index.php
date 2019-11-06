<?php

use Snowdog\DevTest\Component\Menu;
use Snowdog\DevTest\Component\RouteRepository;
use Snowdog\DevTest\Component\ACL;
use Snowdog\DevTest\Model\UserManager;

session_start();

$container = require __DIR__ . '/../app/bootstrap.php';

$routeRepository = RouteRepository::getInstance();

$dispatcher = \FastRoute\simpleDispatcher($routeRepository);

Menu::setContainer($container);

$route = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
switch ($route[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        header("HTTP/1.0 404 Not Found");
        require __DIR__ . '/../src/view/404.phtml';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        header("HTTP/1.0 405 Method Not Allowed");
        require __DIR__ . '/../src/view/405.phtml';
        break;
    case FastRoute\Dispatcher::FOUND:
        $controller = $route[1];
        $parameters = $route[2];

        $loggedIn = UserManager::isLoggedIn();
        $aclRestriction = ACL::getAclRouteRestriction($controller[0], $controller[1]);
        if (! $loggedIn && $aclRestriction == ACL::LOGGED_IN) {
            header("Location: /login");
            die();
        }

        if ($loggedIn && $aclRestriction == ACL::LOGGED_OUT) {
            header("HTTP/1.0 403 Forbidden");
            require __DIR__ . '/../src/view/403.phtml';
            break;
        }

        $container->call($controller, $parameters);
        break;
}
