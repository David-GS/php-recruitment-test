<?php

namespace Snowdog\DevTest\Component;

class ACL
{
    const ALL        = 'all';
    const LOGGED_IN  = 'logged_in';
    const LOGGED_OUT = 'logged_out';

    private const DEFAULT = self::ALL;

    private static $defaultRestriction;

    private static $routes = [];

    /**
     * @param string $restriction
     *
     * @return bool
     */
    private static function validRestriction($restriction)
    {
        return in_array($restriction, [
            self::ALL, self::LOGGED_IN, self::LOGGED_OUT
        ]);
    }

    public static function setDefaultRestriction($restriction = null)
    {
        if (!self::validRestriction($restriction)) {
            trigger_error('Unknown ALC restriction. Default restriction has been set.', E_USER_WARNING);
            $restriction = self::DEFAULT;
        }

        self::$defaultRestriction = $restriction;
    }

    public static function getDefaultRestriction()
    {
        if (empty(self::$defaultRestriction)) {
            self::setDefaultRestriction(self::DEFAULT);
        }

        return self::$defaultRestriction;
    }

    public static function addAclRouteRestriction($controller, $method, $restriction)
    {
        if (!self::validRestriction($restriction)) {
            trigger_error('Unknown ALC restriction. Default restriction has been set.', E_USER_WARNING);
            $restriction = self::getDefaultRestriction();
        }
        self::$routes[$controller][$method] = $restriction;
    }

    public static function getAclRouteRestriction($controller, $method)
    {
        return self::$routes[$controller][$method] ?? self::getDefaultRestriction();
    }
}
