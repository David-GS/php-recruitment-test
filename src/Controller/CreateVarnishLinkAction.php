<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\Varnish;
use Snowdog\DevTest\Model\VarnishManager;
use Snowdog\DevTest\Model\Website;
use Snowdog\DevTest\Model\WebsiteManager;

class CreateVarnishLinkAction
{
    /**
     * @var UserManager
     */
    private $userManager;
    /**
     * @var VarnishManager
     */
    private $varnishManager;
    /**
     * @var WebsiteManager
     */
    private $websiteManager;

    public function __construct(UserManager $userManager, VarnishManager $varnishManager, WebsiteManager $websiteManager)
    {
        $this->userManager = $userManager;
        $this->varnishManager = $varnishManager;
        $this->websiteManager = $websiteManager;
    }

    public function execute()
    {
        $requestParams = json_decode(file_get_contents('php://input'));

        $varnishId = (int)$requestParams->varnish_id;
        $websiteId = (int)$requestParams->website_id;
        $checked   = ($requestParams->checked == 'true') ? true : false;

        try {
            /** @var Varnish|false $varnish */
            $varnish = $this->varnishManager->getById($varnishId);
            if (empty($varnish)) {
                throw new \Exception('Varnish not found');
            }

            /** @var Website|false $website */
            $website = $this->websiteManager->getById($websiteId);
            if (empty($website)) {
                throw new \Exception('Website not found');
            }

            if ($checked) {
                if (!$this->varnishManager->link($varnishId, $websiteId)) {
                    throw new \Exception('Cannot link website to varnish');
                }
            } else {
                if (!$this->varnishManager->unlink($varnishId, $websiteId)) {
                    throw new \Exception('Cannot unlink website and varnish');
                }
            }

            $this->jsonResponse([
                'success' => true,
                'message' => 'Saved'
            ]);

        } catch (\Exception $exc) {
            $this->jsonResponse([
                'success' => false,
                'message' => $exc->getMessage(),
            ]);
        }

        $this->varnishManager->link($varnishId, $websiteId);
    }

    protected function jsonResponse($values)
    {
        echo json_encode($values);
        exit;
    }
}