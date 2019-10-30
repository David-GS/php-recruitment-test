<?php

namespace Snowdog\DevTest\Model;

class Varnish
{

    public $varnish_id;
    public $ip;
    public $user_id;

    public function __construct()
    {
        $this->user_id = intval($this->user_id);
    }

    /**
     * @return int
     */
    public function getVarnishId()
    {
        return $this->varnish_id;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}
