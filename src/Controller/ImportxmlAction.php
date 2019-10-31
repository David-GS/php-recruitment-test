<?php

namespace Snowdog\DevTest\Controller;

use SitemapImport\SitemapImport;
use Snowdog\DevTest\Model\PageManager;
use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\WebsiteManager;

class ImportxmlAction
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var WebsiteManager
     */
    private $websiteManager;
    /**
     * @var SitemapImport
     */
    private $sitemapImport;
    private $pageManager;


    public function __construct(UserManager $userManager, WebsiteManager $websiteManager, SitemapImport $sitemapImport, PageManager $pageManager)
    {
        $this->userManager = $userManager;
        $this->websiteManager = $websiteManager;
        $this->sitemapImport = $sitemapImport;
        $this->pageManager = $pageManager;
    }

    private function validUrl($url)
    {
        if (empty($url)) {
            throw new \Exception('URL cannot be empty');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \Exception('URL is not valid');
        }

        return true;
    }

    public function validFile(array $uploadedFileInfo)
    {
        if ($uploadedFileInfo['type'] != 'text/xml') {
            throw new \Exception('Unknown file type');
        }

        if ($uploadedFileInfo['error'] != 0) {
            throw new \Exception('File uploaded error');
        }

        if (!file_exists($uploadedFileInfo['tmp_name'])) {
            throw new \Exception('File doesn\'t exists');
        }

        return true;
    }

    public function execute()
    {
        try {
            $user = $this->userManager->getByLogin($_SESSION['login']);
            if (empty($user)) {
                throw new \Exception('Unknown user');
            }

            if (isset($_POST['import_sitemap'])) {
                switch ($_POST['import_sitemap']) {
                    case 'url':
                        $url = $_POST['url'] ?? null;
                        $this->validUrl($url);
                        $sitemapArray = $this->sitemapImport->import($url);
                        break;
    
                    case 'file':
                        $this->validFile($_FILES['file']);
                        $sitemapArray = $this->sitemapImport->import($_FILES['file']['tmp_name']);
                        break;
                    
                    default:
                        throw new \Exception('Unknown import method');
                        break;
                }

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
            } else {
                throw new \Exception('Cannot proceed');
            }

            $_SESSION['flash'] = 'Sitemap imported!';
        } catch (\Exception $exc) {
            $_SESSION['flash'] = $exc->getMessage();
        }

        header('Location: /');
    }
}