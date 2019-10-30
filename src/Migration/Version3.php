<?php

namespace Snowdog\DevTest\Migration;

use Snowdog\DevTest\Core\Database;

class Version3
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
        $this->addTimetrackColumnToPages();
    }

    private function addTimetrackColumnToPages()
    {
        $createQuery = <<<SQL
ALTER TABLE `pages` ADD `last_visited` DATETIME NULL AFTER `website_id`;
SQL;
        $this->database->exec($createQuery);
    }
}