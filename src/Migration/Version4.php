<?php

namespace Snowdog\DevTest\Migration;

use Snowdog\DevTest\Core\Database;

class Version4
{
    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(
        Database $database
    ) {
        $this->database = $database;
    }

    public function __invoke()
    {
        $this->createVarnishTable();
        $this->createVarnishWebsiteTable();
    }

    private function createVarnishTable()
    {
        $createQuery = <<<SQL
CREATE TABLE `varnishes` (
    `varnish_id` int(10) unsigned NOT NULL AUTO_INCREMENT ,
    `ip` int(4) NOT NULL ,
    `user_id` int(10) unsigned NOT NULL ,
    PRIMARY KEY (`varnish_id`),
    UNIQUE `ip_unique` (`ip`),
    CONSTRAINT `varnish_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SQL;
        $this->database->exec($createQuery);
    }

    private function createVarnishWebsiteTable()
    {
        $createQuery = <<<SQL
CREATE TABLE `varnishes_websites` (
    `varnish_id` int(10) unsigned NOT NULL,
    `website_id` int(10) unsigned NOT NULL,
    PRIMARY KEY (`varnish_id`,`website_id`),
    CONSTRAINT `varnish_website_varnish_fk` FOREIGN KEY (`varnish_id`) REFERENCES `varnishes` (`varnish_id`),
    CONSTRAINT `varnish_website_website_fk` FOREIGN KEY (`website_id`) REFERENCES `websites` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL;
        $this->database->exec($createQuery);
    }
}