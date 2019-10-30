<?php

namespace Snowdog\DevTest\Controller;

use Snowdog\DevTest\Model\UserManager;
use Snowdog\DevTest\Model\VarnishManager;

class CreateVarnishAction
{
    /**
     * @var VarnishManager
     */
    private $varnishManager;

    public function __construct(UserManager $userManager, VarnishManager $varnishManager)
    {
        $this->userManager = $userManager;
        $this->varnishManager = $varnishManager;
    }

    public function execute()
    {
        $ip = $_POST['ip'];

        try {
            if (empty($ip)) {
                throw new \Exception('IP cannot be empty');
            }

            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                throw new \Exception("IP is not a valid IP address");
            }

            $user = $this->userManager->getByLogin($_SESSION['login']);
            if (empty($user)) {
                throw new \Exception('Unknown user');
            }

            if (!$this->varnishManager->create($user, $ip)) {
                throw new \Exception('Cannot create new varnish');
            }

            $_SESSION['flash'] = 'Varnish ' . $ip . ' added!';

        } catch (\Exception $exc) {
            $_SESSION['flash'] = $exc->getMessage();
        }

        header('Location: /varnishes');
    }
}