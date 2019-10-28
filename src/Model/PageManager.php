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

    public function getById(int $pageId)
    {
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('SELECT * FROM pages WHERE page_id = :page');
        $statement->bindParam(':page', $pageId, \PDO::PARAM_INT);
        $statement->setFetchMode(\PDO::FETCH_CLASS, Page::class);
        $statement->execute();

        return $statement->fetch();
    }

    public function getTotalPagesByUser(User $user)
   {
        $userId = $user->getUserId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('
           SELECT COUNT(*) AS `total_user_pages`
           FROM `pages` p
           INNER JOIN `websites` w ON (w.`website_id` = p.`website_id`)
           WHERE w.`user_id` = :user');
        $statement->bindParam(':user', $userId, \PDO::PARAM_INT);

        if (!$statement->execute()) {
            return 0;
        }

        return (int)$statement->fetchColumn();
    }

    public function getLeastRecentlyVisitedPageByUser(User $user)
    {
        return $this->getMarginalVisitedPageByUser($user, 'ASC');
    }

    public function getMostRecentlyVisitedPageByUser(User $user)
    {
        return $this->getMarginalVisitedPageByUser($user, 'DESC');
    }

    /**
     * Get least/more recently visited page.
     *
     * @param User $user
     * @param string $sortDirection 'ASC' (for least recent) or 'DESC' (for most recent)
     *
     * @return Page
     */
    protected function getMarginalVisitedPageByUser(User $user, $sortDirection = 'DESC')
    {
        $userId = $user->getUserId();
        $sortDirection = strtoupper($sortDirection);
        if (!in_array($sortDirection, ['ASC', 'DESC'])) {
            $sortDirection = 'DESC';
        }

        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('
            SELECT p.`page_id`
            FROM `websites` w
            INNER JOIN `pages` p ON (p.`website_id` = w.`website_id`)
            WHERE
                p.`last_visited` IS NOT NULL
                AND w.`user_id` = :user
            ORDER BY p.`last_visited` ' . $sortDirection . '
            LIMIT 1');
        $statement->bindParam(':user', $userId, \PDO::PARAM_INT);

        if (!$statement->execute() || $statement->rowCount() < 1) {
            return null;
        }

        return $this->getById((int)$statement->fetchColumn());
    }
}
