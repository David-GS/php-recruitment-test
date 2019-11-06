<?php

use Snowdog\DevTest\Controller\LoginFormAction;
use Snowdog\DevTest\Component\Migrations;
use Snowdog\DevTest\Controller\LogoutAction;
use Snowdog\DevTest\Menu\WebsitesMenu;
use Snowdog\DevTest\Controller\CreatePageAction;
use Snowdog\DevTest\Component\RouteRepository;
use Snowdog\DevTest\Command\WarmCommand;
use Snowdog\DevTest\Controller\CreateWebsiteAction;
use Snowdog\DevTest\Command\MigrateCommand;
use Snowdog\DevTest\Component\Menu;
use Snowdog\DevTest\Controller\RegisterFormAction;
use Snowdog\DevTest\Controller\IndexAction;
use Snowdog\DevTest\Menu\LoginMenu;
use Snowdog\DevTest\Component\CommandRepository;
use Snowdog\DevTest\Controller\CreateVarnishAction;
use Snowdog\DevTest\Controller\WebsiteAction;
use Snowdog\DevTest\Menu\RegisterMenu;
use Snowdog\DevTest\Controller\LoginAction;
use Snowdog\DevTest\Controller\RegisterAction;
use Snowdog\DevTest\Controller\VarnishesAction;
use Snowdog\DevTest\Controller\CreateVarnishLinkAction;
use Snowdog\DevTest\Menu\VarnishesMenu;
use Snowdog\DevTest\Command\ImportxmlCommand;
use Snowdog\DevTest\Controller\ImportxmlAction;
use Snowdog\DevTest\Component\ACL;

ACL::setDefaultRestriction(ACL::LOGGED_IN);

Menu::register(RegisterMenu::class, 250);
CommandRepository::registerCommand('migrate_db', MigrateCommand::class);
Menu::register(WebsitesMenu::class, 10);
RouteRepository::registerRoute('POST', '/website', CreateWebsiteAction::class, 'execute');
RouteRepository::registerRoute('GET', '/login', LoginFormAction::class, 'execute', ACL::LOGGED_OUT);
RouteRepository::registerRoute('POST', '/register', RegisterAction::class, 'execute', ACL::LOGGED_OUT);
RouteRepository::registerRoute('GET', '/', IndexAction::class, 'execute');
RouteRepository::registerRoute('POST', '/page', CreatePageAction::class, 'execute');
CommandRepository::registerCommand('warm [id]', WarmCommand::class);
RouteRepository::registerRoute('GET', '/logout', LogoutAction::class, 'execute');
RouteRepository::registerRoute('GET', '/register', RegisterFormAction::class, 'execute', ACL::LOGGED_OUT);
Menu::register(LoginMenu::class, 200);
RouteRepository::registerRoute('POST', '/login', LoginAction::class, 'execute', ACL::LOGGED_OUT);
RouteRepository::registerRoute('GET', '/website/{id:\d+}', WebsiteAction::class, 'execute');
RouteRepository::registerRoute('GET', '/varnishes', VarnishesAction::class, 'execute');
RouteRepository::registerRoute('POST', '/varnish', CreateVarnishAction::class, 'execute');
RouteRepository::registerRoute('POST', '/varnish/assign', CreateVarnishLinkAction::class, 'execute');
Menu::register(VarnishesMenu::class, 20);
CommandRepository::registerCommand('importxml [userid] [source]', ImportxmlCommand::class);
RouteRepository::registerRoute('POST', '/importxml', ImportxmlAction::class, 'execute');

Migrations::registerComponentMigration('Snowdog\\DevTest', 4);
