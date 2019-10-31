<?php

namespace Snowdog\DevTest\Command;

use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\WebsiteManager;
use Symfony\Component\Console\Output\OutputInterface;
use SitemapImport\SitemapImport;
use Snowdog\DevTest\Model\UserManager;

class ImportxmlCommand
{
    /**
     * @var WebsiteManager
     */
    private $websiteManager;
    /**
     * @var PageManager
     */
    private $pageManager;
    /**
     * @var VarnishManager
     */
    private $varnishManager;
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var SitemapImport
     */
    private $sitemapImport;

    public function __construct(
        WebsiteManager $websiteManager,
        PageManager $pageManager,
        VarnishManager $varnishManager,
        UserManager $userManager,
        SitemapImport $sitemapImport)
    {
        $this->websiteManager = $websiteManager;
        $this->pageManager = $pageManager;
        $this->varnishManager = $varnishManager;
        $this->userManager = $userManager;
        $this->sitemapImport = $sitemapImport;
    }

    public function __invoke($userId, $source, OutputInterface $output)
    {
        try {
            $user = $this->userManager->getById((int)$userId);
            if (empty($user)) {
                throw new \Exception('User with ID ' . (int)$userId . ' does not exists!');
            }

            if (empty($source)) {
                throw new \Exception('Please, define source!');
            }

            if (!$this->validSource($source)) {
                throw new \Exception('Cannot find source!');
            }

            $sitemapArray = $this->sitemapImport->import($source);

            foreach ($sitemapArray as $host => $pages) {
                foreach ($pages as $url) {
                    $website = $this->websiteManager->getByHostname($host);
                    if (empty($website)) {
                        $createdWebsiteId = $this->websiteManager->create($user, $host, $host);
                        $website = $this->websiteManager->getById($createdWebsiteId);
                    }

                    $this->pageManager->create($website, $url);
                }
            }
        } catch (\Exception $exc) {
            $output->writeln('<error>' . $exc->getMessage() . '</error>');
        }
    }

    private function validSource($source)
    {
        if (filter_var($source, FILTER_VALIDATE_URL)) {
            return true;
        }

        // source is not URL, check if file exists
        if (file_exists($source)) {
            return true;
        }

        return false;
    }
}
