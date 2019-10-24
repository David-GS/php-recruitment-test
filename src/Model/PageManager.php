<?php

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class PageManager
{

    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getAllByWebsite(Website $website)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM pages WHERE website_id = :website');
        $query->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_CLASS, Page::class);
    }

    public function create(Website $website, $url)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO pages (url, website_id) VALUES (:url, :website)');
        $statement->bindParam(':url', $url, \PDO::PARAM_STR);
        $statement->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $statement->execute();
        return $this->database->lastInsertId();
    }

    public function updateLastVisit(Page $page, $lastVisited = null)
    {
        $lastVisited = $lastVisited ?? date('Y-m-d H:i:s');
        $pageId = $page->getPageId();

        $statement = $this->database->prepare('UPDATE pages SET
            last_visited = :last_visited
            WHERE page_id = :page');

        $statement->bindParam(':last_visited', $lastVisited, \PDO::PARAM_STR);
        $statement->bindParam(':page',  $pageId, \PDO::PARAM_INT);

        return $statement->execute();
    }
}
