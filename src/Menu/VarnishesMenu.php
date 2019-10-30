<?php

namespace Snowdog\DevTest\Menu;

class VarnishesMenu extends AbstractMenu
{

    public function isActive()
    {
        return $_SERVER['REQUEST_URI'] == '/varnishes';
    }

    public function getHref()
    {
        return '/varnishes';
    }

    public function getLabel()
    {
        return 'Varnishes';
    }
}