<?php

use Snowdog\DevTest\Model\Varnish;

interface Old_Legacy_CacheWarmer_Resolver_Interface
{
    public function getIp($hostname);
}

class Old_Legacy_CacheWarmer_Resolver_Method implements Old_Legacy_CacheWarmer_Resolver_Interface 
{
    public function getIp($hostname)
    {
        return gethostbyname($hostname);
    }
}

class Old_Legacy_CacheWarmer_Actor
{
    private $callable;

    public function setActor($callable) {
        $this->callable = $callable;
    }
    
    public function act($hostname, $ip, $url, $varnishIp)
    {
        call_user_func($this->callable, $hostname, $ip, $url, $varnishIp);
    }
}

class Old_Legacy_CacheWarmer_Warmer
{
    /** @var Old_Legacy_CacheWarmer_Actor */
    private $actor;
    /** @var Old_Legacy_CacheWarmer_Resolver_Interface */
    private $resolver;
    /** @var string */
    private $hostname;
    /** @var Varnish */
    private $varnish;

    /**
     * @param Old_Legacy_CacheWarmer_Actor $actor
     */
    public function setActor($actor)
    {
        $this->actor = $actor;
    }

    /**
     * @param string $hostname
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    }

    /**
     * @param Varnish $varnish
     */
    public function setVarnish(Varnish $varnish)
    {
        $this->varnish = $varnish;
    }

    /**
     * @param Old_Legacy_CacheWarmer_Resolver_Interface $resolver
     */
    public function setResolver($resolver)
    {
        $this->resolver = $resolver;
    }

    public function warm($url) {
        $ip = $this->resolver->getIp($this->hostname);
        $varnishIp = $this->varnish->getIp();
        sleep(1); // this emulates visit to http://$hostname/$url via $ip
        $this->actor->act($this->hostname, $ip, $url, $varnishIp);
    }
    
}

