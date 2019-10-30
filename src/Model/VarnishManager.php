<?php

namespace Snowdog\DevTest\Model;

use Snowdog\DevTest\Core\Database;

class VarnishManager
{

    /**
     * @var Database|\PDO
     */
    private $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getById(int $id)
    {
        /** @var \PDOStatement $query */
        $query = $this->database->prepare('SELECT * FROM varnishes WHERE varnish_id = :id');
        $query->setFetchMode(\PDO::FETCH_CLASS, Varnish::class);
        $query->bindParam(':id', $id, \PDO::PARAM_INT);
        $query->execute();

        $user = $query->fetch();

        return $user;
    }

    public function getAllByUser(User $user)
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $query */
        $query = $this->database->prepare(
            'SELECT varnish_id, INET_NTOA(ip) AS ip, user_id FROM varnishes WHERE user_id = :user'
        );
        $query->bindParam(':user', $userId, \PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    public function getWebsites(Varnish $varnish)
    {
        $varnishId = $varnish->getVarnishId();
        $query = $this->database->prepare('
            SELECT w.*
            FROM websites w
            INNER JOIN varnishes_websites vw ON (vw.website_id = w.website_id)
            WHERE vw.varnish_id = :varnish');
        $query->bindParam(':varnish', $varnishId, \PDO::PARAM_INT);
        $query->execute();

        return $query->fetchAll(\PDO::FETCH_CLASS, Website::class);
    }

    public function getByWebsite(Website $website)
    {
        $websiteId = $website->getWebsiteId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('
            SELECT v.varnish_id, INET_NTOA(v.ip) AS ip, v.user_id
            FROM `varnishes` v
            LEFT JOIN varnishes_websites vw ON (vw.varnish_id = v.varnish_id)
            WHERE vw.website_id = :website');
        $statement->bindParam(':website', $websiteId, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_CLASS, Varnish::class);
    }

    public function create(User $user, $ip)
    {
        $userId = $user->getUserId();
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare('INSERT INTO varnishes (ip, user_id) VALUES (INET_ATON(:ip), :user)');
        $statement->bindParam(':ip', $ip, \PDO::PARAM_STR);
        $statement->bindParam(':user', $userId, \PDO::PARAM_INT);
        $statement->execute();

        return $this->database->lastInsertId();
    }

    public function link($varnishId, $websiteId)
    {
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare(
            'REPLACE INTO `varnishes_websites` (varnish_id, website_id) VALUES (:varnish, :website)'
        );
        $statement->bindParam(':varnish', $varnishId, \PDO::PARAM_INT);
        $statement->bindParam(':website', $websiteId, \PDO::PARAM_INT);

        return $statement->execute();
    }

    public function unlink($varnishId, $websiteId)
    {
        /** @var \PDOStatement $statement */
        $statement = $this->database->prepare(
            'DELETE FROM `varnishes_websites` WHERE varnish_id = :varnish AND website_id = :website'
        );
        $statement->bindParam(':varnish', $varnishId, \PDO::PARAM_INT);
        $statement->bindParam(':website', $websiteId, \PDO::PARAM_INT);

        return $statement->execute();
    }

}