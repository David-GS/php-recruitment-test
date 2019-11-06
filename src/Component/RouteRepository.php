<?php

namespace Snowdog\DevTest\Component;

use FastRoute\RouteCollector;
use Snowdog\DevTest\Component\ACL;

class RouteRepository
{
    private static $instance = null;
    private $routes = [];
    const HTTP_METHOD = 'http_method';
    const ROUTE = 'route';
    const CLASS_NAME = 'class_name';
    const METHOD_NAME = 'method_name';
    const ACL_NAME = 'acl';


    /**
     * @return RouteRepository
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function registerRoute($httpMethod, $route, $className, $methodName, $acl = null)
    {
        if (!$acl) {
            $acl = ACL::getDefaultRestriction();
        }
        $instance = self::getInstance();
        $instance->addRoute($httpMethod, $route, $className, $methodName, $acl);
    }

    public function __invoke(RouteCollector $r)
    {
        foreach ($this->routes as $route) {
            ACL::addAclRouteRestriction($route[self::CLASS_NAME], $route[self::METHOD_NAME], $route[self::ACL_NAME]);
            $r->addRoute(
                $route[self::HTTP_METHOD],
                $route[self::ROUTE],
                [
                    $route[self::CLASS_NAME],
                    $route[self::METHOD_NAME]
                ]
            );
        }
    }

    private function addRoute($httpMethod, $route, $className, $methodName, $acl)
    {
        $this->routes[] = [
            self::HTTP_METHOD => $httpMethod,
            self::ROUTE => $route,
            self::CLASS_NAME => $className,
            self::METHOD_NAME => $methodName,
            self::ACL_NAME => $acl
        ];
    }
}